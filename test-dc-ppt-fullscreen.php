<?php
// 设置WordPress环境
require_once 'wp-config.php';
require_once ABSPATH . 'wp-settings.php';

// 检查是否存在测试PDF文件
$test_pdf = '/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf';
$test_pdf_full_path = ABSPATH . ltrim($test_pdf, '/');

if (!file_exists($test_pdf_full_path)) {
    // 如果文件不存在，创建一个简单的PDF数据URL用于测试
    $test_pdf = 'data:application/pdf;base64,JVBERi0xLjMKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL0xlbmd0aCA5NTIKL0ZpbHRlciAvRmxhdGVEZWNvZGUKPj4Kc3RyZWFtCnic7ZDBCgIxDEX/Jee1kMw0SY9FxIM3vSkeBJO8MoVAhFgGrH3/ffcpLFhczP4wEXefCg8BQA6zCQlAhEUQoHtKQcDgYQGwgx1tE2JfYtXKZ1mPnlWgBBtmQdKb2IyQlAK6IZJSOhARKRBxE+C7jgj5hDASAGOd0DCEQGHFqGxlIqLi3lp3Xb/eH8KLKaM8K/ClokSjDw2YqPzAO3KQGQL+CgAA///+DcKLClgFJAA=';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>测试 dc_ppt 短代码全屏按钮</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        .instructions {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007cba;
        }
        .info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        h1, h2 {
            color: #333;
        }
        code {
            background: #f4f4f4;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
        .device-info {
            font-size: 14px;
            color: #666;
            margin: 10px 0;
        }
        .fullscreen-guide {
            background: #d4edda;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 测试 dc_ppt 短代码全屏按钮</h1>
        
        <div class="device-info">
            <strong>设备信息：</strong><br>
            📱 用户代理：<?php echo esc_html($_SERVER['HTTP_USER_AGENT'] ?? '未知'); ?><br>
            🖥️ 设备类型：<?php echo wp_is_mobile() ? '移动设备' : '桌面设备'; ?><br>
            📊 当前时间：<?php echo date('Y-m-d H:i:s'); ?>
        </div>

        <div class="instructions">
            <h3>🎯 测试目标</h3>
            <p>验证 <code>[dc_ppt]</code> 短代码中的PDF.js查看器是否显示全屏按钮：</p>
            <ol>
                <li><strong>查找工具栏：</strong>点击PDF查看器右上角的"工具"按钮（三个点图标）</li>
                <li><strong>查找全屏按钮：</strong>在弹出的工具栏中寻找"Presentation Mode"或"演示模式"按钮</li>
                <li><strong>测试全屏功能：</strong>点击该按钮进入全屏模式</li>
                <li><strong>退出全屏：</strong>按ESC键或使用浏览器退出全屏功能</li>
            </ol>
        </div>

        <div class="fullscreen-guide">
            <h3>🔍 如何找到全屏按钮</h3>
            <p><strong>步骤：</strong></p>
            <ol>
                <li>在PDF查看器中，点击右上角的 <strong>"工具"</strong> 按钮（⋮ 或 ≡ 图标）</li>
                <li>在展开的二级工具栏中，寻找 <strong>"Presentation Mode"</strong> 或 <strong>"演示模式"</strong> 按钮</li>
                <li>该按钮通常位于下载、打印按钮的下方区域</li>
            </ol>
            <p><strong>💡 提示：</strong>如果看不到全屏按钮，可能是以下原因：</p>
            <ul>
                <li>浏览器不支持全屏API</li>
                <li>PDF.js版本问题</li>
                <li>工具栏被隐藏或CSS样式问题</li>
            </ul>
        </div>

        <div class="test-section">
            <h2>📋 测试用例 1：dc_ppt 短代码</h2>
            <p><strong>短代码：</strong> <code>[dc_ppt src="<?php echo esc_html($test_pdf); ?>"]</code></p>
            
            <div style="margin: 20px 0;">
                <?php echo do_shortcode('[dc_ppt src="' . $test_pdf . '"]'); ?>
            </div>
        </div>

        <div class="test-section">
            <h2>📋 测试用例 2：带尺寸的 dc_ppt</h2>
            <p><strong>短代码：</strong> <code>[dc_ppt src="<?php echo esc_html($test_pdf); ?>" width="100%" height="500"]</code></p>
            
            <div style="margin: 20px 0;">
                <?php echo do_shortcode('[dc_ppt src="' . $test_pdf . '" width="100%" height="500"]'); ?>
            </div>
        </div>

        <div class="info">
            <h3>📝 测试结果记录</h3>
            <p>请验证以下项目：</p>
            <ul>
                <li>☐ PDF查看器正常加载</li>
                <li>☐ 可以看到工具栏（右上角的"工具"按钮）</li>
                <li>☐ 点击"工具"按钮后展开二级工具栏</li>
                <li>☐ 在二级工具栏中找到"Presentation Mode"按钮</li>
                <li>☐ 点击"Presentation Mode"可以进入全屏</li>
                <li>☐ 按ESC键可以退出全屏</li>
                <li>☐ 在演示模式下手势缩放是否有效（如果适用）</li>
            </ul>
        </div>

        <div class="instructions">
            <h3>🔧 故障排除</h3>
            <p><strong>如果看不到全屏按钮：</strong></p>
            <ol>
                <li><strong>检查浏览器支持：</strong>确保浏览器支持全屏API（现代浏览器通常支持）</li>
                <li><strong>检查工具栏：</strong>确保工具栏没有被CSS隐藏</li>
                <li><strong>检查控制台：</strong>按F12打开开发者工具，查看是否有JavaScript错误</li>
                <li><strong>直接访问：</strong>尝试直接访问PDF.js查看器页面进行对比</li>
            </ol>
        </div>

        <?php if (current_user_can('manage_options')): ?>
        <div class="test-section">
            <h2>🔧 开发者调试信息</h2>
            <p><strong>PDF.js插件检查：</strong></p>
            <ul>
                <li>pdfjs-viewer-shortcode插件存在：<?php echo file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php') ? '✅ 是' : '❌ 否'; ?></li>
                <li>viewer.php路径：<code><?php echo WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php'; ?></code></li>
                <li>测试PDF路径：<code><?php echo $test_pdf; ?></code></li>
                <li>WordPress根目录：<code><?php echo ABSPATH; ?></code></li>
            </ul>
            
            <p><strong>构建的查看器URL示例：</strong></p>
            <code style="word-break: break-all; display: block; padding: 10px; background: #f4f4f4;">
                <?php
                $sample_url = home_url('/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php') . 
                             '?file=' . urlencode($test_pdf) . 
                             '&attachment_id=0' .
                             '&dButton=false' .
                             '&pButton=false' .
                             '&oButton=false' .
                             '&sButton=true' .
                             '&pagemode=none';
                echo esc_html($sample_url);
                ?>
            </code>
        </div>
        <?php endif; ?>

        <div class="instructions">
            <h3>🎯 结论</h3>
            <p>
                根据代码分析，PDF.js查看器中的 <strong>"Presentation Mode"</strong> 按钮应该是默认显示的，
                不受URL参数控制。如果在 <code>dc_ppt</code> 短代码生成的查看器中看不到该按钮，
                可能是由于CSS样式、JavaScript错误或浏览器兼容性问题。
            </p>
            <p>
                <strong>预期结果：</strong>在PDF.js查看器的工具栏中应该能找到并使用全屏功能。
            </p>
        </div>
    </div>

    <script>
        // 添加一些调试信息
        console.log('测试页面加载完成');
        console.log('用户代理:', navigator.userAgent);
        console.log('全屏API支持:', {
            'document.fullscreenEnabled': document.fullscreenEnabled,
            'document.webkitFullscreenEnabled': document.webkitFullscreenEnabled,
            'document.mozFullScreenEnabled': document.mozFullScreenEnabled,
            'document.msFullscreenEnabled': document.msFullscreenEnabled
        });
        
        // 监听全屏状态变化
        document.addEventListener('fullscreenchange', function() {
            console.log('全屏状态变化:', document.fullscreenElement ? '进入全屏' : '退出全屏');
        });
        
        document.addEventListener('webkitfullscreenchange', function() {
            console.log('Webkit全屏状态变化:', document.webkitFullscreenElement ? '进入全屏' : '退出全屏');
        });
    </script>
</body>
</html> 