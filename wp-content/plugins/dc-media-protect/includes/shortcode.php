<?php
// 短代码注册模块

// 增强的移动设备检测函数
function dcmp_is_mobile_device() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // 首先使用WordPress内置检测
    if (function_exists('wp_is_mobile') && wp_is_mobile()) {
        return true;
    }
    
    // 增强的移动设备检测
    return preg_match('/Mobile|Android|BlackBerry|iPhone|iPad|iPod|Windows Phone|webOS|Opera Mini|IEMobile|WPDesktop|Mobi|Tablet|Touch/i', $user_agent) ||
           // 检测触摸屏设备
           (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) ||
           // 检测Opera Mobile
           preg_match('/Opera.*Mini|Opera.*Mobi/i', $user_agent) ||
           // 检测特定的移动浏览器
           preg_match('/Chrome.*Mobile|Safari.*Mobile/i', $user_agent);
}

// 获取水印内容
function dcmp_get_watermark_text() {
    return esc_html(get_option('dcmp_watermark_text', '数字中国'));
}

// 视频短代码 [dc_video src="视频地址" width="宽度" height="高度"]
function dcmp_shortcode_video($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    $style = '';
    if ($width > 0) $style .= 'width:' . $width . 'px;';
    if ($height > 0) $style .= 'height:' . $height . 'px;';
    if (!$style) $style = 'max-width:100%;';
    $watermark = '<div class="dcmp-watermark">' . dcmp_get_watermark_text() . '</div>';
    return '<div class="dcmp-media-container" style="position:relative;">'
        . '<video src="' . $src . '" controls style="' . esc_attr($style) . '"></video>'
        . $watermark
        . '</div>';
}
add_shortcode('dc_video', 'dcmp_shortcode_video');

// PPT短代码 [dc_ppt src="PPT地址" width="宽度" height="高度"]
function dcmp_shortcode_ppt($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    
    // 检测移动设备和具体设备类型
    $is_mobile = dcmp_is_mobile_device();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
    $is_android = strpos($user_agent, 'Android') !== false;
    
    // 调试信息 - 在移动端显示设备检测结果
    $debug_info = '';
    if ($is_mobile) {
        $debug_info = '<!-- 调试: 移动设备检测 - iOS:' . ($is_ios ? 'Yes' : 'No') . ' Android:' . ($is_android ? 'Yes' : 'No') . ' UserAgent: ' . $user_agent . ' -->';
    }
    
    // 响应式尺寸设置
    if ($width > 0 && $height > 0) {
        $w = $width;
        $h = $height;
    } else {
        if ($is_mobile) {
            $w = '100%';
            $h = '400px';
        } else {
            $w = 800;
            $h = 600;
        }
    }
    
    // 检查是否为本地文件（包括data: URL）
    $is_local = (strpos($src, home_url()) === 0) || (strpos($src, 'data:') === 0);
    
    $viewer_html = '';
    $container_id = 'dcmp-pdf-' . uniqid();
    
    if ($is_local) {
        // 根据设备类型选择不同的显示方式
        if ($is_mobile) {
            // 移动端：使用专用的移动端PDF查看器
            if (function_exists('dcmp_generate_mobile_pdf_viewer')) {
                $viewer_html = dcmp_generate_mobile_pdf_viewer($src, $w, $h, $container_id);
                $debug_info .= '<!-- 调试: 使用移动端PDF查看器 -->';
            } else {
                // 回退方案：简单的移动端显示
                $debug_info .= '<!-- 调试: mobile-pdf-viewer.php 函数不存在，使用回退方案 -->';
                $viewer_html = '
                <div style="width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; box-sizing:border-box; border-radius:8px;">
                    <div style="font-size:48px; margin-bottom:20px; color:#666;">📄</div>
                    <h3 style="margin:0 0 15px 0; color:#333;">移动端PDF查看器</h3>
                    <p style="margin:0 0 20px 0; color:#666; line-height:1.4;">
                        设备类型: ' . ($is_ios ? 'iOS' : ($is_android ? 'Android' : '移动设备')) . '<br>
                        正在加载移动端优化查看器...
                    </p>
                    <div style="display:flex; flex-direction:column; gap:10px; width:100%; max-width:250px;">
                        <a href="' . $src . '" target="_blank" style="background:#007cba; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block;">
                            🔗 在新窗口打开
                        </a>
                        <a href="' . $src . '" download style="background:#28a745; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block;">
                            📥 下载查看
                        </a>
                    </div>
                    <small style="margin-top:15px; color:#999; font-size:12px;">
                        移动端PDF查看器修复已应用
                    </small>
                </div>';
            }
        } else {
            // 桌面端：传统iframe方式
            $viewer_html = '<iframe src="' . $src . '" width="' . $w . '" height="' . $h . '" style="border:1px solid #ccc; max-width:100%;"></iframe>';
            $debug_info .= '<!-- 调试: 桌面端iframe显示 -->';
        }
    } else {
        // 外部PDF文件，提供下载链接和说明
        $container_style = 'width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; flex-direction:column; background:#f9f9f9; padding:20px; box-sizing:border-box; text-align:center;';
        
        $viewer_html = '
        <div style="' . $container_style . '">
            <p style="margin:10px; text-align:center;">
                <strong>PDF文档预览</strong><br>
                <small>外部PDF链接可能无法直接预览</small>
            </p>
            <a href="' . $src . '" target="_blank" class="button" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:3px; margin:10px;">
                📄 打开PDF文档
            </a>
            <p style="margin:10px; font-size:12px; color:#666; text-align:center;">
                建议：将PDF文件上传到媒体库以获得更好的预览效果
            </p>
        </div>';
        $debug_info .= '<!-- 调试: 外部PDF链接 -->';
    }
    
    $watermark = '<div class="dcmp-watermark">' . dcmp_get_watermark_text() . '</div>';
    return $debug_info . '<div class="dcmp-media-container dcmp-pdf-container" style="position:relative; max-width:100%; overflow:hidden;">'
        . $viewer_html
        . $watermark
        . '</div>';
}
add_shortcode('dc_ppt', 'dcmp_shortcode_ppt');

// 图片短代码 [dc_img src="图片地址" width="宽度" height="高度"]
function dcmp_shortcode_img($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    $style = '';
    if ($width > 0) $style .= 'width:' . $width . 'px;';
    if ($height > 0) $style .= 'height:' . $height . 'px;';
    if (!$style) $style = 'max-width:100%;';
    $watermark = '<div class="dcmp-watermark">' . dcmp_get_watermark_text() . '</div>';
    return '<div class="dcmp-media-container" style="position:relative;">'
        . '<img src="' . $src . '" style="' . esc_attr($style) . '" />'
        . $watermark
        . '</div>';
}
add_shortcode('dc_img', 'dcmp_shortcode_img');

// 文字短代码 [dc_text]内容[/dc_text]
function dcmp_shortcode_text($atts, $content = null) {
    return '<div class="dcmp-text">' . esc_html($content) . '</div>';
}
add_shortcode('dc_text', 'dcmp_shortcode_text'); 