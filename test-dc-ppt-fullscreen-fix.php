<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DC PPT 全屏功能测试 - 修复版</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: #fafafa;
        }
        .test-title {
            font-weight: bold;
            font-size: 18px;
            color: #0073aa;
            margin-bottom: 15px;
        }
        .description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .features {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .features h3 {
            margin-top: 0;
            color: #0073aa;
        }
        .features ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .features li {
            margin: 8px 0;
            color: #333;
        }
        .note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .note strong {
            color: #856404;
        }
    </style>
</head>
<body>

<?php
// 模拟WordPress环境
if (!function_exists('home_url')) {
    function home_url() { return 'http://localhost'; }
}
if (!function_exists('plugins_url')) {
    function plugins_url($path, $plugin) { return 'http://localhost/wp-content/plugins' . str_replace('../../', '/', $path); }
}
if (!function_exists('wp_is_mobile')) {
    function wp_is_mobile() { return false; }
}
if (!function_exists('esc_url')) {
    function esc_url($url) { return htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('esc_html')) {
    function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('esc_attr')) {
    function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('get_option')) {
    function get_option($option, $default = false) { 
        return $option === 'dcmp_watermark_text' ? '数字中国' : $default; 
    }
}

// 包含短码文件
require_once 'dc-media-protect/includes/shortcode.php';

// 模拟全屏JavaScript已添加的状态
$dcmp_fullscreen_js_added = false;

// 测试PDF路径
$test_pdf = 'http://localhost/wp-content/uploads/2025/05/test.pdf';
?>

<div class="container">
    <h1>🔳 DC PPT 全屏功能测试 - 修复版</h1>
    
    <div class="features">
        <h3>✨ 新增功能特性</h3>
        <ul>
            <li><strong>原生全屏支持：</strong>使用HTML5 Fullscreen API实现真正的全屏模式</li>
            <li><strong>手势缩放：</strong>全屏模式下支持触摸手势缩放和平移</li>
            <li><strong>多浏览器兼容：</strong>支持Chrome、Firefox、Safari、Edge等主流浏览器</li>
            <li><strong>备用方案：</strong>如果全屏API不支持，自动回退到新窗口打开</li>
            <li><strong>双按钮设计：</strong>全屏按钮 + 新窗口按钮，提供更多选择</li>
            <li><strong>视觉优化：</strong>使用SVG图标，界面更美观</li>
        </ul>
    </div>
    
    <div class="test-section">
        <div class="test-title">📱 基础PDF查看器（小尺寸）</div>
        <div class="description">
            演示在较小尺寸下的PDF查看器，现在可以看到明显的全屏按钮。
        </div>
        <?php echo dcmp_shortcode_ppt(array('src' => $test_pdf, 'width' => 600, 'height' => 400)); ?>
    </div>
    
    <div class="test-section">
        <div class="test-title">🖥️ 大尺寸PDF查看器</div>
        <div class="description">
            演示在较大尺寸下的PDF查看器，全屏按钮位于右上角。
        </div>
        <?php echo dcmp_shortcode_ppt(array('src' => $test_pdf, 'width' => 900, 'height' => 600)); ?>
    </div>
    
    <div class="note">
        <strong>使用说明：</strong><br>
        1. 点击 <strong>🔳 全屏</strong> 按钮进入原生全屏模式，支持手势缩放<br>
        2. 点击 <strong>🗗 新窗口</strong> 按钮在新窗口中打开PDF<br>
        3. 全屏模式下可以使用手势或鼠标滚轮进行缩放<br>
        4. 按ESC键可退出全屏模式
    </div>
    
    <div class="features">
        <h3>🔧 技术改进</h3>
        <ul>
            <li><strong>JavaScript优化：</strong>避免重复定义，使用WordPress footer hook</li>
            <li><strong>全屏API：</strong>使用现代HTML5 Fullscreen API</li>
            <li><strong>响应式设计：</strong>全屏时自动调整iframe尺寸</li>
            <li><strong>跨浏览器兼容：</strong>处理不同浏览器的全屏API差异</li>
            <li><strong>用户体验：</strong>添加tooltips和视觉反馈</li>
        </ul>
    </div>
</div>

<?php
// 模拟WordPress footer，输出JavaScript
if ($dcmp_fullscreen_js_added) {
    dcmp_add_fullscreen_js();
}
?>

</body>
</html>
