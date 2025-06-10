<?php
/**
 * 测试DC Media Protect插件的全屏按钮功能
 */

// 模拟WordPress环境
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// 模拟WordPress函数
if (!function_exists('home_url')) {
    function home_url() {
        return 'http://localhost';
    }
}

if (!function_exists('plugins_url')) {
    function plugins_url($path, $plugin = '') {
        return 'http://localhost/wp-content/plugins' . $path;
    }
}

if (!function_exists('wp_is_mobile')) {
    function wp_is_mobile() {
        return false;
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        if ($option === 'dcmp_watermark_text') {
            return '数字中国';
        }
        return $default;
    }
}

// 包含插件文件
require_once 'dc-media-protect/includes/shortcode.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DC Media Protect - 全屏按钮测试</title>
    <link rel="stylesheet" href="dc-media-protect/assets/css/style.css">
</head>
<body>
    <h1>DC Media Protect - 全屏按钮功能测试</h1>
    
    <h2>测试PDF显示（带全屏按钮）</h2>
    <div style="margin: 20px 0;">
        <?php
        // 测试本地PDF文件
        $test_pdf_url = home_url() . '/wp-content/uploads/test.pdf';
        echo dcmp_shortcode_ppt(['src' => $test_pdf_url, 'width' => 800, 'height' => 600]);
        ?>
    </div>
    
    <h2>功能说明</h2>
    <ul>
        <li>✅ 移除了双击全屏功能</li>
        <li>✅ 添加了全屏按钮（位于PDF查看器右上角）</li>
        <li>✅ 点击全屏按钮可以进入全屏模式</li>
        <li>✅ 按ESC键或点击浏览器退出全屏按钮可以退出全屏</li>
        <li>✅ 支持所有主流浏览器（Chrome、Firefox、Safari、Edge）</li>
        <li>✅ 保留了原有的水印功能</li>
    </ul>
    
    <h2>技术实现</h2>
    <ul>
        <li>使用浏览器原生的Fullscreen API</li>
        <li>兼容Webkit和Mozilla浏览器前缀</li>
        <li>降级方案：在不支持全屏API的浏览器中在新窗口打开</li>
        <li>CSS样式优化，提供良好的用户体验</li>
    </ul>
    
    <script src="dc-media-protect/assets/js/frontend.js"></script>
</body>
</html> 