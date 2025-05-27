<?php
// 增强版移动端PDF测试页面 - 详细设备检测
// 访问地址: http://localhost:8080/wp-content/enhanced-mobile-test.php

// 获取User Agent
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// 方法1: 基础移动设备检测
function basic_mobile_detect($user_agent) {
    return preg_match('/Mobile|Android|BlackBerry|iPhone|iPad|iPod|Windows Phone|webOS/', $user_agent);
}

// 方法2: 增强移动设备检测
function enhanced_mobile_detect($user_agent) {
    return preg_match('/Mobile|Android|BlackBerry|iPhone|iPad|iPod|Windows Phone|webOS|Opera Mini|IEMobile|WPDesktop|Mobi|Tablet|Touch/i', $user_agent) ||
           (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) ||
           preg_match('/Opera.*Mini|Opera.*Mobi/i', $user_agent) ||
           preg_match('/Chrome.*Mobile|Safari.*Mobile/i', $user_agent);
}

// 方法3: WordPress wp_is_mobile模拟
function wp_mobile_detect($user_agent) {
    return preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $user_agent) ||
           strpos(strtolower($user_agent), 'mobile') !== false ||
           strpos(strtolower($user_agent), 'tablet') !== false;
}

// 设备类型分析
$is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
$is_android = strpos($user_agent, 'Android') !== false;
$is_safari = strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false;
$is_chrome = strpos($user_agent, 'Chrome') !== false;
$is_firefox = strpos($user_agent, 'Firefox') !== false;

// 浏览器桌面模式检测
$is_desktop_mode = strpos($user_agent, 'Macintosh') !== false || 
                   strpos($user_agent, 'Windows NT') !== false ||
                   strpos($user_agent, 'X11') !== false;

// 各种检测结果
$basic_result = basic_mobile_detect($user_agent);
$enhanced_result = enhanced_mobile_detect($user_agent);
$wp_result = wp_mobile_detect($user_agent);

