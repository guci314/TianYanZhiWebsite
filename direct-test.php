<!DOCTYPE html>
<html>
<head>
    <title>直接测试</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
    </style>
</head>
<body>
    <h1>🔍 直接测试页面</h1>
    <p><strong>当前时间：</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    <p><strong>这是一个纯PHP页面，不经过WordPress</strong></p>
    
    <div style="background: red; color: white; padding: 20px; margin: 20px 0;">
        <h2>📱 直接全屏按钮测试</h2>
        <p>这里是模拟的全屏按钮，如果您能看到这个，说明页面加载正常。</p>
        
        <button onclick="testDirectFullscreen()" 
                style="background: blue; color: white; border: none; padding: 15px 25px; border-radius: 6px; cursor: pointer; font-size: 16px; margin: 10px;">
            🔳 直接全屏测试
        </button>
        
        <button onclick="alert('直接按钮测试成功！')" 
                style="background: green; color: white; border: none; padding: 15px 25px; border-radius: 6px; cursor: pointer; font-size: 16px; margin: 10px;">
            ✅ 直接测试
        </button>
    </div>
    
    <div style="border: 2px solid blue; padding: 20px; margin: 20px 0;">
        <h3>测试iframe</h3>
        <iframe id="test-iframe" src="http://192.168.29.90:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" 
                width="800" height="600" style="border: 1px solid #ccc;">
        </iframe>
    </div>
    
    <script>
    function testDirectFullscreen() {
        console.log("🚀 直接全屏测试");
        
        const iframe = document.getElementById('test-iframe');
        if (!iframe) {
            alert('❌ 找不到iframe');
            return;
        }
        
        if (iframe.requestFullscreen) {
            iframe.requestFullscreen().then(() => {
                alert('✅ 直接全屏成功！按ESC退出');
            }).catch(err => {
                alert('❌ 全屏失败：' + err.message);
            });
        } else {
            alert('❌ 浏览器不支持全屏API');
        }
    }
    
    console.log('✅ 直接测试页面已加载');
    </script>
</body>
</html>