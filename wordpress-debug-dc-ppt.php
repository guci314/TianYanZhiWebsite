<?php
/**
 * WordPress DC PPT调试代码
 * 将此代码添加到functions.php的末尾，或者作为临时调试插件使用
 */

// 添加调试短代码
add_shortcode('debug_dc_ppt', 'debug_dc_ppt_function');

function debug_dc_ppt_function($atts) {
    // 获取当前的dc_ppt实现
    $test_attrs = array(
        'src' => home_url('/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf'),
        'width' => 600,
        'height' => 400
    );
    
    // 生成调试输出
    $output = dcmp_shortcode_ppt($test_attrs);
    
    $debug_html = '
    <div style="background: #fff; border: 2px solid #ff0000; padding: 20px; margin: 20px 0; border-radius: 5px;">
        <h3 style="color: #ff0000; margin-top: 0;">🔍 DC PPT 调试信息</h3>
        
        <h4>📝 调用参数:</h4>
        <pre style="background: #f0f0f0; padding: 10px; border-radius: 3px; overflow: auto;">' . print_r($test_attrs, true) . '</pre>
        
        <h4>🎯 生成的HTML代码:</h4>
        <textarea rows="15" style="width: 100%; font-family: monospace; font-size: 12px; border: 1px solid #ccc;">' . htmlspecialchars($output) . '</textarea>
        
        <h4>🖼️ 实际渲染效果:</h4>
        <div style="border: 2px dashed #ff0000; padding: 10px; background: #fffacd;">
            ' . $output . '
        </div>
        
        <h4>✅ 检查清单:</h4>
        <ul style="color: #333;">
            <li>□ 右上角是否有半透明的工具栏？</li>
            <li>□ 工具栏中是否有两个按钮？</li>
            <li>□ 是否有蓝色的"全屏"按钮？</li>
            <li>□ 是否有灰色的"新窗口"按钮？</li>
            <li>□ 按钮上是否显示SVG图标？</li>
        </ul>
        
        <script>
        console.log("=== DC PPT 调试信息 ===");
        console.log("生成的HTML:", ' . json_encode($output) . ');
        
        // 查找全屏按钮
        setTimeout(function() {
            const buttons = document.querySelectorAll("button[onclick*=\"dcmpEnterFullscreen\"]");
            console.log("找到全屏按钮数量:", buttons.length);
            
            buttons.forEach((btn, index) => {
                console.log("按钮 " + (index + 1) + ":", btn);
                console.log("按钮文本:", btn.textContent);
                console.log("onclick属性:", btn.getAttribute("onclick"));
                
                // 给按钮添加明显的标记
                btn.style.border = "3px solid red";
                btn.style.animation = "dcmp-debug-blink 1s infinite";
            });
            
            // 添加CSS动画
            if (!document.querySelector("#dcmp-debug-style")) {
                const style = document.createElement("style");
                style.id = "dcmp-debug-style";
                style.textContent = `
                    @keyframes dcmp-debug-blink {
                        0% { border-color: red; }
                        50% { border-color: yellow; }
                        100% { border-color: red; }
                    }
                `;
                document.head.appendChild(style);
            }
        }, 1000);
        </script>
    </div>';
    
    return $debug_html;
}

// 临时修改dc_ppt短代码以强制显示按钮
add_filter('init', 'debug_force_dc_ppt_buttons');

function debug_force_dc_ppt_buttons() {
    // 移除原来的短代码
    remove_shortcode('dc_ppt');
    
    // 添加调试版本的短代码
    add_shortcode('dc_ppt', 'debug_dcmp_shortcode_ppt');
}

