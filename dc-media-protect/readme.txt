数字中国 DC Media Protect 插件
============================

插件简介：
本插件用于 WordPress 网站的视频、PPT、图片等多媒体内容的安全展示与防下载，支持前端水印、禁用右键、拖拽、复制等防护措施。

插件说明：
- 支持视频、PPT（PDF）、图片、文字的短代码展示
- 所有多媒体内容自动叠加半透明水印，水印内容可后台自定义
- 自动禁用右键、拖拽、复制等操作，提升内容防护
- 后台可自定义水印内容

安装方法：
1. 将 dc-media-protect 目录放入 wp-content/plugins/ 目录下。
2. 在 WordPress 后台"插件"页面启用"DC Media Protect"。
3. 在后台左侧菜单"DC媒体防护"中设置水印内容。

使用方法：
- 在文章或页面中插入以下短代码：
  [dc_video src="视频地址" width="640" height="360"]
  [dc_ppt src="PDF地址" width="800" height="600"]
  [dc_img src="图片地址" width="400" height="300"]
  [dc_text]文字内容[/dc_text]
- 例如：
  [dc_video src="https://www.w3schools.com/html/mov_bbb.mp4"]
  [dc_ppt src="http://localhost:8080/wp-content/uploads/2025/05/How-To-Use-AI-Agents-in-2025.pdf" width="800" height="600"]
  [dc_img src="https://www.w3schools.com/html/pic_trulli.jpg" width="400" height="300"]
  [dc_text]测试文字内容[/dc_text]

width 和 height 参数可选。
不设置时，默认最大宽度 100%。
- 在 WordPress 编辑器中，直接复制上述短代码粘贴到文章或页面内容区域即可。
- 视频、PPT、图片会自动叠加半透明水印，水印内容可在后台自定义。
- 页面自动禁用右键、拖拽、复制等操作。

开发方法：
- 插件目录结构：
  dc-media-protect/
    ├── dc-media-protect.php         # 插件主文件
    ├── includes/                   # 功能模块目录
    │   ├── admin-page.php          # 后台管理页面
    │   ├── ppt-convert.php         # PPT转PDF/图片
    │   ├── shortcode.php           # 短代码注册
    │   ├── upload-handler.php      # 上传处理
    │   └── watermark.php           # 水印处理
    ├── assets/
    │   ├── css/style.css           # 前端样式
    │   └── js/frontend.js          # 前端防护脚本
    └── readme.txt                  # 插件说明文档
- 本地开发建议：
  1. 使用 docker-compose 搭建 WordPress 测试环境（见下方 Docker 配置）
  2. 插件开发调试时，直接修改本地代码，刷新页面即可生效
  3. 推荐使用 VSCode 等编辑器，配合 PHP 插件进行语法检查

Docker 代理服务器配置：
- 如需在国内环境下顺利拉取镜像，可为 Docker 配置代理：
1. 编辑/新建 /etc/systemd/system/docker.service.d/http-proxy.conf，内容如下：
   [Service]
   Environment="HTTP_PROXY=http://127.0.0.1:7890"
   Environment="HTTPS_PROXY=http://127.0.0.1:7890"
   Environment="NO_PROXY=localhost,127.0.0.1"
2. 重载并重启 Docker：
   sudo systemctl daemon-reload
   sudo systemctl restart docker
3. 也可在 daemon.json 配置 registry-mirrors 作为加速器

Docker 启动与访问：
------------------
1. 在插件项目根目录下（包含 docker-compose.yml 文件），运行以下命令启动 WordPress 测试环境：
   docker compose up -d
2. 启动后，浏览器访问网站地址：http://localhost:8080
3. 首次访问请按页面提示完成 WordPress 安装，数据库主机填写 db，数据库名 wpdb，用户名 wpuser，密码 wppass。
4. 进入后台启用插件，体验多媒体防护功能。

常见问题：
1. PDF/PPT 外链无法嵌入？
   解决：请上传 PDF 文件到本地媒体库，使用本地链接。
2. 水印内容如何修改？
   解决：在后台"DC媒体防护"设置页面修改并保存。
3. 插件未生效？
   解决：请确认插件已启用，且前端已加载插件的 JS/CSS。

卸载方法：
- 在 WordPress 后台"插件"页面停用并删除本插件。

