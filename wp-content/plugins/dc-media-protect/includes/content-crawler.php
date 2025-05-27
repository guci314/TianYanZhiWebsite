<?php
// 内容采集模块

// 采集网页内容并下载图片
function dcmp_crawl_content($url, $download_images = true, $save_as_draft = false) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return array('error' => '无效的URL地址');
    }
    
    // 检测是否为微信公众号链接
    $is_wechat = (strpos($url, 'mp.weixin.qq.com') !== false);
    
    // 随机延迟，模拟人工访问
    if ($is_wechat) {
        sleep(rand(1, 3));
    }
    
    // 针对微信公众号使用更真实的请求头
    if ($is_wechat) {
        // 随机选择User-Agent
        $user_agents = [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) MicroMessenger/8.0.31(0x18001f2f) NetType/WIFI Language/zh_CN',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 15_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) MicroMessenger/8.0.27(0x18001b2c) NetType/4G Language/zh_CN',
            'Mozilla/5.0 (Linux; Android 12; SM-G975F) AppleWebKit/537.36 (KHTML, like Gecko) MicroMessenger/8.0.30.2220(0x28001E3C) NetType/WIFI Language/zh_CN'
        ];
        $selected_ua = $user_agents[array_rand($user_agents)];
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: ' . $selected_ua,
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                    'Accept-Encoding: gzip, deflate, br',
                    'Cache-Control: max-age=0',
                    'sec-ch-ua: "Not_A Brand";v="99", "MicroMessenger";v="8.0"',
                    'sec-ch-ua-mobile: ?1',
                    'sec-ch-ua-platform: "iOS"',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: none',
                    'Sec-Fetch-User: ?1',
                    'Upgrade-Insecure-Requests: 1',
                    'Referer: https://mp.weixin.qq.com/',
                    'Connection: keep-alive'
                ],
                'timeout' => 60,
                'follow_location' => true,
                'max_redirects' => 5
            ]
        ]);
    } else {
        // 一般网站的请求头
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                    'Accept-Encoding: gzip, deflate',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1'
                ],
                'timeout' => 30
            ]
        ]);
    }
    
    // 获取网页内容（支持重试）
    $html = false;
    $retry_count = $is_wechat ? 5 : 3; // 微信链接多重试几次
    
    for ($i = 0; $i < $retry_count; $i++) {
        $html = @file_get_contents($url, false, $context);
        if ($html !== false) {
            break;
        }
        if ($is_wechat) {
            sleep(rand(3, 6)); // 微信重试间隔更长且随机
        } else {
            sleep(2);
        }
    }
    
    if ($html === false) {
        return array('error' => '无法获取网页内容，请检查URL或网络连接。可能原因：1)网站有防爬措施 2)需要登录访问 3)网络问题');
    }
    
    // 检查是否被重定向到验证页面
    if ($is_wechat && (strpos($html, '环境异常') !== false || strpos($html, '完成验证') !== false)) {
        return array(
            'error' => '微信检测到自动化访问，触发了验证机制。解决方案：\n1) 尝试用浏览器先正常访问该链接\n2) 等待一段时间后再重试\n3) 考虑手动复制内容\n4) 使用代理服务器更换IP\n\n注意：微信反爬措施越来越严格，建议优先使用手动方式获取内容。'
        );
    }
    
    // 检测编码并转换
    $encoding = mb_detect_encoding($html, ['UTF-8', 'GBK', 'GB2312', 'ISO-8859-1'], true);
    if ($encoding && $encoding !== 'UTF-8') {
        $html = mb_convert_encoding($html, 'UTF-8', $encoding);
    }
    
    // 创建DOMDocument解析HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();
    
    $result = array(
        'title' => '',
        'content' => '',
        'images' => array(),
        'url' => $url,
        'debug_info' => array()
    );
    
    // 提取标题
    $titles = $dom->getElementsByTagName('title');
    if ($titles->length > 0) {
        $result['title'] = trim($titles->item(0)->textContent);
    }
    
    // 微信公众号专用选择器（更全面）
    if ($is_wechat) {
        $content_selectors = [
            '//div[@id="js_content"]',
            '//div[@class="rich_media_content"]',
            '//div[contains(@class,"rich_media_content")]',
            '//div[@id="page-content"]',
            '//div[contains(@class,"weui-article")]',
            '//section[contains(@class,"main")]',
            '//div[contains(@class,"msg_content")]'
        ];
    } else {
        // 一般网站选择器
        $content_selectors = [
            '//article',
            '//div[contains(@class,"content")]',
            '//div[contains(@class,"article")]',
            '//div[contains(@class,"post")]',
            '//div[contains(@class,"entry")]',
            '//main',
            '//div[@id="content"]',
            '//div[@class="content"]'
        ];
    }
    
    $xpath = new DOMXPath($dom);
    $content_node = null;
    $used_selector = '';
    
    foreach ($content_selectors as $selector) {
        $nodes = $xpath->query($selector);
        if ($nodes->length > 0) {
            $content_node = $nodes->item(0);
            $used_selector = $selector;
            break;
        }
    }
    
    // 添加调试信息
    $result['debug_info']['is_wechat'] = $is_wechat;
    $result['debug_info']['used_selector'] = $used_selector;
    $result['debug_info']['html_length'] = strlen($html);
    $result['debug_info']['encoding'] = $encoding ?: 'UTF-8';
    $result['debug_info']['title_from_html'] = $result['title'];
    
    if ($content_node) {
        // 清理不需要的元素
        $remove_tags = ['script', 'style', 'iframe', 'embed', 'object'];
        foreach ($remove_tags as $tag) {
            $elements = $content_node->getElementsByTagName($tag);
            $to_remove = [];
            foreach ($elements as $element) {
                $to_remove[] = $element;
            }
            foreach ($to_remove as $element) {
                if ($element->parentNode) {
                    $element->parentNode->removeChild($element);
                }
            }
        }
        
        $result['content'] = $dom->saveHTML($content_node);
        
        // 下载图片
        if ($download_images) {
            $result['images'] = dcmp_download_images_from_content($content_node, $url);
            // 更新内容中的图片链接为本地链接
            $result['content'] = $dom->saveHTML($content_node);
        }
        
        // 保存为WordPress草稿
        if ($save_as_draft) {
            $draft_result = dcmp_save_as_draft($result);
            if (isset($draft_result['error'])) {
                $result['draft_error'] = $draft_result['error'];
            } else {
                $result['draft_id'] = $draft_result['post_id'];
                $result['draft_url'] = $draft_result['edit_url'];
                $result['saved_as_draft'] = true;
            }
        }
    } else {
        $result['error'] = '未能找到页面主要内容。可能原因：1)页面结构特殊 2)需要JavaScript渲染 3)内容需要登录访问';
        
        // 如果是微信文章，提供额外说明
        if ($is_wechat) {
            $result['error'] .= '\n\n微信公众号文章采集提示：\n1) 确保文章链接可以在浏览器中正常打开\n2) 避免采集需要关注才能查看的文章\n3) 有些文章可能有防采集保护\n4) 如遇"环境异常"，建议手动复制内容';
        }
    }
    
    return $result;
}

