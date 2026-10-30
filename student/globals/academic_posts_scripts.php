<?php
/*
|--------------------------------------------------------------------------
| ACADEMIC POSTS - FLOATING READING TOOLS
|--------------------------------------------------------------------------
| Include this file immediately before </body>.
|
| IMPORTANT:
| The reading tools apply only to the post/article content container.
| Add class="post-content" to the wrapper around the actual post content.
| Example:
| <div class="post-content">
|     ...your academic post...
| </div>
|--------------------------------------------------------------------------
*/
?>

<style>
    :root {
        --reading-accent: #ff4c01;
        --reading-panel-bg: #ffffff;
        --reading-panel-text: #172033;
        --reading-panel-border: #e5e7eb;
        --reading-shadow: 0 12px 35px rgba(0, 0, 0, .18);
    }

    html[data-theme="dark"] {
        --reading-panel-bg: #1b202b;
        --reading-panel-text: #f1f5f9;
        --reading-panel-border: #343c4c;
        --reading-shadow: 0 12px 35px rgba(0, 0, 0, .38);
    }

    #readingToolsRoot,
    #readingToolsRoot * {
        box-sizing: border-box;
    }

    #readingToolsFab {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 99990;
        width: 54px;
        height: 54px;
        border: 0;
        border-radius: 50%;
        background: var(--reading-accent);
        color: #fff;
        box-shadow: 0 5px 18px rgba(255, 76, 1, .35);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 22px;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    #readingToolsFab:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(255, 76, 1, .42);
    }

    #readingToolsPanel {
        position: fixed;
        right: 24px;
        bottom: 90px;
        z-index: 99991;
        width: min(340px, calc(100vw - 28px));
        max-height: min(76vh, 650px);
        overflow-y: auto;
        padding: 17px;
        border: 1px solid var(--reading-panel-border);
        border-radius: 14px;
        background: var(--reading-panel-bg);
        color: var(--reading-panel-text);
        box-shadow: var(--reading-shadow);
        font-family: Arial, sans-serif;
        display: none;
    }

    #readingToolsPanel.is-open {
        display: block;
        animation: readingPanelIn .18s ease-out;
    }

    @keyframes readingPanelIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .rt-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 1px solid var(--reading-panel-border);
    }

    .rt-header strong {
        font-size: 15px;
        font-weight: 800;
    }

    .rt-close {
        width: 30px;
        height: 30px;
        border: 0;
        border-radius: 7px;
        background: transparent;
        color: var(--reading-panel-text);
        cursor: pointer;
        font-size: 20px;
    }

    .rt-close:hover {
        background: rgba(127, 127, 127, .13);
    }

    .rt-group {
        margin: 0 0 15px;
    }

    .rt-label {
        display: block;
        margin-bottom: 8px;
        color: var(--reading-panel-text);
        font-size: 12px;
        font-weight: 800;
    }

    .rt-muted {
        display: block;
        margin-top: 5px;
        color: var(--reading-panel-text);
        opacity: .65;
        font-size: 11px;
        line-height: 1.45;
    }

    .rt-button-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .rt-btn,
    .rt-select {
        min-height: 34px;
        padding: 7px 10px;
        border: 1px solid var(--reading-panel-border);
        border-radius: 7px;
        background: var(--reading-panel-bg);
        color: var(--reading-panel-text);
        font-size: 12px;
        cursor: pointer;
    }

    .rt-btn:hover,
    .rt-btn.is-active {
        border-color: var(--reading-accent);
        color: var(--reading-accent);
    }

    .rt-select {
        width: 100%;
        cursor: pointer;
    }

    .rt-color-row {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
    }

    .rt-color {
        width: 29px;
        height: 29px;
        padding: 0;
        border: 2px solid var(--reading-panel-border);
        border-radius: 50%;
        cursor: pointer;
    }

    .rt-color.is-active {
        outline: 2px solid var(--reading-accent);
        outline-offset: 2px;
    }

    .rt-range {
        width: 100%;
        accent-color: var(--reading-accent);
        cursor: pointer;
    }

    .rt-range-labels {
        display: flex;
        justify-content: space-between;
        color: var(--reading-panel-text);
        opacity: .65;
        font-size: 10px;
    }

    .rt-scroll-status {
        margin-top: 8px;
        padding: 8px 10px;
        border-radius: 7px;
        background: rgba(127, 127, 127, .10);
        color: var(--reading-panel-text);
        font-size: 11px;
    }

    #rtScrollStatus.active {
        color: #16a34a;
        font-weight: 700;
    }

    @media (max-width: 480px) {
        #readingToolsFab {
            right: 16px;
            bottom: 16px;
        }

        #readingToolsPanel {
            right: 14px;
            bottom: 80px;
        }
    }
</style>

