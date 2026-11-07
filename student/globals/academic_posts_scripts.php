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

    
#asyncNotesFab {
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


    


    
#asyncNotesPanel .aux-head {
        background: #0f8f83;
    }


    


    
#asyncNotesFab:hover,
#asyncNotesFab:focus-visible {
        opacity: 1;
        transform: translateY(-2px);
        box-shadow: 0 7px 20px rgba(15, 23, 42, .2);
        outline: none;
    }


    
#asyncNotesPanel {
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


    
#asyncNotesPanel.is-open {
        display: flex;
        flex-direction: column;
    }


    
#asyncNotesPanel {
        bottom: 138px;
        width: min(470px, calc(100vw - 28px));
        height: min(620px, 76vh);
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
#asyncNotesPanel button {
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


    
#asyncNotesPanel .notes-toolbar button {
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


    


    


    


    


    


    


    


    


    


    


    


    


    
html[data-theme="dark"] #asyncNotesPanel {
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


        
#asyncNotesFab {
            right: 14px;
            width: 43px;
            height: 43px;
        }


        
#asyncNotesFab {
            bottom: 76px;
        }


        


        
#asyncNotesPanel {
            right: 10px;
            width: calc(100vw - 20px);
        }


        
#asyncNotesPanel {
            bottom: 128px;
            height: min(72vh, 600px);
        }


        


        


        

    
}



    
/* ASYNC unified centered study workspace */

    
#readingToolsPanel,
#asyncNotesPanel,
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
#asyncSoundPanel.is-open {
        display: flex !important;
        flex-direction: column
    }


    
#readingToolsPanel:not(.is-open),
#asyncNotesPanel:not(.is-open),
#asyncSoundPanel:not(.is-open) {
        visibility: hidden;
        pointer-events: none
    }


    
#readingToolsPanel.is-open,
#asyncNotesPanel.is-open,
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


    


    
#asyncSoundPanel {
        width: min(390px, calc(100vw - 28px)) !important;
        padding: 0 14px 14px;
        gap: 12px
    }


    
#asyncNotesPanel.is-minimized,
#asyncSoundPanel.is-minimized,
#readingToolsPanel.is-minimized {
        width: min(300px, calc(100vw - 28px)) !important;
        height: auto !important;
        max-height: none !important;
        overflow: hidden !important;
    }


    
#asyncNotesPanel.is-minimized>:not(.aux-head),
#asyncSoundPanel.is-minimized>:not(.aux-head),
#readingToolsPanel.is-minimized>:not(.rt-header) {
        display: none !important
    }


    
#asyncNotesPanel.is-minimized .aux-head,
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


    


    


    


    


    


    


    


    
html[data-theme="dark"] #readingToolsPanel,
html[data-theme="dark"] #asyncNotesPanel,
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
#asyncSoundFab {
            right: 13px;
            width: 42px;
            height: 42px
        }


        
#asyncNotesFab {
            bottom: 72px
        }


        


        
#asyncSoundFab {
            bottom: 172px
        }


        
#readingToolsPanel,
#asyncNotesPanel,
#asyncSoundPanel {
            width: calc(100vw - 18px) !important;
            max-height: 84dvh;
            border-radius: 15px !important
        }


        
#asyncNotesPanel {
            height: 76dvh !important
        }


        


        


        


        
#readingToolsPanel {
            padding: 12px !important
        }

    
}



    
/* Unified ASYNC tool appearance: one royal-blue glass style for every tool and launcher. */

    
#readingToolsFab,
#asyncNotesFab,
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


    


    
#asyncSoundFab {
        bottom: 198px !important
    }


    
#readingToolsFab:hover,
#asyncNotesFab:hover,
#asyncSoundFab:hover,
#readingToolsFab:focus-visible,
#asyncNotesFab:focus-visible,
#asyncSoundFab:focus-visible {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(37, 76, 180, .36) !important;
        outline: none
    }


    
#readingToolsPanel .rt-header,
.aux-head,
#asyncNotesPanel .aux-head,
#asyncSoundPanel .aux-head {
        background: linear-gradient(110deg, #4169e1, #3157c7) !important;
        color: #fff !important
    }


    
#asyncNotesPanel .aux-head strong,
#asyncSoundPanel .aux-head strong,
#readingToolsPanel .rt-header strong {
        color: #fff !important
    }


    
#asyncNotesPanel .aux-window-actions button,
#asyncSoundPanel .aux-window-actions button,
#readingToolsPanel .rt-window-actions button {
        background: rgba(255, 255, 255, .16) !important;
        color: #fff !important
    }


    
#asyncNotesPanel .aux-head,
#asyncSoundPanel .aux-head {
        border-radius: 16px 16px 0 0
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
#readingToolsPanel .rt-btn {
        border-radius: 9px
    }


    
#asyncNotesPanel .notes-toolbar button:hover,
#readingToolsPanel .rt-btn:hover {
        background: #4169e1;
        color: #fff;
        border-color: #4169e1
    }


    
@media(max-width:480px) {


        
#readingToolsFab,
#asyncNotesFab,
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


        


        
#asyncSoundFab {
            bottom: 174px !important
        }

    
}



    

    


    


    


    


    


    


    


    


    


    


    


    


    


    


    


    


    


    


    
@media(max-width:480px) {

        


        


        


        

    
}



    
#readingToolsFab,
#asyncNotesFab,
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
        width: 48px;
        height: 48px;
        border: 1px solid rgba(255, 255, 255, .52);
        border-radius: 16px;
        background: linear-gradient(145deg, rgba(59, 130, 246, .88), rgba(37, 99, 235, .78));
        color: #fff;
        font-size: 22px;
        box-shadow: 0 9px 26px rgba(37, 76, 180, .28);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        cursor: pointer;
        transition: transform .52s cubic-bezier(.65, 0, .35, 1), border-radius .52s cubic-bezier(.65, 0, .35, 1), box-shadow .52s cubic-bezier(.65, 0, .35, 1), background .3s ease
    }


    
#asyncToolsFab[aria-expanded=true] {
        transform: rotate(90deg) scale(1.02);
        border-radius: 50%;
        box-shadow: 0 12px 30px rgba(37, 76, 180, .4)
    }

#asyncToolsFab i {
        display: inline-block;
        transition: transform .52s cubic-bezier(.65, 0, .35, 1), opacity .22s ease;
    }
#asyncToolsFab[aria-expanded="true"] i {
        transform: rotate(180deg) scale(1.08);
    }


    
#asyncToolsMenu {
        display: flex;
        flex-direction: column-reverse;
        gap: 8px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(12px) scale(.94);
        transform-origin: bottom right;
        transition: opacity .42s cubic-bezier(.65, 0, .35, 1), transform .52s cubic-bezier(.65, 0, .35, 1), visibility .42s
    }


    
#asyncToolsMenu.is-visible {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0) scale(1)
    }


    
#asyncToolsMenu button {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 175px;
        padding: 11px 14px;
        border: 1px solid rgba(255, 255, 255, .48);
        border-radius: 13px;
        background: rgba(59, 130, 246, .78);
        color: #fff;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, .16);
        font-size: 13px;
        text-align: left;
        cursor: pointer;
        transition: transform .28s cubic-bezier(.22, 1, .36, 1), filter .2s ease, background .2s ease
    }


    
#asyncToolsMenu button:nth-child(2) {
        background: rgba(13, 148, 136, .78)
    }


    
#asyncToolsMenu button:nth-child(3) {
        background: rgba(124, 58, 237, .78)
    }


    
#asyncToolsMenu button:nth-child(4) {
        background: rgba(217, 119, 6, .78)
    }


    
#asyncToolsMenu button:nth-child(5) {
        background: rgba(225, 29, 72, .78)
    }


    
#asyncToolsMenu button:hover {
        transform: translateX(-4px) scale(1.025);
        filter: brightness(1.08)
    }


    
#asyncToolsMenu button i {
        width: 20px;
        text-align: center;
        font-size: 17px
    }


    
#readingToolsPanel,
#asyncNotesPanel,
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
#asyncSoundPanel[data-user-positioned="1"] {
        transform: none !important
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
        background: rgba(16, 185, 129, .18);
        border-color: #10b981;
        color: var(--aux-text);
        font-weight: 800
    }


    
#asyncSupplementalSounds button {
        min-height: 34px;
        transition: background .18s ease, border-color .18s ease, transform .18s ease
    }


    
#asyncSupplementalSounds button:hover {
        transform: translateY(-1px);
        border-color: #60a5fa
    }


    
html[data-theme="dark"] #readingToolsPanel,
html[data-theme="dark"] #asyncNotesPanel,
html[data-theme="dark"] #asyncSoundPanel {
        --aux-text: #f1f5f9 !important;
        --aux-editor-text: #f1f5f9 !important;
        --aux-control: rgba(255, 255, 255, .09) !important;
        --aux-display-bg: #111827 !important;
        --aux-op-bg: rgba(96, 165, 250, .2) !important;
        --aux-border: rgba(203, 213, 225, .28) !important;
        color: #f1f5f9 !important;
    }


    
html[data-theme="dark"] #readingToolsPanel :is(p, span, label, small, strong, div, h1, h2, h3, h4, li),
html[data-theme="dark"] #asyncNotesPanel :is(p, span, label, small, strong, div, h1, h2, h3, h4, li),
html[data-theme="dark"] #asyncSoundPanel :is(p, span, label, small, strong, div, h1, h2, h3, h4, li) {
        color: var(--aux-text)
    }


    
html[data-theme="dark"] #readingToolsPanel :is(input, select, textarea),
html[data-theme="dark"] #asyncNotesPanel :is(input, select, textarea),
html[data-theme="dark"] #asyncSoundPanel :is(input, select, textarea) {
        background: #172033 !important;
        color: #f8fafc !important;
        border-color: var(--aux-border) !important
    }


    
html[data-theme="dark"] #asyncNotesPanel .ql-editor {
        color: #f1f5f9 !important
    }


    
html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-picker-options {
        background: #172033;
        color: #f1f5f9
    }


    
html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-picker-item {
        color: #f1f5f9
    }


    
html[data-theme="dark"] #readingToolsPanel .rt-btn,
html[data-theme="dark"] #asyncSoundPanel button {
        color: #f8fafc
    }


    
html[data-theme="dark"] #asyncFloatingTimer {
        color: #f8fafc !important;
        background: rgba(25, 32, 45, .97) !important;
        border-color: rgba(203, 213, 225, .25) !important
    }


    
html[data-theme="dark"] #asyncFloatingTimer :is(strong, label, span, div, p) {
        color: #f1f5f9 !important
    }


    
html[data-theme="dark"] #asyncFloatingTimer input {
        background: #172033 !important;
        color: #f8fafc !important;
        border-color: rgba(203, 213, 225, .28) !important
    }


    
html[data-theme="dark"] #readingToolsPanel button:not(.rt-header button),
html[data-theme="dark"] #asyncNotesPanel button,
html[data-theme="dark"] #asyncSoundPanel button {
        color: #f8fafc
    }


    
@media(max-width:520px) {

        
#asyncToolsDock {
            right: 12px;
            bottom: 12px
        }


        
#asyncToolsFab {
            width: 44px;
            height: 44px;
            border-radius: 14px;
        }


        
#asyncToolsMenu button {
            min-width: 158px;
            padding: 10px 12px
        }


        
#readingToolsPanel,
#asyncNotesPanel,
#asyncSoundPanel {
            width: calc(100vw - 14px) !important;
            max-height: calc(100dvh - 14px) !important
        }


        


        
#asyncSupplementalSounds {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            max-height: 130px
        }

    
}



    
#asyncPresentationPanel {
        display: none;
        position: fixed;
        z-index: 100090;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: min(430px, calc(100vw - 24px));
        max-height: 85dvh;
        overflow: auto;
        padding: 14px;
        border: 1px solid var(--aux-border, rgba(148, 163, 184, .3));
        border-radius: 18px;
        background: var(--aux-glass, rgba(255, 255, 255, .94));
        color: var(--aux-text, #172033);
        box-shadow: 0 22px 65px rgba(15, 23, 42, .25);
        backdrop-filter: blur(20px)
    }


    
#asyncPresentationPanel.is-open {
        display: flex;
        flex-direction: column;
        gap: 12px;
        animation: asyncToolPop .22s ease-out
    }


    
#asyncPresentationPanel.is-minimized {
        display: none !important
    }


    
#asyncPresentationPanel .presentation-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px
    }


    
#asyncPresentationPanel .presentation-actions button {
        padding: 11px 9px;
        border: 1px solid var(--aux-border);
        border-radius: 10px;
        background: var(--aux-control);
        color: var(--aux-text);
        cursor: pointer
    }


    
#asyncPresentationPanel .presentation-actions button.active {
        background: rgba(37, 99, 235, .18);
        border-color: #3b82f6;
        color: var(--aux-text);
        font-weight: 800
    }


    
#asyncPresentationOverlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 100080;
        pointer-events: none
    }


    
#asyncPresentationOverlay.active {
        display: block
    }


    
#asyncPresentationCursor {
        position: fixed;
        left: 0;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #ff2d55;
        box-shadow: 0 0 0 5px rgba(255, 45, 85, .2), 0 0 18px rgba(255, 45, 85, .85);
        transform: translate(-50%, -50%);
        display: none
    }

#asyncPresentationCrosshair { display:none;position:fixed;inset:0; }
#asyncPresentationCrosshair span { position:absolute;background:rgba(37,99,235,.85);box-shadow:0 0 2px #fff; }
#asyncPresentationCrosshair span:first-child { left:0;right:0;height:1px;top:50%; }
#asyncPresentationCrosshair span:last-child { top:0;bottom:0;width:1px;left:50%; }
#asyncPresentationOverlay.mode-crosshair #asyncPresentationCrosshair { display:block; }
#asyncPresentationMagnifier { display:none;position:fixed;left:0;top:0;width:170px;height:170px;border:3px solid #2563eb;border-radius:50%;overflow:hidden;background:rgba(255,255,255,.12);box-shadow:0 8px 30px rgba(0,0,0,.3);transform:translate(22px,22px); }
#asyncPresentationOverlay.mode-magnifier #asyncPresentationMagnifier { display:block; }
#asyncPresentationMagnifierContent { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font:800 22px/1.25 system-ui;background:rgba(15,23,42,.78);text-align:center;padding:12px;overflow:hidden; }
#asyncPresentationOverlay.mode-ruler { cursor:crosshair; }


    
#asyncPresentationOverlay.mode-pointer #asyncPresentationCursor {
        width: 24px;
        height: 24px;
        background: transparent;
        border: 3px solid #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .2)
    }


    
#asyncPresentationOverlay.mode-laser #asyncPresentationCursor {
        display: block
    }


    
#asyncPresentationOverlay.mode-pointer #asyncPresentationCursor {
        display: block
    }


    
