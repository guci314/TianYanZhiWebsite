<?php
/**
 * 简单测试页面 - 验证用户的具体问题
 */

// 引入WordPress环境
require_once 'wp-load.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>简单PDF测试</title>
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
            min-height: 500px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 用户问题测试</h1>
        
        <h2>用户使用的短代码</h2>
        <p><strong>短代码:</strong> <code>[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]</code></p>
        <p><strong>问题:</strong> 在电脑上访问显示"外部链接无法直接预览"</p>
        <p><strong>期望:</strong> 应该正常显示PDF，带有水印和防下载功能</p>
        
        <h2>测试结果</h2>
        <div class="test-box">
            <?php echo do_shortcode('[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]'); ?>
        </div>
        
        <h2>🔍 说明</h2>
        <p>如果上面显示的是PDF查看器（带有水印），说明问题已经解决。</p>
        <p>如果显示"外部链接无法直接预览"，说明还需要进一步调试。</p>
        
        <h2>🔗 其他测试</h2>
        <a href="http://192.168.196.90:8080/path-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">📊 详细路径测试</a>
        <a href="http://192.168.196.90:8080/enhanced-pdf-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🧪 增强功能测试</a>
        <a href="http://192.168.196.90:8080/" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🏠 返回首页</a>
    </div>
</body>
</html> 