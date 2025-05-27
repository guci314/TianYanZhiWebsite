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