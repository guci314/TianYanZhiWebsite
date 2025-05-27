<?php
// 菜单修复工具
// 访问：http://localhost:8080/wp-content/plugins/dc-media-protect/fix-menu.php

require_once('../../../wp-config.php');

if (!current_user_can('manage_options')) {
    die('需要管理员权限');
}

echo '<h1>🔧 DC Media Protect 菜单修复工具</h1>';

// 处理修复请求
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'fix_menu') {
        echo '<div style="background: #f0f8ff; padding: 15px; border: 1px solid #0073aa; margin: 20px 0;">';
        echo '<h2>正在修复菜单...</h2>';
        
        // 1. 清除所有相关缓存
        wp_cache_flush();
        delete_transient('dcmp_menu_cache');
        
        // 2. 重新激活插件
        $plugin_file = 'dc-media-protect/dc-media-protect.php';
        $active_plugins = get_option('active_plugins');
        
        // 先停用
        $active_plugins = array_diff($active_plugins, array($plugin_file));
        update_option('active_plugins', $active_plugins);
        
        // 再激活
        $active_plugins[] = $plugin_file;
        update_option('active_plugins', $active_plugins);
        
        echo '<p>✅ 插件已重新激活</p>';
        
        // 3. 强制清除菜单缓存
        global $menu, $submenu, $admin_page_hooks;
        $menu = null;
        $submenu = null;
        $admin_page_hooks = array();
        
        echo '<p>✅ 菜单缓存已清除</p>';
        
        // 4. 重新加载菜单
        require_once('includes/admin-pages.php');
        do_action('admin_menu');
        
        echo '<p>✅ 菜单已重新注册</p>';
        echo '<p><strong>请刷新WordPress管理页面查看效果</strong></p>';
        echo '</div>';
    }
}

echo '<h2>📋 当前菜单状态检查</h2>';

// 检查插件激活状态
$active_plugins = get_option('active_plugins');
$plugin_file = 'dc-media-protect/dc-media-protect.php';
$is_active = in_array($plugin_file, $active_plugins);

echo '<p><strong>插件激活状态：</strong>' . ($is_active ? '✅ 已激活' : '❌ 未激活') . '</p>';

// 检查菜单注册
global $submenu;

// 检查设置菜单
$settings_exists = false;
if (isset($submenu['options-general.php'])) {
    foreach ($submenu['options-general.php'] as $item) {
        if (isset($item[2]) && $item[2] === 'dc-media-protect') {
            $settings_exists = true;
            break;
        }
    }
}

// 检查工具菜单  
$tools_exists = false;
if (isset($submenu['tools.php'])) {
    foreach ($submenu['tools.php'] as $item) {
        if (isset($item[2]) && $item[2] === 'dcmp-content-crawler') {
            $tools_exists = true;
            break;
        }
    }
}

echo '<p><strong>设置菜单（设置 → DC Media Protect）：</strong>' . ($settings_exists ? '✅ 已注册' : '❌ 未注册') . '</p>';
echo '<p><strong>工具菜单（工具 → 内容采集）：</strong>' . ($tools_exists ? '✅ 已注册' : '❌ 未注册') . '</p>';

echo '<h2>🔧 修复操作</h2>';

if (!$settings_exists || !$tools_exists) {
    echo '<div style="background: #fff7f7; border-left: 4px solid #dc3232; padding: 15px; margin: 20px 0;">';
    echo '<p><strong>检测到菜单问题！</strong></p>';
    echo '<form method="post">';
    echo '<input type="hidden" name="action" value="fix_menu">';
    echo '<button type="submit" style="background: #dc3232; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; font-size: 16px;">🔧 立即修复菜单</button>';
    echo '</form>';
    echo '</div>';
} else {
    echo '<div style="background: #f7fff7; border-left: 4px solid #46b450; padding: 15px; margin: 20px 0;">';
    echo '<p><strong>✅ 菜单状态正常！</strong></p>';
    echo '</div>';
}

echo '<h2>📱 快捷访问</h2>';
echo '<div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; margin: 20px 0;">';
echo '<p><a href="/wp-admin/options-general.php?page=dc-media-protect" target="_blank" style="display: inline-block; background: #0073aa; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; margin: 5px;">⚙️ 插件设置</a></p>';
echo '<p><a href="/wp-admin/tools.php?page=dcmp-content-crawler" target="_blank" style="display: inline-block; background: #46b450; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; margin: 5px;">📥 内容采集</a></p>';
echo '<p><a href="/wp-admin/" target="_blank" style="display: inline-block; background: #666; color: white; padding: 10px 15px; text-decoration: none; border-radius: 3px; margin: 5px;">🏠 返回管理后台</a></p>';
echo '</div>';

echo '<h2>📝 菜单位置说明</h2>';
echo '<div style="background: #fffaef; border: 1px solid #ffb900; padding: 15px; margin: 20px 0;">';
echo '<ul>';
echo '<li><strong>DC Media Protect</strong> - 应该出现在 <strong>设置</strong> 菜单下</li>';
echo '<li><strong>内容采集</strong> - 应该出现在 <strong>工具</strong> 菜单下</li>';
echo '<li>如果菜单出现在错误位置，请使用上面的修复功能</li>';
echo '</ul>';
echo '</div>';

// 调试信息
echo '<h2>🔍 调试信息</h2>';
echo '<details style="background: #f0f0f0; padding: 10px; margin: 20px 0;">';
echo '<summary>点击查看详细调试信息</summary>';
echo '<h3>活跃插件列表：</h3>';
echo '<pre>';
print_r($active_plugins);
echo '</pre>';

echo '<h3>子菜单数组：</h3>';
echo '<pre>';
print_r($submenu);
echo '</pre>';
echo '</details>';
?> 