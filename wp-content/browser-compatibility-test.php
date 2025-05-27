<?php
// 浏览器兼容性PDF测试页面
// 访问地址: http://localhost:8080/wp-content/browser-compatibility-test.php

// 引入WordPress环境
require_once dirname(__DIR__) . '/wp-load.php';

// 测试PDF数据
$test_pdf_data = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCi9WZXJzaW9uIC8xLjQKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSA0IDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCj4+CmVuZG9iago1IDAgb2JqCjw8Ci9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCi9GMSAxMiBUZgo1MCA3MDBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNzQgMDAwMDAgbiAKMDAwMDAwMDEzMSAwMDAwMCBuIAowMDAwMDAwMjkxIDAwMDAwIG4gCjAwMDAwMDAzNzAgMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA2Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo0NjQKJUVPRg==';

// 获取浏览器信息
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// 调用设备检测函数（如果存在）
$device_info = [];
if (function_exists('dcmp_detect_device_type')) {
    $device_info = dcmp_detect_device_type();
}

// 测试短代码
$shortcode_test = '[dc_ppt src="' . $test_pdf_data . '"]';
$shortcode_result = do_shortcode($shortcode_test);

// 浏览器检测
$browser_info = [
    'is_mobile' => function_exists('wp_is_mobile') ? wp_is_mobile() : false,
    'is_firefox' => strpos($user_agent, 'Firefox') !== false,
    'is_chrome' => strpos($user_agent, 'Chrome') !== false,
    'is_safari' => strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false,
    'is_mi_browser' => strpos($user_agent, 'MiuiBrowser') !== false || strpos($user_agent, 'XiaoMi') !== false || strpos($user_agent, 'MIUI') !== false,
    'is_uc_browser' => strpos($user_agent, 'UCBrowser') !== false,
    'is_qq_browser' => strpos($user_agent, 'QQBrowser') !== false,
    'is_huawei_browser' => strpos($user_agent, 'HuaweiBrowser') !== false,
    'is_sogou_browser' => strpos($user_agent, 'SogouMobileBrowser') !== false
];

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>浏览器兼容性PDF测试 - 针对小米浏览器优化</title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #1976d2 0%, #42a5f5 100%);
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
            border-left: 5px solid #1976d2;
        }
        .browser-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }
        .browser-item {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }
        .browser-detected {
            background: #e8f5e8;
            color: #2e7d32;
            border: 2px solid #4caf50;
            font-weight: bold;
        }
        .browser-not {
            background: #fafafa;
            color: #666;
            border: 1px solid #ddd;
        }
        .test-result {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #1976d2;
            min-height: 500px;
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #1976d2; border-bottom: 2px solid #1976d2; padding-bottom: 5px; }
        .success { color: #2e7d32; font-weight: bold; }
        .warning { color: #f57c00; font-weight: bold; }
        .error { color: #d32f2f; font-weight: bold; }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 20px; }
            .browser-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 浏览器兼容性PDF测试</h1>
        <p style="text-align:center; color:#666;">
            专门优化小米浏览器等国产浏览器的PDF显示兼容性 - <?php echo date('Y-m-d H:i:s'); ?>
        </p>

        <h2>📱 浏览器检测结果</h2>
        <div class="status-box">
            <p><strong>用户代理:</strong><br>
            <small style="word-break:break-all; font-family:monospace; background:#f5f5f5; padding:5px; border-radius:3px;"><?php echo esc_html($user_agent); ?></small></p>
            
            <div class="browser-grid">
                <div class="browser-item <?php echo $browser_info['is_firefox'] ? 'browser-detected' : 'browser-not'; ?>">
                    🦊 Firefox<br><?php echo $browser_info['is_firefox'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="browser-item <?php echo $browser_info['is_chrome'] ? 'browser-detected' : 'browser-not'; ?>">
                    🔍 Chrome<br><?php echo $browser_info['is_chrome'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="browser-item <?php echo $browser_info['is_safari'] ? 'browser-detected' : 'browser-not'; ?>">
                    🧭 Safari<br><?php echo $browser_info['is_safari'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="browser-item <?php echo $browser_info['is_mi_browser'] ? 'browser-detected' : 'browser-not'; ?>">
                    📱 小米浏览器<br><?php echo $browser_info['is_mi_browser'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="browser-item <?php echo $browser_info['is_uc_browser'] ? 'browser-detected' : 'browser-not'; ?>">
                    🔶 UC浏览器<br><?php echo $browser_info['is_uc_browser'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="browser-item <?php echo $browser_info['is_qq_browser'] ? 'browser-detected' : 'browser-not'; ?>">
                    🐧 QQ浏览器<br><?php echo $browser_info['is_qq_browser'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
            </div>
        </div>

        <h2>🎯 PDF显示策略</h2>
        <div class="status-box">
            <?php if ($browser_info['is_mi_browser'] || $browser_info['is_uc_browser'] || $browser_info['is_qq_browser'] || $browser_info['is_huawei_browser'] || $browser_info['is_sogou_browser']): ?>
                <p class="warning">⚠️ <strong>国产浏览器</strong> - 使用兼容性增强方案，包含iframe尝试 + 备用选项</p>
                <?php if ($browser_info['is_mi_browser']): ?>
                    <p class="warning">🔍 <strong>检测到小米浏览器</strong> (User Agent中也包含Chrome，但优先识别为小米浏览器)</p>
                <?php endif; ?>
            <?php elseif ($browser_info['is_firefox']): ?>
                <p class="success">✅ <strong>Firefox浏览器</strong> - 使用优化的iframe显示，支持内置PDF查看器</p>
            <?php elseif ($browser_info['is_safari']): ?>
                <p class="success">✅ <strong>Safari浏览器</strong> - 使用原生PDF查看器</p>
            <?php elseif ($browser_info['is_chrome']): ?>
                <p class="success">✅ <strong>Chrome浏览器</strong> - 使用PDF.js渲染</p>
            <?php else: ?>
                <p class="error">❓ <strong>未知浏览器</strong> - 使用通用兼容方案</p>
            <?php endif; ?>
            
            <h4>预期效果：</h4>
            <ul>
                <?php if ($browser_info['is_firefox']): ?>
                    <li>🦊 Firefox内置PDF支持 - 直接显示PDF内容</li>
                <?php elseif ($browser_info['is_mi_browser']): ?>
                    <li>📱 小米浏览器专用优化界面</li>
                    <li>⏳ 先尝试iframe加载，3秒超时检测</li>
                    <li>🔄 加载失败后提供替代方案</li>
                    <li>🔗 "在新标签页打开"按钮</li>
                    <li>📋 "尝试替代方案"提示</li>
                <?php else: ?>
                    <li>📄 标准移动端PDF查看器</li>
                    <li>🔗 新窗口打开选项</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <h2>📄 PDF显示测试结果</h2>
        <div class="test-result">
            <h3>🎯 当前浏览器的PDF显示效果:</h3>
            <?php echo $shortcode_result; ?>
        </div>

        <?php if ($browser_info['is_mi_browser']): ?>
        <div class="status-box">
            <h3>📱 小米浏览器用户说明</h3>
            <p><strong>如果上方PDF无法显示，请尝试：</strong></p>
            <ol>
                <li>点击 <span class="warning">"在新标签页打开"</span> 按钮</li>
                <li>点击 <span class="warning">"尝试替代方案"</span> 查看解决建议</li>
                <li>长按PDF链接，选择"用其他应用打开"</li>
                <li>安装WPS Office或福昕PDF阅读器</li>
                <li>切换到Chrome或Firefox浏览器</li>
            </ol>
            <p class="warning">⚠️ 部分国产浏览器对data: URL的PDF支持有限，这是正常现象</p>
        </div>
        <?php endif; ?>

        <div class="status-box">
            <h3>📱 手机测试地址</h3>
            <p>请在不同手机浏览器中访问:</p>
            <p><code>http://192.168.196.90:8080/wp-content/browser-compatibility-test.php</code></p>
            
            <h4>建议测试的浏览器：</h4>
            <ul style="columns: 2; font-size: 14px;">
                <li>小米浏览器 ✨</li>
                <li>Chrome浏览器</li>
                <li>Firefox浏览器</li>
                <li>UC浏览器</li>
                <li>QQ浏览器</li>
                <li>华为浏览器</li>
                <li>Edge浏览器</li>
                <li>系统自带浏览器</li>
            </ul>
        </div>
    </div>

    <script>
        // 浏览器信息日志
        console.log('浏览器兼容性测试页面');
        console.log('User Agent:', navigator.userAgent);
        console.log('PDF支持检测:', {
            'PDF.js': typeof pdfjsLib !== 'undefined',
            'Blob URL': typeof URL !== 'undefined' && typeof URL.createObjectURL !== 'undefined',
            'Data URL': /data:/.test('data:text/plain,test'),
            'Touch支持': 'ontouchstart' in window,
            '最大触摸点': navigator.maxTouchPoints || 0
        });
        
        // 额外的iframe加载监听（备用）
        document.addEventListener('DOMContentLoaded', function() {
            const iframes = document.querySelectorAll('iframe[src*="data:application/pdf"]');
            iframes.forEach(function(iframe, index) {
                console.log('发现PDF iframe:', iframe.src.substring(0, 50) + '...');
                
                iframe.addEventListener('load', function() {
                    console.log('PDF iframe加载完成:', index);
                });
                
                iframe.addEventListener('error', function() {
                    console.log('PDF iframe加载失败:', index);
                });
            });
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
