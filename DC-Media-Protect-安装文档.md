# DC Media Protect 插件安装文档

## 📋 概述

DC Media Protect 是一个专业的WordPress多媒体保护插件，支持PDF、视频、图片的水印保护和防下载功能。本插件特别针对企业文档保护需求设计，提供多层安全防护机制。

### 🎯 主要功能
- **PDF文档保护**：支持PDF显示、水印叠加、防下载
- **视频流保护**：支持视频播放、水印保护、防下载
- **图片保护**：自动添加水印、防右键下载
- **移动端优化**：特别优化微信、QQ浏览器显示效果
- **智能适配**：自动检测PDF.js插件，提供最佳显示方案

## 📦 安装包内容

```
dc-media-protect/
├── dc-media-protect.php          # 主插件文件
├── includes/
│   ├── shortcode.php             # 短代码处理核心文件
│   └── mobile-pdf-viewer.php     # 移动端PDF查看器
├── assets/
│   ├── css/
│   └── js/
└── README.md                     # 说明文件
```

## 🔧 系统要求

### 基础要求
- **WordPress版本**：5.0 或更高
- **PHP版本**：7.4 或更高
- **MySQL版本**：5.6 或更高
- **服务器**：Apache/Nginx

### 推荐配置
- **内存限制**：至少 256MB
- **文件上传大小**：至少 64MB
- **执行时间**：至少 300秒

### 必需依赖插件
- **PDF.js Viewer (修改版)**：**必须安装**，插件包含自定义全屏功能和消息处理机制

## 📥 安装步骤

### 方法一：WordPress后台安装（推荐）

1. **登录WordPress管理后台**
   ```
   访问：http://您的域名/wp-admin/
   ```

2. **上传插件**
   - 进入 `插件` → `安装插件`
   - 点击 `上传插件`
   - 选择 `dc-media-protect.zip` 文件
   - 点击 `现在安装`

3. **激活插件**
   - 安装完成后点击 `激活插件`
   - 或在 `插件` → `已安装的插件` 中找到并激活

### 方法二：FTP手动安装

1. **解压插件包**
   ```bash
   unzip dc-media-protect.zip
   ```

2. **上传到服务器**
   ```bash
   # 将整个 dc-media-protect 文件夹上传到：
   /wp-content/plugins/dc-media-protect/
   ```

3. **设置权限**
   ```bash
   chmod -R 755 /wp-content/plugins/dc-media-protect/
   chown -R www-data:www-data /wp-content/plugins/dc-media-protect/
   ```

4. **激活插件**
   - 登录WordPress后台
   - 进入 `插件` → `已安装的插件`
   - 找到 "DC Media Protect" 并点击激活

### 方法三：Docker环境安装

如果您使用Docker部署WordPress：

```bash
# 进入WordPress容器
docker exec -it 容器名 bash

# 进入插件目录
cd /var/www/html/wp-content/plugins/

# 解压插件（如果已上传zip文件）
unzip dc-media-protect.zip

# 设置权限
chown -R www-data:www-data dc-media-protect/
chmod -R 755 dc-media-protect/
```

## ⚙️ 配置设置

### 1. 基础配置

插件激活后，进入 `设置` → `DC Media Protect`：

- **水印文本**：设置默认水印文字（如：数字中国）
- **水印位置**：选择水印显示位置
- **防护级别**：设置安全防护强度

### 2. PDF.js插件安装（必需）

**重要：必须使用项目提供的修改版PDF.js插件，标准版本无法正常工作。**

1. **使用项目提供的插件**
   ```
   安装路径：/wp-content/plugins/pdfjs-viewer-shortcode/
   注意：必须使用项目package中的修改版，不是WordPress官方版本
   ```

2. **修改内容说明**
   - 添加了自定义全屏按钮（绿色"🔳 全屏"按钮）
   - 集成了dc_ppt短代码消息处理机制
   - 优化了移动端和微信浏览器兼容性
   - 自定义样式和功能增强

3. **验证安装**
   - 插件激活后，DC Media Protect会自动检测
   - 确认PDF查看器中有绿色全屏按钮
   - 访问测试页面确认功能正常

**⚠️ 警告：使用标准WordPress PDF.js插件将导致功能不完整**

### 3. 数据库配置

插件会自动创建必要的数据库表和选项，无需手动配置。

## 🚀 使用方法

### PDF文档显示

```html
<!-- 基础用法 -->
[dc_ppt src="wp-content/uploads/2025/05/document.pdf"]

<!-- 指定尺寸 -->
[dc_ppt src="wp-content/uploads/2025/05/document.pdf" width="800" height="600"]

<!-- 完整URL -->
[dc_ppt src="http://您的域名/wp-content/uploads/2025/05/document.pdf"]
```

### 视频播放

```html
<!-- 基础用法 -->
[dc_video src="wp-content/uploads/2025/05/video.mp4"]

<!-- 指定尺寸 -->
[dc_video src="wp-content/uploads/2025/05/video.mp4" width="800" height="450"]
```

### 图片保护

```html
<!-- 基础用法 -->
[dc_img src="wp-content/uploads/2025/05/image.jpg"]

<!-- 指定尺寸 -->
[dc_img src="wp-content/uploads/2025/05/image.jpg" width="600" height="400"]
```

## 🔍 功能测试

### 1. 创建测试页面

在WordPress后台创建新页面，添加以下测试内容：

```html
<h2>PDF测试</h2>
[dc_ppt src="wp-content/uploads/2025/05/test.pdf"]

<h2>视频测试</h2>
[dc_video src="wp-content/uploads/2025/05/test.mp4"]

<h2>图片测试</h2>
[dc_img src="wp-content/uploads/2025/05/test.jpg"]
```