#asyncPresentationOverlay.mode-spotlight {
        background: rgba(0, 0, 0, .68);
        -webkit-mask: radial-gradient(circle 95px at var(--spot-x, 50%) var(--spot-y, 50%), transparent 0 90px, #000 98px);
        mask: radial-gradient(circle 95px at var(--spot-x, 50%) var(--spot-y, 50%), transparent 0 90px, #000 98px)
    }


    
#asyncPresentationOverlay.mode-spotlight #asyncPresentationSpotlight {
        display: block;
        position: fixed;
        left: var(--spot-x, 50%);
        top: var(--spot-y, 50%);
        width: 190px;
        height: 190px;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, .65);
        border: 2px solid rgba(255, 255, 255, .85)
    }


    
#asyncPresentationCanvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        touch-action: none
    }


    
#asyncPresentationOverlay.mode-draw {
        pointer-events: auto;
        cursor: crosshair
    }


    
#asyncPresentationOverlay.mode-draw #asyncPresentationCanvas {
        pointer-events: auto;
        cursor: crosshair
    }


    
#asyncPresentationPanel[data-user-positioned="1"] {
        transform: none !important
    }


    
/* Resize controls for the actual floating tool windows (not the launcher). */

    
#readingToolsPanel,
#asyncNotesPanel,
#asyncSoundPanel,
#asyncPresentationPanel,
#asyncFloatingTimer {
        box-sizing: border-box;
    }


    
.async-tool-resize-handle {
        position: absolute;
        z-index: 100;
        display: block;
        padding: 0;
        margin: 0;
        border: 0;
        background: transparent;
        touch-action: none;
        user-select: none;
        -webkit-user-select: none;
        opacity: 0;
        background: transparent;
        transition: opacity .16s ease, background .16s ease;
    }


    
#readingToolsPanel:hover .async-tool-resize-handle,
#asyncNotesPanel:hover .async-tool-resize-handle,
#asyncSoundPanel:hover .async-tool-resize-handle,
#asyncPresentationPanel:hover .async-tool-resize-handle,
#asyncFloatingTimer:hover .async-tool-resize-handle,
.async-tool-resize-handle:focus-visible,
.async-tool-resize-handle:hover,
.async-tool-resize-handle:active {
        opacity: .95;
        background: rgba(96, 165, 250, .22);
    }


    
.async-tool-resize-handle[data-resize="n"],
.async-tool-resize-handle[data-resize="s"] {
        left: 14px;
        right: 14px;
        height: 9px;
        cursor: ns-resize;
    }


    
.async-tool-resize-handle[data-resize="n"] {
        top: 1px;
    }


    
.async-tool-resize-handle[data-resize="s"] {
        bottom: 1px;
    }


    
.async-tool-resize-handle[data-resize="e"],
.async-tool-resize-handle[data-resize="w"] {
        top: 14px;
        bottom: 14px;
        width: 9px;
        cursor: ew-resize;
    }


    
.async-tool-resize-handle[data-resize="e"] {
        right: 1px;
    }


    
.async-tool-resize-handle[data-resize="w"] {
        left: 1px;
    }


    
.async-tool-resize-handle[data-resize="ne"],
.async-tool-resize-handle[data-resize="nw"],
.async-tool-resize-handle[data-resize="se"],
.async-tool-resize-handle[data-resize="sw"] {
        width: 17px;
        height: 17px;
        border: 1px solid rgba(255, 255, 255, .58);
        border-radius: 6px;
        background: rgba(59, 130, 246, .42);
        box-shadow: 0 1px 5px rgba(15, 23, 42, .18);
    }


    
.async-tool-resize-handle[data-resize="ne"] {
        top: 2px;
        right: 2px;
        cursor: nesw-resize;
    }


    
.async-tool-resize-handle[data-resize="nw"] {
        top: 2px;
        left: 2px;
        cursor: nwse-resize;
    }


    
.async-tool-resize-handle[data-resize="se"] {
        bottom: 2px;
        right: 2px;
        cursor: nwse-resize;
    }


    
.async-tool-resize-handle[data-resize="sw"] {
        bottom: 2px;
        left: 2px;
        cursor: nesw-resize;
    }


    
#readingToolsPanel.is-minimized .async-tool-resize-handle,
#asyncNotesPanel.is-minimized .async-tool-resize-handle,
#asyncSoundPanel.is-minimized .async-tool-resize-handle,
#asyncPresentationPanel.is-minimized .async-tool-resize-handle,
#asyncFloatingTimer.is-minimized .async-tool-resize-handle {
        display: none !important;
    }


    
@media (max-width: 480px) {


        
.async-tool-resize-handle[data-resize="ne"],
.async-tool-resize-handle[data-resize="nw"],
.async-tool-resize-handle[data-resize="se"],
.async-tool-resize-handle[data-resize="sw"] {
            width: 20px;
            height: 20px;
        }

    
}



    
/* Neutral icon controls are styled in the final theme-aware rules below. */

#aftTickToggle {
        width: 29px;
        height: 29px;
        flex: 0 0 29px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, .24);
        border-radius: 9px;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        font-size: 15px;
        cursor: pointer
    }


    
#aftTickToggle[aria-pressed="false"] {
        opacity: .65;
        background: rgba(15, 23, 42, .2)
    }


    
/* Match the title bar color with the tool's colored launcher/menu entry. */

    
#readingToolsPanel .rt-header {
        background: #3b82f6 !important
    }


    
#asyncNotesPanel .aux-head {
        background: #0d9488 !important
    }


    


    
#asyncSoundPanel .aux-head {
        background: #d97706 !important
    }


    
#asyncPresentationPanel .aux-head {
        background: #e11d48 !important
    }


    
#asyncFloatingTimer .aft-header {
        background: #3b82f6
    }


    
/* Quill formatting buttons: clean spacing and responsive wrapping. */

    
#asyncNotesPanel .ql-toolbar.ql-snow {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
        padding: 8px 9px !important;
        overflow: visible;
        box-sizing: border-box
    }


    
#asyncNotesPanel .ql-toolbar.ql-snow .ql-formats {
        display: inline-flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 3px;
        margin: 2px 7px 2px 0 !important
    }


    
#asyncNotesPanel .ql-toolbar.ql-snow button {
        width: 29px;
        height: 29px;
        padding: 4px !important;
        margin: 0 !important;
        border-radius: 6px
    }


    
#asyncNotesPanel .ql-toolbar.ql-snow .ql-picker {
        height: 29px;
        margin: 0 2px
    }


    
#asyncNotesPanel .ql-toolbar.ql-snow .ql-picker-label {
        padding: 3px 5px;
        border-radius: 6px
    }


    
#asyncNotesPanel .ql-toolbar.ql-snow .ql-picker-options {
        max-height: 220px;
        overflow: auto
    }


    
@media(max-width:480px) {

        
#asyncNotesPanel .ql-toolbar.ql-snow {
            gap: 3px;
            padding: 7px 6px !important
        }


        
#asyncNotesPanel .ql-toolbar.ql-snow .ql-formats {
            gap: 2px;
            margin-right: 4px !important
        }


        
#asyncNotesPanel .ql-toolbar.ql-snow button {
            width: 27px;
            height: 27px
        }

    
}


    
/* Presentation minimization collapses to an interactive icon toolbar. */

    
#asyncPresentationPanel.is-minimized {
        display: flex !important;
        flex-direction: column;
        width: max-content !important;
        min-width: 0 !important;
        max-width: calc(100vw - 16px) !important;
        height: auto !important;
        max-height: none !important;
        padding: 6px !important;
        gap: 5px !important;
        overflow: visible !important;
        border-radius: 13px !important;
    }


    
#asyncPresentationPanel.is-minimized>:not(.aux-head):not(.presentation-actions) {
        display: none !important
    }


    
#asyncPresentationPanel.is-minimized .aux-head {
        padding: 2px 3px !important;
        min-height: 14px;
        gap: 4px;
        background: transparent !important
    }


    
#asyncPresentationPanel.is-minimized .aux-head>strong,
#asyncPresentationPanel.is-minimized .aux-drag-hint {
        display: none !important
    }


    
#asyncPresentationPanel.is-minimized .aux-window-actions {
        margin: 0 !important
    }


    
#asyncPresentationPanel.is-minimized .presentation-actions {
        display: flex !important;
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px !important
    }


    
#asyncPresentationPanel.is-minimized .presentation-actions button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 33px;
        height: 33px;
        min-width: 33px;
        min-height: 33px;
        padding: 0 !important;
        margin: 0;
        font-size: 0 !important;
        line-height: 0;
        border-radius: 8px;
    }


    
#asyncPresentationPanel.is-minimized .presentation-actions button i {
        font-size: 15px;
        line-height: 1
    }


    
#asyncPresentationPanel.is-minimized .presentation-actions button.active {
        background: rgba(225, 29, 72, .2) !important;
        border-color: #e11d48 !important
    }


    
/* Correct light/dark presentation text and controls. */

    
html[data-theme="dark"] #asyncPresentationPanel {
        --aux-text: #f1f5f9 !important;
        --aux-control: rgba(255, 255, 255, .09) !important;
        --aux-border: rgba(203, 213, 225, .3) !important;
        --aux-glass: rgba(25, 32, 45, .96) !important;
        background: rgba(25, 32, 45, .96) !important;
        color: #f1f5f9 !important;
    }


    
html[data-theme="dark"] #asyncPresentationPanel>p,
html[data-theme="dark"] #asyncPresentationPanel label,
html[data-theme="dark"] #asyncPresentationPanel label span,
html[data-theme="dark"] #asyncPresentationPanel #asyncPresentationStatus {
        color: #f1f5f9 !important
    }


    
html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button {
        background: rgba(255, 255, 255, .08) !important;
        border-color: rgba(203, 213, 225, .3) !important;
        color: #f8fafc !important
    }


    
html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button.active {
        background: rgba(225, 29, 72, .24) !important;
        border-color: #fb7185 !important;
        color: #fff !important
    }


    
html[data-theme="dark"] #asyncPresentationPanel input,
html[data-theme="dark"] #asyncPresentationPanel select {
        color: #f8fafc !important;
        background: #172033 !important;
        border-color: rgba(203, 213, 225, .3) !important
    }


    
html[data-theme="dark"] #asyncPresentationPanel :is(p, div, label, span, small) {
        color: #f1f5f9 !important
    }


    
/* Additional annotation modes can receive pointer/touch drawing input. */

    
#asyncPresentationOverlay:is(.mode-draw, .mode-arrow, .mode-rectangle, .mode-circle, .mode-erase) {
        pointer-events: auto;
        cursor: crosshair
    }


    
#asyncPresentationOverlay:is(.mode-draw, .mode-arrow, .mode-rectangle, .mode-circle, .mode-erase) #asyncPresentationCanvas {
        pointer-events: auto;
        cursor: crosshair
    }


    
#readingToolsPanel.is-maximized .async-tool-resize-handle,
#asyncNotesPanel.is-maximized .async-tool-resize-handle,
#asyncSoundPanel.is-maximized .async-tool-resize-handle,
#asyncPresentationPanel.is-maximized .async-tool-resize-handle,
#asyncFloatingTimer.is-maximized .async-tool-resize-handle {
        display: none !important
    }



    
#readingToolsPanel.is-maximized .async-tool-resize-handle,
#asyncNotesPanel.is-maximized .async-tool-resize-handle,
#asyncSoundPanel.is-maximized .async-tool-resize-handle,
#asyncPresentationPanel.is-maximized .async-tool-resize-handle,
#asyncFloatingTimer.is-maximized .async-tool-resize-handle {
        display: none !important
    }


    
#asyncPresentationOverlay:is(.mode-draw, .mode-arrow, .mode-rectangle, .mode-circle, .mode-erase) {
        pointer-events: auto;
        cursor: crosshair
    }


    
#asyncPresentationOverlay:is(.mode-draw, .mode-arrow, .mode-rectangle, .mode-circle, .mode-erase) #asyncPresentationCanvas {
        pointer-events: auto;
        cursor: crosshair
    }



    
/* Higher-specificity overrides for the controls' original component styles. */

    
#asyncNotesPanel .aux-window-actions .window-close,
#asyncSoundPanel .aux-window-actions .window-close,
#asyncPresentationPanel .aux-window-actions .window-close {
        background: #ff5f57 !important;
        color: transparent !important
    }


    
#asyncNotesPanel .aux-window-actions .window-minimize,
#asyncSoundPanel .aux-window-actions .window-minimize,
#asyncPresentationPanel .aux-window-actions .window-minimize {
        background: #febc2e !important;
        color: transparent !important
    }


    
#asyncNotesPanel .aux-window-actions .window-maximize,
#asyncSoundPanel .aux-window-actions .window-maximize,
#asyncPresentationPanel .aux-window-actions .window-maximize {
        background: #28c840 !important;
        color: transparent !important
    }


    
#aftClose,
#aftMinimize,
#aftMaximize {
        width: 12px !important;
        height: 12px !important;
        min-width: 12px !important;
        min-height: 12px !important;
        padding: 0 !important;
        border: 1px solid rgba(0, 0, 0, .12) !important;
        border-radius: 50% !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 0 !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .3) !important;
    }


    
#aftClose {
        background: #ff5f57 !important
    }


    
#aftMinimize {
        background: #febc2e !important
    }


    
#aftMaximize {
        background: #28c840 !important
    }



    
/* ============================================================
       ASYNC COLOUR-MATCHED GLASSMORPHISM
       Glass keeps each tool's established accent identity:
       blue / teal / purple / orange / red.
       ============================================================ */


    
/* Light-theme tinted glass surfaces. */

    
#readingToolsPanel {
        --tool-glass: rgba(219, 234, 254, .70);
        --tool-glass-deep: rgba(191, 219, 254, .45);
        --tool-glass-border: rgba(59, 130, 246, .34);
        --tool-control-glass: rgba(59, 130, 246, .10);
        --tool-control-hover: rgba(59, 130, 246, .19);
        --tool-accent: #3b82f6;
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #172033 !important;
    }


    
#asyncNotesPanel {
        --tool-glass: rgba(204, 251, 241, .70);
        --tool-glass-deep: rgba(153, 246, 228, .43);
        --tool-glass-border: rgba(13, 148, 136, .34);
        --tool-control-glass: rgba(13, 148, 136, .10);
        --tool-control-hover: rgba(13, 148, 136, .19);
        --tool-accent: #0d9488;
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #123532 !important;
    }


    


    
#asyncSoundPanel {
        --tool-glass: rgba(255, 237, 213, .74);
        --tool-glass-deep: rgba(254, 215, 170, .47);
        --tool-glass-border: rgba(217, 119, 6, .36);
        --tool-control-glass: rgba(217, 119, 6, .10);
        --tool-control-hover: rgba(217, 119, 6, .19);
        --tool-accent: #d97706;
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #422006 !important;
    }


    
