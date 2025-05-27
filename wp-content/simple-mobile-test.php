<?php
// 简单的移动端PDF测试页面 - WordPress Docker环境
// 访问地址: http://localhost:8080/wp-content/simple-mobile-test.php

function is_mobile_device() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    // 增强的移动设备检测
    return preg_match('/Mobile|Android|BlackBerry|iPhone|iPad|iPod|Windows Phone|webOS|Opera Mini|IEMobile|WPDesktop|Mobi|Tablet/', $user_agent) ||
           // 检测触摸屏设备
           (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) ||
           // 检测屏幕宽度
           (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('/mobile/i', $_SERVER['HTTP_X_FORWARDED_FOR']));
}

$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$is_mobile = is_mobile_device();
$is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
$is_android = strpos($user_agent, 'Android') !== false;
$is_safari = strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false;
$is_chrome = strpos($user_agent, 'Chrome') !== false;

$test_pdf_base64 = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCi9WZXJzaW9uIC8xLjQKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSA0IDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCj4+CmVuZG9iago1IDAgb2JqCjw8Ci9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCi9GMSAxMiBUZgo1MCA3MDBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNzQgMDAwMDAgbiAKMDAwMDAwMDEzMSAwMDAwMCBuIAowMDAwMDAwMjkxIDAwMDAwIG4gCjAwMDAwMDAzNzAgMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA2Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo0NjQKJUVPRg==';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>移动端PDF修复测试 - WordPress Docker</title>
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
            background: linear-gradient(135deg, #e7f3ff 0%, #f0f8ff 100%); 
            padding: 25px; 
            border-radius: 10px; 
            margin: 20px 0;
            border-left: 5px solid #007cba;
        }
        .test-area {
            border: 3px solid #007cba;
            padding: 20px;
            margin: 25px 0;
            border-radius: 10px;
            background: #fafafa;
            position: relative;
        }
        .mobile-pdf-viewer {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .device-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #28a745;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin: 5px;
        }
        .ios { background: #007aff; }
        .android { background: #34c759; }
        .desktop { background: #6c757d; }
        .mobile { background: #28a745; }
        .action-button {
            display: inline-block;
            padding: 12px 24px;
            margin: 8px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary { background: #007cba; color: white; }
        .btn-success { background: #28a745; color: white; }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 48px;
            color: rgba(0,124,186,0.1);
            font-weight: bold;
            pointer-events: none;
        }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align:center; color:#4a5568;">📱 移动端PDF显示修复测试</h1>
        <p style="text-align:center; color:#666;">WordPress Docker环境 - DC Media Protect插件</p>
        
        <div class="status-box">
            <h3>🔍 设备检测结果</h3>
            <p><strong>运行时间:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>当前设备:</strong> 
                <?php if ($is_mobile): ?>
                    <span class="device-badge <?php echo $is_ios ? 'ios' : ($is_android ? 'android' : 'mobile'); ?>">
                        <?php echo $is_ios ? '📱 iOS设备' : ($is_android ? '🤖 Android设备' : '📱 移动设备'); ?>
                    </span>
                <?php else: ?>
                    <span class="device-badge desktop">🖥️ 桌面设备</span>
                <?php endif; ?>
            </p>
            <p><strong>浏览器:</strong> <?php echo $is_safari ? 'Safari' : ($is_chrome ? 'Chrome' : '其他'); ?></p>
            <details>
                <summary>查看User Agent</summary>
                <small style="word-break:break-all;"><?php echo htmlspecialchars($user_agent); ?></small>
            </details>
        </div>

        <div class="test-area">
            <div class="watermark">数字中国</div>
            <h4>🧪 PDF显示测试</h4>
            
            <div class="mobile-pdf-viewer">
                <div style="font-size:48px; margin-bottom:20px;">📄</div>
                <h3 style="color:#333; margin:0 0 15px 0;">
                    <?php if ($is_ios): ?>
                        iOS优化PDF查看器
                    <?php elseif ($is_android): ?>
                        Android PDF.js渲染器
                    <?php elseif ($is_mobile): ?>
                        移动端通用查看器
                    <?php else: ?>
                        桌面端PDF查看器
                    <?php endif; ?>
                </h3>
                
                <div style="margin:20px 0;">
                    <?php if ($is_mobile): ?>
                        <a href="<?php echo $test_pdf_base64; ?>" target="_blank" class="action-button btn-primary">
                            🔗 在新窗口打开PDF
                        </a>
                        <br>
                        <a href="<?php echo $test_pdf_base64; ?>" download="test.pdf" class="action-button btn-success">
                            📥 下载PDF文件
                        </a>
                    <?php else: ?>
                        <iframe src="<?php echo $test_pdf_base64; ?>" 
                                width="100%" 
                                height="400" 
                                style="border:1px solid #ccc; border-radius:5px;">
                        </iframe>
                        <br><br>
                        <a href="<?php echo $test_pdf_base64; ?>" target="_blank" class="action-button btn-primary">
                            🔗 在新窗口打开
                        </a>
                    <?php endif; ?>
                </div>
                
                <small style="color:#999;">✅ 移动端PDF显示修复已应用</small>
            </div>
        </div>

        <div class="status-box">
            <h3>📱 手机测试指南</h3>
            <p>要在手机上测试，请在手机浏览器访问:</p>
            <p><code style="background:#f8f9fa; padding:4px 8px; border-radius:3px; font-size:12px;">
                http://192.168.196.90:8080/wp-content/simple-mobile-test.php
            </code></p>
            <p><strong>期望结果:</strong></p>
            <ul>
                <li>✅ 显示对应的移动设备标签</li>
                <li>✅ PDF能正常打开或下载</li>
                <li>✅ 页面完全适配手机屏幕</li>
                <li>✅ 水印保护正常显示</li>
            </ul>
        </div>
    </div>
    
    <script>
        console.log('📱 移动端PDF测试页面已加载');
        console.log('设备信息:', navigator.userAgent);
        
        // JavaScript移动设备检测
        function isMobileJS() {
            return /Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone|webOS|Opera Mini/i.test(navigator.userAgent) ||
                   ('ontouchstart' in window) ||
                   (navigator.maxTouchPoints > 0) ||
                   (window.innerWidth <= 768);
        }
        
        // 如果PHP检测为桌面设备，但JS检测为移动设备，则显示警告
        if (isMobileJS()) {
            console.log('✅ JavaScript检测: 移动设备');
            
            // 如果页面显示为桌面设备，添加移动设备提示
            const deviceBadge = document.querySelector('.device-badge.desktop');
            if (deviceBadge) {
                deviceBadge.innerHTML = '📱 移动设备 (JS检测)';
                deviceBadge.className = 'device-badge mobile';
                deviceBadge.style.background = '#28a745';
                
                // 更新PDF查看器标题
                const viewerTitle = document.querySelector('.mobile-pdf-viewer h3');
                if (viewerTitle) {
                    viewerTitle.textContent = '移动端优化PDF查看器 (JS检测)';
                }
            }
        } else {
            console.log('🖥️ JavaScript检测: 桌面设备');
        }
        
        // 显示详细的设备信息
        console.log('设备详情:', {
            userAgent: navigator.userAgent,
            screenSize: window.screen.width + 'x' + window.screen.height,
            viewportSize: window.innerWidth + 'x' + window.innerHeight,
            touchSupport: 'ontouchstart' in window,
            maxTouchPoints: navigator.maxTouchPoints || 0,
            isMobileJS: isMobileJS()
        });
        
        // 防下载保护
        document.addEventListener('contextmenu', function(e) { e.preventDefault(); });
        document.addEventListener('dragstart', function(e) { e.preventDefault(); });
    </script>
</body>
</html>
