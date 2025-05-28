<?php
// 移动端PDF查看器支持

// 检测用户设备类型
function dcmp_detect_device_type() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $device_info = [
        'is_mobile' => wp_is_mobile(),
        'is_ios' => (strpos($user_agent, 'iPhone') !== false || strpos($user_agent, 'iPad') !== false),
        'is_android' => strpos($user_agent, 'Android') !== false,
        'is_chrome' => strpos($user_agent, 'Chrome') !== false,
        'is_safari' => strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false,
        'is_firefox' => strpos($user_agent, 'Firefox') !== false,
        'is_mi_browser' => strpos($user_agent, 'MiuiBrowser') !== false || strpos($user_agent, 'XiaoMi') !== false || strpos($user_agent, 'MIUI') !== false,
        'is_wechat_browser' => strpos($user_agent, 'MicroMessenger') !== false,
        'is_miui_device' => strpos($user_agent, '2312P') !== false || strpos($user_agent, 'WOCC') !== false || strpos($user_agent, 'MMMWRESDK') !== false,
        'is_uc_browser' => strpos($user_agent, 'UCBrowser') !== false,
        'is_qq_browser' => strpos($user_agent, 'QQBrowser') !== false,
        'is_huawei_browser' => strpos($user_agent, 'HuaweiBrowser') !== false,
        'is_sogou_browser' => strpos($user_agent, 'SogouMobileBrowser') !== false
    ];
    
    return $device_info;
}

// 生成移动端PDF查看器HTML
function dcmp_generate_mobile_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $device = dcmp_detect_device_type();
    
    // 根据设备类型选择最佳显示方案（注意：国产浏览器检测必须优先于Chrome检测）
    if ($device['is_ios'] && $device['is_safari']) {
        // iOS Safari: 使用原生PDF查看器
        return dcmp_generate_ios_pdf_viewer($pdf_url, $width, $height, $container_id);
    } elseif ($device['is_wechat_browser'] && $device['is_miui_device']) {
        // 小米设备上的微信浏览器: 使用专门的微信兼容方案
        return dcmp_generate_wechat_miui_pdf_viewer($pdf_url, $width, $height, $container_id);
    } elseif ($device['is_wechat_browser']) {
        // 微信内置浏览器: 使用微信专用方案
        return dcmp_generate_wechat_pdf_viewer($pdf_url, $width, $height, $container_id);
    } elseif ($device['is_mi_browser'] || $device['is_uc_browser'] || $device['is_qq_browser'] || $device['is_huawei_browser'] || $device['is_sogou_browser']) {
        // 国产浏览器: 使用兼容性更好的方案（优先检测，因为它们的UA也包含Chrome）
        return dcmp_generate_chinese_browser_pdf_viewer($pdf_url, $width, $height, $container_id);
    } elseif ($device['is_firefox']) {
        // Firefox: 支持较好的PDF显示
        return dcmp_generate_firefox_pdf_viewer($pdf_url, $width, $height, $container_id);
    } elseif ($device['is_android'] && $device['is_chrome']) {
        // 纯Android Chrome: 使用PDF.js
        return dcmp_generate_android_pdf_viewer($pdf_url, $width, $height, $container_id);
    } else {
        // 其他移动设备: 通用方案
        return dcmp_generate_generic_pdf_viewer($pdf_url, $width, $height, $container_id);
    }
}

