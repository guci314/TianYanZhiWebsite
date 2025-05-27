<?php
// 临时脚本：强制重新加载DC Media Protect插件菜单
// 使用方法：在浏览器中访问 http://localhost:8080/wp-content/plugins/dc-media-protect/force-reload.php

// 加载WordPress环境
require_once('../../../wp-config.php');

// 检查是否为管理员
if (!current_user_can('manage_options')) {
    die('需要管理员权限');
}

echo '<h1>DC Media Protect 插件状态检查</h1>';

// 检查插件是否激活
$active_plugins = get_option('active_plugins');
$plugin_file = 'dc-media-protect/dc-media-protect.php';

echo '<h2>插件激活状态：</h2>';
if (in_array($plugin_file, $active_plugins)) {
    echo '<p style="color: green;">✅ 插件已激活</p>';
} else {
    echo '<p style="color: red;">❌ 插件未激活</p>';
    echo '<p>尝试激活插件...</p>';
    
    // 强制激活插件
    $active_plugins[] = $plugin_file;
    update_option('active_plugins', $active_plugins);
    
    echo '<p style="color: green;">✅ 插件已强制激活</p>';
}

// 清除缓存
wp_cache_flush();

echo '<h2>菜单注册测试：</h2>';

// 手动加载插件文件
require_once('includes/admin-pages.php');

echo '<p>✅ admin-pages.php 已加载</p>';

echo '<h2>使用说明：</h2>';
echo '<ol>';
echo '<li>刷新WordPress管理页面（Ctrl+F5）</li>';
echo '<li>进入 <strong>工具 → 内容采集</strong></li>';
echo '<li>使用 <strong>✏️ 手动导入</strong> 功能</li>';
echo '</ol>';

echo '<p><a href="/wp-admin/" target="_blank">→ 返回WordPress管理后台</a></p>';
echo '<p><a href="/wp-admin/tools.php?page=dcmp-content-crawler" target="_blank">→ 直接访问内容采集页面</a></p>';
?> 