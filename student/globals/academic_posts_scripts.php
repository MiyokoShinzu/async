<?php
/*
|--------------------------------------------------------------------------
| ASYNC ACADEMIC POSTS - FLOATING READING TOOLS
|--------------------------------------------------------------------------
| Include immediately before </body>.
| Targets: main.main-content > .content-wrapper
|--------------------------------------------------------------------------
*/
?>

<style>
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
        background: #ff4c01;
        color: #fff;
        box-shadow: 0 5px 18px rgba(255, 76, 1, .35);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 22px;
        transition: .2s ease;
    }

    #readingToolsFab:hover {
        transform: translateY(-3px);
    }

    #readingToolsPanel {
        --rt-bg: #fff;
        --rt-text: #172033;
        --rt-border: #e5e7eb;
        position: fixed;
        right: 24px;
        bottom: 90px;
        z-index: 99991;
        width: min(340px, calc(100vw - 28px));
        max-height: min(76vh, 650px);
        overflow-y: auto;
        padding: 17px;
        border: 1px solid var(--rt-border);
        border-radius: 14px;
        background: var(--rt-bg);
        color: var(--rt-text);
        box-shadow: 0 12px 35px rgba(0, 0, 0, .2);
        font-family: Arial, sans-serif;
        display: none;
    }

    html[data-theme="dark"] #readingToolsPanel {
        --rt-bg: #1b202b;
        --rt-text: #f1f5f9;
        --rt-border: #343c4c;
    }

    #readingToolsPanel.is-open {
        display: block;
    }

    .rt-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-bottom: 12px;
        margin-bottom: 12px;
        border-bottom: 1px solid var(--rt-border);
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
        color: var(--rt-text);
        cursor: pointer;
        font-size: 20px;
    }

    .rt-group {
        margin: 0 0 15px;
    }

    .rt-label {
        display: block;
        margin-bottom: 8px;
        color: var(--rt-text);
        font-size: 12px;
        font-weight: 800;
    }

    .rt-muted {
        display: block;
        margin-top: 5px;
        color: var(--rt-text);
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
        border: 1px solid var(--rt-border);
        border-radius: 7px;
        background: var(--rt-bg);
        color: var(--rt-text);
        font-size: 12px;
        cursor: pointer;
    }

    .rt-btn:hover,
    .rt-btn.is-active {
        border-color: #ff4c01;
        color: #ff4c01;
    }

    .rt-select {
        width: 100%;
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
        border: 2px solid var(--rt-border);
        border-radius: 50%;
        cursor: pointer;
    }

    .rt-color.is-active {
        outline: 2px solid #ff4c01;
        outline-offset: 2px;
    }

    .rt-range {
        width: 100%;
        accent-color: #ff4c01;
        cursor: pointer;
    }

    .rt-range-labels {
        display: flex;
        justify-content: space-between;
        color: var(--rt-text);
        opacity: .65;
        font-size: 10px;
    }

    .rt-scroll-status {
        margin-top: 8px;
        padding: 8px 10px;
        border-radius: 7px;
        background: rgba(127, 127, 127, .1);
        color: var(--rt-text);
        font-size: 11px;
    }

    #rtScrollStatus.active {
        color: #16a34a;
        font-weight: 700;
    }


    /* =========================================================
       PERSISTENT READING / FULL-SCREEN MODE
       Stored in localStorage so it remains active across pages
       that include this script.
    ========================================================== */

    html.async-reading-fullscreen,
    html.async-reading-fullscreen body {
        min-height: 100%;
    }

    /* Hide the shared navigation while reading */
    html.async-reading-fullscreen .sidebar,
    html.async-reading-fullscreen #sidebar,
    html.async-reading-fullscreen .main-sidebar,
    html.async-reading-fullscreen .sidebar-wrapper,
    html.async-reading-fullscreen .sidebar-container,
    html.async-reading-fullscreen .topbar,
    html.async-reading-fullscreen #topbar,
    html.async-reading-fullscreen .top-bar,
    html.async-reading-fullscreen header.topbar,
    html.async-reading-fullscreen .main-header {
        display: none !important;
    }

    /* Let the academic content use the available viewport */
    html.async-reading-fullscreen .main-content,
    html.async-reading-fullscreen main.main-content {
        width: 100% !important;
        max-width: none !important;
        margin-left: 0 !important;
        padding-left: clamp(14px, 3vw, 42px) !important;
        padding-right: clamp(14px, 3vw, 42px) !important;
    }

    html.async-reading-fullscreen .main-content>.content-wrapper,
    html.async-reading-fullscreen main.main-content>.content-wrapper {
        width: 100% !important;
        max-width: 1500px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    #rtFullscreenToggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 40px;
        margin: 0 0 15px;
        padding: 9px 12px;
        border: 1px solid rgba(255, 76, 1, .35);
        border-radius: 8px;
        background: rgba(255, 76, 1, .09);
        color: #ff4c01;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s ease;
    }

    #rtFullscreenToggle:hover {
        background: rgba(255, 76, 1, .16);
        border-color: #ff4c01;
    }

    #rtFullscreenHint {
        display: block;
        margin: -7px 0 14px;
        color: var(--rt-text);
        opacity: .65;
        font-size: 10px;
        line-height: 1.4;
    }

    @media(max-width:480px) {
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
    <button type="button" id="readingToolsFab"
        aria-label="Open reading tools"
        aria-controls="readingToolsPanel"
        aria-expanded="false" title="Reading tools">
        <i class="bi bi-book-half" aria-hidden="true"></i>
    </button>

    <section id="readingToolsPanel" aria-label="Reading tools">
        <div class="rt-header">
            <strong><i class="bi bi-sliders"></i> Reading Tools</strong>
            <button type="button" class="rt-close" id="rtClose"
                aria-label="Close reading tools">&times;</button>
        </div>

        <button type="button" id="rtFullscreenToggle"
            aria-pressed="false">
            <i class="bi bi-arrows-fullscreen" aria-hidden="true"></i>
            <span>Enter reading full screen</span>
        </button>
        <small id="rtFullscreenHint">
            Hides the site navigation and keeps reading mode enabled as you move between pages.
        </small>

        <div class="rt-group">
            <span class="rt-label">Text size</span>
            <div class="rt-button-row">
                <button type="button" class="rt-btn" id="rtTextDown">A−</button>
                <button type="button" class="rt-btn" id="rtTextReset">Reset</button>
                <button type="button" class="rt-btn" id="rtTextUp">A+</button>
                <span id="rtTextSizeValue" class="rt-muted" style="margin:0">100%</span>
            </div>
        </div>

        <div class="rt-group">
            <span class="rt-label">Page color</span>
            <div class="rt-color-row" id="rtPageColors">
                <button type="button" class="rt-color is-active" data-color="#ffffff" aria-label="White" style="background:#fff"></button>
                <button type="button" class="rt-color" data-color="#fff4df" aria-label="Cream" style="background:#fff4df"></button>
                <button type="button" class="rt-color" data-color="#eaf2ff" aria-label="Light blue" style="background:#eaf2ff"></button>
                <button type="button" class="rt-color" data-color="#20242d" aria-label="Dark" style="background:#20242d"></button>
                <button type="button" class="rt-color" data-color="#e8f3e8" aria-label="Soft green" style="background:#e8f3e8"></button>
            </div>
        </div>

        <div class="rt-group">
            <span class="rt-label">Text color</span>
            <div class="rt-color-row" id="rtTextColors">
                <button type="button" class="rt-color is-active" data-color="#172033" aria-label="Dark text" style="background:#172033"></button>
                <button type="button" class="rt-color" data-color="#ffffff" aria-label="White text" style="background:#fff"></button>
                <button type="button" class="rt-color" data-color="#1d4ed8" aria-label="Blue text" style="background:#1d4ed8"></button>
                <button type="button" class="rt-color" data-color="#9a3412" aria-label="Brown text" style="background:#9a3412"></button>
                <button type="button" class="rt-color" data-color="#166534" aria-label="Green text" style="background:#166534"></button>
            </div>
        </div>

        <div class="rt-group">
            <label class="rt-label" for="rtFont">Font family</label>
            <select class="rt-select" id="rtFont">
                <option value="">Default font</option>
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
                <button type="button" class="rt-btn" id="rtScrollStart">▶ Start</button>
                <button type="button" class="rt-btn" id="rtScrollPause">Ⅱ Pause</button>
                <button type="button" class="rt-btn" id="rtScrollStop">■ Stop</button>
            </div>
            <div class="rt-scroll-status" id="rtScrollStatus">Auto-scroll is stopped.</div>
        </div>

        <div class="rt-button-row">
            <button type="button" class="rt-btn" id="rtResetAll">Reset all settings</button>
        </div>
        <small class="rt-muted">Preferences are saved in this browser on this device.</small>
    </section>
</div>

<script>
    (() => {
        'use strict';

        const root = document.getElementById('readingToolsRoot');
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        const post = document.querySelector('main.main-content > .content-wrapper');
        const panel = document.getElementById('readingToolsPanel');
        const fab = document.getElementById('readingToolsFab');
        const status = document.getElementById('rtScrollStatus');
        const fontSelect = document.getElementById('rtFont');
        const speedInput = document.getElementById('rtScrollSpeed');
        const fullscreenButton = document.getElementById('rtFullscreenToggle');
        const fullscreenHint = document.getElementById('rtFullscreenHint');

        const fullscreenStorageKey = 'asyncAcademicPersistentFullscreen';

        function isPersistentFullscreenEnabled() {
            try {
                return localStorage.getItem(fullscreenStorageKey) === '1';
            } catch (e) {
                return false;
            }
        }

        function setPersistentFullscreen(enabled) {
            document.documentElement.classList.toggle(
                'async-reading-fullscreen',
                enabled
            );

            try {
                localStorage.setItem(
                    fullscreenStorageKey,
                    enabled ? '1' : '0'
                );
            } catch (e) {
                // The mode still works for this page if storage is unavailable.
            }

            if (fullscreenButton) {
                fullscreenButton.setAttribute('aria-pressed', String(enabled));
                fullscreenButton.innerHTML = enabled ?
                    '<i class="bi bi-fullscreen-exit" aria-hidden="true"></i><span>Exit reading full screen</span>' :
                    '<i class="bi bi-arrows-fullscreen" aria-hidden="true"></i><span>Enter reading full screen</span>';
            }

            if (fullscreenHint) {
                fullscreenHint.textContent = enabled ?
                    'Reading mode is on. It will remain on when you open another page that includes these tools.' :
                    'Hides the site navigation and keeps reading mode enabled as you move between pages.';
            }
        }

        // Restore the student's choice automatically on every page using this file.
        setPersistentFullscreen(isPersistentFullscreenEnabled());

        if (fullscreenButton) {
            fullscreenButton.addEventListener('click', () => {
                setPersistentFullscreen(!isPersistentFullscreenEnabled());
            });
        }

        const storageKey = 'asyncAcademicReadingPreferences';
        const defaults = {
            scale: 1,
            page: '#ffffff',
            text: '#172033',
            font: '',
            speed: 3
        };

        let prefs = {
            ...defaults
        };
        let raf = null;

        try {
            prefs = {
                ...defaults,
                ...JSON.parse(localStorage.getItem(storageKey) || '{}')
            };
        } catch (e) {
            prefs = {
                ...defaults
            };
        }

        const textElements = post ? post.querySelectorAll(
            'h1,h2,h3,h4,h5,h6,p,li,blockquote,td,th,figcaption,.concept-box span,.definition-box span,.result-box span,.application-item span,.key-point-grid span,.theory-header p,.section-label,.reference-note-title,.example-description strong'
        ) : [];

        const originalSizes = new WeakMap();

        textElements.forEach(el => {
            originalSizes.set(el, parseFloat(getComputedStyle(el).fontSize) || 14);
        });

        function save() {
            try {
                localStorage.setItem(storageKey, JSON.stringify(prefs));
            } catch (e) {}
        }

        function setColorSelection(group, color) {
            document.querySelectorAll(group + ' .rt-color').forEach(btn => {
                btn.classList.toggle('is-active', btn.dataset.color === color);
            });
        }

        function apply() {
            if (!post) {
                status.textContent = 'Could not find .content-wrapper inside main.main-content.';
                return;
            }

            post.style.setProperty('--theory-bg', prefs.page);
            post.style.setProperty('--theory-hover', prefs.page);
            post.style.setProperty('--theory-header', prefs.page);
            post.style.setProperty('--theory-text', prefs.text);
            post.style.setProperty('--theory-secondary', prefs.text);
            post.style.setProperty('--theory-muted', prefs.text);
            post.style.setProperty('--theory-blue', prefs.text);
            post.style.setProperty('--theory-green', prefs.text);
            post.style.setProperty('--theory-red', prefs.text);

            textElements.forEach(el => {
                const base = originalSizes.get(el) || 14;

                el.style.setProperty(
                    'font-size',
                    (base * prefs.scale) + 'px',
                    'important'
                );

                el.style.setProperty('color', prefs.text, 'important');

                if (prefs.font) {
                    el.style.setProperty('font-family', prefs.font, 'important');
                } else {
                    el.style.removeProperty('font-family');
                }
            });

            post.style.setProperty('color', prefs.text, 'important');
            post.style.setProperty('background-color', prefs.page, 'important');

            post.querySelectorAll(
                '.theory-card,.reference-note,.theory-header,.theory-body,.example-description,.code-box,.visualizer-controls,.chart-panel,.concept-box,.definition-box,.result-box,.application-item,.reference-note-title,.comparison-table th,.comparison-table td,.key-point-grid>div'
            ).forEach(el => {
                el.style.setProperty('background-color', prefs.page, 'important');
                el.style.setProperty('color', prefs.text, 'important');
            });

            document.getElementById('rtTextSizeValue').textContent =
                Math.round(prefs.scale * 100) + '%';

            fontSelect.value = prefs.font;
            speedInput.value = prefs.speed;

            setColorSelection('#rtPageColors', prefs.page);
            setColorSelection('#rtTextColors', prefs.text);
        }

        function togglePanel(open) {
            panel.classList.toggle('is-open', open);
            fab.setAttribute('aria-expanded', String(open));
        }

        fab.addEventListener('click', () => {
            togglePanel(!panel.classList.contains('is-open'));
        });

        document.getElementById('rtClose').addEventListener('click', () => {
            togglePanel(false);
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') togglePanel(false);
        });

        document.getElementById('rtTextDown').addEventListener('click', () => {
            prefs.scale = Math.max(.7, +(prefs.scale - .1).toFixed(2));
            save();
            apply();
        });

        document.getElementById('rtTextUp').addEventListener('click', () => {
            prefs.scale = Math.min(1.6, +(prefs.scale + .1).toFixed(2));
            save();
            apply();
        });

        document.getElementById('rtTextReset').addEventListener('click', () => {
            prefs.scale = 1;
            save();
            apply();
        });

        document.querySelectorAll('#rtPageColors .rt-color').forEach(btn => {
            btn.addEventListener('click', () => {
                prefs.page = btn.dataset.color;
                save();
                apply();
            });
        });

        document.querySelectorAll('#rtTextColors .rt-color').forEach(btn => {
            btn.addEventListener('click', () => {
                prefs.text = btn.dataset.color;
                save();
                apply();
            });
        });

        fontSelect.addEventListener('change', () => {
            prefs.font = fontSelect.value;
            save();
            apply();
        });

        speedInput.addEventListener('input', () => {
            prefs.speed = Number(speedInput.value);
            save();
        });

        function stopScroll(message) {
            if (raf !== null) cancelAnimationFrame(raf);
            raf = null;
            status.textContent = message || 'Auto-scroll is stopped.';
            status.classList.remove('active');
        }

        function scrollStep() {
            if (raf === null) return;

            window.scrollBy(0, Number(prefs.speed) * .35);

            if (
                window.innerHeight + window.scrollY >=
                document.documentElement.scrollHeight - 2
            ) {
                stopScroll('Reached the bottom of the page.');
                return;
            }

            raf = requestAnimationFrame(scrollStep);
        }

        document.getElementById('rtScrollStart').addEventListener('click', () => {
            if (raf !== null) return;

            status.textContent = 'Auto-scroll is running.';
            status.classList.add('active');
            raf = requestAnimationFrame(scrollStep);
        });

        document.getElementById('rtScrollPause').addEventListener('click', () => {
            if (raf === null) {
                status.textContent = 'Auto-scroll is paused or stopped.';
                return;
            }

            cancelAnimationFrame(raf);
            raf = null;
            status.textContent = 'Auto-scroll is paused.';
            status.classList.remove('active');
        });

        document.getElementById('rtScrollStop').addEventListener('click', () => {
            stopScroll();
        });

        document.getElementById('rtResetAll').addEventListener('click', () => {
            stopScroll();
            prefs = {
                ...defaults
            };
            save();
            apply();
        });

        apply();
    })();
</script>