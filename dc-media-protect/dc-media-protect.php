<?php
/*
Plugin Name: DC Media Protect
Description: 数字中国多媒体防护与水印插件，实现视频、PPT、图片等内容的安全展示与防下载。
Version: 1.0.0
Author: Guci AI
*/

// 插件初始化代码将在后续步骤补充 

require_once plugin_dir_path(__FILE__) . 'includes/upload-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/watermark.php';
require_once plugin_dir_path(__FILE__) . 'includes/ppt-convert.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/content-crawler.php'; 

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('dcmp-frontend', plugins_url('assets/js/frontend.js', __FILE__), [], '1.0.0', true);
    wp_enqueue_style('dcmp-style', plugins_url('assets/css/style.css', __FILE__), [], '1.0.0');
}); 

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
} 