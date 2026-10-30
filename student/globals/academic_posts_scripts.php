<?php
/*
|--------------------------------------------------------------------------
| ASYNC ACADEMIC POSTS - FLOATING READING TOOLS
|--------------------------------------------------------------------------
| Include this file immediately before </body> on academic post pages.
|
| This version follows the supplied post structure:
| main.main-content > .content-wrapper
|
| It changes the academic post area only. Sidebar, topbar, and global
| navigation are not modified.
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
        background: #4169e1;
        color: #fff;
        box-shadow: 0 5px 18px rgba(65, 105, 225, .28);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 22px;
        transition: .2s ease;
    }

    #readingToolsFab:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(65, 105, 225, .38);
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
        border: 1px solid var(--rt-border);
        border-radius: 14px;
        background: var(--rt-bg);
        color: var(--rt-text);
        box-shadow: 0 12px 35px rgba(0, 0, 0, .2);
        font-family: Arial, sans-serif;
        display: none;
    }

    #readingToolsPanel.is-open {
        display: block;
        animation: rtPanelIn .18s ease-out;
    }

    @keyframes rtPanelIn {
        from {
            opacity: 0;
            transform: translateY(8px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    #readingToolsPanel {
        --rt-bg: #fff;
        --rt-text: #172033;
        --rt-border: #e5e7eb;
    }

    html[data-theme="dark"] #readingToolsPanel {
        --rt-bg: #1b202b;
        --rt-text: #f1f5f9;
        --rt-border: #343c4c;
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
        border-color: #4169e1;
        color: #4169e1;
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
        border: 2px solid var(--rt-border);
        border-radius: 50%;
        cursor: pointer;
    }

    .rt-color.is-active {
        outline: 2px solid #4169e1;
        outline-offset: 2px;
    }

    .rt-range {
        width: 100%;
        accent-color: #4169e1;
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

    /* Fullscreen reading layout: hide site navigation and let the article use the screen. */
    html.async-reading-fullscreen,
    html.async-reading-fullscreen body {
        width: 100% !important;
        min-height: 100% !important;
        margin: 0 !important;
    }

    html.async-reading-fullscreen body {
        overflow-x: hidden !important;
    }

    html.async-reading-fullscreen .sidebar,
    html.async-reading-fullscreen #sidebar,
    html.async-reading-fullscreen .sidebar-container,
    html.async-reading-fullscreen .main-sidebar,
    html.async-reading-fullscreen aside.sidebar,
    html.async-reading-fullscreen .topbar,
    html.async-reading-fullscreen #topbar,
    html.async-reading-fullscreen .top-bar,
    html.async-reading-fullscreen header.topbar,
    html.async-reading-fullscreen .navbar {
        display: none !important;
    }

    html.async-reading-fullscreen main.main-content {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        margin-left: 0 !important;
        padding-top: 18px !important;
    }

    html.async-reading-fullscreen main.main-content>.content-wrapper {
        width: min(1200px, calc(100% - 36px)) !important;
        max-width: 1200px !important;
        margin: 0 auto !important;
    }

    #asyncFloatingTimer {
        position: fixed;
        z-index: 100005;
        top: 110px;
        right: 24px;
        width: min(270px, calc(100vw - 24px));
        padding: 14px;
        border: 1px solid rgba(255, 255, 255, .42);
        border-radius: 18px;
        background: rgba(18, 35, 78, .48);
        color: #fff;
        box-shadow: 0 12px 38px rgba(15, 23, 42, .20), inset 0 1px 0 rgba(255, 255, 255, .45);
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        display: none;
        user-select: none;
        font-family: var(--aft-font, inherit);
    }

    #asyncFloatingTimer.is-visible {
        display: block;
    }

    html[data-theme="dark"] #asyncFloatingTimer {
        background: rgba(12, 20, 42, .62);
        border-color: rgba(255, 255, 255, .18);
        color: #fff;
    }

    .aft-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: grab;
        padding-bottom: 9px;
        border-bottom: 1px solid rgba(127, 127, 127, .22);
        touch-action: none;
    }

    .aft-header:active {
        cursor: grabbing;
    }

    .aft-header strong {
        font-size: 13px;
        font-weight: 800;
    }

    #aftClose,
    #aftMinimize {
        border: 0;
        background: transparent;
        color: #fff;
        font-size: 21px;
        cursor: pointer;
        line-height: 1;
        padding: 4px 7px;
        border-radius: 7px;
    }

    #aftMinimize:hover,
    #aftClose:hover {
        background: rgba(255, 255, 255, .16);
    }

    #asyncFloatingTimer .aft-header strong,
    #asyncFloatingTimer .aft-display,
    #asyncFloatingTimer #aftStatus,
    #asyncFloatingTimer .aft-inputs label {
        color: #fff !important;
    }

    #asyncFloatingTimer.is-minimized {
        width: auto;
        min-width: 175px;
        padding: 10px 12px;
    }

    #asyncFloatingTimer.is-minimized .aft-display {
        padding: 4px 0 0;
        font-size: 25px;
    }

    #asyncFloatingTimer.is-minimized .aft-inputs,
    #asyncFloatingTimer.is-minimized .aft-actions,
    #asyncFloatingTimer.is-minimized #aftStatus {
        display: none;
    }

    .aft-display {
        text-align: center;
        font-size: clamp(32px, 8vw, 40px);
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        letter-spacing: .04em;
        padding: 13px 0;
        color: #4169e1;
    }

    .aft-inputs {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .aft-inputs label {
        display: grid;
        gap: 4px;
        font-size: 11px;
        text-align: center;
    }

    .aft-inputs input {
        width: 75px;
        max-width: 35vw;
        padding: 7px;
        border: 1px solid rgba(127, 127, 127, .4);
        border-radius: 9px;
        background: rgba(255, 255, 255, .15);
        color: #fff;
        text-align: center;
        font: inherit;
    }

    .aft-actions {
        display: flex;
        gap: 7px;
        margin-top: 11px;
    }

    .aft-actions button {
        flex: 1;
        min-height: 36px;
        padding: 7px 4px;
        border: 1px solid rgba(255, 255, 255, .48);
        border-radius: 9px;
        background: rgba(255, 255, 255, .16);
        color: #fff;
        cursor: pointer;
        font: inherit;
        font-size: 12px;
    }

    .aft-actions button:first-child {
        background: #4169e1;
        color: #fff;
    }

    #aftStatus {
        display: block;
        margin-top: 9px;
        text-align: center;
        opacity: .72;
        font-size: 10px;
    }

    #aftAlarmOverlay {
        position: fixed;
        inset: 0;
        z-index: 100020;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(8, 13, 28, .62);
        backdrop-filter: blur(7px);
        -webkit-backdrop-filter: blur(7px);
    }

    #aftAlarmOverlay.is-visible {
        display: flex;
    }

    #aftAlarmModal {
        width: min(360px, 100%);
        padding: 25px 22px;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, .32);
        border-radius: 20px;
        color: #fff;
        background: rgba(24, 43, 88, .48);
        box-shadow: 0 22px 70px rgba(0, 0, 0, .38), inset 0 1px 0 rgba(255, 255, 255, .24);
        backdrop-filter: blur(22px) saturate(160%);
        -webkit-backdrop-filter: blur(22px) saturate(160%);
        font-family: var(--aft-font, system-ui, sans-serif);
    }

    #aftAlarmModal h2,
    #aftAlarmModal p {
        color: #fff;
    }

    #aftAlarmModal .aft-alarm-icon {
        font-size: 38px;
        color: #4169e1;
        margin-bottom: 8px;
    }

    #aftAlarmModal h2 {
        margin: 0 0 7px;
        font-size: 22px;
    }

    #aftAlarmModal p {
        margin: 0 0 18px;
        opacity: .75;
        font-size: 13px;
    }

    #aftStopAlarm {
        width: 100%;
        min-height: 44px;
        border: 0;
        border-radius: 11px;
        background: #4169e1;
        color: #fff;
        font-weight: 800;
        cursor: pointer;
    }

    @media(max-width:480px) {
        #readingToolsFab {
            right: 16px;
            bottom: 16px
        }

        #readingToolsPanel {
            right: 14px;
            bottom: 80px
        }

        #asyncFloatingTimer {
            top: auto;
            right: 12px;
            bottom: 12px;
            width: min(270px, calc(100vw - 24px));
        }

        #aftAlarmModal {
            padding: 22px 17px;
        }
    }

    @media(max-width:480px) {
        #readingToolsFab {
            right: 16px;
            bottom: 16px
        }

        #readingToolsPanel {
            right: 14px;
            bottom: 80px
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
            <span class="rt-label">Browser fullscreen</span>
            <button type="button" class="rt-btn" id="rtFullscreenToggle" style="width:100%;padding:11px;border-color:#4169e1;color:#4169e1">
                <i class="bi bi-arrows-fullscreen"></i> Enter Fullscreen
            </button>
            <small class="rt-muted" id="rtFullscreenHint">Fullscreen hides the browser tabs and address bar, similar to F11.</small>
        </div>

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
                <button type="button" class="rt-btn" id="rtScrollStart"><i class="bi bi-play-fill"></i> Start</button>
                <button type="button" class="rt-btn" id="rtScrollPause"><i class="bi bi-pause-fill"></i> Pause</button>
                <button type="button" class="rt-btn" id="rtScrollStop"><i class="bi bi-stop-fill"></i> Stop</button>
            </div>
            <div class="rt-scroll-status" id="rtScrollStatus">Auto-scroll is stopped.</div>
        </div>

        <div class="rt-group">
            <span class="rt-label">Floating timer</span>
            <button type="button" class="rt-btn" id="rtOpenTimer" style="width:100%"><i class="bi bi-stopwatch"></i> Open Timer</button>
            <small class="rt-muted">Set a countdown, drag it anywhere, and hear an alarm when time is up.</small>
        </div>

        <div class="rt-button-row">
            <button type="button" class="rt-btn" id="rtResetAll">Reset all settings</button>
        </div>
        <small class="rt-muted">Preferences are saved in this browser on this device.</small>
    </section>
