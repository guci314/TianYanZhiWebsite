<?php
// WordPress PDF测试页面
// 将此文件放在WordPress根目录，然后访问 yoursite.com/wordpress-test.php

// 加载WordPress环境
$wp_root = dirname(__FILE__);
$wp_config_found = false;

// 查找wp-config.php文件
$search_paths = [
    $wp_root,
    dirname($wp_root),
    dirname(dirname($wp_root)),
    '/var/www/html',
    '/home/guci/congqing/website'
];

foreach ($search_paths as $path) {
    if (file_exists($path . '/wp-config.php')) {
        require_once $path . '/wp-config.php';
        require_once $path . '/wp-load.php';
        $wp_config_found = true;
        break;
    }
}

if (!$wp_config_found) {
    die('WordPress配置文件未找到，请确保此文件在WordPress网站目录中');
}

// 检查插件是否激活
if (!function_exists('dcmp_shortcode_ppt')) {
    die('DC Media Protect 插件未激活，请先激活插件');
}

// 测试PDF URL
$test_pdf_url = 'data:application/pdf;base64,JVBERi0xLjMKJcTl8uXrp/Og0MTGCjQgMCBvYmoKPDwKL0xlbmd0aCA5NTIKL0ZpbHRlciAvRmxhdGVEZWNvZGUKPj4Kc3RyZWFtCnicY2BgYGAYxYABAAoAAQplbmRzdHJlYW0KZW5kb2JqCjEgMCBvYmoKPDwKL1R5cGUgL0NhdGFsb2cKL091dGxpbmVzIDIgMCBSCi9QYWdlcyAzIDAgUgo+PgplbmRvYmoKMiAwIG9iago8PAovVHlwZSAvT3V0bGluZXMKL0NvdW50IDAKPD4KZW5kb2JqCjMgMCBvYmoKPDwKL1R5cGUgL1BhZ2VzCi9Db3VudCAxCi9LaWRzIFs0IDAgUl0KPj4KZW5kb2JqCjQgMCBvYmoKPDwKL1R5cGUgL1BhZ2UKL1BhcmVudCAzIDAgUgovUmVzb3VyY2VzIDw8Ci9Gb250IDw8Ci9GMSAiIDAgUgo+Pgo+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKPj4KZW5kb2JqCjUgMCBvYmoKPDwKL0xlbmd0aCA0NAo+PgpzdHJlYW0KQlQKL0YxIDEyIFRmCjcyIDcyMCBUZAooVGVzdCBQREYpIFRqCkVUCmVuZHN0cmVhbQplbmRvYmoKNiAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvVHlwZTEKL0Jhc2VGb250IC9IZWx2ZXRpY2EKPj4KZW5kb2JqCnhyZWYKMCA3CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDA2MyAwMDAwMCBuIAowMDAwMDAwMTI0IDAwMDAwIG4gCjAwMDAwMDAxODEgMDAwMDAgbiAKMDAwMDAwMDIzOCAwMDAwMCBuIAowMDAwMDAwMzk0IDAwMDAwIG4gCjAwMDAwMDA0ODggMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSA3Ci9Sb290IDEgMCBSCj4+CnN0YXJ0eHJlZgo1ODUKJSVFT0Y=';

// 测试短代码
$shortcode_test = do_shortcode('[dc_ppt src="' . $test_pdf_url . '"]');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress PDF短代码测试</title>
    <?php wp_head(); ?>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            background: #f5f5f5; 
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            padding: 20px; 
            border-radius: 8px; 
        }
        .device-info {
            background: #e7f3ff; 
            padding: 15px; 
            border-radius: 5px; 
            margin-bottom: 20px;
            border-left: 4px solid #007cba;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fafafa;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 WordPress PDF短代码测试</h1>
        
        <div class="device-info">
            <strong>WordPress环境:</strong> ✅ 已连接<br>
            <strong>插件状态:</strong> <?php echo function_exists('dcmp_shortcode_ppt') ? '✅ 已激活' : '❌ 未激活'; ?><br>
            <strong>移动端PDF函数:</strong> <?php echo function_exists('dcmp_generate_mobile_pdf_viewer') ? '✅ 可用' : '❌ 不可用'; ?><br>
            <strong>当前设备:</strong> <?php echo wp_is_mobile() ? '移动设备' : '桌面设备'; ?><br>
            <strong>User Agent:</strong> <small><?php echo esc_html($_SERVER['HTTP_USER_AGENT'] ?? '未知'); ?></small>
        </div>
        
        <div class="test-section">
            <h2>短代码测试结果</h2>
            <p><strong>测试短代码:</strong> <code>[dc_ppt src="data:application/pdf..."]</code></p>
            
            <div style="border: 2px solid #007cba; padding: 10px; background: white;">
                <?php echo $shortcode_test; ?>
            </div>
        </div>
        
        <div class="test-section">
            <h2>函数可用性检查</h2>
            <ul>
                <li>dcmp_shortcode_ppt: <?php echo function_exists('dcmp_shortcode_ppt') ? '✅' : '❌'; ?></li>
                <li>dcmp_generate_mobile_pdf_viewer: <?php echo function_exists('dcmp_generate_mobile_pdf_viewer') ? '✅' : '❌'; ?></li>
                <li>dcmp_detect_device_type: <?php echo function_exists('dcmp_detect_device_type') ? '✅' : '❌'; ?></li>
                <li>wp_is_mobile: <?php echo function_exists('wp_is_mobile') ? '✅' : '❌'; ?></li>
            </ul>
        </div>
        
        <div class="test-section">
            <h2>调试信息</h2>
            <p>如果在移动设备上查看时PDF仍然无法显示，请检查以下内容：</p>
            <ol>
                <li>确保插件已正确激活</li>
                <li>清除浏览器缓存</li>
                <li>检查控制台是否有JavaScript错误</li>
                <li>尝试在不同的移动浏览器中测试</li>
            </ol>
            
            <p><strong>下一步测试:</strong></p>
            <p>在手机上访问此页面，应该看到针对您的设备优化的PDF查看器界面。</p>
        </div>
    </div>
    
    <?php wp_footer(); ?>
</body>
</html> 