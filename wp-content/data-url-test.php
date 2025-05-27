<?php
// data: URL PDF测试页面
// 访问地址: http://localhost:8080/wp-content/data-url-test.php

// 引入WordPress环境
require_once dirname(__DIR__) . '/wp-load.php';

// 测试PDF数据
$test_pdf_data = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCi9WZXJzaW9uIC8xLjQKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSA0IDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCj4+CmVuZG9iago1IDAgb2JqCjw8Ci9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCi9GMSAxMiBUZgo1MCA3MDBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNzQgMDAwMDAgbiAKMDAwMDAwMDEzMSAwMDAwMCBuIAowMDAwMDAwMjkxIDAwMDAwIG4gCjAwMDAwMDAzNzAgMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA2Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo0NjQKJUVPRg==';

// 测试短代码
$shortcode_test = '[dc_ppt src="' . $test_pdf_data . '"]';
$shortcode_result = do_shortcode($shortcode_test);

// 获取设备信息
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = function_exists('dcmp_is_mobile_device') ? dcmp_is_mobile_device() : wp_is_mobile();
$is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
$is_android = strpos($user_agent, 'Android') !== false;

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>data: URL PDF测试 - 修复验证</title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
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
            border-left: 5px solid #28a745;
        }
        .test-result {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #007cba;
            min-height: 450px;
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #28a745; border-bottom: 2px solid #28a745; padding-bottom: 5px; }
        .success { color: #28a745; font-weight: bold; }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ data: URL PDF修复验证</h1>
        <p style="text-align:center; color:#666;">
            验证 data: URL 是否被正确处理为本地文件 - <?php echo date('Y-m-d H:i:s'); ?>
        </p>

        <h2>📱 当前设备信息</h2>
        <div class="status-box">
            <p><strong>用户代理:</strong><br>
            <small style="word-break:break-all; font-family:monospace;"><?php echo esc_html($user_agent); ?></small></p>
            
            <p><strong>设备类型检测:</strong></p>
            <ul>
                <li>移动设备: <span class="<?php echo $is_mobile ? 'success' : ''; ?>"><?php echo $is_mobile ? '✅ 是' : '❌ 否'; ?></span></li>
                <li>iOS设备: <span class="<?php echo $is_ios ? 'success' : ''; ?>"><?php echo $is_ios ? '✅ 是' : '❌ 否'; ?></span></li>
                <li>Android设备: <span class="<?php echo $is_android ? 'success' : ''; ?>"><?php echo $is_android ? '✅ 是' : '❌ 否'; ?></span></li>
            </ul>
            
            <p><strong>预期行为:</strong></p>
            <?php if ($is_mobile): ?>
                <p class="success">✅ 移动设备 - 应显示移动端优化的PDF查看器</p>
            <?php else: ?>
                <p>🖥️ 桌面设备 - 应显示传统iframe PDF查看器</p>
            <?php endif; ?>
        </div>

        <h2>📄 修复后的PDF显示测试</h2>
        <div class="status-box">
            <p><strong>测试data: URL:</strong> <code>data:application/pdf;base64,JVBERi0x...</code></p>
            <p><strong>修复前:</strong> data: URL被识别为外部文件，显示下载链接</p>
            <p><strong>修复后:</strong> data: URL被识别为本地文件，显示移动端PDF查看器</p>
        </div>
        
        <div class="test-result">
            <h3>🎯 短代码输出结果（修复后）:</h3>
            <?php echo $shortcode_result; ?>
        </div>

        <div class="status-box">
            <h3>📝 修复说明</h3>
            <p>✅ <strong>问题:</strong> 原来的代码只检查 <code>home_url()</code> 开头的URL，忽略了 <code>data:</code> URL</p>
            <p>✅ <strong>修复:</strong> 添加了对 <code>data:</code> URL的检测，使其也被识别为本地文件</p>
            <p>✅ <strong>代码行:</strong> <code>$is_local = (strpos($src, home_url()) === 0) || (strpos($src, 'data:') === 0);</code></p>
            <p>✅ <strong>效果:</strong> 现在data: URL会触发移动端PDF查看器，而不是外部文件处理流程</p>
        </div>

        <div class="status-box">
            <h3>📱 手机测试地址</h3>
            <p>请在手机浏览器访问:</p>
            <p><code>http://192.168.196.90:8080/wp-content/data-url-test.php</code></p>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