// 保存采集内容为WordPress文章草稿
function dcmp_save_as_draft($crawl_result) {
    $post_title = !empty($crawl_result['title']) ? $crawl_result['title'] : '采集文章 - ' . date('Y-m-d H:i:s');
    
    // 在内容末尾添加来源信息
    $post_content = $crawl_result['content'];
    $post_content .= "\n\n<hr>\n<p><strong>内容来源：</strong><a href=\"" . esc_url($crawl_result['url']) . "\" target=\"_blank\">" . esc_html($crawl_result['url']) . "</a></p>";
    $post_content .= "\n<p><strong>采集时间：</strong>" . date('Y-m-d H:i:s') . "</p>";
    
    if (!empty($crawl_result['images'])) {
        $post_content .= "\n<p><strong>下载图片：</strong>" . count($crawl_result['images']) . " 张</p>";
    }
    
    // 创建文章草稿
    $post_data = array(
        'post_title'    => wp_strip_all_tags($post_title),
        'post_content'  => $post_content,
        'post_status'   => 'draft',
        'post_type'     => 'post',
        'post_category' => array(1), // 默认分类
        'meta_input'    => array(
            'dcmp_crawled_url' => $crawl_result['url'],
            'dcmp_crawled_time' => current_time('mysql'),
            'dcmp_crawled_images' => maybe_serialize($crawl_result['images'])
        )
    );
    
    $post_id = wp_insert_post($post_data);
    
    if (is_wp_error($post_id)) {
        return array('error' => '保存草稿失败：' . $post_id->get_error_message());
    }
    
    return array(
        'post_id' => $post_id,
        'edit_url' => admin_url('post.php?post=' . $post_id . '&action=edit'),
        'preview_url' => get_permalink($post_id) . '&preview=true'
    );
}

