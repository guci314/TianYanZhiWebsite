<?php
/**
 * 简单的短代码测试
 * 这个文件需要在WordPress根目录中运行
 */

// 加载WordPress
require_once('./wp-config.php');
require_once('./wp-load.php');

// 检查WordPress是否正确加载
if (!function_exists('do_shortcode')) {
    die('WordPress未正确加载');
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>短代码测试</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .test-box {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .result-box {
            border: 2px solid #28a745;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: white;
            min-height: 100px;
        }
        .error { border-color: #dc3545; background-color: #f8d7da; }
        .success { border-color: #28a745; background-color: #d4edda; }
    </style>
</head>
<body>
    <h1>🧪 WordPress短代码测试</h1>
    
    <div class="test-box">
        <h2>WordPress环境信息</h2>
        <p><strong>WordPress版本:</strong> <?php echo get_bloginfo('version'); ?></p>
        <p><strong>当前主题:</strong> <?php echo wp_get_theme()->get('Name'); ?></p>
        <p><strong>是否移动端:</strong> <?php echo wp_is_mobile() ? '是' : '否'; ?></p>
    </div>
    
    <div class="test-box">
        <h2>短代码注册状态</h2>
        <?php
        global $shortcode_tags;
        $test_shortcodes = ['smart_pdf', 'mobile_pdf', 'pdf-embedder'];
        
        foreach ($test_shortcodes as $shortcode) {
            $exists = shortcode_exists($shortcode);
            echo "<p><code>[$shortcode]</code> - ";
            echo $exists ? '<span style="color: green;">✅ 已注册</span>' : '<span style="color: red;">❌ 未注册</span>';
            echo "</p>";
        }
        ?>
    </div>
    
    <div class="test-box">
        <h2>智能PDF短代码测试</h2>
        <p><strong>短代码:</strong> <code>[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]</code></p>
        <p><strong>执行结果:</strong></p>
        <div class="result-box">
            <?php 
            if (shortcode_exists('smart_pdf')) {
                echo do_shortcode('[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]');
            } else {
                echo '<span style="color: red;">❌ 短代码未注册</span>';
            }
            ?>
        </div>
    </div>
    
    <div class="test-box">
        <h2>移动端PDF短代码测试</h2>
        <p><strong>短代码:</strong> <code>[mobile_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]</code></p>
        <p><strong>执行结果:</strong></p>
        <div class="result-box">
            <?php 
            if (shortcode_exists('mobile_pdf')) {
                echo do_shortcode('[mobile_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]');
            } else {
                echo '<span style="color: red;">❌ 短代码未注册</span>';
            }
            ?>
        </div>
    </div>
    
    <div class="test-box">
        <h2>函数检查</h2>
        <?php
        $functions = [
            'smart_pdf_embedder_shortcode',
            'mobile_pdf_viewer_shortcode',
            'wp_is_mobile',
            'do_shortcode'
        ];
        
        foreach ($functions as $func) {
            $exists = function_exists($func);
            echo "<p><code>$func()</code> - ";
            echo $exists ? '<span style="color: green;">✅ 存在</span>' : '<span style="color: red;">❌ 不存在</span>';
            echo "</p>";
        }
        ?>
    </div>
    
    <div class="test-box <?php echo shortcode_exists('smart_pdf') ? 'success' : 'error'; ?>">
        <h2>诊断结果</h2>
        <?php if (shortcode_exists('smart_pdf')): ?>
            <p>✅ <strong>短代码已正确注册和工作！</strong></p>
            <p>如果在您的文章中仍然显示短代码文本，请检查：</p>
            <ul>
                <li>确保在WordPress编辑器的"文本"模式下输入短代码</li>
                <li>清除网站和浏览器缓存</li>
                <li>确保短代码没有被放在代码块中</li>
            </ul>
        <?php else: ?>
            <p>❌ <strong>短代码未正确注册</strong></p>
            <p>可能的问题：</p>
            <ul>
                <li>functions.php中的代码未生效</li>
                <li>主题未正确加载</li>
                <li>存在PHP错误</li>
            </ul>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="javascript:location.reload()" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🔄 重新测试</a>
    </div>
    
</body>
</html> 