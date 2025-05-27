<?php
/**
 * 短代码调试工具
 * 检查WordPress短代码注册状态
 */

// 模拟WordPress环境
define('WP_DEBUG', true);

// 检查WordPress是否已加载
if (!function_exists('add_shortcode')) {
    echo "<h1>❌ WordPress环境未加载</h1>";
    echo "<p>这个文件需要在WordPress环境中运行。</p>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>短代码调试工具</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        .status-good { color: #28a745; background-color: #d4edda; }
        .status-bad { color: #dc3545; background-color: #f8d7da; }
        .status-warning { color: #856404; background-color: #fff3cd; }
        .status-box {
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid;
        }
        .code-test {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
            font-family: monospace;
        }
        h1 { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>🔍 WordPress短代码调试报告</h1>
    
    <h2>1. WordPress环境检查</h2>
    <?php
    $wp_version = get_bloginfo('version');
    $theme_info = wp_get_theme();
    $is_wp_loaded = function_exists('wp_get_theme');
    ?>
    
    <div class="status-box <?php echo $is_wp_loaded ? 'status-good' : 'status-bad'; ?>">
        <strong>WordPress状态:</strong> <?php echo $is_wp_loaded ? '✅ 已加载' : '❌ 未加载'; ?><br>
        <strong>WordPress版本:</strong> <?php echo $wp_version; ?><br>
        <strong>当前主题:</strong> <?php echo $theme_info->get('Name') . ' v' . $theme_info->get('Version'); ?>
    </div>
    
    <h2>2. 短代码注册检查</h2>
    <?php
    global $shortcode_tags;
    
    $our_shortcodes = ['smart_pdf', 'mobile_pdf', 'pdf-embedder'];
    $registered_shortcodes = array_keys($shortcode_tags);
    ?>
    
    <div class="status-box">
        <strong>已注册的短代码总数:</strong> <?php echo count($registered_shortcodes); ?><br>
        <strong>我们的短代码状态:</strong><br>
        <ul>
            <?php foreach ($our_shortcodes as $shortcode): ?>
                <li>
                    <code>[<?php echo $shortcode; ?>]</code> - 
                    <?php if (shortcode_exists($shortcode)): ?>
                        <span style="color: #28a745;">✅ 已注册</span>
                    <?php else: ?>
                        <span style="color: #dc3545;">❌ 未注册</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <h2>3. 函数存在性检查</h2>
    <?php
    $functions_to_check = [
        'smart_pdf_embedder_shortcode',
        'mobile_pdf_viewer_shortcode', 
        'wp_is_mobile',
        'do_shortcode'
    ];
    ?>
    
    <div class="status-box">
        <strong>关键函数检查:</strong><br>
        <ul>
            <?php foreach ($functions_to_check as $func): ?>
                <li>
                    <code><?php echo $func; ?></code> - 
                    <?php if (function_exists($func)): ?>
                        <span style="color: #28a745;">✅ 存在</span>
                    <?php else: ?>
                        <span style="color: #dc3545;">❌ 不存在</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    
    <h2>4. PDF Embedder插件检查</h2>
    <?php
    $pdf_embedder_active = is_plugin_active('pdf-embedder/pdf_embedder.php');
    $pdf_embedder_exists = file_exists(WP_PLUGIN_DIR . '/pdf-embedder/pdf_embedder.php');
    ?>
    
    <div class="status-box <?php echo $pdf_embedder_active ? 'status-good' : 'status-warning'; ?>">
        <strong>PDF Embedder插件:</strong><br>
        <ul>
            <li>文件存在: <?php echo $pdf_embedder_exists ? '✅ 是' : '❌ 否'; ?></li>
            <li>插件激活: <?php echo $pdf_embedder_active ? '✅ 是' : '❌ 否'; ?></li>
        </ul>
    </div>
    
    <h2>5. 短代码实际测试</h2>
    
    <h3>测试1: smart_pdf短代码</h3>
    <div class="code-test">
        <strong>短代码:</strong> [smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]<br>
        <strong>执行结果:</strong><br>
        <div style="border: 1px solid #ddd; padding: 10px; margin: 10px 0; background: white;">
            <?php 
            if (shortcode_exists('smart_pdf')) {
                echo do_shortcode('[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]');
            } else {
                echo '<span style="color: red;">短代码未注册，无法执行</span>';
            }
            ?>
        </div>
    </div>
    
    <h3>测试2: PDF文件可访问性</h3>
    <?php
    $pdf_path = ABSPATH . 'wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf';
    $pdf_exists = file_exists($pdf_path);
    $pdf_size = $pdf_exists ? filesize($pdf_path) : 0;
    ?>
    
    <div class="status-box <?php echo $pdf_exists ? 'status-good' : 'status-bad'; ?>">
        <strong>PDF文件检查:</strong><br>
        <ul>
            <li>文件路径: <code><?php echo $pdf_path; ?></code></li>
            <li>文件存在: <?php echo $pdf_exists ? '✅ 是' : '❌ 否'; ?></li>
            <?php if ($pdf_exists): ?>
                <li>文件大小: <?php echo number_format($pdf_size / 1024, 2); ?> KB</li>
            <?php endif; ?>
        </ul>
    </div>
    
    <h2>6. 移动端检测测试</h2>
    <?php
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_mobile_manual = preg_match('/(android|iphone|ipad|mobile)/i', $user_agent);
    $is_mobile_wp = function_exists('wp_is_mobile') ? wp_is_mobile() : false;
    ?>
    
    <div class="status-box status-good">
        <strong>设备检测:</strong><br>
        <ul>
            <li>用户代理: <code><?php echo htmlspecialchars(substr($user_agent, 0, 100)); ?>...</code></li>
            <li>手动检测移动端: <?php echo $is_mobile_manual ? '✅ 是' : '❌ 否'; ?></li>
            <li>WordPress wp_is_mobile(): <?php echo $is_mobile_wp ? '✅ 是' : '❌ 否'; ?></li>
        </ul>
    </div>
    
    <h2>7. 问题诊断和解决方案</h2>
    
    <?php if (!shortcode_exists('smart_pdf')): ?>
        <div class="status-box status-bad">
            <h3>❌ 问题: 短代码未注册</h3>
            <p><strong>可能原因:</strong></p>
            <ul>
                <li>functions.php文件中的代码未生效</li>
                <li>PHP语法错误导致代码中断</li>
                <li>主题未正确加载</li>
            </ul>
            <p><strong>解决方案:</strong></p>
            <ol>
                <li>检查WordPress错误日志</li>
                <li>确认functions.php文件语法正确</li>
                <li>清除所有缓存</li>
                <li>重新激活主题</li>
            </ol>
        </div>
    <?php else: ?>
        <div class="status-box status-good">
            <h3>✅ 短代码已正确注册</h3>
            <p>如果页面上仍显示短代码而不是内容，可能的原因:</p>
            <ul>
                <li>页面缓存问题</li>
                <li>短代码在代码块中而非内容区域</li>
                <li>编辑器将短代码当作文本处理</li>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (!$pdf_embedder_active): ?>
        <div class="status-box status-warning">
            <h3>⚠️ 警告: PDF Embedder插件未激活</h3>
            <p>smart_pdf短代码在桌面端依赖PDF Embedder插件。请激活该插件以获得完整功能。</p>
        </div>
    <?php endif; ?>
    
    <h2>8. 立即修复操作</h2>
    
    <div class="status-box status-good">
        <h3>🔧 推荐操作步骤:</h3>
        <ol>
            <li><strong>清除缓存:</strong> 如果使用了缓存插件，清除所有缓存</li>
            <li><strong>检查编辑器:</strong> 确保短代码在WordPress编辑器的文本模式下输入</li>
            <li><strong>激活插件:</strong> 确保PDF Embedder插件已激活</li>
            <li><strong>测试简单短代码:</strong> 先试试 <code>[smart_pdf url="测试.pdf"]</code></li>
            <li><strong>查看错误日志:</strong> 检查WordPress调试日志</li>
        </ol>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="javascript:location.reload()" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🔄 重新检测</a>
    </div>
    
</body>
</html> 