#asyncPresentationPanel {
        --tool-glass: rgba(255, 228, 230, .73);
        --tool-glass-deep: rgba(254, 205, 211, .46);
        --tool-glass-border: rgba(225, 29, 72, .34);
        --tool-control-glass: rgba(225, 29, 72, .09);
        --tool-control-hover: rgba(225, 29, 72, .18);
        --tool-accent: #e11d48;
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #4c0519 !important;
    }


    
/* Frosted surfaces: retain hue while allowing the page underneath to show. */

    
#readingToolsPanel,
#asyncNotesPanel,
#asyncSoundPanel,
#asyncPresentationPanel {
        backdrop-filter: blur(26px) saturate(175%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(175%) !important;
        box-shadow:
            0 22px 64px rgba(15, 23, 42, .20),
            0 6px 18px rgba(15, 23, 42, .08),
            inset 0 1px 0 rgba(255, 255, 255, .68),
            inset 0 -1px 0 rgba(255, 255, 255, .18) !important;
        isolation: isolate;
    }


    
/* Color-matched translucent header strips. */

    
#readingToolsPanel .rt-header {
        background: linear-gradient(112deg, rgba(59, 130, 246, .86), rgba(37, 99, 235, .68)) !important;
        border: 1px solid rgba(255, 255, 255, .42) !important;
        border-bottom-color: rgba(255, 255, 255, .30) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .38), 0 5px 16px rgba(37, 99, 235, .12);
        backdrop-filter: blur(18px) saturate(170%);
        -webkit-backdrop-filter: blur(18px) saturate(170%);
    }


    
#asyncNotesPanel .aux-head {
        background: linear-gradient(112deg, rgba(13, 148, 136, .87), rgba(15, 118, 110, .68)) !important;
        border: 1px solid rgba(255, 255, 255, .42) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .38), 0 5px 16px rgba(13, 148, 136, .12);
        backdrop-filter: blur(18px) saturate(170%);
        -webkit-backdrop-filter: blur(18px) saturate(170%);
    }


    


    
#asyncSoundPanel .aux-head {
        background: linear-gradient(112deg, rgba(217, 119, 6, .88), rgba(180, 83, 9, .68)) !important;
        border: 1px solid rgba(255, 255, 255, .42) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .38), 0 5px 16px rgba(217, 119, 6, .12);
        backdrop-filter: blur(18px) saturate(170%);
        -webkit-backdrop-filter: blur(18px) saturate(170%);
    }


    
#asyncPresentationPanel .aux-head {
        background: linear-gradient(112deg, rgba(225, 29, 72, .87), rgba(190, 18, 60, .68)) !important;
        border: 1px solid rgba(255, 255, 255, .42) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .38), 0 5px 16px rgba(225, 29, 72, .12);
        backdrop-filter: blur(18px) saturate(170%);
        -webkit-backdrop-filter: blur(18px) saturate(170%);
    }


    
#asyncFloatingTimer {
        background: linear-gradient(135deg, rgba(59, 130, 246, .48), rgba(30, 64, 175, .32)) !important;
        border: 1px solid rgba(191, 219, 254, .58) !important;
        box-shadow: 0 18px 48px rgba(30, 64, 175, .22),
            inset 0 1px 0 rgba(255, 255, 255, .55),
            inset 0 -1px 0 rgba(255, 255, 255, .16) !important;
        backdrop-filter: blur(24px) saturate(175%) !important;
        -webkit-backdrop-filter: blur(24px) saturate(175%) !important;
    }


    
#asyncFloatingTimer .aft-header {
        background: linear-gradient(112deg, rgba(59, 130, 246, .64), rgba(37, 99, 235, .44)) !important;
        border: 1px solid rgba(255, 255, 255, .32);
        border-radius: 12px;
        padding: 7px 8px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .32);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
    }


    
/* Controls use matching soft colour glass instead of neutral grey. */

    
#readingToolsPanel :is(.rt-btn, .rt-select, select, input:not([type="range"])),
#asyncNotesPanel :is(input, select, .ql-toolbar.ql-snow),
#asyncSoundPanel :is(select, button:not(.window-icon-btn), input:not([type="range"])),
#asyncPresentationPanel :is(button:not(.window-icon-btn), input:not([type="range"]), select) {
        background-color: var(--tool-control-glass) !important;
        border-color: var(--tool-glass-border) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .62),
            0 2px 8px rgba(15, 23, 42, .035);
        backdrop-filter: blur(12px) saturate(155%);
        -webkit-backdrop-filter: blur(12px) saturate(155%);
    }


    
#readingToolsPanel :is(.rt-btn, .rt-select, select) {
        color: #1e3a8a;
    }


    
#asyncNotesPanel :is(input, select, .ql-toolbar.ql-snow) {
        color: #134e4a;
    }


    


    
#asyncSoundPanel :is(select, button:not(.window-icon-btn), input:not([type="range"])) {
        color: #78350f;
    }


    
#asyncPresentationPanel :is(button:not(.window-icon-btn), input:not([type="range"]), select) {
        color: #881337;
    }


    
#readingToolsPanel :is(.rt-btn, select):hover,
#asyncNotesPanel :is(.notes-toolbar button, input, select):hover,
#asyncSoundPanel :is(.sound-choice button, .sound-actions button):hover,
#asyncPresentationPanel .presentation-actions button:hover {
        background-color: var(--tool-control-hover) !important;
        border-color: var(--tool-accent) !important;
    }


    
/* Keep the Notes editor translucent and teal-tinted. */

    
#asyncNotesPanel #asyncQuillEditor,
#asyncNotesPanel .ql-container.ql-snow,
#asyncNotesPanel .ql-editor {
        background: rgba(13, 148, 136, .045) !important;
    }


    
#asyncNotesPanel .ql-toolbar.ql-snow {
        background: rgba(13, 148, 136, .12) !important;
        border-color: rgba(13, 148, 136, .24) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .58);
    }


    
#asyncNotesPanel .ql-snow .ql-stroke {
        stroke: #0f766e;
    }


    
#asyncNotesPanel .ql-snow .ql-fill {
        fill: #0f766e;
    }


    
#asyncNotesPanel .ql-snow .ql-picker {
        color: #0f766e;
    }


    

    


    


    
/* Sound actions retain the orange identity. */

    
#asyncSoundPanel .sound-choice button.active {
        background: rgba(217, 119, 6, .20) !important;
        border-color: rgba(217, 119, 6, .58) !important;
        color: #78350f !important;
    }


    
#asyncSoundPanel .sound-actions button {
        background: linear-gradient(135deg, rgba(217, 119, 6, .88), rgba(180, 83, 9, .76)) !important;
        border: 1px solid rgba(255, 255, 255, .42) !important;
        color: #fff !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .35), 0 4px 12px rgba(180, 83, 9, .15);
    }


    
/* Presentation controls remain red/pink-tinted glass. */

    
#asyncPresentationPanel .presentation-actions button {
        background: rgba(225, 29, 72, .10) !important;
        border-color: rgba(225, 29, 72, .25) !important;
        color: #881337 !important;
    }


    
#asyncPresentationPanel .presentation-actions button.active {
        background: rgba(225, 29, 72, .23) !important;
        border-color: rgba(225, 29, 72, .58) !important;
        color: #881337 !important;
    }


    
/* Dark theme: preserve each hue in deeper tinted glass, not generic charcoal. */

    
html[data-theme="dark"] #readingToolsPanel {
        --tool-glass: rgba(30, 64, 175, .49);
        --tool-glass-deep: rgba(30, 58, 138, .34);
        --tool-glass-border: rgba(147, 197, 253, .30);
        --tool-control-glass: rgba(59, 130, 246, .16);
        --tool-control-hover: rgba(59, 130, 246, .26);
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #eff6ff !important;
    }


    
html[data-theme="dark"] #asyncNotesPanel {
        --tool-glass: rgba(15, 118, 110, .49);
        --tool-glass-deep: rgba(4, 78, 75, .34);
        --tool-glass-border: rgba(94, 234, 212, .27);
        --tool-control-glass: rgba(13, 148, 136, .17);
        --tool-control-hover: rgba(13, 148, 136, .27);
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #ecfdf5 !important;
    }


    


    
html[data-theme="dark"] #asyncSoundPanel {
        --tool-glass: rgba(154, 52, 18, .49);
        --tool-glass-deep: rgba(124, 45, 18, .36);
        --tool-glass-border: rgba(253, 186, 116, .30);
        --tool-control-glass: rgba(217, 119, 6, .17);
        --tool-control-hover: rgba(217, 119, 6, .28);
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #fff7ed !important;
    }


    
html[data-theme="dark"] #asyncPresentationPanel {
        --tool-glass: rgba(159, 18, 57, .49);
        --tool-glass-deep: rgba(136, 19, 55, .36);
        --tool-glass-border: rgba(253, 164, 175, .30);
        --tool-control-glass: rgba(225, 29, 72, .17);
        --tool-control-hover: rgba(225, 29, 72, .28);
        background: linear-gradient(135deg, var(--tool-glass), var(--tool-glass-deep)) !important;
        border-color: var(--tool-glass-border) !important;
        color: #fff1f2 !important;
    }


    
html[data-theme="dark"] #readingToolsPanel :is(.rt-btn, .rt-select, select, input:not([type="range"])),
html[data-theme="dark"] #asyncNotesPanel :is(input, select, .ql-toolbar.ql-snow),
html[data-theme="dark"] #asyncSoundPanel :is(select, button:not(.window-icon-btn), input:not([type="range"])),
html[data-theme="dark"] #asyncPresentationPanel :is(button:not(.window-icon-btn), input:not([type="range"]), select) {
        background-color: var(--tool-control-glass) !important;
        border-color: var(--tool-glass-border) !important;
        color: #f8fafc !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .14),
            0 3px 10px rgba(0, 0, 0, .10);
    }


    
html[data-theme="dark"] #readingToolsPanel :is(.rt-btn, .rt-select, select),
html[data-theme="dark"] #asyncNotesPanel :is(input, select, .ql-editor, .ql-picker),
html[data-theme="dark"] #asyncSoundPanel :is(select, button:not(.window-icon-btn)),
html[data-theme="dark"] #asyncPresentationPanel :is(button:not(.window-icon-btn), input:not([type="range"]), select) {
        color: #f8fafc !important;
    }


    
html[data-theme="dark"] #asyncNotesPanel #asyncQuillEditor,
html[data-theme="dark"] #asyncNotesPanel .ql-container.ql-snow,
html[data-theme="dark"] #asyncNotesPanel .ql-editor {
        background: rgba(13, 148, 136, .10) !important;
        color: #ecfdf5 !important;
    }


    
html[data-theme="dark"] #asyncNotesPanel .ql-toolbar.ql-snow {
        background: rgba(13, 148, 136, .19) !important;
        border-color: rgba(94, 234, 212, .24) !important;
    }


    
html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-stroke {
        stroke: #99f6e4;
    }


    
html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-fill {
        fill: #99f6e4;
    }


    
html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-picker {
        color: #ccfbf1;
    }


    


    


    
html[data-theme="dark"] #asyncSoundPanel .sound-actions button {
        background: linear-gradient(135deg, rgba(217, 119, 6, .90), rgba(180, 83, 9, .78)) !important;
        color: #fff !important;
    }


    
html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button {
        background: rgba(225, 29, 72, .17) !important;
        border-color: rgba(253, 164, 175, .27) !important;
        color: #fff1f2 !important;
    }


    
html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button.active {
        background: rgba(225, 29, 72, .32) !important;
        border-color: rgba(253, 164, 175, .48) !important;
        color: #fff !important;
    }


    
/* The minimized Presentation Tools strip retains the same red glass tint. */

    
#asyncPresentationPanel.is-minimized {
        background: linear-gradient(135deg, rgba(225, 29, 72, .24), rgba(190, 18, 60, .16)) !important;
        border-color: rgba(225, 29, 72, .35) !important;
        backdrop-filter: blur(22px) saturate(170%) !important;
        -webkit-backdrop-filter: blur(22px) saturate(170%) !important;
        box-shadow: 0 12px 30px rgba(190, 18, 60, .16), inset 0 1px 0 rgba(255, 255, 255, .4) !important;
    }


    
html[data-theme="dark"] #asyncPresentationPanel.is-minimized {
        background: linear-gradient(135deg, rgba(159, 18, 57, .65), rgba(136, 19, 55, .46)) !important;
        border-color: rgba(253, 164, 175, .32) !important;
    }


    
/* Timer input fields and controls keep a matching blue glass tint. */

    
#asyncFloatingTimer .aft-inputs input,
#asyncFloatingTimer .aft-actions button,
#aftTickToggle {
        background: rgba(59, 130, 246, .22) !important;
        border: 1px solid rgba(219, 234, 254, .42) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .30),
            0 3px 9px rgba(30, 64, 175, .12);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }


    
#asyncFloatingTimer .aft-actions button:first-child {
        background: linear-gradient(135deg, rgba(59, 130, 246, .88), rgba(37, 99, 235, .72)) !important;
        color: #fff !important;
    }


    
html[data-theme="dark"] #asyncFloatingTimer .aft-inputs input,
html[data-theme="dark"] #asyncFloatingTimer .aft-actions button,
html[data-theme="dark"] #aftTickToggle {
        background: rgba(30, 64, 175, .38) !important;
        border-color: rgba(147, 197, 253, .30) !important;
        color: #fff !important;
    }


    
/* Same tool colours stay visible on hover without losing the glass surface. */

    
#readingToolsPanel :is(.rt-btn, .rt-select):hover {
        color: #1d4ed8 !important;
    }


    
#asyncNotesPanel .notes-toolbar button:hover {
        color: #0f766e !important;
    }


    


    
#asyncSoundPanel .sound-choice button.active {
        color: #92400e !important;
    }


    
#asyncPresentationPanel .presentation-actions button.active {
        color: #be123c !important;
    }


    
