<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>全屏按钮调试</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f0f0f0; 
        }
        .debug-box { 
            background: white; 
            padding: 20px; 
            margin: 20px 0; 
            border-radius: 5px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
        }
        .highlight { 
            background: yellow; 
            padding: 2px 5px; 
            border-radius: 3px; 
        }
    </style>
</head>
<body>

<div class="debug-box">
    <h1>🔍 全屏按钮调试页面</h1>
    <p>这个页面将显示dc_ppt短码生成的HTML，帮助您验证全屏按钮是否正确显示。</p>
</div>

<?php
// 简化的WordPress函数模拟
function home_url() { return 'http://localhost'; }
function plugins_url($path, $plugin) { 
    return 'http://localhost/wp-content/plugins' . str_replace('../../', '/', $path); 
}
function wp_is_mobile() { return false; }
function esc_url($url) { return htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); }
function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
function get_option($option, $default = false) { 
    return $option === 'dcmp_watermark_text' ? '数字中国' : $default; 
}

// 包含短码文件
require_once 'dc-media-protect/includes/shortcode.php';

// 测试参数
$test_attrs = array(
    'src' => 'http://localhost/wp-content/uploads/2025/05/test.pdf',
    'width' => 600,
    'height' => 400
);

echo '<div class="debug-box">';
echo '<h2>📝 短码调用参数</h2>';
echo '<pre>' . print_r($test_attrs, true) . '</pre>';
echo '</div>';

echo '<div class="debug-box">';
echo '<h2>🎯 生成的HTML输出</h2>';
echo '<p>下面是dc_ppt短码生成的完整HTML代码：</p>';

// 生成输出
$output = dcmp_shortcode_ppt($test_attrs);

echo '<textarea rows="20" cols="100" style="width:100%; font-family:monospace; font-size:12px;">';
echo htmlspecialchars($output);
echo '</textarea>';
echo '</div>';

echo '<div class="debug-box">';
echo '<h2>🖼️ 实际渲染效果</h2>';
echo '<p>下面是实际的PDF查看器，请检查 <span class="highlight">右上角是否有全屏按钮</span>：</p>';
echo $output;
echo '</div>';

echo '<div class="debug-box">';
echo '<h2>✅ 检查清单</h2>';
echo '<ul>';
echo '<li>□ 可以看到右上角的工具栏吗？</li>';
echo '<li>□ 工具栏中有两个按钮吗？</li>';
echo '<li>□ 蓝色的"全屏"按钮是否可见？</li>';
echo '<li>□ 灰色的"新窗口"按钮是否可见？</li>';
echo '<li>□ 按钮上有SVG图标吗？</li>';
echo '<li>□ 鼠标悬停时按钮颜色会变化吗？</li>';
echo '</ul>';
echo '</div>';
?>

<script>
// 额外的调试脚本
document.addEventListener('DOMContentLoaded', function() {
    console.log('页面加载完成');
    
    // 查找所有的全屏按钮
    const fullscreenBtns = document.querySelectorAll('button[onclick*="dcmpEnterFullscreen"]');
    console.log('找到全屏按钮数量:', fullscreenBtns.length);
    
    fullscreenBtns.forEach((btn, index) => {
        console.log(`全屏按钮 ${index + 1}:`, btn);
        console.log('按钮文本:', btn.textContent.trim());
        console.log('按钮样式:', btn.style.cssText);
        
        // 给按钮添加红色边框便于识别
        btn.style.border = '2px solid red';
        btn.style.animation = 'blink 1s infinite';
    });
    
    // 添加闪烁动画
    const style = document.createElement('style');
    style.textContent = `
        @keyframes blink {
            0% { border-color: red; }
            50% { border-color: transparent; }
            100% { border-color: red; }
        }
    `;
    document.head.appendChild(style);
});
</script>

</body>
</html> 