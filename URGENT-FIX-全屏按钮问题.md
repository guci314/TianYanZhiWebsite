# 🚨 DC Media Protect 全屏按钮无反应 - 紧急修复

## ⚡ 最快解决方案（90%有效）

**直接修改插件主文件，强制更新JavaScript版本号：**

1. 编辑文件：`wp-content/plugins/dc-media-protect/dc-media-protect.php`

2. 找到第33行左右的这行代码：
```php
wp_enqueue_script('dcmp-frontend', plugins_url('assets/js/frontend.js', __FILE__), ['jquery'], '1.0.7', true);
```

3. 将版本号 `'1.0.7'` 改为当前时间戳：
```php
wp_enqueue_script('dcmp-frontend', plugins_url('assets/js/frontend.js', __FILE__), ['jquery'], time(), true);
```

4. 保存文件，刷新页面测试

**原理**：强制浏览器重新加载JavaScript文件，绕过缓存问题

---

## 🔧 应急临时修复

如果上述方法不行，在WordPress主题的 `functions.php` 文件末尾添加：

```php
// DC Media Protect 应急全屏修复
add_action('wp_footer', function() {
?>
<script>
if (typeof window.dcmpOpenProtectedFullscreen === 'undefined') {
    window.dcmpOpenProtectedFullscreen = function(viewerUrl, watermarkText) {
        console.log('🔧 应急修复：全屏功能启动');
        
        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            // 移动端：页面跳转
            var mobileUrl = viewerUrl + (viewerUrl.indexOf('?') !== -1 ? '&mobile=1' : '?mobile=1');
            window.location.href = mobileUrl;
        } else {
            // 桌面端：弹窗
            var newWindow = window.open("", "_blank", 
                "width=" + screen.width + ",height=" + screen.height + 
                ",scrollbars=yes,resizable=yes,toolbar=no,menubar=no,status=no");
            
            if (newWindow) {
                var html = '<!DOCTYPE html><html><head><title>安全PDF查看器 - ' + watermarkText + '</title><meta charset="utf-8"><style>body{margin:0;padding:0;overflow:hidden;background:#f0f0f0;}.container{position:relative;width:100vw;height:100vh;}.pdf-iframe{width:100%;height:100%;border:none;}.exit-btn{position:absolute;top:20px;left:20px;background:#6c757d;color:white;border:none;padding:12px 16px;border-radius:6px;cursor:pointer;z-index:1000;}.watermark{position:absolute;top:20px;right:20px;background:rgba(255,0,0,0.9);color:white;padding:12px 20px;border-radius:8px;z-index:1000;}</style></head><body><div class="container"><iframe src="' + viewerUrl + '" class="pdf-iframe"></iframe><button class="exit-btn" onclick="window.close()">✕ 退出全屏</button><div class="watermark">🔒 ' + watermarkText + '</div></div></body></html>';
                newWindow.document.write(html);
                newWindow.document.close();
            } else {
                alert('无法打开安全查看器窗口\n\n可能被浏览器弹窗拦截器阻止\n请允许此网站的弹窗');
            }
        }
    };
    console.log('✅ 应急全屏函数已定义');
}
</script>
<?php
});
```

---

## 🔍 快速诊断

**1. 浏览器控制台检查**
- 按F12打开开发者工具
- 在Console中输入：`typeof window.dcmpOpenProtectedFullscreen`
- 如果返回 `"undefined"` = JavaScript未加载
- 如果返回 `"function"` = 函数已加载，可能是其他问题

**2. 网络请求检查**
- 开发者工具 → Network标签页
- 刷新页面，查找 `frontend.js` 文件
- 检查状态码是否为200

**3. 弹窗拦截检查**
- 查看地址栏是否有弹窗拦截图标
- 点击允许弹窗

---

## 📋 其他可能解决方案

### 清除缓存
1. **浏览器缓存**：Ctrl+F5 (Windows) 或 Cmd+Shift+R (Mac)
2. **WordPress缓存插件**：清除全部缓存
3. **CDN缓存**：如使用Cloudflare等，清除静态资源缓存

### 检查插件冲突
临时停用以下类型插件：
- 缓存插件（WP Rocket、W3 Total Cache等）
- JavaScript优化插件
- 代码压缩插件

### 文件权限检查
```bash
chmod 755 wp-content/plugins/dc-media-protect/
chmod 644 wp-content/plugins/dc-media-protect/assets/js/frontend.js
```

---

## ✅ 验证修复成功

修复后测试：
1. 点击全屏按钮有响应
2. 桌面端：弹出新窗口显示PDF
3. 移动端：跳转到新页面
4. 浏览器控制台无错误信息

---

## 🆘 如果仍然无法解决

收集以下信息以获得进一步支持：
1. 浏览器控制台错误截图
2. Network标签页中frontend.js的加载状态
3. 服务器环境：PHP版本、WordPress版本
4. 已安装的缓存/优化插件列表

**最常见原因**：服务器或CDN缓存了旧版本的JavaScript文件，第一个解决方案（更新版本号）通常就能解决问题。 