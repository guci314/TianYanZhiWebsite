<?php
// 管理页面模块

// 添加管理菜单
add_action('admin_menu', 'dcmp_add_admin_menu');
function dcmp_add_admin_menu() {
    add_options_page(
        'DC Media Protect',
        'DC Media Protect',
        'manage_options',
        'dc-media-protect',
        'dcmp_settings_page'
    );
    
    add_management_page(
        '内容采集',
        '内容采集',
        'manage_options',
        'dcmp-content-crawler',
        'dcmp_content_crawler_page'
    );
}

// 设置页面
function dcmp_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('dcmp_watermark_text', sanitize_text_field($_POST['dcmp_watermark_text']));
        update_option('dcmp_watermark_opacity', floatval($_POST['dcmp_watermark_opacity']));
        echo '<div class="notice notice-success"><p>设置已保存！</p></div>';
    }
    
    $watermark_text = get_option('dcmp_watermark_text', '版权所有');
    $watermark_opacity = get_option('dcmp_watermark_opacity', 0.3);
    ?>
    <div class="wrap">
        <h1>DC Media Protect 设置</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">水印文字</th>
                    <td>
                        <input type="text" name="dcmp_watermark_text" value="<?php echo esc_attr($watermark_text); ?>" class="regular-text" />
                        <p class="description">在图片和视频上显示的水印文字</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">水印透明度</th>
                    <td>
                        <input type="range" name="dcmp_watermark_opacity" min="0.1" max="1" step="0.1" value="<?php echo esc_attr($watermark_opacity); ?>" />
                        <span id="opacity-value"><?php echo $watermark_opacity; ?></span>
                        <p class="description">调整水印的透明度（0.1 = 很透明，1.0 = 不透明）</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    document.querySelector('input[name="dcmp_watermark_opacity"]').addEventListener('input', function() {
        document.getElementById('opacity-value').textContent = this.value;
    });
    </script>
    <?php
}