function debug_dcmp_shortcode_ppt($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '❌ DC PPT: 没有提供PDF源文件';
    
    // 检测移动设备
    $is_mobile = wp_is_mobile();
    
    $w = $width > 0 ? $width : ($is_mobile ? 350 : 800);
    $h = $height > 0 ? $height : ($is_mobile ? 500 : 600);
    
    // 检查是否为本地文件
    $is_local = (strpos($src, home_url()) === 0);
    
    $viewer_html = '';
    $unique_id = 'dcmp_pdf_' . md5($src . time());
    
    // 强制显示调试信息
    $debug_info = '
    <div style="background: #e7f3ff; border: 1px solid #0073aa; padding: 10px; margin-bottom: 10px; border-radius: 3px; font-size: 12px;">
        <strong>🔍 调试信息:</strong><br>
        PDF源: ' . $src . '<br>
        是否本地文件: ' . ($is_local ? '是' : '否') . '<br>
        唯一ID: ' . $unique_id . '<br>
        尺寸: ' . $w . 'x' . $h . 'px
    </div>';
    
    if ($is_local) {
        // 本地PDF文件，使用pdfjs viewer
        $pdfjs_url = plugins_url('../../pdfjs-viewer-shortcode/pdfjs/web/viewer.html', __FILE__);
        $viewer_url = $pdfjs_url . '?file=' . urlencode($src);
        
        // 强制显示的全屏按钮 - 使用更明显的样式
        $viewer_html = '
        <div class="dcmp-pdf-container" style="position: relative; width: ' . $w . 'px; height: ' . $h . 'px; border: 2px solid #0073aa; background: #f9f9f9;">
            <iframe id="' . $unique_id . '" src="' . $viewer_url . '" width="' . $w . '" height="' . $h . '" style="border: none;" allowfullscreen></iframe>
            
            <!-- 强制显示的工具栏 -->
            <div style="
                position: absolute; 
                top: 5px; 
                right: 5px; 
                z-index: 10000; 
                display: flex; 
                gap: 5px; 
                background: rgba(255,0,0,0.9); 
                padding: 8px; 
                border-radius: 5px; 
                box-shadow: 0 4px 8px rgba(0,0,0,0.5);
                border: 2px solid yellow;
            ">
                <button onclick="
                    console.log(\'全屏按钮被点击\');
                    const iframe = document.getElementById(\'' . $unique_id . '\');
                    if (iframe && iframe.requestFullscreen) {
                        iframe.requestFullscreen();
                    } else {
                        window.open(\'' . $viewer_url . '\', \'_blank\');
                    }
                " style="
                    background: #0073aa; 
                    color: white; 
                    border: 2px solid white; 
                    padding: 10px 15px; 
                    border-radius: 5px; 
                    cursor: pointer; 
                    font-size: 14px; 
                    font-weight: bold;
                    display: block;
                " title="进入全屏模式">
                    🔳 全屏
                </button>
                
                <button onclick="window.open(\'' . $viewer_url . '\', \'_blank\')" style="
                    background: #666; 
                    color: white; 
                    border: 2px solid white; 
                    padding: 10px 15px; 
                    border-radius: 5px; 
                    cursor: pointer; 
                    font-size: 14px; 
                    font-weight: bold;
                    display: block;
                " title="在新窗口中打开">
                    🗗 新窗口
                </button>
            </div>
        </div>';
    } else {
        $viewer_html = '
        <div style="width:' . $w . 'px; height:' . $h . 'px; border:2px solid #ff0000; display:flex; align-items:center; justify-content:center; flex-direction:column; background:#ffe6e6;">
            <p style="margin:10px; text-align:center; color: #cc0000;">
                <strong>⚠️ 外部PDF文件</strong><br>
                <small>无法直接预览，不显示全屏按钮</small>
            </p>
            <a href="' . $src . '" target="_blank" style="background:#cc0000; color:white; padding:10px 20px; text-decoration:none; border-radius:3px;">
                📄 打开PDF文档
            </a>
        </div>';
    }
    
    $watermark = '<div class="dcmp-watermark" style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 5px 10px; border-radius: 3px; font-size: 12px;">' . esc_html(get_option('dcmp_watermark_text', '数字中国')) . '</div>';
    
    return $debug_info . '<div class="dcmp-media-container" style="position:relative;">'
        . $viewer_html
        . $watermark
        . '</div>';
}

/**
 * 使用方法:
 * 1. 将此代码添加到主题的functions.php文件末尾
 * 2. 或者创建一个临时插件文件
 * 3. 在页面或文章中使用 [debug_dc_ppt] 短代码进行调试
 * 4. 现有的 [dc_ppt] 短代码会自动使用调试版本
 */

?> 