html[data-theme="dark"] #readingToolsPanel :is(.rt-btn, .rt-select):hover,
html[data-theme="dark"] #asyncNotesPanel .notes-toolbar button:hover,
html[data-theme="dark"] #asyncSoundPanel .sound-choice button.active,
html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button.active {
        color: #fff !important;
    }


    /* Glass icon controls: neutral, monochrome and aligned at the top-right. */
    .window-icon-controls {
        order: 0 !important;
        margin-left: auto !important;
        margin-right: 0 !important;
        gap: 6px !important;
        flex: 0 0 auto !important;
        align-items: center !important;
    }

    #readingToolsPanel .rt-window-actions {
        order: 0 !important;
        margin-left: auto !important;
    }

    .window-icon-controls .window-icon-btn,
    #readingToolsPanel .rt-window-actions .window-icon-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 29px !important;
        height: 29px !important;
        min-width: 29px !important;
        min-height: 29px !important;
        flex: 0 0 29px !important;
        padding: 0 !important;
        border: 1px solid rgba(255, 255, 255, .38) !important;
        border-radius: 10px !important;
        background: rgba(255, 255, 255, .17) !important;
        color: #fff !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.32), 0 3px 9px rgba(15,23,42,.10) !important;
        backdrop-filter: blur(12px) saturate(150%);
        -webkit-backdrop-filter: blur(12px) saturate(150%);
        font-size: 13px !important;
        line-height: 1 !important;
        cursor: pointer;
        transition: background .18s ease, transform .18s ease, box-shadow .18s ease;
    }

    .window-icon-controls .window-icon-btn i,
    #readingToolsPanel .rt-window-actions .window-icon-btn i {
        display: block;
        font-size: 12px;
        line-height: 1;
        color: inherit;
        pointer-events: none;
    }

    .window-icon-btn.window-close,
    .window-icon-btn.window-minimize,
    .window-icon-btn.window-maximize {
        background: rgba(255, 255, 255, .17) !important;
        border-color: rgba(255, 255, 255, .38) !important;
    }

    .window-icon-btn::after {
        display: none !important;
        content: none !important;
    }

    .window-icon-btn:hover {
        filter: none !important;
        background: rgba(255, 255, 255, .29) !important;
        transform: translateY(-1px) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.42), 0 5px 12px rgba(15,23,42,.14) !important;
    }

    #asyncTimerDrag>.aft-tick-toggle {
        margin-left: auto !important;
    }

    /* Keep the note-writing page white in both themes; the surrounding window
       and formatting toolbar continue to use the ASYNC glass/theme styling. */
    #asyncNotesPanel #asyncNoteTitle {
        background: #fff !important;
        color: #1f2937 !important;
        border-color: rgba(100,116,139,.28) !important;
    }

    #asyncNotesPanel #asyncQuillEditor,
    #asyncNotesPanel .ql-container.ql-snow,
    #asyncNotesPanel .ql-editor {
        background: #fff !important;
        color: #1f2937 !important;
    }

    #asyncNotesPanel .ql-editor {
        caret-color: #1f2937;
    }

    #asyncNotesPanel .ql-editor.ql-blank::before {
        color: #64748b !important;
        opacity: .78 !important;
    }

    /* Resize remains active on all eight hit areas, while only four tiny
       glass dots are visible to suggest corner resizing. */
    .async-tool-resize-handle {
        opacity: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .async-tool-resize-handle[data-resize="n"],
    .async-tool-resize-handle[data-resize="s"] {
        left: 18px;
        right: 18px;
        height: 10px;
        width: auto;
    }

    .async-tool-resize-handle[data-resize="e"],
    .async-tool-resize-handle[data-resize="w"] {
        top: 18px;
        bottom: 18px;
        width: 10px;
        height: auto;
    }

    .async-tool-resize-handle[data-resize="ne"],
    .async-tool-resize-handle[data-resize="nw"],
    .async-tool-resize-handle[data-resize="se"],
    .async-tool-resize-handle[data-resize="sw"] {
        display: block !important;
        width: 23px !important;
        height: 23px !important;
        opacity: 1 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .async-tool-resize-handle[data-resize="ne"] { top: 0; right: 0; }
    .async-tool-resize-handle[data-resize="nw"] { top: 0; left: 0; }
    .async-tool-resize-handle[data-resize="se"] { bottom: 0; right: 0; }
    .async-tool-resize-handle[data-resize="sw"] { bottom: 0; left: 0; }

    .async-tool-resize-handle[data-resize="ne"]::after,
    .async-tool-resize-handle[data-resize="nw"]::after,
    .async-tool-resize-handle[data-resize="se"]::after,
    .async-tool-resize-handle[data-resize="sw"]::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 3px;
        left: 3px;
        border-radius: 50%;
        background:
            radial-gradient(circle at 3px 3px, rgba(255,255,255,.98) 1.35px, transparent 1.8px),
            radial-gradient(circle at 8px 8px, rgba(255,255,255,.88) 1.35px, transparent 1.8px),
            radial-gradient(circle at 13px 13px, rgba(255,255,255,.75) 1.35px, transparent 1.8px);
        filter: drop-shadow(0 1px 1px rgba(15,23,42,.48));
        opacity: .76;
        transition: opacity .18s ease, transform .18s ease;
    }

    .async-tool-resize-handle[data-resize="ne"]::after { transform: rotate(90deg); }
    .async-tool-resize-handle[data-resize="se"]::after { transform: rotate(180deg); }
    .async-tool-resize-handle[data-resize="sw"]::after { transform: rotate(270deg); }

    .async-tool-resize-handle[data-resize="ne"]:hover::after,
    .async-tool-resize-handle[data-resize="nw"]:hover::after,
    .async-tool-resize-handle[data-resize="se"]:hover::after,
    .async-tool-resize-handle[data-resize="sw"]:hover::after {
        opacity: 1;
        transform: scale(1.08);
    }

    /* Presentation minimization becomes a floating icon-only glass dock. */
    #asyncPresentationPanel.is-minimized {
        position: fixed !important;
        left: auto !important;
        top: auto !important;
        right: 90px !important;
        bottom: 22px !important;
        transform: none !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        width: max-content !important;
        max-width: min(420px, calc(100vw - 20px)) !important;
        height: auto !important;
        max-height: none !important;
        padding: 8px !important;
        gap: 0 !important;
        border-radius: 17px !important;
        overflow: visible !important;
        z-index: 100130 !important;
    }

    #asyncPresentationPanel.is-minimized > .aux-head,
    #asyncPresentationPanel.is-minimized > :not(.presentation-actions) {
        display: none !important;
    }

    #asyncPresentationPanel.is-minimized .presentation-actions {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        gap: 5px !important;
    }

    #asyncPresentationPanel.is-minimized .presentation-actions button {
        display: inline-flex !important;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        min-height: 36px !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        border-radius: 11px !important;
        font-size: 0 !important;
        line-height: 0 !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 3px 9px rgba(15,23,42,.12) !important;
        backdrop-filter: blur(14px) saturate(160%);
        -webkit-backdrop-filter: blur(14px) saturate(160%);
    }

    #asyncPresentationPanel.is-minimized .presentation-actions button i {
        display: inline-block !important;
        font-size: 16px !important;
        line-height: 1 !important;
    }

    #asyncPresentationPanel.is-minimized .presentation-actions button.active {
        background: rgba(225,29,72,.25) !important;
        border-color: rgba(225,29,72,.55) !important;
        color: #9f1239 !important;
    }

    html[data-theme="dark"] #asyncPresentationPanel.is-minimized .presentation-actions button.active {
        color: #fff1f2 !important;
    }

    @media(max-width: 480px) {
        #asyncPresentationPanel.is-minimized {
            right: 70px !important;
            bottom: 10px !important;
            max-width: calc(100vw - 80px) !important;
            padding: 6px !important;
        }

        #asyncPresentationPanel.is-minimized .presentation-actions {
            gap: 4px !important;
        }

        #asyncPresentationPanel.is-minimized .presentation-actions button {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            min-height: 32px !important;
        }
    }

    /* Soft spring pop: tools scale in, briefly overshoot, then settle. */
    #readingToolsPanel.is-open,
    #asyncNotesPanel.is-open,
    #asyncSoundPanel.is-open,
    #asyncPresentationPanel.is-open,
    #asyncFloatingTimer.is-visible {
        animation: asyncToolSpring .58s cubic-bezier(.16, 1.28, .3, 1) both !important;
        transform-origin: center center;
    }

    @keyframes asyncToolSpring {
        0%   { opacity: 0; scale: .76; filter: blur(7px); }
        58%  { opacity: 1; scale: 1.045; filter: blur(0); }
        78%  { opacity: 1; scale: .985; }
        100% { opacity: 1; scale: 1; filter: blur(0); }
    }

    #asyncToolsMenu.is-visible button {
        animation: asyncMenuItemSpring .46s cubic-bezier(.65, 0, .35, 1) both;
    }

    #asyncToolsMenu.is-visible button:nth-child(1) { animation-delay: 0ms; }
    #asyncToolsMenu.is-visible button:nth-child(2) { animation-delay: 35ms; }
    #asyncToolsMenu.is-visible button:nth-child(3) { animation-delay: 70ms; }
    #asyncToolsMenu.is-visible button:nth-child(4) { animation-delay: 105ms; }
    #asyncToolsMenu.is-visible button:nth-child(5) { animation-delay: 140ms; }
    #asyncToolsMenu.is-visible button:nth-child(6) { animation-delay: 175ms; }

    @keyframes asyncMenuItemSpring {
        0% { opacity: 0; transform: translateY(14px) scale(.96); filter: blur(3px); }
        100% { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
    }

    /* Disable mobile long-press text callouts/selection on the article itself.
       Editable fields remain selectable and usable. */
    @media (pointer: coarse) {
        main.main-content > .content-wrapper,
        main.main-content > .content-wrapper *:not(input):not(textarea):not([contenteditable="true"]) {
            -webkit-touch-callout: none !important;
            -webkit-user-select: none !important;
            user-select: none !important;
            -webkit-tap-highlight-color: transparent;
        }

        main.main-content > .content-wrapper input,
        main.main-content > .content-wrapper textarea,
        main.main-content > .content-wrapper [contenteditable="true"] {
            -webkit-user-select: text !important;
            user-select: text !important;
        }
    }



    /* Neutral icon-only glass window controls. */
    .window-icon-controls {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        flex: 0 0 auto !important;
        gap: 6px !important;
        order: 2 !important;
        margin: 0 0 0 auto !important;
    }
    .window-icon-controls .window-icon-btn,
    #readingToolsPanel .rt-window-actions .window-icon-btn {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 30px !important;
        width: 30px !important;
        min-width: 30px !important;
        height: 30px !important;
        min-height: 30px !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 1px solid rgba(255,255,255,.34) !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,.16) !important;
        color: currentColor !important;
        font-size: 13px !important;
        line-height: 1 !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.28), 0 2px 8px rgba(15,23,42,.10) !important;
        backdrop-filter: blur(13px) saturate(150%);
        -webkit-backdrop-filter: blur(13px) saturate(150%);
        cursor: pointer !important;
        transition: transform .18s ease, background .18s ease, box-shadow .18s ease !important;
    }
    .window-icon-controls .window-icon-btn i,
    #readingToolsPanel .rt-window-actions .window-icon-btn i {
        display: block !important;
        font-size: 13px !important;
        line-height: 1 !important;
        pointer-events: none !important;
    }
    .window-icon-btn.window-close,
    .window-icon-btn.window-minimize,
    .window-icon-btn.window-maximize {
        background: rgba(255,255,255,.16) !important;
        border-color: rgba(255,255,255,.34) !important;
        color: inherit !important;
    }
    .window-icon-btn::after { content: none !important; display: none !important; }
    .window-icon-btn:hover {
        transform: translateY(-1px) !important;
        background: rgba(255,255,255,.28) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.4), 0 4px 11px rgba(15,23,42,.13) !important;
    }
    #asyncNotesPanel .aux-window-actions .window-icon-btn,
    #asyncSoundPanel .aux-window-actions .window-icon-btn,
    #asyncPresentationPanel .aux-window-actions .window-icon-btn,
    #asyncFloatingTimer .window-icon-controls .window-icon-btn,
    #readingToolsPanel .rt-window-actions .window-icon-btn {
        flex: 0 0 29px !important;
        width: 29px !important;
        min-width: 29px !important;
        height: 29px !important;
        min-height: 29px !important;
        padding: 0 !important;
        border: 1px solid rgba(255,255,255,.38) !important;
        border-radius: 10px !important;
        background: rgba(255,255,255,.17) !important;
        color: #fff !important;
        font-size: 13px !important;
        line-height: 1 !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.32), 0 3px 9px rgba(15,23,42,.10) !important;
    }
    #asyncFloatingTimer .window-icon-controls .window-icon-btn {
        flex-basis: 25px !important;
        width: 25px !important;
        min-width: 25px !important;
        height: 25px !important;
        min-height: 25px !important;
        border-radius: 8px !important;
    }
    #asyncNotesPanel .aux-window-actions .window-icon-btn i,
    #asyncSoundPanel .aux-window-actions .window-icon-btn i,
    #asyncPresentationPanel .aux-window-actions .window-icon-btn i,
    #asyncFloatingTimer .window-icon-controls .window-icon-btn i {
        display: block !important;
        color: inherit !important;
        font-size: 13px !important;
        line-height: 1 !important;
    }
    #asyncNotesPanel .aux-window-actions .window-icon-btn::after,
    #asyncSoundPanel .aux-window-actions .window-icon-btn::after,
    #asyncPresentationPanel .aux-window-actions .window-icon-btn::after { content: none !important; display: none !important; }
    .tool-window-header,
    #asyncTimerDrag {
        display: flex !important;
        align-items: center !important;
        min-width: 0 !important;
    }
    .tool-window-header > strong { flex: 0 1 auto !important; min-width: 0; }
    .tool-window-header > .aux-drag-hint { margin-left: auto !important; }
    #readingToolsPanel .rt-window-actions { order: 2 !important; margin-left: auto !important; }

    /* Timer display scales to its own resized window instead of the viewport. */
    #asyncFloatingTimer {
        container-type: inline-size;
        min-width: 220px !important;
        max-width: calc(100vw - 12px) !important;
        box-sizing: border-box !important;
    }
    #asyncTimerDrag {
        gap: 5px !important;
        width: 100%;
        min-width: 0 !important;
        overflow: hidden;
    }
    #asyncTimerDrag > strong {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        max-width: 94px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 11px !important;
    }
    #aftTickToggle {
        flex: 0 0 27px !important;
        width: 27px !important;
        min-width: 27px !important;
        margin: 0 !important;
    }
    #asyncFloatingTimer .window-icon-controls {
        flex: 0 0 auto !important;
        gap: 3px !important;
    }
    #asyncFloatingTimer .window-icon-controls .window-icon-btn {
        flex-basis: 25px !important;
        width: 25px !important;
        min-width: 25px !important;
        height: 25px !important;
        min-height: 25px !important;
        border-radius: 8px !important;
    }
    #asyncFloatingTimer .aft-display {
        display: block;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: clip;
        font-size: clamp(20px, 7vw, 38px) !important;
        font-size: clamp(18px, 17cqi, 38px) !important;
        letter-spacing: .025em;
        line-height: 1.18;
    }
    #aftStatus {
        max-width: 100%;
        overflow-wrap: anywhere;
        white-space: normal;
    }
    #asyncFloatingTimer .aft-inputs,
    #asyncFloatingTimer .aft-actions { min-width: 0; }
    #asyncFloatingTimer .aft-inputs label { flex: 1 1 60px; min-width: 0; }
    #asyncFloatingTimer .aft-inputs input,
    #asyncFloatingTimer .aft-actions button { min-width: 0; max-width: 100%; box-sizing: border-box; }

    /* Notes use a neutral theme-aware surface; the Quill toolbar stays above text. */
    #asyncNotesPanel {
        --notes-surface: #ffffff;
        --notes-editor-text: #1f2937;
        --notes-toolbar-surface: rgba(59,130,246,.075);
        --notes-panel-surface: rgba(255,255,255,.94);
        --notes-panel-text: #172033;
        --notes-border: rgba(100,116,139,.25);
        background: var(--notes-panel-surface) !important;
        color: var(--notes-panel-text) !important;
        border-color: var(--notes-border) !important;
        overflow: visible !important;
    }
    html[data-theme="dark"] #asyncNotesPanel {
        --notes-surface: #111827;
        --notes-editor-text: #e5e7eb;
        --notes-toolbar-surface: rgba(59,130,246,.13);
        --notes-panel-surface: rgba(17,24,39,.96);
        --notes-panel-text: #e5e7eb;
        --notes-border: rgba(148,163,184,.28);
        background: var(--notes-panel-surface) !important;
        color: var(--notes-panel-text) !important;
        border-color: var(--notes-border) !important;
    }
    #asyncNotesPanel .aux-head {
        position: relative !important;
        z-index: 30 !important;
        flex: 0 0 auto !important;
        background: linear-gradient(112deg, rgba(59,130,246,.88), rgba(37,99,235,.74)) !important;
        border-color: rgba(255,255,255,.38) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.36), 0 5px 16px rgba(37,99,235,.13) !important;
    }
    #asyncNotesPanel .notes-toolbar,
    #asyncNotesPanel .ql-toolbar.ql-snow {
        position: relative !important;
        z-index: 25 !important;
        flex: 0 0 auto !important;
        background: var(--notes-toolbar-surface) !important;
        border-color: var(--notes-border) !important;
    }
    #asyncNotesPanel .ql-picker-options { z-index: 100150 !important; }
    #asyncNotesPanel #asyncNoteTitle {
        position: relative !important;
        z-index: 5 !important;
        flex: 0 0 auto !important;
        background: var(--notes-surface) !important;
        color: var(--notes-editor-text) !important;
        border-color: var(--notes-border) !important;
    }
    #asyncNotesPanel #asyncQuillEditor {
        position: relative !important;
        z-index: 1 !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
        overflow: auto !important;
        background: var(--notes-surface) !important;
        color: var(--notes-editor-text) !important;
    }
    #asyncNotesPanel .ql-container.ql-snow,
    #asyncNotesPanel .ql-editor {
        position: relative !important;
        z-index: 1 !important;
        background: var(--notes-surface) !important;
        color: var(--notes-editor-text) !important;
        border-color: var(--notes-border) !important;
    }
    #asyncNotesPanel .ql-editor { caret-color: var(--notes-editor-text) !important; }
    #asyncNotesPanel .ql-editor.ql-blank::before { color: var(--notes-editor-text) !important; opacity: .58 !important; }
    #asyncNotesPanel .ql-snow .ql-stroke { stroke: var(--notes-panel-text) !important; }
    #asyncNotesPanel .ql-snow .ql-fill { fill: var(--notes-panel-text) !important; }
    #asyncNotesPanel .ql-snow .ql-picker { color: var(--notes-panel-text) !important; }
    /* Override the older higher-specificity teal rules for the dark theme too. */
    html[data-theme="dark"] #asyncNotesPanel #asyncQuillEditor,
    html[data-theme="dark"] #asyncNotesPanel .ql-container.ql-snow,
    html[data-theme="dark"] #asyncNotesPanel .ql-editor {
        background: var(--notes-surface) !important;
        color: var(--notes-editor-text) !important;
        border-color: var(--notes-border) !important;
    }
    html[data-theme="dark"] #asyncNotesPanel .ql-toolbar.ql-snow {
        background: var(--notes-toolbar-surface) !important;
        border-color: var(--notes-border) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.10) !important;
    }
    html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-stroke { stroke: var(--notes-panel-text) !important; }
    html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-fill { fill: var(--notes-panel-text) !important; }
    html[data-theme="dark"] #asyncNotesPanel .ql-snow .ql-picker { color: var(--notes-panel-text) !important; }
    #asyncNotesPanel .notes-toolbar button:hover {
        color: #1d4ed8 !important;
        background: rgba(59,130,246,.12) !important;
        border-color: rgba(59,130,246,.34) !important;
    }
    html[data-theme="dark"] #asyncNotesPanel .notes-toolbar button:hover {
        color: #dbeafe !important;
        background: rgba(59,130,246,.22) !important;
    }
    #asyncNotesPanel .notes-footer {
        position: relative !important;
        z-index: 10 !important;
        flex: 0 0 auto !important;
        background: var(--notes-panel-surface) !important;
        border-color: var(--notes-border) !important;
        color: var(--notes-panel-text) !important;
    }
    html[data-theme="dark"] #asyncNotesPanel .notes-toolbar button,
    html[data-theme="dark"] #asyncNotesPanel #asyncNoteTitle {
        color: var(--notes-editor-text) !important;
    }

    /* Presentation toolbar: shortcut-only horizontal blue glass dock. */
    #asyncPresentationPanel {
        display: flex !important;
        position: fixed !important;
        left: 50% !important;
        right: auto !important;
        top: auto !important;
        bottom: 16px !important;
        z-index: 100160 !important;
        width: max-content !important;
        max-width: calc(100vw - 20px) !important;
        height: auto !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 8px !important;
        gap: 0 !important;
        overflow: visible !important;
        border: 1px solid rgba(96,165,250,.48) !important;
        border-radius: 20px !important;
        background: linear-gradient(135deg, rgba(219,234,254,.88), rgba(147,197,253,.76)) !important;
        color: #1e3a8a !important;
        box-shadow: 0 18px 48px rgba(37,99,235,.22), inset 0 1px 0 rgba(255,255,255,.72) !important;
        backdrop-filter: blur(26px) saturate(185%) !important;
        -webkit-backdrop-filter: blur(26px) saturate(185%) !important;
        transform: translate(-50%, calc(100% + 42px)) !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        transition: transform .62s cubic-bezier(.16,1.24,.3,1), opacity .28s ease, visibility 0s linear .62s !important;
    }
    #asyncPresentationPanel.is-toolbar-visible {
        transform: translate(-50%, 0) !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        transition: transform .62s cubic-bezier(.16,1.24,.3,1), opacity .28s ease, visibility 0s linear 0s !important;
    }
    html[data-theme="dark"] #asyncPresentationPanel {
        border-color: rgba(147,197,253,.34) !important;
        background: linear-gradient(135deg, rgba(30,64,175,.88), rgba(30,58,138,.84)) !important;
        color: #eff6ff !important;
        box-shadow: 0 18px 48px rgba(2,6,23,.42), inset 0 1px 0 rgba(191,219,254,.2) !important;
    }
    #asyncPresentationPanel > .aux-head,
    #asyncPresentationPanel > p,
    #asyncPresentationPanel > .sound-volume,
    #asyncPresentationPanel > #asyncPresentationStatus,
    #asyncPresentationPanel > .async-tool-resize-handle { display: none !important; }
    #asyncPresentationPanel .presentation-actions {
        display: flex !important;
        flex: 0 1 auto !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 5px !important;
        width: max-content !important;
        max-width: calc(100vw - 38px) !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        scrollbar-width: none;
    }
    #asyncPresentationPanel .presentation-actions::-webkit-scrollbar { display: none; }
    #asyncPresentationPanel .presentation-actions button {
        display: inline-flex !important;
        flex: 0 0 42px !important;
        align-items: center !important;
        justify-content: center !important;
        width: 42px !important;
        min-width: 42px !important;
        height: 42px !important;
        min-height: 42px !important;
        padding: 0 !important;
        border: 1px solid rgba(59,130,246,.22) !important;
        border-radius: 13px !important;
        background: rgba(255,255,255,.43) !important;
        color: #1d4ed8 !important;
        font-size: 0 !important;
        line-height: 0 !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.68), 0 3px 10px rgba(30,64,175,.10) !important;
        backdrop-filter: blur(12px) saturate(160%);
        -webkit-backdrop-filter: blur(12px) saturate(160%);
        cursor: pointer !important;
        transition: background .18s ease, border-color .18s ease, transform .18s ease !important;
    }
    #asyncPresentationPanel .presentation-actions button i {
        display: inline-block !important;
        font-size: 17px !important;
        line-height: 1 !important;
        pointer-events: none !important;
    }
    #asyncPresentationPanel .presentation-actions button:hover {
        transform: translateY(-2px) !important;
        background: rgba(255,255,255,.72) !important;
    }
    #asyncPresentationPanel .presentation-actions button.active {
        background: rgba(37,99,235,.24) !important;
        border-color: rgba(37,99,235,.66) !important;
        color: #1e40af !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.56), 0 0 0 2px rgba(59,130,246,.14) !important;
    }
    html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button {
        border-color: rgba(191,219,254,.22) !important;
        background: rgba(15,23,42,.34) !important;
        color: #dbeafe !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.13), 0 3px 10px rgba(2,6,23,.24) !important;
    }
    html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button:hover {
        background: rgba(96,165,250,.26) !important;
    }
    html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button:not(.window-icon-btn) {
        background: rgba(15,23,42,.34) !important;
        border-color: rgba(191,219,254,.22) !important;
        color: #dbeafe !important;
    }
    html[data-theme="dark"] #asyncPresentationPanel .presentation-actions button.active:not(.window-icon-btn) {
        background: rgba(96,165,250,.34) !important;
        border-color: rgba(147,197,253,.72) !important;
        color: #fff !important;
    }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button {
        animation: asyncPresentIconRise .48s cubic-bezier(.16,1.25,.3,1) both;
    }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(1) { animation-delay: 0ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(2) { animation-delay: 25ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(3) { animation-delay: 50ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(4) { animation-delay: 75ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(5) { animation-delay: 100ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(6) { animation-delay: 125ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(7) { animation-delay: 150ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(8) { animation-delay: 175ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(9) { animation-delay: 200ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(10) { animation-delay: 225ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(11) { animation-delay: 250ms; }
    #asyncPresentationPanel.is-toolbar-visible .presentation-actions button:nth-child(12) { animation-delay: 275ms; }
    @keyframes asyncPresentIconRise {
        0% { opacity: 0; transform: translateY(22px) scale(.72); filter: blur(4px); }
        72% { opacity: 1; transform: translateY(-3px) scale(1.06); filter: blur(0); }
        100% { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
    }
    #asyncPresentationCursor {
        background: #2563eb !important;
        box-shadow: 0 0 0 5px rgba(37,99,235,.20), 0 0 18px rgba(37,99,235,.82) !important;
    }
    @media (max-width: 600px) {
        #asyncPresentationPanel { bottom: 10px !important; padding: 6px !important; border-radius: 16px !important; }
        #asyncPresentationPanel .presentation-actions { gap: 4px !important; max-width: calc(100vw - 32px) !important; }
        #asyncPresentationPanel .presentation-actions button {
            flex-basis: 37px !important; width: 37px !important; min-width: 37px !important;
            height: 37px !important; min-height: 37px !important; border-radius: 11px !important;
        }
        #asyncPresentationPanel .presentation-actions button i { font-size: 15px !important; }
    }


/* v18 refinements */
#asyncFloatingTimer .async-tool-resize-handle { display:none !important; }
#asyncNotesPanel .aux-window-actions .window-minimize,
#asyncNotesPanel .aux-window-actions .window-maximize,
#asyncFloatingTimer .window-maximize,
#asyncSoundPanel .window-maximize,
#asyncPresentationPanel .window-maximize,
#readingToolsPanel .window-maximize { display:none !important; }
#asyncNotesPanel .aux-window-actions { margin-left:auto !important; }

/* Two small glass dots at each corner; resize hit areas remain functional. */
.async-tool-resize-handle[data-resize="ne"]::after,
.async-tool-resize-handle[data-resize="nw"]::after,
.async-tool-resize-handle[data-resize="se"]::after,
.async-tool-resize-handle[data-resize="sw"]::after {
    width:6px !important;height:6px !important;top:8px !important;left:8px !important;
    border-radius:50% !important;
    background:radial-gradient(circle at 25% 50%,rgba(255,255,255,.98) 0 1.5px,transparent 1.8px),
               radial-gradient(circle at 75% 50%,rgba(147,197,253,.95) 0 1.5px,transparent 1.8px) !important;
    box-shadow:0 1px 3px rgba(15,23,42,.22) !important;opacity:.72 !important;
}
.async-tool-resize-handle[data-resize="ne"]:hover::after,
.async-tool-resize-handle[data-resize="nw"]:hover::after,
.async-tool-resize-handle[data-resize="se"]:hover::after,
.async-tool-resize-handle[data-resize="sw"]:hover::after { opacity:1 !important; }

.presentation-settings-wrap { position:relative;display:flex;justify-content:flex-end; }
.presentation-settings-toggle {
    display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;padding:0;
    border:1px solid var(--aux-border);border-radius:11px;
    background:var(--aux-control);color:var(--aux-text);cursor:pointer;
    backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
}
.presentation-settings {
    position:absolute;right:0;bottom:calc(100% + 10px);width:min(270px,calc(100vw - 30px));
    display:grid;gap:9px;margin:0;padding:11px;z-index:100150;
    border:1px solid var(--aux-border);border-radius:12px;
    background:var(--aux-glass,rgba(255,255,255,.9));
    box-shadow:0 10px 28px rgba(15,23,42,.15);
}
.presentation-settings[hidden] { display:none !important; }
.presentation-settings .sound-volume { display:flex;align-items:center;gap:9px; }
.presentation-settings input[type="color"] { width:38px;height:30px;padding:2px;border:1px solid var(--aux-border);border-radius:7px;background:var(--aux-control); }

@media print {
    @page { margin:14mm; }
    html,body { background:#fff !important;color:#111 !important; }
    body * { visibility:hidden !important; }
    main.main-content,
    main.main-content > .content-wrapper,
    main.main-content > .content-wrapper * { visibility:visible !important; }
    .sidebar,#sidebar,.sidebar-container,.main-sidebar,aside.sidebar,
    .topbar,#topbar,.top-bar,header.topbar,.navbar,
    #asyncToolsDock,#readingToolsFab,#readingToolsPanel,#asyncNotesPanel,
    #asyncSoundPanel,#asyncPresentationPanel,#asyncPresentationOverlay,
    #asyncFloatingTimer,#aftAlarmOverlay,#readingToolsRoot { display:none !important;visibility:hidden !important; }
    main.main-content {
        position:absolute !important;left:0 !important;top:0 !important;width:100% !important;
        max-width:none !important;margin:0 !important;padding:0 !important;overflow:visible !important;
    }
    main.main-content > .content-wrapper {
        display:block !important;position:static !important;width:100% !important;max-width:none !important;
        margin:0 !important;padding:0 !important;box-shadow:none !important;
    }
    main.main-content > .content-wrapper * {
        -webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;
    }
    a { color:inherit !important;text-decoration:none !important; }
    img,svg,table,pre,blockquote { break-inside:avoid; }
}

#asyncPresentationPanel.is-minimized > :not(.presentation-actions):not(.presentation-settings-wrap) {
    display:none !important;
}
#asyncPresentationPanel.is-minimized .presentation-settings-wrap {
    display:flex !important;align-items:center;margin-left:5px;
}
#asyncPresentationPanel.is-minimized .presentation-settings-toggle {
    width:36px;height:36px;min-width:36px;padding:0;justify-content:center;
    font-size:0;border-radius:11px;
}
#asyncPresentationPanel.is-minimized .presentation-settings-toggle i { font-size:16px; }
#asyncPresentationPanel.is-minimized .presentation-settings {
    position:absolute;right:0;bottom:calc(100% + 10px);width:min(260px,calc(100vw - 30px));
    z-index:100150;
}
</style>

<div id="readingToolsRoot">
    <div id="asyncToolsDock">
        <div id="asyncToolsMenu" aria-hidden="true">
            <button type="button" data-open-tool="readingToolsPanel"><i class="bi bi-book-half"></i><span>Reading tools</span></button>
            <button type="button" data-open-tool="asyncNotesPanel"><i class="bi bi-journal-text"></i><span>Study notes</span></button>
            <button type="button" data-open-tool="asyncSoundPanel"><i class="bi bi-headphones"></i><span>Ambient sounds</span></button>
            <button type="button" id="asyncDockTimer"><i class="bi bi-stopwatch"></i><span>Timer</span></button>
        </div>
        <button type="button" id="asyncToolsFab" aria-label="Show study tools" aria-expanded="false"><i class="bi bi-grid-3x3-gap-fill"></i></button>
    </div>
    <section id="readingToolsPanel" aria-label="Reading tools">
        <div class="rt-header" id="rtDrag">
            <strong><i class="bi bi-sliders"></i> Reading Tools</strong>
            <span class="rt-window-actions window-icon-controls">
                <button type="button" class="window-icon-btn window-close" id="rtClose" title="Close Reading Tools" aria-label="Close Reading Tools"><i class="bi bi-x-lg"></i></button>
            </span>
        </div>

        <div class="rt-group">
            <span class="rt-label">Browser fullscreen</span>
            <button type="button" class="rt-btn" id="rtFullscreenToggle" style="width:100%;padding:11px;border-color:#4169e1;color:#4169e1">
                <i class="bi bi-arrows-fullscreen"></i> Enter Fullscreen
            </button>
            <small class="rt-muted" id="rtFullscreenHint">Fullscreen hides the browser tabs and address bar, similar to F11.</small>
        </div>

        <div class="rt-group">
            <span class="rt-label">Export page</span>
            <button type="button" class="rt-btn" id="rtExportPdf" style="width:100%;padding:11px">
                <i class="bi bi-file-earmark-pdf"></i> Export page as PDF
            </button>
            <small class="rt-muted">Exports only the academic page, without the website sidebar or topbar, while retaining your reading preferences.</small>
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
        <div class="rt-button-row">
            <button type="button" class="rt-btn" id="rtResetAll">Reset all settings</button>
        </div>
        <small class="rt-muted">Preferences are saved in this browser on this device.</small>
    </section>
</div>

<div id="asyncFloatingTimer" role="dialog" aria-label="Floating countdown timer" aria-hidden="true">
    <div id="asyncTimerDrag" class="aft-header">
        <strong><i class="bi bi-stopwatch"></i> ASYNC Timer</strong>
        <button type="button" id="aftTickToggle" class="aft-tick-toggle" aria-pressed="true" title="Ticking sound on" aria-label="Turn ticking sound off"><i class="bi bi-volume-up-fill"></i></button>
        <span class="aft-window-controls window-icon-controls">
            <button type="button" class="window-icon-btn window-close" id="aftClose" aria-label="Close timer" title="Close timer"><i class="bi bi-x-lg"></i></button>
            <button type="button" class="window-icon-btn window-minimize" id="aftMinimize" aria-label="Minimize timer" title="Minimize timer"><i class="bi bi-dash-lg"></i></button>
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
    <div class="aux-head tool-window-header" id="asyncNotesDrag">
        <strong><i class="bi bi-grip-horizontal"></i> My Study Notes</strong>
        <span class="aux-drag-hint">Drag to move</span>
        <span class="aux-window-actions window-icon-controls">
            <button type="button" class="window-icon-btn window-close" id="asyncNotesClose" aria-label="Close My Study Notes" title="Close"><i class="bi bi-x-lg"></i></button>
            <button type="button" class="window-icon-btn window-minimize" id="asyncNotesMinimize" aria-label="Minimize My Study Notes" title="Minimize"><i class="bi bi-dash-lg"></i></button>
        </span>
    </div>
    <div class="notes-toolbar">
        <select id="asyncNotesList" aria-label="Saved notes"></select>
        <button type="button" id="asyncNoteNew"><i class="bi bi-plus-lg"></i> New</button>
        <button type="button" id="asyncNoteDelete" title="Delete note"><i class="bi bi-trash"></i></button>
    </div>
    <input id="asyncNoteTitle" type="text" maxlength="120" placeholder="Note title">
    <div id="asyncQuillEditor"></div>
    <div class="notes-footer"><span id="asyncNotesSaveStatus">Saved in this browser</span><span>Private to this device/browser</span></div>
</section>

<section id="asyncSoundPanel" aria-label="Ambient sound player">
    <div class="aux-head tool-window-header" id="asyncSoundDrag">
        <strong><i class="bi bi-headphones"></i> Reading Ambience</strong>
        <span class="aux-drag-hint">Drag to move</span>
        <span class="aux-window-actions window-icon-controls">
            <button type="button" class="window-icon-btn window-close" id="asyncSoundClose" aria-label="Close Reading Ambience" title="Close"><i class="bi bi-x-lg"></i></button>
            <button type="button" class="window-icon-btn window-minimize" id="asyncSoundMinimize" aria-label="Minimize Reading Ambience" title="Minimize"><i class="bi bi-dash-lg"></i></button>
        </span>
    </div>
    <div style="font-size:12px;color:var(--aux-text);opacity:.75">Choose a continuous background sound while you read.</div>
    <div class="supplemental-heading">Audio library <small>Upload MP3, OGG, or WAV files to assets/audio/ambient/ on your hosting account.</small></div>
    <div style="display:flex;gap:7px;align-items:center">
        <select id="asyncAudioLibrarySelect" style="flex:1;min-width:0;padding:9px;border:1px solid var(--aux-border);border-radius:9px;background:var(--aux-control);color:var(--aux-text)">
            <option value="">No audio files found — add files to assets/audio/ambient/</option>
            <?php
            $asyncAudioDir = __DIR__ . '/assets/audio/ambient/';
            $asyncAudioFiles = [];
            if (is_dir($asyncAudioDir)) {
                foreach (glob($asyncAudioDir . '*.{mp3,ogg,wav,m4a}', GLOB_BRACE) ?: [] as $asyncAudioFile) {
                    if (is_file($asyncAudioFile)) $asyncAudioFiles[] = basename($asyncAudioFile);
                }
            }
            foreach ($asyncAudioFiles as $asyncAudioFile) {
                $asyncAudioUrl = '/assets/audio/ambient/' . rawurlencode($asyncAudioFile);
                echo '<option value="' . htmlspecialchars($asyncAudioUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars(pathinfo($asyncAudioFile, PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8') . '</option>';
            }
            ?>
        </select>
        <button type="button" id="asyncLibraryPlay" style="padding:9px 12px;border:0;border-radius:9px;background:#2563eb;color:white">Play</button>
        <button type="button" id="asyncLibraryStop" style="padding:9px 12px;border:0;border-radius:9px;background:#64748b;color:white">Stop</button>
    </div>
    <audio id="asyncLibraryAudio" preload="none" loop></audio>
    <div class="sound-choice" aria-label="Choose ambience">
        <button type="button" data-sound="ocean" title="Ocean waves">🌊 Ocean</button>
        <button type="button" data-sound="rain" title="Rain">🌧️ Rain</button>
        <button type="button" data-sound="white" title="White noise">〰️ White noise</button>
        <button type="button" data-sound="fire" title="Fireplace">🔥 Fireplace</button>
        <button type="button" data-sound="forest" title="Forest ambience">🌲 Forest</button>
    </div>
    <label class="sound-volume"><span>Volume</span><input id="asyncSoundVolume" type="range" min="0" max="100" value="28"><span id="asyncSoundVolumeValue">28%</span></label>
    <div class="sound-actions"><button type="button" id="asyncSoundPlay"><i class="bi bi-play-fill"></i> Play sound</button><button type="button" id="asyncSoundStop" style="background:#64748b">Stop all</button></div>
    <div id="asyncSoundStatus" aria-live="polite">Select a sound, then press Play. Audio starts only after your action.</div>
</section>

<section id="asyncPresentationPanel" class="async-extra-panel" aria-label="Presentation tools">
    <div class="aux-head tool-window-header" id="asyncPresentationDrag">
        <strong><i class="bi bi-easel2"></i> Presentation Tools</strong>
        <span class="aux-drag-hint">Drag to move</span>
        <span class="aux-window-actions window-icon-controls">
            <button type="button" class="window-icon-btn window-close" id="asyncPresentationClose" aria-label="Close Presentation Tools" title="Close"><i class="bi bi-x-lg"></i></button>
            <button type="button" class="window-icon-btn window-minimize" id="asyncPresentationMinimize" aria-label="Minimize Presentation Tools" title="Minimize"><i class="bi bi-dash-lg"></i></button>
        </span>
    </div>
    <p style="font-size:12px;color:var(--aux-text)">Use on slides, lessons, or any page. Presentation effects appear over the whole screen.</p>
    <div class="presentation-actions">
        <button type="button" data-present-mode="laser" title="Laser pointer"><i class="bi bi-record-circle"></i> Laser</button>
        <button type="button" data-present-mode="pointer" title="Pointer ring"><i class="bi bi-cursor-fill"></i> Pointer</button>
        <button type="button" data-present-mode="spotlight" title="Spotlight"><i class="bi bi-brightness-high"></i> Spotlight</button>
        <button type="button" data-present-mode="draw" title="Freehand pen"><i class="bi bi-pencil"></i> Pen</button>
        <button type="button" data-present-mode="arrow" title="Draw arrow"><i class="bi bi-arrow-up-right"></i> Arrow</button>
        <button type="button" data-present-mode="rectangle" title="Draw rectangle"><i class="bi bi-square"></i> Rectangle</button>
        <button type="button" data-present-mode="circle" title="Draw circle"><i class="bi bi-circle"></i> Circle</button>
        <button type="button" data-present-mode="erase" title="Eraser"><i class="bi bi-eraser"></i> Eraser</button>
        <button type="button" data-present-mode="magnifier" title="Magnifier"><i class="bi bi-search"></i> Magnifier</button>
        <button type="button" data-present-mode="crosshair" title="Crosshair"><i class="bi bi-crosshair"></i> Crosshair</button>
        <button type="button" data-present-mode="ruler" title="Ruler"><i class="bi bi-rulers"></i> Ruler</button>
        <button type="button" id="asyncPresentationClear" title="Clear annotations"><i class="bi bi-trash3"></i> Clear</button>
        <button type="button" id="asyncPresentationExit" title="Exit presentation mode"><i class="bi bi-x-circle"></i> Exit</button>
    </div>
    <div class="presentation-settings-wrap">
        <button type="button" id="asyncPresentationSettingsToggle" class="presentation-settings-toggle" aria-expanded="false" aria-controls="asyncPresentationSettings">
            <i class="bi bi-gear-fill"></i><span class="visually-hidden">Tool settings</span>
        </button>
        <div id="asyncPresentationSettings" class="presentation-settings" hidden>
            <label class="sound-volume"><span>Tool size</span><input id="asyncPresentationSize" type="range" min="6" max="48" value="14"><span id="asyncPresentationSizeValue">14px</span></label>
            <label class="sound-volume"><span>Tool color</span><input id="asyncPresentationColor" type="color" value="#2563eb"></label>
        </div>
    </div>
    <div id="asyncPresentationStatus" style="font-size:11px;color:var(--aux-text)">Choose Laser, Pointer, Spotlight, or Draw to begin.</div>
</section>
<div id="asyncPresentationOverlay" aria-hidden="true">
    <div id="asyncPresentationSpotlight"></div>
    <div id="asyncPresentationCursor"></div>
    <div id="asyncPresentationCrosshair"><span></span><span></span></div>
    <div id="asyncPresentationMagnifier" aria-hidden="true"><div id="asyncPresentationMagnifierContent"></div></div>
    <canvas id="asyncPresentationCanvas"></canvas>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
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
        const exportPdfButton = document.getElementById('rtExportPdf');
        if (exportPdfButton) exportPdfButton.addEventListener('click', () => window.print());
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
        let tickSoundEnabled = true;
        try {
            tickSoundEnabled = localStorage.getItem('asyncTimerTickSound') !== 'off';
        } catch (_) {}
        const tickToggle = document.getElementById('aftTickToggle');

        function renderTickToggle() {
            if (!tickToggle) return;
            tickToggle.setAttribute('aria-pressed', String(tickSoundEnabled));
            tickToggle.setAttribute('aria-label', tickSoundEnabled ? 'Turn ticking sound off' : 'Turn ticking sound on');
            tickToggle.title = tickSoundEnabled ? 'Ticking sound on' : 'Ticking sound off';
            tickToggle.innerHTML = tickSoundEnabled ?
                '<i class="bi bi-volume-up-fill"></i>' :
                '<i class="bi bi-volume-mute-fill"></i>';
        }
        if (tickToggle) tickToggle.addEventListener('click', () => {
            tickSoundEnabled = !tickSoundEnabled;
            try {
                localStorage.setItem('asyncTimerTickSound', tickSoundEnabled ? 'on' : 'off');
            } catch (_) {}
            renderTickToggle();
        });
        renderTickToggle();

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
            if (!tickSoundEnabled) return;
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
        document.getElementById('aftMinimize').addEventListener('click', () => {
            const minimized = timerBox.classList.toggle('is-minimized');
            const button = document.getElementById('aftMinimize');
            button.setAttribute('aria-label', minimized ? 'Restore timer' : 'Minimize timer');
            button.title = minimized ? 'Restore timer' : 'Minimize timer';
            if (timerBox.classList.contains('is-maximized') && window.asyncRestoreMaximized) window.asyncRestoreMaximized(timerBox);
        });
        document.getElementById('aftClose').addEventListener('click', () => {
            if (alarmActive) return;
            if (timerBox.classList.contains('is-maximized') && window.asyncRestoreMaximized) window.asyncRestoreMaximized(timerBox);
            timerBox.classList.remove('is-visible', 'is-minimized', 'is-maximized');
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
            prefs.scale = Math.min(1.9, +(prefs.scale + .1).toFixed(2));
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
        const notesPanel = byId('asyncNotesPanel');
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
        const presentationPanel = byId('asyncPresentationPanel');
        const allToolPanels = [readingPanel, notesPanel, soundPanel, presentationPanel].filter(Boolean);

        function setPanelClosed(item) {
            if (!item) return;
            if (item.classList.contains('is-maximized')) restoreMaximized(item);
            item.classList.remove('is-open', 'is-minimized', 'is-maximized', 'is-toolbar-visible');
            if (item.id === 'asyncPresentationPanel') item.setAttribute('aria-hidden', 'true');
            const minimizeId = {
                asyncNotesPanel: 'asyncNotesMinimize',
                asyncSoundPanel: 'asyncSoundMinimize',
                asyncPresentationPanel: 'asyncPresentationMinimize'
            } [item.id];
            const minimizeButton = minimizeId && byId(minimizeId);
            if (minimizeButton) {
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
        if (presentationPanel) {
            presentationPanel.classList.remove('is-toolbar-visible');
            presentationPanel.setAttribute('aria-hidden', 'true');
        }

        function openPanel(target) {
            if (!target) return;
            if (target.classList.contains('is-open') && target.classList.contains('is-minimized')) {
                target.classList.remove('is-minimized');
                if (target.id === 'asyncPresentationPanel') {
                    let saved = null;
                    try {
                        saved = JSON.parse(target.dataset.preMinGeometry || 'null');
                    } catch (_) {}
                    if (saved) {
                        target.style.setProperty('left', Math.max(6, Math.min(window.innerWidth - saved.width - 6, saved.left)) + 'px', 'important');
                        target.style.setProperty('top', Math.max(6, Math.min(window.innerHeight - saved.height - 6, saved.top)) + 'px', 'important');
                        target.style.setProperty('right', 'auto', 'important');
                        target.style.setProperty('bottom', 'auto', 'important');
                        target.style.setProperty('transform', 'none', 'important');
                        target.style.setProperty('width', saved.width + 'px', 'important');
                        target.style.setProperty('height', saved.height + 'px', 'important');
                        target.style.setProperty('max-width', 'none', 'important');
                        target.style.setProperty('max-height', 'none', 'important');
                        target.dataset.userPositioned = '1';
                    }
                    delete target.dataset.preMinGeometry;
                    const restoreButton = byId('asyncPresentationMinimize');
                    if (restoreButton) {
                        restoreButton.title = 'Minimize';
                        restoreButton.setAttribute('aria-label', 'Minimize Presentation Tools');
                    }
                }
                return;
            }
            const shouldOpen = !target.classList.contains('is-open');
            if (shouldOpen) closeOtherTools(target);
            target.classList.toggle('is-open', shouldOpen);
            target.classList.remove('is-minimized');
            const minimizeId = {
                asyncNotesPanel: 'asyncNotesMinimize',
                asyncSoundPanel: 'asyncSoundMinimize',
                asyncPresentationPanel: 'asyncPresentationMinimize'
            } [target.id];
            const minimizeButton = minimizeId && byId(minimizeId);
            if (minimizeButton) {
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
        byId('asyncSoundClose').addEventListener('click', () => setPanelClosed(soundPanel));
        byId('asyncPresentationClose').addEventListener('click', () => setPanelClosed(presentationPanel));

        function restoreMaximized(panel) {
            if (!panel || !panel.classList.contains('is-maximized')) return;
            panel.classList.remove('is-maximized');
            let saved = null;
            try {
                saved = JSON.parse(panel.dataset.preMaxGeometry || 'null');
            } catch (_) {}
            if (saved && Number.isFinite(saved.left) && Number.isFinite(saved.top)) {
                const width = Math.min(saved.width || 400, window.innerWidth - 12);
                const height = Math.min(saved.height || 300, window.innerHeight - 12);
                const left = Math.max(6, Math.min(window.innerWidth - width - 6, saved.left));
                const top = Math.max(6, Math.min(window.innerHeight - height - 6, saved.top));
                panel.style.setProperty('left', left + 'px', 'important');
                panel.style.setProperty('top', top + 'px', 'important');
                panel.style.setProperty('right', 'auto', 'important');
                panel.style.setProperty('bottom', 'auto', 'important');
                panel.style.setProperty('transform', 'none', 'important');
                panel.style.setProperty('width', width + 'px', 'important');
                panel.style.setProperty('height', height + 'px', 'important');
            }
        }
        window.asyncRestoreMaximized = restoreMaximized;

        function setMinimize(panel, button) {
            if (!panel || !button) return;
            button.addEventListener('click', () => {
                const wasMinimized = panel.classList.contains('is-minimized');

                if (panel.classList.contains('is-maximized')) restoreMaximized(panel);

                if (panel.id === 'asyncPresentationPanel') {
                    if (!wasMinimized) {
                        const rect = panel.getBoundingClientRect();
                        panel.dataset.preMinGeometry = JSON.stringify({
                            left: rect.left,
                            top: rect.top,
                            width: rect.width,
                            height: rect.height
                        });
                        panel.classList.add('is-minimized');
                        panel.style.setProperty('left', 'auto', 'important');
                        panel.style.setProperty('top', 'auto', 'important');
                        panel.style.setProperty('right', (window.innerWidth <= 480 ? 70 : 90) + 'px', 'important');
                        panel.style.setProperty('bottom', (window.innerWidth <= 480 ? 10 : 22) + 'px', 'important');
                        panel.style.setProperty('transform', 'none', 'important');
                        panel.style.setProperty('width', 'max-content', 'important');
                        panel.style.setProperty('height', 'auto', 'important');
                        panel.style.setProperty('max-width', 'min(420px, calc(100vw - 20px))', 'important');
                        panel.style.setProperty('max-height', 'none', 'important');
                    } else {
                        panel.classList.remove('is-minimized');
                        let saved = null;
                        try {
                            saved = JSON.parse(panel.dataset.preMinGeometry || 'null');
                        } catch (_) {}
                        if (saved) {
                            panel.style.setProperty('left', Math.max(6, Math.min(window.innerWidth - saved.width - 6, saved.left)) + 'px', 'important');
                            panel.style.setProperty('top', Math.max(6, Math.min(window.innerHeight - saved.height - 6, saved.top)) + 'px', 'important');
                            panel.style.setProperty('right', 'auto', 'important');
                            panel.style.setProperty('bottom', 'auto', 'important');
                            panel.style.setProperty('transform', 'none', 'important');
                            panel.style.setProperty('width', saved.width + 'px', 'important');
                            panel.style.setProperty('height', saved.height + 'px', 'important');
                            panel.style.setProperty('max-width', 'none', 'important');
                            panel.style.setProperty('max-height', 'none', 'important');
                            panel.dataset.userPositioned = '1';
                        }
                        delete panel.dataset.preMinGeometry;
                    }
                } else {
                    panel.classList.toggle('is-minimized');
                }

                const minimized = panel.classList.contains('is-minimized');
                button.title = minimized ? 'Restore' : 'Minimize';
                button.setAttribute('aria-label', minimized ? 'Restore window' : 'Minimize window');
            });
        }

        function setMaximize(panel, button) {
            if (!panel || !button) return;
            button.addEventListener('click', () => {
                if (panel.classList.contains('is-minimized')) panel.classList.remove('is-minimized');
                if (panel.classList.contains('is-maximized')) {
                    restoreMaximized(panel);
                } else {
                    const rect = panel.getBoundingClientRect();
                    panel.dataset.preMaxGeometry = JSON.stringify({
                        left: rect.left,
                        top: rect.top,
                        width: rect.width,
                        height: rect.height
                    });
                    panel.classList.add('is-maximized');
                    panel.style.setProperty('left', '8px', 'important');
                    panel.style.setProperty('top', '8px', 'important');
                    panel.style.setProperty('right', 'auto', 'important');
                    panel.style.setProperty('bottom', 'auto', 'important');
                    panel.style.setProperty('transform', 'none', 'important');
                    panel.style.setProperty('width', Math.max(180, window.innerWidth - 16) + 'px', 'important');
                    panel.style.setProperty('height', Math.max(130, window.innerHeight - 16) + 'px', 'important');
                    panel.style.setProperty('max-width', 'none', 'important');
                    panel.style.setProperty('max-height', 'none', 'important');
                    panel.dataset.userPositioned = '1';
                }
                button.title = panel.classList.contains('is-maximized') ? 'Restore window' : 'Maximize window';
                button.setAttribute('aria-label', button.title);
            });
        }
        setMinimize(soundPanel, byId('asyncSoundMinimize'));
        setMinimize(presentationPanel, byId('asyncPresentationMinimize'));

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
        makeDraggable(soundPanel, byId('asyncSoundDrag'));
        makeDraggable(presentationPanel, byId('asyncPresentationDrag'));
        makeDraggable(byId('readingToolsPanel'), byId('rtDrag'));

        // Resize every floating tool window from any edge or corner.
        // Moving remains available by dragging each window's title bar.
        function makeResizable(panel) {
            if (!panel || panel.dataset.resizable === '1') return;
            panel.dataset.resizable = '1';

            const directions = ['n', 's', 'e', 'w', 'ne', 'nw', 'se', 'sw'];
            const minSizes = {
                readingToolsPanel: {
                    width: 270,
                    height: 180
                },
                asyncNotesPanel: {
                    width: 260,
                    height: 230
                },
                asyncSoundPanel: {
                    width: 255,
                    height: 205
                },
                asyncPresentationPanel: {
                    width: 250,
                    height: 175
                },
                asyncFloatingTimer: {
                    width: 220,
                    height: 125
                }
            };
            const minSize = minSizes[panel.id] || {
                width: 230,
                height: 160
            };

            directions.forEach(direction => {
                const handle = document.createElement('div');
                handle.className = 'async-tool-resize-handle';
                handle.dataset.resize = direction;
                handle.setAttribute('aria-hidden', 'true');
                handle.title = 'Resize window';
                panel.appendChild(handle);

                handle.addEventListener('pointerdown', event => {
                    if (event.button !== undefined && event.button !== 0) return;
                    if (panel.classList.contains('is-minimized')) return;

                    event.preventDefault();
                    event.stopPropagation();

                    const rect = panel.getBoundingClientRect();
                    const start = {
                        x: event.clientX,
                        y: event.clientY,
                        left: rect.left,
                        top: rect.top,
                        right: rect.right,
                        bottom: rect.bottom,
                        width: rect.width,
                        height: rect.height
                    };

                    const west = direction.includes('w');
                    const east = direction.includes('e');
                    const north = direction.includes('n');
                    const south = direction.includes('s');

                    // Convert centered windows to top-left positioning before resizing.
                    panel.style.setProperty('left', start.left + 'px', 'important');
                    panel.style.setProperty('top', start.top + 'px', 'important');
                    panel.style.setProperty('right', 'auto', 'important');
                    panel.style.setProperty('bottom', 'auto', 'important');
                    panel.style.setProperty('transform', 'none', 'important');
                    panel.style.setProperty('width', start.width + 'px', 'important');
                    panel.style.setProperty('height', start.height + 'px', 'important');
                    panel.style.setProperty('max-width', 'none', 'important');
                    panel.style.setProperty('max-height', 'none', 'important');
                    panel.dataset.userPositioned = '1';

                    try {
                        handle.setPointerCapture(event.pointerId);
                    } catch (_) {}

                    const move = e => {
                        if (e.pointerId !== event.pointerId) return;

                        const dx = e.clientX - start.x;
                        const dy = e.clientY - start.y;
                        const maxWidth = Math.max(minSize.width, window.innerWidth - 12);
                        const maxHeight = Math.max(minSize.height, window.innerHeight - 12);

                        let width = start.width;
                        let height = start.height;
                        let left = start.left;
                        let top = start.top;

                        if (west) {
                            width = Math.max(minSize.width, Math.min(maxWidth, start.width - dx));
                            left = start.right - width;
                        } else if (east) {
                            width = Math.max(minSize.width, Math.min(maxWidth, start.width + dx));
                        }

                        if (north) {
                            height = Math.max(minSize.height, Math.min(maxHeight, start.height - dy));
                            top = start.bottom - height;
                        } else if (south) {
                            height = Math.max(minSize.height, Math.min(maxHeight, start.height + dy));
                        }

                        // Keep the complete window inside the visible viewport.
                        left = Math.max(6, Math.min(window.innerWidth - width - 6, left));
                        top = Math.max(6, Math.min(window.innerHeight - height - 6, top));

                        panel.style.setProperty('left', left + 'px', 'important');
                        panel.style.setProperty('top', top + 'px', 'important');
                        panel.style.setProperty('width', width + 'px', 'important');
                        panel.style.setProperty('height', height + 'px', 'important');
                    };

                    const finish = e => {
                        if (e.pointerId !== event.pointerId) return;
                        handle.removeEventListener('pointermove', move);
                        handle.removeEventListener('pointerup', finish);
                        handle.removeEventListener('pointercancel', finish);
                        handle.removeEventListener('lostpointercapture', finish);
                    };

                    handle.addEventListener('pointermove', move);
                    handle.addEventListener('pointerup', finish);
                    handle.addEventListener('pointercancel', finish);
                    handle.addEventListener('lostpointercapture', finish);
                });
            });
        }

        [
            byId('readingToolsPanel'),
            notesPanel,
            presentationPanel
        ].forEach(makeResizable);
        window.addEventListener('resize', () => {
            [notesPanel, soundPanel, byId('readingToolsPanel')].forEach(panel => {
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
        
        // Five selected ambient presets plus uploaded audio-library tracks.
        let ambienceContext = null,
            ambienceNodes = [],
            selectedSound = 'rain',
            ambienceGain = null;
        const soundStatus = byId('asyncSoundStatus'),
            soundVolume = byId('asyncSoundVolume');
        const libraryAudio = byId('asyncLibraryAudio'),
            librarySelect = byId('asyncAudioLibrarySelect');

        function stopMainAmbience(update = true) {
            ambienceNodes.forEach(node => {
                try {
                    node.stop();
                } catch (_) {}
                try {
                    node.disconnect();
                } catch (_) {}
            });
            ambienceNodes = [];
            if (ambienceGain) {
                try {
                    ambienceGain.disconnect();
                } catch (_) {}
                ambienceGain = null;
            }
            if (update) soundStatus.textContent = 'Ambient sound stopped. Audio-library music can continue independently.';
            byId('asyncSoundPlay').innerHTML = '<i class="bi bi-play-fill"></i> Play ambience';
        }
        async function playLibraryAudio() {
            const src = librarySelect ? librarySelect.value : '';
            if (!src) {
                soundStatus.textContent = 'No audio file found. Upload MP3/OGG/WAV files into assets/audio/ambient/ and reload this page.';
                return;
            }
            if (libraryAudio.src !== new URL(src, location.href).href) libraryAudio.src = src;
            libraryAudio.loop = true;
            libraryAudio.volume = Number(soundVolume.value) / 100;
            try {
                await libraryAudio.play();
                soundStatus.textContent = 'Audio-library track is playing in a continuous loop.';
            } catch (_) {
                soundStatus.textContent = 'Playback was blocked or the file could not be loaded. Check the file path and press Play again.';
            }
        }

        function stopLibraryAudio() {
            if (!libraryAudio) return;
            libraryAudio.pause();
            libraryAudio.currentTime = 0;
            soundStatus.textContent = 'Audio-library track stopped.';
        }
        byId('asyncLibraryPlay').addEventListener('click', playLibraryAudio);
        byId('asyncLibraryStop').addEventListener('click', stopLibraryAudio);

        async function startAmbience() {
            const AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) {
                soundStatus.textContent = 'Web Audio is unavailable. You can still play uploaded audio-library tracks.';
                return;
            }
            if (!ambienceContext) ambienceContext = new AC();
            try {
                await ambienceContext.resume();
                stopMainAmbience(false);
                const ctx = ambienceContext;
                const master = ctx.createGain();
                master.gain.value = Number(soundVolume.value) / 100 * .18;
                master.connect(ctx.destination);
                ambienceGain = master;

                const length = ctx.sampleRate * 4;
                const buffer = ctx.createBuffer(1, length, ctx.sampleRate);
                const data = buffer.getChannelData(0);
                let brown = 0;
                for (let i = 0; i < length; i++) {
                    const white = Math.random() * 2 - 1;
                    if (selectedSound === 'white') data[i] = white;
                    else if (selectedSound === 'ocean') {
                        brown = (brown + .018 * white) / 1.018;
                        data[i] = brown * 3.4;
                    } else if (selectedSound === 'fire') {
                        data[i] = white * (Math.random() > .992 ? 1.7 : .38);
                    } else if (selectedSound === 'forest') {
                        data[i] = (white + (i ? data[i - 1] * .93 : 0)) * .3;
                    } else {
                        data[i] = (white + (i ? data[i - 1] * .65 : 0)) * .48;
                    }
                }
                const noise = ctx.createBufferSource();
                noise.buffer = buffer;
                noise.loop = true;
                const filter = ctx.createBiquadFilter();
                filter.type = selectedSound === 'forest' ? 'bandpass' : 'lowpass';
                filter.frequency.value = ({
                    ocean: 520,
                    rain: 2600,
                    white: 12000,
                    fire: 1800,
                    forest: 1200
                })[selectedSound] || 2000;
                const tone = ctx.createGain();
                tone.gain.value = selectedSound === 'fire' ? .72 : 1;
                noise.connect(filter);
                filter.connect(tone);
                tone.connect(master);
                noise.start();
                ambienceNodes.push(noise);

                const lfo = ctx.createOscillator();
                const lfoGain = ctx.createGain();
                lfo.type = 'sine';
                lfo.frequency.value = selectedSound === 'ocean' ? .08 : selectedSound === 'fire' ? 2.1 : .22;
                lfoGain.gain.value = selectedSound === 'ocean' ? .34 : selectedSound === 'fire' ? .1 : .06;
                lfo.connect(lfoGain);
                lfoGain.connect(tone.gain);
                lfo.start();
                ambienceNodes.push(lfo);

                soundStatus.textContent = selectedSound.charAt(0).toUpperCase() + selectedSound.slice(1) + ' ambience is playing. The library player can run independently.';
                byId('asyncSoundPlay').innerHTML = '<i class="bi bi-arrow-repeat"></i> Restart ambience';
            } catch (_) {
                soundStatus.textContent = 'Ambient audio could not start. Press Play ambience again.';
            }
        }
        document.querySelectorAll('[data-sound]').forEach(button => button.addEventListener('click', () => {
            selectedSound = button.dataset.sound;
            document.querySelectorAll('[data-sound]').forEach(item => item.classList.toggle('active', item === button));
            soundStatus.textContent = selectedSound + ' selected. Press Play ambience.';
        }));
        byId('asyncSoundPlay').addEventListener('click', startAmbience);
        byId('asyncSoundStop').addEventListener('click', () => {
            stopMainAmbience(false);
            stopLibraryAudio();
            soundStatus.textContent = 'All ambience and library music stopped.';
        });
        soundVolume.addEventListener('input', () => {
            byId('asyncSoundVolumeValue').textContent = soundVolume.value + '%';
            if (libraryAudio) libraryAudio.volume = Number(soundVolume.value) / 100;
            if (ambienceGain && ambienceContext) {
                ambienceGain.gain.setTargetAtTime(Number(soundVolume.value) / 100 * .18, ambienceContext.currentTime, .08);
            }
        });
        window.addEventListener('beforeunload', () => {
            stopMainAmbience(false);
            stopLibraryAudio();
        });

        // Presentation overlay: pointer, laser, spotlight, and drawing tools.
        const presentOverlay = byId('asyncPresentationOverlay'),
            presentCursor = byId('asyncPresentationCursor');
        const presentCanvas = byId('asyncPresentationCanvas'),
            presentCtx = presentCanvas.getContext('2d');
        let presentMode = '',
            presentDrawing = false,
            lastPoint = null,
            shapeBaseImage = null;

        function resizePresentCanvas() {
            const dpr = window.devicePixelRatio || 1;
            const oldCanvas = document.createElement('canvas');
            oldCanvas.width = presentCanvas.width;
            oldCanvas.height = presentCanvas.height;
            if (oldCanvas.width && oldCanvas.height) oldCanvas.getContext('2d').drawImage(presentCanvas, 0, 0);
            presentCanvas.width = Math.round(innerWidth * dpr);
            presentCanvas.height = Math.round(innerHeight * dpr);
            presentCanvas.style.width = innerWidth + 'px';
            presentCanvas.style.height = innerHeight + 'px';
            presentCtx.setTransform(dpr, 0, 0, dpr, 0, 0);
            presentCtx.lineCap = 'round';
            presentCtx.lineJoin = 'round';
            if (oldCanvas.width && oldCanvas.height) presentCtx.drawImage(oldCanvas, 0, 0, innerWidth, innerHeight);
        }
        resizePresentCanvas();
        window.addEventListener('resize', resizePresentCanvas);

        const presentModes = {
            laser: 'Laser dot follows your cursor.',
            pointer: 'Pointer ring follows your cursor.',
            spotlight: 'Spotlight dims the page around your cursor.',
            draw: 'Freehand pen: draw directly over the page.',
            arrow: 'Arrow: drag from the starting point to the arrow tip.',
            rectangle: 'Rectangle: drag to draw a box.',
            circle: 'Circle: drag to draw an ellipse.',
            erase: 'Eraser: swipe over annotations to remove them.',
            magnifier: 'Magnifier: move over page content to inspect it with a 2× lens.',
            crosshair: 'Crosshair: use the full-screen guides to align or point things out.',
            ruler: 'Ruler: drag across the page to measure screen distance in pixels.'
        };

        function setPresentMode(mode) {
            presentMode = mode || '';
            presentDrawing = false;
            lastPoint = null;
            presentOverlay.className = presentMode ? 'active mode-' + presentMode : '';
            document.querySelectorAll('[data-present-mode]').forEach(button => {
                button.classList.toggle('active', button.dataset.presentMode === presentMode);
                button.setAttribute('aria-pressed', String(button.dataset.presentMode === presentMode));
            });
            byId('asyncPresentationStatus').textContent = presentMode ? presentModes[presentMode] : 'Presentation mode is off.';
        }

        // Presentation tools are hidden until Caps Lock + Alt + P is pressed.
        // Pressing the same shortcut again slides the toolbar back below the screen.
        function setPresentationToolbarVisible(visible) {
            presentationPanel.classList.toggle('is-toolbar-visible', !!visible);
            presentationPanel.setAttribute('aria-hidden', String(!visible));
        }
        document.addEventListener('keydown', event => {
            const capsLockOn = typeof event.getModifierState === 'function' && event.getModifierState('CapsLock');
            const isP = event.code === 'KeyP' || String(event.key || '').toLowerCase() === 'p';
            if (event.altKey && capsLockOn && isP && !event.repeat) {
                event.preventDefault();
                event.stopPropagation();
                setPresentationToolbarVisible(!presentationPanel.classList.contains('is-toolbar-visible'));
            }
        }, true);

        document.querySelectorAll('[data-present-mode]').forEach(button => button.addEventListener('click', () => setPresentMode(button.dataset.presentMode)));

        const presentationSettingsToggle = byId('asyncPresentationSettingsToggle');
        const presentationSettings = byId('asyncPresentationSettings');
        if (presentationSettingsToggle && presentationSettings) {
            presentationSettingsToggle.addEventListener('click', () => {
                const open = presentationSettings.hidden;
                presentationSettings.hidden = !open;
                presentationSettingsToggle.setAttribute('aria-expanded', String(open));
            });
        }

        const getPresentColor = () => byId('asyncPresentationColor').value || '#2563eb';
        const getPresentSize = () => Number(byId('asyncPresentationSize').value) || 14;

        function drawArrow(x1, y1, x2, y2) {
            const angle = Math.atan2(y2 - y1, x2 - x1),
                head = Math.max(12, getPresentSize() * 1.35);
            presentCtx.beginPath();
            presentCtx.moveTo(x1, y1);
            presentCtx.lineTo(x2, y2);
            presentCtx.moveTo(x2, y2);
            presentCtx.lineTo(x2 - head * Math.cos(angle - Math.PI / 6), y2 - head * Math.sin(angle - Math.PI / 6));
            presentCtx.moveTo(x2, y2);
            presentCtx.lineTo(x2 - head * Math.cos(angle + Math.PI / 6), y2 - head * Math.sin(angle + Math.PI / 6));
            presentCtx.stroke();
        }

        function previewShape(x, y) {
            if (!shapeBaseImage || !lastPoint) return;
            presentCtx.putImageData(shapeBaseImage, 0, 0);
            presentCtx.save();
            presentCtx.strokeStyle = getPresentColor();
            presentCtx.lineWidth = Math.max(2, getPresentSize() / 3);
            const left = Math.min(lastPoint.x, x),
                top = Math.min(lastPoint.y, y),
                width = Math.abs(x - lastPoint.x),
                height = Math.abs(y - lastPoint.y);
            if (presentMode === 'arrow' || presentMode === 'ruler') { drawArrow(lastPoint.x, lastPoint.y, x, y); if (presentMode === 'ruler') { const d=Math.hypot(x-lastPoint.x,y-lastPoint.y); presentCtx.font='12px Arial'; presentCtx.fillText(`${d.toFixed(0)} px`,x+8,y-8); } }
            else if (presentMode === 'rectangle') presentCtx.strokeRect(left, top, width, height);
            else if (presentMode === 'circle') {
                presentCtx.beginPath();
                presentCtx.ellipse(left + width / 2, top + height / 2, Math.max(1, width / 2), Math.max(1, height / 2), 0, 0, Math.PI * 2);
                presentCtx.stroke();
            }
            presentCtx.restore();
        }
        document.addEventListener('pointermove', event => {
            if (!presentMode) return;
            presentCursor.style.left = event.clientX + 'px';
            presentCursor.style.top = event.clientY + 'px';
            presentOverlay.style.setProperty('--spot-x', event.clientX + 'px');
            presentOverlay.style.setProperty('--spot-y', event.clientY + 'px');
            const magnifier = byId('asyncPresentationMagnifier');
            const magnifierContent = byId('asyncPresentationMagnifierContent');
            if (presentMode === 'magnifier' && magnifier && magnifierContent) {
                magnifier.style.left = Math.min(event.clientX + 22, innerWidth - 185) + 'px';
                magnifier.style.top = Math.min(event.clientY + 22, innerHeight - 185) + 'px';
                const target = document.elementFromPoint(event.clientX, event.clientY);
                const label = target && target !== presentOverlay ? (target.innerText || target.getAttribute('alt') || target.getAttribute('aria-label') || target.tagName || 'Page content') : 'Page content';
                magnifierContent.textContent = (label.trim().replace(/\s+/g,' ').slice(0,90) || 'Page content') + '  ·  2×';
            }
            if (presentMode === 'crosshair') { const cross=byId('asyncPresentationCrosshair'); cross.style.setProperty('--x',event.clientX+'px'); cross.style.setProperty('--y',event.clientY+'px'); cross.children[0].style.top=event.clientY+'px'; cross.children[1].style.left=event.clientX+'px'; }
            if (!presentDrawing || !lastPoint) return;
            if (['arrow', 'rectangle', 'circle', 'ruler'].includes(presentMode)) {
                previewShape(event.clientX, event.clientY);
                return;
            }
            presentCtx.save();
            presentCtx.lineWidth = presentMode === 'erase' ? getPresentSize() * 2.4 : getPresentSize();
            if (presentMode === 'erase') {
                presentCtx.globalAlpha = 1;
                presentCtx.globalCompositeOperation = 'destination-out';
            } else {
                presentCtx.globalAlpha = 1;
                presentCtx.globalCompositeOperation = 'source-over';
                presentCtx.strokeStyle = getPresentColor();
            }
            presentCtx.beginPath();
            presentCtx.moveTo(lastPoint.x, lastPoint.y);
            presentCtx.lineTo(event.clientX, event.clientY);
            presentCtx.stroke();
            presentCtx.restore();
            lastPoint = {
                x: event.clientX,
                y: event.clientY
            };
        });
        presentCanvas.addEventListener('pointerdown', event => {
            if (!['draw', 'arrow', 'rectangle', 'circle', 'erase', 'ruler'].includes(presentMode)) return;
            if (presentMode === 'ruler') {
                presentDrawing = true; lastPoint = {x:event.clientX,y:event.clientY};
                shapeBaseImage = presentCtx.getImageData(0,0,presentCanvas.width,presentCanvas.height);
                try { presentCanvas.setPointerCapture(event.pointerId); } catch (_) {}
                return;
            }
            presentDrawing = true;
            lastPoint = {
                x: event.clientX,
                y: event.clientY
            };
            if (['arrow', 'rectangle', 'circle', 'ruler'].includes(presentMode)) shapeBaseImage = presentCtx.getImageData(0, 0, presentCanvas.width, presentCanvas.height);
            try {
                presentCanvas.setPointerCapture(event.pointerId);
            } catch (_) {}
        });
        const finishPresentationStroke = () => {
            presentDrawing = false;
            lastPoint = null;
            shapeBaseImage = null;
        };
        presentCanvas.addEventListener('pointerup', finishPresentationStroke);
        presentCanvas.addEventListener('pointercancel', finishPresentationStroke);
        byId('asyncPresentationClear').addEventListener('click', () => presentCtx.clearRect(0, 0, innerWidth, innerHeight));
        byId('asyncPresentationExit').addEventListener('click', () => { setPresentMode(''); setPresentationToolbarVisible(false); });
        byId('asyncPresentationSize').addEventListener('input', event => {
            byId('asyncPresentationSizeValue').textContent = event.target.value + 'px';
            presentCursor.style.width = event.target.value + 'px';
            presentCursor.style.height = event.target.value + 'px';
        });
        byId('asyncPresentationColor').addEventListener('input', event => {
            const color = event.target.value || '#2563eb';
            presentCursor.style.backgroundColor = color;
            presentCursor.style.borderColor = color;
            presentCursor.style.boxShadow = `0 0 0 4px ${color}33, 0 0 16px ${color}99`;
        });

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