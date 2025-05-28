<?php
// 微信浏览器PDF测试页面
// 访问地址: http://localhost:8080/wp-content/wechat-pdf-test.php

// 引入WordPress环境
require_once dirname(__DIR__) . '/wp-load.php';

// 检查插件是否激活
$plugin_active = is_plugin_active('dc-media-protect/dc-media-protect.php');

// 测试PDF路径
$test_pdf_url = '/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf';

// 模拟不同的User Agent进行测试
$test_user_agents = [
    'normal' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'wechat' => 'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/88.0.4324.181 Mobile Safari/537.36 MicroMessenger/8.0.1.1841(0x28000151) Process/tools WeChat/arm64 Setlang/zh_CN',
    'qq' => 'Mozilla/5.0 (Linux; Android 10; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/88.0.4324.181 Mobile Safari/537.36 QQBrowser/12.0.0.3040',
    'firefox_mobile' => 'Mozilla/5.0 (Mobile; rv:68.0) Gecko/68.0 Firefox/68.0'
];

// 获取当前测试的User Agent
$test_mode = $_GET['test'] ?? 'normal';
if (isset($test_user_agents[$test_mode])) {
    $_SERVER['HTTP_USER_AGENT'] = $test_user_agents[$test_mode];
}

// 检查函数是否存在
$functions_check = [
    'dcmp_shortcode_ppt' => function_exists('dcmp_shortcode_ppt'),
    'dcmp_is_mobile_device' => function_exists('dcmp_is_mobile_device'),
    'dcmp_is_wechat_browser' => function_exists('dcmp_is_wechat_browser'),
    'dcmp_is_qq_browser' => function_exists('dcmp_is_qq_browser'),
];

// 获取检测结果
$is_mobile = function_exists('dcmp_is_mobile_device') ? dcmp_is_mobile_device() : wp_is_mobile();
$is_wechat = function_exists('dcmp_is_wechat_browser') ? dcmp_is_wechat_browser() : false;
$is_qq = function_exists('dcmp_is_qq_browser') ? dcmp_is_qq_browser() : false;

// 测试短代码
$shortcode_test = '[dc_ppt src="' . $test_pdf_url . '"]';
$shortcode_result = do_shortcode($shortcode_test);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信浏览器PDF测试</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0; 
            padding: 20px; 
            background: #f5f5f5; 
            line-height: 1.6;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 12px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .test-info {
            background: #e3f2fd; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            border-left: 4px solid #2196f3;
        }
        .detection-results {
            background: #f3e5f5; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px;
            border-left: 4px solid #9c27b0;
        }
        .test-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .test-btn {
            padding: 10px 15px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }
        .test-btn:hover {
            background: #005a87;
        }
        .test-btn.active {
            background: #28a745;
        }
        .shortcode-result {
            margin: 30px 0;
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            background: #fafafa;
        }
        .code-block {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            word-break: break-all;
        }
        .status-good { color: #28a745; font-weight: bold; }
        .status-bad { color: #dc3545; font-weight: bold; }
        .status-warning { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 微信浏览器PDF显示测试</h1>
            <p>测试不同浏览器环境下的PDF显示效果</p>
        </div>

        <div class="test-info">
            <h3>📱 当前测试环境</h3>
            <p><strong>测试模式:</strong> <?php echo ucfirst($test_mode); ?></p>
            <p><strong>User Agent:</strong></p>
            <div class="code-block"><?php echo esc_html($_SERVER['HTTP_USER_AGENT']); ?></div>
        </div>

        <div class="test-buttons">
            <a href="?test=normal" class="test-btn <?php echo $test_mode === 'normal' ? 'active' : ''; ?>">🖥️ 正常浏览器</a>
            <a href="?test=wechat" class="test-btn <?php echo $test_mode === 'wechat' ? 'active' : ''; ?>">💬 微信浏览器</a>
            <a href="?test=qq" class="test-btn <?php echo $test_mode === 'qq' ? 'active' : ''; ?>">🐧 QQ浏览器</a>
            <a href="?test=firefox_mobile" class="test-btn <?php echo $test_mode === 'firefox_mobile' ? 'active' : ''; ?>">🦊 Firefox移动版</a>
        </div>

        <div class="detection-results">
            <h3>🔍 检测结果</h3>
            <p><strong>插件状态:</strong> <span class="<?php echo $plugin_active ? 'status-good' : 'status-bad'; ?>"><?php echo $plugin_active ? '✅ 已激活' : '❌ 未激活'; ?></span></p>
            <p><strong>移动设备:</strong> <span class="<?php echo $is_mobile ? 'status-good' : 'status-warning'; ?>"><?php echo $is_mobile ? '✅ 是' : '❌ 否'; ?></span></p>
            <p><strong>微信浏览器:</strong> <span class="<?php echo $is_wechat ? 'status-good' : 'status-warning'; ?>"><?php echo $is_wechat ? '✅ 是' : '❌ 否'; ?></span></p>
            <p><strong>QQ浏览器:</strong> <span class="<?php echo $is_qq ? 'status-good' : 'status-warning'; ?>"><?php echo $is_qq ? '✅ 是' : '❌ 否'; ?></span></p>
            
            <h4>📋 函数检查</h4>
            <?php foreach ($functions_check as $func => $exists): ?>
                <p><strong><?php echo $func; ?>:</strong> <span class="<?php echo $exists ? 'status-good' : 'status-bad'; ?>"><?php echo $exists ? '✅ 存在' : '❌ 不存在'; ?></span></p>
            <?php endforeach; ?>
        </div>

        <div class="shortcode-result">
            <h3>📄 PDF短代码测试结果</h3>
            <p><strong>测试短代码:</strong></p>
            <div class="code-block"><?php echo esc_html($shortcode_test); ?></div>
            
            <h4>显示效果:</h4>
            <div style="border: 1px solid #ddd; padding: 10px; background: white; border-radius: 4px;">
                <?php echo $shortcode_result; ?>
            </div>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
            <h4>💡 测试说明</h4>
            <ul>
                <li><strong>微信浏览器:</strong> 应显示专门的微信浏览器界面，提供"在浏览器中打开"和"下载到手机"选项</li>
                <li><strong>QQ浏览器:</strong> 应显示QQ浏览器优化界面</li>
                <li><strong>Firefox移动版:</strong> 应显示标准的移动端PDF查看器</li>
                <li><strong>正常浏览器:</strong> 应显示iframe嵌入的PDF</li>
            </ul>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #d4edda; border-radius: 8px; border-left: 4px solid #28a745;">
            <h4>🎯 快速测试链接</h4>
            <p>点击以下链接快速测试不同浏览器环境：</p>
            <ul>
                <li><a href="http://192.168.196.90:8080/wp-content/wechat-pdf-test.php?test=wechat" target="_blank">🔗 微信浏览器测试</a></li>
                <li><a href="http://192.168.196.90:8080/wp-content/wechat-pdf-test.php?test=qq" target="_blank">🔗 QQ浏览器测试</a></li>
                <li><a href="http://192.168.196.90:8080/wp-content/wechat-pdf-test.php?test=firefox_mobile" target="_blank">🔗 Firefox移动版测试</a></li>
                <li><a href="http://192.168.196.90:8080/wp-content/wechat-pdf-test.php?test=normal" target="_blank">🔗 正常浏览器测试</a></li>
            </ul>
        </div>
    </div>
</body>
</html> 