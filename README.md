# DC Media Protect 插件分发包

## 📋 概述

DC Media Protect 是一个专业的WordPress多媒体保护插件，提供PDF、视频、图片的水印保护和防下载功能。

## 📦 分发包内容

### 核心文件
- `wp-content/plugins/dc-media-protect/` - 插件主目录
  - `dc-media-protect.php` - 主插件文件
  - `includes/shortcode.php` - 短代码处理核心
  - `includes/mobile-pdf-viewer.php` - 移动端查看器

### 文档文件
- `快速安装指南.md` - **⭐ 推荐先看这个**
- `DC-Media-Protect-安装文档.md` - 完整详细的安装文档
- `dc-media-protect-pdfjs-principle.md` - 技术原理说明

### 工具文件
- `package-plugin.sh` - 自动打包脚本
- `README.md` - 本文件

## 🚀 快速开始

### 1. 安装插件（5分钟）

**方法A：WordPress后台安装**
1. 将 `dc-media-protect` 文件夹打包为 zip
2. WordPress后台 → 插件 → 安装插件 → 上传插件
3. 选择zip文件，安装并激活

**方法B：直接复制**
1. 将 `dc-media-protect` 文件夹复制到目标网站的 `/wp-content/plugins/`
2. 设置权限：`chmod -R 755 dc-media-protect/`
3. WordPress后台激活插件

### 2. 安装PDF.js插件（推荐）
- WordPress后台搜索安装 "PDF.js Viewer Shortcode"

### 3. 测试功能
在页面中添加：
```
[dc_ppt src="wp-content/uploads/你的PDF文件.pdf"]
```

## 📖 使用方法

### PDF文档保护
```html
[dc_ppt src="wp-content/uploads/document.pdf" width="800" height="600"]
```

### 视频保护
```html
[dc_video src="wp-content/uploads/video.mp4" width="800" height="450"]
```

### 图片保护
```html
[dc_img src="wp-content/uploads/image.jpg" width="600" height="400"]
```

## 🎯 主要特性

- ✅ **智能PDF显示**：自动检测PDF.js插件，提供最佳显示效果
- ✅ **多层水印保护**：右上角、左下角、中心等多个位置水印
- ✅ **防下载机制**：禁用右键、拖拽、下载按钮
- ✅ **移动端优化**：特别优化微信、QQ浏览器显示
- ✅ **安全防护**：iframe沙箱、nonce验证、路径检查
- ✅ **跨平台兼容**：支持各种浏览器和设备

## 🛠️ 故障排除

### 常见问题
1. **PDF显示"外部链接无法直接预览"**
   - 使用相对路径：`src="wp-content/uploads/file.pdf"`

2. **水印不显示**
   - 检查数据库中的 `dcmp_watermark_text` 选项

3. **移动端显示异常**
   - 清除浏览器缓存，检查CSS冲突

### 测试页面
安装完成后可访问以下测试页面：
- `/dependency-test.php` - 依赖关系测试
- `/watermark-test.php` - 水印功能测试
- `/pdfjs-principle-demo.php` - 技术原理演示

## 📞 技术支持

### 文档优先级
1. **快速问题** → `快速安装指南.md`
2. **详细配置** → `DC-Media-Protect-安装文档.md`
3. **技术原理** → `dc-media-protect-pdfjs-principle.md`

### 调试信息
启用WordPress调试模式：
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 🔄 打包分发

如需重新打包插件：
```bash
chmod +x package-plugin.sh
./package-plugin.sh
```

## 📝 版本信息

- **当前版本**：v1.0.0
- **兼容性**：WordPress 5.0+, PHP 7.4+
- **许可证**：GPL v2+

---

**🎉 安装完成后，您就可以使用专业的多媒体保护功能了！**

如有任何问题，请优先查看对应的文档文件。 