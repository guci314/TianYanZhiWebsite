<?php
// 菜单诊断工具
// 访问：http://localhost:8080/wp-content/plugins/dc-media-protect/debug-menu.php

require_once('../../../wp-config.php');

if (!current_user_can('manage_options')) {
    die('需要管理员权限');
}

echo '<h1>DC Media Protect 菜单诊断工具</h1>';

echo '<h2>1. 插件状态检查</h2>';
$active_plugins = get_option('active_plugins');
$plugin_file = 'dc-media-protect/dc-media-protect.php';
echo '<p>插件文件：' . $plugin_file . '</p>';
echo '<p>激活状态：' . (in_array($plugin_file, $active_plugins) ? '✅ 已激活' : '❌ 未激活') . '</p>';

echo '<h2>2. 当前用户权限检查</h2>';
echo '<p>当前用户：' . wp_get_current_user()->user_login . '</p>';
echo '<p>manage_options权限：' . (current_user_can('manage_options') ? '✅ 有权限' : '❌ 无权限') . '</p>';

echo '<h2>3. 钩子执行测试</h2>';

// 测试 admin_menu 钩子
$admin_menu_called = false;
add_action('admin_menu', function() use (&$admin_menu_called) {
    global $admin_menu_called;
    $admin_menu_called = true;
    echo '<p>✅ admin_menu 钩子已执行</p>';
});

// 强制执行 admin_menu 钩子
do_action('admin_menu');

echo '<p>admin_menu钩子执行：' . ($admin_menu_called ? '✅ 成功' : '❌ 失败') . '</p>';

echo '<h2>4. 手动注册菜单测试</h2>';

// 手动注册菜单并检查
function test_dcmp_add_admin_menu() {
    $options_page = add_options_page(
        'DC Media Protect',
        'DC Media Protect',
        'manage_options',
        'dc-media-protect',
        function() { echo '设置页面测试'; }
    );
    
    $tools_page = add_management_page(
        '内容采集',
        '内容采集',
        'manage_options',
        'dcmp-content-crawler',
        function() { echo '采集页面测试'; }
    );
    
    echo '<p>设置页面注册：' . ($options_page ? '✅ 成功 (' . $options_page . ')' : '❌ 失败') . '</p>';
    echo '<p>工具页面注册：' . ($tools_page ? '✅ 成功 (' . $tools_page . ')' : '❌ 失败') . '</p>';
    
    return array($options_page, $tools_page);
}

$menu_result = test_dcmp_add_admin_menu();

echo '<h2>5. 全局菜单数组检查</h2>';
global $admin_page_hooks, $submenu;

echo '<h3>管理页面钩子：</h3>';
echo '<pre>';
print_r($admin_page_hooks);
echo '</pre>';

echo '<h3>子菜单数组：</h3>';
echo '<pre>';
print_r($submenu);
echo '</pre>';

echo '<h2>6. 直接链接测试</h2>';
echo '<p><a href="/wp-admin/options-general.php?page=dc-media-protect" target="_blank">→ 直接访问设置页面</a></p>';
echo '<p><a href="/wp-admin/tools.php?page=dcmp-content-crawler" target="_blank">→ 直接访问内容采集页面</a></p>';

echo '<h2>7. 解决方案</h2>';
echo '<div style="background: #f0f8ff; padding: 15px; border: 1px solid #0073aa; border-radius: 5px;">';
echo '<h3>方法1：强制重新激活插件</h3>';
echo '<button onclick="forceReactivate()" style="background: #0073aa; color: white; padding: 10px; border: none; border-radius: 3px; cursor: pointer;">强制重新激活插件</button>';

echo '<h3>方法2：清除WordPress缓存</h3>';
echo '<button onclick="clearCache()" style="background: #46b450; color: white; padding: 10px; border: none; border-radius: 3px; cursor: pointer;">清除所有缓存</button>';
echo '</div>';

?>

<script>
function forceReactivate() {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=force_reactivate'
    })
    .then(response => response.text())
    .then(data => {
        alert('插件已重新激活，请刷新WordPress管理页面');
    });
}

function clearCache() {
    fetch('', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=clear_cache'
    })
    .then(response => response.text())
    .then(data => {
        alert('缓存已清除，请刷新WordPress管理页面');
    });
}
</script>

<?php
// 处理AJAX请求
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'force_reactivate') {
        // 重新激活插件
        $active_plugins = get_option('active_plugins');
        $active_plugins = array_diff($active_plugins, array($plugin_file));
        $active_plugins[] = $plugin_file;
        update_option('active_plugins', $active_plugins);
        
        // 触发激活钩子
        do_action('activate_' . $plugin_file);
        
        echo 'OK';
        exit;
    }
    
    if ($_POST['action'] == 'clear_cache') {
        // 清除各种缓存
        wp_cache_flush();
        delete_transient('dcmp_menu_cache');
        
        // 清除对象缓存
        if (function_exists('wp_cache_delete')) {
            wp_cache_delete('active_plugins', 'options');
        }
        
        echo 'OK';
        exit;
    }
}
?> 