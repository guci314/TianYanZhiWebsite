<?php
// 全屏按钮调试页面
// 尝试不同的wp-load.php路径
if (file_exists('wp-load.php')) {
    require_once('wp-load.php');
} elseif (file_exists('./wp-load.php')) {
    require_once('./wp-load.php');
} elseif (file_exists('../wp-load.php')) {
    require_once('../wp-load.php');
} else {
    // 手动查找WordPress
    $wp_path = dirname(__FILE__);
    while ($wp_path && !file_exists($wp_path . '/wp-load.php')) {
        $wp_path = dirname($wp_path);
        if ($wp_path === '/') break;
    }
    if (file_exists($wp_path . '/wp-load.php')) {
        require_once($wp_path . '/wp-load.php');
    } else {
        die('找不到WordPress安装路径');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>全屏按钮调试</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-section { background: #f0f0f0; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
        .test-result { background: #e7f3ff; padding: 10px; margin: 5px 0; border-left: 4px solid #007cba; }
    </style>
</head>
<body>
    <h1>🔍 DC PPT 全屏按钮调试页面</h1>
    
    <div class="debug-section">
        <h3>测试环境信息</h3>
        <p><strong>当前时间：</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        <p><strong>WordPress版本：</strong> <?php echo get_bloginfo('version'); ?></p>
        <p><strong>是否移动设备：</strong> <?php echo wp_is_mobile() ? '是' : '否'; ?></p>
        <p><strong>用户代理：</strong> <?php echo esc_html($_SERVER['HTTP_USER_AGENT'] ?? '未知'); ?></p>
    </div>

    <div class="debug-section">
        <h3>测试PDF URL处理</h3>
        <?php
        $test_url = "http://192.168.29.90:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf";
        $home_url = home_url();
        
        echo "<p><strong>测试URL：</strong> $test_url</p>";
        echo "<p><strong>Home URL：</strong> $home_url</p>";
        
        $is_local_checks = [
            'home_url匹配' => strpos($test_url, $home_url) === 0,
            'data:匹配' => strpos($test_url, 'data:') === 0,
            '/wp-content/匹配' => strpos($test_url, '/wp-content/') === 0,
            'wp-content/开头' => strpos($test_url, 'wp-content/') === 0,
            'uploads路径匹配' => strpos($test_url, 'wp-content/uploads/') !== false,
        ];
        
        foreach ($is_local_checks as $check => $result) {
            echo "<div class='test-result'>$check: " . ($result ? '✅ 匹配' : '❌ 不匹配') . "</div>";
        }
        
        $is_local = array_reduce($is_local_checks, function($carry, $item) { return $carry || $item; }, false);
        echo "<p><strong>最终判断是否本地文件：</strong> " . ($is_local ? '✅ 是' : '❌ 否') . "</p>";
        ?>
    </div>

    <div class="debug-section">
        <h3>PDF.js插件检查</h3>
        <?php
        $pdfjs_plugin_path = WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php';
        $pdfjs_plugin_exists = file_exists($pdfjs_plugin_path);
        echo "<p><strong>插件路径：</strong> $pdfjs_plugin_path</p>";
        echo "<p><strong>插件是否存在：</strong> " . ($pdfjs_plugin_exists ? '✅ 存在' : '❌ 不存在') . "</p>";
        
        if ($pdfjs_plugin_exists) {
            echo "<div class='test-result'>✅ PDF.js插件存在，应该显示全屏按钮</div>";
        } else {
            echo "<div class='test-result'>❌ PDF.js插件不存在，将使用备用方案</div>";
        }
        ?>
    </div>

    <div class="debug-section">
        <h3>实际短码测试</h3>
        <p>以下是实际的短码输出：</p>
        <div style="border: 2px solid #ff0000; padding: 10px; background: #fff;">
            <?php
            // 直接调用短码函数进行测试
            echo do_shortcode('[dc_ppt src="' . $test_url . '" width="600" height="400"]');
            ?>
        </div>
    </div>

    <div class="debug-section">
        <h3>页面源码检查</h3>
        <p>请使用浏览器开发者工具 (F12) 检查：</p>
        <ol>
            <li>查看HTML源码中是否有 <code>dcmp-fullscreen-toolbar</code> 元素</li>
            <li>查看控制台是否有JavaScript错误</li>
            <li>查看网络面板PDF是否加载成功</li>
        </ol>
    </div>

    <script>
    console.log("=== 全屏按钮调试页面加载 ===");
    console.log("查找全屏工具栏...");
    
    setTimeout(function() {
        const toolbars = document.querySelectorAll('.dcmp-fullscreen-toolbar');
        console.log("找到工具栏数量:", toolbars.length);
        
        if (toolbars.length > 0) {
            toolbars.forEach((toolbar, index) => {
                console.log("工具栏 " + (index + 1) + ":");
                console.log("  - 元素:", toolbar);
                console.log("  - 显示状态:", window.getComputedStyle(toolbar).display);
                console.log("  - 可见性:", window.getComputedStyle(toolbar).visibility);
                console.log("  - 位置:", toolbar.getBoundingClientRect());
                
                const buttons = toolbar.querySelectorAll('button');
                console.log("  - 按钮数量:", buttons.length);
                buttons.forEach((btn, btnIndex) => {
                    console.log("    按钮 " + (btnIndex + 1) + ":", btn.textContent);
                });
            });
        } else {
            console.error("❌ 没有找到全屏工具栏！");
        }
        
        // 检查PDF容器
        const containers = document.querySelectorAll('.dcmp-pdf-container');
        console.log("找到PDF容器数量:", containers.length);
        
        // 检查是否有HTML注释（调试信息）
        const comments = document.createNodeIterator(
            document.body,
            NodeFilter.SHOW_COMMENT,
            null,
            false
        );
        
        let comment;
        const debugComments = [];
        while (comment = comments.nextNode()) {
            if (comment.textContent.includes('调试')) {
                debugComments.push(comment.textContent);
            }
        }
        
        console.log("调试注释信息:", debugComments);
        
    }, 1000);
    </script>
</body>
</html>