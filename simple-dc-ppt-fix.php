<?php
/**
 * 简单的DC PPT修复测试
 * 直接在浏览器中访问这个文件: http://your-site.com/simple-dc-ppt-fix.php
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DC PPT 全屏修复测试</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #f0f0f0; 
        }
        .test-container { 
            background: white; 
            padding: 20px; 
            border-radius: 5px; 
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .pdf-container {
            position: relative;
            width: 800px;
            height: 600px;
            border: 2px solid #0073aa;
            background: #f9f9f9;
            margin: 20px 0;
        }
        .fullscreen-toolbar {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 9999;
            background: rgba(0, 115, 170, 0.9);
            padding: 8px;
            border-radius: 5px;
            display: flex;
            gap: 5px;
        }
        .fullscreen-btn {
            background: white;
            color: #0073aa;
            border: none;
            padding: 8px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .fullscreen-btn:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

<div class="test-container">
    <h1>🔧 DC PPT 全屏按钮修复测试</h1>
    <p><strong>说明：</strong>这个测试显示了修复后dc_ppt短码应该的样子。</p>
</div>

<div class="test-container">
    <h2>✅ 修复后的效果预览</h2>
    <p>下面是带全屏按钮的PDF查看器：</p>
    
    <div class="pdf-container">
        <iframe 
            id="test-pdf" 
            src="/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.html?file=<?php echo urlencode('/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf'); ?>" 
            width="800" 
            height="600" 
            style="border: none;"
            allowfullscreen>
        </iframe>
        
        <!-- 全屏工具栏 -->
        <div class="fullscreen-toolbar">
            <button class="fullscreen-btn" onclick="enterFullscreen()">
                <span>⛶</span>
                <span>全屏</span>
            </button>
            <button class="fullscreen-btn" onclick="openNewWindow()" style="background: rgba(255,255,255,0.9); color: #333;">
                <span>↗</span>
                <span>新窗口</span>
            </button>
        </div>
    </div>
</div>

<div class="test-container">
    <h2>🛠️ 如果您能看到上面的按钮</h2>
    <p>说明修复方案是有效的。现在需要应用到您的WordPress中：</p>
    
    <h3>方法1：直接替换短码函数</h3>
    <p>将以下代码复制到您的主题的 <code>functions.php</code> 文件末尾：</p>
    
    <textarea rows="20" cols="100" style="width: 100%; font-family: monospace; font-size: 12px;">
// 修复dc_ppt短码的全屏按钮
add_action('init', 'fix_dc_ppt_fullscreen_button');

function fix_dc_ppt_fullscreen_button() {
    // 移除原始短码
    remove_shortcode('dc_ppt');
    
    // 添加修复版本
    add_shortcode('dc_ppt', 'fixed_dcmp_shortcode_ppt');
}

function fixed_dcmp_shortcode_ppt($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    
    $is_mobile = wp_is_mobile();
    $w = $width > 0 ? $width : ($is_mobile ? 350 : 800);
    $h = $height > 0 ? $height : ($is_mobile ? 500 : 600);
    $is_local = (strpos($src, home_url()) === 0);
    
    if (!$is_local) {
        return '<div style="width:'.$w.'px; height:'.$h.'px; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; flex-direction:column; background:#f9f9f9;">
            <p><strong>PDF文档预览</strong><br><small>外部PDF链接可能无法直接预览</small></p>
            <a href="'.$src.'" target="_blank" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:3px;">📄 打开PDF文档</a>
        </div>';
    }
    
    $unique_id = 'dcmp_pdf_' . md5($src . time());
    $pdfjs_url = plugins_url('../../pdfjs-viewer-shortcode/pdfjs/web/viewer.html', __FILE__);
    $viewer_url = $pdfjs_url . '?file=' . urlencode($src);
    
    return '
    <div style="position: relative; width: '.$w.'px; height: '.$h.'px; border: 1px solid #ddd; background: #f9f9f9;">
        <iframe id="'.$unique_id.'" src="'.$viewer_url.'" width="'.$w.'" height="'.$h.'" style="border: none;" allowfullscreen></iframe>
        
        <div style="position: absolute; top: 10px; right: 10px; z-index: 9999; background: rgba(0, 115, 170, 0.9); padding: 8px; border-radius: 5px; display: flex; gap: 5px;">
            <button onclick="
                const iframe = document.getElementById(\''.$unique_id.'\');
                if (iframe.requestFullscreen) {
                    iframe.requestFullscreen();
                } else {
                    window.open(iframe.src, \'_blank\', \'width=\' + screen.width + \',height=\' + screen.height);
                }
            " style="background: white; color: #0073aa; border: none; padding: 8px 12px; border-radius: 3px; cursor: pointer; font-size: 13px; font-weight: bold;">
                ⛶ 全屏
            </button>
            <button onclick="window.open(\''.$viewer_url.'\', \'_blank\')" style="background: rgba(255,255,255,0.9); color: #333; border: none; padding: 8px 12px; border-radius: 3px; cursor: pointer; font-size: 13px; font-weight: bold;">
                ↗ 新窗口
            </button>
        </div>
        
        <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px; pointer-events: none;">数字中国</div>
    </div>';
}
    </textarea>
</div>

<script>
function enterFullscreen() {
    const iframe = document.getElementById('test-pdf');
    if (iframe.requestFullscreen) {
        iframe.requestFullscreen().then(() => {
            console.log('全屏成功');
        }).catch(err => {
            console.log('全屏失败，打开新窗口', err);
            openNewWindow();
        });
    } else if (iframe.webkitRequestFullscreen) {
        iframe.webkitRequestFullscreen();
    } else {
        openNewWindow();
    }
}

function openNewWindow() {
    const iframe = document.getElementById('test-pdf');
    window.open(iframe.src, '_blank', 'width=' + screen.width + ',height=' + screen.height);
}

// 全屏事件监听
document.addEventListener('fullscreenchange', function() {
    const fullscreenElement = document.fullscreenElement;
    if (fullscreenElement && fullscreenElement.tagName === 'IFRAME') {
        fullscreenElement.style.width = '100vw';
        fullscreenElement.style.height = '100vh';
        fullscreenElement.style.border = 'none';
        fullscreenElement.style.position = 'fixed';
        fullscreenElement.style.top = '0';
        fullscreenElement.style.left = '0';
        fullscreenElement.style.zIndex = '9999';
    }
});

console.log('DC PPT 全屏测试页面已加载');
</script>

</body>
</html> 