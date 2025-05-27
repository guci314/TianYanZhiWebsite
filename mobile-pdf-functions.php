<?php
/**
 * 移动端PDF显示解决方案 - WordPress函数
 * 添加到主题的 functions.php 文件中
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 智能PDF嵌入短代码
 * 自动检测移动端并提供最佳显示方案
 */
function smart_pdf_embedder_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'width' => 'max',
        'height' => '600',
        'toolbar' => 'none',
        'mobile_text' => '点击查看PDF文档'
    ), $atts);
    
    // 验证URL
    if (empty($atts['url'])) {
        return '<p style="color: red;">错误：请提供PDF文件URL</p>';
    }
    
    // 移动端检测
    $is_mobile = wp_is_mobile();
    
    ob_start();
    ?>
    <div class="smart-pdf-container" data-url="<?php echo esc_url($atts['url']); ?>">
        <?php if ($is_mobile): ?>
            <!-- 移动端显示 -->
            <div class="mobile-pdf-viewer" onclick="openMobilePDF('<?php echo esc_url($atts['url']); ?>')">
                <div class="pdf-thumbnail">
                    <div class="pdf-icon">📄</div>
                    <h4><?php echo esc_html($atts['mobile_text']); ?></h4>
                    <p>点击在新窗口中打开PDF获得最佳阅读体验</p>
                    <div class="mobile-pdf-actions">
                        <span class="pdf-action-btn">📱 移动端优化阅读</span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- 桌面端显示 -->
            <?php echo do_shortcode('[pdf-embedder url="' . $atts['url'] . '" width="' . $atts['width'] . '" toolbar="' . $atts['toolbar'] . '"]'); ?>
        <?php endif; ?>
    </div>
    
    <style>
    .smart-pdf-container {
        margin: 20px 0;
        position: relative;
    }
    
    .mobile-pdf-viewer {
        border: 2px dashed #007cba;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .mobile-pdf-viewer:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        border-color: #005a87;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 124, 186, 0.2);
    }
    
    .pdf-thumbnail {
        text-align: center;
    }
    
    .pdf-icon {
        font-size: 64px;
        display: block;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    
    .mobile-pdf-viewer h4 {
        color: #333;
        margin: 10px 0;
        font-size: 18px;
        font-weight: 600;
    }
    
    .mobile-pdf-viewer p {
        color: #666;
        margin: 10px 0;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .pdf-action-btn {
        display: inline-block;
        background-color: #007cba;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        margin-top: 10px;
    }
    
    /* 移动端特定样式 */
    @media (max-width: 768px) {
        .smart-pdf-container {
            margin: 15px 0;
        }
        
        .mobile-pdf-viewer {
            padding: 25px 15px;
            min-height: 180px;
        }
        
        .pdf-icon {
            font-size: 48px;
        }
        
        .mobile-pdf-viewer h4 {
            font-size: 16px;
        }
        
        .mobile-pdf-viewer p {
            font-size: 13px;
        }
    }
    </style>
    
    <script>
    function openMobilePDF(url) {
        // 在新窗口中打开PDF
        const newWindow = window.open(url, '_blank', 'width=400,height=600,scrollbars=yes,resizable=yes');
        
        // 如果新窗口打开失败，提供下载选项
        if (!newWindow) {
            if (confirm('无法在新窗口中打开PDF，是否直接下载？')) {
                const a = document.createElement('a');
                a.href = url;
                a.download = '';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        }
    }
    </script>
    <?php
    
    return ob_get_clean();
}
add_shortcode('smart_pdf', 'smart_pdf_embedder_shortcode');

/**
 * 移动端友好的PDF查看器（使用PDF.js）
 */
function mobile_pdf_viewer_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'width' => '100%',
        'height' => '600px'
    ), $atts);
    
    if (empty($atts['url'])) {
        return '<p style="color: red;">错误：请提供PDF文件URL</p>';
    }
    
    $is_mobile = wp_is_mobile();
    
    ob_start();
    ?>
    <div class="mobile-pdf-container">
        <?php if ($is_mobile): ?>
            <!-- 移动端使用优化的显示方式 -->
            <div class="mobile-pdf-fallback">
                <div class="pdf-preview-card">
                    <div class="pdf-preview-icon">📄</div>
                    <div class="pdf-preview-content">
                        <h4>PDF文档预览</h4>
                        <p>为了在移动设备上获得最佳阅读体验，请选择以下方式：</p>
                        <div class="pdf-action-buttons">
                            <a href="<?php echo esc_url($atts['url']); ?>" target="_blank" class="pdf-btn pdf-btn-view">
                                🔍 在线查看
                            </a>
                            <a href="<?php echo esc_url($atts['url']); ?>" download class="pdf-btn pdf-btn-download">
                                ⬇️ 下载PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- 桌面端使用PDF.js -->
            <iframe 
                src="https://mozilla.github.io/pdf.js/web/viewer.html?file=<?php echo urlencode($atts['url']); ?>"
                width="<?php echo esc_attr($atts['width']); ?>" 
                height="<?php echo esc_attr($atts['height']); ?>"
                style="border: none; border-radius: 8px;">
            </iframe>
        <?php endif; ?>
    </div>
    
    <style>
    .mobile-pdf-container {
        margin: 20px 0;
    }
    
    .mobile-pdf-fallback {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
    }
    
    .pdf-preview-card {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    
    .pdf-preview-icon {
        font-size: 48px;
        opacity: 0.7;
    }
    
    .pdf-preview-content h4 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 18px;
    }
    
    .pdf-preview-content p {
        margin: 0 0 15px 0;
        color: #666;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .pdf-action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .pdf-btn {
        display: inline-block;
        padding: 10px 16px;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .pdf-btn-view {
        background-color: #007cba;
        color: white;
    }
    
    .pdf-btn-view:hover {
        background-color: #005a87;
        color: white;
    }
    
    .pdf-btn-download {
        background-color: #28a745;
        color: white;
    }
    
    .pdf-btn-download:hover {
        background-color: #218838;
        color: white;
    }
    
    @media (max-width: 768px) {
        .pdf-preview-card {
            flex-direction: column;
            text-align: center;
            gap: 15px;
        }
        
        .pdf-action-buttons {
            justify-content: center;
        }
        
        .pdf-btn {
            flex: 1;
            text-align: center;
            min-width: 120px;
        }
    }
    </style>
    <?php
    
    return ob_get_clean();
}
add_shortcode('mobile_pdf', 'mobile_pdf_viewer_shortcode');