// 测试PDF
$test_pdf = 'data:application/pdf;base64,JVBERi0xLjQKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDIgMCBSCi9WZXJzaW9uIC8xLjQKPj4KZW5kb2JqCjIgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUl0KL0NvdW50IDEKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAyIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSA0IDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL0ZvbnQKL1N1YnR5cGUgL1R5cGUxCi9CYXNlRm9udCAvSGVsdmV0aWNhCj4+CmVuZG9iago1IDAgb2JqCjw8Ci9MZW5ndGggNDQKPj4Kc3RyZWFtCkJUCi9GMSAxMiBUZgo1MCA3MDBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDYKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDA5IDAwMDAwIG4gCjAwMDAwMDAwNzQgMDAwMDAgbiAKMDAwMDAwMDEzMSAwMDAwMCBuIAowMDAwMDAwMjkxIDAwMDAwIG4gCjAwMDAwMDAzNzAgMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA2Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo0NjQKJUVPRg==';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>增强版移动端检测测试</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            font-size: 14px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .detection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .detection-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }
        .detection-card.positive {
            border-color: #28a745;
            background: #f8fff9;
        }
        .detection-card.negative {
            border-color: #dc3545;
            background: #fff8f8;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin: 2px;
        }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .test-area {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 2px solid #007cba;
        }
        .user-agent {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            line-height: 1.4;
        }
        .pdf-viewer {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 15px 0;
        }
        h1 { color: #333; text-align: center; margin-bottom: 5px; }
        h2 { color: #007cba; border-bottom: 2px solid #007cba; padding-bottom: 5px; }
        h3 { color: #495057; margin-top: 0; }
        @media (max-width: 768px) {
            body { padding: 10px; font-size: 13px; }
            .container { padding: 15px; }
            .detection-grid { grid-template-columns: 1fr; gap: 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 增强版移动端设备检测测试</h1>
        <p style="text-align:center; color:#666; margin-bottom:25px;">
            详细分析移动设备检测算法 - <?php echo date('Y-m-d H:i:s'); ?>
        </p>

        <h2>📱 检测结果对比</h2>
        <div class="detection-grid">
            <div class="detection-card <?php echo $basic_result ? 'positive' : 'negative'; ?>">
                <h3>🔵 基础检测</h3>
                <p><strong>结果:</strong> 
                    <span class="status-badge <?php echo $basic_result ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $basic_result ? '✅ 移动设备' : '❌ 桌面设备'; ?>
                    </span>
                </p>
                <p><strong>算法:</strong> 基础正则表达式匹配</p>
                <small>匹配: Mobile|Android|BlackBerry|iPhone|iPad|iPod|Windows Phone|webOS</small>
            </div>

            <div class="detection-card <?php echo $enhanced_result ? 'positive' : 'negative'; ?>">
                <h3>🟢 增强检测</h3>
                <p><strong>结果:</strong> 
                    <span class="status-badge <?php echo $enhanced_result ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $enhanced_result ? '✅ 移动设备' : '❌ 桌面设备'; ?>
                    </span>
                </p>
                <p><strong>算法:</strong> 多条件综合检测</p>
                <small>包括WAP配置、Opera Mobile、Chrome Mobile等</small>
            </div>

            <div class="detection-card <?php echo $wp_result ? 'positive' : 'negative'; ?>">
                <h3>🔶 WordPress风格</h3>
                <p><strong>结果:</strong> 
                    <span class="status-badge <?php echo $wp_result ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $wp_result ? '✅ 移动设备' : '❌ 桌面设备'; ?>
                    </span>
                </p>
                <p><strong>算法:</strong> 模拟wp_is_mobile函数</p>
                <small>匹配: 浏览器标识符 + mobile/tablet 关键词</small>
            </div>
        </div>

        <h2>🧬 设备特征分析</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
            <div>
                <h4>操作系统</h4>
                <span class="status-badge <?php echo $is_ios ? 'badge-info' : 'badge-danger'; ?>">
                    iOS: <?php echo $is_ios ? '是' : '否'; ?>
                </span><br>
                <span class="status-badge <?php echo $is_android ? 'badge-info' : 'badge-danger'; ?>">
                    Android: <?php echo $is_android ? '是' : '否'; ?>
                </span>
            </div>
            <div>
                <h4>浏览器</h4>
                <span class="status-badge <?php echo $is_safari ? 'badge-info' : 'badge-danger'; ?>">
                    Safari: <?php echo $is_safari ? '是' : '否'; ?>
                </span><br>
                <span class="status-badge <?php echo $is_chrome ? 'badge-info' : 'badge-danger'; ?>">
                    Chrome: <?php echo $is_chrome ? '是' : '否'; ?>
                </span><br>
                <span class="status-badge <?php echo $is_firefox ? 'badge-info' : 'badge-danger'; ?>">
                    Firefox: <?php echo $is_firefox ? '是' : '否'; ?>
                </span>
            </div>
            <div>
                <h4>模式检测</h4>
                <span class="status-badge <?php echo $is_desktop_mode ? 'badge-info' : 'badge-danger'; ?>">
                    桌面模式: <?php echo $is_desktop_mode ? '是' : '否'; ?>
                </span>
            </div>
        </div>

        <h2>📄 User Agent 详细信息</h2>
        <div class="user-agent">
            <?php echo htmlspecialchars($user_agent); ?>
        </div>

        <h2>🧪 PDF显示测试</h2>
        <div class="test-area">
            <p><strong>根据增强检测结果选择显示方案:</strong></p>
            
            <div class="pdf-viewer">
                <?php if ($enhanced_result): ?>
                    <!-- 移动端方案 -->
                    <div style="font-size:48px; margin-bottom:15px;">📱</div>
                    <h3 style="color:#28a745;">移动端优化PDF查看器</h3>
                    <p style="color:#666; margin-bottom:20px;">
                        检测到移动设备，使用移动端优化方案
                    </p>
                    <div style="display:flex; flex-direction:column; gap:10px; max-width:300px; margin:0 auto;">
                        <a href="<?php echo $test_pdf; ?>" target="_blank" 
                           style="background:#007cba; color:white; padding:12px 20px; text-decoration:none; border-radius:8px; font-weight:bold;">
                            🔗 在新窗口打开PDF
                        </a>
                        <a href="<?php echo $test_pdf; ?>" download="test.pdf" 
                           style="background:#28a745; color:white; padding:12px 20px; text-decoration:none; border-radius:8px; font-weight:bold;">
                            📥 下载PDF文件
                        </a>
                    </div>
                    <small style="color:#999; margin-top:15px; display:block;">
                        ✅ 移动端PDF显示修复已应用
                    </small>
                <?php else: ?>
                    <!-- 桌面端方案 -->
                    <div style="font-size:48px; margin-bottom:15px;">🖥️</div>
                    <h3 style="color:#6c757d;">桌面端PDF查看器</h3>
                    <iframe src="<?php echo $test_pdf; ?>" 
                            width="100%" 
                            height="400" 
                            style="border:1px solid #ccc; border-radius:5px; margin:15px 0;">
                    </iframe>
                    <a href="<?php echo $test_pdf; ?>" target="_blank" 
                       style="background:#007cba; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">
                        🔗 在新窗口打开
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div style="background:#e7f3ff; padding:20px; border-radius:10px; margin-top:20px;">
            <h3>📱 测试建议</h3>
            <p><strong>手机测试地址:</strong></p>
            <p><code style="background:#f8f9fa; padding:4px 8px; border-radius:3px; font-size:12px;">
                http://192.168.196.90:8080/wp-content/enhanced-mobile-test.php
            </code></p>
            
            <h4>期望结果:</h4>
            <ul style="margin:10px 0;">
                <li>✅ 至少一种检测方法应识别为移动设备</li>
                <li>✅ 显示移动端优化的PDF查看器</li>
                <li>✅ PDF可以正常打开或下载</li>
                <li>✅ 页面在手机上完全适配</li>
            </ul>
        </div>
    </div>

    <script>
        console.log('=== 增强版移动端检测测试 ===');
        console.log('User Agent:', navigator.userAgent);
        console.log('屏幕尺寸:', screen.width + 'x' + screen.height);
        console.log('视口尺寸:', window.innerWidth + 'x' + window.innerHeight);
        
        // JavaScript移动设备检测
        function jsIsMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
                   ('ontouchstart' in window) ||
                   (navigator.maxTouchPoints > 0) ||
                   (window.innerWidth <= 768);
        }
        
        console.log('JavaScript检测结果:', jsIsMobile() ? '移动设备' : '桌面设备');
        console.log('触摸支持:', 'ontouchstart' in window);
        console.log('触摸点数:', navigator.maxTouchPoints || 0);
        
        // 动态更新检测结果
        if (jsIsMobile()) {
            document.body.insertAdjacentHTML('beforeend', 
                '<div style="position:fixed; bottom:20px; right:20px; background:#28a745; color:white; padding:10px 15px; border-radius:25px; font-size:12px; z-index:1000;">' +
                '✅ JS检测: 移动设备</div>'
            );
        }
        
        // 防下载保护
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('dragstart', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
    </script>
</body>
</html>
