<?php
// 短代码注册模块

// 增强的移动设备检测函数
function dcmp_is_mobile_device() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // 首先使用WordPress内置检测
    if (function_exists('wp_is_mobile') && wp_is_mobile()) {
        return true;
    }
    
    // 增强的移动设备检测
    return preg_match('/Mobile|Android|BlackBerry|iPhone|iPad|iPod|Windows Phone|webOS|Opera Mini|IEMobile|WPDesktop|Mobi|Tablet|Touch/i', $user_agent) ||
           // 检测触摸屏设备
           (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) ||
           // 检测Opera Mobile
           preg_match('/Opera.*Mini|Opera.*Mobi/i', $user_agent) ||
           // 检测特定的移动浏览器
           preg_match('/Chrome.*Mobile|Safari.*Mobile/i', $user_agent);
}

// 检测微信浏览器
function dcmp_is_wechat_browser() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return strpos($user_agent, 'MicroMessenger') !== false;
}

// 检测QQ浏览器
function dcmp_is_qq_browser() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return strpos($user_agent, 'QQBrowser') !== false || strpos($user_agent, 'MQQBrowser') !== false;
}

// 获取水印内容
function dcmp_get_watermark_text() {
    return esc_html(get_option('dcmp_watermark_text', '数字中国'));
}

// 视频短代码 [dc_video src="视频地址" width="宽度" height="高度"]
function dcmp_shortcode_video($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    $style = '';
    if ($width > 0) $style .= 'width:' . $width . 'px;';
    if ($height > 0) $style .= 'height:' . $height . 'px;';
    if (!$style) $style = 'max-width:100%;';
    $watermark = '<div class="dcmp-watermark">' . dcmp_get_watermark_text() . '</div>';
    return '<div class="dcmp-media-container" style="position:relative;">'
        . '<video src="' . $src . '" controls style="' . esc_attr($style) . '"></video>'
        . $watermark
        . '</div>';
}
add_shortcode('dc_video', 'dcmp_shortcode_video');