</div>

<div id="asyncFloatingTimer" role="dialog" aria-label="Floating countdown timer" aria-hidden="true">
    <div id="asyncTimerDrag" class="aft-header">
        <strong><i class="bi bi-stopwatch"></i> ASYNC Timer</strong>
        <span style="display:flex;align-items:center;gap:3px">
            <button type="button" id="aftMinimize" aria-label="Minimize timer" title="Minimize timer"><i class="bi bi-dash-lg"></i></button>
            <button type="button" id="aftClose" aria-label="Close timer">&times;</button>
        </span>
    </div>
    <div class="aft-display" id="aftDisplay">05:00</div>
    <div class="aft-inputs">
        <label>Min<input id="aftMinutes" type="number" min="0" max="999" value="5"></label>
        <label>Sec<input id="aftSeconds" type="number" min="0" max="59" value="0"></label>
    </div>
    <div class="aft-actions">
        <button type="button" id="aftStart">Start</button>
        <button type="button" id="aftPause">Pause</button>
        <button type="button" id="aftReset">Reset</button>
    </div>
    <small id="aftStatus">Drag this panel by its header.</small>
</div>

<div id="aftAlarmOverlay" role="alertdialog" aria-modal="true" aria-labelledby="aftAlarmTitle" aria-describedby="aftAlarmMessage">
    <div id="aftAlarmModal">
        <div class="aft-alarm-icon"><i class="bi bi-alarm-fill" aria-hidden="true"></i></div>
        <h2 id="aftAlarmTitle">Time is up!</h2>
        <p id="aftAlarmMessage">Your countdown has finished. Press the button below to stop the alarm.</p>
        <button type="button" id="aftStopAlarm">Stop Alarm</button>
    </div>
