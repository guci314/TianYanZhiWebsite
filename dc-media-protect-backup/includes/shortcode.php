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

// 检测微信浏览器
function dcmp_is_wechat_browser() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return strpos($user_agent, 'MicroMessenger') !== false;
}

// 检测QQ浏览器
function dcmp_is_qq_browser() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return strpos($user_agent, 'QQBrowser') !== false || strpos($user_agent, 'MQQBrowser') !== false;
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
    $src = isset($atts['src']) ? $atts['src'] : '';  // 先不使用esc_url，避免路径被修改
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    
    // 处理相对路径，确保PDF.js能正确访问
    $processed_src = $src;
    if (strpos($src, 'wp-content/') === 0) {
        // 如果是相对路径（不以/开头），转换为绝对路径
        $processed_src = '/' . $src;
    }
    
    // 现在安全地使用esc_url
    $processed_src = esc_url($processed_src);
    
    // 检测移动设备和具体设备类型
    $is_mobile = dcmp_is_mobile_device();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
    $is_android = strpos($user_agent, 'Android') !== false;
    $is_wechat = dcmp_is_wechat_browser();
    $is_qq_browser = dcmp_is_qq_browser();
    
    // 检查是否为本地文件（包括data: URL和相对路径，以及同网段的WordPress上传文件）
    $home_url = home_url();
    $is_local = (strpos($processed_src, $home_url) === 0) || 
                (strpos($processed_src, 'data:') === 0) || 
                (strpos($processed_src, '/wp-content/') === 0) ||
                (strpos($src, 'wp-content/') === 0) ||  // 检查原始路径中的相对路径
                (strpos($processed_src, 'wp-content/uploads/') !== false) ||  // 包含uploads路径的URL
                (strpos($src, 'wp-content/uploads/') !== false);  // 原始路径中的uploads
    
    $viewer_html = '';
    $container_id = 'dcmp-pdf-' . uniqid();
    
    // 可见的调试信息框
    $visible_debug = '
    <div style="background: #ff0000; color: white; padding: 15px; margin: 10px 0; border: 3px solid #ffff00; font-family: monospace; font-size: 12px; line-height: 1.4;">
        <strong>🔍 DC PPT 调试信息 (根目录版本)</strong><br>
        <strong>原始路径:</strong> ' . esc_html($src) . '<br>
        <strong>处理后路径:</strong> ' . esc_html($processed_src) . '<br>
        <strong>Home URL:</strong> ' . esc_html($home_url) . '<br>
        <strong>是否本地文件:</strong> ' . ($is_local ? '✅ 是' : '❌ 否') . '<br>
        <strong>uploads路径匹配:</strong> ' . (strpos($processed_src, 'wp-content/uploads/') !== false ? '✅ 是' : '❌ 否') . '<br>
        <strong>PDF.js插件:</strong> ' . (file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php') ? '✅ 存在' : '❌ 不存在') . '<br>
        <strong>进入分支:</strong> ' . ($is_local ? '本地文件处理' : '外部文件处理') . '
    </div>';
    
    if ($is_local) {
        // 简化的全屏按钮实现
        $viewer_html = $visible_debug . '
        <div style="background: #0073aa; padding: 10px; margin-bottom: 10px; border-radius: 5px;">
            <button onclick="alert(\'全屏功能测试 - 这是根目录版本\')" 
                    style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                🔳 全屏查看 (根目录版本)
            </button>
            <button onclick="window.open(\'' . esc_url($processed_src) . '\', \'_blank\')" 
                    style="background: #007cba; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; margin-left: 10px;">
                🗗 新窗口打开
            </button>
        </div>
        <div style="width:800px; height:600px; border:1px solid #ccc;">
            <iframe src="' . esc_url($processed_src) . '" width="100%" height="100%" style="border:none;"></iframe>
        </div>';
    } else {
        $viewer_html = $visible_debug . '
        <div style="width:800px; height:600px; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; flex-direction:column; background:#f9f9f9;">
            <p>外部PDF文件</p>
            <a href="' . esc_url($src) . '" target="_blank" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:3px;">
                📄 打开PDF文档
            </a>
        </div>';
    }
    
    $watermark = '<div class="dcmp-watermark" style="position:absolute; bottom:10px; right:10px; background:rgba(0,0,0,0.6); color:white; padding:4px 8px; border-radius:3px; font-size:11px; z-index:1000; pointer-events:none;">' . dcmp_get_watermark_text() . '</div>';
    return '<div class="dcmp-media-container" style="position:relative;">'
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