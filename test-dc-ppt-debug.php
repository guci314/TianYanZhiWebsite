<?php
// 测试 dc_ppt 短码的调试
require_once('/home/guci/congqing/website/wp-content/plugins/dc-media-protect/includes/shortcode.php');

// 模拟 WordPress 环境
if (!function_exists('esc_url')) {
    function esc_url($url) { return $url; }
}
if (!function_exists('esc_html')) {
    function esc_html($text) { return htmlspecialchars($text); }
}
if (!function_exists('esc_attr')) {
    function esc_attr($text) { return htmlspecialchars($text); }
}
if (!function_exists('get_option')) {
    function get_option($name, $default = '') { 
        if ($name === 'dcmp_watermark_text') return '数字中国';
        return $default; 
    }
}
if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) { return 'test_nonce_12345'; }
}
if (!function_exists('home_url')) {
    function home_url($path = '') { return 'http://localhost:8080' . $path; }
}
if (!defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', '/home/guci/congqing/website/wp-content/plugins');
}

// 设置测试参数
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Mobile Safari/537.36 MicroMessenger/8.0.1';

echo "<h1>DC PPT 短码调试测试</h1>";

// 测试一个已知存在的PDF文件
$test_pdf = 'wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf';

echo "<h2>测试PDF: $test_pdf</h2>";

$result = dcmp_shortcode_ppt(['src' => $test_pdf, 'width' => '800', 'height' => '600']);

echo "<h3>生成的HTML:</h3>";
echo "<pre>" . htmlspecialchars($result) . "</pre>";

echo "<h3>渲染结果:</h3>";
echo $result;
?>