<?php
/**
 * WordPress URL修复脚本
 * 解决WordPress管理界面URL设置错误导致无法访问的问题
 */

// 引入WordPress环境
require_once 'wp-load.php';

echo "<h1>🔧 WordPress URL修复工具</h1>\n";

// 获取当前URL设置
$home_url = get_option('home');
$site_url = get_option('siteurl');

echo "<h2>📋 当前URL设置</h2>\n";
echo "<p><strong>Home URL:</strong> " . esc_html($home_url) . "</p>\n";
echo "<p><strong>Site URL:</strong> " . esc_html($site_url) . "</p>\n";

// 处理URL修复
if ($_POST['action'] ?? '' === 'fix_urls') {
    $new_url = 'http://192.168.196.90:8080';
    
    // 更新数据库中的URL设置
    $home_updated = update_option('home', $new_url);
    $site_updated = update_option('siteurl', $new_url);
    
    if ($home_updated || $site_updated) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
        echo "<h3>✅ URL修复成功！</h3>\n";
        echo "<p>WordPress URL已更新为: <strong>$new_url</strong></p>\n";
        echo "<p>现在您可以访问: <a href='$new_url/wp-admin/' target='_blank'>$new_url/wp-admin/</a></p>\n";
        echo "</div>\n";
        
        // 清除缓存
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        echo "<p><em>缓存已清除，请刷新页面查看最新设置。</em></p>\n";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
        echo "<h3>❌ 更新失败</h3>\n";
        echo "<p>URL设置可能已经是正确的，或者数据库连接有问题。</p>\n";
        echo "</div>\n";
    }
    
    // 重新获取更新后的URL
    $home_url = get_option('home');
    $site_url = get_option('siteurl');
    
    echo "<h2>📋 更新后的URL设置</h2>\n";
    echo "<p><strong>Home URL:</strong> " . esc_html($home_url) . "</p>\n";
    echo "<p><strong>Site URL:</strong> " . esc_html($site_url) . "</p>\n";
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress URL修复工具</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f1f1f1;
            line-height: 1.6;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn {
            background: #0073aa;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 0;
        }
        .btn:hover {
            background: #005a87;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning">
            <strong>⚠️ 问题说明：</strong>
            <p>当您在WordPress管理界面修改了网站URL设置后，如果设置不正确，会导致无法访问管理界面。这个工具可以帮您修复这个问题。</p>
        </div>

        <div class="info-box">
            <h3>🎯 修复目标</h3>
            <p>将WordPress URL设置修复为: <strong>http://192.168.196.90:8080</strong></p>
            <p>修复后您就可以正常访问WordPress管理界面了。</p>
        </div>

        <form method="post">
            <input type="hidden" name="action" value="fix_urls">
            <button type="submit" class="btn">🔧 立即修复WordPress URL</button>
        </form>

        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h3>📝 其他解决方案</h3>
            <p>如果上述方法不起作用，您还可以：</p>
            <ul>
                <li>通过数据库直接修改wp_options表中的home和siteurl值</li>
                <li>在wp-config.php中添加强制URL设置</li>
                <li>使用WP-CLI命令行工具修复</li>
            </ul>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 5px;">
            <h3>🔗 快速链接</h3>
            <ul>
                <li><a href="http://192.168.196.90:8080/" target="_blank">网站首页</a></li>
                <li><a href="http://192.168.196.90:8080/wp-admin/" target="_blank">WordPress管理后台</a></li>
                <li><a href="http://192.168.196.90:8080/reset-admin-password.php" target="_blank">密码重置工具</a></li>
            </ul>
        </div>
    </div>
</body>
</html> 