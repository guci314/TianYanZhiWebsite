<?php
/** ==== Shortcode ==== */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

// tell WordPress to register the pdfjs-viewer shortcode.
add_shortcode( 'pdfjs-viewer', 'pdfjs_handler_with_debug' );

// 调试版本的处理函数
function pdfjs_handler_with_debug( $incoming_from_post ) {
    // 强制显示调试信息
    return '
    <div style="background: #ff0000; color: white; padding: 20px; margin: 15px 0; border: 5px solid #ffff00; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.8;">
        <strong>🚨 PDFJS插件调试模式 🚨</strong><br>
        <strong>成功拦截了pdfjs-viewer短码！</strong><br>
        <strong>参数:</strong> ' . print_r($incoming_from_post, true) . '<br>
        <strong>时间:</strong> ' . date('Y-m-d H:i:s') . '
    </div>
    
    <div style="background: #28a745; padding: 20px; margin: 15px 0; border-radius: 10px; text-align: center;">
        <h2 style="color: white; margin: 0 0 20px 0;">📱 PDFJS插件全屏测试</h2>
        
        <button onclick="testPdfjsFullscreen()" 
                style="background: #007cba; color: white; border: none; padding: 20px 30px; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: bold; margin: 10px;">
            🔳 PDFJS全屏测试
        </button>
        
        <button onclick="alert(\'PDFJS插件按钮工作！\')" 
                style="background: #ffc107; color: black; border: none; padding: 20px 30px; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: bold; margin: 10px;">
            ✅ PDFJS测试
        </button>
    </div>
    
    <script>
    function testPdfjsFullscreen() {
        alert("✅ PDFJS插件全屏功能测试！\\n\\n这证明我们可以在这里添加真正的全屏功能");
        console.log("🚀 PDFJS插件全屏测试");
    }
    console.log("🔍 PDFJS插件调试模式激活");
    </script>';
}

/**
 * Get the embed info from the shortcode.
 */
function pdfjs_handler( $incoming_from_post ) {

	// do not run this code on the admin screens.
	if ( is_admin() || defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}

	// set defaults.
	$incoming_from_post = shortcode_atts(
		array(
			'url'               => plugin_dir_url( __DIR__ ) . '/pdf-loading-error.pdf',
			'viewer_height'     => '800px',
			'viewer_width'      => '100%',
			'fullscreen'        => 'true',
			'fullscreen_text'   => 'View Fullscreen',
			'fullscreen_target' => 'false',
			'download'          => 'true',
			'print'             => 'true',
			'openfile'          => 'false',
			'zoom'              => 'auto',
			'attachment_id'     => '',
		),
		$incoming_from_post
	);

	// send back text to replace shortcode in post.
	return pdfjs_generator( $incoming_from_post );
}
