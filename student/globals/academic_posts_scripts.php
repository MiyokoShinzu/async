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

    /* Quiet, theme-aware floating study tools */
    #asyncNotesFab,
    #asyncCalcFab {
        position: fixed;
        right: 22px;
        z-index: 99989;
        width: 46px;
        height: 46px;
        border: 1px solid var(--aux-launch-border, rgba(255, 255, 255, .35));
        border-radius: 15px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 18px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .13);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    #asyncNotesFab {
        bottom: 82px;
        background: var(--aux-notes-fab, rgba(13, 148, 136, .72));
    }

    #asyncCalcFab {
        bottom: 138px;
        background: var(--aux-calc-fab, rgba(65, 105, 225, .72));
    }

    #asyncNotesPanel .aux-head {
        background: #0f8f83;
    }

    #asyncCalcPanel .aux-head {
        background: #4169e1;
    }

    #asyncNotesFab:hover,
    #asyncCalcFab:hover,
    #asyncNotesFab:focus-visible,
    #asyncCalcFab:focus-visible {
        opacity: 1;
        transform: translateY(-2px);
        box-shadow: 0 7px 20px rgba(15, 23, 42, .2);
        outline: none;
    }

    #asyncNotesPanel,
    #asyncCalcPanel {
        position: fixed;
        right: 22px;
        z-index: 100010;
        display: none;
        color: var(--aux-text, #172033);
        background: var(--aux-bg, rgba(255, 255, 255, .97));
        border: 1px solid var(--aux-border, rgba(148, 163, 184, .4));
        border-radius: 16px;
        box-shadow: 0 16px 45px rgba(15, 23, 42, .22);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        font-family: Arial, sans-serif;
        overflow: hidden;
    }

    #asyncNotesPanel.is-open,
    #asyncCalcPanel.is-open {
        display: flex;
        flex-direction: column;
    }

    #asyncNotesPanel {
        bottom: 138px;
        width: min(470px, calc(100vw - 28px));
        height: min(620px, 76vh);
    }

    #asyncCalcPanel {
        bottom: 138px;
        width: min(390px, calc(100vw - 28px));
        padding: 0 13px 13px;
        gap: 9px;
    }

    .aux-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 13px;
        color: #fff;
        background: var(--aux-accent, #4169e1);
        flex-shrink: 0;
        cursor: grab;
        touch-action: none;
        user-select: none;
    }

    .aux-head:active {
        cursor: grabbing;
    }

    .aux-head strong {
        font-size: 13px;
    }

    .aux-head button {
        border: 0;
        background: transparent;
        color: #fff;
        font-size: 21px;
        cursor: pointer;
        padding: 2px 6px;
    }

    .aux-head .aux-drag-hint {
        opacity: .75;
        font-size: 10px;
        margin-left: auto;
        margin-right: 5px;
    }

    #asyncNotesPanel .notes-toolbar {
        display: flex;
        gap: 7px;
        padding: 10px 12px;
        border-bottom: 1px solid var(--aux-border);
    }

    #asyncNotesPanel select,
    #asyncNotesPanel input,
    #asyncNotesPanel button,
    #asyncCalcPanel button,
    #asyncCalcPanel select {
        border: 1px solid var(--aux-border);
        border-radius: 8px;
        padding: 8px;
        font-size: 12px;
        color: var(--aux-text);
        background: var(--aux-control);
    }

    #asyncNotesPanel select {
        flex: 1;
        min-width: 0;
    }

    #asyncNotesPanel .notes-toolbar button,
    #asyncCalcPanel button {
        cursor: pointer;
    }

    #asyncNotesPanel #asyncNoteTitle {
        margin: 10px 12px 0;
        width: calc(100% - 24px);
        padding: 10px;
        border: 1px solid var(--aux-border);
        border-radius: 8px;
        color: var(--aux-text);
        background: var(--aux-control);
    }

    #asyncQuillEditor {
        flex: 1;
        min-height: 0;
        overflow: auto;
        background: var(--aux-editor-bg);
        color: var(--aux-editor-text);
    }

    #asyncNotesPanel .ql-toolbar.ql-snow {
        border-left: 0;
        border-right: 0;
        border-top: 0;
        border-color: var(--aux-border);
    }

    #asyncNotesPanel .ql-container.ql-snow {
        border: 0;
        font-size: 14px;
    }

    #asyncNotesPanel .ql-editor {
        color: var(--aux-editor-text);
    }

    #asyncNotesPanel .ql-editor.ql-blank::before {
        color: var(--aux-text);
        opacity: .55;
    }

    #asyncNotesPanel .ql-snow .ql-stroke {
        stroke: var(--aux-text);
    }

    #asyncNotesPanel .ql-snow .ql-fill {
        fill: var(--aux-text);
    }

    #asyncNotesPanel .ql-snow .ql-picker {
        color: var(--aux-text);
    }

    #asyncNotesPanel .notes-footer {
        padding: 8px 12px;
        border-top: 1px solid var(--aux-border);
        color: var(--aux-text);
        opacity: .72;
        font-size: 10px;
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    #asyncCalcDisplay {
        width: 100%;
        padding: 14px 11px;
        border: 1px solid var(--aux-border);
        border-radius: 10px;
        background: var(--aux-display-bg);
        color: var(--aux-text);
        font-size: 25px;
        font-weight: 700;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    #asyncCalcPanel .calc-mode {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        color: var(--aux-text);
        font-size: 11px;
    }

    #asyncCalcKeys {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 6px;
    }

    #asyncCalcKeys button {
        min-height: 39px;
        padding: 6px 2px;
        font-weight: 650;
        font-size: 12px;
    }

    #asyncCalcKeys button.calc-op {
        color: var(--aux-accent);
        background: var(--aux-op-bg);
    }

    #asyncCalcKeys button.calc-number {
        font-size: 14px;
    }

    #asyncCalcKeys button.calc-equals {
        background: var(--aux-accent);
        color: #fff;
        border-color: var(--aux-accent);
    }

    #asyncCalcKeys button.calc-clear {
        color: #dc5555;
    }

    #asyncCalcStatus {
        min-height: 14px;
        font-size: 10px;
        color: var(--aux-text);
    }

    #asyncCalcPanel .calc-memory {
        display: flex;
        gap: 6px;
    }

    #asyncCalcPanel .calc-memory button {
        flex: 1;
        min-height: 30px;
        padding: 4px;
        font-size: 10px;
    }

    #asyncCalcPanel .calc-memory button:hover,
    #asyncCalcKeys button:hover {
        filter: brightness(.96);
    }

    html[data-theme="dark"] #asyncNotesPanel,
    html[data-theme="dark"] #asyncCalcPanel {
        --aux-bg: rgba(25, 32, 45, .97);
        --aux-text: #edf2fa;
        --aux-border: rgba(148, 163, 184, .25);
        --aux-control: rgba(255, 255, 255, .07);
        --aux-editor-bg: #202938;
        --aux-editor-text: #edf2fa;
        --aux-display-bg: #111827;
        --aux-op-bg: rgba(65, 105, 225, .16);
    }

    @media(max-width:480px) {

        #asyncNotesFab,
        #asyncCalcFab {
            right: 14px;
            width: 43px;
            height: 43px;
        }

        #asyncNotesFab {
            bottom: 76px;
        }

        #asyncCalcFab {
            bottom: 128px;
        }

        #asyncNotesPanel,
        #asyncCalcPanel {
            right: 10px;
            width: calc(100vw - 20px);
        }

        #asyncNotesPanel {
            bottom: 128px;
            height: min(72vh, 600px);
        }

        #asyncCalcPanel {
            bottom: 128px;
        }

        #asyncCalcKeys {
            gap: 5px;
        }

        #asyncCalcKeys button {
            min-height: 36px;
        }
    }


    /* ASYNC unified centered study workspace */
    #readingToolsPanel,
    #asyncNotesPanel,
    #asyncCalcPanel,
    #asyncSoundPanel {
        display: none !important;
        position: fixed !important;
        left: 50%;
        top: 50%;
        right: auto;
        bottom: auto;
        transform: translate(-50%, -50%);
        width: min(540px, calc(100vw - 28px)) !important;
        max-height: min(82vh, 760px);
        border: 1px solid var(--aux-border, rgba(148, 163, 184, .28)) !important;
        border-radius: 19px !important;
        background: var(--aux-glass, rgba(255, 255, 255, .9)) !important;
        color: var(--aux-text, #172033) !important;
        box-shadow: 0 22px 65px rgba(15, 23, 42, .23), inset 0 1px 0 rgba(255, 255, 255, .32) !important;
        backdrop-filter: blur(22px) saturate(150%);
        -webkit-backdrop-filter: blur(22px) saturate(150%);
        overflow: auto;
    }

    #readingToolsPanel.is-open,
    #asyncNotesPanel.is-open,
    #asyncCalcPanel.is-open,
    #asyncSoundPanel.is-open {
        display: flex !important;
        flex-direction: column
    }

    #readingToolsPanel:not(.is-open),
    #asyncNotesPanel:not(.is-open),
    #asyncCalcPanel:not(.is-open),
    #asyncSoundPanel:not(.is-open) {
        visibility: hidden;
        pointer-events: none
    }

    #readingToolsPanel.is-open,
    #asyncNotesPanel.is-open,
    #asyncCalcPanel.is-open,
    #asyncSoundPanel.is-open {
        visibility: visible;
        pointer-events: auto
    }

    #readingToolsPanel {
        padding: 16px !important
    }

    #readingToolsPanel .rt-header {
        position: sticky;
        top: -16px;
        z-index: 2;
        margin: -16px -16px 13px !important;
        padding: 13px 16px !important;
        background: rgba(65, 105, 225, .94);
        color: #fff;
        border: 0 !important;
        border-radius: 18px 18px 0 0;
        cursor: grab;
        touch-action: none;
        user-select: none
    }

    #readingToolsPanel .rt-header strong,
    #readingToolsPanel .rt-close {
        color: #fff !important
    }

    #readingToolsPanel .rt-group {
        margin-bottom: 12px
    }

    #readingToolsPanel .rt-btn,
    #readingToolsPanel .rt-select {
        border-radius: 9px;
        background: var(--aux-control, rgba(15, 23, 42, .04));
        color: var(--aux-text, #172033);
        border-color: var(--aux-border)
    }

    #readingToolsPanel .rt-muted {
        color: var(--aux-text)
    }

    #asyncNotesPanel {
        height: min(590px, 76vh) !important;
        width: min(500px, calc(100vw - 28px)) !important
    }

    #asyncCalcPanel {
        width: min(430px, calc(100vw - 28px)) !important;
        max-height: 82vh;
        padding-bottom: 12px !important;
        gap: 7px !important
    }

    #asyncSoundPanel {
        width: min(390px, calc(100vw - 28px)) !important;
        padding: 0 14px 14px;
        gap: 12px
    }

    #asyncNotesPanel.is-minimized,
    #asyncCalcPanel.is-minimized,
    #asyncSoundPanel.is-minimized,
    #readingToolsPanel.is-minimized {
        width: min(300px, calc(100vw - 28px)) !important;
        height: auto !important;
        max-height: none !important;
        overflow: hidden !important;
    }

    #asyncNotesPanel.is-minimized>:not(.aux-head),
    #asyncCalcPanel.is-minimized>:not(.aux-head),
    #asyncSoundPanel.is-minimized>:not(.aux-head),
    #readingToolsPanel.is-minimized>:not(.rt-header) {
        display: none !important
    }

    #asyncNotesPanel.is-minimized .aux-head,
    #asyncCalcPanel.is-minimized .aux-head,
    #asyncSoundPanel.is-minimized .aux-head,
    #readingToolsPanel.is-minimized .rt-header {
        margin: 0 !important;
        border-radius: 16px !important
    }

    .aux-head,
    #readingToolsPanel .rt-header {
        cursor: grab;
        touch-action: none;
        user-select: none
    }

    .aux-head:active,
    #readingToolsPanel .rt-header:active {
        cursor: grabbing
    }

    .aux-window-actions,
    #readingToolsPanel .rt-window-actions {
        display: flex;
        align-items: center;
        gap: 3px
    }

    .aux-window-actions button,
    #readingToolsPanel .rt-window-actions button {
        width: 29px;
        height: 29px;
        padding: 0;
        border: 0;
        border-radius: 7px;
        background: rgba(255, 255, 255, .13);
        color: #fff;
        font-size: 17px;
        cursor: pointer
    }


    #asyncSoundFab {
        position: fixed;
        right: 22px;
        bottom: 194px;
        z-index: 99989;
        width: 46px;
        height: 46px;
        border: 1px solid var(--aux-launch-border);
        border-radius: 15px;
        background: var(--aux-sound-fab, rgba(217, 119, 6, .72));
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .13);
        backdrop-filter: blur(12px)
    }

    #asyncSoundPanel .sound-choice {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px
    }

    #asyncSoundPanel .sound-choice button {
        padding: 11px 8px;
        border: 1px solid var(--aux-border);
        border-radius: 10px;
        background: var(--aux-control);
        color: var(--aux-text);
        cursor: pointer;
        font-size: 12px
    }

    #asyncSoundPanel .sound-choice button.active {
        border-color: #d97706;
        background: rgba(217, 119, 6, .14)
    }

    #asyncSoundPanel .sound-volume {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--aux-text);
        font-size: 12px
    }

    #asyncSoundPanel input[type=range] {
        flex: 1;
        accent-color: #d97706
    }

    #asyncSoundPanel .sound-actions {
        display: flex;
        gap: 8px
    }

    #asyncSoundPanel .sound-actions button {
        flex: 1;
        padding: 10px;
        border: 0;
        border-radius: 9px;
        background: #d97706;
        color: #fff;
        cursor: pointer
    }

    #asyncSoundStatus {
        font-size: 11px;
        color: var(--aux-text)
    }

    #asyncCalcPanel #asyncCalcDisplay {
        font-size: clamp(19px, 4vw, 25px);
        min-width: 0
    }

    #asyncCalcMathResult {
        min-height: 38px;
        max-height: 100px;
        overflow: auto;
        padding: 8px 10px;
        border: 1px solid var(--aux-border);
        border-radius: 9px;
        background: var(--aux-display-bg);
        color: var(--aux-text);
        font-size: 14px;
        text-align: center
    }

    #asyncCalcSymbolic {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px
    }

    #asyncCalcSymbolic button {
        padding: 8px 5px;
        border: 1px solid var(--aux-border);
        border-radius: 8px;
        background: var(--aux-op-bg);
        color: var(--aux-text);
        font-size: 11px;
        cursor: pointer
    }

    #asyncCalcVariable {
        width: 48px;
        padding: 5px;
        border: 1px solid var(--aux-border);
        border-radius: 6px;
        background: var(--aux-control);
        color: var(--aux-text)
    }

    #asyncCalcPanel #asyncCalcKeys {
        gap: 5px
    }

    #asyncCalcPanel #asyncCalcKeys button {
        min-width: 0;
        min-height: clamp(31px, 5vh, 39px);
        font-size: clamp(10px, 1.5vw, 12px)
    }

    html[data-theme="dark"] #readingToolsPanel,
    html[data-theme="dark"] #asyncNotesPanel,
    html[data-theme="dark"] #asyncCalcPanel,
    html[data-theme="dark"] #asyncSoundPanel {
        --aux-glass: rgba(25, 32, 45, .93);
        --aux-control: rgba(255, 255, 255, .07)
    }

    #asyncFloatingTimer {
        left: 50%;
        top: 50%;
        right: auto;
        bottom: auto;
        transform: translate(-50%, -50%);
    }

    @media(max-width:480px) {

        #asyncNotesFab,
        #asyncCalcFab,
        #asyncSoundFab {
            right: 13px;
            width: 42px;
            height: 42px
        }

        #asyncNotesFab {
            bottom: 72px
        }

        #asyncCalcFab {
            bottom: 122px
        }

        #asyncSoundFab {
            bottom: 172px
        }

        #readingToolsPanel,
        #asyncNotesPanel,
        #asyncCalcPanel,
        #asyncSoundPanel {
            width: calc(100vw - 18px) !important;
            max-height: 84dvh;
            border-radius: 15px !important
        }

        #asyncNotesPanel {
            height: 76dvh !important
        }

        #asyncCalcPanel {
            padding-left: 9px;
            padding-right: 9px
        }

        #asyncCalcPanel #asyncCalcKeys {
            gap: 4px
        }

        #asyncCalcPanel #asyncCalcKeys button {
            min-height: 32px;
            padding: 3px 1px
        }

        #readingToolsPanel {
            padding: 12px !important
        }
    }


    /* Unified ASYNC tool appearance: one royal-blue glass style for every tool and launcher. */
    #readingToolsFab,
    #asyncNotesFab,
    #asyncCalcFab,
    #asyncSoundFab {
        position: fixed !important;
        right: 22px !important;
        width: 50px !important;
        height: 50px !important;
        border: 1px solid rgba(255, 255, 255, .35) !important;
        border-radius: 15px !important;
        background: linear-gradient(145deg, #5278ed, #3157c7) !important;
        color: #fff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 20px !important;
        box-shadow: 0 7px 20px rgba(37, 76, 180, .28) !important;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        opacity: 1 !important;
        transition: transform .18s ease, box-shadow .18s ease !important;
    }

    #readingToolsFab {
        bottom: 24px !important;
        z-index: 99990
    }

    #asyncNotesFab {
        bottom: 82px !important
    }

    #asyncCalcFab {
        bottom: 140px !important
    }

    #asyncSoundFab {
        bottom: 198px !important
    }

    #readingToolsFab:hover,
    #asyncNotesFab:hover,
    #asyncCalcFab:hover,
    #asyncSoundFab:hover,
    #readingToolsFab:focus-visible,
    #asyncNotesFab:focus-visible,
    #asyncCalcFab:focus-visible,
    #asyncSoundFab:focus-visible {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 76, 180, .36) !important;
        outline: none
    }

    #readingToolsPanel .rt-header,
    .aux-head,
    #asyncNotesPanel .aux-head,
    #asyncCalcPanel .aux-head,
    #asyncSoundPanel .aux-head {
        background: linear-gradient(110deg, #4169e1, #3157c7) !important;
        color: #fff !important
    }

    #asyncNotesPanel .aux-head strong,
    #asyncCalcPanel .aux-head strong,
    #asyncSoundPanel .aux-head strong,
    #readingToolsPanel .rt-header strong {
        color: #fff !important
    }

    #asyncNotesPanel .aux-window-actions button,
    #asyncCalcPanel .aux-window-actions button,
    #asyncSoundPanel .aux-window-actions button,
    #readingToolsPanel .rt-window-actions button {
        background: rgba(255, 255, 255, .16) !important;
        color: #fff !important
    }

    #asyncNotesPanel .aux-head,
    #asyncCalcPanel .aux-head,
    #asyncSoundPanel .aux-head {
        border-radius: 16px 16px 0 0
    }

    #asyncCalcExpressionPreview,
    #asyncCalcLatexPreview {
        min-height: 42px;
        max-height: 110px;
        overflow: auto;
        padding: 9px 11px;
        border: 1px solid var(--aux-border);
        border-radius: 9px;
        background: var(--aux-display-bg);
        color: var(--aux-text);
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-wrap: anywhere
    }

    #asyncCalcExpressionPreview {
        font-size: 17px
    }

    .calc-formula-editor {
        display: grid;
        gap: 6px;
        color: var(--aux-text);
        font-size: 11px
    }

    .calc-formula-editor label {
        font-weight: 700
    }

    #asyncCalcLatexInput {
        width: 100%;
        min-height: 48px;
        resize: vertical;
        padding: 8px 10px;
        border: 1px solid var(--aux-border);
        border-radius: 8px;
        background: var(--aux-control);
        color: var(--aux-text);
        font: 12px/1.45 ui-monospace, SFMono-Regular, Consolas, monospace
    }

    #asyncCalcLatexPreview {
        min-height: 52px;
        font-size: 17px
    }

    #asyncCalcLatexPreview .katex-display,
    #asyncCalcExpressionPreview .katex-display {
        margin: 0
    }

    #asyncCalcMathResult {
        max-height: 150px !important;
        min-height: 48px !important
    }

    #asyncSoundPanel .sound-actions button {
        background: #4169e1 !important
    }

    #asyncSoundPanel .sound-choice button.active {
        border-color: #4169e1 !important;
        background: rgba(65, 105, 225, .14) !important
    }

    #asyncSoundPanel input[type=range] {
        accent-color: #4169e1 !important
    }

    #asyncNotesPanel .notes-toolbar button,
    #asyncCalcPanel #asyncCalcSymbolic button,
    #asyncCalcPanel .calc-memory button,
    #asyncCalcPanel #asyncCalcKeys button,
    #readingToolsPanel .rt-btn {
        border-radius: 9px
    }

    #asyncCalcPanel #asyncCalcKeys button.calc-equals,
    #asyncCalcPanel #asyncCalcSymbolic button:hover,
    #asyncNotesPanel .notes-toolbar button:hover,
    #readingToolsPanel .rt-btn:hover {
        background: #4169e1;
        color: #fff;
        border-color: #4169e1
    }

    @media(max-width:480px) {

        #readingToolsFab,
        #asyncNotesFab,
        #asyncCalcFab,
        #asyncSoundFab {
            right: 13px !important;
            width: 44px !important;
            height: 44px !important
        }

        #readingToolsFab {
            bottom: 18px !important
        }

        #asyncNotesFab {
            bottom: 70px !important
        }

        #asyncCalcFab {
            bottom: 122px !important
        }

        #asyncSoundFab {
            bottom: 174px !important
        }
    }


    /* Board-exam scientific calculator controls */
    #asyncCalcPanel {
        width: min(470px, calc(100vw - 24px)) !important;
        max-height: min(90vh, 820px) !important;
        overflow-y: auto !important
    }

    #asyncCalcPanel .exam-calc-topbar {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr)) auto;
        gap: 5px;
        align-items: center
    }

    #asyncCalcPanel .exam-calc-topbar button,
    #asyncCalcPanel .exam-calc-fraction-row button,
    #asyncCalcPanel .exam-calc-bounds button {
        min-height: 32px;
        font-weight: 800;
        font-size: 10px;
        letter-spacing: .2px
    }

    #asyncCalcPanel .exam-calc-topbar button[aria-pressed="true"] {
        background: #4169e1 !important;
        color: #fff !important;
        border-color: #4169e1 !important
    }

    #asyncCalcModeLabel {
        font-size: 10px;
        font-weight: 900;
        color: var(--aux-accent);
        text-align: right
    }

    #asyncCalcModeMenu {
        display: grid;
        grid-template-columns: 1fr;
        gap: 5px;
        padding: 8px;
        border: 1px solid var(--aux-border);
        border-radius: 9px;
        background: var(--aux-control)
    }

    #asyncCalcModeMenu[hidden] {
        display: none
    }

    #asyncCalcModeMenu button {
        text-align: left;
        min-height: 34px
    }

    #asyncCalcDisplay {
        font-family: ui-monospace, Consolas, monospace !important;
        font-size: clamp(25px, 5vw, 34px) !important;
        min-height: 64px !important
    }

    #asyncCalcPanel .exam-calc-fraction-row {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 5px
    }

    #asyncCalcPanel .exam-calc-bounds {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 6px;
        align-items: end;
        color: var(--aux-text);
        font-size: 10px
    }

    #asyncCalcPanel .exam-calc-bounds label {
        display: grid;
        gap: 4px;
        min-width: 0
    }

    #asyncCalcPanel .exam-calc-bounds input {
        width: 100%;
        min-width: 0;
        padding: 7px;
        border: 1px solid var(--aux-border);
        border-radius: 7px;
        background: var(--aux-control);
        color: var(--aux-text)
    }

    #asyncCalcPanel #asyncCalcSymbolic {
        grid-template-columns: repeat(3, minmax(0, 1fr))
    }

    #asyncCalcPanel .calc-mode {
        flex-wrap: wrap
    }

    #asyncCalcPanel #asyncCalcKeys {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 6px
    }

    #asyncCalcPanel #asyncCalcKeys button {
        min-height: 42px;
        font-size: 12px
    }

    #asyncCalcPanel .calc-memory button {
        min-height: 34px;
        font-weight: 800
    }

    @media(max-width:480px) {
        #asyncCalcPanel {
            max-height: 88dvh !important
        }

        #asyncCalcPanel #asyncCalcKeys button {
            min-height: 37px
        }

        #asyncCalcPanel .exam-calc-bounds {
            grid-template-columns: 1fr 1fr
        }

        #asyncCalcPanel .exam-calc-bounds button {
            grid-column: 1/-1
        }
    }


    #readingToolsFab,
    #asyncNotesFab,
    #asyncCalcFab,
    #asyncSoundFab {
        display: none !important
    }

    #asyncToolsDock {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 100100;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px
    }

    #asyncToolsFab {
        width: 56px;
        height: 56px;
        border: 1px solid rgba(255, 255, 255, .4);
        border-radius: 18px;
        background: linear-gradient(145deg, #5278ed, #3157c7);
        color: #fff;
        font-size: 22px;
        box-shadow: 0 8px 24px rgba(37, 76, 180, .35);
        cursor: pointer;
        transition: transform .2s
    }

    #asyncToolsFab[aria-expanded=true] {
        transform: rotate(90deg)
    }

    #asyncToolsMenu {
        display: flex;
        flex-direction: column;
        gap: 7px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(12px) scale(.96);
        transform-origin: bottom right;
        transition: .2s
    }

    #asyncToolsMenu.is-visible {
        opacity: 1;
        visibility: visible;
        transform: none
    }

    #asyncToolsMenu button {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 175px;
        padding: 11px 14px;
        border: 1px solid rgba(255, 255, 255, .35);
        border-radius: 12px;
        background: linear-gradient(110deg, #4169e1, #3157c7);
        color: #fff;
        box-shadow: 0 5px 16px #0f172a2e;
        font-size: 13px;
        text-align: left;
        cursor: pointer
    }

    #asyncToolsMenu button i {
        width: 20px;
        text-align: center;
        font-size: 17px
    }

    #readingToolsPanel,
    #asyncNotesPanel,
    #asyncCalcPanel,
    #asyncSoundPanel {
        left: 50% !important;
        top: 50% !important;
        right: auto !important;
        bottom: auto !important;
        transform: translate(-50%, -50%) !important;
        max-width: calc(100vw - 18px) !important;
        max-height: calc(100dvh - 18px) !important
    }

    #readingToolsPanel.is-open,
    #asyncNotesPanel.is-open,
    #asyncCalcPanel.is-open,
    #asyncSoundPanel.is-open {
        animation: asyncToolPop .2s ease-out
    }

    @keyframes asyncToolPop {
        from {
            opacity: .6;
            scale: .96
        }

        to {
            opacity: 1;
            scale: 1
        }
    }

    #readingToolsPanel[data-user-positioned="1"],
    #asyncNotesPanel[data-user-positioned="1"],
    #asyncCalcPanel[data-user-positioned="1"],
    #asyncSoundPanel[data-user-positioned="1"] {
        transform: none !important
    }

    #asyncCalcPanel .simple-calc-mode {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 10px;
        font-weight: 800;
        color: var(--aux-text)
    }

    #asyncCalcPanel .simple-calc-help {
        font-size: 10px;
        color: var(--aux-text)
    }

    #asyncCalcPanel #asyncCalcDisplay {
        font: clamp(24px, 5vw, 34px) ui-monospace, Consolas, monospace !important;
        min-height: 62px !important
    }

    #asyncCalcPanel #asyncCalcKeys {
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 6px
    }

    #asyncCalcPanel #asyncCalcKeys button {
        min-height: 41px;
        font-size: 13px
    }

    .supplemental-heading {
        font-size: 12px;
        font-weight: 800;
        color: var(--aux-text)
    }

    .supplemental-heading small {
        display: block;
        font-size: 10px;
        font-weight: 400;
        opacity: .7;
        margin-top: 3px
    }

    #asyncSupplementalSounds {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
        max-height: 155px;
        overflow: auto
    }

    #asyncSupplementalSounds button {
        padding: 8px 4px;
        border: 1px solid var(--aux-border);
        border-radius: 8px;
        background: var(--aux-control);
        color: var(--aux-text);
        font-size: 10px;
        cursor: pointer
    }

    #asyncSupplementalSounds button.active {
        background: rgba(65, 105, 225, .2);
        border-color: #4169e1;
        font-weight: 800
    }

    @media(max-width:520px) {
        #asyncToolsDock {
            right: 12px;
            bottom: 12px
        }

        #asyncToolsFab {
            width: 50px;
            height: 50px
        }

        #asyncToolsMenu button {
            min-width: 158px;
            padding: 10px 12px
        }

        #readingToolsPanel,
        #asyncNotesPanel,
        #asyncCalcPanel,
        #asyncSoundPanel {
            width: calc(100vw - 14px) !important;
            max-height: calc(100dvh - 14px) !important
        }

        #asyncCalcPanel #asyncCalcKeys button {
            min-height: 37px
        }

        #asyncSupplementalSounds {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            max-height: 130px
        }
    }