// iOS PDF查看器
function dcmp_generate_ios_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $w = is_numeric($width) ? $width . 'px' : $width;
    $h = is_numeric($height) ? $height . 'px' : $height;
    
    return '
    <div class="dcmp-pdf-ios-container" style="width:' . $w . '; height:' . $h . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; border-radius:8px; overflow:hidden;">
        <div style="background:#007cba; color:white; padding:12px; text-align:center; font-size:16px;">
            📄 PDF文档查看器
        </div>
        <div style="flex:1; position:relative; background:white;">
            <iframe src="' . $pdf_url . '" 
                    style="width:100%; height:100%; border:none;" 
                    sandbox="allow-same-origin allow-scripts allow-forms allow-downloads"
                    id="' . $container_id . '-iframe"></iframe>
        </div>
        <div style="background:#f0f0f0; padding:10px; text-align:center; border-top:1px solid #ddd;">
            <a href="' . $pdf_url . '" target="_blank" style="background:#007cba; color:white; padding:8px 16px; text-decoration:none; border-radius:4px; font-size:14px;">
                在新标签页打开
            </a>
        </div>
    </div>';
}

// Firefox PDF查看器 (优化的iframe)
function dcmp_generate_firefox_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $w = is_numeric($width) ? $width . 'px' : $width;
    $h = is_numeric($height) ? $height . 'px' : $height;
    
    return '
    <div class="dcmp-pdf-firefox-container" style="width:' . $w . '; height:' . $h . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; border-radius:8px; overflow:hidden;">
        <div style="background:#ff9500; color:white; padding:12px; text-align:center; font-size:16px; display:flex; align-items:center; justify-content:space-between;">
            <span>🦊 Firefox PDF查看器</span>
            <a href="' . $pdf_url . '" target="_blank" style="background:rgba(255,255,255,0.2); color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:12px;">
                新窗口
            </a>
        </div>
        <div style="flex:1; position:relative; background:white;">
            <iframe src="' . $pdf_url . '" 
                    style="width:100%; height:100%; border:none;" 
                    sandbox="allow-same-origin allow-scripts allow-forms allow-downloads"
                    id="' . $container_id . '-iframe"></iframe>
        </div>
        <div style="background:#f0f0f0; padding:8px; text-align:center; border-top:1px solid #ddd; font-size:12px; color:#666;">
            Firefox内置PDF支持 - 支持缩放、搜索等功能
        </div>
    </div>';
}

