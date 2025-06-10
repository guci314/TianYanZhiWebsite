# DC Media Protect 插件架构

## 1. 概述

DC Media Protect 是一款WordPress插件，设计用于通过添加水印和阻止下载来保护多媒体内容（PDF、视频、图片）。其架构围绕WordPress钩子（hooks）、用于内容显示的短代码（shortcodes）以及结合服务器端PHP和客户端HTML/CSS/JavaScript的渲染与保护机制。一个关键特性是它集成了经过修改的PDF.js查看器以保护PDF，并为各种移动设备和浏览器提供了广泛的备用方案。

## 2. 主要架构组件

### 2.1. 插件核心 (`dc-media-protect.php`)

*   **职责:**
    *   WordPress加载时初始化插件。
    *   处理插件激活和停用钩子（例如，刷新重写规则）。
    *   包含 `includes/` 目录中的所有其他PHP模块文件。
    *   加载全局前端CSS (`assets/css/style.css`) 和JavaScript (`assets/js/frontend.js`) 资源。
    *   在WordPress后台上下文中，有条件地加载后台特定功能 (`includes/admin-pages.php`)。
*   **交互:**
    *   作为注册到WordPress的主要入口点。
    *   加载并协调所有其他插件组件。

### 2.2. 短代码引擎 (`includes/shortcode.php`)

*   **职责:**
    *   通过短代码定义和处理核心面向用户的功能：
        *   `[dc_video src="..."]`: 显示带有HTML叠加水印的视频播放器。
        *   `[dc_img src="..."]`: 显示带有HTML叠加水印的图片。
        *   `[dc_ppt src="..."]`: 最复杂的短代码，用于显示PDF。其职责包括：
            *   解析 `src`、`width` 和 `height` 属性。
            *   将相对PDF路径转换为绝对URL。
            *   检测PDF源是本地还是外部。
            *   **主要PDF查看方式 (本地PDF):**
                *   检查 "PDF.js Viewer Shortcode" 插件是否存在。
                *   构建指向其 `viewer.php` 的URL，并将其嵌入到 `<iframe>` 中。
                *   传递参数以禁用PDF.js原生的下载、打印和打开按钮。
                *   添加一个自定义的“全屏显示”按钮，该按钮会在新的最大化窗口中打开PDF.js查看器。
            *   **HTML/CSS/JS叠加保护 (通过PDF.js处理PDF时):**
                *   生成多个使用CSS样式化的HTML `<div>` 元素，层叠在PDF iframe之上。这些元素显示各种水印（文本、版权声明、时间戳、大的居中水印文本）和图案背景。
                *   嵌入内联JavaScript以禁用PDF容器内的上下文菜单、拖放、文本选择和特定键盘快捷键（Ctrl+S、Ctrl+P、Ctrl+A、F12），以阻止下载/复制。
            *   如果未找到 "PDF.js Viewer Shortcode" 插件或针对特定的非合作型移动浏览器，则提供备用机制（委托给移动PDF显示引擎）。
            *   通过显示直接打开链接的方式处理外部PDF。
    *   使用 `dcmp_get_watermark_text()` (从WordPress选项中读取) 检索水印文本。
    *   包含用于设备/浏览器检测的实用函数 (`dcmp_is_mobile_device`, `dcmp_is_wechat_browser`, `dcmp_is_qq_browser`)。
*   **交互:**
    *   向WordPress注册短代码。
    *   向前端输出HTML、CSS和JavaScript。
    *   依赖 `移动PDF显示引擎` 提供某些PDF渲染的备用方案。
    *   其主要的PDF显示方法依赖于 "PDF.js Viewer Shortcode" 插件的存在。

### 2.3. 移动PDF显示引擎 (`includes/mobile-pdf-viewer.php`)

*   **职责:**
    *   为各种移动浏览器提供优化的或备用的PDF查看策略，尤其是在 `shortcode.php` 中的主要PDF.js iframe方法不适用或失败时。
    *   执行细粒度的移动设备和浏览器检测（iOS Safari、Firefox、MIUI上的微信、通用微信、小米浏览器、UC浏览器、QQ浏览器、华为浏览器、搜狗浏览器）。
    *   根据检测到的环境生成定制的HTML输出：
        *   为功能更强大的移动浏览器（如iOS Safari、Firefox）提供简单的iframe。
        *   为限制性环境（如微信）提供带有链接/按钮的自定义用户界面（例如，“在浏览器中打开”、“复制链接”、“使用其他应用打开”）。
        *   一种针对Android Chrome的实验性方法，该方法准备一个 `<canvas>` 元素和一个 `window.dcmpPdfData` JavaScript对象，推测用于与一个独立的PDF.js渲染脚本配合使用（尽管该脚本本身不在此PHP文件中）。
    *   包含一个AJAX端点 (`dcmp_ajax_get_pdf_info`) 用于获取PDF元数据（内容类型、大小），可能用于客户端验证，尽管PHP生成的视图中未明确调用此端点。
*   **交互:**
    *   主要由 `shortcode.php` 中的 `[dc_ppt]` 短代码逻辑作为备用方案调用。
    *   输出特定于移动PDF查看的HTML。

### 2.4. 水印系统

