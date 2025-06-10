<?php
/**
 * Simple fullscreen PDF viewer
 * Handles direct file parameter for fullscreen viewing
 */

// Get the PDF file URL from the query parameter
$pdfjs_url = isset($_GET['file']) ? $_GET['file'] : '';

// Validate the URL
if (empty($pdfjs_url)) {
    die('No PDF file specified');
}

// Basic URL validation
$pdfjs_url = filter_var($pdfjs_url, FILTER_VALIDATE_URL);
if (!$pdfjs_url) {
    die('Invalid PDF file URL');
}

// Set a basic attachment ID for compatibility
$attachment_id = 0;

?><!DOCTYPE html>
<html class="no-js" dir="ltr" mozdisallowselectionprint>
<head>
    <title>PDF Fullscreen Viewer</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="google" content="notranslate">

    <!-- PDF.js Resources -->
    <link rel="resource" type="application/l10n" href="locale/locale.json">
    <link rel="stylesheet" href="viewer.css">
    <script src="../build/pdf.js" type="module"></script>
    <script src="viewer.js" type="module"></script>

    <script>
        // Initialize PDF.js with the file URL
        setTimeout(function(){
            PDFViewerApplication.open({"url": "<?php echo esc_js($pdfjs_url); ?>"});
        }, 100);
    </script>

    <style>
        /* Fullscreen specific styles */
        body {
            margin: 0;
            padding: 0;
            background: #404040;
        }
        
        #outerContainer {
            height: 100vh;
            width: 100vw;
        }
        
        #viewerContainer {
            height: calc(100vh - 40px); /* Account for toolbar */
        }
        
        /* Hide some UI elements for cleaner fullscreen experience */
        #sidebarContainer {
            display: none;
        }
        
        #mainContainer {
            left: 0;
        }
        
        /* Add exit fullscreen button */
        #exitFullscreen {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 10000;
            background: #d32f2f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        
        #exitFullscreen:hover {
            background: #b71c1c;
        }
    </style>
</head>