// PPT短代码 [dc_ppt src="PPT地址" width="宽度" height="高度"]
function dcmp_shortcode_ppt($atts) {
    $src = isset($atts['src']) ? $atts['src'] : '';  // 先不使用esc_url，避免路径被修改
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    
    // 处理相对路径，确保PDF.js能正确访问
    $processed_src = $src;
    if (strpos($src, 'wp-content/') === 0) {
        // 如果是相对路径，转换为完整的URL
        $processed_src = home_url('/' . $src);
    } elseif (strpos($src, '/wp-content/') === 0) {
        // 如果已经是绝对路径，转换为完整URL
        $processed_src = home_url($src);
    }
    
    // 现在安全地使用esc_url
    $processed_src = esc_url($processed_src);
    
    // 检测移动设备和具体设备类型
    $is_mobile = dcmp_is_mobile_device();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_ios = strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false;
    $is_android = strpos($user_agent, 'Android') !== false;
    $is_wechat = dcmp_is_wechat_browser();
    $is_qq_browser = dcmp_is_qq_browser();
    
    // 调试信息 - 在移动端显示设备检测结果
    $debug_info = '';
    if ($is_mobile) {
        $debug_info = '<!-- 调试: 移动设备检测 - iOS:' . ($is_ios ? 'Yes' : 'No') . ' Android:' . ($is_android ? 'Yes' : 'No') . ' 微信:' . ($is_wechat ? 'Yes' : 'No') . ' QQ浏览器:' . ($is_qq_browser ? 'Yes' : 'No') . ' UserAgent: ' . $user_agent . ' -->';
    }
    
    // 响应式尺寸设置
    if ($width > 0 && $height > 0) {
        $w = $width;
        $h = $height;
    } else {
        if ($is_mobile) {
            $w = '100%';
            $h = '500px';  // 增加移动端高度
        } else {
            $w = 800;
            $h = 600;
        }
    }
    
    // 检查是否为本地文件（包括data: URL和相对路径，以及同网段的WordPress上传文件）
    $home_url = home_url();
    $is_local = (strpos($processed_src, $home_url) === 0) || 
                (strpos($processed_src, 'data:') === 0) || 
                (strpos($processed_src, '/wp-content/') === 0) ||
                (strpos($src, 'wp-content/') === 0) ||  // 检查原始路径中的相对路径
                (strpos($processed_src, 'wp-content/uploads/') !== false) ||  // 包含uploads路径的URL
                (strpos($src, 'wp-content/uploads/') !== false);  // 原始路径中的uploads
    
    $viewer_html = '';
    $container_id = 'dcmp-pdf-' . uniqid();
    
    // 添加详细调试信息（包括可见的调试框）
    $debug_info .= '<!-- 调试详情: 
        原始路径=' . $src . ' 
        处理后路径=' . $processed_src . ' 
        home_url=' . $home_url . '
        是否本地=' . ($is_local ? 'Yes' : 'No') . '
        检测结果: 
        - home_url匹配: ' . (strpos($processed_src, $home_url) === 0 ? 'Yes' : 'No') . '
        - data:匹配: ' . (strpos($processed_src, 'data:') === 0 ? 'Yes' : 'No') . '
        - /wp-content/匹配: ' . (strpos($processed_src, '/wp-content/') === 0 ? 'Yes' : 'No') . '
        - 原始wp-content/匹配: ' . (strpos($src, 'wp-content/') === 0 ? 'Yes' : 'No') . '
        - uploads路径匹配: ' . (strpos($processed_src, 'wp-content/uploads/') !== false ? 'Yes' : 'No') . '
        - 原始uploads匹配: ' . (strpos($src, 'wp-content/uploads/') !== false ? 'Yes' : 'No') . '
    -->';
    
    
    if ($is_local) {
        // 检查是否有PDF.js Viewer插件
        $pdfjs_plugin_exists = file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php');
        
        if ($pdfjs_plugin_exists) {
            // 使用PDF.js Viewer插件的方式，但添加水印和防下载功能
            // 移动设备也使用PDF.js，现代移动浏览器都支持
            $watermark_text = dcmp_get_watermark_text();
            $nonce = wp_create_nonce('dcmp_pdf_viewer');
            
            // 构建PDF.js查看器URL - 使用正确的WordPress URL
            $viewer_url = home_url('/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php') . 
                         '?file=' . urlencode($processed_src) . 
                         '&attachment_id=0' .
                         '&dButton=false' .  // 禁用下载按钮
                         '&pButton=false' .  // 禁用打印按钮
                         '&oButton=false' .  // 禁用打开文件按钮
                         '&sButton=true' .   // 保留搜索按钮
                         '&pagemode=none' .
                         '&dcmp_protected=1' .  // 标记为受保护的查看器
                         '&dcmp_watermark=' . urlencode($watermark_text) .  // 传递水印文本
                         '&_wpnonce=' . $nonce;
            
            // 创建全屏功能的JavaScript字符串（用于onclick）
            $fullscreen_js = 'dcmpOpenProtectedFullscreen(\'' . esc_js($viewer_url) . '\', \'' . esc_js($watermark_text) . '\');';
            
            // 创建新窗口按钮工具栏 - 修改为受保护的全屏查看
            $protected_fullscreen_id = 'dcmp-protected-fullscreen-' . uniqid();
            
            // 移动端显示不同的提示文字
            $mobile_hint = $is_mobile ? '📱 点击跳转到全屏页面' : '💻 点击打开安全窗口';
            $button_text = $is_mobile ? '🔒 全屏查看 →' : '🔒 安全全屏查看';
            
            $fullscreen_link = '
            <div class="dcmp-fullscreen-toolbar" style="position: relative; margin-bottom: 8px; text-align: right;">
                <div style="display: inline-flex; background: rgba(40,167,69,0.15); padding: 12px; border-radius: 8px; border: 2px solid #28a745; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <button id="' . $protected_fullscreen_id . '" 
                            style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; padding: 14px 20px; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(40,167,69,0.4); transition: all 0.3s; transform: scale(1.02);"
                            onmouseover="this.style.background=\'linear-gradient(135deg, #1e7e34, #1abc9c)\'; this.style.transform=\'scale(1.05)\';"
                            onmouseout="this.style.background=\'linear-gradient(135deg, #28a745, #20c997)\'; this.style.transform=\'scale(1.02)\';"
                            title="' . esc_attr($mobile_hint) . '"
                            onclick="console.log(\'🎯 安全全屏按钮被点击了！\'); ' . $fullscreen_js . ' return false;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/>
                        </svg>
                        ' . $button_text . '
                    </button>
                    <div style="margin-left: 12px; color: #28a745; font-size: 12px; display: flex; flex-direction: column; justify-content: center;">
                        <div style="font-weight: bold;">✨ 推荐使用</div>
                        <div>' . ($is_mobile ? '移动优化' : '受保护查看') . '</div>
                    </div>
                </div>
            </div>';
            
            // 移动端添加额外的下载链接作为备用方案
            $mobile_fallback = '';
            if ($is_mobile) {
                $mobile_fallback = '
                <div class="dcmp-mobile-fallback" style="position:absolute; top:5px; left:5px; z-index:999998; background:rgba(40,167,69,0.9); color:white; padding:4px 8px; border-radius:4px; font-size:11px;">
                    <a href="' . esc_url($processed_src) . '" target="_blank" style="color:white; text-decoration:none;">📄 直接打开PDF</a>
                </div>';
            }
            
            // PDF查看器iframe
            $viewer_html = $fullscreen_link . '
            <div class="dcmp-pdf-container" style="position:relative; width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:2px solid #007cba; border-radius:8px; overflow:hidden; background:#f9f9f9; ' . ($is_mobile ? 'min-height:500px;' : '') . '">' . $mobile_fallback . '
                <iframe src="' . esc_url($viewer_url) . '" 
                        width="100%" 
                        height="100%" 
                        style="border:none; display:block; ' . ($is_mobile ? 'min-height:500px;' : '') . '" 
                        title="PDF文档查看器"
                        sandbox="allow-same-origin allow-scripts allow-forms allow-downloads allow-popups allow-popups-to-escape-sandbox"
                        oncontextmenu="return false;"
                        class="dcmp-pdf-iframe"></iframe>
                
                <div class="dcmp-watermark-overlay" style="position:absolute; top:0; left:0; right:0; bottom:0; pointer-events:none; z-index:999999 !important; background:repeating-linear-gradient(45deg, transparent, transparent 150px, rgba(0,124,186,0.03) 150px, rgba(0,124,186,0.03) 300px);">
                    <div style="position:absolute; top:15px; right:15px; background:rgba(0,124,186,0.9); color:white; padding:8px 15px; border-radius:6px; font-size:14px; font-weight:bold; box-shadow:0 3px 6px rgba(0,0,0,0.3); border:2px solid rgba(255,255,255,0.3);">
                        🔒 ' . esc_html($watermark_text) . '
                    </div>

                    
                    <div style="position:absolute; bottom:15px; left:15px; background:rgba(0,124,186,0.9); color:white; padding:6px 12px; border-radius:4px; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.3);">
                        版权保护 - 禁止下载
                    </div>

                    
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%) rotate(-15deg); font-size:48px; color:rgba(0,124,186,0.1); font-weight:bold; pointer-events:none; user-select:none; text-shadow:2px 2px 4px rgba(0,0,0,0.1);">
                        ' . esc_html($watermark_text) . '
                    </div>

                    
                    <div style="position:absolute; bottom:15px; right:15px; background:rgba(0,124,186,0.8); color:white; padding:4px 8px; border-radius:3px; font-size:10px; opacity:0.8;">
                        ' . date('Y-m-d H:i') . '
                    </div>

                    
                    <div style="position:absolute; top:15px; left:15px; background:rgba(0,124,186,0.8); color:white; padding:4px 8px; border-radius:3px; font-size:10px; opacity:0.8;">
                        受保护文档
                    </div>
                </div>
            </div>
            
            <style>
            .dcmp-pdf-container {
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                position: relative !important;
            }
            .dcmp-pdf-iframe {
                pointer-events: auto;
                position: relative;
                z-index: 1;
            }
            .dcmp-watermark-overlay {
                position: absolute !important;
                z-index: 999999 !important;
                pointer-events: none !important;
            }
            .dcmp-pdf-container iframe {
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            .dcmp-pdf-container .dcmp-watermark-overlay * {
                z-index: 999999 !important;
                position: relative;
                         }
             </style>
             ';
             
             $debug_info .= '<!-- 调试: 使用PDF.js Viewer插件 + 水印保护 (移动端兼容) -->';
             $debug_info .= '<!-- 安全全屏按钮ID: ' . $protected_fullscreen_id . ' -->';
             
             // 移动端和桌面端JavaScript注入
             wp_add_inline_script('dcmp-frontend', '
                 jQuery(document).ready(function() {
                     console.log("✅ DC Media Protect JavaScript已加载");
                     var btn = document.getElementById("' . $protected_fullscreen_id . '");
                     if (btn) {
                         console.log("🎯 找到安全全屏按钮");
                         
                         // 检测移动设备
                         var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                         console.log("📱 移动设备检测:", isMobile);
                         
                         btn.addEventListener("click", function(e) {
                             e.preventDefault();
                             console.log("🔥 安全全屏按钮被点击！设备:", isMobile ? "移动端" : "桌面端");
                             
                             if (isMobile) {
                                 // 移动端：直接在当前标签页中打开
                                 console.log("📱 移动端：在当前标签页打开全屏查看器");
                                 window.location.href = "' . esc_js($fullscreen_url) . '&mobile=1&watermark=" + encodeURIComponent("' . esc_js($watermark_text) . '");
                             } else {
                                 // 桌面端：使用弹窗
                                 console.log("💻 桌面端：使用弹窗全屏查看器");
                                 dcmpOpenProtectedFullscreen("' . esc_js($fullscreen_url) . '", "' . esc_js($watermark_text) . '");
                             }
                             return false;
                         });
                         
                         // 移动端专用：添加触摸事件
                         if (isMobile) {
                             btn.addEventListener("touchend", function(e) {
                                 e.preventDefault();
                                 console.log("📱 移动端触摸事件触发");
                                 window.location.href = "' . esc_js($fullscreen_url) . '&mobile=1&watermark=" + encodeURIComponent("' . esc_js($watermark_text) . '");
                                 return false;
                             });
                         }
                     } else {
                         console.warn("❌ 未找到安全全屏按钮");
                     }
                 });
             ');
        } else {
            // PDF.js插件不存在，使用原有的移动端优化方案
            if ($is_mobile) {
                // 微信浏览器特殊处理
                if ($is_wechat) {
                    $viewer_html = '
                    <div style="width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:2px solid #1aad19; background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; box-sizing:border-box; border-radius:12px; position:relative;">
                        <div style="position:absolute; top:10px; right:15px; background:#1aad19; color:white; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:bold;">微信浏览器</div>
                        <div style="font-size:48px; margin-bottom:20px; color:#1aad19;">📱</div>
                        <h3 style="margin:0 0 15px 0; color:#333; font-size:18px;">PDF文档查看</h3>
                        <p style="margin:0 0 20px 0; color:#666; line-height:1.4; font-size:14px;">
                            微信浏览器不支持直接预览PDF<br>
                            请选择以下方式查看文档
                        </p>
                        <div style="display:flex; flex-direction:column; gap:12px; width:100%; max-width:280px;">
                            <a href="' . $src . '" target="_blank" style="background:#1aad19; color:white; padding:14px 20px; text-decoration:none; border-radius:8px; font-size:16px; display:block; font-weight:bold; box-shadow:0 2px 4px rgba(26,173,25,0.3);">
                                🌐 在浏览器中打开
                            </a>
                            <a href="' . $src . '" download style="background:#ff9500; color:white; padding:14px 20px; text-decoration:none; border-radius:8px; font-size:16px; display:block; font-weight:bold; box-shadow:0 2px 4px rgba(255,149,0,0.3);">
                                📥 下载到手机
                            </a>
                            <div style="background:#f0f0f0; padding:12px; border-radius:6px; margin-top:8px;">
                                <small style="color:#666; font-size:12px; line-height:1.3;">
                                    💡 提示：点击"在浏览器中打开"可获得更好的阅读体验
                                </small>
                            </div>
                        </div>
                    </div>';
                    $debug_info .= '<!-- 调试: 微信浏览器特殊处理 -->';
                }
                // QQ浏览器特殊处理
                else if ($is_qq_browser) {
                    $viewer_html = '
                    <div style="width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:2px solid #12b7f5; background:linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; box-sizing:border-box; border-radius:12px; position:relative;">
                        <div style="position:absolute; top:10px; right:15px; background:#12b7f5; color:white; padding:4px 8px; border-radius:12px; font-size:12px; font-weight:bold;">QQ浏览器</div>
                        <div style="font-size:48px; margin-bottom:20px; color:#12b7f5;">📄</div>
                        <h3 style="margin:0 0 15px 0; color:#333; font-size:18px;">PDF文档查看</h3>
                        <p style="margin:0 0 20px 0; color:#666; line-height:1.4; font-size:14px;">
                            QQ浏览器优化显示方案
                        </p>
                        <div style="display:flex; flex-direction:column; gap:12px; width:100%; max-width:280px;">
                            <a href="' . $src . '" target="_blank" style="background:#12b7f5; color:white; padding:14px 20px; text-decoration:none; border-radius:8px; font-size:16px; display:block; font-weight:bold; box-shadow:0 2px 4px rgba(18,183,245,0.3);">
                                🔗 打开PDF文档
                            </a>
                            <a href="' . $src . '" download style="background:#28a745; color:white; padding:14px 20px; text-decoration:none; border-radius:8px; font-size:16px; display:block; font-weight:bold; box-shadow:0 2px 4px rgba(40,167,69,0.3);">
                                📥 下载查看
                            </a>
                        </div>
                    </div>';
                    $debug_info .= '<!-- 调试: QQ浏览器特殊处理 -->';
                }
                // 其他移动端浏览器
                else {
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
                }
            } else {
                // 桌面端：传统iframe方式，但添加水印
                $watermark_text = dcmp_get_watermark_text();
                $viewer_html = '
                <div class="dcmp-pdf-container" style="position:relative; width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:1px solid #ccc; border-radius:4px; overflow:hidden;">
                    <!-- 水印层 -->
                    <div class="dcmp-watermark-overlay" style="position:absolute; top:0; left:0; right:0; bottom:0; pointer-events:none; z-index:1000;">
                        <div style="position:absolute; top:10px; right:10px; background:rgba(0,124,186,0.8); color:white; padding:6px 10px; border-radius:4px; font-size:11px; font-weight:bold;">
                            🔒 ' . esc_html($watermark_text) . '
                        </div>
                    </div>
                    <iframe src="' . $src . '" width="100%" height="100%" style="border:none; display:block;" oncontextmenu="return false;"></iframe>
                </div>';
                $debug_info .= '<!-- 调试: 桌面端iframe显示 + 水印 -->';
            }
            
            $debug_info .= '<!-- 调试: PDF.js Viewer插件未找到，使用备用方案 -->';
        }
    } else {
        // 外部PDF文件，提供下载链接和说明
        $container_style = 'width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:1px solid #ccc; display:flex; align-items:center; justify-content:center; flex-direction:column; background:#f9f9f9; padding:20px; box-sizing:border-box; text-align:center;';
        
        $viewer_html = '
        <div style="' . $container_style . '">
            <p style="margin:10px; text-align:center;">
                <strong>PDF文档预览</strong><br>
                <small>外部PDF链接可能无法直接预览</small>
            </p>
            <a href="' . $src . '" target="_blank" class="button" style="background:#0073aa; color:white; padding:10px 20px; text-decoration:none; border-radius:3px; margin:10px;">
                📄 打开PDF文档
            </a>
            <p style="margin:10px; font-size:12px; color:#666; text-align:center;">
                建议：将PDF文件上传到媒体库以获得更好的预览效果
            </p>
        </div>';
        $debug_info .= '<!-- 调试: 外部PDF链接 -->';
    }
    
    $watermark = '<div class="dcmp-watermark" style="position:absolute; bottom:5px; right:5px; background:rgba(0,124,186,0.7); color:white; padding:3px 6px; border-radius:2px; font-size:9px; z-index:999999 !important; pointer-events:none;">' . dcmp_get_watermark_text() . '</div>';
    return $debug_info . '<div class="dcmp-media-container dcmp-pdf-container" style="position:relative; max-width:100%; overflow:hidden;">'
        . $viewer_html
        . $watermark
        . '</div>
        
        <style>
        .dcmp-media-container {
            position: relative !important;
        }
        .dcmp-watermark {
            position: absolute !important;
            z-index: 999999 !important;
            pointer-events: none !important;
        }
        /* 全屏按钮工具栏样式 */
        .dcmp-fullscreen-toolbar {
            position: relative !important;
            z-index: 1000 !important;
            margin-bottom: 8px !important;
        }
        .dcmp-fullscreen-toolbar button {
            background: #007cba !important;
            color: white !important;
            border: none !important;
            padding: 10px 16px !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            font-size: 14px !important;
            font-weight: bold !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
            transition: all 0.2s !important;
            vertical-align: top !important;
        }
        .dcmp-fullscreen-toolbar button:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3) !important;
        }
        .dcmp-fullscreen-toolbar button svg {
            width: 16px !important;
            height: 16px !important;
            fill: currentColor !important;
        }
        /* 全屏时的样式 */
        .dcmp-pdf-container iframe:fullscreen {
            width: 100vw !important;
            height: 100vh !important;
            border: none !important;
        }
        .dcmp-pdf-container iframe:-webkit-full-screen {
            width: 100vw !important;
            height: 100vh !important;
            border: none !important;
        }
        .dcmp-pdf-container iframe:-moz-full-screen {
            width: 100vw !important;
            height: 100vh !important;
            border: none !important;
        }
        </style>';
}
add_shortcode('dc_ppt', 'dcmp_shortcode_ppt');

// 图片短代码 [dc_img src="图片地址" width="宽度" height="高度"]
function dcmp_shortcode_img($atts) {
    $src = isset($atts['src']) ? esc_url($atts['src']) : '';
    $width = isset($atts['width']) ? intval($atts['width']) : 0;
    $height = isset($atts['height']) ? intval($atts['height']) : 0;
    if (!$src) return '';
    $style = '';
    if ($width > 0) $style .= 'width:' . $width . 'px;';
    if ($height > 0) $style .= 'height:' . $height . 'px;';
    if (!$style) $style = 'max-width:100%;';
    $watermark = '<div class="dcmp-watermark">' . dcmp_get_watermark_text() . '</div>';
    return '<div class="dcmp-media-container" style="position:relative;">'
        . '<img src="' . $src . '" style="' . esc_attr($style) . '" />'
        . $watermark
        . '</div>';
}
add_shortcode('dc_img', 'dcmp_shortcode_img');

// 文字短代码 [dc_text]内容[/dc_text]
function dcmp_shortcode_text($atts, $content = null) {
    return '<div class="dcmp-text">' . esc_html($content) . '</div>';
}
add_shortcode('dc_text', 'dcmp_shortcode_text'); 