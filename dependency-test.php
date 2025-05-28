<?php
/**
 * DC Media Protect 依赖测试页面
 */

// 引入WordPress环境
require_once 'wp-load.php';

// 检查PDF.js插件状态
$pdfjs_plugin_exists = file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php');
$pdfjs_plugin_active = is_plugin_active('pdfjs-viewer-shortcode/pdfjs-viewer-shortcode.php');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DC Media Protect 依赖测试</title>
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
        .status {
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .feature-box {
            border: 2px solid #007cba;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .yes { color: #28a745; font-weight: bold; }
        .no { color: #dc3545; font-weight: bold; }
        .partial { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 DC Media Protect 依赖分析</h1>
        
        <h2>📊 当前插件状态</h2>
        <div class="status <?php echo $pdfjs_plugin_exists ? 'success' : 'warning'; ?>">
            <strong>PDF.js Viewer插件:</strong> 
            <?php if ($pdfjs_plugin_exists): ?>
                ✅ 已安装 (路径: /wp-content/plugins/pdfjs-viewer-shortcode/)
            <?php else: ?>
                ⚠️ 未安装
            <?php endif; ?>
        </div>
        
        <div class="status info">
            <strong>DC Media Protect:</strong> ✅ 已安装并激活
        </div>
        
        <h2>🤔 DC Media Protect 是否依赖 PDF.js 插件？</h2>
        
        <div class="status success">
            <h3>答案：不是强依赖，但推荐安装</h3>
            <p>DC Media Protect 插件设计为<strong>智能适配</strong>，可以在有无 PDF.js 插件的情况下都能正常工作，但功能体验会有所不同。</p>
        </div>
        
        <h2>📋 功能对比分析</h2>
        
        <div class="comparison">
            <div class="feature-box">
                <h3>🚀 有 PDF.js 插件时</h3>
                <ul class="feature-list">
                    <li><span class="yes">✅</span> 优秀的PDF显示效果</li>
                    <li><span class="yes">✅</span> 内嵌式PDF查看器</li>
                    <li><span class="yes">✅</span> 缩放、搜索、翻页功能</li>
                    <li><span class="yes">✅</span> 多层水印保护</li>
                    <li><span class="yes">✅</span> 防下载、防打印</li>
                    <li><span class="yes">✅</span> 防右键、防拖拽</li>
                    <li><span class="yes">✅</span> 移动端优化</li>
                    <li><span class="yes">✅</span> 跨浏览器兼容</li>
                </ul>
            </div>
            
            <div class="feature-box">
                <h3>⚡ 无 PDF.js 插件时</h3>
                <ul class="feature-list">
                    <li><span class="partial">📱</span> 移动端：优化界面</li>
                    <li><span class="partial">💻</span> 桌面端：基础iframe显示</li>
                    <li><span class="yes">✅</span> 基础水印保护</li>
                    <li><span class="partial">⚠️</span> 有限的防下载功能</li>
                    <li><span class="yes">✅</span> 微信/QQ浏览器特殊处理</li>
                    <li><span class="yes">✅</span> 下载链接提供</li>
                    <li><span class="no">❌</span> 无内嵌PDF查看器</li>
                    <li><span class="no">❌</span> 无高级PDF功能</li>
                </ul>
            </div>
        </div>
        
        <h2>🔧 技术实现逻辑</h2>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-family: monospace; font-size: 12px;">
            <strong>DC Media Protect 的智能检测机制:</strong><br><br>
            1. 检测 PDF.js 插件是否存在<br>
            &nbsp;&nbsp;&nbsp;<code>file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php')</code><br><br>
            
            2. 如果存在 PDF.js 插件:<br>
            &nbsp;&nbsp;&nbsp;• 使用 PDF.js 提供高级PDF显示<br>
            &nbsp;&nbsp;&nbsp;• 添加多层水印保护<br>
            &nbsp;&nbsp;&nbsp;• 禁用下载/打印按钮<br><br>
            
            3. 如果不存在 PDF.js 插件:<br>
            &nbsp;&nbsp;&nbsp;• 移动端：显示优化的下载界面<br>
            &nbsp;&nbsp;&nbsp;• 桌面端：使用基础iframe显示<br>
            &nbsp;&nbsp;&nbsp;• 提供基础水印和防护<br>
        </div>
        
        <h2>💡 建议</h2>
        
        <?php if ($pdfjs_plugin_exists): ?>
            <div class="status success">
                <h3>✅ 当前配置最佳</h3>
                <p>您已经安装了 PDF.js Viewer 插件，DC Media Protect 正在提供最佳的PDF显示和保护体验。</p>
            </div>
        <?php else: ?>
            <div class="status warning">
                <h3>⚠️ 建议安装 PDF.js Viewer 插件</h3>
                <p>虽然 DC Media Protect 可以独立工作，但安装 PDF.js Viewer 插件可以获得更好的用户体验：</p>
                <ul>
                    <li>更好的PDF显示效果</li>
                    <li>内嵌式查看器</li>
                    <li>更强的防下载保护</li>
                    <li>更好的移动端体验</li>
                </ul>
                <p><strong>安装方法:</strong> WordPress后台 → 插件 → 安装插件 → 搜索 "PDF.js Viewer"</p>
            </div>
        <?php endif; ?>
        
        <h2>🧪 实际测试</h2>
        <p>以下是当前配置下的PDF显示效果：</p>
        
        <div style="border: 2px solid #007cba; border-radius: 8px; padding: 15px; margin: 20px 0; min-height: 400px;">
            <?php echo do_shortcode('[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]'); ?>
        </div>
        
        <h2>🔗 相关链接</h2>
        <a href="http://192.168.196.90:8080/" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🏠 返回首页</a>
        <a href="http://192.168.196.90:8080/watermark-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🎨 水印测试</a>
        <a href="http://192.168.196.90:8080/final-test.php" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:4px; margin:5px; display:inline-block;">🎯 最终测试</a>
    </div>
</body>
</html> 