/**
 * 添加PDF相关的CSS到前端
 */
function smart_pdf_styles() {
    if (!is_admin()) {
        wp_add_inline_style('wp-block-library', '
            /* PDF Embedder移动端优化 */
            @media (max-width: 768px) {
                .pdfemb-viewer {
                    width: 100% !important;
                    height: auto !important;
                    min-height: 400px !important;
                }
                
                .pdfemb-viewer canvas {
                    max-width: 100% !important;
                    height: auto !important;
                }
            }
        ');
    }
}
add_action('wp_enqueue_scripts', 'smart_pdf_styles');

/**
 * 在管理后台添加使用说明
 */
function smart_pdf_admin_notice() {
    $screen = get_current_screen();
    if ($screen->id === 'plugins') {
        ?>
        <div class="notice notice-info">
            <p>
                <strong>移动端PDF解决方案已激活！</strong> 
                使用 <code>[smart_pdf url="PDF文件URL"]</code> 或 <code>[mobile_pdf url="PDF文件URL"]</code> 
                来获得移动端友好的PDF显示效果。
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'smart_pdf_admin_notice');

/**
 * 创建移动端PDF测试页面（管理员可访问）
 */
function create_mobile_pdf_test_page() {
    if (current_user_can('manage_options') && isset($_GET['mobile_pdf_test'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>移动端PDF测试</title>
        </head>
        <body>
            <h1>移动端PDF显示测试</h1>
            
            <h2>智能PDF显示（推荐）</h2>
            <?php echo do_shortcode('[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]'); ?>
            
            <h2>移动端友好PDF显示</h2>
            <?php echo do_shortcode('[mobile_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]'); ?>
            
            <h2>原始PDF Embedder</h2>
            <?php echo do_shortcode('[pdf-embedder url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]'); ?>
            
        </body>
        </html>
        <?php
        exit;
    }
}
add_action('init', 'create_mobile_pdf_test_page');
?>

<!-- 
使用说明：

1. 将此文件的内容添加到您的主题的 functions.php 文件中

2. 使用新的短代码：
   [smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
   
3. 或者使用移动端友好版本：
   [mobile_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]

4. 访问 yourdomain.com/?mobile_pdf_test=1 查看测试页面（需要管理员权限）

5. 将原来的短代码：
   [pdf-embedder src="..."] 
   替换为：
   [smart_pdf url="..."]
--> 