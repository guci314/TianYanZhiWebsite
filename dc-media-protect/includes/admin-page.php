<?php
// 后台管理页面模块

add_action('admin_menu', function() {
    add_menu_page(
        'DC Media Protect',
        'DC媒体防护',
        'manage_options',
        'dcmp-admin',
        'dcmp_admin_page',
        'dashicons-shield-alt',
        80
    );
    
    // 添加内容采集子菜单
    add_submenu_page(
        'dcmp-admin',
        '内容采集',
        '内容采集',
        'manage_options',
        'dcmp-crawler',
        'dcmp_crawler_page'
    );
});

add_action('admin_init', function() {
    register_setting('dcmp_options_group', 'dcmp_watermark_text');
    add_settings_section('dcmp_section', '水印设置', null, 'dcmp-admin');
    add_settings_field(
        'dcmp_watermark_text',
        '水印内容',
        function() {
            $value = esc_attr(get_option('dcmp_watermark_text', '数字中国'));
            echo '<input type="text" name="dcmp_watermark_text" value="' . $value . '" class="regular-text">';
        },
        'dcmp-admin',
        'dcmp_section'
    );
});

function dcmp_admin_page() {
    echo '<div class="wrap"><h1>DC Media Protect 插件设置</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('dcmp_options_group');
    do_settings_sections('dcmp-admin');
    submit_button();
    echo '</form></div>';
}

// 内容采集页面
function dcmp_crawler_page() {
    ?>
    <div class="wrap">
        <h1>内容采集</h1>
        <p>支持采集微信公众号文章、网站页面内容，并自动下载图片到本地。</p>
        
        <form id="dcmp-crawler-form">
            <table class="form-table">
                <tr>
                    <th scope="row">目标URL</th>
                    <td>
                        <input type="url" id="crawl-url" class="regular-text" placeholder="请输入微信公众号文章链接或其他网站URL" required>
                        <p class="description">支持微信公众号文章链接和一般网站页面</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">下载图片</th>
                    <td>
                        <label>
                            <input type="checkbox" id="download-images" checked> 
                            自动下载页面中的图片到本地
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">保存设置</th>
                    <td>
                        <label>
                            <input type="checkbox" id="save-as-draft" checked> 
                            自动保存为WordPress文章草稿
                        </label>
                        <p class="description">选中后，采集到的内容将自动保存为文章草稿，方便后续编辑和发布</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">开始采集</button>
            </p>
        </form>
        
        <div id="crawl-result" style="display:none;">
            <h2>采集结果</h2>
            <div id="result-content"></div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#dcmp-crawler-form').on('submit', function(e) {
            e.preventDefault();
            
            var url = $('#crawl-url').val();
            var downloadImages = $('#download-images').is(':checked');
            var saveAsDraft = $('#save-as-draft').is(':checked');
            
            if (!url) {
                alert('请输入要采集的URL');
                return;
            }
            
            // 显示加载状态
            $('#result-content').html('<p>正在采集内容，请稍候...</p>');
            $('#crawl-result').show();
            
            // 发送AJAX请求
            $.post(ajaxurl, {
                action: 'dcmp_crawl_content',
                nonce: '<?php echo wp_create_nonce('dcmp_crawl_nonce'); ?>',
                url: url,
                download_images: downloadImages,
                save_as_draft: saveAsDraft
            }, function(response) {
                if (response.error) {
                    $('#result-content').html('<div class="notice notice-error"><p>' + response.error + '</p></div>');
                } else {
                    var html = '<h3>标题：' + response.title + '</h3>';
                    html += '<p><strong>原始URL：</strong>' + response.url + '</p>';
                    
                    // 显示草稿保存结果
                    if (response.saved_as_draft && response.draft_id) {
                        html += '<div class="notice notice-success"><p>✅ 内容已保存为文章草稿！</p>';
                        html += '<p><a href="' + response.draft_url + '" target="_blank" class="button button-primary">编辑草稿文章</a></p></div>';
                    }
                    
                    if (response.images && response.images.length > 0) {
                        html += '<h4>下载的图片 (' + response.images.length + '张)：</h4>';
                        html += '<div class="dcmp-images-grid" style="display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:10px;">';
                        response.images.forEach(function(img) {
                            html += '<div><img src="' + img.local_url + '" style="width:100%; max-width:150px; height:auto; border:1px solid #ddd;"><br><small>' + img.original_url + '</small></div>';
                        });
                        html += '</div>';
                    }
                    
                    html += '<h4>内容预览：</h4>';
                    html += '<div style="border:1px solid #ddd; padding:10px; max-height:400px; overflow-y:auto;">' + response.content + '</div>';
                    
                    $('#result-content').html(html);
                }
            }).fail(function() {
                $('#result-content').html('<div class="notice notice-error"><p>采集失败，请检查网络连接或URL是否正确</p></div>');
            });
        });
    });
    </script>
    <?php
}

// 加载必要的脚本
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook == 'dc媒体防护_page_dcmp-crawler') {
        wp_enqueue_script('jquery');
    }
}); 