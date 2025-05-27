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