*   **HTML/CSS叠加水印 (在 `includes/shortcode.php` 中实现):**
    *   **职责:** 动态生成带有CSS样式的HTML `<div>` 元素以创建可见水印。这些水印在浏览器中层叠在媒体内容（PDF、视频、图片）之上。对于通过PDF.js显示的PDF，这是一个包含各种文本元素和背景图案的多层系统。对于视频和图片，则是较简单的文本叠加。
    *   **关键特性:** 水印并非嵌入媒体文件本身，而是渲染的HTML页面的一部分。
    *   **交互:** 逻辑与短代码渲染功能紧密耦合。
*   **水印配置 (通过 `get_option('dcmp_watermark_text')`):**
    *   **职责:** 实际的水印文本从WordPress选项 (`dcmp_watermark_text`) 中检索，该选项大概是通过后台界面设置的。
    *   **交互:** `短代码引擎` 读取此选项。
*   **基于文件的水印占位符 (`includes/watermark.php`):**
    *   **职责:** 包含占位符函数 (`dcmp_add_image_watermark`, `dcmp_add_pdf_watermark`)，旨在将来实现直接修改图像和PDF文件以嵌入水印的功能。
    *   **关键特性:** 此组件目前**未实现**。
    *   **交互:** 在当前状态下无交互。

### 2.5. 前端资源 (`assets/`)

*   **`assets/css/style.css`:**
    *   **职责:** 为插件在前端的输出提供通用CSS规则。这可能包括容器样式、基本水印外观（尽管许多PDF水印样式是内联在 `shortcode.php` 中的）。
*   **`assets/js/frontend.js`:**
    *   **职责:** 为潜在的全局客户端交互而加载。虽然PDF保护的JavaScript（上下文菜单、键盘快捷键）目前是内联在 `shortcode.php` 中的，但此文件可用于其他增强功能。它可能旨在与 `mobile-pdf-viewer.php` 中的Android PDF.js 画布渲染设置配合使用。
*   **交互:** 这两个文件都由 `插件核心` 加载并在前端页面上使用。

### 2.6. 后台管理界面 (`includes/admin-pages.php`)

*   **(基于文件名和典型插件结构推断；此轮未完全分析其内容)*
*   **职责:** 预期用于在WordPress后台区域创建和管理设置页面。这可能包括水印文本 (`dcmp_watermark_text`) 的设置以及其他可能的插件配置。
*   **交互:** 与WordPress后台菜单系统挂钩。将选项保存到WordPress数据库，然后由其他组件（例如 `短代码引擎` 读取水印文本）读取。

### 2.7. 辅助工具 (`includes/`)

*   **上传处理器 (`includes/upload-handler.php`):**
    *   **(内容未完全分析)*
    *   **潜在职责:** 可能参与处理文件上传，例如应用保护、转换文件类型或与（未来的）基于文件的水印系统集成。
*   **PPT转换器 (`includes/ppt-convert.php`):**
    *   **(内容未完全分析)*
    *   **潜在职责:** 暗示具有处理PowerPoint (`.ppt`, `.pptx`) 文件的功能。这可能涉及服务器端将演示文稿转换为PDF或图片，以便随后通过现有机制进行保护。当前的 `[dc_ppt]` 短代码直接期望一个PDF URL，因此这可能是一个独立的或预备性功能。
*   **内容爬虫 (`includes/content-crawler.php`):**
    *   **(内容未完全分析)*
    *   **潜在职责:** 其目的尚不明确。它可能会扫描帖子内容以查找未受保护的媒体以建议保护，或执行与插件范围内的媒体管理相关的其他自动化任务。

## 3. 关键架构特性与数据流

*   **WordPress集成:** 通过钩子（激活、停用、脚本加载、后台菜单）和Shortcode API与WordPress深度集成。
*   **以短代码为中心的设计:** 用户应用媒体保护和显示的主要方法是通过短代码。
*   **客户端保护:** 对于PDF，严重依赖客户端HTML结构、CSS叠加和JavaScript事件操作来实现水印和阻止下载。这意味着保护依赖于浏览器。
*   **渐进增强/备用方案:** 特别是对于移动设备上的PDF查看，插件会尝试使用功能最丰富的方法（通过iframe的PDF.js），并根据浏览器功能和检测到的环境回退到更简单的iframe、链接或自定义用户界面。
*   **依赖性:** 主要的PDF保护机制强烈依赖于 "PDF.js Viewer Shortcode" 插件已安装并在已知路径下可访问。
*   **模块化:** 功能被分离到 `includes/` 目录中的不同PHP文件中，代表不同的关注点（短代码、移动查看、后台管理等）。

**`[dc_ppt]` 短代码的简化数据/控制流:**

1.  用户将 `[dc_ppt src="path/to/file.pdf"]` 插入帖子。
2.  WordPress解析短代码，调用 `shortcode.php` 中的 `dcmp_shortcode_ppt`。
3.  该函数处理 `src` 属性。
4.  进行广泛的设备/浏览器检测。
5.  **如果PDF是本地文件且检测到PDF.js Viewer插件 (常见情况):**
    a.  构建PDF.js查看器的 `<iframe>` URL。
    b.  生成一个“全屏”按钮。
    c.  生成iframe和复杂的多层水印叠加的HTML。
    d.  添加用于阻止下载/复制的内联JavaScript。
6.  **如果PDF.js不可用或处于特定的移动浏览器上下文:**
    a.  逻辑可能委托给 `mobile-pdf-viewer.php` 中的 `dcmp_generate_mobile_pdf_viewer`。
    b.  此函数根据进一步的检测返回特定的HTML（更简单的iframe、链接、自定义用户界面）。
7.  最终的HTML（包括水印和保护脚本）被返回并在页面上呈现。