<div id="readingToolsRoot">
    <button type="button" id="readingToolsFab" aria-label="Open reading tools"
        aria-controls="readingToolsPanel" aria-expanded="false" title="Reading tools">
        <i class="bi bi-book-half" aria-hidden="true"></i>
    </button>

    <section id="readingToolsPanel" aria-label="Reading tools">
        <div class="rt-header">
            <strong><i class="bi bi-sliders"></i> Reading Tools</strong>
            <button type="button" class="rt-close" id="rtClose" aria-label="Close reading tools">&times;</button>
        </div>

        <div class="rt-group">
            <span class="rt-label">Text size</span>
            <div class="rt-button-row">
                <button type="button" class="rt-btn" id="rtTextDown" aria-label="Decrease text size">A−</button>
                <button type="button" class="rt-btn" id="rtTextReset">Reset</button>
                <button type="button" class="rt-btn" id="rtTextUp" aria-label="Increase text size">A+</button>
                <span id="rtTextSizeValue" class="rt-muted" style="margin:0">100%</span>
            </div>
        </div>

        <div class="rt-group">
            <span class="rt-label">Page color</span>
            <div class="rt-color-row" id="rtPageColors">
                <button type="button" class="rt-color is-active" data-color="#ffffff" aria-label="White page" style="background:#ffffff"></button>
                <button type="button" class="rt-color" data-color="#fff4df" aria-label="Warm cream page" style="background:#fff4df"></button>
                <button type="button" class="rt-color" data-color="#eaf2ff" aria-label="Light blue page" style="background:#eaf2ff"></button>
                <button type="button" class="rt-color" data-color="#20242d" aria-label="Dark page" style="background:#20242d"></button>
                <button type="button" class="rt-color" data-color="#e8f3e8" aria-label="Soft green page" style="background:#e8f3e8"></button>
            </div>
        </div>

        <div class="rt-group">
            <span class="rt-label">Text color</span>
            <div class="rt-color-row" id="rtTextColors">
                <button type="button" class="rt-color is-active" data-color="#172033" aria-label="Dark text" style="background:#172033"></button>
                <button type="button" class="rt-color" data-color="#ffffff" aria-label="White text" style="background:#ffffff"></button>
                <button type="button" class="rt-color" data-color="#1d4ed8" aria-label="Blue text" style="background:#1d4ed8"></button>
                <button type="button" class="rt-color" data-color="#9a3412" aria-label="Brown text" style="background:#9a3412"></button>
                <button type="button" class="rt-color" data-color="#166534" aria-label="Green text" style="background:#166534"></button>
            </div>
        </div>

        <div class="rt-group">
            <label class="rt-label" for="rtFont">Font family</label>
            <select class="rt-select" id="rtFont">
                <option value="inherit">Default font</option>
                <option value="Arial, sans-serif">Arial</option>
                <option value="Georgia, serif">Georgia</option>
                <option value="'Times New Roman', serif">Times New Roman</option>
                <option value="Verdana, sans-serif">Verdana</option>
                <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
            </select>
        </div>

        <div class="rt-group">
            <span class="rt-label">Auto-scroll speed</span>
            <input class="rt-range" type="range" id="rtScrollSpeed" min="1" max="10" value="3">
            <div class="rt-range-labels"><span>Slow</span><span>Fast</span></div>
            <div class="rt-button-row" style="margin-top:9px">
                <button type="button" class="rt-btn" id="rtScrollStart"><i class="bi bi-play-fill"></i> Start</button>
                <button type="button" class="rt-btn" id="rtScrollPause"><i class="bi bi-pause-fill"></i> Pause</button>
                <button type="button" class="rt-btn" id="rtScrollStop"><i class="bi bi-stop-fill"></i> Stop</button>
            </div>
            <div class="rt-scroll-status" id="rtScrollStatus">Auto-scroll is stopped.</div>
        </div>

        <div class="rt-button-row">
            <button type="button" class="rt-btn" id="rtResetAll">Reset all settings</button>
        </div>
        <small class="rt-muted">Your reading preferences are saved in this browser on this device.</small>
    </section>
</div>