### 2. 功能验证清单

- [ ] PDF正常显示
- [ ] 水印正确叠加
- [ ] 右键菜单被禁用
- [ ] 下载按钮被隐藏
- [ ] 移动端显示正常
- [ ] 微信浏览器兼容

## 🛠️ 故障排除

### 常见问题及解决方案

#### 1. PDF显示"外部链接无法直接预览"

**原因**：路径配置问题
**解决**：
```php
// 确保使用正确的路径格式
[dc_ppt src="/wp-content/uploads/2025/05/document.pdf"]
// 或
[dc_ppt src="wp-content/uploads/2025/05/document.pdf"]
```

#### 2. 水印不显示

**原因**：数据库配置问题
**解决**：
```sql
-- 检查数据库中的水印设置
SELECT * FROM wp_options WHERE option_name LIKE '%dcmp%';

-- 重置水印文本
UPDATE wp_options SET option_value = '数字中国' WHERE option_name = 'dcmp_watermark_text';
```

#### 3. 移动端显示异常

**原因**：CSS样式冲突
**解决**：
- 检查主题CSS是否冲突
- 清除浏览器缓存
- 检查移动端特殊处理代码

#### 4. WordPress管理界面空白

**原因**：PHP语法错误
**解决**：
```bash
# 检查PHP错误日志
tail -f /var/log/apache2/error.log

# 检查插件文件语法
php -l wp-content/plugins/dc-media-protect/includes/shortcode.php
```

#### 5. 权限问题

**原因**：文件权限不正确
**解决**：
```bash
# 设置正确的文件权限
chmod -R 755 wp-content/plugins/dc-media-protect/
chown -R www-data:www-data wp-content/plugins/dc-media-protect/
```

## 🔧 高级配置

### 1. 自定义水印样式

编辑 `includes/shortcode.php` 文件，修改水印CSS：

```css
.dcmp-watermark-overlay {
    /* 自定义水印样式 */
    background: your-custom-background;
    color: your-custom-color;
}
```

### 2. 添加新的短代码

在 `dc-media-protect.php` 中注册新的短代码：

```php
add_shortcode('dc_custom', 'dcmp_shortcode_custom');
```

### 3. 移动端特殊处理

修改 `includes/mobile-pdf-viewer.php` 添加新的浏览器支持：

```php
function dcmp_is_custom_browser() {
    return strpos($_SERVER['HTTP_USER_AGENT'], 'CustomBrowser') !== false;
}
```

## 📱 移动端优化

### 微信浏览器
- 自动检测微信环境
- 提供专门的下载界面
- 绿色主题设计

### QQ浏览器
- 自动检测QQ浏览器
- 蓝色主题优化
- 增强兼容性

### 其他移动浏览器
- 响应式设计
- 触摸优化
- 性能优化

## 🔒 安全特性

### 多层防护
1. **界面防护**：禁用右键、拖拽、选择
2. **功能限制**：隐藏下载、打印按钮
3. **水印保护**：多层水印叠加
4. **访问控制**：nonce验证、路径检查

### 防护机制
- iframe沙箱隔离
- JavaScript防护脚本
- CSS样式保护
- 服务器端验证

## 📊 性能优化

### 1. 缓存配置
```php
// 在wp-config.php中添加
define('WP_CACHE', true);
```

### 2. 图片优化
- 使用WebP格式
- 启用图片压缩
- 配置CDN加速

### 3. 数据库优化
```sql
-- 定期清理数据库
OPTIMIZE TABLE wp_options;
```

## 🔄 更新升级

### 自动更新
插件支持WordPress自动更新机制，当有新版本时会自动提示。

### 手动更新
1. 备份当前插件文件
2. 下载新版本
3. 替换插件文件
4. 清除缓存

### 版本兼容性
- 向后兼容旧版本配置
- 自动迁移数据库结构
- 保持API接口稳定

## 📞 技术支持

### 联系方式
- **技术支持**：请联系开发团队
- **文档更新**：定期更新使用文档
- **问题反馈**：提供详细的错误信息

### 调试模式
启用WordPress调试模式：
```php
// 在wp-config.php中添加
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### 日志查看
```bash
# 查看WordPress错误日志
tail -f wp-content/debug.log
```

## 📝 更新日志

### v1.0.0 (当前版本)
- ✅ 基础PDF、视频、图片保护功能
- ✅ 智能PDF.js插件检测和集成
- ✅ 移动端优化（微信、QQ浏览器）
- ✅ 多层水印保护系统
- ✅ 完整的安全防护机制

### 计划功能
- 🔄 批量文件处理
- 🔄 更多视频格式支持
- 🔄 高级水印定制
- 🔄 用户权限管理

## ⚠️ 注意事项

1. **备份重要**：安装前请备份网站数据
2. **测试环境**：建议先在测试环境验证功能
3. **权限设置**：确保文件权限正确配置
4. **插件依赖**：必须使用项目提供的修改版PDF.js插件
5. **插件冲突**：注意与其他PDF查看器插件的兼容性
6. **性能影响**：大文件可能影响页面加载速度
7. **版本匹配**：DC Media Protect与PDF.js插件必须配套使用

## 📄 许可证

本插件遵循 GPL v2 或更高版本许可证。

---

**安装完成后，建议访问以下测试页面验证功能：**
- 依赖测试：`/dependency-test.php`
- 水印测试：`/watermark-test.php`
- 原理演示：`/pdfjs-principle-demo.php`

如有任何问题，请参考故障排除部分或联系技术支持。 