</div>

<script>
    (() => {
        'use strict';

        const root = document.getElementById('readingToolsRoot');
        if (!root || root.dataset.initialized === '1') return;
        root.dataset.initialized = '1';

        // Matches the supplied academic post layout: main.main-content > .content-wrapper.
        const post = document.querySelector('main.main-content > .content-wrapper');
        const panel = document.getElementById('readingToolsPanel');
        const fab = document.getElementById('readingToolsFab');
        const status = document.getElementById('rtScrollStatus');
        const fontSelect = document.getElementById('rtFont');
        const speedInput = document.getElementById('rtScrollSpeed');
        const fullscreenButton = document.getElementById('rtFullscreenToggle');
        const fullscreenHint = document.getElementById('rtFullscreenHint');
        const fullscreenKey = 'asyncAcademicFullscreenPreferred';

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

        // Floating timer: per-second tick and persistent alarm, synthesized with Web Audio.
        const timerBox = document.getElementById('asyncFloatingTimer');
        const timerDisplay = document.getElementById('aftDisplay');
        const timerStatus = document.getElementById('aftStatus');
        const timerMin = document.getElementById('aftMinutes');
        const timerSec = document.getElementById('aftSeconds');
        const alarmOverlay = document.getElementById('aftAlarmOverlay');
        const stopAlarmButton = document.getElementById('aftStopAlarm');
        let timerRemaining = 300,
            timerInterval = null,
            timerEndAt = 0;
        let audioContext = null,
            alarmLoop = null,
            alarmActive = false;

        function formatTimer(seconds) {
            const m = Math.floor(seconds / 60),
                sec = seconds % 60;
            return String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
        }

        function renderTimer() {
            timerDisplay.textContent = formatTimer(Math.max(0, Math.ceil(timerRemaining)));
        }

        function getAudioContext() {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return null;
            if (!audioContext) audioContext = new AudioCtx();
            return audioContext;
        }
        async function unlockAudio() {
            const ctx = getAudioContext();
            if (!ctx) throw new Error('Web Audio is not supported by this browser.');
            if (ctx.state !== 'running') await ctx.resume();
            if (ctx.state !== 'running') throw new Error('Audio is blocked by the browser.');
            return ctx;
        }

        function playTone(frequency, duration, volume, type = 'sine') {
            try {
                const ctx = getAudioContext();
                if (!ctx || ctx.state !== 'running') return;
                const osc = ctx.createOscillator(),
                    gain = ctx.createGain();
                osc.type = type;
                osc.frequency.setValueAtTime(frequency, ctx.currentTime);
                gain.gain.setValueAtTime(0.0001, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(Math.min(volume, 0.8), ctx.currentTime + 0.02);
                gain.gain.setValueAtTime(Math.min(volume, 0.8), ctx.currentTime + Math.max(0.04, duration * 0.65));
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + duration + 0.03);
            } catch (e) {
                console.warn('ASYNC timer sound:', e);
            }
        }

        function tickSound() {
            playTone(1250, 0.12, 0.20, 'square');
        }

        function alarmSound() {
            if (alarmLoop !== null) clearInterval(alarmLoop);
            const sequence = () => {
                playTone(980, 0.42, 0.72, 'square');
                setTimeout(() => playTone(740, 0.42, 0.72, 'square'), 470);
                setTimeout(() => playTone(980, 0.42, 0.72, 'square'), 940);
            };
            sequence();
            alarmLoop = setInterval(sequence, 1450);
        }

        function stopAlarm() {
            alarmActive = false;
            if (alarmLoop !== null) clearInterval(alarmLoop);
            alarmLoop = null;
            alarmOverlay.classList.remove('is-visible');
            alarmOverlay.setAttribute('aria-hidden', 'true');
        }

        function stopTimerInterval() {
            if (timerInterval !== null) clearInterval(timerInterval);
            timerInterval = null;
        }

        function finishTimer() {
            stopTimerInterval();
            timerRemaining = 0;
            renderTimer();
            timerStatus.textContent = 'Time is up — alarm is active.';
            alarmActive = true;
            alarmOverlay.classList.add('is-visible');
            alarmOverlay.setAttribute('aria-hidden', 'false');
            alarmSound();
            stopAlarmButton.focus();
        }
        async function startTimer() {
            if (timerInterval !== null || alarmActive) return;
            if (timerRemaining <= 0) timerRemaining = Math.max(0, Number(timerMin.value || 0) * 60 + Number(timerSec.value || 0));
            if (timerRemaining <= 0) {
                timerStatus.textContent = 'Set a time greater than zero.';
                return;
            }
            try {
                await unlockAudio(); // Must be unlocked from the Start button gesture.
            } catch (error) {
                timerStatus.textContent = 'Allow sound for this site, then press Start again.';
                console.warn('ASYNC audio unlock failed:', error);
                return;
            }
            timerEndAt = Date.now() + timerRemaining * 1000;
            timerStatus.textContent = 'Timer running…';
            let lastTick = timerRemaining;
            timerInterval = setInterval(() => {
                const next = Math.max(0, Math.ceil((timerEndAt - Date.now()) / 1000));
                if (next < lastTick && next > 0) tickSound();
                lastTick = next;
                timerRemaining = next;
                renderTimer();
                if (timerRemaining <= 0) finishTimer();
            }, 50);
        }

        function resetTimer() {
            stopTimerInterval();
            stopAlarm();
            timerRemaining = Math.max(0, Number(timerMin.value || 0) * 60 + Number(timerSec.value || 0));
            renderTimer();
            timerStatus.textContent = 'Timer reset.';
        }
        document.getElementById('rtOpenTimer').addEventListener('click', () => {
            timerBox.classList.add('is-visible');
            timerBox.setAttribute('aria-hidden', 'false');
            togglePanel(false);
        });
        document.getElementById('aftMinimize').addEventListener('click', () => {
            const minimized = timerBox.classList.toggle('is-minimized');
            const button = document.getElementById('aftMinimize');
            button.innerHTML = minimized ? '<i class="bi bi-arrows-angle-expand"></i>' : '<i class="bi bi-dash-lg"></i>';
            button.setAttribute('aria-label', minimized ? 'Expand timer' : 'Minimize timer');
            button.title = minimized ? 'Expand timer' : 'Minimize timer';
        });
        document.getElementById('aftClose').addEventListener('click', () => {
            if (alarmActive) return;
            timerBox.classList.remove('is-visible');
            timerBox.setAttribute('aria-hidden', 'true');
        });
        document.getElementById('aftStart').addEventListener('click', startTimer);
        document.getElementById('aftPause').addEventListener('click', () => {
            if (timerInterval !== null) {
                timerRemaining = Math.max(0, Math.ceil((timerEndAt - Date.now()) / 1000));
                stopTimerInterval();
                renderTimer();
                timerStatus.textContent = 'Paused.';
            }
        });
        document.getElementById('aftReset').addEventListener('click', resetTimer);
        stopAlarmButton.addEventListener('click', () => {
            stopAlarm();
            timerStatus.textContent = 'Alarm stopped. Set a new countdown.';
        });
        // Do not let Escape or backdrop clicks dismiss the alarm: only Stop Alarm can.
        alarmOverlay.addEventListener('click', e => {
            if (e.target === alarmOverlay && alarmActive) e.stopPropagation();
        });
        document.addEventListener('keydown', e => {
            if (alarmActive && e.key === 'Escape') {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }, true);
        [timerMin, timerSec].forEach(input => input.addEventListener('change', () => {
            if (timerInterval === null && !alarmActive) {
                timerSec.value = Math.min(59, Math.max(0, Number(timerSec.value || 0)));
                timerMin.value = Math.min(999, Math.max(0, Number(timerMin.value || 0)));
                resetTimer();
            }
        }));
        // Pointer-based dragging works with mouse and touch; keep timer in viewport.
        const dragHandle = document.getElementById('asyncTimerDrag');
        let dragState = null;
        dragHandle.addEventListener('pointerdown', e => {
            if (e.target.closest('button')) return;
            const r = timerBox.getBoundingClientRect();
            dragState = {
                x: e.clientX,
                y: e.clientY,
                left: r.left,
                top: r.top
            };
            try {
                dragHandle.setPointerCapture(e.pointerId);
            } catch (_) {}
        });
        dragHandle.addEventListener('pointermove', e => {
            if (!dragState) return;
            const left = Math.max(0, Math.min(window.innerWidth - timerBox.offsetWidth, dragState.left + e.clientX - dragState.x));
            const top = Math.max(0, Math.min(window.innerHeight - timerBox.offsetHeight, dragState.top + e.clientY - dragState.y));
            timerBox.style.left = left + 'px';
            timerBox.style.top = top + 'px';
            timerBox.style.right = 'auto';
            timerBox.style.bottom = 'auto';
        });
        ['pointerup', 'pointercancel', 'lostpointercapture'].forEach(name => dragHandle.addEventListener(name, () => {
            dragState = null;
        }));
        renderTimer();

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

        // Cache each element's original font size so repeated adjustments never compound.
        const textElements = post ? post.querySelectorAll(
            'h1,h2,h3,h4,h5,h6,p,li,blockquote,td,th,figcaption,.concept-box span,.definition-box span,.result-box span,.application-item span,.key-point-grid span,.theory-header p,.section-label,.reference-note-title,.example-description strong'
        ) : [];
        const originalSizes = new WeakMap();
        textElements.forEach(el => originalSizes.set(el, parseFloat(getComputedStyle(el).fontSize) || 14));

        // Preserve the page's original inline styles so Reset restores its real light/dark theme.
        const originalInlineStyles = new Map();
        if (post) {
            [post, ...post.querySelectorAll('*')].forEach(el => originalInlineStyles.set(el, el.getAttribute('style')));
        }

        function restoreOriginalPostStyles() {
            originalInlineStyles.forEach((style, el) => {
                if (!el.isConnected) return;
                if (style === null) el.removeAttribute('style');
                else el.setAttribute('style', style);
            });
        }

        function syncTimerTypography() {
            const source = post || document.body;
            const computed = getComputedStyle(source);
            const chosenFont = prefs.font || computed.fontFamily || 'system-ui, sans-serif';
            timerBox.style.setProperty('--aft-font', chosenFont);
            timerBox.style.setProperty('--aft-text', '#ffffff');
            timerBox.style.fontFamily = chosenFont;
            document.getElementById('aftAlarmModal').style.fontFamily = chosenFont;
        }

        function save() {
            try {
                localStorage.setItem(storageKey, JSON.stringify(prefs));
            } catch (e) {}
        }

        function setColorSelection(group, color) {
            document.querySelectorAll(group + ' .rt-color').forEach(btn =>
                btn.classList.toggle('is-active', btn.dataset.color === color)
            );
        }

        function apply() {
            if (!post) {
                status.textContent = 'Could not find .content-wrapper inside main.main-content.';
                return;
            }

            // Native theme mode restores the page exactly as its original CSS defines it.
            if (prefs.nativeTheme) {
                restoreOriginalPostStyles();
                document.getElementById('rtTextSizeValue').textContent = Math.round(prefs.scale * 100) + '%';
                fontSelect.value = prefs.font;
                speedInput.value = prefs.speed;
                setColorSelection('#rtPageColors', prefs.page);
                setColorSelection('#rtTextColors', prefs.text);
                syncTimerTypography();
                return;
            }

            // Override the post's existing theme variables without changing global UI.
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
                el.style.setProperty('font-size', (base * prefs.scale) + 'px', 'important');
                el.style.setProperty('color', prefs.text, 'important');
                if (prefs.font) el.style.setProperty('font-family', prefs.font, 'important');
                else el.style.removeProperty('font-family');
            });

            // Also recolor post content wrappers/cards; navigation is outside .content-wrapper.
            post.style.setProperty('color', prefs.text, 'important');
            post.style.setProperty('background-color', prefs.page, 'important');
            post.querySelectorAll('.theory-card,.reference-note,.theory-header,.theory-body,.example-description,.code-box,.visualizer-controls,.chart-panel,.concept-box,.definition-box,.result-box,.application-item,.reference-note-title,.comparison-table th,.comparison-table td,.key-point-grid>div').forEach(el => {
                el.style.setProperty('background-color', prefs.page, 'important');
                el.style.setProperty('color', prefs.text, 'important');
            });

            document.getElementById('rtTextSizeValue').textContent = Math.round(prefs.scale * 100) + '%';
            fontSelect.value = prefs.font;
            speedInput.value = prefs.speed;
            setColorSelection('#rtPageColors', prefs.page);
            setColorSelection('#rtTextColors', prefs.text);
            syncTimerTypography();
        }

        function togglePanel(open) {
            panel.classList.toggle('is-open', open);
            fab.setAttribute('aria-expanded', String(open));
        }

        fab.addEventListener('click', () => togglePanel(!panel.classList.contains('is-open')));
        document.getElementById('rtClose').addEventListener('click', () => togglePanel(false));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') togglePanel(false);
        });

        document.getElementById('rtTextDown').addEventListener('click', () => {
            prefs.nativeTheme = false;
            prefs.scale = Math.max(.7, +(prefs.scale - .1).toFixed(2));
            save();
            apply();
        });
        document.getElementById('rtTextUp').addEventListener('click', () => {
            prefs.nativeTheme = false;
            prefs.scale = Math.min(1.6, +(prefs.scale + .1).toFixed(2));
            save();
            apply();
        });
        document.getElementById('rtTextReset').addEventListener('click', () => {
            prefs.nativeTheme = false;
            prefs.scale = 1;
            save();
            apply();
        });

        document.querySelectorAll('#rtPageColors .rt-color').forEach(btn => btn.addEventListener('click', () => {
            prefs.nativeTheme = false;
            prefs.page = btn.dataset.color;
            save();
            apply();
        }));
        document.querySelectorAll('#rtTextColors .rt-color').forEach(btn => btn.addEventListener('click', () => {
            prefs.nativeTheme = false;
            prefs.text = btn.dataset.color;
            save();
            apply();
        }));
        fontSelect.addEventListener('change', () => {
            prefs.nativeTheme = false;
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
            if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2) {
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
        document.getElementById('rtScrollStop').addEventListener('click', () => stopScroll());

        document.getElementById('rtResetAll').addEventListener('click', () => {
            stopScroll();
            prefs = {
                ...defaults,
                nativeTheme: true
            };
            try {
                localStorage.removeItem(storageKey);
            } catch (e) {}
            restoreOriginalPostStyles();
            fontSelect.value = '';
            speedInput.value = defaults.speed;
            document.getElementById('rtTextSizeValue').textContent = '100%';
            setColorSelection('#rtPageColors', defaults.page);
            setColorSelection('#rtTextColors', defaults.text);
            apply();
        });

        // Browser security requires a fresh click to enter fullscreen after page navigation.
        // Remember the user's preference so the next academic page offers Restore Fullscreen.
        function fullscreenPreferred() {
            try {
                return localStorage.getItem(fullscreenKey) === '1';
            } catch (e) {
                return false;
            }
        }

        function rememberFullscreen(enabled) {
            try {
                localStorage.setItem(fullscreenKey, enabled ? '1' : '0');
            } catch (e) {}
        }

        function setReadingFullscreenLayout(enabled) {
            document.documentElement.classList.toggle('async-reading-fullscreen', enabled);
        }

        function refreshFullscreenButton() {
            const active = !!document.fullscreenElement;
            const restore = !active && fullscreenPreferred();
            setReadingFullscreenLayout(active);
            fullscreenButton.innerHTML = active ?
                '<i class="bi bi-fullscreen-exit"></i> Exit Fullscreen' :
                restore ?
                '<i class="bi bi-arrows-fullscreen"></i> Restore Fullscreen' :
                '<i class="bi bi-arrows-fullscreen"></i> Enter Fullscreen';
            fullscreenButton.setAttribute('aria-pressed', String(active));
            fullscreenHint.textContent = active ?
                'Sidebar and topbar are hidden. Press Esc or click Exit Fullscreen to leave.' :
                restore ?
                'Click to restore fullscreen. Browsers require a new click after page navigation.' :
                'Hides the sidebar and topbar and expands the article. Browser fullscreen is also enabled.';
        }

        fullscreenButton.addEventListener('click', async () => {
            try {
                if (document.fullscreenElement) {
                    rememberFullscreen(false);
                    await document.exitFullscreen();
                } else {
                    // Browsers require a fresh user gesture after every full-page navigation.
                    await document.documentElement.requestFullscreen();
                    rememberFullscreen(true);
                }
                refreshFullscreenButton();
            } catch (error) {
                fullscreenHint.textContent = 'Fullscreen was blocked. Try your browser fullscreen key (F11 or Fn + F11).';
                console.error('ASYNC fullscreen:', error);
            }
        });

        document.addEventListener('fullscreenchange', refreshFullscreenButton);
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                if (document.fullscreenElement) rememberFullscreen(false);
                setReadingFullscreenLayout(false);
            }
        });
        refreshFullscreenButton();

        apply();
    })();
</script>