// ========================================
// 移动端PDF显示解决方案
// ========================================

/**
 * 智能PDF嵌入短代码
 */
if ( ! function_exists( 'smart_pdf_embedder_shortcode' ) ) :
function smart_pdf_embedder_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'width' => 'max',
        'height' => '600',
        'toolbar' => 'none',
        'mobile_text' => '点击查看PDF文档'
    ), $atts);
    
    if (empty($atts['url'])) {
        return '<p style="color: red;">错误：请提供PDF文件URL</p>';
    }
    
    $is_mobile = wp_is_mobile();
    
    if ($is_mobile) {
        return '<div class="mobile-pdf-viewer" onclick="window.open(\'' . esc_url($atts['url']) . '\', \'_blank\')">
                    <div class="pdf-icon">📄</div>
                    <h4>' . esc_html($atts['mobile_text']) . '</h4>
                    <p>点击在新窗口中打开PDF</p>
                </div>
                <style>
                .mobile-pdf-viewer {
                    border: 2px dashed #007cba;
                    border-radius: 12px;
                    padding: 30px 20px;
                    text-align: center;
                    background: #f8f9fa;
                    cursor: pointer;
                    margin: 20px 0;
                    transition: all 0.3s ease;
                }
                .mobile-pdf-viewer:hover {
                    background: #e9ecef;
                    transform: translateY(-2px);
                }
                .pdf-icon { 
                    font-size: 48px; 
                    margin-bottom: 10px; 
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
                    font-size: 14px;
                }
                </style>';
    } else {
        return do_shortcode('[pdf-embedder url="' . $atts['url'] . '" width="' . $atts['width'] . '" toolbar="' . $atts['toolbar'] . '"]');
    }
}
endif;
add_shortcode('smart_pdf', 'smart_pdf_embedder_shortcode'); 