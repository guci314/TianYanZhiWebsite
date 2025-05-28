<?php
/**
 * DC Media Protect 调用 PDF.js 插件原理演示
 */

// 引入WordPress环境
require_once 'wp-load.php';

// 检查PDF.js插件状态
$pdfjs_plugin_exists = file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php');
$pdfjs_plugin_active = is_plugin_active('pdfjs-viewer-shortcode/pdfjs-viewer-shortcode.php');

// 模拟DC Media Protect的检测和URL构建过程
$test_pdf = "wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf";
$processed_src = "/" . ltrim($test_pdf, '/');

if ($pdfjs_plugin_exists) {
    $nonce = wp_create_nonce('dcmp_pdf_viewer');
    $viewer_url = home_url('/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php') . 
                 '?file=' . urlencode($processed_src) . 
                 '&attachment_id=0' .
                 '&dButton=false' .
                 '&pButton=false' .
                 '&oButton=false' .
                 '&sButton=true' .
                 '&pagemode=none' .
                 '&_wpnonce=' . $nonce;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DC Media Protect 调用 PDF.js 插件原理演示</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .step {
            background: #f8f9fa;
            border-left: 5px solid #007cba;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            position: relative;
        }
        .step-number {
            position: absolute;
            left: -15px;
            top: 15px;
            background: #007cba;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            margin: 15px 0;
            border: 1px solid #4a5568;
        }
        .highlight {
            background: #ffd700;
            padding: 2px 4px;
            border-radius: 3px;
            color: #333;
            font-weight: bold;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid;
        }
        .success { background: #d4edda; color: #155724; border-color: #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border-color: #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .flow-diagram {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .flow-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            position: relative;
        }
        .flow-box::after {
            content: '→';
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #007cba;
        }
        .flow-box:last-child::after {
            display: none;
        }
        .demo-section {
            border: 2px solid #007cba;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            background: #f8f9fa;
        }
        .url-breakdown {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-family: monospace;
            word-break: break-all;
        }
        .param-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .param-table th, .param-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        .param-table th {
            background: #007cba;
            color: white;
        }
        .param-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .architecture-diagram {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
            font-family: monospace;
            line-height: 2;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #005a8b;
            color: white;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .comparison-box {
            border: 2px solid #007cba;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 DC Media Protect 调用 PDF.js 插件技术原理</h1>
        <p>深入解析插件间协作的技术实现机制</p>
    </div>

    <div class="container">
        <h2>📊 当前环境状态</h2>
        
        <div class="status <?php echo $pdfjs_plugin_exists ? 'success' : 'warning'; ?>">
            <strong>PDF.js Viewer 插件:</strong> 
            <?php if ($pdfjs_plugin_exists): ?>
                ✅ 已安装 (文件路径存在)
            <?php else: ?>
                ⚠️ 未安装 (文件路径不存在)
            <?php endif; ?>
        </div>
        
        <div class="status info">
            <strong>DC Media Protect:</strong> ✅ 已安装并激活
        </div>
    </div>

    <div class="container">
        <h2>🏗️ 技术架构流程</h2>
        
        <div class="architecture-diagram">
            用户请求 [dc_ppt] 短代码<br>
            ↓<br>
            DC Media Protect 插件处理<br>
            ↓<br>
            检测 PDF.js 插件是否存在<br>
            ↓<br>
            ┌─────────┴─────────┐<br>
            ↓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;↓<br>
            存在PDF.js插件&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;不存在PDF.js插件<br>
            ↓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;↓<br>
            构建PDF.js URL&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;使用备用方案<br>
            ↓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;↓<br>
            嵌入iframe + 水印&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;基础显示 + 水印
        </div>
    </div>

    <div class="container">
        <h2>🔧 核心技术步骤</h2>
        
        <div class="step">
            <div class="step-number">1</div>
            <h3>插件检测机制</h3>
            <p>DC Media Protect 首先检测 PDF.js 插件是否存在：</p>
            <div class="code-block">
$pdfjs_plugin_exists = file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php');
            </div>
            <p><strong>检测结果:</strong> 
                <span class="highlight"><?php echo $pdfjs_plugin_exists ? '插件存在' : '插件不存在'; ?></span>
            </p>
        </div>

        <?php if ($pdfjs_plugin_exists): ?>
        <div class="step">
            <div class="step-number">2</div>
            <h3>URL构建机制</h3>
            <p>当检测到 PDF.js 插件存在时，构建调用URL：</p>
            <div class="code-block">
$viewer_url = home_url('/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php') . 
             '?file=' . urlencode($processed_src) . 
             '&attachment_id=0' .
             '&dButton=false' .  // 禁用下载按钮
             '&pButton=false' .  // 禁用打印按钮
             '&oButton=false' .  // 禁用打开文件按钮
             '&sButton=true' .   // 保留搜索按钮
             '&pagemode=none' .
             '&_wpnonce=' . $nonce;
            </div>
            
            <h4>实际构建的URL:</h4>
            <div class="url-breakdown">
                <?php echo esc_html($viewer_url); ?>
            </div>
        </div>

        <div class="step">
            <div class="step-number">3</div>
            <h3>URL参数详解</h3>
            <table class="param-table">
                <thead>
                    <tr>
                        <th>参数名</th>
                        <th>值</th>
                        <th>作用</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>file</td>
                        <td><?php echo esc_html($processed_src); ?></td>
                        <td>指定要显示的PDF文件路径</td>
                    </tr>
                    <tr>
                        <td>dButton</td>
                        <td>false</td>
                        <td>禁用下载按钮</td>
                    </tr>
                    <tr>
                        <td>pButton</td>
                        <td>false</td>
                        <td>禁用打印按钮</td>
                    </tr>
                    <tr>
                        <td>oButton</td>
                        <td>false</td>
                        <td>禁用打开文件按钮</td>
                    </tr>
                    <tr>
                        <td>sButton</td>
                        <td>true</td>
                        <td>保留搜索按钮</td>
                    </tr>
                    <tr>
                        <td>_wpnonce</td>
                        <td><?php echo substr($nonce, 0, 10) . '...'; ?></td>
                        <td>WordPress安全令牌</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="step">
            <div class="step-number">4</div>
            <h3>iframe嵌入机制</h3>
            <p>将PDF.js查看器嵌入到安全的iframe中：</p>
            <div class="code-block">
&lt;iframe src="<?php echo esc_html($viewer_url); ?>" 
        width="100%" 
        height="100%" 
        style="border:none; display:block;" 
        title="PDF文档查看器"
        sandbox="allow-same-origin allow-scripts allow-forms"
        oncontextmenu="return false;"
        class="dcmp-pdf-iframe"&gt;&lt;/iframe&gt;
            </div>
        </div>

        <div class="step">
            <div class="step-number">5</div>
            <h3>水印叠加技术</h3>
            <p>在iframe上方叠加多层水印保护：</p>
            <div class="code-block">
&lt;div class="dcmp-watermark-overlay" style="
    position:absolute; top:0; left:0; right:0; bottom:0; 
    pointer-events:none; z-index:999999 !important;"&gt;
    
    &lt;!-- 右上角主水印 --&gt;
    &lt;div style="position:absolute; top:15px; right:15px;"&gt;🔒 数字中国&lt;/div&gt;
    
    &lt;!-- 左下角版权信息 --&gt;
    &lt;div style="position:absolute; bottom:15px; left:15px;"&gt;版权保护 - 禁止下载&lt;/div&gt;
    
    &lt;!-- 更多水印元素... --&gt;
&lt;/div&gt;
            </div>
        </div>
        <?php else: ?>
        <div class="step">
            <div class="step-number">2</div>
            <h3>备用方案处理</h3>
            <p>当 PDF.js 插件不存在时，使用备用显示方案：</p>
            <div class="code-block">
// 桌面端：使用基础iframe显示
// 移动端：显示下载链接界面
// 保持基础水印功能
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>🔄 数据流程图</h2>
        
        <div class="flow-diagram">
            <div class="flow-box">
                <h4>1. 短代码解析</h4>
                <p>[dc_ppt] 被WordPress处理</p>
            </div>
            <div class="flow-box">
                <h4>2. 插件检测</h4>
                <p>检查PDF.js插件文件</p>
            </div>
            <div class="flow-box">
                <h4>3. URL构建</h4>
                <p>生成查看器URL</p>
            </div>
            <div class="flow-box">
                <h4>4. HTML生成</h4>
                <p>创建iframe+水印</p>
            </div>
            <div class="flow-box">
                <h4>5. 浏览器渲染</h4>
                <p>显示最终效果</p>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>⚖️ 有无PDF.js插件对比</h2>
        
        <div class="comparison-grid">
            <div class="comparison-box">
                <h3>🚀 有 PDF.js 插件</h3>
                <ul>
                    <li>✅ 优秀的PDF显示效果</li>
                    <li>✅ 内嵌式PDF查看器</li>
                    <li>✅ 缩放、搜索、翻页功能</li>
                    <li>✅ 多层水印保护</li>
                    <li>✅ 防下载、防打印</li>
                    <li>✅ 跨浏览器兼容</li>
                </ul>
            </div>
            
            <div class="comparison-box">
                <h3>⚡ 无 PDF.js 插件</h3>
                <ul>
                    <li>📱 移动端优化界面</li>
                    <li>💻 桌面端基础iframe</li>
                    <li>✅ 基础水印保护</li>
                    <li>⚠️ 有限的防下载功能</li>
                    <li>✅ 微信/QQ浏览器特殊处理</li>
                    <li>❌ 无高级PDF功能</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>🛡️ 安全机制</h2>
        
        <div class="demo-section">
            <h3>多重防护措施</h3>
            <ul>
                <li><strong>访问控制:</strong> WordPress nonce验证、本地文件检测</li>
                <li><strong>界面防护:</strong> 禁用下载/打印按钮、禁用右键菜单</li>
                <li><strong>水印保护:</strong> 多层水印元素、最高z-index优先级</li>
                <li><strong>行为限制:</strong> 禁用拖拽、选择、快捷键</li>
            </ul>
        </div>
    </div>

    <?php if ($pdfjs_plugin_exists): ?>
    <div class="container">
        <h2>🧪 实际效果演示</h2>
        <p>以下是当前配置下的PDF显示效果（使用PDF.js插件）：</p>
        
        <div style="border: 2px solid #007cba; border-radius: 8px; padding: 15px; margin: 20px 0; min-height: 500px;">
            <?php echo do_shortcode('[dc_ppt src="' . $test_pdf . '"]'); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <h2>🔗 相关链接</h2>
        <a href="http://192.168.196.90:8080/" class="btn">🏠 返回首页</a>
        <a href="http://192.168.196.90:8080/dependency-test.php" class="btn">📊 依赖测试</a>
        <a href="http://192.168.196.90:8080/watermark-test.php" class="btn">🎨 水印测试</a>
        <a href="http://192.168.196.90:8080/final-test.php" class="btn">🎯 最终测试</a>
        <a href="http://192.168.196.90:8080/dc-media-protect-pdfjs-principle.md" class="btn">📖 技术文档</a>
    </div>

    <div class="container">
        <h2>📝 总结</h2>
        <div class="status info">
            <p><strong>DC Media Protect 调用 PDF.js 插件的核心原理：</strong></p>
            <ol>
                <li><strong>智能检测:</strong> 通过文件存在性检查PDF.js插件状态</li>
                <li><strong>URL构建:</strong> 动态生成带参数的PDF.js查看器URL</li>
                <li><strong>参数控制:</strong> 通过GET参数精确控制PDF.js功能</li>
                <li><strong>安全嵌入:</strong> 使用iframe沙箱机制安全显示</li>
                <li><strong>水印叠加:</strong> 在PDF查看器上方添加多层保护</li>
                <li><strong>备用方案:</strong> 无PDF.js插件时提供替代显示</li>
            </ol>
            <p>这种设计实现了<span class="highlight">松耦合、高安全、强兼容</span>的插件协作架构。</p>
        </div>
    </div>
</body>
</html> 