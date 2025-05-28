<?php
/**
 * 路径测试页面
 */

// 引入WordPress环境
require_once 'wp-load.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>路径测试</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .test-box {
            border: 2px solid #007cba;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 PDF路径测试</h1>
        
        <h2>测试1: 相对路径（无前导斜杠）</h2>
        <p><strong>短代码:</strong> <code>[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]</code></p>
        <div class="test-box">
            <?php echo do_shortcode('[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]'); ?>
        </div>
        
        <h2>测试2: 绝对路径（有前导斜杠）</h2>
        <p><strong>短代码:</strong> <code>[dc_ppt src="/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]</code></p>
        <div class="test-box">
            <?php echo do_shortcode('[dc_ppt src="/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]'); ?>
        </div>
        
        <h2>测试3: 完整URL路径</h2>
        <p><strong>短代码:</strong> <code>[dc_ppt src="http://192.168.196.90:8080/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]</code></p>
        <div class="test-box">
            <?php echo do_shortcode('[dc_ppt src="http://192.168.196.90:8080/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]'); ?>
        </div>
        
        <h2>🔍 调试信息</h2>
        <p>查看页面源代码中的HTML注释，可以看到路径处理的调试信息。</p>
        
        <h2>🔗 快速链接</h2>
        <a href="http://192.168.196.90:8080/" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🏠 返回首页</a>
        <a href="http://192.168.196.90:8080/enhanced-pdf-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🧪 增强测试页面</a>
    </div>
</body>
</html> 