// 小米设备微信浏览器PDF查看器 (特殊优化)
function dcmp_generate_wechat_miui_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $w = is_numeric($width) ? $width . 'px' : $width;
    $h = is_numeric($height) ? $height . 'px' : $height;
    
    return '
    <div class="dcmp-pdf-wechat-miui-container" style="width:' . $w . '; height:' . $h . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; border-radius:8px; overflow:hidden;">
        <div style="background:#1aad19; color:white; padding:12px; text-align:center; font-size:16px; display:flex; align-items:center; justify-content:space-between;">
            <span>💬📱 微信小米设备PDF查看器</span>
        </div>
        <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; background:white;">
            <div style="font-size:48px; margin-bottom:20px; color:#1aad19;">📄</div>
            <h3 style="margin:0 0 15px 0; color:#333;">PDF文档查看</h3>
            <p style="margin:0 0 20px 0; color:#666; line-height:1.5; font-size:14px;">
                检测到您在 <strong>小米设备</strong> 上使用 <strong>微信内置浏览器</strong><br>
                为确保最佳体验，建议：
            </p>
            
            <div style="display:flex; flex-direction:column; gap:12px; width:100%; max-width:300px; margin-bottom:20px;">
                <a href="' . $pdf_url . '" target="_blank" style="background:#1aad19; color:white; padding:14px 20px; text-decoration:none; border-radius:8px; font-size:16px; display:block; text-align:center; box-shadow:0 2px 6px rgba(26,173,25,0.3);">
                    🔗 在浏览器中打开PDF
                </a>
                <button onclick="dcmpOpenInDefaultApp(\'' . $pdf_url . '\')" style="background:#ff9800; color:white; padding:14px 20px; border:none; border-radius:8px; font-size:16px; cursor:pointer; box-shadow:0 2px 6px rgba(255,152,0,0.3);">
                    📱 用其他应用打开
                </button>
                <button onclick="dcmpCopyLink(\'' . $pdf_url . '\')" style="background:#2196f3; color:white; padding:14px 20px; border:none; border-radius:8px; font-size:16px; cursor:pointer; box-shadow:0 2px 6px rgba(33,150,243,0.3);">
                    📋 复制链接
                </button>
            </div>
            
            <!-- 尝试直接显示 -->
            <details style="width:100%; margin-top:10px;">
                <summary style="cursor:pointer; color:#1aad19; font-weight:bold; padding:10px; background:#f0f8f0; border-radius:5px;">
                    🔧 尝试直接显示（可能不兼容）
                </summary>
                <div style="margin-top:15px; border:1px solid #ddd; border-radius:5px; height:300px; background:#fafafa; position:relative;">
                    <iframe src="' . $pdf_url . '" 
                            style="width:100%; height:100%; border:none; border-radius:5px;" 
                            id="' . $container_id . '-iframe"
                            onload="dcmpIframeLoaded(\'' . $container_id . '\')"
                            onerror="dcmpIframeError(\'' . $container_id . '\')"></iframe>
                    <div id="' . $container_id . '-loading" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:#666;">
                        <div style="text-align:center;">
                            <div style="font-size:24px; margin-bottom:10px;">⏳</div>
                            <div>尝试加载中...</div>
                        </div>
                    </div>
                </div>
            </details>
        </div>
        <div style="background:#f0f8f0; padding:10px; text-align:center; border-top:1px solid #ddd; font-size:11px; color:#666;">
            💡 提示：微信内置浏览器对PDF支持有限，建议在外部浏览器打开
        </div>
    </div>
    
    <script>
    function dcmpOpenInDefaultApp(url) {
        // 尝试唤起系统默认应用
        const link = document.createElement("a");
        link.href = url;
        link.target = "_blank";
        link.rel = "noopener noreferrer";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        setTimeout(() => {
            alert("如果PDF没有打开，请：\\n1. 长按上方链接选择\\"用其他应用打开\\"\\n2. 安装WPS Office或其他PDF阅读器\\n3. 复制链接到浏览器打开");
        }, 1000);
    }
    
    function dcmpCopyLink(url) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                alert("✅ PDF链接已复制！\\n请粘贴到浏览器地址栏打开");
            }).catch(() => {
                dcmpFallbackCopyTextToClipboard(url);
            });
        } else {
            dcmpFallbackCopyTextToClipboard(url);
        }
    }
    
    function dcmpFallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.width = "2em";
        textArea.style.height = "2em";
        textArea.style.padding = "0";
        textArea.style.border = "none";
        textArea.style.outline = "none";
        textArea.style.boxShadow = "none";
        textArea.style.background = "transparent";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand("copy");
            if (successful) {
                alert("✅ PDF链接已复制！\\n请粘贴到浏览器地址栏打开");
            } else {
                alert("❌ 复制失败，请手动复制链接：\\n" + text);
            }
        } catch (err) {
            alert("❌ 复制失败，请手动复制链接：\\n" + text);
        }
        
        document.body.removeChild(textArea);
    }
    
    function dcmpIframeLoaded(containerId) {
        const loading = document.getElementById(containerId + "-loading");
        if (loading) loading.style.display = "none";
    }
    
    function dcmpIframeError(containerId) {
        const container = document.getElementById(containerId + "-iframe-container");
        const loading = document.getElementById(containerId + "-loading");
        if (loading) loading.style.display = "none";
        if (container) {
            container.innerHTML = `
                <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#666; flex-direction:column;">
                    <div style="font-size:24px; margin-bottom:10px;">❌</div>
                    <div>微信浏览器暂不支持直接显示PDF</div>
                    <div style="font-size:12px; margin-top:5px;">请使用上方按钮打开</div>
                </div>
            `;
        }
    }
    </script>';
}

