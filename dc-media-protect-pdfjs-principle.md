# DC Media Protect 调用 PDF.js 插件的技术原理

## 概述
DC Media Protect 插件通过智能检测和URL构建的方式调用 PDF.js Viewer 插件，实现了高级PDF显示功能与水印保护的完美结合。

## 技术架构图

```
用户请求 [dc_ppt] 短代码
         ↓
DC Media Protect 插件处理
         ↓
检测 PDF.js 插件是否存在
         ↓
    ┌─────────┴─────────┐
    ↓                   ↓
存在PDF.js插件        不存在PDF.js插件
    ↓                   ↓
构建PDF.js URL        使用备用方案
    ↓                   ↓
嵌入iframe + 水印     基础显示 + 水印
```

## 核心技术原理

### 1. 插件检测机制

```php
// 检测PDF.js插件是否存在
$pdfjs_plugin_exists = file_exists(WP_PLUGIN_DIR . '/pdfjs-viewer-shortcode/pdfjs/web/viewer.php');
```

**检测逻辑：**
- 检查文件路径：`/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php`
- 这是PDF.js插件的核心查看器文件
- 如果文件存在，说明PDF.js插件已安装

### 2. URL构建机制

当检测到PDF.js插件存在时，DC Media Protect构建调用URL：

```php
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

**URL参数说明：**
- `file`: PDF文件的路径
- `dButton`: 控制下载按钮显示（false=隐藏）
- `pButton`: 控制打印按钮显示（false=隐藏）
- `oButton`: 控制打开文件按钮显示（false=隐藏）
- `sButton`: 控制搜索按钮显示（true=显示）
- `pagemode`: 页面模式设置
- `_wpnonce`: WordPress安全令牌

### 3. PDF.js插件参数处理

PDF.js插件的 `viewer.php` 文件通过PHP处理这些参数：

```php
// 控制下载按钮显示
<button id="secondaryDownload" <?php if ($_GET["dButton"]!=="true") { echo 'style="display:none;"'; } ?>>

// 控制打印按钮显示  
<button id="secondaryPrint" <?php if ($_GET["pButton"]!=="true") { echo 'style="display:none;"'; }?>>

// 控制打开文件按钮显示
<button id="secondaryOpenFile" <?php if ($_GET["oButton"]!=="true") { echo 'style="display:none;"'; } ?>>

// 控制搜索按钮显示
<button id="viewFind" <?php if (isset($_GET["sButton"]) && $_GET["sButton"]!=="true") { echo 'style="display:none;"'; }?>>
```

### 4. iframe嵌入机制

DC Media Protect将PDF.js查看器嵌入到iframe中：

```php
<iframe src="' . esc_url($viewer_url) . '" 
        width="100%" 
        height="100%" 
        style="border:none; display:block;" 
        title="PDF文档查看器"
        sandbox="allow-same-origin allow-scripts allow-forms"
        oncontextmenu="return false;"
        class="dcmp-pdf-iframe"></iframe>
```

**iframe特性：**
- `sandbox`: 安全沙箱限制
- `oncontextmenu="return false"`: 禁用右键菜单
- 完整的宽高设置

### 5. 水印叠加技术

DC Media Protect在iframe上方叠加水印层：

```php
<div class="dcmp-watermark-overlay" style="
    position:absolute; 
    top:0; left:0; right:0; bottom:0; 
    pointer-events:none; 
    z-index:999999 !important;
    background:repeating-linear-gradient(45deg, transparent, transparent 150px, rgba(0,124,186,0.03) 150px, rgba(0,124,186,0.03) 300px);">
    
    <!-- 多个水印元素 -->
    <div style="position:absolute; top:15px; right:15px; ...">🔒 数字中国</div>
    <div style="position:absolute; bottom:15px; left:15px; ...">版权保护 - 禁止下载</div>
    <!-- ... 更多水印元素 ... -->
</div>
```

**水印技术要点：**
- `z-index:999999`: 确保在最上层
- `pointer-events:none`: 不影响PDF操作
- `position:absolute`: 绝对定位覆盖
- 多层水印元素提供全面保护

## 数据流程

### 1. 请求处理流程

```
1. 用户访问包含 [dc_ppt] 短代码的页面
2. WordPress 解析短代码，调用 dcmp_shortcode_ppt() 函数
3. 函数检测 PDF.js 插件是否存在
4. 如果存在，构建 PDF.js 查看器 URL
5. 生成包含 iframe 和水印的 HTML
6. 浏览器加载 iframe，显示 PDF.js 查看器
7. PDF.js 根据 URL 参数加载指定的 PDF 文件
8. 水印层叠加在 PDF 查看器之上
```

### 2. 文件访问流程

```
PDF文件请求路径：
/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf
         ↓
PDF.js查看器URL：
/wp-content/plugins/pdfjs-viewer-shortcode/pdfjs/web/viewer.php?file=/wp-content/uploads/2025/05/AutoGPT-PDF-Guide-1.pdf
         ↓
iframe嵌入：
<iframe src="查看器URL">
         ↓
水印叠加：
<div class="watermark-overlay">
```

## 安全机制

### 1. 访问控制
- WordPress nonce 验证
- 本地文件检测
- 相对路径处理

### 2. 防护措施
- 禁用下载/打印按钮
- 禁用右键菜单
- 禁用拖拽和选择
- 禁用快捷键（Ctrl+S, Ctrl+P等）

### 3. 水印保护
- 多层水印元素
- 最高z-index优先级
- 时间戳防重用
- 版权声明

## 兼容性处理

### 1. 移动端适配
```php
if ($is_mobile) {
    if ($is_wechat) {
        // 微信浏览器特殊处理
    } else if ($is_qq_browser) {
        // QQ浏览器特殊处理  
    } else {
        // 其他移动端浏览器
    }
}
```

### 2. 备用方案
当PDF.js插件不存在时：
- 桌面端：使用基础iframe显示
- 移动端：显示下载链接界面
- 保持基础水印功能

## 优势分析

### 1. 技术优势
- **无缝集成**：不修改PDF.js插件代码
- **智能检测**：自动适配有无插件环境
- **参数控制**：精确控制PDF.js功能
- **安全隔离**：iframe沙箱机制

### 2. 功能优势
- **最佳体验**：利用PDF.js优秀的显示效果
- **强化保护**：添加多层水印和防护
- **灵活配置**：可控制各种按钮显示
- **跨平台**：支持各种浏览器和设备

### 3. 维护优势
- **松耦合**：两个插件独立更新
- **向后兼容**：PDF.js插件更新不影响功能
- **易扩展**：可以轻松添加新的保护功能

## 总结

DC Media Protect 通过巧妙的插件检测、URL构建、iframe嵌入和水印叠加技术，实现了与PDF.js插件的完美集成。这种设计既保持了PDF.js的优秀显示效果，又增强了文档的安全保护功能，是一个典型的插件协作架构案例。

**核心特点：**
- 🔍 智能检测机制
- 🔗 URL参数传递
- 📱 iframe安全嵌入  
- 🎨 水印叠加保护
- 🛡️ 多重安全防护
- 📱 移动端优化
- 🔄 备用方案支持 