</style>

<div id="readingToolsRoot">
    <div id="asyncToolsDock">
        <div id="asyncToolsMenu" aria-hidden="true">
            <button type="button" data-open-tool="readingToolsPanel"><i class="bi bi-book-half"></i><span>Reading tools</span></button>
            <button type="button" data-open-tool="asyncNotesPanel"><i class="bi bi-journal-text"></i><span>Study notes</span></button>
            <button type="button" data-open-tool="asyncCalcPanel"><i class="bi bi-calculator"></i><span>Calculator</span></button>
            <button type="button" data-open-tool="asyncSoundPanel"><i class="bi bi-headphones"></i><span>Ambient sounds</span></button>
            <button type="button" id="asyncDockTimer"><i class="bi bi-stopwatch"></i><span>Timer</span></button>
        </div>
        <button type="button" id="asyncToolsFab" aria-label="Show study tools" aria-expanded="false"><i class="bi bi-grid-3x3-gap-fill"></i></button>
    </div>
    <section id="readingToolsPanel" aria-label="Reading tools">
        <div class="rt-header" id="rtDrag">
            <strong><i class="bi bi-sliders"></i> Reading Tools</strong>
            <span class="rt-window-actions"><button type="button" id="rtMinimize" title="Minimize">−</button><button type="button" class="rt-close" id="rtClose" aria-label="Close reading tools">&times;</button></span>
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


