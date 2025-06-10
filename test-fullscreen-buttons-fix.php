<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>全屏按钮修复测试</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
            line-height: 1.6;
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
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .info {
            background: #e7f3ff;
            border: 1px solid #b0d4f1;
            color: #0073aa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
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
    function plugins_url($path, $plugin) { 
        return 'http://localhost/wp-content/plugins' . str_replace('../../', '/', $path); 
    }
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

// 包含修复后的短码文件
require_once 'dc-media-protect/includes/shortcode.php';

// 测试PDF路径（模拟本地文件）
$test_pdf_local = 'http://localhost/wp-content/uploads/2025/05/test.pdf';
$test_pdf_external = 'https://example.com/test.pdf';
?>

<div class="container">
    <h1>🔳 全屏按钮修复测试</h1>
    
    <div class="success">
        <strong>✅ 修复完成！</strong><br>
        已解决全屏按钮不显示的问题。现在使用内联JavaScript确保功能正常工作。
    </div>
    
    <div class="info">
        <strong>📋 修复内容：</strong><br>
        1. 移除了全局变量依赖<br>
        2. 使用内联JavaScript，确保每个PDF查看器都有独立的全屏函数<br>
        3. 增强了按钮样式和交互效果<br>
        4. 添加了控制台调试信息<br>
        5. 改进了工具栏的视觉效果
    </div>
    
    <div class="test-section">
        <div class="test-title">📱 小尺寸PDF查看器测试</div>
        <div class="description">
            测试在小尺寸下全屏按钮是否清晰可见。注意右上角的工具栏。
        </div>
        <?php echo dcmp_shortcode_ppt(array('src' => $test_pdf_local, 'width' => 500, 'height' => 350)); ?>
    </div>
    
    <div class="test-section">
        <div class="test-title">🖥️ 大尺寸PDF查看器测试</div>
        <div class="description">
            测试在大尺寸下全屏按钮的布局和功能。工具栏应该位于右上角，背景半透明。
        </div>
        <?php echo dcmp_shortcode_ppt(array('src' => $test_pdf_local, 'width' => 800, 'height' => 500)); ?>
    </div>
    
    <div class="test-section">
        <div class="test-title">🌍 外部PDF文件测试</div>
        <div class="description">
            测试外部PDF文件的处理方式（不显示全屏按钮，提供下载链接）。
        </div>
        <?php echo dcmp_shortcode_ppt(array('src' => $test_pdf_external, 'width' => 600, 'height' => 400)); ?>
    </div>
    
    <div class="info">
        <strong>🎯 测试说明：</strong><br>
        1. <strong>全屏按钮</strong>：点击蓝色的"全屏"按钮应该让PDF进入全屏模式<br>
        2. <strong>新窗口按钮</strong>：点击灰色的"新窗口"按钮在新窗口中打开PDF<br>
        3. <strong>悬停效果</strong>：鼠标悬停在按钮上应该有颜色变化<br>
        4. <strong>控制台调试</strong>：打开浏览器开发者工具查看控制台输出<br>
        5. <strong>ESC退出</strong>：在全屏模式下按ESC键可以退出全屏
    </div>
    
    <div class="success">
        <strong>🚀 功能特性：</strong><br>
        ✅ 明显的全屏按钮（右上角）<br>
        ✅ 半透明工具栏背景<br>
        ✅ SVG图标美观显示<br>
        ✅ 悬停交互效果<br>
        ✅ 独立的JavaScript函数（避免冲突）<br>
        ✅ 控制台调试信息<br>
        ✅ 全屏模式下的自动调整<br>
        ✅ 多浏览器兼容性
    </div>
</div>

</body>
</html> 