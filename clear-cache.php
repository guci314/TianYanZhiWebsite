<?php
// 清理WordPress缓存脚本
require_once '../../../wp-config.php';
require_once '../../../wp-load.php';

// 清理所有缓存
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ 对象缓存已清理\n";
}

// 清理重写规则
flush_rewrite_rules();
echo "✅ 重写规则已刷新\n";

// 清理插件缓存
delete_transient('plugin_slugs');
delete_site_transient('update_plugins');
echo "✅ 插件缓存已清理\n";

// 强制重新加载插件
if (function_exists('deactivate_plugins') && function_exists('activate_plugin')) {
    deactivate_plugins('dc-media-protect/dc-media-protect.php');
    echo "✅ 插件已停用\n";
    
    activate_plugin('dc-media-protect/dc-media-protect.php');
    echo "✅ 插件已重新激活\n";
}

echo "\n🎉 缓存清理完成！请刷新前台页面测试。\n";
?> 