<body tabindex="1">
    <!-- Exit fullscreen button -->
    <button id="exitFullscreen" title="退出全屏" onclick="window.close()">✕ 退出全屏</button>
    
    <div id="outerContainer">
        <div id="sidebarContainer">
            <div id="toolbarSidebar">
                <div id="toolbarSidebarLeft">
                    <div id="sidebarViewButtons" class="splitToolbarButton toggled" role="radiogroup">
                        <button id="viewThumbnail" class="toolbarButton toggled" title="Show Thumbnails" tabindex="2" data-l10n-id="pdfjs-thumbs-button" role="radio" aria-checked="true" aria-controls="thumbnailView">
                            <span data-l10n-id="pdfjs-thumbs-button-label">Thumbnails</span>
                        </button>
                        <button id="viewOutline" class="toolbarButton" title="Show Document Outline (double-click to expand/collapse all items)" tabindex="3" data-l10n-id="pdfjs-document-outline-button" role="radio" aria-checked="false" aria-controls="outlineView">
                            <span data-l10n-id="pdfjs-document-outline-button-label">Document Outline</span>
                        </button>
                        <button id="viewAttachments" class="toolbarButton" title="Show Attachments" tabindex="4" data-l10n-id="pdfjs-attachments-button" role="radio" aria-checked="false" aria-controls="attachmentsView">
                            <span data-l10n-id="pdfjs-attachments-button-label">Attachments</span>
                        </button>
                        <button id="viewLayers" class="toolbarButton" title="Show Layers (double-click to reset all layers to the default state)" tabindex="5" data-l10n-id="pdfjs-layers-button" role="radio" aria-checked="false" aria-controls="layersView">
                            <span data-l10n-id="pdfjs-layers-button-label">Layers</span>
                        </button>
                    </div>
                </div>

                <div id="toolbarSidebarRight">
                    <div id="outlineOptionsContainer">
                        <div class="verticalToolbarSeparator"></div>

                        <button id="currentOutlineItem" class="toolbarButton" disabled="disabled" title="Find Current Outline Item" tabindex="6" data-l10n-id="pdfjs-current-outline-item-button">
                            <span data-l10n-id="pdfjs-current-outline-item-button-label">Current Outline Item</span>
                        </button>
                    </div>
                </div>
            </div>
            <div id="sidebarContent">
                <div id="thumbnailView">
                </div>
                <div id="outlineView" class="hidden">
                </div>
                <div id="attachmentsView" class="hidden">
                </div>
                <div id="layersView" class="hidden">
                </div>
            </div>
            <div id="sidebarResizer"></div>
        </div>  <!-- sidebarContainer -->

        <div id="mainContainer">
            <div class="findbar hidden doorHanger" id="findbar">
                <div id="findbarInputContainer">
                    <span class="loadingInput end">
                        <input id="findInput" class="toolbarField" title="Find" placeholder="Find in document…" tabindex="91" data-l10n-id="pdfjs-find-input" aria-invalid="false">
                    </span>
                    <div class="splitToolbarButton">
                        <button id="findPrevious" class="toolbarButton" title="Find the previous occurrence of the phrase" tabindex="92" data-l10n-id="pdfjs-find-previous-button">
                            <span data-l10n-id="pdfjs-find-previous-button-label">Previous</span>
                        </button>
                        <div class="splitToolbarButtonSeparator"></div>
                        <button id="findNext" class="toolbarButton" title="Find the next occurrence of the phrase" tabindex="93" data-l10n-id="pdfjs-find-next-button">
                            <span data-l10n-id="pdfjs-find-next-button-label">Next</span>
                        </button>
                    </div>
                </div>

                <div id="findbarOptionsOneContainer">
                    <input type="checkbox" id="findHighlightAll" class="toolbarField" tabindex="94">
                    <label for="findHighlightAll" class="toolbarLabel" data-l10n-id="pdfjs-find-highlight-checkbox">Highlight All</label>
                    <input type="checkbox" id="findMatchCase" class="toolbarField" tabindex="95">
                    <label for="findMatchCase" class="toolbarLabel" data-l10n-id="pdfjs-find-match-case-checkbox-label">Match Case</label>
                </div>
                <div id="findbarOptionsTwoContainer">
                    <input type="checkbox" id="findMatchDiacritics" class="toolbarField" tabindex="96">
                    <label for="findMatchDiacritics" class="toolbarLabel" data-l10n-id="pdfjs-find-match-diacritics-checkbox-label">Match Diacritics</label>
                    <input type="checkbox" id="findEntireWord" class="toolbarField" tabindex="97">
                    <label for="findEntireWord" class="toolbarLabel" data-l10n-id="pdfjs-find-entire-word-checkbox-label">Whole Words</label>
                </div>

                <div id="findbarMessageContainer" aria-live="polite">
                    <span id="findResultsCount" class="toolbarLabel"></span>
                    <span id="findMsg" class="toolbarLabel"></span>
                </div>
            </div>  <!-- findbar -->

            <div class="toolbar">
                <div id="toolbarContainer">
                    <div id="toolbarViewer">
                        <div id="toolbarViewerLeft">
                            <button id="sidebarToggle" class="toolbarButton" title="Toggle Sidebar" tabindex="11" data-l10n-id="pdfjs-toggle-sidebar-button" aria-expanded="false" aria-controls="sidebarContainer">
                                <span data-l10n-id="pdfjs-toggle-sidebar-button-label">Toggle Sidebar</span>
                            </button>
                            <div class="toolbarButtonSpacer"></div>
                            <button id="viewFind" class="toolbarButton" title="Find in Document" tabindex="12" data-l10n-id="pdfjs-findbar-button" aria-expanded="false" aria-controls="findbar">
                                <span data-l10n-id="pdfjs-findbar-button-label">Find</span>
                            </button>
                            <div class="splitToolbarButton hiddenSmallView">
                                <button class="toolbarButton" title="Previous Page" id="previous" tabindex="13" data-l10n-id="pdfjs-previous-button">
                                    <span data-l10n-id="pdfjs-previous-button-label">Previous</span>
                                </button>
                                <div class="splitToolbarButtonSeparator"></div>
                                <button class="toolbarButton" title="Next Page" id="next" tabindex="14" data-l10n-id="pdfjs-next-button">
                                    <span data-l10n-id="pdfjs-next-button-label">Next</span>
                                </button>
                            </div>
                            <span class="loadingInput start">
                                <input type="number" id="pageNumber" class="toolbarField" title="Page" value="1" min="1" tabindex="15" data-l10n-id="pdfjs-page-input" autocomplete="off">
                            </span>
                            <span id="numPages" class="toolbarLabel"></span>
                        </div>
                        <div id="toolbarViewerRight">
                            <button id="print" class="toolbarButton hiddenMediumView" title="Print" tabindex="41" data-l10n-id="pdfjs-print-button">
                                <span data-l10n-id="pdfjs-print-button-label">Print</span>
                            </button>

                            <button id="download" class="toolbarButton hiddenMediumView" title="Save" tabindex="42" data-l10n-id="pdfjs-save-button">
                                <span data-l10n-id="pdfjs-save-button-label">Save</span>
                            </button>
                        </div>
                        <div id="toolbarViewerMiddle">
                            <div class="splitToolbarButton">
                                <button id="zoomOut" class="toolbarButton" title="Zoom Out" tabindex="21" data-l10n-id="pdfjs-zoom-out-button">
                                    <span data-l10n-id="pdfjs-zoom-out-button-label">Zoom Out</span>
                                </button>
                                <div class="splitToolbarButtonSeparator"></div>
                                <button id="zoomIn" class="toolbarButton" title="Zoom In" tabindex="22" data-l10n-id="pdfjs-zoom-in-button">
                                    <span data-l10n-id="pdfjs-zoom-in-button-label">Zoom In</span>
                                </button>
                            </div>
                            <span id="scaleSelectContainer" class="dropdownToolbarButton">
                                <select id="scaleSelect" title="Zoom" tabindex="23" data-l10n-id="pdfjs-zoom-select">
                                    <option id="pageAutoOption" title="" value="auto" selected="selected" data-l10n-id="pdfjs-page-scale-auto">Automatic Zoom</option>
                                    <option id="pageActualOption" title="" value="page-actual" data-l10n-id="pdfjs-page-scale-actual">Actual Size</option>
                                    <option id="pageFitOption" title="" value="page-fit" data-l10n-id="pdfjs-page-scale-fit">Page Fit</option>
                                    <option id="pageWidthOption" title="" value="page-width" data-l10n-id="pdfjs-page-scale-width">Page Width</option>
                                    <option id="customScaleOption" title="" value="custom" disabled="disabled" hidden="true" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 0 }'>0%</option>
                                    <option title="" value="0.5" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 50 }'>50%</option>
                                    <option title="" value="0.75" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 75 }'>75%</option>
                                    <option title="" value="1" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 100 }'>100%</option>
                                    <option title="" value="1.25" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 125 }'>125%</option>
                                    <option title="" value="1.5" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 150 }'>150%</option>
                                    <option title="" value="2" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 200 }'>200%</option>
                                    <option title="" value="3" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 300 }'>300%</option>
                                    <option title="" value="4" data-l10n-id="pdfjs-page-scale-percent" data-l10n-args='{ "scale": 400 }'>400%</option>
                                </select>
                            </span>
                        </div>
                    </div>
                    <div id="loadingBar">
                        <div class="progress">
                            <div class="glimmer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="viewerContainer" tabindex="0">
                <div id="viewer" class="pdfViewer"></div>
            </div>
        </div> <!-- mainContainer -->

        <div id="dialogContainer"></div>

    </div> <!-- outerContainer -->
    <div id="printContainer"></div>

    <script>
        // Auto fullscreen on load
        document.addEventListener('DOMContentLoaded', function() {
            console.log("🔍 Fullscreen viewer loaded");
            
            // Try to enter browser fullscreen
            setTimeout(() => {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log("Fullscreen failed, user can press F11:", err);
                    });
                }
            }, 500);
            
            // ESC key to exit
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    window.close();
                }
            });
        });
    </script>
</body>
</html>