// 从内容中下载图片
function dcmp_download_images_from_content($content_node, $base_url) {
    $images = array();
    $img_tags = $content_node->getElementsByTagName('img');
    
    $upload_dir = wp_upload_dir();
    $dcmp_dir = $upload_dir['basedir'] . '/dcmp-crawled/';
    $dcmp_url = $upload_dir['baseurl'] . '/dcmp-crawled/';
    
    // 创建存储目录
    if (!file_exists($dcmp_dir)) {
        wp_mkdir_p($dcmp_dir);
    }
    
    foreach ($img_tags as $img) {
        $src = $img->getAttribute('src');
        if (empty($src)) continue;
        
        // 处理相对URL
        if (strpos($src, 'http') !== 0) {
            if (strpos($src, '//') === 0) {
                $src = 'https:' . $src;
            } else {
                $src = rtrim($base_url, '/') . '/' . ltrim($src, '/');
            }
        }
        
        // 下载图片
        $downloaded = dcmp_download_image($src, $dcmp_dir);
        if ($downloaded) {
            $images[] = array(
                'original_url' => $src,
                'local_path' => $downloaded['path'],
                'local_url' => $dcmp_url . $downloaded['filename']
            );
            
            // 更新HTML中的图片地址为本地地址
            $img->setAttribute('src', $dcmp_url . $downloaded['filename']);
        }
    }
    
    return $images;
}

// 下载单个图片
function dcmp_download_image($url, $target_dir) {
    // 对微信图片使用特殊的请求头
    $is_wechat_img = (strpos($url, 'mmbiz.qpic.cn') !== false);
    
    if ($is_wechat_img) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) MicroMessenger/8.0.27',
                    'Referer: https://mp.weixin.qq.com/'
                ],
                'timeout' => 30
            ]
        ]);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'timeout' => 30
            ]
        ]);
    }
    
    $image_data = @file_get_contents($url, false, $context);
    if ($image_data === false) {
        return false;
    }
    
    // 生成文件名
    $filename = time() . '_' . uniqid() . '_' . basename(parse_url($url, PHP_URL_PATH));
    if (empty(pathinfo($filename, PATHINFO_EXTENSION))) {
        $filename .= '.jpg'; // 默认扩展名
    }
    $filepath = $target_dir . $filename;
    
    // 保存文件
    if (file_put_contents($filepath, $image_data)) {
        return array(
            'filename' => $filename,
            'path' => $filepath
        );
    }
    
    return false;
}

// AJAX处理采集请求
add_action('wp_ajax_dcmp_crawl_content', 'dcmp_handle_crawl_request');
function dcmp_handle_crawl_request() {
    check_ajax_referer('dcmp_crawl_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('权限不足');
    }
    
    $url = sanitize_url($_POST['url']);
    $download_images = isset($_POST['download_images']) ? (bool)$_POST['download_images'] : true;
    $save_as_draft = isset($_POST['save_as_draft']) ? (bool)$_POST['save_as_draft'] : false;
    
    $result = dcmp_crawl_content($url, $download_images, $save_as_draft);
    
    wp_send_json($result);
}

// AJAX处理手动导入请求
add_action('wp_ajax_dcmp_manual_import', 'dcmp_handle_manual_import');
function dcmp_handle_manual_import() {
    check_ajax_referer('dcmp_manual_nonce', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_die('权限不足');
    }
    
    $title = sanitize_text_field($_POST['title']);
    $content = wp_kses_post($_POST['content']);
    $url = sanitize_url($_POST['url']);
    $download_images = isset($_POST['download_images']) ? (bool)$_POST['download_images'] : false;
    $save_as_draft = isset($_POST['save_as_draft']) ? (bool)$_POST['save_as_draft'] : false;
    
    $result = array(
        'title' => $title,
        'content' => $content,
        'url' => $url,
        'images' => array()
    );
    
    // 如果需要下载图片，处理内容中的图片
    if ($download_images && !empty($content)) {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();
        
        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body) {
            $result['images'] = dcmp_download_images_from_content($body, $url ?: 'manual');
            $result['content'] = $dom->saveHTML($body);
        }
    }
    
    // 保存为草稿
    if ($save_as_draft) {
        $draft_result = dcmp_save_as_draft($result);
        if (isset($draft_result['error'])) {
            $result['draft_error'] = $draft_result['error'];
        } else {
            $result['draft_id'] = $draft_result['post_id'];
            $result['draft_url'] = $draft_result['edit_url'];
            $result['saved_as_draft'] = true;
        }
    }
    
    wp_send_json($result);
} 