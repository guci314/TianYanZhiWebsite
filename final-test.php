<?php
/**
 * 最终测试页面 - 验证PDF路径修复
 */

// 引入WordPress环境
require_once 'wp-load.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>最终PDF测试</title>
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
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-box {
            border: 2px solid #007cba;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            min-height: 600px;
        }
        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 最终PDF测试 - 路径修复验证</h1>
        
        <div class="status success">
            ✅ <strong>修复内容:</strong> 修正了PDF.js viewer的URL构建问题
        </div>
        
        <div class="status info">
            📋 <strong>测试目标:</strong> 验证相对路径 <code>wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf</code> 是否能正常显示
        </div>
        
        <h2>用户原始短代码测试</h2>
        <p><strong>短代码:</strong> <code>[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]</code></p>
        <p><strong>期望结果:</strong> 显示PDF.js查看器，带有水印和防下载功能</p>
        
        <div class="test-box">
            <?php echo do_shortcode('[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]'); ?>
        </div>
        
        <h2>📊 技术信息</h2>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px;">
            <strong>修复详情:</strong><br>
            • 原问题: plugin_dir_url() 生成了错误的URL路径<br>
            • 修复方案: 使用 home_url() + 相对路径构建正确的PDF.js viewer URL<br>
            • 路径处理: 自动将 "wp-content/" 转换为 "/wp-content/"<br>
            • 安全措施: 保留水印、防下载、防右键等保护功能<br>
        </div>
        
        <h2>🔗 相关链接</h2>
        <a href="http://192.168.196.90:8080/" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🏠 返回首页</a>
        <a href="http://192.168.196.90:8080/path-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">📊 路径测试</a>
        <a href="http://192.168.196.90:8080/enhanced-pdf-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🧪 增强测试</a>
    </div>
    
    <div class="container">
        <h2>🔍 故障排除</h2>
        <p>如果PDF仍然无法显示，请检查:</p>
        <ul>
            <li>PDF文件是否存在于 <code>/wp-content/uploads/2025/05/</code> 目录</li>
            <li>PDF.js Viewer插件是否正确安装</li>
            <li>浏览器控制台是否有JavaScript错误</li>
            <li>网络连接是否正常</li>
        </ul>
        
        <h3>直接测试链接</h3>
        <a href="http://192.168.196.90:8080/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf" target="_blank" style="background:#28a745; color:white; padding:8px 16px; text-decoration:none; border-radius:4px;">📄 直接访问PDF文件</a>
        <a href="http://192.168.196.90:8080/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php?file=/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf" target="_blank" style="background:#17a2b8; color:white; padding:8px 16px; text-decoration:none; border-radius:4px; margin-left:10px;">🔧 直接测试PDF.js</a>
    </div>
</body>
</html> 