// 微信浏览器PDF查看器 (通用微信)
function dcmp_generate_wechat_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $w = is_numeric($width) ? $width . 'px' : $width;
    $h = is_numeric($height) ? $height . 'px' : $height;
    
    return '
    <div class="dcmp-pdf-wechat-container" style="width:' . $w . '; height:' . $h . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; border-radius:8px; overflow:hidden;">
        <div style="background:#1aad19; color:white; padding:12px; text-align:center; font-size:16px;">
            💬 微信PDF查看器
        </div>
        <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; background:white;">
            <div style="font-size:48px; margin-bottom:20px; color:#1aad19;">📄</div>
            <h3 style="margin:0 0 15px 0; color:#333;">PDF文档查看</h3>
            <p style="margin:0 0 20px 0; color:#666; line-height:1.5; font-size:14px;">
                检测到您使用 <strong>微信内置浏览器</strong><br>
                建议使用以下方式查看PDF：
            </p>
            
            <div style="display:flex; flex-direction:column; gap:10px; width:100%; max-width:280px;">
                <a href="' . $pdf_url . '" target="_blank" style="background:#1aad19; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block; text-align:center;">
                    🔗 在浏览器中打开
                </a>
                <button onclick="dcmpCopyLink(\'' . $pdf_url . '\')" style="background:#2196f3; color:white; padding:12px 20px; border:none; border-radius:5px; font-size:16px; cursor:pointer;">
                    📋 复制链接
                </button>
            </div>
        </div>
        <div style="background:#f0f8f0; padding:8px; text-align:center; border-top:1px solid #ddd; font-size:11px; color:#666;">
            微信内置浏览器对PDF支持有限，建议使用外部浏览器
        </div>
    </div>';
}

