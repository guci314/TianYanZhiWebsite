<?php
/**
 * PDF Embedder Plugin Test
 * 测试PDF Embedder插件是否已正确安装
 */

// 模拟WordPress环境（简化版）
define('ABSPATH', __DIR__ . '/');

// 检查插件文件是否存在
$plugin_file = 'wp-content/plugins/pdf-embedder/pdf_embedder.php';
$plugin_exists = file_exists($plugin_file);

echo "<h1>PDF Embedder 插件安装检查</h1>";

if ($plugin_exists) {
    echo "<p style='color: green;'>✓ PDF Embedder 插件文件已找到</p>";
    
    // 读取插件头信息
    $plugin_data = get_file_data($plugin_file);
    
    if (!empty($plugin_data)) {
        echo "<h2>插件信息：</h2>";
        echo "<ul>";
        foreach ($plugin_data as $key => $value) {
            if (!empty($value)) {
                echo "<li><strong>$key:</strong> $value</li>";
            }
        }
        echo "</ul>";
    }
    
    echo "<h2>安装状态：</h2>";
    echo "<p style='color: green;'>✓ 插件已成功安装到 wp-content/plugins/pdf-embedder/</p>";
    echo "<p>现在您可以在WordPress管理后台的插件页面激活此插件。</p>";
    
    echo "<h2>使用说明：</h2>";
    echo "<ol>";
    echo "<li>登录WordPress管理后台</li>";
    echo "<li>导航到 '插件' → '已安装的插件'</li>";
    echo "<li>找到 'PDF Embedder' 插件并点击 '激活'</li>";
    echo "<li>激活后，您可以使用短代码 [pdf-embedder url='PDF文件URL'] 来嵌入PDF</li>";
    echo "</ol>";
    
} else {
    echo "<p style='color: red;'>✗ PDF Embedder 插件文件未找到</p>";
    echo "<p>请确保插件已正确复制到 wp-content/plugins/pdf-embedder/ 目录</p>";
}

function get_file_data($file) {
    $data = array();
    $content = file_get_contents($file);
    
    // 提取插件头信息
    $headers = array(
        'Plugin Name' => 'Plugin Name',
        'Plugin URI' => 'Plugin URI', 
        'Description' => 'Description',
        'Version' => 'Version',
        'Author' => 'Author',
        'Author URI' => 'Author URI',
        'Text Domain' => 'Text Domain'
    );
    
    foreach ($headers as $field => $regex) {
        if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $content, $match) && $match[1]) {
            $data[$field] = trim(preg_replace('/\s*(?:\*\/|\?>).*/i', '', $match[1]));
        }
    }
    
    return $data;
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    line-height: 1.6;
}
h1 {
    color: #333;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
}
h2 {
    color: #555;
    margin-top: 30px;
}
li {
    margin: 5px 0;
}
</style> 