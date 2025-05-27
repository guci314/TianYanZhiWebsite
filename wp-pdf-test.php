<?php
/**
 * WordPress PDF Embedder 功能测试
 * 这个文件模拟WordPress环境来测试PDF Embedder短代码
 */

// 模拟WordPress的基本函数
if (!function_exists('do_shortcode')) {
    function do_shortcode($content) {
        // 简化的短代码处理
        return $content;
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Embedder 功能测试</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .test-container {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            background-color: #f9f9f9;
        }
        .shortcode-display {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            word-break: break-all;
        }
        .pdf-container {
            border: 1px solid #ccc;
            border-radius: 4px;
            min-height: 600px;
            background-color: white;
            margin: 20px 0;
            padding: 20px;
            text-align: center;
        }
        .error {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>📄 PDF Embedder 功能测试</h1>
    
    <div class="test-container">
        <h2>🧪 测试1: 原始错误短代码</h2>
        <p><strong>您使用的错误短代码：</strong></p>
        <div class="shortcode-display">
            [pdf-embedder src="http://localhost:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
        <div class="error">
            ❌ 错误：参数应该是 'url' 而不是 'src'
        </div>
    </div>
    
    <div class="test-container">
        <h2>✅ 测试2: 正确的短代码格式</h2>
        <p><strong>正确的短代码：</strong></p>
        <div class="shortcode-display">
            [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
        
        <?php
        // 检查文件是否存在
        $pdf_path = "wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf";
        if (file_exists($pdf_path)) {
            echo '<div class="success">✅ PDF文件存在：' . $pdf_path . '</div>';
            echo '<div class="success">📊 文件大小：' . number_format(filesize($pdf_path) / 1024, 2) . ' KB</div>';
        } else {
            echo '<div class="error">❌ PDF文件不存在：' . $pdf_path . '</div>';
        }
        ?>
    </div>
    
    <div class="test-container">
        <h2>🎨 测试3: 带参数的高级配置</h2>
        <p><strong>防下载保护配置：</strong></p>
        <div class="shortcode-display">
            [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" width="max" toolbar="none"]
        </div>
        <p>这个配置会：</p>
        <ul>
            <li>隐藏工具栏（减少下载入口）</li>
            <li>设置最大宽度（响应式设计）</li>
            <li>更好的防下载保护</li>
        </ul>
    </div>
    
    <div class="test-container">
        <h2>🔧 实际修复操作</h2>
        <p><strong>请在您的WordPress文章中：</strong></p>
        <ol>
            <li>找到包含PDF短代码的文章</li>
            <li>编辑文章内容</li>
            <li>将 <code>src</code> 替换为 <code>url</code></li>
            <li>保存文章</li>
        </ol>
        
        <p><strong>替换前：</strong></p>
        <div class="shortcode-display" style="color: #dc3545;">
            [pdf-embedder src="http://localhost:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
        
        <p><strong>替换后：</strong></p>
        <div class="shortcode-display" style="color: #28a745;">
            [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
    </div>
    
    <div class="test-container">
        <h2>📋 检查清单</h2>
        <ul>
            <li>✅ PDF Embedder插件已安装</li>
            <li>
                <?php
                $plugin_active = file_exists('wp-content/plugins/pdf-embedder/pdf_embedder.php');
                if ($plugin_active) {
                    echo '✅ 插件文件存在';
                } else {
                    echo '❌ 插件文件不存在';
                }
                ?>
            </li>
            <li>
                <?php
                if (file_exists($pdf_path)) {
                    echo '✅ PDF文件存在';
                } else {
                    echo '❌ PDF文件不存在';
                }
                ?>
            </li>
            <li>🔄 需要在WordPress后台激活插件</li>
            <li>🔄 需要修改短代码参数 src → url</li>
        </ul>
    </div>
    
    <div class="test-container">
        <h2>🎯 结合防下载功能</h2>
        <p>为了最大化保护您的PDF文件，建议结合以下措施：</p>
        <ol>
            <li><strong>使用正确的短代码：</strong>
                <div class="shortcode-display">
                    [pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" width="max" toolbar="none"]
                </div>
            </li>
            <li><strong>添加JavaScript防下载代码</strong>（如您在readme.md中提到的）</li>
            <li><strong>服务器端Referer检查</strong></li>
            <li><strong>考虑升级到PDF Embedder Premium</strong>获得更强的保护功能</li>
        </ol>
    </div>
    
    <div class="success" style="margin-top: 30px; text-align: center; font-size: 18px;">
        <strong>🚀 立即行动：将文章中的 'src' 改为 'url' 即可解决问题！</strong>
    </div>
    
</body>
</html> 