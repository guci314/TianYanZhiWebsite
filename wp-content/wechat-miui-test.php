<?php
// 微信小米设备专用PDF测试页面
// 访问地址: http://localhost:8080/wp-content/wechat-miui-test.php

// 引入WordPress环境
require_once dirname(__DIR__) . '/wp-load.php';

// 测试PDF数据
$test_pdf_data = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCi9WZXJzaW9uIC8xLjQKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSA0IDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCj4+CmVuZG9iago1IDAgb2JqCjw8Ci9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCi9GMSAxMiBUZgo1MCA3MDBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNzQgMDAwMDAgbiAKMDAwMDAwMDEzMSAwMDAwMCBuIAowMDAwMDAwMjkxIDAwMDAwIG4gCjAwMDAwMDAzNzAgMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA2Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo0NjQKJUVPRg==';

// 获取用户代理
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// 详细检测
$detection = [
    'is_wechat' => strpos($user_agent, 'MicroMessenger') !== false,
    'is_miui_device' => strpos($user_agent, '2312P') !== false || strpos($user_agent, 'WOCC') !== false || strpos($user_agent, 'MMMWRESDK') !== false,
    'contains_micromessenger' => strpos($user_agent, 'MicroMessenger') !== false,
    'contains_android' => strpos($user_agent, 'Android') !== false,
    'contains_mobile' => strpos($user_agent, 'Mobile') !== false,
    'version' => ''
];

// 提取微信版本
if ($detection['is_wechat']) {
    preg_match('/MicroMessenger\/([0-9.]+)/', $user_agent, $matches);
    $detection['version'] = $matches[1] ?? '未知';
}

// 插件检测结果
$plugin_detection = [];
if (function_exists('dcmp_detect_device_type')) {
    $plugin_detection = dcmp_detect_device_type();
}

