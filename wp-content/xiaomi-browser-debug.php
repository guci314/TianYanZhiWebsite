<?php
// 小米浏览器专用调试页面
// 访问地址: http://localhost:8080/wp-content/xiaomi-browser-debug.php

// 引入WordPress环境
require_once dirname(__DIR__) . '/wp-load.php';

// 测试PDF数据
$test_pdf_data = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCi9WZXJzaW9uIC8xLjQKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSA0IDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCj4+CmVuZG9iago1IDAgb2JqCjw8Ci9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCi9GMSAxMiBUZgo1MCA3MDBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNzQgMDAwMDAgbiAKMDAwMDAwMDEzMSAwMDAwMCBuIAowMDAwMDAwMjkxIDAwMDAwIG4gCjAwMDAwMDAzNzAgMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA2Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo0NjQKJUVPRg==';

// 获取用户代理
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// 详细的浏览器检测
$detailed_detection = [
    'contains_MiuiBrowser' => strpos($user_agent, 'MiuiBrowser') !== false,
    'contains_XiaoMi' => strpos($user_agent, 'XiaoMi') !== false,
    'contains_MIUI' => strpos($user_agent, 'MIUI') !== false,
    'contains_Chrome' => strpos($user_agent, 'Chrome') !== false,
    'contains_Android' => strpos($user_agent, 'Android') !== false,
    'contains_Mobile' => strpos($user_agent, 'Mobile') !== false,
    'contains_MicroMessenger' => strpos($user_agent, 'MicroMessenger') !== false,
    'contains_MIUI_Device' => strpos($user_agent, '2312P') !== false || strpos($user_agent, 'WOCC') !== false || strpos($user_agent, 'MMMWRESDK') !== false,
    'wp_is_mobile' => function_exists('wp_is_mobile') ? wp_is_mobile() : false
];

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
    <title>🔧 小米浏览器专用调试页面</title>
    <?php wp_head(); ?>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #ff6900 0%, #fcb045 100%);
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
        .debug-box {
            background: #fff3e0;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #ff9800;
        }
        .success-box {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #4caf50;
        }
        .error-box {
            background: #ffebee;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 5px solid #f44336;
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
            background: #e1f5fe;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #0288d1;
            min-height: 500px;
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #ff9800; border-bottom: 2px solid #ff9800; padding-bottom: 5px; }
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
        <h1>🔧 小米浏览器专用调试页面</h1>
        <p style="text-align:center; color:#666;">
            📱 专门诊断小米浏览器PDF显示问题 - <?php echo date('Y-m-d H:i:s'); ?>
        </p>

        <h2>🕵️ 用户代理字符串分析</h2>
        <div class="debug-box">
            <p><strong>完整User Agent:</strong></p>
            <div class="code-block"><?php echo esc_html($user_agent); ?></div>
            
            <p><strong>关键字符串检测:</strong></p>
            <div class="detection-grid">
                <div class="detection-item <?php echo $detailed_detection['contains_MiuiBrowser'] ? 'detected' : 'not-detected'; ?>">
                    MiuiBrowser<br><?php echo $detailed_detection['contains_MiuiBrowser'] ? '✅ 找到' : '❌ 未找到'; ?>
                </div>
                <div class="detection-item <?php echo $detailed_detection['contains_XiaoMi'] ? 'detected' : 'not-detected'; ?>">
                    XiaoMi<br><?php echo $detailed_detection['contains_XiaoMi'] ? '✅ 找到' : '❌ 未找到'; ?>
                </div>
                <div class="detection-item <?php echo $detailed_detection['contains_MIUI'] ? 'detected' : 'not-detected'; ?>">
                    MIUI<br><?php echo $detailed_detection['contains_MIUI'] ? '✅ 找到' : '❌ 未找到'; ?>
                </div>
                <div class="detection-item <?php echo $detailed_detection['contains_Chrome'] ? 'detected' : 'not-detected'; ?>">
                    Chrome<br><?php echo $detailed_detection['contains_Chrome'] ? '✅ 找到' : '❌ 未找到'; ?>
                </div>
                <div class="detection-item <?php echo $detailed_detection['contains_MicroMessenger'] ? 'detected' : 'not-detected'; ?>">
                    MicroMessenger<br><?php echo $detailed_detection['contains_MicroMessenger'] ? '✅ 找到' : '❌ 未找到'; ?>
                </div>
                <div class="detection-item <?php echo $detailed_detection['contains_MIUI_Device'] ? 'detected' : 'not-detected'; ?>">
                    MIUI设备<br><?php echo $detailed_detection['contains_MIUI_Device'] ? '✅ 找到' : '❌ 未找到'; ?>
                </div>
            </div>
        </div>

        <h2>🎯 插件检测结果</h2>
        <?php if (!empty($plugin_detection)): ?>
            <div class="<?php echo ($plugin_detection['is_mi_browser'] ?? false) ? 'success-box' : 'error-box'; ?>">
                <p><strong>插件检测状态:</strong></p>
                <ul>
                    <li><strong>小米浏览器:</strong> <?php echo ($plugin_detection['is_mi_browser'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>Chrome浏览器:</strong> <?php echo ($plugin_detection['is_chrome'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>微信浏览器:</strong> <?php echo ($plugin_detection['is_wechat_browser'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>MIUI设备:</strong> <?php echo ($plugin_detection['is_miui_device'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>Android设备:</strong> <?php echo ($plugin_detection['is_android'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                    <li><strong>移动设备:</strong> <?php echo ($plugin_detection['is_mobile'] ?? false) ? '✅ 检测到' : '❌ 未检测到'; ?></li>
                </ul>
                
                <?php if (($plugin_detection['is_wechat_browser'] ?? false) && ($plugin_detection['is_miui_device'] ?? false)): ?>
                    <p class="success" style="color:#2e7d32; font-weight:bold; margin-top:15px;">
                        🎉 完美！检测到您在小米设备上使用微信浏览器！<br>
                        将使用专门的小米微信兼容方案显示PDF
                    </p>
                <?php elseif ($plugin_detection['is_wechat_browser'] ?? false): ?>
                    <p class="success" style="color:#1976d2; font-weight:bold; margin-top:15px;">
                        ✅ 检测到微信浏览器！<br>
                        将使用专门的微信兼容方案显示PDF
                    </p>
                <?php elseif ($plugin_detection['is_mi_browser'] ?? false): ?>
                    <p class="success" style="color:#2e7d32; font-weight:bold; margin-top:15px;">
                        🎉 太好了！小米浏览器现在被正确识别了！<br>
                        将使用专门的国产浏览器兼容方案显示PDF
                    </p>
                <?php elseif ($plugin_detection['is_chrome'] ?? false): ?>
                    <p class="error" style="color:#c62828; font-weight:bold; margin-top:15px;">
                        ⚠️ 仍被识别为Chrome浏览器，可能需要进一步优化检测逻辑
                    </p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="error-box">
                <p style="color:#c62828; font-weight:bold;">❌ 插件检测函数不可用，请检查插件是否正确加载</p>
            </div>
        <?php endif; ?>

        <h2>📄 PDF显示测试</h2>
        <div class="test-result">
            <h3>🎯 当前的PDF显示效果:</h3>
            <p style="margin-bottom:20px; color:#666;">
                <?php if (($plugin_detection['is_wechat_browser'] ?? false) && ($plugin_detection['is_miui_device'] ?? false)): ?>
                    <span style="color:#1aad19; font-weight:bold;">💬📱 使用小米微信专用显示方案</span><br>
                    预期显示：微信内置浏览器优化界面 + 外部应用唤起 + 链接复制
                <?php elseif ($plugin_detection['is_wechat_browser'] ?? false): ?>
                    <span style="color:#1aad19; font-weight:bold;">💬 使用微信浏览器专用显示方案</span><br>
                    预期显示：微信优化界面 + 外部浏览器选项
                <?php elseif (($plugin_detection['is_mi_browser'] ?? false)): ?>
                    <span style="color:#2e7d32; font-weight:bold;">✅ 使用小米浏览器专用显示方案</span><br>
                    预期显示：3秒iframe尝试 → 失败提示 → 备用按钮
                <?php elseif (($plugin_detection['is_chrome'] ?? false)): ?>
                    <span style="color:#ff9800; font-weight:bold;">⚠️ 使用Chrome浏览器PDF.js方案</span><br>
                    预期显示：PDF.js Canvas渲染 + 翻页控制
                <?php else: ?>
                    <span style="color:#666; font-weight:bold;">📱 使用通用移动端方案</span><br>
                    预期显示：简单的下载和新窗口选项
                <?php endif; ?>
            </p>
            <?php echo $shortcode_result; ?>
        </div>

        <div class="debug-box">
            <h3>📝 诊断说明</h3>
            <p><strong>修复重点:</strong></p>
            <ul>
                <li>✅ 调整检测优先级：国产浏览器检测优先于Chrome检测</li>
                <li>✅ 增加MIUI关键字检测，提高小米浏览器识别准确性</li>
                <li>✅ 为小米浏览器提供专门的兼容方案</li>
            </ul>
            
            <p><strong>如果仍显示为Chrome:</strong></p>
            <ol>
                <li>检查User Agent中是否包含"MIUI"、"XiaoMi"或"MiuiBrowser"</li>
                <li>如果都没有，可能需要根据实际UA进一步调整检测逻辑</li>
                <li>也可以在浏览器设置中查看是否有"标识"或"用户代理"选项</li>
            </ol>
        </div>

        <div class="debug-box">
            <h3>📱 手机测试地址</h3>
            <p>请在小米浏览器中访问:</p>
            <p><code>http://192.168.196.90:8080/wp-content/xiaomi-browser-debug.php</code></p>
            
            <p><strong>测试步骤:</strong></p>
            <ol>
                <li>查看"插件检测结果"是否显示"✅ 小米浏览器: 检测到"</li>
                <li>观察PDF显示区域是否显示小米浏览器专用界面</li>
                <li>测试"在新标签页打开"和"尝试替代方案"按钮</li>
                <li>如果问题仍然存在，请提供User Agent字符串以便进一步优化</li>
            </ol>
        </div>
    </div>

    <script>
        // 客户端浏览器信息日志
        console.log('🔧 小米浏览器调试页面');
        console.log('Navigator User Agent:', navigator.userAgent);
        console.log('Navigator Platform:', navigator.platform);
        console.log('Navigator Vendor:', navigator.vendor);
        console.log('屏幕信息:', {
            width: screen.width,
            height: screen.height,
            availWidth: screen.availWidth,
            availHeight: screen.availHeight,
            colorDepth: screen.colorDepth,
            pixelDepth: screen.pixelDepth
        });
        
        // 检查是否在iframe中
        console.log('是否在iframe中:', window !== window.top);
        
        // 检查触摸支持
        console.log('触摸支持:', {
            ontouchstart: 'ontouchstart' in window,
            maxTouchPoints: navigator.maxTouchPoints || 0,
            touchSupport: 'createTouch' in document
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
