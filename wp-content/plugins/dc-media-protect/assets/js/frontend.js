// 禁用右键菜单
window.addEventListener('contextmenu', function(e) {
    e.preventDefault();
}, false);

// 禁用拖拽
window.addEventListener('dragstart', function(e) {
    e.preventDefault();
}, false);

// 禁用复制
window.addEventListener('copy', function(e) {
    e.preventDefault();
}, false);

// PDF.js 移动端支持
(function() {
    // 检查是否需要加载PDF.js
    var needsPdfJs = document.querySelector('.dcmp-pdf-mobile-container canvas');
    
    if (needsPdfJs && typeof pdfjsLib === 'undefined') {
        // 动态加载PDF.js
        var script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
        script.onload = function() {
            // PDF.js加载完成后初始化所有PDF
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            initializePdfViewers();
        };
        document.head.appendChild(script);
    } else if (typeof pdfjsLib !== 'undefined') {
        // PDF.js已存在，直接初始化
        setTimeout(initializePdfViewers, 100);
    }
})();

// 初始化所有PDF查看器
function initializePdfViewers() {
    if (typeof window.dcmpPdfData === 'undefined') return;
    
    Object.keys(window.dcmpPdfData).forEach(function(containerId) {
        var pdfData = window.dcmpPdfData[containerId];
        loadPdf(containerId, pdfData.url);
    });
}

// 加载PDF文档
function loadPdf(containerId, url) {
    if (typeof pdfjsLib === 'undefined') {
        console.error('PDF.js not loaded');
        return;
    }
    
    var canvas = document.getElementById(containerId + '-canvas');
    var pageNumElement = document.getElementById(containerId + '-pagenum');
    var pageCountElement = document.getElementById(containerId + '-pagecount');
    
    if (!canvas) return;
    
    var ctx = canvas.getContext('2d');
    var pdfData = window.dcmpPdfData[containerId];
    
    // 显示加载状态
    ctx.fillStyle = '#f0f0f0';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#333';
    ctx.font = '16px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('加载PDF中...', canvas.width / 2, canvas.height / 2);
    
    pdfjsLib.getDocument(url).promise.then(function(pdf) {
        pdfData.pdfDoc = pdf;
        pdfData.totalPages = pdf.numPages;
        
        if (pageCountElement) {
            pageCountElement.textContent = pdf.numPages;
        }
        
        renderPage(containerId, 1);
    }).catch(function(error) {
        console.error('PDF加载失败:', error);
        ctx.fillStyle = '#f9f9f9';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#d73502';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('PDF加载失败', canvas.width / 2, canvas.height / 2 - 10);
        ctx.fillText('点击右上角按钮在新窗口查看', canvas.width / 2, canvas.height / 2 + 10);
    });
}

// 渲染指定页面
function renderPage(containerId, pageNum) {
    var pdfData = window.dcmpPdfData[containerId];
    if (!pdfData.pdfDoc) return;
    
    var canvas = document.getElementById(containerId + '-canvas');
    var pageNumElement = document.getElementById(containerId + '-pagenum');
    
    if (!canvas) return;
    
    var ctx = canvas.getContext('2d');
    
    pdfData.pdfDoc.getPage(pageNum).then(function(page) {
        // 计算缩放比例以适应canvas大小
        var containerRect = canvas.parentElement.getBoundingClientRect();
        var viewport = page.getViewport({ scale: 1 });
        var scale = Math.min(
            (containerRect.width - 20) / viewport.width,
            (containerRect.height - 60) / viewport.height
        );
        
        var scaledViewport = page.getViewport({ scale: scale });
        
        // 设置canvas尺寸
        canvas.width = scaledViewport.width;
        canvas.height = scaledViewport.height;
        
        // 渲染页面
        var renderContext = {
            canvasContext: ctx,
            viewport: scaledViewport
        };
        
        page.render(renderContext).promise.then(function() {
            pdfData.currentPage = pageNum;
            if (pageNumElement) {
                pageNumElement.textContent = pageNum;
            }
        });
    });
}

// 上一页
function dcmpPrevPage(containerId) {
    var pdfData = window.dcmpPdfData[containerId];
    if (pdfData.currentPage > 1) {
        renderPage(containerId, pdfData.currentPage - 1);
    }
}

// 下一页
function dcmpNextPage(containerId) {
    var pdfData = window.dcmpPdfData[containerId];
    if (pdfData.currentPage < pdfData.totalPages) {
        renderPage(containerId, pdfData.currentPage + 1);
    }
}

// 窗口大小改变时重新渲染当前页面
window.addEventListener('resize', function() {
    if (typeof window.dcmpPdfData === 'undefined') return;
    
    setTimeout(function() {
        Object.keys(window.dcmpPdfData).forEach(function(containerId) {
            var pdfData = window.dcmpPdfData[containerId];
            if (pdfData.pdfDoc && pdfData.currentPage) {
                renderPage(containerId, pdfData.currentPage);
            }
        });
    }, 300);
});

