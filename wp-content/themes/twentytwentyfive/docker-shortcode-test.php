<?php
/**
 * Docker WordPress 短代码测试
 * 此文件可以直接通过浏览器访问: http://localhost:8080/wp-content/themes/twentytwentyfive/docker-shortcode-test.php
 */

// 设置测试环境变量
$is_docker = true;
$current_dir = __DIR__;

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docker WordPress 短代码测试</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1000px; 
            margin: 0 auto; 
            padding: 20px; 
            line-height: 1.6;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
        }
        .test-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
        }
        .status-good { 
            background-color: #d4edda; 
            border-color: #c3e6cb; 
            color: #155724; 
        }
        .status-bad { 
            background-color: #f8d7da; 
            border-color: #f5c6cb; 
            color: #721c24; 
        }
        .status-warning { 
            background-color: #fff3cd; 
            border-color: #ffeaa7; 
            color: #856404; 
        }
        .code-block {
            background-color: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            margin: 15px 0;
            overflow-x: auto;
        }
        .test-result {
            border: 2px solid #28a745;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            background-color: white;
            min-height: 80px;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px 10px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #005a87;
            color: white;
        }
        .btn-success { background-color: #28a745; }
        .btn-success:hover { background-color: #218838; }
        .diagnostic-info {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐳 Docker WordPress 短代码测试</h1>
        <p>测试Docker环境中的PDF短代码功能</p>
    </div>
    
    <div class="test-section status-good">
        <h2>📋 Docker环境信息</h2>
        <div class="diagnostic-info">
            <p><strong>测试时间:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>当前目录:</strong> <code><?php echo $current_dir; ?></code></p>
            <p><strong>Docker环境:</strong> ✅ 是</p>
            <p><strong>访问URL:</strong> <code>http://localhost:8080/wp-content/themes/twentytwentyfive/docker-shortcode-test.php</code></p>
        </div>
    </div>
    
    <div class="test-section">
        <h2>🔧 立即修复操作</h2>
        
        <h3>步骤1: 清除WordPress缓存</h3>
        <div class="code-block">
# 进入WordPress容器清除缓存
docker exec -it website-wordpress-1 bash -c "
    # 清除对象缓存
    php -r 'if (function_exists(\"wp_cache_flush\")) wp_cache_flush();'
    
    # 清除rewrite规则
    php -r 'flush_rewrite_rules();'
    
    # 清除opcode缓存
    php -r 'if (function_exists(\"opcache_reset\")) opcache_reset();'
"
        </div>
        
        <h3>步骤2: 验证函数文件</h3>
        <div class="code-block">
# 检查functions.php是否正确挂载
docker exec website-wordpress-1 ls -la /var/www/html/wp-content/themes/twentytwentyfive/functions.php

# 检查文件内容（查看最后几行）
docker exec website-wordpress-1 tail -20 /var/www/html/wp-content/themes/twentytwentyfive/functions.php
        </div>
        
        <h3>步骤3: 重新激活主题</h3>
        <div class="code-block">
# 在WordPress容器中重新激活主题
docker exec -it website-wordpress-1 wp theme activate twentytwentyfive --allow-root
        </div>
    </div>
    
    <div class="test-section">
        <h2>🧪 短代码测试方案</h2>
        
        <p>由于Docker环境的特殊性，我们提供以下测试方法：</p>
        
        <h3>方法1: 直接在WordPress后台测试</h3>
        <ol>
            <li>访问 <a href="http://localhost:8080/wp-admin" target="_blank">WordPress后台</a></li>
            <li>创建新文章或编辑现有文章</li>
            <li>在编辑器的"文本"模式下输入短代码</li>
            <li>保存并预览</li>
        </ol>
        
        <div class="code-block">
测试用短代码（复制粘贴到文章中）:

[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]
        </div>
        
        <h3>方法2: 通过WP-CLI验证</h3>
        <div class="code-block">
# 进入容器执行WP-CLI命令
docker exec -it website-wordpress-1 bash

# 在容器内执行：
wp eval 'echo shortcode_exists("smart_pdf") ? "smart_pdf已注册" : "smart_pdf未注册";' --allow-root
wp eval 'echo shortcode_exists("mobile_pdf") ? "mobile_pdf已注册" : "mobile_pdf未注册";' --allow-root
wp eval 'echo function_exists("smart_pdf_embedder_shortcode") ? "函数存在" : "函数不存在";' --allow-root
        </div>
        
        <h3>方法3: 创建测试页面</h3>
        <div class="code-block">
# 通过WP-CLI创建测试页面
docker exec -it website-wordpress-1 wp post create \
  --post_type=page \
  --post_title="PDF测试页面" \
  --post_content='[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]' \
  --post_status=publish \
  --allow-root
        </div>
    </div>
    
    <div class="test-section status-warning">
        <h2>⚠️ 常见问题排查</h2>
        
        <h3>问题1: 短代码显示为文本</h3>
        <p><strong>原因:</strong> 函数未加载或主题缓存问题</p>
        <p><strong>解决:</strong></p>
        <ul>
            <li>重启WordPress容器</li>
            <li>清除所有缓存</li>
            <li>确认在"文本"模式下输入短代码</li>
        </ul>
        
        <h3>问题2: PDF Embedder依赖</h3>
        <p><strong>检查插件状态:</strong></p>
        <div class="code-block">
docker exec -it website-wordpress-1 wp plugin status pdf-embedder --allow-root
docker exec -it website-wordpress-1 wp plugin activate pdf-embedder --allow-root
        </div>
        
        <h3>问题3: 文件权限问题</h3>
        <div class="code-block">
# 修复文件权限
docker exec -it website-wordpress-1 chown -R www-data:www-data /var/www/html/wp-content
docker exec -it website-wordpress-1 chmod -R 755 /var/www/html/wp-content
        </div>
    </div>
    
    <div class="test-section status-good">
        <h2>✅ 验证步骤</h2>
        
        <ol>
            <li><strong>重启容器:</strong> <code>docker restart website-wordpress-1</code></li>
            <li><strong>访问后台:</strong> <a href="http://localhost:8080/wp-admin" target="_blank">http://localhost:8080/wp-admin</a></li>
            <li><strong>激活插件:</strong> 确保PDF Embedder插件已激活</li>
            <li><strong>测试短代码:</strong> 在文章中使用 <code>[smart_pdf url="wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf"]</code></li>
            <li><strong>检查效果:</strong> 桌面端显示PDF，移动端显示预览卡片</li>
        </ol>
    </div>
    
    <div class="action-buttons">
        <a href="http://localhost:8080/wp-admin" class="btn" target="_blank">🔗 打开WordPress后台</a>
        <a href="http://localhost:8080" class="btn btn-success" target="_blank">🌐 查看网站首页</a>
        <a href="javascript:location.reload()" class="btn">🔄 刷新测试页面</a>
    </div>
    
    <div class="test-section">
        <h2>📱 移动端测试</h2>
        <p>在桌面浏览器中使用开发者工具模拟移动设备：</p>
        <ol>
            <li>按F12打开开发者工具</li>
            <li>点击"切换设备模拟"按钮（手机图标）</li>
            <li>选择移动设备型号（如iPhone X）</li>
            <li>刷新页面查看移动端效果</li>
        </ol>
        
        <p><strong>预期效果:</strong></p>
        <ul>
            <li>桌面端：显示完整的PDF嵌入</li>
            <li>移动端：显示点击式PDF预览卡片</li>
        </ul>
    </div>
    
    <div style="text-align: center; margin: 40px 0; padding: 20px; background-color: #e9ecef; border-radius: 8px;">
        <h3>🎯 下一步行动</h3>
        <p>1. 立即在WordPress后台测试短代码</p>
        <p>2. 确认移动端和桌面端的不同显示效果</p>
        <p>3. 如果仍有问题，执行上述排查命令</p>
    </div>
    
</body>
</html> 