// 测试短代码
$shortcode_test = '[dc_ppt src="' . $test_pdf_data . '"]';
$shortcode_result = do_shortcode($shortcode_test);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💬📱 微信小米设备PDF测试页面</title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #1aad19 0%, #07c160 100%);
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .wechat-header {
            background: linear-gradient(135deg, #1aad19, #07c160);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .status-box {
            background: #f0f8f0;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #1aad19;
        }
        .detection-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 15px 0;
        }
        .detection-item {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
        }
        .detected {
            background: #c8e6c9;
            color: #2e7d32;
            font-weight: bold;
        }
        .not-detected {
            background: #ffcdd2;
            color: #c62828;
        }
        .test-result {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #1aad19;
            min-height: 500px;
        }
        h1 { color: #1aad19; text-align: center; margin: 0; }
        h2 { color: #1aad19; border-bottom: 2px solid #1aad19; padding-bottom: 5px; }
        .code-block {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
            font-size: 12px;
            margin: 10px 0;
        }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 20px; }
            .detection-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wechat-header">
            <h1>💬📱 微信小米设备PDF测试</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">专门针对小米设备上的微信内置浏览器优化</p>
        </div>

        <h2>🔍 设备环境检测</h2>
        <div class="status-box">
            <p><strong>完整User Agent:</strong></p>
            <div class="code-block"><?php echo esc_html($user_agent); ?></div>
            
            <p><strong>关键特征检测:</strong></p>
            <div class="detection-grid">
                <div class="detection-item <?php echo $detection['is_wechat'] ? 'detected' : 'not-detected'; ?>">
                    💬 微信浏览器<br><?php echo $detection['is_wechat'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="detection-item <?php echo $detection['is_miui_device'] ? 'detected' : 'not-detected'; ?>">
                    📱 MIUI设备<br><?php echo $detection['is_miui_device'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="detection-item <?php echo $detection['contains_android'] ? 'detected' : 'not-detected'; ?>">
                    🤖 Android系统<br><?php echo $detection['contains_android'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
                <div class="detection-item <?php echo $detection['contains_mobile'] ? 'detected' : 'not-detected'; ?>">
                    📱 移动设备<br><?php echo $detection['contains_mobile'] ? '✅ 检测到' : '❌ 未检测到'; ?>
                </div>
            </div>
            
            <?php if ($detection['is_wechat']): ?>
                <p><strong>微信版本:</strong> <?php echo esc_html($detection['version']); ?></p>
            <?php endif; ?>
        </div>

        <h2>🎯 插件检测结果</h2>
        <?php if (!empty($plugin_detection)): ?>
            <div class="status-box">
                <p><strong>DC Media Protect插件检测:</strong></p>
                <ul>
                    <li><strong>微信浏览器:</strong> <?php echo ($plugin_detection['is_wechat_browser'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>MIUI设备:</strong> <?php echo ($plugin_detection['is_miui_device'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>小米浏览器:</strong> <?php echo ($plugin_detection['is_mi_browser'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>Chrome浏览器:</strong> <?php echo ($plugin_detection['is_chrome'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>移动设备:</strong> <?php echo ($plugin_detection['is_mobile'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                </ul>
                
                <?php if (($plugin_detection['is_wechat_browser'] ?? false) && ($plugin_detection['is_miui_device'] ?? false)): ?>
                    <p style="color:#2e7d32; font-weight:bold; margin-top:15px; padding:10px; background:#c8e6c9; border-radius:5px;">
                        🎉 完美匹配！您的设备环境被正确识别为："小米设备上的微信浏览器"<br>
                        将使用专门优化的微信小米兼容方案显示PDF
                    </p>
                <?php elseif ($plugin_detection['is_wechat_browser'] ?? false): ?>
                    <p style="color:#1976d2; font-weight:bold; margin-top:15px; padding:10px; background:#e3f2fd; border-radius:5px;">
                        ✅ 检测到微信浏览器，但未识别为MIUI设备<br>
                        将使用通用微信兼容方案显示PDF
                    </p>
                <?php else: ?>
                    <p style="color:#c62828; font-weight:bold; margin-top:15px; padding:10px; background:#ffebee; border-radius:5px;">
                        ⚠️ 未能正确识别为微信浏览器，请检查检测逻辑
                    </p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="status-box" style="background:#ffebee; border-left-color:#f44336;">
                <p style="color:#c62828; font-weight:bold;">❌ 插件检测函数不可用，请确认插件是否正确安装和激活</p>
            </div>
        <?php endif; ?>

        <h2>📄 PDF显示效果测试</h2>
        <div class="test-result">
            <h3>🎯 当前的PDF显示方案:</h3>
            <p style="margin-bottom:20px; color:#666;">
                <?php if (($plugin_detection['is_wechat_browser'] ?? false) && ($plugin_detection['is_miui_device'] ?? false)): ?>
                    <span style="color:#1aad19; font-weight:bold;">💬📱 小米微信专用方案</span><br>
                    预期功能：微信绿色主题 + 外部应用唤起 + 链接复制 + 可选iframe显示
                <?php elseif ($plugin_detection['is_wechat_browser'] ?? false): ?>
                    <span style="color:#1aad19; font-weight:bold;">💬 微信通用方案</span><br>
                    预期功能：微信主题 + 外部浏览器选项 + 链接复制
                <?php else: ?>
                    <span style="color:#666; font-weight:bold;">📱 备用方案</span><br>
                    预期功能：根据其他检测结果选择显示方案
                <?php endif; ?>
            </p>
            
            <?php echo $shortcode_result; ?>
        </div>

        <div class="status-box">
            <h3>📱 微信用户使用指南</h3>
            <p><strong>推荐操作步骤：</strong></p>
            <ol>
                <li><strong>首选：</strong>点击 "在浏览器中打开PDF" 按钮</li>
                <li><strong>备选：</strong>点击 "用其他应用打开" 按钮，选择PDF阅读器</li>
                <li><strong>手动：</strong>点击 "复制链接" 然后粘贴到浏览器中</li>
                <li><strong>最后：</strong>展开 "尝试直接显示" 查看iframe效果（通常不兼容）</li>
            </ol>
            
            <p><strong>如果所有方法都无效：</strong></p>
            <ul>
                <li>安装 WPS Office、福昕PDF阅读器等应用</li>
                <li>使用手机自带浏览器打开链接</li>
                <li>将链接分享给朋友，用Chrome等浏览器打开</li>
            </ul>
        </div>

        <div class="status-box">
            <h3>🔧 技术说明</h3>
            <p><strong>微信内置浏览器限制：</strong></p>
            <ul>
                <li>基于Chrome内核，但对PDF显示有安全限制</li>
                <li>不支持data: URL的PDF直接显示</li>
                <li>需要通过外部应用或浏览器查看PDF</li>
                <li>小米MIUI系统提供了更好的应用调用支持</li>
            </ul>
        </div>

        <div class="status-box">
            <h3>📱 测试地址</h3>
            <p>在微信中分享此链接给朋友测试：</p>
            <p><code>http://192.168.196.90:8080/wp-content/wechat-miui-test.php</code></p>
        </div>
    </div>

    <script>
        // 客户端环境检测
        console.log('💬📱 微信小米设备PDF测试页面');
        console.log('User Agent:', navigator.userAgent);
        console.log('Platform:', navigator.platform);
        console.log('屏幕尺寸:', screen.width + 'x' + screen.height);
        console.log('可用尺寸:', screen.availWidth + 'x' + screen.availHeight);
        console.log('设备像素比:', window.devicePixelRatio || 1);
        console.log('触摸支持:', 'ontouchstart' in window);
        console.log('是否在iframe中:', window !== window.top);
        
        // 检测微信特有对象
        console.log('微信JS-SDK:', typeof wx !== 'undefined');
        console.log('微信小程序:', typeof __wxjs_environment !== 'undefined');
        
        // 检测PDF支持
        console.log('PDF插件支持:', navigator.plugins && Array.from(navigator.plugins).some(p => p.name.toLowerCase().includes('pdf')));
        console.log('mimeTypes支持:', navigator.mimeTypes && navigator.mimeTypes['application/pdf']);
    </script>

    <?php wp_footer(); ?>
</body>
</html>
