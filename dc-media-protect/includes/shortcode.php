<?php
// 短代码注册模块

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
    
    // 检测移动设备
    $is_mobile = wp_is_mobile();
    
    $w = $width > 0 ? $width : ($is_mobile ? 350 : 800);
    $h = $height > 0 ? $height : ($is_mobile ? 500 : 600);
    
    // 检查是否为本地文件
    $is_local = (strpos($src, home_url()) === 0);
    
    $viewer_html = '';
    
    if ($is_local) {
        // 本地PDF文件，直接iframe嵌入
        $viewer_html = '<iframe src="' . $src . '" width="' . $w . '" height="' . $h . '" style="border:1px solid #ccc;"></iframe>';
    } else {
        // 外部PDF文件，提供下载链接和说明
        $viewer_html = '
        <div style="width:' . $w . 'px; height:' . $h . 'px; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; flex-direction:column; background:#f9f9f9;">
            <p style="margin:10px; text-align:center;">
                <strong>PDF文档预览</strong><br>
                <small>外部PDF链接可能无法直接预览</small>
            </p>
            <a href="' . $src . '" target="_blank" class="button" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:3px;">
                📄 打开PDF文档
            </a>
            <p style="margin:10px; font-size:12px; color:#666; text-align:center;">
                建议：将PDF文件上传到媒体库以获得更好的预览效果
            </p>
        </div>';
    }
    
    $watermark = '<div class="dcmp-watermark">' . dcmp_get_watermark_text() . '</div>';
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