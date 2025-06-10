# DC Media Protect Plugin Architecture

## 1. Overview

DC Media Protect is a WordPress plugin designed to protect multimedia content (PDFs, videos, images) by adding watermarks and preventing downloads. Its architecture is centered around WordPress hooks, shortcodes for content display, and a combination of server-side PHP and client-side HTML/CSS/JavaScript for rendering and protection. A key feature is its integration with a modified PDF.js viewer for PDF protection, with extensive fallbacks for various mobile devices and browsers.

## 2. Main Architectural Components

### 2.1. Plugin Core (`dc-media-protect.php`)

*   **Responsibilities:**
    *   Initializes the plugin upon WordPress loading.
    *   Handles plugin activation and deactivation hooks (e.g., flushing rewrite rules).
    *   Includes all other PHP module files from the `includes/` directory.
    *   Enqueues global frontend CSS (`assets/css/style.css`) and JavaScript (`assets/js/frontend.js`) assets.
    *   Conditionally loads admin-specific functionality (`includes/admin-pages.php`) when in the WordPress admin context.
*   **Interactions:**
    *   Acts as the main entry point registered with WordPress.
    *   Loads and orchestrates all other plugin components.

### 2.2. Shortcode Engine (`includes/shortcode.php`)

*   **Responsibilities:**
    *   Defines and handles the core user-facing functionality through shortcodes:
        *   `[dc_video src="..."]`: Displays a video player with an HTML overlay watermark.
        *   `[dc_img src="..."]`: Displays an image with an HTML overlay watermark.
        *   `[dc_ppt src="..."]`: The most complex shortcode, for displaying PDFs. Its responsibilities include:
            *   Parsing `src`, `width`, and `height` attributes.
            *   Converting relative PDF paths to absolute URLs.
            *   Detecting if the PDF source is local or external.
            *   **Primary PDF Viewing (Local PDFs):**
                *   Checking for the existence of the "PDF.js Viewer Shortcode" plugin.
                *   Constructing a URL to its `viewer.php` and embedding it in an `<iframe>`.
                *   Passing parameters to disable PDF.js's native download, print, and open buttons.
                *   Adding a custom "Fullscreen Display" button that opens the PDF.js viewer in a new maximized window.
            *   **HTML/CSS/JS Overlay Protection (for PDFs via PDF.js):**
                *   Generating multiple HTML `<div>` elements styled with CSS, layered over the PDF iframe. These display various watermarks (text, copyright notices, timestamps, large centered text) and a patterned background.
                *   Embedding inline JavaScript to disable context menus, drag-and-drop, text selection, and specific keyboard shortcuts (Ctrl+S, Ctrl+P, Ctrl+A, F12) within the PDF container to deter downloading/copying.
            *   Providing fallback mechanisms if the "PDF.js Viewer Shortcode" plugin is not found or for specific non-cooperative mobile browsers (delegating to the Mobile PDF Display Engine).
            *   Handling external PDFs by showing a link to open them directly.
    *   Retrieves watermark text using `dcmp_get_watermark_text()` (which reads from WordPress options).
    *   Includes utility functions for device/browser detection (`dcmp_is_mobile_device`, `dcmp_is_wechat_browser`, `dcmp_is_qq_browser`).
*   **Interactions:**
    *   Registers shortcodes with WordPress.
    *   Outputs HTML, CSS, and JavaScript to the frontend.
    *   Relies on the `Mobile PDF Display Engine` for certain PDF rendering fallbacks.
    *   Depends on the presence of the "PDF.js Viewer Shortcode" plugin for its primary PDF display method.

### 2.3. Mobile PDF Display Engine (`includes/mobile-pdf-viewer.php`)

*   **Responsibilities:**
    *   Provides alternative PDF viewing strategies optimized or as fallbacks for various mobile browsers, especially when the main PDF.js iframe method in `shortcode.php` is not suitable or fails.
    *   Performs fine-grained mobile device and browser detection (iOS Safari, Firefox, WeChat on MIUI, general WeChat, Xiaomi Browser, UC Browser, QQ Browser, Huawei Browser, Sogou Browser).
    *   Generates tailored HTML output based on the detected environment:
        *   Simple iframes for more capable mobile browsers (e.g., iOS Safari, Firefox).
        *   Custom UI with links/buttons (e.g., "Open in browser," "Copy link," "Use other app") for restrictive environments like WeChat.
        *   An experimental approach for Android Chrome that prepares a `<canvas>` element and a `window.dcmpPdfData` JavaScript object, presumably for use with a separate PDF.js rendering script (though the script itself is not in this PHP file).
    *   Includes an AJAX endpoint (`dcmp_ajax_get_pdf_info`) to fetch PDF metadata (content type, size), likely for client-side validation, though its direct invocation from the PHP-generated views is not explicit.
*   **Interactions:**
    *   Primarily invoked by the `[dc_ppt]` shortcode logic in `shortcode.php` as a fallback.
    *   Outputs HTML specific to mobile PDF viewing.

### 2.4. Watermark System

*   **HTML/CSS Overlay Watermarks (Implemented in `includes/shortcode.php`):**
    *   **Responsibilities:** Dynamically generates HTML `<div>` elements with CSS styling to create visible watermarks. These are layered on top of the media content (PDFs, videos, images) within the browser. For PDFs displayed via PDF.js, this is a multi-layer system with various text elements and a background pattern. For videos and images, it's a simpler text overlay.
    *   **Key Characteristic:** Watermarks are not embedded into the media files themselves but are part of the rendered HTML page.
    *   **Interactions:** Logic is tightly coupled with the shortcode rendering functions.
