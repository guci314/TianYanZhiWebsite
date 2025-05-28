<?php
/**
 * 增强PDF显示功能测试页面
 * 集成PDF.js Viewer + 水印保护 + 防下载功能
 */

// 引入WordPress环境
require_once 'wp-load.php';

// 获取测试参数
$test_mode = $_GET['mode'] ?? 'auto';
$pdf_file = $_GET['pdf'] ?? '/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>增强PDF显示功能测试</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fafafa;
        }
        .btn {
            display: inline-block;
            background: #0073aa;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
            font-size: 14px;
        }
        .btn:hover {
            background: #005a87;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
        .success-box {
            background: #e8f5e8;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 增强PDF显示功能测试</h1>
        
        <div class="info-box">
            <h3>📋 功能说明</h3>
            <p>这个测试页面验证了dc-media-protect插件的增强功能：</p>
            <ul>
                <li><strong>PDF.js集成</strong>：使用PDF.js Viewer插件提供更好的PDF显示效果</li>
                <li><strong>水印保护</strong>：在PDF上叠加水印，防止截图和复制</li>
                <li><strong>防下载功能</strong>：禁用下载、打印、右键菜单等功能</li>
                <li><strong>移动端优化</strong>：针对不同移动浏览器提供优化方案</li>
            </ul>
        </div>

        <?php
        // 检查PDF.js Viewer插件是否存在
        $pdfjs_exists = file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php');
        
        if ($pdfjs_exists) {
            echo '<div class="success-box">';
            echo '<h3>✅ PDF.js Viewer插件已安装</h3>';
            echo '<p>系统将使用PDF.js Viewer提供更好的PDF显示效果，同时保留水印和防下载功能。</p>';
            echo '</div>';
        } else {
            echo '<div class="warning-box">';
            echo '<h3>⚠️ PDF.js Viewer插件未找到</h3>';
            echo '<p>系统将使用备用方案显示PDF，功能可能受限。建议安装PDF.js Viewer插件以获得最佳体验。</p>';
            echo '</div>';
        }
        ?>

        <div class="test-section">
            <h3>🎯 测试选项</h3>
            <p>选择不同的测试模式：</p>
            <a href="?mode=auto&pdf=<?php echo urlencode($pdf_file); ?>" class="btn">🔄 自动检测模式</a>
            <a href="?mode=mobile&pdf=<?php echo urlencode($pdf_file); ?>" class="btn">📱 模拟移动设备</a>
            <a href="?mode=wechat&pdf=<?php echo urlencode($pdf_file); ?>" class="btn">💬 模拟微信浏览器</a>
            <a href="?mode=desktop&pdf=<?php echo urlencode($pdf_file); ?>" class="btn">🖥️ 桌面模式</a>
        </div>

        <div class="test-section">
            <h3>📄 PDF显示测试</h3>
            <p><strong>当前模式：</strong> <?php echo esc_html($test_mode); ?></p>
            <p><strong>PDF文件：</strong> <?php echo esc_html($pdf_file); ?></p>
            
            <?php
            // 根据测试模式模拟不同的用户代理
            switch ($test_mode) {
                case 'mobile':
                    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1';
                    break;
                case 'wechat':
                    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 MicroMessenger/8.0.0(0x18000029) NetType/WIFI Language/zh_CN';
                    break;
                case 'desktop':
                    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
                    break;
            }
            
            // 使用dc_ppt短代码显示PDF
            echo '<div style="margin: 20px 0; border: 2px solid #007cba; border-radius: 8px; padding: 10px;">';
            echo do_shortcode('[dc_ppt src="' . $pdf_file . '" width="100%" height="500"]');
            echo '</div>';
            ?>
        </div>

        <div class="test-section">
            <h3>🔍 设备检测信息</h3>
            <?php
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $is_mobile = function_exists('dcmp_is_mobile_device') ? dcmp_is_mobile_device() : wp_is_mobile();
            $is_wechat = function_exists('dcmp_is_wechat_browser') ? dcmp_is_wechat_browser() : false;
            $is_qq = function_exists('dcmp_is_qq_browser') ? dcmp_is_qq_browser() : false;
            ?>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #f9f9f9;">
                    <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">检测项目</td>
                    <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">结果</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">移动设备</td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo $is_mobile ? '✅ 是' : '❌ 否'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">微信浏览器</td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo $is_wechat ? '✅ 是' : '❌ 否'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">QQ浏览器</td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo $is_qq ? '✅ 是' : '❌ 否'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">PDF.js插件</td>
                    <td style="padding: 8px; border: 1px solid #ddd;"><?php echo $pdfjs_exists ? '✅ 已安装' : '❌ 未安装'; ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;">User Agent</td>
                    <td style="padding: 8px; border: 1px solid #ddd; font-size: 12px; word-break: break-all;"><?php echo esc_html($user_agent); ?></td>
                </tr>
            </table>
        </div>

        <div class="test-section">
            <h3>🛠️ 功能验证</h3>
            <p>请验证以下功能是否正常工作：</p>
            <ul>
                <li>✅ PDF能否正常显示</li>
                <li>✅ 水印是否显示在PDF上方</li>
                <li>✅ 右键菜单是否被禁用</li>
                <li>✅ 下载按钮是否被隐藏</li>
                <li>✅ 打印按钮是否被禁用</li>
                <li>✅ 移动端是否有优化显示</li>
            </ul>
        </div>

        <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h3>🔗 快速链接</h3>
            <a href="http://192.168.196.90:8080/" class="btn">🏠 返回首页</a>
            <a href="http://192.168.196.90:8080/wp-admin/" class="btn">⚙️ WordPress管理</a>
            <a href="?pdf=/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf" class="btn">📄 测试其他PDF</a>
        </div>
    </div>
</body>
</html> 