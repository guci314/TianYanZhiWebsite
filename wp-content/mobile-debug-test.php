<?php
// 移动端PDF功能测试页面 - 模拟WordPress环境
// 直接访问: http://localhost:8080/wp-content/mobile-debug-test.php

// 模拟WordPress函数
function wp_is_mobile() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return preg_match('/Mobile|Android|BlackBerry|iPhone|iPad/', $user_agent);
}

function esc_url($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

function esc_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function home_url() {
    return 'http://localhost:8080';
}

// 引入我们的移动端PDF查看器函数
$mobile_pdf_viewer_path = __DIR__ . '/plugins/dc-media-protect/includes/mobile-pdf-viewer.php';
if (file_exists($mobile_pdf_viewer_path)) {
    require_once $mobile_pdf_viewer_path;
}

// 模拟短代码处理函数
function dcmp_get_watermark_text() {
    return '数字中国';
}

// 复制我们修改后的短代码函数
function dcmp_shortcode_ppt($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    
    // 检测移动设备和具体设备类型
    $is_mobile = wp_is_mobile();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
    $is_android = strpos($user_agent, 'Android') !== false;
    
    // 调试信息
    $debug_info = '';
    if ($is_mobile) {
        $debug_info = '<!-- 调试: 移动设备检测 - iOS:' . ($is_ios ? 'Yes' : 'No') . ' Android:' . ($is_android ? 'Yes' : 'No') . ' UserAgent: ' . $user_agent . ' -->';
    }
    
    // 响应式尺寸设置
    if ($width > 0 && $height > 0) {
        $w = $width;
        $h = $height;
    } else {
        if ($is_mobile) {
            $w = '100%';
            $h = '400px';
        } else {
            $w = 800;
            $h = 600;
        }
    }
    
    // 检查是否为本地文件
    $is_local = (strpos($src, home_url()) === 0) || (strpos($src, 'data:') === 0);
    
    $viewer_html = '';
    $container_id = 'dcmp-pdf-' . uniqid();
    
    if ($is_local) {
        if ($is_mobile) {
            // 移动端：使用专用的移动端PDF查看器
            if (function_exists('dcmp_generate_mobile_pdf_viewer')) {
                $viewer_html = dcmp_generate_mobile_pdf_viewer($src, $w, $h, $container_id);
                $debug_info .= '<!-- 调试: 使用移动端PDF查看器 -->';
            } else {
                // 回退方案：简单的移动端显示
                $debug_info .= '<!-- 调试: mobile-pdf-viewer.php 函数不存在，使用回退方案 -->';
                $viewer_html = '
                <div style="width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; box-sizing:border-box; border-radius:8px;">
                    <div style="font-size:48px; margin-bottom:20px; color:#666;">📄</div>
                    <h3 style="margin:0 0 15px 0; color:#333;">移动端PDF查看器</h3>
                    <p style="margin:0 0 20px 0; color:#666; line-height:1.4;">
                        设备类型: ' . ($is_ios ? 'iOS' : ($is_android ? 'Android' : '移动设备')) . '<br>
                        正在加载移动端优化查看器...
                    </p>
                    <div style="display:flex; flex-direction:column; gap:10px; width:100%; max-width:250px;">
                        <a href="' . $src . '" target="_blank" style="background:#007cba; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block;">
                            🔗 在新窗口打开
                        </a>
                        <a href="' . $src . '" download style="background:#28a745; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block;">
                            📥 下载查看
                        </a>
                    </div>
                    <small style="margin-top:15px; color:#999; font-size:12px;">
                        移动端PDF查看器修复已应用
                    </small>
                </div>';
            }
        } else {
            // 桌面端：传统iframe方式
            $viewer_html = '<iframe src="' . $src . '" width="' . $w . '" height="' . $h . '" style="border:1px solid #ccc; max-width:100%;"></iframe>';
            $debug_info .= '<!-- 调试: 桌面端iframe显示 -->';
        }
    } else {
        // 外部PDF文件处理
        $debug_info .= '<!-- 调试: 外部PDF链接 -->';
        $viewer_html = '<div style="text-align:center; padding:20px;">外部PDF: <a href="' . $src . '" target="_blank">打开链接</a></div>';
    }
    
    $watermark = '<div class="dcmp-watermark">' . dcmp_get_watermark_text() . '</div>';
    return $debug_info . '<div class="dcmp-media-container dcmp-pdf-container" style="position:relative; max-width:100%; overflow:hidden;">'
        . $viewer_html
        . $watermark
        . '</div>';
}

// 简化的短代码处理
function do_shortcode($content) {
    if (strpos($content, '[dc_ppt') !== false) {
        preg_match('/\[dc_ppt([^\]]*)\]/', $content, $matches);
        if (isset($matches[1])) {
            $atts_string = trim($matches[1]);
            $atts = [];
            if (preg_match('/src="([^"]*)"/', $atts_string, $src_match)) {
                $atts['src'] = $src_match[1];
            }
            return dcmp_shortcode_ppt($atts);
        }
    }
    return $content;
}

// 测试用的PDF数据
$test_pdf_url = 'data:application/pdf;base64,JVBERi0xLjMKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL0xlbmd0aCA5NTIKL0ZpbHRlciAvRmxhdGVEZWNvZGUKPj4Kc3RyZWFtCnicY2BgYGAYxYABAAoAAQplbmRzdHJlYW0KZW5kb2JqCjEgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL091dGxpbmVzIDIgMCBSCi9QYWdlcyAzIDAgUgo+PgplbmRvYmoKMiAwIG9iago8PAovVHlwZSAvT3V0bGluZXMKL0NvdW50IDAKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9Db3VudCAxCi9LaWRzIFs0IDAgUl0KPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAzIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSAiIDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjUgMCBvYmoKPDwKL0xlbmd0aCA0NAo+PgpzdHJlYW0KQlQKL0YxIDEyIFRmCjcyIDcyMCBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKNiAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9IZWx2ZXRpY2EKPj4KZW5kb2JqCnhyZWYKMCA3CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDA2MyAwMDAwMCBuIAowMDAwMDAwMTI0IDAwMDAwIG4gCjAwMDAwMDAxODEgMDAwMDAgbiAKMDAwMDAwMDIzOCAwMDAwMCBuIAowMDAwMDAwMzk0IDAwMDAwIG4gCjAwMDAwMDA0ODggMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA3Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo1ODUKJSVFT0Y=';

// 生成测试短代码结果
$shortcode_result = do_shortcode('[dc_ppt src="' . $test_pdf_url . '"]');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>移动端PDF修复测试 - Docker WordPress</title>
    <link rel="stylesheet" href="plugins/dc-media-protect/assets/css/style.css" type="text/css">
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #f1f1f1; 
            line-height: 1.6;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .status-box {
            background: #e7f3ff; 
            padding: 20px; 
            border-radius: 5px; 
            margin: 20px 0;
            border-left: 4px solid #007cba;
        }
        .device-info {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            font-family: monospace;
            font-size: 14px;
        }
        .test-area {
            border: 2px solid #007cba;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            background: #fafafa;
        }
        .debug-section {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        h1 { color: #333; text-align: center; }
        h2 { color: #007cba; border-bottom: 2px solid #007cba; padding-bottom: 5px; }
        .highlight { background: #ffffcc; padding: 2px 4px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 移动端PDF显示修复测试</h1>
        <p style="text-align:center; color:#666;">Docker WordPress 环境测试</p>
        
        <div class="status-box">
            <h3>🔍 环境检测结果</h3>
            <div class="device-info">
                <strong>运行环境:</strong> Docker WordPress (端口8080)<br>
                <strong>当前设备:</strong> <?php echo wp_is_mobile() ? '<span class="highlight">移动设备</span>' : '桌面设备'; ?><br>
                <strong>User Agent:</strong> <?php echo esc_html($_SERVER['HTTP_USER_AGENT'] ?? '未知'); ?><br>
                <strong>移动端PDF函数:</strong> <?php echo function_exists('dcmp_generate_mobile_pdf_viewer') ? '<span class="success">✅ 可用</span>' : '<span class="error">❌ 不可用</span>'; ?><br>
                <strong>测试时间:</strong> <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>

        <div class="debug-section">
            <h3>📋 设备类型详细分析</h3>
            <?php
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $is_mobile = wp_is_mobile();
            $is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
            $is_android = strpos($user_agent, 'Android') !== false;
            $is_safari = strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false;
            $is_chrome = strpos($user_agent, 'Chrome') !== false;
            ?>
            <ul>
                <li><strong>移动设备:</strong> <?php echo $is_mobile ? '是' : '否'; ?></li>
                <li><strong>iOS设备:</strong> <?php echo $is_ios ? '是' : '否'; ?></li>
                <li><strong>Android设备:</strong> <?php echo $is_android ? '是' : '否'; ?></li>
                <li><strong>Safari浏览器:</strong> <?php echo $is_safari ? '是' : '否'; ?></li>
                <li><strong>Chrome浏览器:</strong> <?php echo $is_chrome ? '是' : '否'; ?></li>
            </ul>
            
            <p><strong>预期行为:</strong></p>
            <ul>
                <?php if ($is_ios && $is_safari): ?>
                    <li class="success">✅ iOS Safari - 应显示原生PDF查看器界面</li>
                <?php elseif ($is_android): ?>
                    <li class="success">✅ Android设备 - 应显示PDF.js渲染界面</li>
                <?php elseif ($is_mobile): ?>
                    <li class="success">✅ 其他移动设备 - 应显示通用查看器</li>
                <?php else: ?>
                    <li>📱 桌面设备 - 应显示传统iframe</li>
                <?php endif; ?>
            </ul>
        </div>

        <h2>🧪 短代码测试</h2>
        <p><strong>测试短代码:</strong> <code>[dc_ppt src="data:application/pdf..."]</code></p>
        
        <div class="test-area">
            <h4>输出结果:</h4>
            <?php echo $shortcode_result; ?>
        </div>

        <div class="debug-section">
            <h3>🔧 故障排除</h3>
            <p>如果PDF仍然无法显示，请检查:</p>
            <ol>
                <li><strong>WordPress插件状态:</strong> 访问 <code>http://localhost:8080/wp-admin/plugins.php</code> 确保插件已激活</li>
                <li><strong>Docker容器状态:</strong> 运行 <code>docker-compose ps</code> 检查容器是否正常运行</li>
                <li><strong>浏览器控制台:</strong> 按F12查看是否有JavaScript错误</li>
                <li><strong>网络访问:</strong> 确保能访问 <code>http://localhost:8080</code></li>
            </ol>
            
            <h4>快速修复命令:</h4>
            <pre style="background:#f8f9fa; padding:10px; border-radius:3px; overflow-x:auto;">
# 重启Docker容器
cd /home/guci/congqing/website
docker-compose down && docker-compose up -d

# 检查容器状态
docker-compose ps

# 访问WordPress管理后台
# http://localhost:8080/wp-admin/
            </pre>
        </div>

        <div class="status-box">
            <h3>📱 移动端测试建议</h3>
            <p>要在手机上测试，请:</p>
            <ol>
                <li>确保手机和电脑在同一网络</li>
                <li>获取电脑的IP地址: <code>ip addr show</code></li>
                <li>在手机浏览器访问: <code>http://[电脑IP]:8080/wp-content/mobile-debug-test.php</code></li>
                <li>或者使用手机访问: <code>http://localhost:8080/wp-content/mobile-debug-test.php</code> (如果支持)</li>
            </ol>
        </div>
    </div>
    
    <script src="plugins/dc-media-protect/assets/js/frontend.js" type="text/javascript"></script>
    <script>
        // 额外的调试信息
        console.log('移动端PDF测试页面已加载');
        console.log('User Agent:', navigator.userAgent);
        console.log('Screen size:', window.screen.width + 'x' + window.screen.height);
        console.log('Viewport size:', window.innerWidth + 'x' + window.innerHeight);
        
        // 检查是否有PDF.js相关错误
        window.addEventListener('error', function(e) {
            console.error('页面错误:', e.error);
        });
    </script>
</body>
</html> 