*   **Watermark Configuration (via `get_option('dcmp_watermark_text')`):**
    *   **Responsibilities:** The actual watermark text is retrieved from a WordPress option (`dcmp_watermark_text`), presumably set via the admin interface.
    *   **Interactions:** The `Shortcode Engine` reads this option.
*   **Placeholder for File-Based Watermarking (`includes/watermark.php`):**
    *   **Responsibilities:** Contains placeholder functions (`dcmp_add_image_watermark`, `dcmp_add_pdf_watermark`) intended for future functionality to directly modify image and PDF files to embed watermarks.
    *   **Key Characteristic:** This component is currently **unimplemented**.
    *   **Interactions:** None in its current state.

### 2.5. Frontend Assets (`assets/`)

*   **`assets/css/style.css`:**
    *   **Responsibilities:** Provides general CSS rules for styling the plugin's output on the frontend. This might include containers, basic watermark appearance (though much of the PDF watermark styling is inline in `shortcode.php`).
*   **`assets/js/frontend.js`:**
    *   **Responsibilities:** Enqueued for potential global client-side interactions. While the PDF protection JavaScript (context menu, keyboard shortcuts) is currently inline within `shortcode.php`, this file could be used for other enhancements. It might be intended for use with the Android PDF.js canvas rendering setup in `mobile-pdf-viewer.php`.
*   **Interactions:** Both files are enqueued by the `Plugin Core` and loaded on frontend pages.

### 2.6. Admin Interface (`includes/admin-pages.php`)

*   **(Assumed based on name and typical plugin structure; content not fully analyzed in this pass)*
*   **Responsibilities:** Expected to create and manage settings pages within the WordPress admin area. This would likely include settings for the watermark text (`dcmp_watermark_text`) and potentially other plugin configurations.
*   **Interactions:** Hooks into the WordPress admin menu system. Saves options to the WordPress database, which are then read by other components (e.g., `Shortcode Engine` for watermark text).

### 2.7. Supporting Utilities (`includes/`)

*   **Upload Handler (`includes/upload-handler.php`):**
    *   **(Content not fully analyzed)*
    *   **Potential Responsibilities:** Could be involved in processing file uploads, perhaps applying protections, converting file types, or integrating with the (future) file-based watermarking system.
*   **PPT Convert (`includes/ppt-convert.php`):**
    *   **(Content not fully analyzed)*
    *   **Potential Responsibilities:** Suggests functionality for handling PowerPoint (`.ppt`, `.pptx`) files. This might involve server-side conversion to PDF or images to then be protected by the existing mechanisms. The current `[dc_ppt]` shortcode directly expects a PDF URL, so this might be a separate or preparatory feature.
*   **Content Crawler (`includes/content-crawler.php`):**
    *   **(Content not fully analyzed)*
    *   **Potential Responsibilities:** The purpose is not immediately clear. It might scan post content for unprotected media to suggest protection, or perform other automated tasks related to media management within the plugin's scope.

## 3. Key Architectural Characteristics & Data Flow

*   **WordPress Integration:** Deeply integrated with WordPress via hooks (activation, deactivation, script enqueuing, admin menus) and the Shortcode API.
*   **Shortcode-Centric Design:** The primary method for users to apply media protection and display is through shortcodes.
*   **Client-Side Protection:** For PDFs, significant reliance on client-side HTML structure, CSS overlays, and JavaScript event manipulation to achieve watermarking and deter downloads. This means protection is browser-dependent.
*   **Progressive Enhancement/Fallback:** Especially for PDF viewing on mobile, the plugin attempts to use the most feature-rich method (PDF.js via iframe) and falls back to simpler iframes, links, or custom UIs depending on browser capabilities and detected environment.
*   **Dependency:** The primary PDF protection mechanism has a strong dependency on the "PDF.js Viewer Shortcode" plugin being installed and accessible at a known path.
*   **Modularity:** Functionality is separated into different PHP files within the `includes/` directory, representing different concerns (shortcodes, mobile viewing, admin, etc.).

**Simplified Data/Control Flow for `[dc_ppt]` shortcode:**

1.  User inserts `[dc_ppt src="path/to/file.pdf"]` into a post.
2.  WordPress parses the shortcode, invoking `dcmp_shortcode_ppt` in `shortcode.php`.
3.  The function processes the `src` attribute.
4.  Extensive device/browser detection occurs.
5.  **If local PDF and PDF.js Viewer plugin is detected (common case):**
    a.  An `<iframe>` URL for the PDF.js viewer is constructed.
    b.  A "Fullscreen" button is generated.
    c.  HTML for the iframe and complex multi-layer watermark overlays is generated.
    d.  Inline JavaScript for download/copy prevention is added.
6.  **If PDF.js is not available OR specific mobile browser context:**
    a.  Logic may delegate to `dcmp_generate_mobile_pdf_viewer` in `mobile-pdf-viewer.php`.
    b.  This function, based on further detection, returns specific HTML (simpler iframe, links, custom UI).
7.  The final HTML (including watermarks and protection scripts) is returned and rendered on the page.
