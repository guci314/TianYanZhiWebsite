<!DOCTYPE html>
<html>
<head>
    <title>简单调试</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-info { background: #f0f0f0; padding: 15px; margin: 10px 0; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>🔍 简单调试页面</h1>
    
    <div class="debug-info">
        <h3>手动测试全屏按钮HTML</h3>
        <p>以下是手动创建的全屏按钮，用来测试基本功能：</p>
        
        <!-- 手动创建全屏按钮来测试 -->
        <div class="dcmp-fullscreen-toolbar" style="position: relative; margin-bottom: 8px; text-align: right;">
            <div style="display: inline-flex; gap: 8px; background: rgba(0,124,186,0.1); padding: 8px; border-radius: 6px; border: 1px solid rgba(0,124,186,0.2);">
                <button onclick="alert('全屏按钮工作！')" 
                        style="background: #007cba; color: white; border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                    全屏查看
                </button>
                <button onclick="alert('新窗口按钮工作！')" 
                        style="background: #28a745; color: white; border: none; padding: 10px 16px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold; display: flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/>
                    </svg>
                    新窗口
                </button>
            </div>
        </div>
        
        <!-- 测试iframe -->
        <div style="border: 2px solid #007cba; border-radius: 8px; overflow: hidden; width: 600px; height: 400px;">
            <iframe src="http://192.168.29.90:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" 
                    width="100%" height="100%" style="border: none;">
            </iframe>
        </div>
    </div>

    <div class="debug-info">
        <h3>检查清单</h3>
        <p>如果您能看到上面的蓝色和绿色按钮，说明：</p>
        <ul>
            <li>✅ HTML渲染正常</li>
            <li>✅ CSS样式工作</li>
            <li>✅ 按钮点击事件正常</li>
        </ul>
        
        <p>如果PDF能正常显示，说明：</p>
        <ul>
            <li>✅ PDF URL可访问</li>
            <li>✅ iframe加载正常</li>
        </ul>
        
        <p><strong>下一步：</strong>检查为什么WordPress短码没有生成这些按钮。</p>
    </div>

    <script>
    console.log("=== 简单调试页面 ===");
    console.log("如果您能在控制台看到这条消息，JavaScript工作正常");
    
    // 检查按钮是否存在
    setTimeout(function() {
        const buttons = document.querySelectorAll('.dcmp-fullscreen-toolbar button');
        console.log("找到按钮数量:", buttons.length);
        
        if (buttons.length > 0) {
            console.log("✅ 按钮HTML已正确渲染");
            buttons.forEach((btn, index) => {
                console.log("按钮 " + (index + 1) + ":", btn.textContent.trim());
            });
        } else {
            console.error("❌ 没有找到按钮");
        }
    }, 500);
    </script>
</body>
</html>