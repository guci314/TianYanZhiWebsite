// 移除可能的双击全屏提示
(function() {
    // 页面加载完成后立即执行
    document.addEventListener('DOMContentLoaded', function() {
        // 移除可能包含"双击PDF全屏"文本的元素
        const removeTooltips = function() {
            // 查找并移除可能的提示元素
            const possibleSelectors = [
                '[title*="双击"]',
                '[data-tooltip*="双击"]',
                '[aria-label*="双击"]',
                '.tooltip:contains("双击")',
                '.tip:contains("双击")',
                '.hint:contains("双击")',
                '[data-hint*="双击"]',
                '[data-tip*="双击"]'
            ];
            
            possibleSelectors.forEach(function(selector) {
                try {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(function(element) {
                        if (element.textContent && element.textContent.includes('双击')) {
                            element.style.display = 'none';
                        }
                        if (element.title && element.title.includes('双击')) {
                            element.title = '';
                        }
                        if (element.getAttribute('aria-label') && element.getAttribute('aria-label').includes('双击')) {
                            element.setAttribute('aria-label', '');
                        }
                    });
                } catch (e) {
                    // 忽略选择器错误
                }
            });
            
            // 移除可能通过JavaScript动态添加的提示
            const allElements = document.querySelectorAll('*');
            allElements.forEach(function(element) {
                if (element.textContent && element.textContent.includes('双击PDF全屏')) {
                    element.style.display = 'none';
                }
            });
        };
        
        // 立即执行一次
        removeTooltips();
        
        // 定期检查（前5秒钟每秒检查一次）
        let checkCount = 0;
        const intervalId = setInterval(function() {
            removeTooltips();
            checkCount++;
            if (checkCount >= 5) {
                clearInterval(intervalId);
            }
        }, 1000);
    });
    
    // 监听DOM变化，移除新添加的提示
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            if (node.textContent && node.textContent.includes('双击PDF全屏')) {
                                node.style.display = 'none';
                            }
                            // 检查子元素
                            const children = node.querySelectorAll ? node.querySelectorAll('*') : [];
                            children.forEach(function(child) {
                                if (child.textContent && child.textContent.includes('双击PDF全屏')) {
                                    child.style.display = 'none';
                                }
                            });
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();

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

// PDF全屏功能
function dcmpEnterFullscreen(iframeId) {
    const iframe = document.getElementById(iframeId);
    if (!iframe) return;
    
    const container = iframe.parentElement;
    
    // 尝试使用浏览器原生全屏API
    if (container.requestFullscreen) {
        container.requestFullscreen().then(() => {
            // 全屏成功后调整iframe大小
            iframe.style.width = '100vw';
            iframe.style.height = '100vh';
            iframe.style.border = 'none';
        });
    } else if (container.webkitRequestFullscreen) {
        container.webkitRequestFullscreen();
        iframe.style.width = '100vw';
        iframe.style.height = '100vh';
        iframe.style.border = 'none';
    } else if (container.mozRequestFullScreen) {
        container.mozRequestFullScreen();
        iframe.style.width = '100vw';
        iframe.style.height = '100vh';
        iframe.style.border = 'none';
    } else {
        // 降级方案：在新窗口中打开
        const iframeSrc = iframe.src;
        window.open(iframeSrc, '_blank', 'width=' + screen.width + ',height=' + screen.height + ',scrollbars=yes,resizable=yes');
    }
}

// 监听全屏状态变化，恢复iframe尺寸
document.addEventListener('fullscreenchange', function() {
    if (!document.fullscreenElement) {
        // 退出全屏时恢复原始大小
        const iframes = document.querySelectorAll('.dcmp-pdf-container iframe');
        iframes.forEach(function(iframe) {
            const container = iframe.parentElement;
            const originalWidth = container.style.width || '800px';
            const originalHeight = container.style.height || '600px';
            iframe.style.width = originalWidth;
            iframe.style.height = originalHeight;
            iframe.style.border = '1px solid #ccc';
        });
    }
});

// 兼容Webkit浏览器
document.addEventListener('webkitfullscreenchange', function() {
    if (!document.webkitFullscreenElement) {
        const iframes = document.querySelectorAll('.dcmp-pdf-container iframe');
        iframes.forEach(function(iframe) {
            const container = iframe.parentElement;
            const originalWidth = container.style.width || '800px';
            const originalHeight = container.style.height || '600px';
            iframe.style.width = originalWidth;
            iframe.style.height = originalHeight;
            iframe.style.border = '1px solid #ccc';
        });
    }
});

// 兼容Mozilla浏览器
document.addEventListener('mozfullscreenchange', function() {
    if (!document.mozFullScreenElement) {
        const iframes = document.querySelectorAll('.dcmp-pdf-container iframe');
        iframes.forEach(function(iframe) {
            const container = iframe.parentElement;
            const originalWidth = container.style.width || '800px';
            const originalHeight = container.style.height || '600px';
            iframe.style.width = originalWidth;
            iframe.style.height = originalHeight;
            iframe.style.border = '1px solid #ccc';
        });
    }
}); 