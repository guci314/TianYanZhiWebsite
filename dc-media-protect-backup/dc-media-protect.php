<?php
/*
Plugin Name: DC Media Protect
Description: 数字中国多媒体防护与水印插件，实现视频、PPT、图片等内容的安全展示与防下载。
Version: 1.0.0
Author: Guci AI
*/

// 插件初始化代码将在后续步骤补充 

require_once plugin_dir_path(__FILE__) . 'includes/upload-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/watermark.php';
require_once plugin_dir_path(__FILE__) . 'includes/ppt-convert.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/content-crawler.php'; 

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('dcmp-frontend', plugins_url('assets/js/frontend.js', __FILE__), [], '1.0.0', true);
    wp_enqueue_script('dcmp-remove-tips', plugins_url('assets/js/remove-double-click-tips.js', __FILE__), [], '1.0.0', true);
    wp_enqueue_style('dcmp-style', plugins_url('assets/css/style.css', __FILE__), [], '1.0.0');
}); 

// 移除双击全屏提示的功能
add_action('wp_footer', function() {
    ?>
    <script>
    // 移除双击PDF全屏提示
    (function() {
        function removeDoubleClickTips() {
            // 移除所有包含"双击PDF全屏"或"双击全屏"的元素
            const textNodes = [];
            const walker = document.createTreeWalker(
                document.body,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );
            
            let node;
            while (node = walker.nextNode()) {
                if (node.textContent.includes('双击PDF全屏') || 
                    node.textContent.includes('双击全屏') ||
                    node.textContent.includes('double click fullscreen')) {
                    textNodes.push(node);
                }
            }
            
            textNodes.forEach(function(textNode) {
                const parent = textNode.parentElement;
                if (parent) {
                    parent.style.display = 'none';
                }
            });
            
            // 移除可能的提示属性
            document.querySelectorAll('*').forEach(function(element) {
                if (element.title && (element.title.includes('双击') || element.title.includes('double click'))) {
                    element.title = '';
                }
                if (element.getAttribute('data-tooltip') && element.getAttribute('data-tooltip').includes('双击')) {
                    element.removeAttribute('data-tooltip');
                }
                if (element.getAttribute('aria-label') && element.getAttribute('aria-label').includes('双击')) {
                    element.setAttribute('aria-label', '');
                }
            });
        }
        
        // 页面加载完成后执行
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', removeDoubleClickTips);
        } else {
            removeDoubleClickTips();
        }
        
        // 定期检查（前3秒）
        let checkCount = 0;
        const intervalId = setInterval(function() {
            removeDoubleClickTips();
            checkCount++;
            if (checkCount >= 3) {
                clearInterval(intervalId);
            }
        }, 1000);
    })();
    </script>
    <style>
    /* 强制隐藏可能的双击提示 */
    *[title*="双击"],
    *[data-tooltip*="双击"],
    *[aria-label*="双击"] {
        display: none !important;
    }
    </style>
    <?php
});

if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
} 