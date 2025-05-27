#!/bin/bash

# 修复Docker Compose的texttable模块问题

echo "🔧 修复Docker Compose问题"
echo "========================="

# 检查texttable模块
echo "🔍 检查Python texttable模块..."
python3 -c "import texttable" 2>/dev/null
if [ $? -ne 0 ]; then
    echo "❌ texttable模块缺失，正在安装..."
    
    # 尝试使用pip安装
    if command -v pip3 &> /dev/null; then
        pip3 install texttable
        echo "✅ 使用pip3安装texttable完成"
    elif command -v pip &> /dev/null; then
        pip install texttable
        echo "✅ 使用pip安装texttable完成"
    else
        echo "⚠️  pip未找到，使用apt安装..."
        sudo apt-get update
        sudo apt-get install -y python3-texttable
        echo "✅ 使用apt安装texttable完成"
    fi
else
    echo "✅ texttable模块已存在"
fi

# 测试Docker Compose
echo ""
echo "🧪 测试Docker Compose..."
docker-compose --version
if [ $? -eq 0 ]; then
    echo "✅ Docker Compose工作正常"
    
    # 检查容器状态
    echo ""
    echo "📊 当前容器状态:"
    docker-compose ps
    
    # 如果没有运行，启动WordPress
    if ! docker-compose ps | grep -q "Up"; then
        echo ""
        echo "🚀 启动WordPress容器..."
        docker-compose up -d
        sleep 5
        echo "📊 启动后容器状态:"
        docker-compose ps
    fi
    
else
    echo "❌ Docker Compose仍有问题"
    echo "💡 尝试重新安装Docker Compose:"
    echo "   sudo apt-get remove docker-compose"
    echo "   sudo curl -L \"https://github.com/docker/compose/releases/download/1.29.2/docker-compose-\$(uname -s)-\$(uname -m)\" -o /usr/local/bin/docker-compose"
    echo "   sudo chmod +x /usr/local/bin/docker-compose"
fi

# 检查WordPress访问
echo ""
echo "🌐 检查WordPress访问性..."
for i in {1..3}; do
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8080)
    if [[ "$HTTP_CODE" == "200" || "$HTTP_CODE" == "302" ]]; then
        echo "✅ WordPress正常运行: http://localhost:8080 (状态码: $HTTP_CODE)"
        break
    else
        echo "⏳ 第$i次检查: 状态码 $HTTP_CODE，等待WordPress初始化..."
        sleep 3
    fi
done

# 检查测试页面
echo ""
echo "📱 检查移动端测试页面..."
TEST_URL="http://localhost:8080/wp-content/mobile-debug-test.php"
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$TEST_URL")

if [[ "$HTTP_CODE" == "200" ]]; then
    echo "✅ 测试页面可访问: $TEST_URL"
    
    # 获取IP地址用于手机测试
    LOCAL_IP=$(ip route get 1 | awk '{print $7; exit}' 2>/dev/null || hostname -I | awk '{print $1}')
    echo ""
    echo "📱 移动端测试地址:"
    echo "   桌面端: $TEST_URL"
    echo "   手机端: http://$LOCAL_IP:8080/wp-content/mobile-debug-test.php"
    
else
    echo "❌ 测试页面无法访问 (状态码: $HTTP_CODE)"
    echo "💡 请检查:"
    echo "   1. 文件权限: ls -la wp-content/mobile-debug-test.php"
    echo "   2. WordPress配置是否正确"
    echo "   3. Docker容器是否正常运行"
fi

echo ""
echo "🎉 Docker环境检查完成！" 