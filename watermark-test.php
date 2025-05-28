<?php
/**
 * 水印测试页面 - 验证增强水印功能
 */

// 引入WordPress环境
require_once 'wp-load.php';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>水印功能测试</title>
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
            min-height: 650px;
        }
        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 水印功能测试</h1>
        
        <div class="status success">
            ✅ <strong>水印增强:</strong> 已修复数据库中的水印文本，增强了水印显示效果
        </div>
        
        <div class="status info">
            📋 <strong>水印特性:</strong> 
            • 右上角主水印：🔒 数字中国<br>
            • 左下角版权信息：版权保护 - 禁止下载<br>
            • 中心大水印：半透明旋转显示<br>
            • 右下角时间戳：当前时间<br>
            • 左上角标识：受保护文档
        </div>
        
        <div class="status warning">
            ⚠️ <strong>注意:</strong> 如果看不到水印，请检查浏览器是否阻止了CSS样式或JavaScript
        </div>
        
        <h2>增强水印测试</h2>
        <p><strong>短代码:</strong> <code>[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]</code></p>
        <p><strong>期望结果:</strong> PDF正常显示 + 多层水印保护</p>
        
        <div class="test-box">
            <?php echo do_shortcode('[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]'); ?>
        </div>
        
        <h2>📊 水印技术信息</h2>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px;">
            <strong>水印实现方式:</strong><br>
            • CSS z-index: 999999 确保在最上层<br>
            • pointer-events: none 不影响PDF操作<br>
            • position: absolute 绝对定位<br>
            • 多个水印元素提供全面保护<br>
            • 背景渐变增加视觉效果<br>
            • 时间戳防止截图重用<br>
        </div>
        
        <h2>🔍 水印检查清单</h2>
        <ul>
            <li>✅ 右上角是否显示 "🔒 数字中国"</li>
            <li>✅ 左下角是否显示 "版权保护 - 禁止下载"</li>
            <li>✅ 中心是否有半透明的大水印</li>
            <li>✅ 右下角是否显示当前时间</li>
            <li>✅ 左上角是否显示 "受保护文档"</li>
            <li>✅ 背景是否有渐变水印图案</li>
        </ul>
        
        <h2>🔗 相关链接</h2>
        <a href="http://192.168.196.90:8080/" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🏠 返回首页</a>
        <a href="http://192.168.196.90:8080/final-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🎯 最终测试</a>
        <a href="http://192.168.196.90:8080/simple-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">📄 简单测试</a>
    </div>
    
    <div class="container">
        <h2>🛠️ 故障排除</h2>
        <p>如果水印仍然不显示，可能的原因：</p>
        <ul>
            <li><strong>浏览器缓存:</strong> 按 Ctrl+F5 强制刷新页面</li>
            <li><strong>CSS被阻止:</strong> 检查浏览器是否阻止了内联样式</li>
            <li><strong>z-index冲突:</strong> PDF.js可能有更高的z-index</li>
            <li><strong>iframe限制:</strong> 某些浏览器对iframe内容有限制</li>
        </ul>
        
        <h3>当前水印设置</h3>
        <?php
        $watermark_text = get_option('dcmp_watermark_text', '数字中国');
        $watermark_opacity = get_option('dcmp_watermark_opacity', '0.3');
        echo '<p><strong>水印文本:</strong> ' . esc_html($watermark_text) . '</p>';
        echo '<p><strong>水印透明度:</strong> ' . esc_html($watermark_opacity) . '</p>';
        ?>
    </div>
</body>
</html> 