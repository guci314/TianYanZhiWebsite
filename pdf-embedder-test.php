<?php
/**
 * PDF Embedder 测试页面
 * 演示正确的短代码使用方法
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Embedder 使用说明</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .problem {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .solution {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .code {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 10px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .incorrect {
            color: #dc3545;
        }
        .correct {
            color: #28a745;
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
        .step {
            margin: 15px 0;
            padding: 10px;
            border-left: 4px solid #0073aa;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>📄 PDF Embedder 使用问题解决方案</h1>
    
    <div class="problem">
        <h2>🚫 问题诊断</h2>
        <p><strong>您使用的短代码：</strong></p>
        <div class="code incorrect">
            [pdf-embedder src="http://localhost:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
        <p><strong>问题原因：</strong></p>
        <ul>
            <li>❌ 参数错误：使用了 <code>src</code> 参数，应该使用 <code>url</code></li>
            <li>⚠️ 路径问题：使用了完整的localhost URL，可能导致跨域问题</li>
        </ul>
    </div>
    
    <div class="solution">
        <h2>✅ 正确解决方案</h2>
        <p><strong>正确的短代码格式：</strong></p>
        <div class="code correct">
            [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
        <p><strong>或者使用完整URL：</strong></p>
        <div class="code correct">
            [pdf-embedder url="<?php echo 'http://localhost:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf'; ?>"]
        </div>
    </div>
    
    <h2>🔧 修复步骤</h2>
    
    <div class="step">
        <h3>步骤 1: 更正短代码参数</h3>
        <p>将 <code>src</code> 改为 <code>url</code>：</p>
        <div class="code">
            <span class="incorrect">❌ [pdf-embedder src="..."]</span><br>
            <span class="correct">✅ [pdf-embedder url="..."]</span>
        </div>
    </div>
    
    <div class="step">
        <h3>步骤 2: 确认插件已激活</h3>
        <p>在WordPress后台检查插件状态：</p>
        <ol>
            <li>登录WordPress管理后台</li>
            <li>导航到 <strong>插件 → 已安装的插件</strong></li>
            <li>确认 <strong>PDF Embedder</strong> 插件状态为"已激活"</li>
        </ol>
    </div>
    
    <div class="step">
        <h3>步骤 3: 检查文件路径</h3>
        <p>✅ PDF文件确认存在于：<code>wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf</code></p>
        <p>文件大小：809KB</p>
    </div>
    
    <h2>📋 其他可选参数</h2>
    <p>PDF Embedder支持以下参数来自定义显示效果：</p>
    
    <div class="code">
        [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" width="800" height="600"]
    </div>
    
    <table border="1" style="border-collapse: collapse; width: 100%; margin: 20px 0;">
        <tr style="background-color: #f8f9fa;">
            <th style="padding: 10px;">参数</th>
            <th style="padding: 10px;">说明</th>
            <th style="padding: 10px;">示例</th>
        </tr>
        <tr>
            <td style="padding: 10px;"><code>url</code></td>
            <td style="padding: 10px;">PDF文件的URL路径</td>
            <td style="padding: 10px;"><code>url="path/to/file.pdf"</code></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><code>width</code></td>
            <td style="padding: 10px;">宽度（像素或"max"）</td>
            <td style="padding: 10px;"><code>width="800"</code> 或 <code>width="max"</code></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><code>height</code></td>
            <td style="padding: 10px;">高度（像素或"auto"）</td>
            <td style="padding: 10px;"><code>height="600"</code> 或 <code>height="auto"</code></td>
        </tr>
        <tr>
            <td style="padding: 10px;"><code>toolbar</code></td>
            <td style="padding: 10px;">工具栏位置</td>
            <td style="padding: 10px;"><code>toolbar="top"</code>, <code>"bottom"</code>, <code>"both"</code>, <code>"none"</code></td>
        </tr>
    </table>
    
    <h2>🎯 针对您项目的建议</h2>
    <p>根据您的readme.md中的需求，建议使用以下配置：</p>
    
    <div class="step">
        <h3>防下载保护配置</h3>
        <div class="code">
            [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" width="max" toolbar="none"]
        </div>
        <p>这样配置可以：</p>
        <ul>
            <li>隐藏工具栏，减少下载入口</li>
            <li>设置最大宽度，提升用户体验</li>
            <li>结合您现有的防下载JS代码</li>
        </ul>
    </div>
    
    <h2>🔍 故障排除</h2>
    <p>如果PDF仍然不显示，请检查：</p>
    <ol>
        <li><strong>浏览器控制台</strong>：按F12查看是否有JavaScript错误</li>
        <li><strong>网络选项卡</strong>：确认PDF文件是否成功加载（状态码200）</li>
        <li><strong>插件冲突</strong>：暂时停用其他插件进行测试</li>
        <li><strong>主题兼容性</strong>：尝试切换到默认主题测试</li>
    </ol>
    
    <div class="solution">
        <h2>✨ 立即修复</h2>
        <p><strong>请将您文章中的短代码替换为：</strong></p>
        <div class="code correct" style="font-size: 16px; font-weight: bold;">
            [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
    </div>
    
</body>
</html> 