// 内容采集页面
function dcmp_content_crawler_page() {
    ?>
    <div class="wrap">
        <h1>内容采集</h1>
        
        <!-- 自动采集部分 -->
        <div class="dcmp-crawler-section">
            <h2>🤖 自动采集</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">网页URL</th>
                    <td>
                        <input type="url" id="crawl-url" class="regular-text" placeholder="请输入要采集的网页地址" />
                        <p class="description">支持一般网站和微信公众号文章</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">采集选项</th>
                    <td>
                        <label>
                            <input type="checkbox" id="download-images" checked />
                            下载页面图片到本地
                        </label><br/>
                        <label>
                            <input type="checkbox" id="save-as-draft" checked />
                            自动保存为文章草稿
                        </label>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="button" class="button-primary" onclick="startCrawling()">开始采集</button>
            </p>
        </div>
        
        <!-- 手动导入部分 -->
        <div class="dcmp-crawler-section" style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px;">
            <h2>✏️ 手动导入</h2>
            <p class="description">当自动采集失败时（如微信验证、登录限制等），可以手动复制内容进行导入</p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">文章标题</th>
                    <td>
                        <input type="text" id="manual-title" class="regular-text" placeholder="请输入文章标题" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">原文链接</th>
                    <td>
                        <input type="url" id="manual-url" class="regular-text" placeholder="请输入原文链接（可选）" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">文章内容</th>
                    <td>
                        <textarea id="manual-content" rows="10" class="large-text" placeholder="请粘贴文章内容（支持HTML格式）"></textarea>
                        <p class="description">可以直接从浏览器复制内容粘贴，系统会自动处理格式</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">导入选项</th>
                    <td>
                        <label>
                            <input type="checkbox" id="manual-download-images" checked />
                            尝试下载内容中的图片到本地
                        </label><br/>
                        <label>
                            <input type="checkbox" id="manual-save-as-draft" checked />
                            保存为文章草稿
                        </label>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="button" class="button-primary" onclick="startManualImport()">导入内容</button>
            </p>
        </div>
        
        <div id="crawl-result" style="margin-top: 20px;"></div>
    </div>

    <style>
    .dcmp-crawler-section {
        background: #fff;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .dcmp-result {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 15px;
        margin-top: 20px;
        border-radius: 5px;
    }
    .dcmp-success {
        border-left: 4px solid #46b450;
        background: #f7fff7;
    }
    .dcmp-error {
        border-left: 4px solid #dc3232;
        background: #fff7f7;
    }
    .dcmp-loading {
        border-left: 4px solid #0073aa;
        background: #f7faff;
    }
    .dcmp-debug {
        background: #fffaef;
        border: 1px solid #ffb900;
        padding: 10px;
        margin-top: 10px;
        font-size: 12px;
        font-family: monospace;
    }
    </style>

    <script>
    function startCrawling() {
        const url = document.getElementById('crawl-url').value.trim();
        if (!url) {
            alert('请输入要采集的网页地址');
            return;
        }
        
        const downloadImages = document.getElementById('download-images').checked;
        const saveAsDraft = document.getElementById('save-as-draft').checked;
        
        showResult('正在采集中，请稍候...', 'loading');
        
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'dcmp_crawl_content',
                nonce: '<?php echo wp_create_nonce('dcmp_crawl_nonce'); ?>',
                url: url,
                download_images: downloadImages ? '1' : '0',
                save_as_draft: saveAsDraft ? '1' : '0'
            })
        })
        .then(response => response.json())
        .then(data => {
            displayCrawlResult(data);
        })
        .catch(error => {
            showResult('采集失败：' + error.message, 'error');
        });
    }
    
    function startManualImport() {
        const title = document.getElementById('manual-title').value.trim();
        const content = document.getElementById('manual-content').value.trim();
        const url = document.getElementById('manual-url').value.trim();
        const downloadImages = document.getElementById('manual-download-images').checked;
        const saveAsDraft = document.getElementById('manual-save-as-draft').checked;
        
        if (!title && !content) {
            alert('请至少输入标题或内容');
            return;
        }
        
        showResult('正在导入内容，请稍候...', 'loading');
        
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'dcmp_manual_import',
                nonce: '<?php echo wp_create_nonce('dcmp_manual_nonce'); ?>',
                title: title,
                content: content,
                url: url,
                download_images: downloadImages ? '1' : '0',
                save_as_draft: saveAsDraft ? '1' : '0'
            })
        })
        .then(response => response.json())
        .then(data => {
            displayCrawlResult(data);
        })
        .catch(error => {
            showResult('导入失败：' + error.message, 'error');
        });
    }
    
    function displayCrawlResult(data) {
        if (data.error) {
            showResult('❌ ' + data.error.replace(/\\n/g, '<br>'), 'error');
        } else {
            let resultHtml = '<h3>✅ 采集成功</h3>';
            if (data.title) {
                resultHtml += '<p><strong>标题：</strong>' + escapeHtml(data.title) + '</p>';
            }
            if (data.content) {
                const contentPreview = data.content.replace(/<[^>]*>/g, '').substring(0, 200);
                resultHtml += '<p><strong>内容预览：</strong>' + escapeHtml(contentPreview) + '...</p>';
            }
            if (data.images && data.images.length > 0) {
                resultHtml += '<p><strong>下载图片：</strong>' + data.images.length + ' 张</p>';
            }
            if (data.saved_as_draft) {
                resultHtml += '<p><strong>草稿已保存：</strong><a href="' + data.draft_url + '" target="_blank">编辑文章</a></p>';
            }
            
            // 调试信息
            if (data.debug_info) {
                let debugHtml = '<div class="dcmp-debug"><strong>调试信息：</strong><br>';
                for (let key in data.debug_info) {
                    debugHtml += key + ': ' + JSON.stringify(data.debug_info[key]) + '<br>';
                }
                debugHtml += '</div>';
                resultHtml += debugHtml;
            }
            
            showResult(resultHtml, 'success');
        }
    }
    
    function showResult(message, type) {
        const resultDiv = document.getElementById('crawl-result');
        resultDiv.innerHTML = '<div class="dcmp-result dcmp-' + type + '">' + message + '</div>';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    </script>
    <?php
}