// 国产浏览器PDF查看器 (兼容性优先)
function dcmp_generate_chinese_browser_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $w = is_numeric($width) ? $width . 'px' : $width;
    $h = is_numeric($height) ? $height . 'px' : $height;
    
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $browser_name = '';
    if (strpos($user_agent, 'MiuiBrowser') !== false || strpos($user_agent, 'XiaoMi') !== false) {
        $browser_name = '小米浏览器';
    } elseif (strpos($user_agent, 'UCBrowser') !== false) {
        $browser_name = 'UC浏览器';
    } elseif (strpos($user_agent, 'QQBrowser') !== false) {
        $browser_name = 'QQ浏览器';
    } elseif (strpos($user_agent, 'HuaweiBrowser') !== false) {
        $browser_name = '华为浏览器';
    } elseif (strpos($user_agent, 'SogouMobileBrowser') !== false) {
        $browser_name = '搜狗浏览器';
    } else {
        $browser_name = '移动浏览器';
    }
    
    return '
    <div class="dcmp-pdf-chinese-container" style="width:' . $w . '; height:' . $h . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; border-radius:8px; overflow:hidden;">
        <div style="background:#1976d2; color:white; padding:12px; text-align:center; font-size:16px;">
            📱 ' . $browser_name . ' PDF查看器
        </div>
        <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; background:white;">
            <div style="font-size:48px; margin-bottom:20px; color:#1976d2;">📄</div>
            <h3 style="margin:0 0 15px 0; color:#333;">PDF文档查看</h3>
            <p style="margin:0 0 20px 0; color:#666; line-height:1.5; font-size:14px;">
                检测到您使用的是 <strong>' . $browser_name . '</strong><br>
                为确保最佳查看体验，请选择以下方式：
            </p>
            
            <!-- 先尝试直接显示 -->
            <div id="' . $container_id . '-iframe-container" style="width:100%; height:250px; margin:15px 0; border:1px solid #ddd; border-radius:5px; background:#fafafa; position:relative;">
                <iframe src="' . $pdf_url . '" 
                        style="width:100%; height:100%; border:none; border-radius:5px;" 
                        id="' . $container_id . '-iframe"
                        onload="dcmpIframeLoaded(\'' . $container_id . '\')"
                        onerror="dcmpIframeError(\'' . $container_id . '\')"></iframe>
                <div id="' . $container_id . '-loading" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); color:#666;">
                    <div style="text-align:center;">
                        <div style="font-size:24px; margin-bottom:10px;">⏳</div>
                        <div>正在加载PDF...</div>
                    </div>
                </div>
            </div>
            
            <!-- 备用选项 -->
            <div style="display:flex; flex-direction:column; gap:10px; width:100%; max-width:280px;">
                <a href="' . $pdf_url . '" target="_blank" style="background:#1976d2; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block; text-align:center;">
                    🔗 在新标签页打开
                </a>
                <button onclick="dcmpTryAlternative(\'' . $container_id . '\', \'' . $pdf_url . '\')" style="background:#ff9800; color:white; padding:12px 20px; border:none; border-radius:5px; font-size:16px; cursor:pointer;">
                    🔄 尝试替代方案
                </button>
            </div>
        </div>
        <div style="background:#f0f0f0; padding:8px; text-align:center; border-top:1px solid #ddd; font-size:11px; color:#666;">
            如遇显示问题，建议使用系统自带浏览器或Chrome浏览器
        </div>
    </div>
    
    <script>
    // PDF iframe加载处理
    function dcmpIframeLoaded(containerId) {
        const loading = document.getElementById(containerId + "-loading");
        if (loading) loading.style.display = "none";
    }
    
    function dcmpIframeError(containerId) {
        const container = document.getElementById(containerId + "-iframe-container");
        const loading = document.getElementById(containerId + "-loading");
        if (loading) loading.style.display = "none";
        if (container) {
            container.innerHTML = `
                <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#666; flex-direction:column;">
                    <div style="font-size:24px; margin-bottom:10px;">❌</div>
                    <div>该浏览器暂不支持直接显示PDF</div>
                    <div style="font-size:12px; margin-top:5px;">请使用下方按钮打开</div>
                </div>
            `;
        }
    }
    
    // 替代方案
    function dcmpTryAlternative(containerId, pdfUrl) {
        const container = document.getElementById(containerId + "-iframe-container");
        if (container) {
            container.innerHTML = `
                <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#666; flex-direction:column; padding:20px;">
                    <div style="font-size:24px; margin-bottom:15px;">📋</div>
                    <div style="text-align:center; line-height:1.4;">
                        <div style="margin-bottom:10px;">尝试以下解决方案：</div>
                        <div style="font-size:12px; color:#999;">
                            1. 长按PDF链接选择"用其他应用打开"<br>
                            2. 安装WPS、福昕等PDF阅读器<br>
                            3. 使用Chrome或Firefox浏览器
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    // 3秒后检查iframe是否加载成功
    setTimeout(function() {
        const iframe = document.getElementById("' . $container_id . '-iframe");
        const loading = document.getElementById("' . $container_id . '-loading");
        if (iframe && loading && loading.style.display !== "none") {
            dcmpIframeError("' . $container_id . '");
        }
    }, 3000);
    </script>';
}

// Android PDF查看器 (PDF.js)
function dcmp_generate_android_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $w = is_numeric($width) ? $width . 'px' : $width;
    $h = is_numeric($height) ? $height . 'px' : $height;
    
    return '
    <div class="dcmp-pdf-android-container" style="width:' . $w . '; height:' . $h . '; border:1px solid #ccc; background:#f9f9f9; position:relative; border-radius:8px; overflow:hidden;">
        <div style="position:absolute; top:10px; right:10px; z-index:1000;">
            <a href="' . $pdf_url . '" target="_blank" style="background:#007cba; color:white; padding:6px 12px; text-decoration:none; border-radius:4px; font-size:12px; box-shadow:0 2px 4px rgba(0,0,0,0.2);">
                新窗口
            </a>
        </div>
        <canvas id="' . $container_id . '-canvas" style="width:100%; height:calc(100% - 50px); display:block; background:white;"></canvas>
        <div id="' . $container_id . '-controls" style="position:absolute; bottom:0; left:0; right:0; background:linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.6)); color:white; padding:10px; display:flex; justify-content:center; align-items:center;">
            <button onclick="dcmpPrevPage(\'' . $container_id . '\')" style="background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); color:white; margin:0 8px; cursor:pointer; padding:8px 12px; border-radius:4px; font-size:14px;">
                ◀ 上一页
            </button>
            <span style="margin:0 15px; font-size:14px; background:rgba(255,255,255,0.1); padding:4px 8px; border-radius:3px;">
                <span id="' . $container_id . '-pagenum">1</span> / <span id="' . $container_id . '-pagecount">1</span>
            </span>
            <button onclick="dcmpNextPage(\'' . $container_id . '\')" style="background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); color:white; margin:0 8px; cursor:pointer; padding:8px 12px; border-radius:4px; font-size:14px;">
                下一页 ▶
            </button>
        </div>
    </div>
    <script>
    window.dcmpPdfData = window.dcmpPdfData || {};
    window.dcmpPdfData["' . $container_id . '"] = {
        url: "' . $pdf_url . '",
        currentPage: 1,
        totalPages: 1,
        pdfDoc: null
    };
    </script>';
}

// 通用PDF查看器
function dcmp_generate_generic_pdf_viewer($pdf_url, $width, $height, $container_id) {
    $w = is_numeric($width) ? $width . 'px' : $width;
    $h = is_numeric($height) ? $height . 'px' : $height;
    
    return '
    <div class="dcmp-pdf-generic-container" style="width:' . $w . '; height:' . $h . '; border:1px solid #ccc; background:#f9f9f9; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:20px; box-sizing:border-box; border-radius:8px;">
        <div style="font-size:48px; margin-bottom:20px; color:#666;">📄</div>
        <h3 style="margin:0 0 15px 0; color:#333;">PDF文档</h3>
        <p style="margin:0 0 20px 0; color:#666; line-height:1.4;">
            移动设备上查看PDF可能受到限制<br>
            建议使用以下方式打开文档
        </p>
        <div style="display:flex; flex-direction:column; gap:10px; width:100%; max-width:250px;">
            <a href="' . $pdf_url . '" target="_blank" style="background:#007cba; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block;">
                🔗 在新窗口打开
            </a>
            <a href="' . $pdf_url . '" download style="background:#28a745; color:white; padding:12px 20px; text-decoration:none; border-radius:5px; font-size:16px; display:block;">
                📥 下载查看
            </a>
        </div>
        <small style="margin-top:15px; color:#999; font-size:12px;">
            某些设备可能需要安装PDF阅读器应用
        </small>
    </div>';
}

// AJAX处理器：获取PDF信息
add_action('wp_ajax_dcmp_get_pdf_info', 'dcmp_ajax_get_pdf_info');
add_action('wp_ajax_nopriv_dcmp_get_pdf_info', 'dcmp_ajax_get_pdf_info');

function dcmp_ajax_get_pdf_info() {
    $pdf_url = sanitize_url($_POST['pdf_url'] ?? '');
    
    if (!$pdf_url) {
        wp_die(json_encode(['error' => 'PDF URL is required']));
    }
    
    // 检查PDF文件是否存在
    $response = wp_remote_head($pdf_url);
    
    if (is_wp_error($response)) {
        wp_die(json_encode(['error' => 'PDF file not accessible']));
    }
    
    $content_type = wp_remote_retrieve_header($response, 'content-type');
    $content_length = wp_remote_retrieve_header($response, 'content-length');
    
    wp_die(json_encode([
        'success' => true,
        'content_type' => $content_type,
        'file_size' => $content_length,
        'is_pdf' => strpos($content_type, 'pdf') !== false
    ]));
} 