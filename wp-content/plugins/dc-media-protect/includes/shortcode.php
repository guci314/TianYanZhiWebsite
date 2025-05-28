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
        // 如果是相对路径（不以/开头），转换为绝对路径
        $processed_src = '/' . $src;
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
            $h = '400px';
        } else {
            $w = 800;
            $h = 600;
        }
    }
    
    // 检查是否为本地文件（包括data: URL和相对路径）
    $home_url = home_url();
    $is_local = (strpos($processed_src, $home_url) === 0) || 
                (strpos($processed_src, 'data:') === 0) || 
                (strpos($processed_src, '/wp-content/') === 0) ||
                (strpos($src, 'wp-content/') === 0);  // 检查原始路径中的相对路径
    
    $viewer_html = '';
    $container_id = 'dcmp-pdf-' . uniqid();
    
    // 添加详细调试信息
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
    -->';
    
    if ($is_local) {
        // 检查是否有PDF.js Viewer插件
        $pdfjs_plugin_exists = file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php');
        
        if ($pdfjs_plugin_exists) {
            // 使用PDF.js Viewer插件的方式，但添加水印和防下载功能
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
                         '&_wpnonce=' . $nonce;
            
            $viewer_html = '
            <div class="dcmp-pdf-container" style="position:relative; width:' . (is_numeric($w) ? $w . 'px' : $w) . '; height:' . (is_numeric($h) ? $h . 'px' : $h) . '; border:2px solid #007cba; border-radius:8px; overflow:hidden; background:#f9f9f9;">
                <!-- PDF查看器iframe -->
                <iframe src="' . esc_url($viewer_url) . '" 
                        width="100%" 
                        height="100%" 
                        style="border:none; display:block;" 
                        title="PDF文档查看器"
                        sandbox="allow-same-origin allow-scripts allow-forms"
                        oncontextmenu="return false;"
                        class="dcmp-pdf-iframe"></iframe>
                
                <!-- 增强水印层 - 确保在最上层 -->
                <div class="dcmp-watermark-overlay" style="position:absolute; top:0; left:0; right:0; bottom:0; pointer-events:none; z-index:999999 !important; background:repeating-linear-gradient(45deg, transparent, transparent 150px, rgba(0,124,186,0.03) 150px, rgba(0,124,186,0.03) 300px);">
                    <!-- 右上角主水印 -->
                    <div style="position:absolute; top:15px; right:15px; background:rgba(0,124,186,0.9); color:white; padding:8px 15px; border-radius:6px; font-size:14px; font-weight:bold; box-shadow:0 3px 6px rgba(0,0,0,0.3); border:2px solid rgba(255,255,255,0.3);">
                        🔒 ' . esc_html($watermark_text) . '
                    </div>
                    
                    <!-- 左下角版权信息 -->
                    <div style="position:absolute; bottom:15px; left:15px; background:rgba(0,124,186,0.9); color:white; padding:6px 12px; border-radius:4px; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.3);">
                        版权保护 - 禁止下载
                    </div>
                    
                    <!-- 中心大水印 -->
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%) rotate(-15deg); font-size:48px; color:rgba(0,124,186,0.1); font-weight:bold; pointer-events:none; user-select:none; text-shadow:2px 2px 4px rgba(0,0,0,0.1);">
                        ' . esc_html($watermark_text) . '
                    </div>
                    
                    <!-- 右下角时间戳 -->
                    <div style="position:absolute; bottom:15px; right:15px; background:rgba(0,124,186,0.8); color:white; padding:4px 8px; border-radius:3px; font-size:10px; opacity:0.8;">
                        ' . date('Y-m-d H:i') . '
                    </div>
                    
                    <!-- 左上角标识 -->
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
            /* 防止右键菜单 */
            .dcmp-pdf-container iframe {
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            /* 确保水印始终在最上层 */
            .dcmp-pdf-container .dcmp-watermark-overlay * {
                z-index: 999999 !important;
                position: relative;
            }
            </style>
            
            <script>
            // 防下载保护脚本
            document.addEventListener("DOMContentLoaded", function() {
                // 禁用右键菜单
                document.addEventListener("contextmenu", function(e) {
                    if (e.target.closest(".dcmp-pdf-container")) {
                        e.preventDefault();
                        return false;
                    }
                });
                
                // 禁用拖拽
                document.addEventListener("dragstart", function(e) {
                    if (e.target.closest(".dcmp-pdf-container")) {
                        e.preventDefault();
                        return false;
                    }
                });
                
                // 禁用选择
                document.addEventListener("selectstart", function(e) {
                    if (e.target.closest(".dcmp-pdf-container")) {
                        e.preventDefault();
                        return false;
                    }
                });
                
                // 禁用快捷键
                document.addEventListener("keydown", function(e) {
                    if (e.target.closest(".dcmp-pdf-container")) {
                        // 禁用 Ctrl+S (保存), Ctrl+P (打印), Ctrl+A (全选), F12 (开发者工具)
                        if ((e.ctrlKey && (e.key === "s" || e.key === "p" || e.key === "a")) || e.key === "F12") {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            });
            </script>';
            
            $debug_info .= '<!-- 调试: 使用PDF.js Viewer插件 + 水印保护 -->';
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