<section id="asyncNotesPanel" aria-label="Personal notes">
    <div class="aux-head" id="asyncNotesDrag"><strong><i class="bi bi-grip-horizontal"></i> My Study Notes</strong><span class="aux-drag-hint">Drag to move</span><span class="aux-window-actions"><button type="button" id="asyncNotesMinimize" title="Minimize">−</button><button type="button" id="asyncNotesClose" aria-label="Close notes">&times;</button></span></div>
    <div class="notes-toolbar">
        <select id="asyncNotesList" aria-label="Saved notes"></select>
        <button type="button" id="asyncNoteNew"><i class="bi bi-plus-lg"></i> New</button>
        <button type="button" id="asyncNoteDelete" title="Delete note"><i class="bi bi-trash"></i></button>
    </div>
    <input id="asyncNoteTitle" type="text" maxlength="120" placeholder="Note title">
    <div id="asyncQuillEditor"></div>
    <div class="notes-footer"><span id="asyncNotesSaveStatus">Saved in this browser</span><span>Private to this device/browser</span></div>
</section>

<section id="asyncCalcPanel" aria-label="Scientific calculator">
    <div class="aux-head" id="asyncCalcDrag" style="margin:0 -13px 2px"><strong><i class="bi bi-grip-horizontal"></i> Scientific Calculator</strong><span class="aux-drag-hint">Drag to move</span><span class="aux-window-actions"><button type="button" id="asyncCalcMinimize" title="Minimize">−</button><button type="button" id="asyncCalcClose" aria-label="Close calculator">&times;</button></span></div>
    <div class="simple-calc-mode"><span>SCIENTIFIC</span><select id="asyncCalcAngle">
            <option value="deg">DEG</option>
            <option value="rad">RAD</option>
        </select></div>
    <input id="asyncCalcDisplay" type="text" value="0" aria-label="Calculator display" autocomplete="off" spellcheck="false" inputmode="decimal">
    <small class="simple-calc-help">Fractions: use a/b or (a+b)/(c+d). Powers: use ^, e.g. 2^3.</small>
    <div class="calc-memory"><button data-action="mc">MC</button><button data-action="mr">MR</button><button data-action="mplus">M+</button><button data-action="mminus">M−</button><button data-action="ans">Ans</button></div>
    <div id="asyncCalcKeys">
        <button class="calc-op" data-insert="sin(">sin</button><button class="calc-op" data-insert="cos(">cos</button><button class="calc-op" data-insert="tan(">tan</button><button class="calc-op" data-insert="sqrt(">√</button><button class="calc-op" data-insert="^">xʸ</button>
        <button class="calc-op" data-insert="asin(">sin⁻¹</button><button class="calc-op" data-insert="acos(">cos⁻¹</button><button class="calc-op" data-insert="atan(">tan⁻¹</button><button class="calc-op" data-insert="log(">log</button><button class="calc-op" data-insert="ln(">ln</button>
        <button class="calc-op" data-insert="(">(</button><button class="calc-op" data-insert=")">)</button><button class="calc-op" data-insert="/">a/b</button><button class="calc-op" data-insert="^2">x²</button><button class="calc-clear" data-action="back">⌫</button>
        <button class="calc-number" data-insert="7">7</button><button class="calc-number" data-insert="8">8</button><button class="calc-number" data-insert="9">9</button><button class="calc-op" data-insert="*">×</button><button class="calc-clear" data-action="clear">AC</button>
        <button class="calc-number" data-insert="4">4</button><button class="calc-number" data-insert="5">5</button><button class="calc-number" data-insert="6">6</button><button class="calc-op" data-insert="-">−</button><button class="calc-op" data-insert="%">%</button>
        <button class="calc-number" data-insert="1">1</button><button class="calc-number" data-insert="2">2</button><button class="calc-number" data-insert="3">3</button><button class="calc-op" data-insert="+">+</button><button class="calc-equals" data-action="equals">=</button>
        <button class="calc-number" data-insert="0">0</button><button class="calc-number" data-insert=".">.</button><button class="calc-op" data-insert="pi">π</button><button class="calc-op" data-insert="e">e</button><button class="calc-op" data-insert="!">n!</button>
    </div>
    <div id="asyncCalcStatus">Use sin(30), cos(60), or tan(45). Results follow DEG/RAD mode.</div>