// 移动端触摸手势支持
(function() {
    var startX, startY;
    
    document.addEventListener('touchstart', function(e) {
        if (e.target.closest('.dcmp-pdf-mobile-container canvas')) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }
    });
    
    document.addEventListener('touchend', function(e) {
        var canvas = e.target.closest('.dcmp-pdf-mobile-container canvas');
        if (!canvas || !startX || !startY) return;
        
        var endX = e.changedTouches[0].clientX;
        var endY = e.changedTouches[0].clientY;
        var deltaX = endX - startX;
        var deltaY = endY - startY;
        
        // 检测左右滑动手势
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
            var containerId = canvas.id.replace('-canvas', '');
            
            if (deltaX > 0) {
                // 向右滑动 - 上一页
                dcmpPrevPage(containerId);
            } else {
                // 向左滑动 - 下一页
                dcmpNextPage(containerId);
            }
        }
        
        startX = null;
        startY = null;
    });
})();

// 检查函数是否可用
console.log('🔧 DC Media Protect frontend.js 已加载');

// 全屏保护查看器功能 - 确保全局可用
window.dcmpOpenProtectedFullscreen = function(viewerUrl, watermarkText) {
    console.log("🚀 启动安全全屏查看器...");
    console.log("📥 参数 - viewerUrl:", viewerUrl);
    console.log("📥 参数 - watermarkText:", watermarkText);
    
    // 检测移动设备
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    console.log("📱 设备检测 - 移动端:", isMobile);
    
    if (isMobile) {
        // 移动端：直接跳转到新页面（避免弹窗被拦截）
        console.log("📱 移动端：使用页面跳转方式");
        
        // 添加移动端标识参数
        var mobileParam = viewerUrl.indexOf('?') !== -1 ? '&mobile=1' : '?mobile=1';
        var mobileUrl = viewerUrl + mobileParam;
        
        console.log("📱 跳转到移动端URL:", mobileUrl);
        
        // 直接跳转到新页面
        window.location.href = mobileUrl;
        
        console.log("✅ 移动端页面跳转已执行");
        return;
    }
    
    // 桌面端：使用弹窗方式
    console.log("💻 桌面端：使用弹窗方式");
    var newWindow = window.open("", "_blank", 
        "width=" + screen.width + ",height=" + screen.height + 
        ",scrollbars=yes,resizable=yes,toolbar=no,menubar=no,status=no");
    
    if (newWindow) {
        console.log("✅ 新窗口打开成功");
        
        // 使用模板字符串替代，避免复杂的字符串连接
        var protectedHTML = `
<!DOCTYPE html>
<html>
<head>
    <title>安全PDF查看器 - ${watermarkText}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { margin: 0; padding: 0; overflow: hidden; user-select: none; -webkit-user-select: none; background: #f0f0f0; }
        .protected-container { position: relative; width: 100vw; height: 100vh; background: #f0f0f0; }
        .pdf-iframe { width: 100%; height: 100%; border: none; display: block; }
        .watermark-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: 999999; background: repeating-linear-gradient(45deg, transparent, transparent 150px, rgba(255,0,0,0.02) 150px, rgba(255,0,0,0.02) 300px); }
        .interactive-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1000000; pointer-events: none; }
        .interactive-overlay > * { pointer-events: auto; }
        .watermark-text { position: absolute; top: 20px; right: 20px; background: rgba(255,0,0,0.9); color: white; padding: 12px 20px; border-radius: 8px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 2px solid rgba(255,255,255,0.3); z-index: 1000000; pointer-events: none; }
        .protection-notice { position: absolute; bottom: 20px; left: 20px; background: rgba(255,0,0,0.9); color: white; padding: 8px 16px; border-radius: 6px; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.3); z-index: 1000000; pointer-events: none; }
        .center-watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-15deg); font-size: 72px; color: rgba(255,0,0,0.08); font-weight: bold; pointer-events: none; user-select: none; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); z-index: 1000000; }
        .exit-button { position: absolute; top: 20px; left: 20px; background: #6c757d; color: white; border: none; padding: 12px 16px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: bold; z-index: 1000001; box-shadow: 0 2px 8px rgba(0,0,0,0.3); pointer-events: auto; }
        .exit-button:hover { background: #5a6268; }
    </style>
</head>
<body>
    <div class="protected-container">
        <iframe src="${viewerUrl}" class="pdf-iframe" sandbox="allow-same-origin allow-scripts allow-forms" oncontextmenu="return false;"></iframe>
        
        <!-- 背景水印层（不可交互） -->
        <div class="watermark-overlay">
            <div class="center-watermark">${watermarkText}</div>
        </div>
        
        <!-- 交互元素层 -->
        <div class="interactive-overlay">
            <button class="exit-button" onclick="closeFullscreen()">✕ 退出全屏</button>
            <div class="watermark-text">🔒 ${watermarkText}</div>
            <div class="protection-notice">⚠️ 受保护文档 - 禁止下载</div>
        </div>
    </div>
         <script>
         function closeFullscreen() {
             console.log("🚪 退出按钮被点击了！");
             
             // 检测移动设备
             var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
             console.log("📱 设备类型检测 - 移动端:", isMobile);
             
             if (isMobile) {
                 console.log("📱 移动端退出处理");
                 
                 // 移动端尝试多种方法
                 var success = false;
                 
                 // 方法1: 尝试关闭窗口
                 try {
                     window.close();
                     success = true;
                     console.log("✅ 移动端 window.close() 成功");
                 } catch(e) {
                     console.log("❌ 移动端 window.close() 失败:", e);
                 }
                 
                 // 方法2: 如果无法关闭，显示指导界面
                 setTimeout(function() {
                     if (!success && !window.closed) {
                         console.log("🔄 显示移动端退出指导");
                         
                         // 创建全屏指导覆盖层
                         var overlay = document.createElement("div");
                         overlay.style.cssText = "position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.95);z-index:99999999;display:flex;align-items:center;justify-content:center;flex-direction:column;color:white;font-size:18px;text-align:center;padding:20px;box-sizing:border-box;font-family:Arial,sans-serif;";
                         
                         overlay.innerHTML = "<div style=\\"margin-bottom:30px;font-size:48px;\\">📱</div>" +
                             "<div style=\\"margin-bottom:20px;font-weight:bold;font-size:22px;\\">请手动关闭此页面</div>" +
                             "<div style=\\"margin-bottom:30px;line-height:1.6;color:#ccc;font-size:16px;\\">在手机浏览器中：<br><br>• 点击浏览器的<strong style=\\"color:white\\">返回按钮</strong><br>• 或者<strong style=\\"color:white\\">滑动关闭</strong>此标签页<br>• 或者<strong style=\\"color:white\\">关闭浏览器</strong></div>" +
                             "<button onclick=\\"this.parentNode.remove()\\" style=\\"background:#28a745;color:white;border:none;padding:15px 30px;border-radius:8px;font-size:16px;cursor:pointer;margin-top:20px;\\">我知道了</button>";
                         
                         document.body.appendChild(overlay);
                     }
                 }, 500);
                 
             } else {
                 console.log("💻 桌面端退出处理");
                 try {
                     console.log("🔄 尝试关闭窗口...");
                     window.close();
                     console.log("✅ window.close() 命令已执行");
                 } catch(e) {
                     console.log("❌ 窗口关闭失败:", e);
                     alert("请手动关闭此标签页");
                 }
             }
         }
        
        // ESC键退出全屏
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                console.log("⌨️ ESC键被按下，正在退出全屏");
                closeFullscreen();
            }
        });
        
                 // 防止右键菜单
         document.addEventListener("contextmenu", function(e) {
             e.preventDefault();
             return false;
         });
         
         // 移动端专用：增强退出按钮的触摸支持
         var exitButton = document.querySelector(".exit-button");
         if (exitButton) {
             // 添加触摸事件监听
             exitButton.addEventListener("touchstart", function(e) {
                 console.log("📱 移动端触摸开始");
                 this.style.background = "#5a6268";
                 e.preventDefault(); // 防止触摸事件转换为点击事件
             });
             
             exitButton.addEventListener("touchend", function(e) {
                 console.log("📱 移动端触摸结束，执行退出");
                 this.style.background = "#6c757d";
                 e.preventDefault();
                 closeFullscreen(); // 直接调用退出函数
             });
             
             // 确保移动端也能响应点击
             exitButton.addEventListener("click", function(e) {
                 console.log("📱 移动端点击事件");
                 e.preventDefault();
                 closeFullscreen();
             });
         }
         
         console.log("🔒 受保护的PDF查看器已启动");
    </script>
</body>
</html>`;
        
        newWindow.document.write(protectedHTML);
        newWindow.document.close();
        newWindow.document.title = "安全PDF查看器 - " + watermarkText;
        
        console.log("🔒 受保护的PDF查看器已启动");
        console.log("🆔 新窗口对象:", newWindow);
        
    } else {
        console.warn("❌ 新窗口被阻止");
        alert("❌ 无法打开安全查看器窗口\n\n可能被浏览器的弹窗拦截器阻止了\n请允许此网站的弹窗，然后重试");
    }
}

// 确认函数已全局可用
console.log('🔧 dcmpOpenProtectedFullscreen 函数已定义并设为全局可用');
console.log('🔧 测试函数可用性:', typeof window.dcmpOpenProtectedFullscreen);