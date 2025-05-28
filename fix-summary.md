# PDF路径问题修复总结

## 问题描述
用户使用短代码 `[dc_ppt src="wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf"]` 时，在电脑上显示"外部链接无法直接预览"错误。

## 根本原因
在 `wp-content/plugins/dc-media-protect/includes/shortcode.php` 文件中，PDF.js viewer的URL构建使用了错误的函数：

```php
// 错误的代码
$pdfjs_plugin_path = plugin_dir_url(__DIR__) . '../../pdfjs-viewer-shortcode/pdfjs/web/viewer.php';
```

这导致生成了类似这样的错误URL：
```
http://192.168.196.90:8080/wp-content/plugins/dc-media-protect/../../pdfjs-viewer-shortcode/pdfjs/web/viewer.php
```

## 修复方案
将URL构建改为使用 `home_url()` 函数：

```php
// 修复后的代码
$viewer_url = home_url('/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php') . 
             '?file=' . urlencode($processed_src) . 
             '&attachment_id=0' .
             '&dButton=false' .  // 禁用下载按钮
             '&pButton=false' .  // 禁用打印按钮
             '&oButton=false' .  // 禁用打开文件按钮
             '&sButton=true' .   // 保留搜索按钮
             '&pagemode=none' .
             '&_wpnonce=' . $nonce;
```

## 修复结果
现在生成正确的URL：
```
http://192.168.196.90:8080/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php?file=/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf&...
```

## 功能特性
修复后的PDF显示功能包括：
- ✅ 支持相对路径（`wp-content/...`）自动转换为绝对路径
- ✅ 使用PDF.js提供优秀的PDF显示效果
- ✅ 保留水印和版权保护功能
- ✅ 禁用下载、打印、右键等防护措施
- ✅ 移动端优化（微信浏览器、QQ浏览器特殊处理）

## 测试页面
- `http://192.168.196.90:8080/final-test.php` - 最终修复验证
- `http://192.168.196.90:8080/simple-test.php` - 简单测试
- `http://192.168.196.90:8080/path-test.php` - 路径测试

## 状态
🎉 **问题已完全解决** - 用户现在可以正常使用相对路径的PDF短代码了！ 