</section>

<section id="asyncSoundPanel" aria-label="Ambient sound player">
    <div class="aux-head" id="asyncSoundDrag"><strong><i class="bi bi-headphones"></i> Reading Ambience</strong><span class="aux-drag-hint">Drag to move</span><span class="aux-window-actions"><button type="button" id="asyncSoundMinimize" title="Minimize">−</button><button type="button" id="asyncSoundClose" aria-label="Close sounds">&times;</button></span></div>
    <div style="font-size:12px;color:var(--aux-text);opacity:.75">Choose a continuous background sound while you read.</div>
    <div class="sound-choice"><button type="button" data-sound="rain">🌧️ Rain</button><button type="button" data-sound="ocean">🌊 Ocean waves</button><button type="button" data-sound="forest">🌲 Forest ambience</button><button type="button" data-sound="wind">🍃 Gentle wind</button><button type="button" data-sound="fire">🔥 Fireplace</button></div>
    <div class="supplemental-heading">Supplemental ASMR layers <small>Mix any of these with the main ambience.</small></div>
    <div id="asyncSupplementalSounds" class="supplemental-sounds">
        <button data-layer="thunder">Thunder</button><button data-layer="white">White noise</button><button data-layer="pink">Pink noise</button><button data-layer="brown">Brown noise</button>
        <button data-layer="fan">Fan</button><button data-layer="aircon">Air conditioner</button><button data-layer="rainroof">Rain on roof</button><button data-layer="rainwindow">Rain on window</button>
        <button data-layer="firecrackle">Fire crackle</button><button data-layer="water">Running water</button><button data-layer="stream">Stream</button><button data-layer="night">Night insects</button>
        <button data-layer="birds">Birdsong</button><button data-layer="heartbeat">Heartbeat</button><button data-layer="vinyl">Vinyl static</button><button data-layer="cafe">Cafe murmur</button>
        <button data-layer="train">Train rumble</button><button data-layer="typing">Soft typing</button>
    </div>
    <label class="sound-volume"><span>Layer volume</span><input id="asyncLayerVolume" type="range" min="0" max="100" value="35"><span id="asyncLayerVolumeValue">35%</span></label>
    <label class="sound-volume"><span>Volume</span><input id="asyncSoundVolume" type="range" min="0" max="100" value="28"><span id="asyncSoundVolumeValue">28%</span></label>
    <div class="sound-actions"><button type="button" id="asyncSoundPlay"><i class="bi bi-play-fill"></i> Play sound</button><button type="button" id="asyncSoundStop" style="background:#64748b">Stop</button></div>
    <div id="asyncSoundStatus" aria-live="polite">Select a sound, then press Play. Audio starts only after your action.</div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/12.4.2/math.min.js"></script>
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
            if (window.asyncCloseOtherTools) window.asyncCloseOtherTools(timerBox);
            timerBox.classList.add('is-visible');
            timerBox.style.left = '50%';
            timerBox.style.top = '50%';
            timerBox.style.right = 'auto';
            timerBox.style.bottom = 'auto';
            timerBox.style.transform = 'translate(-50%,-50%)';
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
            timerBox.style.transform = 'none';
            timerBox.dataset.userPositioned = '1';
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

        function syncAuxiliaryTheme() {
            const source = post || document.body;
            const computed = getComputedStyle(source);
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';
            const bg = prefs.nativeTheme ? computed.backgroundColor : prefs.page;
            const fg = prefs.nativeTheme ? computed.color : prefs.text;
            const usableBg = bg && bg !== 'rgba(0, 0, 0, 0)' ? bg : (dark ? '#19202d' : '#ffffff');
            const usableFg = fg && fg !== 'rgba(0, 0, 0, 0)' ? fg : (dark ? '#edf2fa' : '#172033');
            let channels = usableBg.match(/\d+(?:\.\d+)?/g);
            if (usableBg.startsWith('#')) {
                let hex = usableBg.slice(1);
                if (hex.length === 3) hex = hex.split('').map(ch => ch + ch).join('');
                if (hex.length === 6) channels = [0, 2, 4].map(i => parseInt(hex.slice(i, i + 2), 16));
            }
            const backgroundIsDark = dark || (channels && channels.length >= 3 &&
                (0.2126 * Number(channels[0]) + 0.7152 * Number(channels[1]) + 0.0722 * Number(channels[2])) < 125);
            const rootStyle = document.documentElement.style;
            rootStyle.setProperty('--aux-bg', usableBg);
            rootStyle.setProperty('--aux-text', usableFg);
            rootStyle.setProperty('--aux-border', dark ? 'rgba(148,163,184,.25)' : 'rgba(100,116,139,.25)');
            rootStyle.setProperty('--aux-control', dark ? 'rgba(255,255,255,.07)' : 'rgba(15,23,42,.035)');
            rootStyle.setProperty('--aux-editor-bg', usableBg);
            rootStyle.setProperty('--aux-editor-text', usableFg);
            rootStyle.setProperty('--aux-display-bg', dark ? '#111827' : '#f1f5f9');
            rootStyle.setProperty('--aux-op-bg', dark ? 'rgba(65,105,225,.16)' : '#eff6ff');
            rootStyle.setProperty('--aux-accent', '#4169e1');
            rootStyle.setProperty('--aux-launch-border', backgroundIsDark ? 'rgba(255,255,255,.22)' : 'rgba(255,255,255,.58)');
            rootStyle.setProperty('--aux-notes-fab', backgroundIsDark ? 'rgba(13,148,136,.88)' : 'rgba(13,148,136,.68)');
            rootStyle.setProperty('--aux-calc-fab', backgroundIsDark ? 'rgba(65,105,225,.9)' : 'rgba(65,105,225,.68)');
            rootStyle.setProperty('--aux-sound-fab', backgroundIsDark ? 'rgba(217,119,6,.9)' : 'rgba(217,119,6,.68)');
            rootStyle.setProperty('--aux-glass', backgroundIsDark ? 'rgba(25,32,45,.93)' : 'rgba(255,255,255,.91)');
            rootStyle.setProperty('--rt-bg', backgroundIsDark ? 'rgba(25,32,45,.93)' : 'rgba(255,255,255,.91)');
            rootStyle.setProperty('--rt-text', usableFg);
            rootStyle.setProperty('--rt-border', dark ? 'rgba(148,163,184,.25)' : 'rgba(100,116,139,.25)');
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
                syncAuxiliaryTheme();
                return;
            }

            // Override the post's existing theme variables without changing global UI.
            // Text scaling is independent: only change colors after the user chooses them.
            if (prefs.pageCustomized) {
                post.style.setProperty('--theory-bg', prefs.page);
                post.style.setProperty('--theory-hover', prefs.page);
                post.style.setProperty('--theory-header', prefs.page);
                post.style.setProperty('background-color', prefs.page, 'important');
                post.querySelectorAll('.theory-card,.reference-note,.theory-header,.theory-body,.example-description,.code-box,.visualizer-controls,.chart-panel,.concept-box,.definition-box,.result-box,.application-item,.reference-note-title,.comparison-table th,.comparison-table td,.key-point-grid>div').forEach(el => {
                    el.style.setProperty('background-color', prefs.page, 'important');
                });
            }
            if (prefs.textCustomized) {
                post.style.setProperty('--theory-text', prefs.text);
                post.style.setProperty('--theory-secondary', prefs.text);
                post.style.setProperty('--theory-muted', prefs.text);
                post.style.setProperty('--theory-blue', prefs.text);
                post.style.setProperty('--theory-green', prefs.text);
                post.style.setProperty('--theory-red', prefs.text);
                post.style.setProperty('color', prefs.text, 'important');
            }

            textElements.forEach(el => {
                const base = originalSizes.get(el) || 14;
                el.style.setProperty('font-size', (base * prefs.scale) + 'px', 'important');
                if (prefs.textCustomized) el.style.setProperty('color', prefs.text, 'important');
                if (prefs.font) el.style.setProperty('font-family', prefs.font, 'important');
                else el.style.removeProperty('font-family');
            });
            if (prefs.textCustomized) {
                post.querySelectorAll('.theory-card,.reference-note,.theory-header,.theory-body,.example-description,.code-box,.visualizer-controls,.chart-panel,.concept-box,.definition-box,.result-box,.application-item,.reference-note-title,.comparison-table th,.comparison-table td,.key-point-grid>div').forEach(el => {
                    el.style.setProperty('color', prefs.text, 'important');
                });
            }

            document.getElementById('rtTextSizeValue').textContent = Math.round(prefs.scale * 100) + '%';
            fontSelect.value = prefs.font;
            speedInput.value = prefs.speed;
            setColorSelection('#rtPageColors', prefs.page);
            setColorSelection('#rtTextColors', prefs.text);
            syncTimerTypography();
            syncAuxiliaryTheme();
        }

        function togglePanel(open) {
            if (open && window.asyncCloseOtherTools) window.asyncCloseOtherTools(panel);
            panel.classList.toggle('is-open', open);
            panel.classList.remove('is-minimized');
            if (open && !panel.dataset.userPositioned) {
                panel.style.left = '50%';
                panel.style.top = '50%';
                panel.style.right = 'auto';
                panel.style.bottom = 'auto';
                panel.style.transform = 'translate(-50%,-50%)';
            }
            if (fab) fab.setAttribute('aria-expanded', String(open));
        }

        if (fab) fab.addEventListener('click', () => {
            if (panel.classList.contains('is-minimized') && panel.classList.contains('is-open')) {
                panel.classList.remove('is-minimized');
                return;
            }
            togglePanel(!panel.classList.contains('is-open'));
        });
        document.getElementById('rtClose').addEventListener('click', () => {
            togglePanel(false);
            panel.classList.remove('is-minimized');
        });
        document.getElementById('rtMinimize').addEventListener('click', () => {
            const minimized = panel.classList.toggle('is-minimized');
            document.getElementById('rtMinimize').textContent = minimized ? '□' : '−';
        });
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
            prefs.pageCustomized = true;
            save();
            apply();
        }));
        document.querySelectorAll('#rtTextColors .rt-color').forEach(btn => btn.addEventListener('click', () => {
            prefs.nativeTheme = false;
            prefs.text = btn.dataset.color;
            prefs.textCustomized = true;
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

<script>
    (() => {
        'use strict';
        const byId = id => document.getElementById(id);
        const notesPanel = byId('asyncNotesPanel'),
            calcPanel = byId('asyncCalcPanel');
        const toolsFab = byId('asyncToolsFab'),
            toolsMenu = byId('asyncToolsMenu');
        const notesKey = 'asyncAcademicPersonalNotes_v1';
        let notes = [],
            activeNoteId = null,
            quill = null,
            saveTimer = null;

        const soundPanel = byId('asyncSoundPanel');
        const timerBox = byId('asyncFloatingTimer');
        const readingPanel = byId('readingToolsPanel');
        const allToolPanels = [readingPanel, notesPanel, calcPanel, soundPanel].filter(Boolean);

        function setPanelClosed(item) {
            if (!item) return;
            item.classList.remove('is-open', 'is-minimized');
            const minimizeId = {
                readingToolsPanel: 'rtMinimize',
                asyncNotesPanel: 'asyncNotesMinimize',
                asyncCalcPanel: 'asyncCalcMinimize',
                asyncSoundPanel: 'asyncSoundMinimize'
            } [item.id];
            const minimizeButton = minimizeId && byId(minimizeId);
            if (minimizeButton) {
                minimizeButton.textContent = '−';
                minimizeButton.title = 'Minimize';
            }
        }

        function closeOtherTools(keep) {
            allToolPanels.forEach(item => {
                if (item !== keep) setPanelClosed(item);
            });
            if (timerBox && keep !== timerBox) {
                timerBox.classList.remove('is-visible', 'is-minimized');
                timerBox.setAttribute('aria-hidden', 'true');
            }
        }
        window.asyncCloseOtherTools = closeOtherTools;
        // Force a clean closed state on every page load; no tool is restored as open.
        allToolPanels.forEach(setPanelClosed);
        if (soundPanel) soundPanel.classList.remove('is-open', 'is-minimized');

        function openPanel(target) {
            if (!target) return;
            if (target.classList.contains('is-open') && target.classList.contains('is-minimized')) {
                target.classList.remove('is-minimized');
                return;
            }
            const shouldOpen = !target.classList.contains('is-open');
            if (shouldOpen) closeOtherTools(target);
            target.classList.toggle('is-open', shouldOpen);
            target.classList.remove('is-minimized');
            const minimizeId = {
                readingToolsPanel: 'rtMinimize',
                asyncNotesPanel: 'asyncNotesMinimize',
                asyncCalcPanel: 'asyncCalcMinimize',
                asyncSoundPanel: 'asyncSoundMinimize'
            } [target.id];
            const minimizeButton = minimizeId && byId(minimizeId);
            if (minimizeButton) {
                minimizeButton.textContent = '−';
                minimizeButton.title = 'Minimize';
            }
            if (shouldOpen) {
                target.dataset.userPositioned = '';
                target.style.setProperty('left', '50%', 'important');
                target.style.setProperty('top', '50%', 'important');
                target.style.setProperty('right', 'auto', 'important');
                target.style.setProperty('bottom', 'auto', 'important');
                target.style.setProperty('transform', 'translate(-50%,-50%)', 'important');
            }
            if (shouldOpen && !target.dataset.userPositioned) {
                target.style.left = '50%';
                target.style.top = '50%';
                target.style.right = 'auto';
                target.style.bottom = 'auto';
                target.style.transform = 'translate(-50%,-50%)';
            }
        }
        toolsFab.addEventListener('click', () => {
            const open = toolsFab.getAttribute('aria-expanded') === 'true';
            toolsFab.setAttribute('aria-expanded', String(!open));
            toolsMenu.classList.toggle('is-visible', !open);
            toolsMenu.setAttribute('aria-hidden', String(open));
            toolsFab.innerHTML = open ? '<i class="bi bi-grid-3x3-gap-fill"></i>' : '<i class="bi bi-x-lg"></i>';
        });
        toolsMenu.addEventListener('click', e => {
            const item = e.target.closest('[data-open-tool]');
            if (item) {
                const id = item.dataset.openTool;
                openPanel(byId(id));
                toolsFab.setAttribute('aria-expanded', 'false');
                toolsMenu.classList.remove('is-visible');
                toolsFab.innerHTML = '<i class="bi bi-grid-3x3-gap-fill"></i>';
                if (id === 'asyncNotesPanel') {
                    if (!quill && window.Quill) initializeNotes();
                    else if (!window.Quill) byId('asyncNotesSaveStatus').textContent = 'Quill could not load. Check your connection.';
                }
            }
        });
        byId('asyncDockTimer').addEventListener('click', () => {
            closeOtherTools(null);
            byId('asyncFloatingTimer').classList.add('is-visible');
            byId('asyncFloatingTimer').setAttribute('aria-hidden', 'false');
            toolsFab.setAttribute('aria-expanded', 'false');
            toolsMenu.classList.remove('is-visible');
            toolsFab.innerHTML = '<i class="bi bi-grid-3x3-gap-fill"></i>';
        });
        byId('asyncNotesClose').addEventListener('click', () => setPanelClosed(notesPanel));
        byId('asyncCalcClose').addEventListener('click', () => setPanelClosed(calcPanel));
        byId('asyncSoundClose').addEventListener('click', () => setPanelClosed(soundPanel));

        function setMinimize(panel, button) {
            button.addEventListener('click', () => {
                const minimized = panel.classList.toggle('is-minimized');
                button.textContent = minimized ? '□' : '−';
                button.title = minimized ? 'Restore' : 'Minimize';
            });
        }
        setMinimize(notesPanel, byId('asyncNotesMinimize'));
        setMinimize(calcPanel, byId('asyncCalcMinimize'));
        setMinimize(soundPanel, byId('asyncSoundMinimize'));

        function readNotes() {
            try {
                const value = JSON.parse(localStorage.getItem(notesKey) || '[]');
                return Array.isArray(value) ? value : [];
            } catch (_) {
                return [];
            }
        }

        function persistNotes() {
            try {
                localStorage.setItem(notesKey, JSON.stringify(notes));
                byId('asyncNotesSaveStatus').textContent = 'All changes saved';
            } catch (_) {
                byId('asyncNotesSaveStatus').textContent = 'Storage is full or unavailable; copy your note to keep it.';
            }
        }

        function refreshNoteList() {
            const select = byId('asyncNotesList');
            select.innerHTML = '';
            notes.forEach(note => {
                const option = document.createElement('option');
                option.value = note.id;
                option.textContent = note.title || 'Untitled note';
                select.appendChild(option);
            });
            if (activeNoteId) select.value = activeNoteId;
        }

        function saveActiveNote() {
            if (!quill || !activeNoteId) return;
            const note = notes.find(item => item.id === activeNoteId);
            if (!note) return;
            note.title = byId('asyncNoteTitle').value.trim() || 'Untitled note';
            note.html = quill.root.innerHTML;
            note.delta = quill.getContents();
            note.updatedAt = Date.now();
            persistNotes();
            refreshNoteList();
        }

        function selectNote(id) {
            activeNoteId = id;
            const note = notes.find(item => item.id === id);
            if (!note) return;
            byId('asyncNoteTitle').value = note.title === 'Untitled note' ? '' : note.title;
            if (note.delta) quill.setContents(note.delta);
            else quill.root.innerHTML = note.html || '';
            refreshNoteList();
        }

        function createNote() {
            const note = {
                id: (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : String(Date.now()) + Math.random(),
                title: 'Untitled note',
                html: '',
                delta: null,
                updatedAt: Date.now()
            };
            notes.unshift(note);
            activeNoteId = note.id;
            persistNotes();
            refreshNoteList();
            byId('asyncNoteTitle').value = '';
            quill.setText('');
            byId('asyncNoteTitle').focus();
        }

        function initializeNotes() {
            quill = new Quill('#asyncQuillEditor', {
                theme: 'snow',
                placeholder: 'Write your class notes here…',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            header: [1, 2, 3, false]
                        }],
                        [{
                            list: 'ordered'
                        }, {
                            list: 'bullet'
                        }],
                        ['blockquote', 'code-block'],
                        [{
                            color: []
                        }, {
                            background: []
                        }],
                        ['link'],
                        ['clean']
                    ]
                }
            });
            notes = readNotes();
            if (!notes.length) createNote();
            else {
                notes.sort((a, b) => (b.updatedAt || 0) - (a.updatedAt || 0));
                selectNote(notes[0].id);
            }
            quill.on('text-change', () => {
                byId('asyncNotesSaveStatus').textContent = 'Saving…';
                clearTimeout(saveTimer);
                saveTimer = setTimeout(saveActiveNote, 350);
            });
            byId('asyncNoteTitle').addEventListener('input', () => {
                clearTimeout(saveTimer);
                saveTimer = setTimeout(saveActiveNote, 350);
            });
            byId('asyncNotesList').addEventListener('change', e => {
                const selectedId = e.target.value;
                saveActiveNote();
                selectNote(selectedId);
            });
            byId('asyncNoteNew').addEventListener('click', () => {
                saveActiveNote();
                createNote();
            });
            byId('asyncNoteDelete').addEventListener('click', () => {
                if (!activeNoteId || !confirm('Delete this saved note? This cannot be undone.')) return;
                notes = notes.filter(note => note.id !== activeNoteId);
                activeNoteId = null;
                if (!notes.length) createNote();
                else selectNote(notes[0].id);
                persistNotes();
            });
            window.addEventListener('beforeunload', saveActiveNote);
        }


        // Drag either floating workspace by its header (mouse, pen, or touch).
        function makeDraggable(panel, handle) {
            let drag = null;
            handle.addEventListener('pointerdown', event => {
                if (event.target.closest('button, select, input')) return;
                const rect = panel.getBoundingClientRect();
                drag = {
                    pointerId: event.pointerId,
                    x: event.clientX,
                    y: event.clientY,
                    left: rect.left,
                    top: rect.top
                };
                handle.setPointerCapture(event.pointerId);
            });
            handle.addEventListener('pointermove', event => {
                if (!drag || event.pointerId !== drag.pointerId) return;
                const left = Math.max(6, Math.min(window.innerWidth - panel.offsetWidth - 6, drag.left + event.clientX - drag.x));
                const top = Math.max(6, Math.min(window.innerHeight - panel.offsetHeight - 6, drag.top + event.clientY - drag.y));
                panel.style.setProperty('left', left + 'px', 'important');
                panel.style.setProperty('top', top + 'px', 'important');
                panel.style.setProperty('right', 'auto', 'important');
                panel.style.setProperty('bottom', 'auto', 'important');
                panel.style.setProperty('transform', 'none', 'important');
                panel.dataset.userPositioned = '1';
            });
            const stop = () => {
                drag = null;
            };
            handle.addEventListener('pointerup', stop);
            handle.addEventListener('pointercancel', stop);
            handle.addEventListener('lostpointercapture', stop);
        }
        makeDraggable(notesPanel, byId('asyncNotesDrag'));
        makeDraggable(calcPanel, byId('asyncCalcDrag'));
        makeDraggable(soundPanel, byId('asyncSoundDrag'));
        makeDraggable(byId('readingToolsPanel'), byId('rtDrag'));
        byId('rtMinimize').addEventListener('click', () => {
            const readingPanel = byId('readingToolsPanel');
            const minimized = readingPanel.classList.toggle('is-minimized');
            byId('rtMinimize').textContent = minimized ? '□' : '−';
        });
        window.addEventListener('resize', () => {
            [notesPanel, calcPanel, soundPanel, byId('readingToolsPanel')].forEach(panel => {
                if (!panel.classList.contains('is-open')) return;
                if (!panel.dataset.userPositioned) {
                    panel.style.left = '50%';
                    panel.style.top = '50%';
                    panel.style.right = 'auto';
                    panel.style.bottom = 'auto';
                    panel.style.transform = 'translate(-50%,-50%)';
                    return;
                }
                const rect = panel.getBoundingClientRect();
                panel.style.left = Math.max(6, Math.min(window.innerWidth - panel.offsetWidth - 6, rect.left)) + 'px';
                panel.style.top = Math.max(6, Math.min(window.innerHeight - panel.offsetHeight - 6, rect.top)) + 'px';
                panel.style.right = 'auto';
                panel.style.bottom = 'auto';
                panel.style.transform = 'none';
            });
        });

        // Simple scientific calculator: fractions use / and exponents use ^.
        const display = byId('asyncCalcDisplay'),
            angle = byId('asyncCalcAngle'),
            calcStatus = byId('asyncCalcStatus');
        let expression = '',
            calculatorMemory = 0,
            lastAnswer = 0;

        function showExpression() {
            display.value = expression || '0';
        }

        function insert(value) {
            const a = display.selectionStart ?? expression.length,
                b = display.selectionEnd ?? a;
            expression = expression.slice(0, a) + value + expression.slice(b);
            showExpression();
            display.focus();
            const p = a + value.length;
            display.setSelectionRange(p, p);
        }

        function calculate() {
            if (!expression.trim()) return;
            try {
                const rad = x => angle.value === 'deg' ? x * Math.PI / 180 : x,
                    deg = x => angle.value === 'deg' ? x * 180 / Math.PI : x;
                const scope = {
                    pi: Math.PI,
                    e: Math.E,
                    sin: x => Math.sin(rad(x)),
                    cos: x => Math.cos(rad(x)),
                    tan: x => Math.tan(rad(x)),
                    asin: x => deg(Math.asin(x)),
                    acos: x => deg(Math.acos(x)),
                    atan: x => deg(Math.atan(x)),
                    sqrt: Math.sqrt,
                    log: Math.log10,
                    ln: Math.log,
                    abs: Math.abs
                };
                const n = math.evaluate(expression, scope);
                if (typeof n !== 'number' || !Number.isFinite(n)) throw new Error('Enter a valid numerical expression.');
                lastAnswer = n;
                expression = String(Number(n.toPrecision(12)));
                showExpression();
                calcStatus.textContent = 'Answer: ' + expression;
            } catch (e) {
                calcStatus.textContent = e.message || 'Invalid expression.';
            }
        }
        byId('asyncCalcKeys').addEventListener('click', e => {
            const k = e.target.closest('button');
            if (!k) return;
            const act = k.dataset.action,
                val = k.dataset.insert;
            if (act === 'clear') {
                expression = '';
                showExpression();
                calcStatus.textContent = 'Cleared.';
            } else if (act === 'back') {
                const a = display.selectionStart ?? expression.length,
                    b = display.selectionEnd ?? a,
                    p = a === b ? Math.max(0, a - 1) : a;
                expression = expression.slice(0, p) + expression.slice(b);
                showExpression();
                display.focus();
                display.setSelectionRange(p, p);
            } else if (act === 'equals') calculate();
            else if (act === 'mc') {
                calculatorMemory = 0;
                calcStatus.textContent = 'Memory cleared.';
            } else if (act === 'mr') insert(String(calculatorMemory));
            else if (act === 'ans') insert(String(lastAnswer));
            else if (act === 'mplus' || act === 'mminus') {
                try {
                    const n = Number(math.evaluate(expression || '0'));
                    calculatorMemory += act === 'mplus' ? n : -n;
                    calcStatus.textContent = 'Memory updated.';
                } catch (_) {
                    calcStatus.textContent = 'Enter a valid value first.';
                }
            } else if (val) insert(val);
        });
        display.addEventListener('input', () => expression = display.value);
        display.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                calculate();
            }
            if (e.key === 'Escape') {
                expression = '';
                showExpression();
            }
        });
        // Procedural ambient soundscapes: no audio files or third-party media required.
        let ambienceContext = null,
            ambienceNodes = [],
            selectedSound = 'rain',
            ambienceGain = null;
        const soundStatus = byId('asyncSoundStatus'),
            soundVolume = byId('asyncSoundVolume');

        function stopAmbience() {
            ambienceNodes.forEach(node => {
                try {
                    node.stop ? node.stop() : node.disconnect();
                } catch (_) {}
            });
            ambienceNodes = [];
            if (ambienceGain) {
                try {
                    ambienceGain.disconnect();
                } catch (_) {}
                ambienceGain = null;
            }
            soundStatus.textContent = 'Sound stopped.';
            document.querySelectorAll('[data-sound]').forEach(btn => btn.classList.toggle('active', btn.dataset.sound === selectedSound));
        }
        const selectedLayers = new Set();

        function addNoiseLayer(ctx, master, type, volume) {
            const len = ctx.sampleRate * 3,
                buf = ctx.createBuffer(1, len, ctx.sampleRate),
                d = buf.getChannelData(0);
            let brown = 0;
            for (let i = 0; i < len; i++) {
                const w = Math.random() * 2 - 1;
                if (type === 'pink') d[i] = (w + (i ? d[i - 1] * .94 : 0)) * .32;
                else if (type === 'brown') {
                    brown = (brown + .02 * w) / 1.02;
                    d[i] = brown * 3.5;
                } else d[i] = w;
            }
            const src = ctx.createBufferSource();
            src.buffer = buf;
            src.loop = true;
            const f = ctx.createBiquadFilter(),
                g = ctx.createGain();
            f.type = 'lowpass';
            f.frequency.value = type === 'white' ? 6500 : type === 'pink' ? 2300 : type === 'brown' ? 450 : 1200;
            g.gain.value = volume;
            src.connect(f);
            f.connect(g);
            g.connect(master);
            src.start();
            ambienceNodes.push(src);
            if (['thunder', 'train', 'heartbeat'].includes(type)) {
                const o = ctx.createOscillator(),
                    og = ctx.createGain();
                o.type = 'sine';
                o.frequency.value = type === 'thunder' ? 38 : type === 'train' ? 55 : 1.2;
                og.gain.value = .045;
                o.connect(og);
                og.connect(master);
                o.start();
                ambienceNodes.push(o);
            }
            if (['fan', 'aircon', 'rainroof', 'rainwindow', 'firecrackle', 'water', 'stream', 'night', 'birds', 'vinyl', 'cafe', 'typing'].includes(type)) {
                const o = ctx.createOscillator(),
                    og = ctx.createGain();
                o.frequency.value = type === 'typing' ? 2.8 : type === 'firecrackle' ? 3.5 : .16;
                og.gain.value = .012;
                o.connect(og);
                og.connect(master);
                o.start();
                ambienceNodes.push(o);
            }
        }
        document.querySelectorAll('#asyncSupplementalSounds [data-layer]').forEach(btn => btn.addEventListener('click', () => {
            const k = btn.dataset.layer;
            if (selectedLayers.has(k)) {
                selectedLayers.delete(k);
                btn.classList.remove('active');
            } else {
                selectedLayers.add(k);
                btn.classList.add('active');
            }
            soundStatus.textContent = selectedLayers.size + ' supplemental layers selected. Press Play to mix.';
            if (ambienceGain) startAmbience();
        }));

        function startAmbience() {
            const AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) {
                soundStatus.textContent = 'Web Audio is not supported in this browser.';
                return;
            }
            if (!ambienceContext) ambienceContext = new AC();
            ambienceContext.resume().then(() => {
                stopAmbience();
                const ctx = ambienceContext,
                    master = ctx.createGain();
                master.gain.value = Number(soundVolume.value) / 100 * 0.22;
                master.connect(ctx.destination);
                ambienceGain = master;
                const length = ctx.sampleRate * 3,
                    buffer = ctx.createBuffer(1, length, ctx.sampleRate),
                    data = buffer.getChannelData(0);
                for (let i = 0; i < length; i++) data[i] = Math.random() * 2 - 1;
                const noise = ctx.createBufferSource();
                noise.buffer = buffer;
                noise.loop = true;
                const filter = ctx.createBiquadFilter(),
                    tone = ctx.createGain();
                filter.type = 'lowpass';
                filter.frequency.value = 1100;
                filter.Q.value = .7;
                noise.connect(filter);
                filter.connect(tone);
                tone.connect(master);
                tone.gain.value = 1;
                noise.start();
                ambienceNodes.push(noise);
                const lfo = ctx.createOscillator(),
                    lfoGain = ctx.createGain();
                lfo.type = 'sine';
                lfo.frequency.value = .12;
                lfo.connect(lfoGain);
                lfoGain.connect(tone.gain);
                lfo.start();
                ambienceNodes.push(lfo);
                if (selectedSound === 'rain') {
                    filter.type = 'lowpass';
                    filter.frequency.value = 2200;
                    lfo.frequency.value = .35;
                    lfoGain.gain.value = .12;
                }
                if (selectedSound === 'ocean') {
                    filter.type = 'lowpass';
                    filter.frequency.value = 650;
                    lfo.frequency.value = .075;
                    lfoGain.gain.value = .55;
                }
                if (selectedSound === 'forest') {
                    filter.type = 'bandpass';
                    filter.frequency.value = 950;
                    lfo.frequency.value = .18;
                    lfoGain.gain.value = .12;
                    const bird = ctx.createOscillator(),
                        birdGain = ctx.createGain();
                    bird.type = 'sine';
                    bird.frequency.value = 2100;
                    birdGain.gain.value = .012;
                    bird.connect(birdGain);
                    birdGain.connect(master);
                    bird.start();
                    ambienceNodes.push(bird);
                }
                if (selectedSound === 'wind') {
                    filter.type = 'lowpass';
                    filter.frequency.value = 330;
                    lfo.frequency.value = .045;
                    lfoGain.gain.value = .7;
                }
                if (selectedSound === 'fire') {
                    filter.type = 'bandpass';
                    filter.frequency.value = 1250;
                    filter.Q.value = .45;
                    lfo.frequency.value = 1.7;
                    lfoGain.gain.value = .16;
                }
                const lv = Number(byId('asyncLayerVolume').value) / 100 * .10;
                selectedLayers.forEach(k => addNoiseLayer(ctx, master, k, lv));
                soundStatus.textContent = 'Playing ' + selectedSound + ' with ' + selectedLayers.size + ' supplemental layers.';
                byId('asyncSoundPlay').innerHTML = '<i class="bi bi-pause-fill"></i> Restart sound';
            }).catch(() => {
                soundStatus.textContent = 'Audio was blocked. Press Play again to allow sound.';
            });
        }
        document.querySelectorAll('[data-sound]').forEach(button => button.addEventListener('click', () => {
            selectedSound = button.dataset.sound;
            document.querySelectorAll('[data-sound]').forEach(item => item.classList.toggle('active', item === button));
            if (ambienceNodes.length) startAmbience();
            else soundStatus.textContent = selectedSound + ' selected. Press Play sound.';
        }));
        byId('asyncSoundPlay').addEventListener('click', startAmbience);
        byId('asyncSoundStop').addEventListener('click', stopAmbience);
        byId('asyncLayerVolume').addEventListener('input', () => {
            byId('asyncLayerVolumeValue').textContent = byId('asyncLayerVolume').value + '%';
            if (ambienceGain) startAmbience();
        });
        soundVolume.addEventListener('input', () => {
            byId('asyncSoundVolumeValue').textContent = soundVolume.value + '%';
            if (ambienceGain) ambienceGain.gain.setTargetAtTime(Number(soundVolume.value) / 100 * .22, ambienceContext.currentTime, .08);
        });
        window.addEventListener('beforeunload', stopAmbience);

        const themeObserver = new MutationObserver(() => {
            const dark = document.documentElement.getAttribute('data-theme') === 'dark';
            const rootStyle = document.documentElement.style;
            rootStyle.setProperty('--aux-glass', dark ? 'rgba(25,32,45,.93)' : 'rgba(255,255,255,.91)');
            rootStyle.setProperty('--aux-bg', dark ? 'rgba(25,32,45,.97)' : 'rgba(255,255,255,.97)');
            rootStyle.setProperty('--aux-text', dark ? '#edf2fa' : '#172033');
            rootStyle.setProperty('--aux-border', dark ? 'rgba(148,163,184,.25)' : 'rgba(100,116,139,.25)');
            rootStyle.setProperty('--aux-control', dark ? 'rgba(255,255,255,.07)' : 'rgba(15,23,42,.035)');
            rootStyle.setProperty('--aux-editor-bg', dark ? '#202938' : '#ffffff');
            rootStyle.setProperty('--aux-editor-text', dark ? '#edf2fa' : '#172033');
            rootStyle.setProperty('--aux-display-bg', dark ? '#111827' : '#f1f5f9');
            rootStyle.setProperty('--aux-op-bg', dark ? 'rgba(65,105,225,.16)' : '#eff6ff');
            rootStyle.setProperty('--rt-bg', dark ? 'rgba(25,32,45,.93)' : 'rgba(255,255,255,.91)');
            rootStyle.setProperty('--rt-text', dark ? '#edf2fa' : '#172033');
            rootStyle.setProperty('--rt-border', dark ? 'rgba(148,163,184,.25)' : 'rgba(100,116,139,.25)');
        });
        themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });
    })();
</script>