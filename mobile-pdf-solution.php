<?php
/**
 * 移动端PDF显示解决方案
 * 解决PDF Embedder免费版在手机上无法显示的问题
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>移动端PDF显示解决方案</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
            line-height: 1.6;
        }
        .problem-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .solution-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .code-block {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            overflow-x: auto;
            font-size: 14px;
        }
        .mobile-demo {
            border: 2px solid #007cba;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .pdf-viewer-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            min-height: 400px;
            background-color: white;
            margin: 15px 0;
            position: relative;
        }
        .mobile-friendly-viewer {
            width: 100%;
            height: 500px;
            border: none;
            border-radius: 8px;
        }
        .download-fallback {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #005a87;
        }
        .step-container {
            background-color: #f9f9f9;
            border-left: 4px solid #007cba;
            padding: 15px;
            margin: 15px 0;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 10px;
        }
        .warning {
            color: #856404;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .code-block {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <h1>📱 移动端PDF显示解决方案</h1>
    
    <div class="problem-alert">
        <h2>🚫 问题确认</h2>
        <p><strong>PDF Embedder免费版移动端限制：</strong></p>
        <ul>
            <li>❌ 无法在移动设备上正确定位文档</li>
            <li>❌ 文档可能超出屏幕范围</li>
            <li>❌ 用户交互体验差</li>
            <li>❌ 在小屏幕上显示不完整</li>
        </ul>
        <div class="warning">
            ⚠️ 根据官方文档：<strong>"免费版可以在大多数移动浏览器上工作，但无法将文档完全定位在屏幕内"</strong>
        </div>
    </div>
    
    <div class="solution-box">
        <h2>✅ 解决方案汇总</h2>
        <p>我们提供多种解决方案来改善移动端PDF显示：</p>
        <ol>
            <li><strong>方案1：</strong> 使用移动端友好的PDF查看器</li>
            <li><strong>方案2：</strong> 实现响应式PDF嵌入</li>
            <li><strong>方案3：</strong> 添加移动端检测和下载选项</li>
            <li><strong>方案4：</strong> 升级到Premium版本</li>
        </ol>
    </div>
    
    <h2>🛠️ 方案1：移动端友好的PDF查看器</h2>
    <div class="step-container">
        <h3>使用PDF.js替代方案</h3>
        <p>创建一个自定义的移动端PDF查看器：</p>
        <div class="code-block">
&lt;!-- 移动端PDF查看器 --&gt;
&lt;div id="mobile-pdf-viewer"&gt;
    &lt;iframe 
        src="https://mozilla.github.io/pdf.js/web/viewer.html?file=<?php echo urlencode('http://localhost:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf'); ?>"
        width="100%" 
        height="600px" 
        style="border: none; border-radius: 8px;"&gt;
    &lt;/iframe&gt;
&lt;/div&gt;
        </div>
    </div>
    
    <h2>🎨 方案2：响应式PDF嵌入</h2>
    <div class="step-container">
        <h3>创建WordPress短代码替代方案</h3>
        <div class="code-block">
function mobile_friendly_pdf_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'width' => '100%',
        'height' => '600px'
    ), $atts);
    
    // 检测是否为移动设备
    $is_mobile = wp_is_mobile();
    
    if ($is_mobile) {
        // 移动端使用PDF.js查看器
        return '&lt;div class="mobile-pdf-container"&gt;
            &lt;iframe 
                src="https://mozilla.github.io/pdf.js/web/viewer.html?file=' . urlencode($atts['url']) . '"
                width="' . $atts['width'] . '" 
                height="' . $atts['height'] . '"
                style="border: none; border-radius: 8px;"&gt;
            &lt;/iframe&gt;
            &lt;p style="text-align: center; margin-top: 10px;"&gt;
                &lt;a href="' . $atts['url'] . '" class="btn" download&gt;📄 下载PDF&lt;/a&gt;
            &lt;/p&gt;
        &lt;/div&gt;';
    } else {
        // 桌面端使用PDF Embedder
        return do_shortcode('[pdf-embedder url="' . $atts['url'] . '"]');
    }
}
add_shortcode('mobile_pdf', 'mobile_friendly_pdf_shortcode');
        </div>
        <p><strong>使用方法：</strong></p>
        <div class="code-block">
[mobile_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
    </div>
    
    <h2>📱 方案3：移动端检测和优化显示</h2>
    <div class="mobile-demo">
        <h3>实时演示：移动端适配效果</h3>
        <div class="pdf-viewer-container">
            <?php
            // 简单的移动端检测
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $is_mobile = preg_match('/(android|iphone|ipad|mobile)/i', $user_agent);
            
            if ($is_mobile || isset($_GET['mobile'])) {
                echo '<div class="download-fallback">';
                echo '<h4>📱 移动端优化显示</h4>';
                echo '<p>检测到移动设备，为您提供最佳观看体验：</p>';
                echo '<a href="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" class="btn">📄 在新标签页打开PDF</a>';
                echo '<a href="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" class="btn" download>⬇️ 下载PDF文件</a>';
                echo '</div>';
            } else {
                echo '<iframe 
                    src="https://mozilla.github.io/pdf.js/web/viewer.html?file=' . urlencode('http://localhost:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf') . '"
                    class="mobile-friendly-viewer">
                </iframe>';
            }
            ?>
        </div>
        <p><a href="?mobile=1">🔄 模拟移动端显示</a> | <a href="?">🖥️ 桌面端显示</a></p>
    </div>
    
    <h2>💡 方案4：完整的移动端PDF解决方案</h2>
    <div class="step-container">
        <h3>创建自定义移动端PDF查看器</h3>
        <p>将以下代码添加到您的主题的 functions.php 文件中：</p>
        <div class="code-block">
// 移动端PDF显示解决方案
function enhanced_pdf_embedder($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'width' => 'max',
        'height' => 'auto',
        'toolbar' => 'none'
    ), $atts);
    
    // 移动端检测
    $is_mobile = wp_is_mobile();
    
    ob_start();
    ?>
    &lt;div class="responsive-pdf-container"&gt;
        &lt;?php if ($is_mobile): ?&gt;
            &lt;!-- 移动端显示 --&gt;
            &lt;div class="mobile-pdf-viewer"&gt;
                &lt;div class="pdf-thumbnail" onclick="openPDFFullscreen('&lt;?php echo esc_url($atts['url']); ?&gt;')"&gt;
                    &lt;div class="pdf-preview"&gt;
                        &lt;i class="pdf-icon"&gt;📄&lt;/i&gt;
                        &lt;h4&gt;点击查看PDF文档&lt;/h4&gt;
                        &lt;p&gt;在新窗口中打开以获得最佳阅读体验&lt;/p&gt;
                    &lt;/div&gt;
                &lt;/div&gt;
            &lt;/div&gt;
            
            &lt;script&gt;
            function openPDFFullscreen(url) {
                window.open(url, '_blank', 'fullscreen=yes,scrollbars=yes,resizable=yes');
            }
            &lt;/script&gt;
            
            &lt;style&gt;
            .mobile-pdf-viewer {
                border: 2px dashed #007cba;
                border-radius: 8px;
                padding: 40px 20px;
                text-align: center;
                background-color: #f8f9fa;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .mobile-pdf-viewer:hover {
                background-color: #e9ecef;
                border-color: #005a87;
            }
            .pdf-icon {
                font-size: 48px;
                display: block;
                margin-bottom: 15px;
            }
            &lt;/style&gt;
        &lt;?php else: ?&gt;
            &lt;!-- 桌面端显示 --&gt;
            &lt;?php echo do_shortcode('[pdf-embedder url="' . $atts['url'] . '" width="' . $atts['width'] . '" toolbar="' . $atts['toolbar'] . '"]'); ?&gt;
        &lt;?php endif; ?&gt;
    &lt;/div&gt;
    &lt;?php
    return ob_get_clean();
}
add_shortcode('smart_pdf', 'enhanced_pdf_embedder');
        </div>
    </div>
    
    <h2>🚀 立即实施建议</h2>
    <div class="solution-box">
        <h3>推荐的实施步骤：</h3>
        <ol>
            <li><strong>短期解决方案：</strong>
                <div class="code-block">
[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
                </div>
            </li>
            <li><strong>添加移动端CSS优化：</strong>
                <div class="code-block">
@media (max-width: 768px) {
    .pdfemb-viewer {
        width: 100% !important;
        height: auto !important;
        min-height: 400px;
    }
}
                </div>
            </li>
            <li><strong>考虑升级方案：</strong> PDF Embedder Premium版本提供完整的移动端支持</li>
        </ol>
    </div>
    
    <h2>💰 Premium版本优势</h2>
    <div class="step-container">
        <h3>PDF Embedder Premium移动端功能：</h3>
        <ul>
            <li>✅ 智能全屏模式</li>
            <li>✅ 移动端优化布局</li>
            <li>✅ 触摸手势支持</li>
            <li>✅ 连续滚动浏览</li>
            <li>✅ 移动端防下载保护</li>
        </ul>
        <p><strong>官网：</strong> <a href="https://wp-pdf.com/premium/" target="_blank">https://wp-pdf.com/premium/</a></p>
    </div>
    
    <div class="warning">
        <h3>⚡ 立即行动建议</h3>
        <p><strong>最快解决方案：</strong>将您当前的短代码替换为：</p>
        <div class="code-block" style="background-color: #d4edda; font-weight: bold;">
[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
        <p>这样可以为移动端用户提供点击打开PDF的选项，而桌面端用户仍然可以看到嵌入的PDF。</p>
    </div>
    
</body>
</html> 