<script>
    (() => {
        'use strict';

        const root = document.getElementById('readingToolsRoot');
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        /*
         * Add class="post-content" to the element wrapping the post itself.
         * If that class is not present, the script tries common article wrappers.
         */
        const content = document.querySelector(
            '.post-content, .academic-post-content, .article-content, .post-body, .academic-post, article'
        );

        const fab = document.getElementById('readingToolsFab');
        const panel = document.getElementById('readingToolsPanel');
        const close = document.getElementById('rtClose');
        const fontSelect = document.getElementById('rtFont');
        const speedInput = document.getElementById('rtScrollSpeed');
        const status = document.getElementById('rtScrollStatus');

        const storageKey = 'academicPostReadingPreferences';
        const defaults = {
            textScale: 1,
            pageColor: '#ffffff',
            textColor: '#172033',
            font: 'inherit',
            speed: 3
        };

        let settings = {
            ...defaults
        };
        let scrollTimer = null;
        let scrollDirection = 1;

        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
            settings = {
                ...defaults,
                ...saved
            };
        } catch (error) {
            settings = {
                ...defaults
            };
        }

        function saveSettings() {
            try {
                localStorage.setItem(storageKey, JSON.stringify(settings));
            } catch (error) {
                /* Storage may be unavailable; controls still work. */ }
        }

        function applyReadingSettings() {
            if (!content) {
                status.textContent = 'Add class="post-content" to your post wrapper to enable reading adjustments.';
                return;
            }

            content.style.setProperty('--reading-text-scale', settings.textScale);
            content.style.setProperty('font-size', (settings.textScale * 100) + '%', 'important');
            content.style.setProperty('background-color', settings.pageColor, 'important');
            content.style.setProperty('color', settings.textColor, 'important');
            content.style.setProperty('font-family', settings.font, 'important');

            content.querySelectorAll('p, li, blockquote, td, th, figcaption').forEach(el => {
                el.style.setProperty('font-size', '1em', 'important');
                el.style.setProperty('color', settings.textColor, 'important');
                el.style.setProperty('font-family', settings.font, 'important');
            });

            content.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach(el => {
                el.style.setProperty('color', settings.textColor, 'important');
                el.style.setProperty('font-family', settings.font, 'important');
            });

            document.getElementById('rtTextSizeValue').textContent =
                Math.round(settings.textScale * 100) + '%';
            fontSelect.value = settings.font;
            speedInput.value = settings.speed;

            document.querySelectorAll('#rtPageColors .rt-color').forEach(btn => {
                btn.classList.toggle('is-active', btn.dataset.color === settings.pageColor);
            });
            document.querySelectorAll('#rtTextColors .rt-color').forEach(btn => {
                btn.classList.toggle('is-active', btn.dataset.color === settings.textColor);
            });
        }

        function openPanel(open) {
            panel.classList.toggle('is-open', open);
            fab.setAttribute('aria-expanded', String(open));
        }

        fab.addEventListener('click', () => openPanel(!panel.classList.contains('is-open')));
        close.addEventListener('click', () => openPanel(false));

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') openPanel(false);
        });

        document.getElementById('rtTextDown').addEventListener('click', () => {
            settings.textScale = Math.max(.7, +(settings.textScale - .1).toFixed(2));
            saveSettings();
            applyReadingSettings();
        });

        document.getElementById('rtTextUp').addEventListener('click', () => {
            settings.textScale = Math.min(1.6, +(settings.textScale + .1).toFixed(2));
            saveSettings();
            applyReadingSettings();
        });

        document.getElementById('rtTextReset').addEventListener('click', () => {
            settings.textScale = 1;
            saveSettings();
            applyReadingSettings();
        });

        document.querySelectorAll('#rtPageColors .rt-color').forEach(btn => {
            btn.addEventListener('click', () => {
                settings.pageColor = btn.dataset.color;
                saveSettings();
                applyReadingSettings();
            });
        });

        document.querySelectorAll('#rtTextColors .rt-color').forEach(btn => {
            btn.addEventListener('click', () => {
                settings.textColor = btn.dataset.color;
                saveSettings();
                applyReadingSettings();
            });
        });

        fontSelect.addEventListener('change', () => {
            settings.font = fontSelect.value;
            saveSettings();
            applyReadingSettings();
        });

        speedInput.addEventListener('input', () => {
            settings.speed = Number(speedInput.value);
            saveSettings();
        });

        function stopScroll(message) {
            if (scrollTimer) {
                cancelAnimationFrame(scrollTimer);
                scrollTimer = null;
            }
            status.textContent = message || 'Auto-scroll is stopped.';
            status.classList.remove('active');
        }

        function scrollFrame() {
            if (!scrollTimer) return;
            const pixelsPerFrame = Number(settings.speed) * 0.35;
            window.scrollBy(0, pixelsPerFrame * scrollDirection);

            const atBottom = window.innerHeight + window.scrollY >=
                document.documentElement.scrollHeight - 2;

            if (atBottom) {
                stopScroll('Reached the bottom of the page.');
                return;
            }
            scrollTimer = requestAnimationFrame(scrollFrame);
        }

        document.getElementById('rtScrollStart').addEventListener('click', () => {
            if (scrollTimer) return;
            scrollDirection = 1;
            status.textContent = 'Auto-scroll is running.';
            status.classList.add('active');
            scrollTimer = requestAnimationFrame(scrollFrame);
        });

        document.getElementById('rtScrollPause').addEventListener('click', () => {
            if (!scrollTimer) {
                status.textContent = 'Auto-scroll is paused or stopped.';
                return;
            }
            cancelAnimationFrame(scrollTimer);
            scrollTimer = null;
            status.textContent = 'Auto-scroll is paused.';
            status.classList.remove('active');
        });

        document.getElementById('rtScrollStop').addEventListener('click', () => {
            stopScroll('Auto-scroll is stopped.');
        });

        document.getElementById('rtResetAll').addEventListener('click', () => {
            stopScroll('Auto-scroll is stopped.');
            settings = {
                ...defaults
            };
            saveSettings();
            applyReadingSettings();
        });

        applyReadingSettings();
    })();
</script>