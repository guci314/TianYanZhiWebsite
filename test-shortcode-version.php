<?php
// 确定哪个shortcode文件在生效
require_once('./wp-load.php');

// 强制显示当前注册的短码信息
echo '<h1>短码调试测试</h1>';

echo '<h2>当前注册的短码列表</h2>';
global $shortcode_tags;
if (isset($shortcode_tags['dc_ppt'])) {
    echo '<p><strong>dc_ppt短码已注册</strong></p>';
    echo '<p>回调函数: ' . $shortcode_tags['dc_ppt'] . '</p>';
    
    // 获取函数反射信息
    if (function_exists($shortcode_tags['dc_ppt'])) {
        $reflection = new ReflectionFunction($shortcode_tags['dc_ppt']);
        echo '<p>函数定义文件: ' . $reflection->getFileName() . '</p>';
        echo '<p>函数定义行号: ' . $reflection->getStartLine() . '</p>';
    }
} else {
    echo '<p style="color: red;">dc_ppt短码未注册！</p>';
}

echo '<h2>直接测试短码输出</h2>';
$test_output = do_shortcode('[dc_ppt src="http://192.168.29.90:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]');
echo '<div style="border: 2px solid blue; padding: 10px;">';
echo $test_output;
echo '</div>';

echo '<h2>查看生成的HTML源码</h2>';
echo '<textarea rows="20" style="width: 100%; font-family: monospace;">';
echo htmlspecialchars($test_output);
echo '</textarea>';
?>