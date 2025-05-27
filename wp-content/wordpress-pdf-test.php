<?php
// WordPress环境PDF测试页面
// 访问地址: http://localhost:8080/wp-content/wordpress-pdf-test.php

// 引入WordPress环境
require_once dirname(__DIR__) . '/wp-load.php';

// 检查插件是否激活
$plugin_active = is_plugin_active('dc-media-protect/dc-media-protect.php');

// 测试PDF数据
$test_pdf_data = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCi9WZXJzaW9uIC8xLjQKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSA0IDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCj4+CmVuZG9iago1IDAgb2JqCjw8Ci9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCi9GMSAxMiBUZgo1MCA3MDBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNzQgMDAwMDAgbiAKMDAwMDAwMDEzMSAwMDAwMCBuIAowMDAwMDAwMjkxIDAwMDAwIG4gCjAwMDAwMDAzNzAgMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA2Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo0NjQKJUVPRg==';

// 测试短代码
$shortcode_test = '[dc_ppt src="' . $test_pdf_data . '"]';
$shortcode_result = do_shortcode($shortcode_test);

// 检查函数是否存在
$functions_check = [
    'dcmp_shortcode_ppt' => function_exists('dcmp_shortcode_ppt'),
    'dcmp_is_mobile_device' => function_exists('dcmp_is_mobile_device'),
    'dcmp_generate_mobile_pdf_viewer' => function_exists('dcmp_generate_mobile_pdf_viewer'),
    'wp_is_mobile' => function_exists('wp_is_mobile'),
    'do_shortcode' => function_exists('do_shortcode')
];

// 获取用户代理信息
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = function_exists('dcmp_is_mobile_device') ? dcmp_is_mobile_device() : wp_is_mobile();
$wp_mobile = wp_is_mobile();

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress PDF测试 - DC Media Protect</title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .status-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #007cba;
        }
        .error-box {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #dc3545;
        }
        .success-box {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #28a745;
        }
        .test-result {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #007cba;
        }
        .function-check {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
        }
        .function-item {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }
        .function-ok {
            background: #d4edda;
            color: #155724;
        }
        .function-error {
            background: #f8d7da;
            color: #721c24;
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #007cba; border-bottom: 2px solid #007cba; padding-bottom: 5px; }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 20px; }
            .function-check { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 WordPress PDF测试环境</h1>
        <p style="text-align:center; color:#666;">
            DC Media Protect 插件移动端PDF功能测试 - <?php echo date('Y-m-d H:i:s'); ?>
        </p>

        <h2>🔌 插件状态检查</h2>
        <?php if ($plugin_active): ?>
            <div class="success-box">
                <h3>✅ 插件状态: 已激活</h3>
                <p>DC Media Protect 插件已正确激活并加载。</p>
            </div>
        <?php else: ?>
            <div class="error-box">
                <h3>❌ 插件状态: 未激活</h3>
                <p>请前往 <a href="/wp-admin/plugins.php">插件管理页面</a> 激活 DC Media Protect 插件。</p>
            </div>
        <?php endif; ?>

        <h2>🔧 函数可用性检查</h2>
        <div class="function-check">
            <?php foreach ($functions_check as $func_name => $exists): ?>
                <div class="function-item <?php echo $exists ? 'function-ok' : 'function-error'; ?>">
                    <strong><?php echo esc_html($func_name); ?></strong><br>
                    <?php echo $exists ? '✅ 可用' : '❌ 不可用'; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>📱 设备检测结果</h2>
        <div class="status-box">
            <p><strong>用户代理:</strong><br>
            <small style="word-break:break-all; font-family:monospace;"><?php echo esc_html($user_agent); ?></small></p>
            
            <p><strong>WordPress wp_is_mobile():</strong> 
                <span style="color:<?php echo $wp_mobile ? '#28a745' : '#dc3545'; ?>;">
                    <?php echo $wp_mobile ? '✅ 移动设备' : '❌ 桌面设备'; ?>
                </span>
            </p>
            
            <?php if (function_exists('dcmp_is_mobile_device')): ?>
                <p><strong>DC插件 dcmp_is_mobile_device():</strong> 
                    <span style="color:<?php echo $is_mobile ? '#28a745' : '#dc3545'; ?>;">
                        <?php echo $is_mobile ? '✅ 移动设备' : '❌ 桌面设备'; ?>
                    </span>
                </p>
            <?php endif; ?>
        </div>

        <h2>📄 短代码测试</h2>
        <div class="status-box">
            <p><strong>测试短代码:</strong> <code><?php echo esc_html($shortcode_test); ?></code></p>
        </div>
        
        <div class="test-result">
            <h3>短代码输出结果:</h3>
            <?php echo $shortcode_result; ?>
        </div>

        <?php if (!$plugin_active): ?>
            <div class="error-box">
                <h3>🚨 解决方案</h3>
                <ol>
                    <li>访问 <a href="/wp-admin/plugins.php">WordPress管理后台 > 插件</a></li>
                    <li>找到 "DC Media Protect" 插件</li>
                    <li>点击 "激活" 按钮</li>
                    <li>刷新此页面重新测试</li>
                </ol>
            </div>
        <?php endif; ?>

        <div class="status-box">
            <h3>📱 手机测试地址</h3>
            <p>请在手机浏览器访问:</p>
            <p><code>http://192.168.196.90:8080/wp-content/wordpress-pdf-test.php</code></p>
        </div>
    </div>

    <script>
        // 移动端检测补充
        console.log('WordPress PDF测试页面');
        console.log('User Agent:', navigator.userAgent);
        console.log('触摸支持:', 'ontouchstart' in window);
        console.log('最大触摸点:', navigator.maxTouchPoints || 0);
        
        // 如果是移动设备但显示为桌面，添加提示
        const isMobileJS = /Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                          ('ontouchstart' in window) ||
                          (navigator.maxTouchPoints > 0);
        
        if (isMobileJS) {
            console.log('JavaScript检测: 移动设备');
        }
    </script>

    <?php wp_footer(); ?>
</body>
</html>
