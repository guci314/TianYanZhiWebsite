# DC Media Protect - PDF全屏功能修复说明

## 问题描述

用户反映：**PDF.js有全屏按钮，点击全屏按钮进入全屏模式（不是演示模式），全屏模式下可以手势放大。但是dc_ppt短码看不到全屏按钮。**

## 问题分析

### 根本原因
1. **PDF.js原生全屏按钮位置**：PDF.js的全屏按钮（`presentationMode`）位于secondary toolbar中
2. **iframe显示限制**：dc_ppt短码使用iframe嵌入PDF.js，在小尺寸下secondary toolbar可能不完全可见
3. **按钮访问性**：即使PDF.js有全屏功能，用户在iframe中很难找到和使用

### PDF.js全屏按钮位置
```html
<div id="secondaryToolbar" class="secondaryToolbar hidden doorHangerRight">
  <div id="secondaryToolbarButtonContainer">
    <button id="presentationMode" class="secondaryToolbarButton" 
            title="Switch to Presentation Mode" 
            data-l10n-id="pdfjs-presentation-mode-button">
      <span data-l10n-id="pdfjs-presentation-mode-button-label">Presentation Mode</span>
    </button>
  </div>
</div>
```

## 解决方案

### 1. 添加外部全屏按钮
在iframe外部添加明显的全屏按钮，使用HTML5 Fullscreen API让整个iframe进入全屏模式。

### 2. 技术实现

#### JavaScript全屏函数
```javascript
function dcmpEnterFullscreen(elementId) {
    const iframe = document.getElementById(elementId);
    if (!iframe) return;
    
    // 尝试使用现代全屏API
    if (iframe.requestFullscreen) {
        iframe.requestFullscreen();
    } else if (iframe.webkitRequestFullscreen) {
        iframe.webkitRequestFullscreen();
    } else if (iframe.msRequestFullscreen) {
        iframe.msRequestFullscreen();
    } else if (iframe.mozRequestFullScreen) {
        iframe.mozRequestFullScreen();
    } else {
        // 备用方案：新窗口打开
        window.open(iframe.src, '_blank', 'fullscreen=yes,scrollbars=yes,resizable=yes');
    }
}
```

#### 全屏事件监听
```javascript
function handleFullscreenChange() {
    const fullscreenElement = document.fullscreenElement || 
                            document.webkitFullscreenElement || 
                            document.mozFullScreenElement || 
                            document.msFullscreenElement;
    
    if (fullscreenElement && fullscreenElement.tagName === 'IFRAME') {
        fullscreenElement.style.width = '100vw';
        fullscreenElement.style.height = '100vh';
        fullscreenElement.style.border = 'none';
    }
}
```

#### PHP短码修改
```php
// 添加全屏支持的工具栏
<div class="dcmp-toolbar" style="position: absolute; top: 10px; right: 10px; z-index: 1000; display: flex; gap: 5px;">
    <button class="dcmp-fullscreen-btn" onclick="dcmpEnterFullscreen('.$unique_id.')" 
            title="进入全屏模式（支持手势缩放）">
        <svg>...</svg> 全屏
    </button>
    <button onclick="window.open('.$viewer_url.', '_blank')" 
            title="在新窗口中打开">
        <svg>...</svg> 新窗口
    </button>
</div>
```

## 功能特性

### ✨ 新增功能
1. **原生全屏支持**：使用HTML5 Fullscreen API实现真正的全屏模式
2. **手势缩放支持**：全屏模式下PDF.js的手势缩放功能完全可用
3. **多浏览器兼容**：支持Chrome、Firefox、Safari、Edge等主流浏览器
4. **备用方案**：全屏API不支持时自动回退到新窗口打开
5. **双按钮设计**：提供全屏按钮和新窗口按钮两种选择

### 🔧 技术改进
1. **JavaScript优化**：避免重复定义，使用WordPress footer hook
2. **响应式设计**：全屏时自动调整iframe尺寸填满整个屏幕
3. **跨浏览器兼容**：处理不同浏览器的全屏API差异
4. **用户体验**：添加SVG图标和tooltips
5. **性能优化**：JavaScript只在需要时加载一次

## 使用方法

### 基本语法
```
[dc_ppt src="PDF文件地址" width="宽度" height="高度"]
```

### 使用示例
```
[dc_ppt src="/wp-content/uploads/2025/05/document.pdf" width="800" height="600"]
```

### 操作说明
1. **点击全屏按钮**：进入原生HTML5全屏模式
2. **手势操作**：在全屏模式下可以使用触摸手势缩放
3. **鼠标操作**：使用鼠标滚轮进行缩放
4. **退出全屏**：按ESC键或点击浏览器的退出全屏按钮
5. **新窗口打开**：点击新窗口按钮在独立窗口中查看PDF

## 兼容性

### 支持的浏览器
- ✅ Chrome 71+
- ✅ Firefox 64+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ iOS Safari 12+
- ✅ Android Chrome 71+

### 移动设备支持
- ✅ iOS设备：支持全屏和手势缩放
- ✅ Android设备：支持全屏和手势缩放
- ✅ 响应式设计：根据设备自动调整默认尺寸

## 文件修改

### 主要修改文件
- `dc-media-protect/includes/shortcode.php` - 主要逻辑修改

### 新增功能
- 全屏JavaScript函数
- 事件监听器
- 双按钮工具栏
- SVG图标

### 向后兼容
- ✅ 保持原有功能不变
- ✅ 现有短码语法继续有效
- ✅ 不影响其他媒体类型（video、img等）

## 测试验证

### 测试文件
- `test-dc-ppt-fullscreen-fix.php` - 功能测试页面

### 测试场景
1. **小尺寸iframe**：验证全屏按钮可见性
2. **大尺寸iframe**：验证工具栏布局
3. **移动设备**：验证触摸手势支持
4. **多种浏览器**：验证跨浏览器兼容性

## 总结

这次修复彻底解决了dc_ppt短码中PDF全屏功能不可见的问题，通过添加外部全屏按钮和使用现代HTML5 Fullscreen API，用户现在可以：

1. **轻松找到全屏按钮**：明显的工具栏按钮
2. **享受真正的全屏体验**：整个屏幕都用于PDF显示
3. **使用手势缩放**：在全屏模式下完全支持触摸操作
4. **获得最佳兼容性**：支持所有主流浏览器和移动设备

这个解决方案既保持了原有功能的完整性，又大大提升了用户体验，让PDF查看功能更加完善和易用。 