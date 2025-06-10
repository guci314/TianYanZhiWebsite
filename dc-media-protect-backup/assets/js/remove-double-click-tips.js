/**
 * 移除双击PDF全屏提示
 * 这个脚本会在页面加载时自动移除任何包含"双击PDF全屏"的提示信息
 * 特别针对带有💡图标的工具提示
 */

(function() {
    'use strict';
    
    // 要移除的文本模式
    const textPatterns = [
        '双击PDF全屏',
        '双击全屏',
        '双击进入全屏',
        '双击PDF可全屏显示',
        '双击PDF可全屏显示(修复版)',
        'double click fullscreen',
        'double-click fullscreen',
        'dblclick fullscreen'
    ];
    
    // 要检查的属性
    const attributesToCheck = [
        'title',
        'data-tooltip',
        'data-tip',
        'aria-label',
        'data-hint',
        'data-original-title',
        'alt',
        'data-title',
        'data-help',
        'data-info'
    ];
    
    // 带有图标的提示选择器
    const iconTipSelectors = [
        '[class*="hint"]',
        '[class*="tip"]',
        '[class*="tooltip"]',
        '[class*="help"]',
        '[class*="info"]',
        '[class*="guide"]',
        '[class*="notification"]',
        '[class*="popup"]',
        '[class*="bubble"]',
        '[class*="callout"]'
    ];
    
    /**
     * 移除包含指定文本的元素
     */
    function removeElementsWithText() {
        // 遍历所有文本节点
        const walker = document.createTreeWalker(
            document.body,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        
        const textNodesToRemove = [];
        let node;
        
        while (node = walker.nextNode()) {
            const text = node.textContent.trim();
            if (textPatterns.some(pattern => text.includes(pattern))) {
                textNodesToRemove.push(node);
            }
        }
        
        // 移除或隐藏包含目标文本的元素
        textNodesToRemove.forEach(function(textNode) {
            let parent = textNode.parentElement;
            while (parent && parent !== document.body) {
                // 如果父元素只包含这个文本节点，隐藏整个父元素
                if (parent.textContent.trim() === textNode.textContent.trim()) {
                    parent.style.display = 'none';
                    parent.style.visibility = 'hidden';
                    parent.style.opacity = '0';
                    parent.style.pointerEvents = 'none';
                    parent.style.zIndex = '-9999';
                    break;
                }
                parent = parent.parentElement;
            }
        });
    }
    
    /**
     * 移除带有图标的提示元素
     */
    function removeIconTips() {
        // 查找可能包含💡图标的元素
        const elementsWithLightbulb = Array.from(document.querySelectorAll('*')).filter(el => {
            const text = el.textContent || '';
            const innerHTML = el.innerHTML || '';
            return text.includes('💡') || innerHTML.includes('💡') || 
                   innerHTML.includes('&#128161;') || innerHTML.includes('&#x1f4a1;') ||
                   text.includes('bulb') || text.includes('lightbulb');
        });
        
        elementsWithLightbulb.forEach(function(element) {
            const text = element.textContent || '';
            if (textPatterns.some(pattern => text.includes(pattern))) {
                element.style.display = 'none';
                element.style.visibility = 'hidden';
                element.style.opacity = '0';
                element.style.pointerEvents = 'none';
                element.style.zIndex = '-9999';
                // 如果是父容器，也隐藏它
                if (element.parentElement) {
                    const parentText = element.parentElement.textContent || '';
                    if (textPatterns.some(pattern => parentText.includes(pattern))) {
                        element.parentElement.style.display = 'none';
                    }
                }
            }
        });
        
        // 检查可能的提示容器
        iconTipSelectors.forEach(function(selector) {
            try {
                const elements = document.querySelectorAll(selector);
                elements.forEach(function(element) {
                    const text = element.textContent || '';
                    if (textPatterns.some(pattern => text.includes(pattern))) {
                        element.style.display = 'none !important';
                        element.style.visibility = 'hidden !important';
                        element.style.opacity = '0 !important';
                        element.style.pointerEvents = 'none';
                        element.style.zIndex = '-9999';
                        element.remove(); // 直接移除
                    }
                });
            } catch (e) {
                // 忽略选择器错误
            }
        });
    }
    
    /**
     * 移除可能的浮动提示
     */
    function removeFloatingTips() {
        // 查找可能的浮动提示元素
        const floatingElements = document.querySelectorAll([
            '[style*="position: fixed"]',
            '[style*="position: absolute"]',
            '[style*="z-index"]',
            '.floating',
            '.overlay',
            '.modal',
            '.popup',
            '.toast',
            '.notification'
        ].join(', '));
        
        floatingElements.forEach(function(element) {
            const text = element.textContent || '';
            if (textPatterns.some(pattern => text.includes(pattern))) {
                element.style.display = 'none !important';
                element.style.visibility = 'hidden !important';
                element.style.opacity = '0 !important';
                element.style.pointerEvents = 'none';
                element.style.zIndex = '-9999';
                element.remove();
            }
        });
    }
    
    /**
     * 清理元素属性中的双击提示
     */
    function cleanElementAttributes() {
        document.querySelectorAll('*').forEach(function(element) {
            attributesToCheck.forEach(function(attr) {
                const value = element.getAttribute(attr);
                if (value && textPatterns.some(pattern => value.includes(pattern))) {
                    element.removeAttribute(attr);
                }
            });
            
            // 检查内联样式中的content属性
            if (element.style.content) {
                const content = element.style.content;
                if (textPatterns.some(pattern => content.includes(pattern))) {
                    element.style.content = '';
                }
            }
        });
    }
    
    /**
     * 移除可能的CSS伪元素提示
     */
    function addHidingStyles() {
        if (document.getElementById('dcmp-hide-double-click-tips')) {
            return; // 已经添加过了
        }
        
        const style = document.createElement('style');
        style.id = 'dcmp-hide-double-click-tips';
        style.textContent = `
            /* 隐藏可能的双击提示 */
            *[title*="双击"],
            *[data-tooltip*="双击"],
            *[data-tip*="双击"],
            *[aria-label*="双击"],
            .tooltip:contains("双击"),
            .tip:contains("双击"),
            .hint:contains("双击"),
            [class*="hint"]:contains("双击"),
            [class*="tip"]:contains("双击"),
            [class*="tooltip"]:contains("双击"),
            [class*="help"]:contains("双击"),
            [class*="info"]:contains("双击"),
            [class*="guide"]:contains("双击"),
            [class*="notification"]:contains("双击"),
            [class*="popup"]:contains("双击"),
            [class*="bubble"]:contains("双击"),
            [class*="callout"]:contains("双击") {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
                z-index: -9999 !important;
            }
            
            /* 隐藏可能的弹出提示 */
            .tooltip-inner:contains("双击"),
            .popover-content:contains("双击"),
            .ui-tooltip:contains("双击"),
            .tippy-content:contains("双击"),
            .hint--top:contains("双击"),
            .hint--bottom:contains("双击"),
            .hint--left:contains("双击"),
            .hint--right:contains("双击") {
                display: none !important;
            }
            
            /* 隐藏包含💡和双击的元素 */
            *:contains("💡"):contains("双击") {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
            }
            
            /* 隐藏所有可能的双击全屏提示 */
            *:contains("双击PDF全屏"),
            *:contains("双击全屏"),
            *:contains("双击PDF可全屏显示"),
            *:contains("双击PDF可全屏显示(修复版)") {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
                z-index: -9999 !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    /**
     * 主清理函数
     */
    function cleanDoubleClickTips() {
        removeElementsWithText();
        removeIconTips();
        removeFloatingTips();
        cleanElementAttributes();
        addHidingStyles();
    }
    
    /**
     * 监听DOM变化
     */
    function observeChanges() {
        if (window.MutationObserver) {
            const observer = new MutationObserver(function(mutations) {
                let shouldClean = false;
                
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === Node.ELEMENT_NODE || node.nodeType === Node.TEXT_NODE) {
                                const text = node.textContent || '';
                                const innerHTML = node.innerHTML || '';
                                if (textPatterns.some(pattern => text.includes(pattern)) ||
                                    innerHTML.includes('💡')) {
                                    shouldClean = true;
                                }
                            }
                        });
                    }
                    
                    if (mutation.type === 'attributes') {
                        const value = mutation.target.getAttribute(mutation.attributeName) || '';
                        if (textPatterns.some(pattern => value.includes(pattern))) {
                            shouldClean = true;
                        }
                    }
                });
                
                if (shouldClean) {
                    setTimeout(cleanDoubleClickTips, 50);
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: attributesToCheck
            });
        }
    }
    
    /**
     * 初始化
     */
    function init() {
        // 立即执行一次清理
        cleanDoubleClickTips();
        
        // 页面加载完成后再次清理
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', cleanDoubleClickTips);
        }
        
        // 页面完全加载后再次清理
        window.addEventListener('load', cleanDoubleClickTips);
        
        // 定期清理（前10秒钟，每500毫秒检查一次）
        let checkCount = 0;
        const intervalId = setInterval(function() {
            cleanDoubleClickTips();
            checkCount++;
            if (checkCount >= 20) { // 10秒
                clearInterval(intervalId);
                // 开始监听DOM变化
                observeChanges();
            }
        }, 500);
    }
    
    // 启动清理程序
    init();
    
    // 导出到全局作用域（用于调试）
    window.dcmpRemoveDoubleClickTips = cleanDoubleClickTips;
    
    // 在控制台输出调试信息
    console.log('🔧 DC Media Protect: 双击全屏提示移除脚本已启动');
    
})(); 