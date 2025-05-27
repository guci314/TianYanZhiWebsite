#!/bin/bash

# DC Media Protect - Docker WordPress测试环境设置脚本
# 用于解决移动端PDF显示问题

echo "🐳 Docker WordPress 测试环境设置"
echo "=================================="

# 检查当前目录
CURRENT_DIR=$(pwd)
echo "📁 当前目录: $CURRENT_DIR"

# 定位到网站根目录
WEBSITE_ROOT="/home/guci/congqing/website"
if [ ! -d "$WEBSITE_ROOT" ]; then
    echo "❌ 错误: 找不到网站根目录 $WEBSITE_ROOT"
    exit 1
fi

cd "$WEBSITE_ROOT"
echo "📂 切换到网站根目录: $(pwd)"

# 检查docker-compose.yml文件
if [ ! -f "docker-compose.yml" ]; then
    echo "❌ 错误: 找不到 docker-compose.yml 文件"
    exit 1
fi

echo "✅ 找到 docker-compose.yml 文件"

# 检查Docker服务状态
echo ""
echo "🔍 检查Docker服务状态..."
if ! systemctl is-active --quiet docker; then
    echo "⚠️  Docker服务未运行，尝试启动..."
    sudo systemctl start docker
    sleep 3
fi

# 检查Docker Compose是否可用
echo "🔍 检查Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose 未安装或不可用"
    echo "💡 尝试安装: sudo apt-get install docker-compose"
    exit 1
fi

# 停止现有容器
echo ""
echo "🛑 停止现有WordPress容器..."
docker-compose down --remove-orphans

# 启动WordPress容器
echo ""
echo "🚀 启动WordPress Docker容器..."
docker-compose up -d

# 等待容器启动
echo "⏳ 等待容器启动完成..."
sleep 10

# 检查容器状态
echo ""
echo "📊 检查容器状态:"
docker-compose ps

# 检查WordPress可访问性
echo ""
echo "🌐 检查WordPress访问性..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200\|302"; then
    echo "✅ WordPress可访问: http://localhost:8080"
else
    echo "⚠️  WordPress暂时不可访问，可能仍在初始化中"
fi

# 检查插件文件
echo ""
echo "🔍 检查插件文件状态:"
PLUGIN_DIR="$WEBSITE_ROOT/wp-content/plugins/dc-media-protect"
if [ -d "$PLUGIN_DIR" ]; then
    echo "✅ 插件目录存在: $PLUGIN_DIR"
    
    # 检查关键文件
    files_to_check=(
        "dc-media-protect.php"
        "includes/shortcode.php"
        "includes/mobile-pdf-viewer.php"
        "assets/css/style.css"
        "assets/js/frontend.js"
    )
    
    for file in "${files_to_check[@]}"; do
        if [ -f "$PLUGIN_DIR/$file" ]; then
            echo "  ✅ $file"
        else
            echo "  ❌ $file (缺失)"
        fi
    done
else
    echo "❌ 插件目录不存在: $PLUGIN_DIR"
fi

# 检查测试页面
TEST_PAGE="$WEBSITE_ROOT/wp-content/mobile-debug-test.php"
if [ -f "$TEST_PAGE" ]; then
    echo "✅ 测试页面已创建"
else
    echo "❌ 测试页面缺失"
fi

# 获取本机IP地址
echo ""
echo "📱 移动端测试信息:"
LOCAL_IP=$(ip route get 1 | awk '{print $7; exit}' 2>/dev/null || hostname -I | awk '{print $1}')
echo "💻 电脑IP地址: $LOCAL_IP"
echo "🌐 桌面端测试地址: http://localhost:8080/wp-content/mobile-debug-test.php"
echo "📱 手机端测试地址: http://$LOCAL_IP:8080/wp-content/mobile-debug-test.php"

# WordPress管理后台信息
echo ""
echo "🔧 WordPress管理信息:"
echo "📋 管理后台: http://localhost:8080/wp-admin/"
echo "🔌 插件管理: http://localhost:8080/wp-admin/plugins.php"

# 常用Docker命令提示
echo ""
echo "🛠️  常用Docker命令:"
echo "   查看容器状态: docker-compose ps"
echo "   查看容器日志: docker-compose logs wordpress"
echo "   重启容器: docker-compose restart"
echo "   停止容器: docker-compose down"
echo "   完全重建: docker-compose down && docker-compose up -d --build"

# 故障排除提示
echo ""
echo "🔧 故障排除:"
echo "1. 如果端口8080被占用:"
echo "   sudo netstat -tlnp | grep :8080"
echo "   sudo kill -9 <PID>"
echo ""
echo "2. 如果Docker权限问题:"
echo "   sudo usermod -aG docker $USER"
echo "   newgrp docker"
echo ""
echo "3. 如果WordPress无法访问:"
echo "   docker-compose logs wordpress"
echo "   docker-compose restart"

echo ""
echo "🎉 Docker WordPress测试环境设置完成！"
echo "📖 请访问测试页面验证移动端PDF修复功能" 