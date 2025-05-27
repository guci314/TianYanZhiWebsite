<?php
/*
Plugin Name: DC Media Protect
Description: 数字中国多媒体防护与水印插件，实现视频、PPT、图片等内容的安全展示与防下载。
Version: 1.0.0
Author: Guci AI
*/

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 插件激活钩子
register_activation_hook(__FILE__, 'dcmp_activate_plugin');
function dcmp_activate_plugin() {
    // 清除重写规则缓存
    flush_rewrite_rules();
}

// 插件停用钩子
register_deactivation_hook(__FILE__, 'dcmp_deactivate_plugin');
function dcmp_deactivate_plugin() {
    flush_rewrite_rules();
}

require_once plugin_dir_path(__FILE__) . 'includes/upload-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/watermark.php';
require_once plugin_dir_path(__FILE__) . 'includes/ppt-convert.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/content-crawler.php';
require_once plugin_dir_path(__FILE__) . 'includes/mobile-pdf-viewer.php'; 

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('dcmp-frontend', plugins_url('assets/js/frontend.js', __FILE__), [], '1.0.0', true);
    wp_enqueue_style('dcmp-style', plugins_url('assets/css/style.css', __FILE__), [], '1.0.0');
}); 

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'includes/admin-pages.php';
    
    // 调试：确保admin-pages.php已加载
    add_action('admin_notices', function() {
        if (current_user_can('manage_options') && isset($_GET['dcmp_debug'])) {
            echo '<div class="notice notice-info"><p>✅ DC Media Protect admin-pages.php 已加载</p></div>';
        }
    });
}

// 添加调试信息到WordPress debug.log
add_action('init', function() {
    if (WP_DEBUG) {
        error_log('DC Media Protect plugin loaded successfully');
    }
}); 