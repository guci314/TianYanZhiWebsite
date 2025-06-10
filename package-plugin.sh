#!/bin/bash

# DC Media Protect 插件打包脚本
# 使用方法: ./package-plugin.sh

echo "🚀 开始打包 DC Media Protect 插件..."

# 设置变量
PLUGIN_NAME="dc-media-protect"
PLUGIN_DIR="wp-content/plugins/$PLUGIN_NAME"
PACKAGE_NAME="dc-media-protect-v1.0.0"
TEMP_DIR="/tmp/$PACKAGE_NAME"
CURRENT_DIR=$(pwd)

# 检查插件目录是否存在
if [ ! -d "$PLUGIN_DIR" ]; then
    echo "❌ 错误: 插件目录 $PLUGIN_DIR 不存在"
    exit 1
fi

echo "📁 创建临时目录..."
rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR"

echo "📋 复制插件文件..."
cp -r "$PLUGIN_DIR" "$TEMP_DIR/"

echo "📄 复制文档文件..."
cp "DC-Media-Protect-安装文档.md" "$TEMP_DIR/"
cp "快速安装指南.md" "$TEMP_DIR/"
cp "dc-media-protect-pdfjs-principle.md" "$TEMP_DIR/"

echo "🧹 清理不必要的文件..."
# 删除临时文件和缓存
find "$TEMP_DIR" -name ".DS_Store" -delete
find "$TEMP_DIR" -name "Thumbs.db" -delete
find "$TEMP_DIR" -name "*.tmp" -delete

echo "📦 创建安装包..."
cd /tmp
zip -r "$CURRENT_DIR/$PACKAGE_NAME.zip" "$PACKAGE_NAME/" -x "*.git*" "*.svn*"

echo "🧹 清理临时文件..."
rm -rf "$TEMP_DIR"

cd "$CURRENT_DIR"

echo "✅ 打包完成！"
echo "📦 安装包位置: $CURRENT_DIR/$PACKAGE_NAME.zip"
echo "📊 文件大小: $(du -h "$PACKAGE_NAME.zip" | cut -f1)"

echo ""
echo "📋 安装包内容:"
echo "├── dc-media-protect/                    # 插件主目录"
echo "│   ├── dc-media-protect.php            # 主插件文件"
echo "│   └── includes/                       # 核心功能文件"
echo "│       ├── shortcode.php               # 短代码处理"
echo "│       └── mobile-pdf-viewer.php       # 移动端查看器"
echo "├── DC-Media-Protect-安装文档.md         # 完整安装文档"
echo "├── 快速安装指南.md                     # 5分钟快速安装"
echo "└── dc-media-protect-pdfjs-principle.md # 技术原理文档"

echo ""
echo "🎯 下一步操作:"
echo "1. 将 $PACKAGE_NAME.zip 发送给同事"
echo "2. 同事按照'快速安装指南.md'进行安装"
echo "3. 如有问题，参考'DC-Media-Protect-安装文档.md'"

echo ""
echo "📞 技术支持:"
echo "- 依赖测试页面: /dependency-test.php"
echo "- 水印测试页面: /watermark-test.php"
echo "- 原理演示页面: /pdfjs-principle-demo.php" 