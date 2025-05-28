<?php
/**
 * WordPress管理员密码重置脚本
 * 使用方法：访问 http://192.168.196.90:8080/reset-admin-password.php
 * 重置完成后请立即删除此文件！
 */

// 引入WordPress环境
require_once 'wp-load.php';

// 安全检查：只允许本地访问
$allowed_ips = ['127.0.0.1', '::1', '192.168.196.90', 'localhost'];
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($client_ip, $allowed_ips) && !preg_match('/^192\.168\./', $client_ip)) {
    die('访问被拒绝：只允许本地网络访问');
}

// 处理密码重置
$message = '';
$success = false;

if ($_POST['action'] ?? '' === 'reset_password') {
    $username = sanitize_text_field($_POST['username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    
    if (empty($username) || empty($new_password)) {
        $message = '❌ 用户名和密码不能为空';
    } else {
        $user = get_user_by('login', $username);
        if (!$user) {
            $user = get_user_by('email', $username);
        }
        
        if ($user) {
            wp_set_password($new_password, $user->ID);
            $message = '✅ 密码重置成功！用户：' . $user->user_login . ' (ID: ' . $user->ID . ')';
            $success = true;
        } else {
            $message = '❌ 找不到用户：' . $username;
        }
    }
}

// 获取所有管理员用户
$admin_users = get_users(['role' => 'administrator']);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress管理员密码重置</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 600px;
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
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .btn {
            background: #0073aa;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .btn:hover {
            background: #005a87;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .user-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .user-item {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .delete-warning {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 WordPress管理员密码重置</h1>
            <p>安全工具 - 仅限本地网络访问</p>
        </div>

        <div class="warning">
            <strong>⚠️ 安全提醒：</strong>
            <ul>
                <li>此脚本仅用于紧急密码重置</li>
                <li>重置完成后请立即删除此文件</li>
                <li>建议使用强密码</li>
            </ul>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success">
                <h3>✅ 重置成功！</h3>
                <p>现在您可以使用新密码登录WordPress后台：</p>
                <p><strong>登录地址：</strong> <a href="http://192.168.196.90:8080/wp-admin/" target="_blank">http://192.168.196.90:8080/wp-admin/</a></p>
            </div>
        <?php endif; ?>

        <div class="user-list">
            <h3>📋 当前管理员用户</h3>
            <?php if (empty($admin_users)): ?>
                <p>❌ 没有找到管理员用户</p>
            <?php else: ?>
                <?php foreach ($admin_users as $user): ?>
                    <div class="user-item">
                        <strong><?php echo esc_html($user->user_login); ?></strong>
                        (<?php echo esc_html($user->user_email); ?>)
                        - ID: <?php echo $user->ID; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <form method="post">
            <input type="hidden" name="action" value="reset_password">
            
            <div class="form-group">
                <label for="username">用户名或邮箱：</label>
                <input type="text" id="username" name="username" placeholder="输入管理员用户名或邮箱" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">新密码：</label>
                <input type="password" id="new_password" name="new_password" placeholder="输入新密码（建议使用强密码）" required>
            </div>
            
            <button type="submit" class="btn">🔄 重置密码</button>
        </form>

        <div class="delete-warning">
            <strong>🚨 重要提醒</strong><br>
            密码重置完成后，请立即删除此文件：<br>
            <code>rm /home/guci/congqing/website/reset-admin-password.php</code>
        </div>
    </div>
</body>
</html> 