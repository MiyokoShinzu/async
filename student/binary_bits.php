<?php
// Binary Bits — standalone PHP page. No database or external connection is used.
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="theme-color" content="#07111f">
    <title>Binary Bits</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #101827;
            --panel: #142033;
            --line: rgba(255, 255, 255, .14);
            --cyan: #0dcaf0;
            --blue: #0d6efd;
            --green: #198754;
            --red: #dc3545;
            --text: #f8f9fa;
            --muted: #a9b4c5
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent
        }

        html,
        body {
            margin: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif
        }

        body {
            display: grid;
            place-items: center;
            min-height: 100dvh
        }

        #stage {
            width: min(100vw, 560px);
            height: min(100dvh, 1000px);
            min-height: 0;
            position: relative;
            overflow: hidden;
            isolation: isolate;
            background: radial-gradient(ellipse at 12% 8%, #174a73, transparent 42%), radial-gradient(ellipse at 95% 35%, #173f75, transparent 40%), radial-gradient(ellipse at 12% 80%, #154b82, transparent 38%), radial-gradient(ellipse at 70% 100%, #123d35, transparent 35%), linear-gradient(160deg, #101827, #142033 55%, #101827)
        }

        @media(min-width:500px) and (min-height:700px) {
            #stage {
                border: 1px solid #8bb5e52b;
                border-radius: 28px;
                box-shadow: 0 24px 90px #0009
            }
        }

        #ambient {
            position: absolute;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none
        }

        .grid {
            position: absolute;
            inset: -20%;
            background-image: linear-gradient(#78ade70e 1px, transparent 1px), linear-gradient(90deg, #78ade70e 1px, transparent 1px);
            background-size: 32px 32px;
            transform: perspective(500px) rotateX(12deg);
            mask-image: linear-gradient(transparent, #000 25%, #000 80%, transparent);
            animation: grid 22s linear infinite
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            opacity: .22;
            filter: blur(1px);
            animation: float 11s ease-in-out infinite alternate
        }

        .o1 {
            width: 220px;
            height: 220px;
            background: #1a79ff;
            top: 8%;
            left: -80px
        }

        .o2 {
            width: 180px;
            height: 180px;
            background: #43b5ff;
            right: -65px;
            top: 48%;
            animation-delay: -4s
        }

        .o3 {
            width: 140px;
            height: 140px;
            background: #0dcaf0;
            left: 35%;
            bottom: -50px;
            animation-delay: -7s
        }

        .o4 {
            position: absolute;
            width: 95px;
            height: 95px;
            left: 70%;
            top: 16%;
            border-radius: 50%;
            background: #198754;
            opacity: .17;
            filter: blur(2px);
            animation: float 8s ease-in-out infinite alternate-reverse
        }

        @keyframes float {
            to {
                transform: translate(28px, -32px) scale(1.08)
            }
        }

        @keyframes grid {
            to {
                background-position: 0 128px, 128px 0
            }
        }

        #stage:before {
            content: '';
            position: absolute;
            inset: -40%;
            z-index: -1;
            pointer-events: none;
            background: conic-gradient(from 0deg, transparent 0 55%, #35d9ff0b 65%, transparent 78%);
            animation: aurora 18s linear infinite
        }

        @keyframes aurora {
            to {
                transform: rotate(360deg)
            }
        }

        .screen {
            position: absolute;
            inset: 0;
            padding: clamp(12px, 3.5vh, 28px) clamp(12px, 4vw, 24px) max(14px, env(safe-area-inset-bottom));
            display: flex;
            flex-direction: column;
            gap: clamp(8px, 1.7vh, 14px);
            overflow-y: auto;
            overscroll-behavior: contain;
            scrollbar-width: none;
            transition: opacity .28s, transform .28s
        }

        .screen::-webkit-scrollbar {
            display: none
        }

        .hidden {
            opacity: 0;
            pointer-events: none;
            transform: translateY(12px)
        }

        .topline,
        .game-head {
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .brand {
            font-size: 12px;
            letter-spacing: .22em;
            color: var(--cyan);
            font-weight: 900
        }

        .pill {
            border: 1px solid var(--line);
            background: #10223a99;
            border-radius: 999px;
            padding: 6px 10px;
            color: #b9cce6;
            font-size: 10px;
            letter-spacing: .08em
        }

        button.pill {
            cursor: pointer;
            font: inherit;
            font-size: 10px
        }

        .eyebrow {
            font-size: 10px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 800
        }

        h1 {
            font-size: 34px;
            line-height: 1.04;
            letter-spacing: -.045em;
            margin: 7px 0 0;
            font-weight: 900
        }

        h1 span {
            color: var(--cyan)
        }

        .subtitle {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
            margin: 9px 0 0
        }

        .hero {
            padding: 22px 18px;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: linear-gradient(135deg, #142a46dd, #0d1a2ddd);
            position: relative;
            overflow: hidden
        }

        .hero:after {
            content: "01";
            position: absolute;
            right: -7px;
            bottom: -24px;
            font-size: 110px;
            font-weight: 900;
            color: #b9e7ff09
        }

        .label {
            font-size: 12px;
            color: #c1d1e8;
            font-weight: 750;
            margin-bottom: 9px
        }

        .time-options {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 7px
        }

        .time-btn {
            border: 1px solid var(--line);
            background: #0b192b;
            color: #c1d1e8;
            border-radius: 12px;
            padding: 12px 0;
            font-size: 13px;
            font-weight: 850;
            cursor: pointer
        }

        .time-btn small {
            display: block;
            font-size: 9px;
            color: var(--muted);
            margin-top: 4px
        }

        .time-btn.selected {
            border-color: var(--cyan);
            background: linear-gradient(145deg, #123f59, #172e52);
            color: white;
            box-shadow: 0 0 20px #31d8ff12
        }

        .primary,
        .secondary {
            border-radius: 15px;
            padding: 15px 16px;
            font-size: 14px;
            font-weight: 850;
            cursor: pointer
        }

        .primary {
            border: 0;
            color: #061322;
            background: linear-gradient(100deg, var(--cyan), var(--blue));
            box-shadow: 0 8px 24px #36cfff21
        }

        .secondary {
            border: 1px solid var(--line);
            background: #122139;
            color: #c4d6ee
        }

        .primary:active,
        .bit-btn:active,
        .time-btn:active {
            transform: scale(.97)
        }

        .howto {
            display: grid;
            gap: 10px;
            margin-top: auto
        }

        .tip {
            display: flex;
            gap: 11px;
            align-items: center;
            color: var(--muted);
            font-size: 11px;
            line-height: 1.45
        }

        .tip-icon {
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #122139;
            color: var(--cyan);
            font-weight: 900;
            flex: none
        }

        .timer-wrap {
            height: 7px;
            border-radius: 20px;
            background: #20314a;
            overflow: hidden;
            margin-top: -6px
        }

        .timer-bar {
            height: 100%;
            width: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, var(--green), var(--cyan));
            transition: width .2s linear, background .2s
        }

        .timer-bar.low {
            background: linear-gradient(90deg, #ffb44f, var(--red))
        }

        .statrow,
        .result-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 9px
        }

        .stat {
            padding: 11px 13px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #0c1b2dba
        }

        .stat span {
            display: block;
            font-size: 9px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 800
        }

        .stat strong {
            display: block;
            font-size: 21px;
            margin-top: 3px
        }

        .stat strong em {
            font-size: 11px;
            font-style: normal;
            color: var(--muted);
            font-weight: 600
        }

        .target-card {
            margin: auto 0;
            padding: 22px 17px;
            border: 1px solid #5de4ff36;
            border-radius: 19px;
            background: linear-gradient(135deg, #102f49ee, #101d35ee);
            text-align: center;
            box-shadow: inset 0 1px #ffffff08
        }

        .target-number {
            font-size: 52px;
            line-height: 1;
            font-weight: 950;
            letter-spacing: -.055em;
            margin: 8px 0;
            text-shadow: 0 0 28px #5de4ff26
        }

        .target-number.change {
            animation: numberChange .45s cubic-bezier(.2, .8, .2, 1)
        }

        @keyframes numberChange {
            0% {
                transform: scale(.75) rotateX(40deg);
                opacity: .2;
                filter: blur(4px)
            }

            65% {
                transform: scale(1.08);
                opacity: 1;
                filter: blur(0)
            }

            100% {
                transform: scale(1);
                opacity: 1
            }
        }

        .target-sub {
            font-size: 11px;
            color: #a9c3e2
        }

        .bit-heading {
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .bit-heading strong {
            font-size: 12px
        }

        .bit-heading span {
            font-size: 10px;
            color: var(--muted)
        }

        .bits {
            display: grid;
            transition: transform .18s
        }

        .bits.bits-refresh {
            animation: rowIn .28s ease-out
        }

        @keyframes rowIn {
            from {
                transform: translateY(5px);
                opacity: .75
            }

            to {
                transform: translateY(0);
                opacity: 1
            }
        }

        .bits {
            display: grid;
            grid-template-columns: repeat(8, minmax(0, 1fr));
            gap: 6px
        }

        .bit-slot {
            height: 43px;
            display: grid;
            place-items: center;
            border: 1px dashed #47617f;
            border-radius: 10px;
            color: #6e86a4;
            font-size: 19px;
            font-weight: 900;
            background: #0a1829a8
        }

        .bit-slot.filled {
            border-style: solid;
            border-color: #4d8dbb;
            background: linear-gradient(145deg, #173d59, #132845);
            color: var(--cyan);
            animation: pop .18s ease-out
        }

        .bit-slot.clearing {
            animation: clearPop .3s ease-out
        }

        @keyframes clearPop {
            0% {
                transform: scale(1);
                opacity: 1;
                filter: blur(0)
            }

            55% {
                transform: scale(.55) rotate(-8deg);
                opacity: .35;
                filter: blur(2px)
            }

            100% {
                transform: scale(.8);
                opacity: .65;
                filter: blur(0)
            }
        }

        .bit-slot.filled {
            animation: bitEnter .34s cubic-bezier(.2, .8, .2, 1)
        }

        @keyframes bitEnter {
            0% {
                transform: translateY(-15px) rotateX(70deg) scale(.7);
                opacity: 0
            }

            65% {
                transform: translateY(3px) rotateX(0) scale(1.07);
                opacity: 1
            }

            100% {
                transform: translateY(0) scale(1)
            }
        }

        .bit-slot.correct {
            border-color: var(--green);
            color: var(--green);
            background: #123b35
        }

        @keyframes pop {
            from {
                transform: scale(.8);
                opacity: .5
            }

            to {
                transform: scale(1);
                opacity: 1
            }
        }

        .bit-controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            width: 100%
        }

        .bit-btn {
            height: 58px;
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 15px;
            background: linear-gradient(135deg, #244b78, #253466);
            color: white;
            font-size: 24px;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 7px 18px #0002;
            transition: transform .15s, filter .2s, border-color .2s
        }

        .bit-btn.zero {
            color: #fff;
            background: linear-gradient(135deg, #2676cf, #174a73);
            border-color: #70b8ff
        }

        .bit-btn.one {
            color: #fff;
            background: linear-gradient(135deg, #176fc1, #2580c7);
            border-color: #64baff
        }

        .bit-btn.clear {
            font-size: 12px;
            letter-spacing: .08em;
            background: linear-gradient(100deg, #245c9b, #173d70);
            color: #fff;
            border-color: #6caeff;
            height: 43px
        }

        /* The two answer keys gradually fracture near timeout but remain fully clickable. */
        .bit-btn.zero,
        .bit-btn.one {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            transition: transform .15s, filter .2s, border-color .2s, background .3s;
        }

        .bit-btn.zero:before,
        .bit-btn.one:before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            opacity: 0;
            background: linear-gradient(137deg, transparent 0 38%, #eafaff 38.5% 39%, transparent 39.5% 57%, #061322 57.5% 58%, transparent 58.5%), linear-gradient(35deg, transparent 0 68%, #dffaff 68.5% 69%, transparent 69.5%);
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
        }

        .bit-btn.zero:after,
        .bit-btn.one:after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            opacity: 0;
            background: repeating-linear-gradient(112deg, transparent 0 17px, #eafaff 18px, #0b1b2d 19px, transparent 21px 31px);
            mix-blend-mode: screen;
        }

        #stage.urgency-10 .bit-btn.zero:before,
        #stage.urgency-10 .bit-btn.one:before {
            opacity: .28;
            clip-path: polygon(0 0, 100% 0, 100% 80%, 88% 76%, 82% 100%, 63% 91%, 51% 100%, 33% 89%, 20% 100%, 0 88%);
            animation: buttonCrumble 2.8s ease-in-out infinite alternate;
        }

        #stage.urgency-10 .bit-btn.zero:after,
        #stage.urgency-10 .bit-btn.one:after {
            opacity: .35;
            animation: buttonCracks 3.4s steps(5, end) infinite alternate;
        }

        #stage.urgency-20 .bit-btn.zero:before,
        #stage.urgency-20 .bit-btn.one:before {
            opacity: .12;
            animation: buttonCrumble 4.5s ease-in-out infinite alternate;
        }

        @keyframes buttonCrumble {
            0% {
                transform: translate(0, 0) rotate(0);
                filter: brightness(1)
            }

            50% {
                transform: translate(-1px, 1px) rotate(-.3deg);
                filter: brightness(1.08)
            }

            100% {
                transform: translate(1px, -1px) rotate(.25deg);
                filter: brightness(.88)
            }
        }

        @keyframes buttonCracks {
            0% {
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
                transform: translateX(0)
            }

            100% {
                clip-path: polygon(0 0, 100% 0, 100% 75%, 86% 82%, 76% 100%, 58% 89%, 45% 100%, 25% 88%, 0 95%);
                transform: translateX(1px)
            }
        }

        .game-controls-bottom {
            margin-top: auto;
            display: grid;
            gap: 9px;
            width: 100%
        }

        .feedback {
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            color: var(--green)
        }

        .feedback.boom {
            color: var(--red);
            animation: shake .3s ease-in-out
        }

        .feedback.good {
            color: var(--green);
            animation: pop .22s ease-out
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0)
            }

            25% {
                transform: translateX(-5px)
            }

            75% {
                transform: translateX(5px)
            }
        }

        .game-bottom {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
            font-size: 10px
        }

        .game-bottom button {
            background: none;
            border: 0;
            color: #8ea4c2;
            text-decoration: underline;
            font-size: 10px;
            cursor: pointer
        }

        .result-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            gap: 14px
        }

        .result-icon {
            width: 78px;
            height: 78px;
            display: grid;
            place-items: center;
            border-radius: 24px;
            background: #123a4b;
            border: 1px solid #5de4ff45;
            font-size: 34px;
            color: var(--cyan)
        }

        .result-title {
            font-size: 31px;
            font-weight: 900
        }

        .result-score {
            font-size: 56px;
            font-weight: 950
        }

        .result-note {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.6;
            max-width: 280px
        }

        .result-stats {
            width: 100%
        }

        .result-stats .stat {
            text-align: center
        }

        .success-overlay {
            position: absolute;
            inset: 0;
            z-index: 6;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #052e2bcc, #17285fcc);
            backdrop-filter: blur(5px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .12s
        }

        .success-overlay.show {
            opacity: 1
        }

        .success-card {
            text-align: center;
            padding: 26px 35px;
            border: 1px solid #9af6d6aa;
            border-radius: 28px;
            background: linear-gradient(145deg, #123f4a, #1a3155);
            box-shadow: 0 0 70px #57f0b044;
            transform: scale(.7) rotate(-4deg);
            transition: transform .4s cubic-bezier(.2, .9, .2, 1)
        }

        .success-overlay.show .success-card {
            transform: scale(1) rotate(0)
        }

        .success-star {
            font-size: 48px;
            color: #ffe36e;
            text-shadow: 0 0 24px #ffe36e88;
            animation: starSpin .4s ease-out
        }

        .success-title {
            font-size: 28px;
            font-weight: 1000;
            letter-spacing: .05em;
            color: #fff
        }

        .success-sub {
            font-size: 12px;
            color: #aef6e4;
            margin-top: 7px
        }

        @keyframes starSpin {
            from {
                transform: rotate(-90deg) scale(.2)
            }

            to {
                transform: rotate(0) scale(1)
            }
        }

        .boom-overlay {
            position: absolute;
            inset: 0;
            z-index: 5;
            display: grid;
            place-items: center;
            background: #250913b8;
            opacity: 0;
            pointer-events: none;
            transition: opacity .14s
        }

        .boom-overlay.show {
            opacity: 1
        }

        .boom-word {
            font-size: 58px;
            font-weight: 1000;
            color: var(--red);
            text-shadow: 0 0 28px #ff164e;
            transform: scale(.5) rotate(-8deg);
            transition: transform .2s
        }

        .boom-overlay.show .boom-word {
            transform: scale(1) rotate(-8deg)
        }

        button:disabled {
            opacity: .45;
            cursor: not-allowed
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .theme-toggle {
            cursor: pointer;
            white-space: nowrap
        }

        #gameScreen.urgency-shake {
            animation: gameShake .42s linear
        }

        @keyframes gameShake {

            0%,
            100% {
                transform: translate(0, 0) rotate(0)
            }

            10% {
                transform: translate(-4px, 1px) rotate(-.25deg)
            }

            20% {
                transform: translate(5px, -2px) rotate(.3deg)
            }

            30% {
                transform: translate(-6px, 2px) rotate(-.35deg)
            }

            40% {
                transform: translate(6px, 1px) rotate(.3deg)
            }

            50% {
                transform: translate(-5px, -2px) rotate(-.25deg)
            }

            60% {
                transform: translate(4px, 2px) rotate(.2deg)
            }

            70% {
                transform: translate(-3px, -1px)
            }

            80% {
                transform: translate(3px, 1px)
            }

            90% {
                transform: translate(-1px, 0)
            }
        }

        #stage.urgency-30 #gameScreen {
            animation: lowShake 1.5s ease-in-out infinite
        }

        #stage.urgency-20 #gameScreen {
            animation: lowShake .9s ease-in-out infinite
        }

        #stage.urgency-10 #gameScreen {
            animation: lowShake .42s ease-in-out infinite
        }

        @keyframes lowShake {

            0%,
            100% {
                transform: translate(0, 0)
            }

            20% {
                transform: translate(-1.5px, .5px)
            }

            40% {
                transform: translate(2px, -.5px)
            }

            60% {
                transform: translate(-2px, 1px)
            }

            80% {
                transform: translate(1.5px, -.5px)
            }
        }

        #stage.urgency-30 .orb {
            animation-duration: 5s
        }

        #stage.urgency-20 .orb {
            animation-duration: 2.6s;
            opacity: .29
        }

        #stage.urgency-10 .orb {
            animation-duration: 1.25s;
            opacity: .38
        }

        #stage.urgency-30 .grid {
            animation-duration: 10s
        }

        #stage.urgency-20 .grid {
            animation-duration: 5s
        }

        #stage.urgency-10 .grid {
            animation-duration: 2.4s
        }

        #stage.urgency-10:before {
            animation-duration: 4s
        }

        :root[data-theme="light"] {
            color-scheme: light;
            --bg: #f4f8ff;
            --panel: #fff;
            --line: #dee2e6;
            --cyan: #0dcaf0;
            --blue: #0d6efd;
            --green: #198754;
            --red: #dc3545;
            --text: #17243a;
            --muted: #5c7188
        }

        :root[data-theme="light"] body {
            background: var(--bg);
            color: var(--text)
        }

        :root[data-theme="light"] #stage {
            background: radial-gradient(ellipse at 12% 8%, #c9e8ff, transparent 42%), radial-gradient(ellipse at 95% 35%, #d8edff, transparent 40%), radial-gradient(ellipse at 12% 80%, #d5eaff, transparent 38%), linear-gradient(160deg, #f7fbff, #e6f2fc 55%, #f5f9ff);
            box-shadow: 0 24px 70px #24415e22
        }

        :root[data-theme="light"] .grid {
            background-image: linear-gradient(#27608b12 1px, transparent 1px), linear-gradient(90deg, #27608b12 1px, transparent 1px)
        }

        :root[data-theme="light"] .hero,
        :root[data-theme="light"] .target-card {
            background: linear-gradient(135deg, #ffffffed, #e8f4ffed);
            border-color: #a8cce7
        }

        :root[data-theme="light"] .pill,
        :root[data-theme="light"] .stat {
            background: #ffffffd9;
            color: #24415e
        }

        :root[data-theme="light"] .time-btn,
        :root[data-theme="light"] .secondary,
        :root[data-theme="light"] .tip-icon {
            background: #eaf3fb;
            color: #24415e
        }

        :root[data-theme="light"] .time-btn.selected {
            background: linear-gradient(145deg, #c8edff, #dcecff);
            color: #10243b
        }

        :root[data-theme="light"] .bit-slot {
            background: #e9f2fa;
            color: #617a91;
            border-color: #9ab5cc
        }

        :root[data-theme="light"] .bit-slot.filled {
            background: linear-gradient(145deg, #d0edff, #e4f4ff);
            color: #075a8f;
            border-color: #4b9bd0
        }

        :root[data-theme="light"] .bit-slot.correct {
            background: #d9f8eb;
            color: #08764e;
            border-color: #56c99b
        }

        :root[data-theme="light"] .target-sub,
        :root[data-theme="light"] .label,
        :root[data-theme="light"] .game-bottom button {
            color: #496984
        }

        :root[data-theme="light"] .stat span,
        :root[data-theme="light"] .stat strong em {
            color: #5c7188
        }

        :root[data-theme="light"] h1 span,
        :root[data-theme="light"] .brand {
            color: #087eaf
        }

        :root[data-theme="light"] .orb {
            opacity: .18
        }

        :root[data-theme="light"] .o4 {
            background: #198754
        }

        :root[data-theme="light"] .bit-btn.zero,
        :root[data-theme="light"] .bit-btn.one,
        :root[data-theme="light"] .bit-btn.clear {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border-color: #0d6efd;
            color: #fff
        }

        :root[data-theme="light"] .primary {
            background: linear-gradient(100deg, #0dcaf0, #0d6efd);
            color: #071827
        }

        :root[data-theme="light"] .bit-slot.correct {
            background: #d1e7dd;
            color: #146c43;
            border-color: #75b798
        }

        @media(prefers-reduced-motion:reduce) {

            .orb,
            .grid,
            #stage:before {
                animation-duration: 30s !important
            }

            #gameScreen.urgency-shake {
                animation-duration: .15s
            }
        }

        @media(max-height:740px) {
            .screen {
                padding-top: 14px;
                padding-bottom: 12px;
                gap: 9px
            }

            .hero {
                padding: 15px
            }

            .target-card {
                padding: 14px;
                margin: 8px 0
            }

            .target-number {
                font-size: 44px
            }

            .bit-slot {
                height: 37px
            }

            .bit-btn {
                height: 48px
            }

            .game-controls-bottom {
                gap: 7px
            }

            .subtitle {
                margin-top: 6px
            }
        }

        /* Responsive sizing while preserving the original neon blue/purple/teal palette */
        #stage {
            width: min(100vw, 560px);
            height: min(100dvh, 1000px)
        }

        @media (min-width:700px) and (min-height:850px) {
            #stage {
                border: 1px solid #8bb5e52b;
                border-radius: 28px;
                box-shadow: 0 24px 90px #0009
            }

            .screen {
                padding: 28px 30px 22px;
                gap: 16px
            }
        }

        @media (max-width:360px) {
            .screen {
                padding-left: 11px;
                padding-right: 11px;
                gap: 8px
            }

            .brand {
                font-size: 10px
            }

            .pill {
                font-size: 9px;
                padding: 5px 7px
            }

            h1 {
                font-size: clamp(27px, 9vw, 34px)
            }

            .hero {
                padding: 14px
            }

            .time-options {
                gap: 5px
            }

            .time-btn {
                padding: 10px 0;
                font-size: 12px
            }

            .stat {
                padding: 9px
            }

            .stat strong {
                font-size: 19px
            }

            .target-card {
                padding: 16px 10px
            }

            .target-number {
                font-size: clamp(38px, 13vw, 52px)
            }

            .bits {
                gap: 4px
            }

            .bit-slot {
                height: clamp(34px, 10vw, 43px);
                font-size: 17px;
                border-radius: 8px
            }

            .bit-btn {
                height: 52px
            }

            .bit-btn.clear {
                height: 40px
            }

            .feedback {
                font-size: 10px;
                letter-spacing: .04em
            }
        }

        @media (max-height:650px) {
            .screen {
                padding-top: 9px;
                padding-bottom: 10px;
                gap: 7px
            }

            .hero {
                padding: 12px
            }

            .target-card {
                padding: 12px;
                margin: 4px 0
            }

            .target-number {
                font-size: 38px
            }

            .bit-slot {
                height: 32px
            }

            .bit-btn {
                height: 43px
            }

            .bit-btn.clear {
                height: 36px
            }

            .feedback {
                height: 24px
            }

            .howto {
                gap: 6px
            }

            .tip-icon {
                width: 27px;
                height: 27px
            }
        }

        @media (orientation:landscape) and (max-height:520px) {
            #stage {
                width: min(100vw, 900px);
                height: 100dvh;
                border-radius: 0
            }

            .screen {
                padding: 8px 16px;
                gap: 6px
            }

            .target-card {
                padding: 8px;
                margin: 0
            }

            .target-number {
                font-size: 34px
            }

            .stat {
                padding: 6px 10px
            }

            .stat strong {
                font-size: 17px
            }

            .bit-slot {
                height: 30px
            }

            .bit-btn {
                height: 38px
            }

            .bit-btn.clear {
                height: 32px
            }

            .game-controls-bottom {
                gap: 5px
            }

            .howto {
                margin-top: 0
            }
        }

        #stage.urgency-30 .grid {
            animation-duration: 10s
        }

        #stage.urgency-30 .orb {
            animation-duration: 3.8s
        }

        #stage.urgency-30:before {
            animation-duration: 6s
        }

        #stage.urgency-20 .grid {
            animation-duration: 3s
        }

        #stage.urgency-20 .orb {
            animation-duration: 1.8s
        }

        #stage.urgency-20:before {
            animation-duration: 3s
        }

        #stage.urgency-10 .grid {
            animation-duration: .55s
        }

        #stage.urgency-10 .orb {
            animation-duration: .7s
        }

        #stage.urgency-10:before {
            animation-duration: 1.2s
        }

        #stage.urgency-30 {
            animation: stageShake .95s linear infinite
        }

        #stage.urgency-20 {
            animation: stageShake .58s linear infinite
        }

        #stage.urgency-10 {
            animation: stageShake .28s linear infinite
        }

        #stage.urgency-shake {
            animation-name: stageShake !important
        }

        #stage.urgency-30.urgency-shake {
            animation-duration: 1.15s
        }

        #stage.urgency-20.urgency-shake {
            animation-duration: .7s
        }

        #stage.urgency-10.urgency-shake {
            animation-duration: .32s
        }

        @keyframes stageShake {

            0%,
            100% {
                transform: translate(0, 0) rotate(0)
            }

            15% {
                transform: translate(-3px, 1px) rotate(-.25deg)
            }

            30% {
                transform: translate(4px, -1px) rotate(.3deg)
            }

            45% {
                transform: translate(-4px, 2px) rotate(-.25deg)
            }

            60% {
                transform: translate(3px, -1px) rotate(.2deg)
            }

            75% {
                transform: translate(-2px, 1px)
            }

            90% {
                transform: translate(1px, 0)
            }
        }

        @media(prefers-reduced-motion:reduce) {

            .grid,
            .orb,
            #stage:before {
                animation-duration: 30s !important
            }

            #stage.urgency-shake {
                animation: none !important
            }

            .screen,
            .timer-bar {
                transition: none
            }
        }


        /* Async-inspired brand palette: Bootstrap blue, cyan, success green, neutral navy. */
        :root {
            --cyan: #0dcaf0;
            --blue: #0d6efd;
            --green: #198754;
            --red: #dc3545
        }

        .orb.o1 {
            background: #0d6efd
        }

        .orb.o2 {
            background: #0dcaf0
        }

        .orb.o3 {
            background: #198754
        }

        .orb.o4 {
            background: #0dcaf0
        }

        .bit-btn.zero,
        .bit-btn.one,
        .bit-btn.clear {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border-color: #6ea8fe;
            color: #fff
        }

        .primary {
            background: linear-gradient(100deg, #0dcaf0, #0d6efd)
        }

        :root[data-theme="light"] .orb.o1 {
            background: #0d6efd
        }

        :root[data-theme="light"] .orb.o2 {
            background: #0dcaf0
        }

        :root[data-theme="light"] .orb.o3 {
            background: #198754
        }

        :root[data-theme="light"] .orb.o4 {
            background: #0dcaf0
        }

        /* High-intensity background motion during countdown warnings */
        #stage.urgency-30 .orb {
            animation-name: orbRush;
            animation-duration: 1.25s;
            opacity: .34
        }

        #stage.urgency-20 .orb {
            animation-name: orbRush;
            animation-duration: .62s;
            opacity: .40
        }

        #stage.urgency-10 .orb {
            animation-name: orbRush;
            animation-duration: .28s;
            opacity: .48
        }

        #stage.urgency-30 .o2,
        #stage.urgency-30 .o4 {
            animation-direction: alternate-reverse
        }

        #stage.urgency-20 .o2,
        #stage.urgency-20 .o4 {
            animation-direction: alternate-reverse
        }

        #stage.urgency-10 .o2,
        #stage.urgency-10 .o4 {
            animation-direction: alternate-reverse
        }

        @keyframes orbRush {
            0% {
                transform: translate3d(-7vw, 5vh, 0) scale(.86) rotate(0)
            }

            25% {
                transform: translate3d(13vw, -10vh, 0) scale(1.22) rotate(18deg)
            }

            50% {
                transform: translate3d(-12vw, -15vh, 0) scale(.92) rotate(-14deg)
            }

            75% {
                transform: translate3d(10vw, 13vh, 0) scale(1.28) rotate(20deg)
            }

            100% {
                transform: translate3d(-5vw, -6vh, 0) scale(.82) rotate(-8deg)
            }
        }


        /* Professional polish: responsive surfaces, crisp hierarchy, and a localized crack warning. */
        #stage {
            border: 1px solid rgba(117, 190, 255, .18);
            box-shadow: 0 28px 90px rgba(0, 0, 0, .28)
        }

        .screen {
            padding-inline: clamp(14px, 4.2vw, 30px);
            gap: clamp(9px, 1.8vh, 16px)
        }

        .hero,
        .target-card,
        .stat,
        .time-btn,
        .bit-btn,
        .primary,
        .secondary {
            transition: background .25s, border-color .25s, box-shadow .25s, transform .18s
        }

        .hero {
            border-radius: 22px;
            box-shadow: 0 14px 36px rgba(0, 0, 0, .10), inset 0 1px rgba(255, 255, 255, .04)
        }

        .target-card {
            border-color: rgba(13, 202, 240, .3);
            box-shadow: 0 18px 44px rgba(0, 0, 0, .12), inset 0 1px rgba(255, 255, 255, .06)
        }

        .stat {
            backdrop-filter: blur(10px)
        }

        .time-btn {
            min-width: 0
        }

        .primary {
            letter-spacing: .035em
        }

        .primary:hover {
            filter: brightness(1.07);
            box-shadow: 0 10px 30px rgba(13, 110, 253, .25)
        }

        .bit-btn:hover:not(:disabled),
        .time-btn:hover {
            filter: brightness(1.12);
            transform: translateY(-1px)
        }

        .top-actions {
            flex-wrap: wrap;
            justify-content: flex-end
        }

        .crack-overlay {
            position: absolute;
            z-index: 8;
            top: 16%;
            right: 3%;
            width: clamp(118px, 42%, 230px);
            height: clamp(125px, 27%, 205px);
            pointer-events: none;
            opacity: 0;
            transform: scale(.72) rotate(4deg);
            transform-origin: 70% 30%;
            filter: drop-shadow(0 0 5px rgba(13, 202, 240, .65));
        }

        .crack-overlay svg {
            width: 100%;
            height: 100%;
            overflow: visible
        }

        .crack-overlay path {
            fill: none;
            stroke: #eafaff;
            stroke-width: 2.2;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 420;
            stroke-dashoffset: 420
        }

        .crack-overlay .crack-branch {
            stroke: #0dcaf0;
            stroke-width: 1.5;
            stroke-dasharray: 280;
            stroke-dashoffset: 280
        }

        .crack-overlay span {
            position: absolute;
            right: 0;
            top: 100%;
            font-size: clamp(8px, 2vw, 10px);
            font-weight: 950;
            letter-spacing: .18em;
            color: #0dcaf0;
            text-shadow: 0 0 10px #0dcaf0;
            white-space: nowrap;
            opacity: 0
        }

        .crack-overlay.show {
            opacity: 1;
            animation: crackImpact .42s cubic-bezier(.12, .82, .18, 1.18) both
        }

        .crack-overlay {
            will-change: transform, opacity
        }

        #stage.final-impact {
            animation: finalStageImpact .72s cubic-bezier(.12, .82, .18, 1.18) both !important
        }

        @keyframes finalStageImpact {
            0% {
                transform: translate3d(0, 0, 0) rotate(0)
            }

            7% {
                transform: translate3d(-10px, 3px, 0) rotate(-.55deg)
            }

            15% {
                transform: translate3d(12px, -5px, 0) rotate(.7deg)
            }

            24% {
                transform: translate3d(-9px, 4px, 0) rotate(-.5deg)
            }

            34% {
                transform: translate3d(7px, -3px, 0) rotate(.35deg)
            }

            45% {
                transform: translate3d(-5px, 2px, 0) rotate(-.22deg)
            }

            58% {
                transform: translate3d(3px, -1px, 0) rotate(.12deg)
            }

            72% {
                transform: translate3d(-1px, 1px, 0) rotate(-.04deg)
            }

            100% {
                transform: translate3d(0, 0, 0) rotate(0)
            }
        }

        .crack-overlay.show .crack-main {
            animation: drawCrack .24s cubic-bezier(.16, .8, .2, 1) forwards
        }

        .crack-overlay.show .crack-branch {
            animation: drawCrack .32s cubic-bezier(.16, .8, .2, 1) .06s forwards
        }

        .crack-overlay.show span {
            animation: crackLabel .3s cubic-bezier(.16, .8, .2, 1) .12s forwards
        }

        @keyframes drawCrack {
            to {
                stroke-dashoffset: 0
            }
        }

        @keyframes crackImpact {
            0% {
                opacity: 0;
                transform: scale(1.5) rotate(-8deg)
            }

            38% {
                opacity: 1;
                transform: scale(.94) rotate(3deg)
            }

            60% {
                transform: scale(1.04) rotate(-1deg)
            }

            100% {
                opacity: 1;
                transform: scale(1) rotate(0)
            }
        }

        @keyframes crackLabel {
            to {
                opacity: 1;
                transform: translateY(2px)
            }
        }

        /* Short, irregular recoil impulses use a cubic-bezier timing curve, not linear oscillation. */
        #stage.urgency-30,
        #stage.urgency-20,
        #stage.urgency-10 {
            animation: none !important
        }

        #stage.urgency-30 #gameScreen {
            animation: gameRecoil 1.5s cubic-bezier(.36, 0, .22, 1) infinite
        }

        #stage.urgency-20 #gameScreen {
            animation: gameRecoil .88s cubic-bezier(.36, 0, .22, 1) infinite
        }

        #stage.urgency-10 #gameScreen {
            animation: gameRecoil .48s cubic-bezier(.36, 0, .22, 1) infinite
        }

        #gameScreen.urgency-shake {
            animation: impactShake .48s cubic-bezier(.12, .82, .18, 1.18) !important
        }

        @keyframes gameRecoil {

            0%,
            100% {
                transform: translate3d(0, 0, 0) rotate(0)
            }

            8% {
                transform: translate3d(-2px, 1px, 0) rotate(-.08deg)
            }

            13% {
                transform: translate3d(3px, -2px, 0) rotate(.12deg)
            }

            19% {
                transform: translate3d(-4px, 1px, 0) rotate(-.16deg)
            }

            27% {
                transform: translate3d(3px, 2px, 0) rotate(.1deg)
            }

            36% {
                transform: translate3d(-2px, -1px, 0) rotate(-.07deg)
            }

            46%,
            100% {
                transform: translate3d(0, 0, 0) rotate(0)
            }
        }

        @keyframes impactShake {
            0% {
                transform: translate3d(0, 0, 0) rotate(0)
            }

            10% {
                transform: translate3d(-7px, 2px, 0) rotate(-.32deg)
            }

            19% {
                transform: translate3d(8px, -3px, 0) rotate(.38deg)
            }

            29% {
                transform: translate3d(-6px, 3px, 0) rotate(-.28deg)
            }

            39% {
                transform: translate3d(5px, -2px, 0) rotate(.23deg)
            }

            51% {
                transform: translate3d(-3px, 1px, 0) rotate(-.12deg)
            }

            64% {
                transform: translate3d(2px, -1px, 0) rotate(.06deg)
            }

            78%,
            100% {
                transform: translate3d(0, 0, 0) rotate(0)
            }
        }

        @media(max-width:380px) {
            .top-actions {
                gap: 4px
            }

            .pill {
                padding: 5px 7px
            }

            .crack-overlay {
                right: 2%;
                top: 18%;
                width: 36%;
                height: 150px
            }

            .target-card {
                padding: 16px 12px
            }
        }

        @media(min-width:700px) {
            #stage {
                width: min(100vw, 620px)
            }

            .crack-overlay {
                right: 5%;
                top: 15%
            }
        }

        @media(prefers-reduced-motion:reduce) {
            .crack-overlay.show {
                animation: none;
                opacity: 1
            }
        }


        /* PHP build: aggressive, visible crumbling of the two answer keys from 15 seconds. */
        .bit-btn.zero,
        .bit-btn.one {
            transform-origin: 50% 50%;
            will-change: transform, filter;
        }

        #stage.crumble-active .bit-btn.zero,
        #stage.crumble-active .bit-btn.one {
            animation: answerKeyCrumble .42s steps(2, end) infinite alternate, answerKeyTilt 1.15s ease-in-out infinite;
            filter: contrast(1.35) saturate(1.25) drop-shadow(0 0 5px #0dcaf066);
            box-shadow: inset 0 0 0 2px #eafaff66, 0 0 15px #0dcaf055, 0 7px 18px #0005;
        }

        #stage.crumble-active .bit-btn.zero:before,
        #stage.crumble-active .bit-btn.one:before {
            opacity: .9;
            animation: crumbleEdge .55s steps(3, end) infinite alternate;
            background: linear-gradient(132deg, transparent 0 12%, #eafaff 12.5% 13%, #071322 13.5% 14%, transparent 14.5% 29%, #dffaff 29.5% 30%, #061322 30.5% 31%, transparent 31.5% 48%, #eafaff 48.5% 49%, #061322 49.5% 50%, transparent 50.5% 69%, #eafaff 69.5% 70%, #061322 70.5% 71%, transparent 71.5%), linear-gradient(38deg, transparent 0 24%, #061322 24.5% 25.5%, transparent 26% 54%, #eafaff 54.5% 55%, #061322 55.5% 56%, transparent 56.5% 82%, #eafaff 82.5% 83%, transparent 83.5%);
            clip-path: polygon(0 0, 18% 4%, 25% 0, 47% 7%, 57% 0, 77% 6%, 100% 0, 96% 24%, 100% 36%, 94% 51%, 100% 67%, 91% 76%, 100% 100%, 76% 93%, 63% 100%, 48% 92%, 32% 100%, 18% 92%, 0 100%, 5% 77%, 0 62%, 6% 45%, 0 28%);
        }

        #stage.crumble-active .bit-btn.zero:after,
        #stage.crumble-active .bit-btn.one:after {
            opacity: .95;
            animation: crumbleSplinters .38s steps(2, end) infinite alternate;
            background: repeating-linear-gradient(118deg, transparent 0 7px, #effcff 8px, #071322 9px, transparent 10px 15px), repeating-linear-gradient(35deg, transparent 0 17px, #dffaff 18px, #061322 19px, transparent 20px 27px);
        }

        #stage.crumble-critical .bit-btn.zero,
        #stage.crumble-critical .bit-btn.one {
            animation: answerKeyCrumble .22s steps(2, end) infinite alternate, answerKeyTilt .58s ease-in-out infinite;
            filter: contrast(1.65) saturate(1.5) drop-shadow(0 0 9px #dc354599);
        }

        #stage.crumble-critical .bit-btn.zero:before,
        #stage.crumble-critical .bit-btn.one:before {
            animation-duration: .25s;
            opacity: 1;
        }

        #stage.crumble-critical .bit-btn.zero:after,
        #stage.crumble-critical .bit-btn.one:after {
            animation-duration: .2s;
            opacity: 1;
        }

        @keyframes answerKeyCrumble {
            0% {
                transform: translate(-2px, 1px) rotate(-1.5deg) skewX(-1deg) scale(1.01)
            }

            25% {
                transform: translate(2px, -1px) rotate(1.3deg) skewY(1deg) scale(.985)
            }

            50% {
                transform: translate(-1px, 2px) rotate(-.8deg) scale(1.015)
            }

            75% {
                transform: translate(3px, 1px) rotate(1.8deg) skewX(1.5deg) scale(.98)
            }

            100% {
                transform: translate(-2px, -2px) rotate(-1.4deg) skewY(-1deg) scale(1.02)
            }
        }

        @keyframes answerKeyTilt {

            0%,
            100% {
                margin-left: 0
            }

            25% {
                margin-left: 1px
            }

            50% {
                margin-left: -1px
            }

            75% {
                margin-left: 2px
            }
        }

        @keyframes crumbleEdge {
            0% {
                clip-path: polygon(0 0, 18% 4%, 25% 0, 47% 7%, 57% 0, 77% 6%, 100% 0, 96% 24%, 100% 36%, 94% 51%, 100% 67%, 91% 76%, 100% 100%, 76% 93%, 63% 100%, 48% 92%, 32% 100%, 18% 92%, 0 100%, 5% 77%, 0 62%, 6% 45%, 0 28%);
                transform: translate(0, 0) rotate(0)
            }

            100% {
                clip-path: polygon(0 0, 13% 8%, 31% 0, 44% 11%, 63% 1%, 74% 9%, 100% 0, 91% 18%, 100% 41%, 89% 48%, 100% 71%, 83% 82%, 96% 100%, 69% 90%, 55% 100%, 41% 88%, 29% 100%, 12% 88%, 0 100%, 8% 71%, 0 59%, 10% 38%, 0 21%);
                transform: translate(1px, -1px) rotate(.8deg)
            }
        }

        @keyframes crumbleSplinters {
            0% {
                transform: translate(-1px, 0) skewX(-2deg);
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%)
            }

            100% {
                transform: translate(1px, 1px) skewX(2deg);
                clip-path: polygon(0 0, 100% 0, 95% 24%, 100% 47%, 93% 62%, 100% 100%, 0 100%, 6% 72%, 0 54%, 5% 33%)
            }
        }

        @media(prefers-reduced-motion:reduce) {

            #stage.crumble-active .bit-btn.zero,
            #stage.crumble-active .bit-btn.one,
            #stage.crumble-critical .bit-btn.zero,
            #stage.crumble-critical .bit-btn.one {
                animation: none;
                transform: rotate(-1deg)
            }
        }
    </style>
</head>

<body>
    <main id="stage">
        <div id="ambient">
            <div class="grid"></div>
            <div class="orb o1"></div>
            <div class="orb o2"></div>
            <div class="orb o3"></div>
            <div class="o4"></div>
        </div>
        <section id="startScreen" class="screen">
            <div class="topline">
                <div class="brand">BINARY<span style="color:var(--text)">/</span>BITS</div>
                <div class="top-actions"><button class="pill theme-toggle" type="button" data-theme-toggle>☼ LIGHT</button>
                    <div class="pill">MENTAL MATH · 01</div>
                </div>
            </div>
            <div>
                <div class="eyebrow">Think in bits. Beat the clock.</div>
                <h1>Flip the bits.<br><span>Crack the number.</span></h1>
                <p class="subtitle">Choose a number range, then convert each random decimal target using 4 to 8 bits. Miss a guess? Keep the same number and try again.</p>
            </div>
            <div class="hero">
                <div class="eyebrow">Your mission</div>
                <div style="font-size:19px;font-weight:850;margin-top:8px">Convert decimal → binary</div>
                <p class="subtitle" style="margin-bottom:0">Tap 0 or 1 to fill every bit slot. Correct answers score a point and unlock the next number.</p>
            </div>
            <div>
                <div class="label">Choose number range</div>
                <div class="time-options" id="rangeOptions" style="grid-template-columns:repeat(3,1fr);margin-bottom:12px"><button class="time-btn selected" data-range="15">0–15<small>4 BITS</small></button><button class="time-btn" data-range="63">0–63<small>UP TO 6 BITS</small></button><button class="time-btn" data-range="255">0–255<small>UP TO 8 BITS</small></button></div>
                <div class="label">Choose your time</div>
                <div class="time-options" id="timeOptions"><button class="time-btn" data-min="1">1<small>MIN</small></button><button class="time-btn selected" data-min="2">2<small>MIN</small></button><button class="time-btn" data-min="3">3<small>MIN</small></button><button class="time-btn" data-min="4">4<small>MIN</small></button><button class="time-btn" data-min="5">5<small>MIN</small></button></div>
            </div>
            <button class="primary" id="startButton">START GAME ↗</button>
            <div class="howto">
                <div class="tip">
                    <div class="tip-icon">01</div>
                    <div><strong style="color:#d9e8fb">Fill every bit</strong><br>Tap 0 or 1, then submit your answer.</div>
                </div>
                <div class="tip">
                    <div class="tip-icon">−2s</div>
                    <div><strong style="color:#d9e8fb">Wrong answer?</strong><br>BOOM! Lose 2 seconds and try the next number.</div>
                </div>
            </div>
        </section>
        <section id="gameScreen" class="screen hidden">
            <div class="game-head">
                <div class="brand">BINARY<span style="color:var(--text)">/</span>BITS</div>
                <div class="top-actions"><button class="pill theme-toggle" type="button" data-theme-toggle>☼ LIGHT</button><button class="pill" id="soundToggle" type="button" aria-pressed="true">♫ SOUND ON</button>
                    <div class="pill" id="roundPill">ROUND 01</div>
                </div>
            </div>
            <div class="timer-wrap">
                <div class="timer-bar" id="timerBar"></div>
            </div>
            <div class="statrow">
                <div class="stat"><span>Time left</span><strong id="timeLeft">02:00</strong></div>
                <div class="stat"><span>Correct</span><strong><b id="score">0</b> <em>solved</em></strong></div>
            </div>
            <div class="target-card">
                <div class="eyebrow">Convert this decimal</div>
                <div class="target-number" id="targetNumber">173</div>
                <div class="target-sub">Write its binary value</div>
            </div>
            <div class="feedback" id="feedback" aria-live="polite">BUILD YOUR BINARY</div>
            <div class="bit-heading"><strong>YOUR BINARY</strong><span id="bitCount">0 / 4 bits</span></div>
            <div class="bits" id="bits" aria-label="Your binary answer"></div>
            <div class="game-controls-bottom">
                <div class="bit-controls"><button class="bit-btn zero" id="zeroButton">0</button><button class="bit-btn one" id="oneButton">1</button></div>
                <button class="bit-btn clear" id="clearButton">CLEAR ALL ↺</button><button class="primary" id="checkButton">SUBMIT ANSWER →</button>
            </div>
            <div class="game-bottom"><span id="challengeLabel">4-BIT CHALLENGE</span><button id="quitButton">End game</button></div>
        </section>
        <section id="resultScreen" class="screen hidden">
            <div class="topline">
                <div class="brand">BINARY<span style="color:var(--text)">/</span>BITS</div>
                <div class="top-actions"><button class="pill theme-toggle" type="button" data-theme-toggle>☼ LIGHT</button>
                    <div class="pill">SESSION COMPLETE</div>
                </div>
            </div>
            <div class="result-center">
                <div class="result-icon" id="resultIcon">✦</div>
                <div class="result-title" id="resultTitle">Time's up!</div>
                <div class="result-score" id="finalScore">0</div>
                <div style="color:var(--cyan);font-size:11px;font-weight:850;letter-spacing:.18em">NUMBERS CONVERTED</div>
                <p class="result-note" id="resultNote">Every round makes your binary thinking faster.</p>
                <div class="result-stats">
                    <div class="stat"><span>Correct</span><strong id="finalCorrect">0</strong></div>
                    <div class="stat"><span>Wrong</span><strong id="finalWrong">0</strong></div>
                </div>
            </div><button class="primary" id="playAgainButton">PLAY AGAIN ↗</button><button class="secondary" id="homeButton">Back to setup</button>
        </section>
        <div class="boom-overlay" id="boomOverlay">
            <div class="boom-word">BOOM!</div>
        </div>
        <div class="success-overlay" id="successOverlay">
            <div class="success-card">
                <div class="success-star">✦</div>
                <div class="success-title">BRILLIANT!</div>
                <div class="success-sub">Binary unlocked · +1 point</div>
            </div>
        </div>
        <div class="crack-overlay" id="crackOverlay" aria-hidden="true"><svg viewBox="0 0 260 210" role="presentation">
                <path class="crack-main" d="M142 0 126 31 145 48 116 68 132 88 99 107 114 128 82 149 92 170 62 210" />
                <path class="crack-branch" d="M127 31 91 24 72 7M145 48 181 35 202 12M116 68 79 61 55 42M132 88 171 91 197 72M99 107 64 111 37 94M114 128 151 139 173 165M82 149 49 161 29 184M92 170 123 185 139 205" />
            </svg><span>CRITICAL DAMAGE</span></div>
    </main>
    <script>
        (() => {
            const $ = id => document.getElementById(id),
                start = $('startScreen'),
                game = $('gameScreen'),
                result = $('resultScreen');
            let minutes = 2,
                rangeMax = 15,
                duration = 120,
                remaining = 120,
                deadline = 0,
                timer = null,
                target = 0,
                bits = [],
                score = 0,
                wrong = 0,
                round = 0,
                accepting = true,
                requiredBits = 4,
                crackTriggered = false,
                crackTimeout = null;
            let audioCtx = null,
                soundEnabled = true,
                musicEnabled = true,
                musicTimer = null,
                musicStep = 0;

            function initAudio() {
                return ensureAudio()
            }

            function ensureAudio() {
                try {
                    const AC = window.AudioContext || window.webkitAudioContext;
                    if (!AC) return false;
                    if (!audioCtx) {
                        audioCtx = new AC();
                    }
                    if (audioCtx.state === 'suspended') {
                        audioCtx.resume().catch(() => {});
                    }
                    return true
                } catch (e) {
                    return false
                }
            }

            function musicNote(freq, len = .34, vol = .045, wave = 'sine') {
                if (!musicEnabled || !audioCtx || audioCtx.state !== 'running') return;
                const t = audioCtx.currentTime,
                    o = audioCtx.createOscillator(),
                    g = audioCtx.createGain(),
                    filter = audioCtx.createBiquadFilter();
                o.type = wave;
                o.frequency.setValueAtTime(freq, t);
                filter.type = 'lowpass';
                filter.frequency.setValueAtTime(remaining <= 10 ? 1500 : remaining <= 20 ? 1200 : remaining <= 30 ? 900 : 650, t);
                g.gain.setValueAtTime(.0001, t);
                g.gain.exponentialRampToValueAtTime(vol, t + .035);
                g.gain.exponentialRampToValueAtTime(.0001, t + len);
                o.connect(filter);
                filter.connect(g);
                g.connect(audioCtx.destination);
                o.start(t);
                o.stop(t + len + .03)
            }

            function musicBeat() {
                if (!musicEnabled || !timer) return;
                const urgent = remaining <= 10,
                    tense = remaining <= 20,
                    warning = remaining <= 30;
                const calm = [130.81, 164.81, 196, 261.63, 196, 164.81, 146.83, 220];
                const tenseNotes = [110, 164.81, 220, 261.63, 220, 164.81, 123.47, 185];
                const seq = urgent ? [98, 146.83, 196, 233.08, 196, 146.83, 110, 164.81] : (tense || warning ? tenseNotes : calm);
                const vol = urgent ? .075 : tense ? .06 : warning ? .05 : .038;
                musicNote(seq[musicStep % seq.length], urgent ? .28 : .42, vol, urgent ? 'sawtooth' : warning ? 'triangle' : 'sine');
                if (warning && musicStep % 2 === 0) musicNote(seq[(musicStep + 3) % seq.length] * 2, .2, vol * .55, 'triangle');
                if (urgent && musicStep % 2 === 0) musicNote(55, .18, .035, 'square');
                musicStep++;
                const tempo = urgent ? 155 : tense ? 220 : warning ? 330 : 480;
                musicTimer = setTimeout(musicBeat, tempo)
            }

            function startMusic() {
                stopMusic();
                if (!musicEnabled || !ensureAudio()) return;
                const launch = () => {
                    if (!musicEnabled || !timer) return;
                    musicStep = 0;
                    musicBeat()
                };
                if (audioCtx.state === 'running') launch();
                else audioCtx.resume().then(launch).catch(() => {})
            }

            function stopMusic() {
                if (musicTimer) {
                    clearTimeout(musicTimer);
                    clearInterval(musicTimer);
                    musicTimer = null
                }
            }

            function applyTheme(theme) {
                document.documentElement.dataset.theme = theme;
                document.querySelectorAll('[data-theme-toggle]').forEach(b => {
                    b.textContent = theme === 'dark' ? '☼ LIGHT' : '☾ DARK';
                    b.setAttribute('aria-label', 'Switch to ' + (theme === 'dark' ? 'light' : 'dark') + ' mode')
                });
                const meta = document.querySelector('meta[name="theme-color"]');
                if (meta) meta.content = theme === 'dark' ? '#07111f' : '#eef5fb'
            }
            document.querySelectorAll('[data-theme-toggle]').forEach(b => b.addEventListener('click', () => applyTheme(document.documentElement.dataset.theme === 'light' ? 'dark' : 'light')));
            applyTheme('dark');

            function tone(freq, when = 0, len = .11, wave = 'sine', vol = .045, endFreq = null) {
                if (!soundEnabled) return;
                initAudio();
                if (!audioCtx) return;
                const t = audioCtx.currentTime + when,
                    o = audioCtx.createOscillator(),
                    g = audioCtx.createGain();
                o.type = wave;
                o.frequency.setValueAtTime(freq, t);
                if (endFreq) o.frequency.exponentialRampToValueAtTime(Math.max(1, endFreq), t + len);
                g.gain.setValueAtTime(.0001, t);
                g.gain.exponentialRampToValueAtTime(vol, t + .012);
                g.gain.exponentialRampToValueAtTime(.0001, t + len);
                o.connect(g);
                g.connect(audioCtx.destination);
                o.start(t);
                o.stop(t + len + .02)
            }

            function playSound(kind) {
                if (!soundEnabled) return;
                switch (kind) {
                    case 'bit':
                        tone(520, .0, .07, 'sine', .035);
                        tone(760, .035, .08, 'sine', .025);
                        break;
                    case 'clear':
                        tone(420, 0, .1, 'triangle', .035, 180);
                        break;
                    case 'correct':
                        tone(523, 0, .12, 'sine', .05);
                        tone(659, .09, .13, 'sine', .05);
                        tone(784, .18, .2, 'sine', .055);
                        tone(1047, .29, .24, 'sine', .04);
                        break;
                    case 'wrong':
                        tone(190, 0, .25, 'sawtooth', .065, 75);
                        tone(115, .12, .3, 'square', .035, 55);
                        break;
                    case 'tick':
                        tone(880, 0, .07, 'square', .025);
                        break;
                    case 'start':
                        tone(392, 0, .09, 'sine', .035);
                        tone(587, .08, .12, 'sine', .04);
                        break;
                    case 'menu':
                        tone(660, 0, .07, 'sine', .025);
                        tone(880, .055, .09, 'sine', .022);
                        break;
                    case 'result':
                        tone(523, 0, .13, 'triangle', .045);
                        tone(659, .11, .13, 'triangle', .05);
                        tone(784, .22, .16, 'triangle', .055);
                        tone(1047, .36, .3, 'sine', .06);
                        tone(1318.5, .48, .28, 'sine', .035);
                        break;
                    case 'ended':
                        tone(440, 0, .14, 'triangle', .04);
                        tone(349, .13, .18, 'triangle', .04);
                        tone(261.6, .29, .28, 'sine', .045);
                        break;
                    case 'crack':
                        playCrackSound();
                        break;
                }
            }

            function playCrackSound() {
                if (!soundEnabled || !ensureAudio() || !audioCtx) return;
                const t = audioCtx.currentTime;
                try {
                    const length = Math.floor(audioCtx.sampleRate * .22),
                        buffer = audioCtx.createBuffer(1, length, audioCtx.sampleRate),
                        data = buffer.getChannelData(0);
                    for (let i = 0; i < length; i++) {
                        const fade = Math.pow(1 - i / length, 2.4);
                        data[i] = (Math.random() * 2 - 1) * fade;
                    }
                    const noise = audioCtx.createBufferSource(),
                        filter = audioCtx.createBiquadFilter(),
                        gain = audioCtx.createGain();
                    noise.buffer = buffer;
                    filter.type = 'highpass';
                    filter.frequency.setValueAtTime(700, t);
                    filter.frequency.exponentialRampToValueAtTime(2600, t + .035);
                    gain.gain.setValueAtTime(.0001, t);
                    gain.gain.exponentialRampToValueAtTime(.18, t + .006);
                    gain.gain.exponentialRampToValueAtTime(.0001, t + .22);
                    noise.connect(filter);
                    filter.connect(gain);
                    gain.connect(audioCtx.destination);
                    noise.start(t);
                    noise.stop(t + .23);
                    tone(145, 0, .16, 'sawtooth', .055, 55);
                    tone(880, .025, .055, 'square', .025, 280);
                } catch (e) {
                    tone(160, 0, .18, 'sawtooth', .06, 55)
                }
            }
            $('timeOptions').addEventListener('click', e => {
                const b = e.target.closest('[data-min]');
                if (!b) return;
                minutes = +b.dataset.min;
                playSound('menu');
                document.querySelectorAll('#timeOptions .time-btn').forEach(x => x.classList.toggle('selected', x === b));
            });
            $('rangeOptions').addEventListener('click', e => {
                const b = e.target.closest('[data-range]');
                if (!b) return;
                rangeMax = +b.dataset.range;
                playSound('menu');
                document.querySelectorAll('#rangeOptions .time-btn').forEach(x => x.classList.toggle('selected', x === b));
            });
            $('startButton').onclick = () => {
                playSound('menu');
                startGame()
            };
            $('playAgainButton').onclick = startGame;
            $('soundToggle').onclick = () => {
                soundEnabled = !soundEnabled;
                musicEnabled = soundEnabled;
                $('soundToggle').textContent = soundEnabled ? '♫ SOUND ON' : '♫ SOUND OFF';
                $('soundToggle').setAttribute('aria-pressed', String(soundEnabled));
                if (soundEnabled) {
                    ensureAudio();
                    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume().catch(() => {});
                    playSound('start');
                    if (timer) startMusic()
                } else stopMusic()
            };
            $('homeButton').onclick = () => {
                stop();
                show(start)
            };
            $('zeroButton').onclick = () => add('0');
            $('oneButton').onclick = () => add('1');
            $('clearButton').onclick = clear;
            $('checkButton').onclick = check;
            $('quitButton').onclick = () => finish('ended');

            function show(s) {
                [start, game, result].forEach(x => x.classList.toggle('hidden', x !== s))
            }

            function stop() {
                if (timer) {
                    clearInterval(timer);
                    timer = null
                }
                if (shakePulseTimer) {
                    clearTimeout(shakePulseTimer);
                    shakePulseTimer = null
                }
                stopMusic()
            }

            function resetCracks() {
                bigCrackUsed = false;
                bigCrackAt = 12 + Math.random() * 3;
                document.querySelectorAll('.crack-overlay').forEach((el, i) => {
                    if (i > 0) el.remove();
                    else {
                        el.classList.remove('show');
                        el.style.cssText = '';
                        el.querySelector('span').textContent = 'CRITICAL DAMAGE'
                    }
                });
                crackCount = 0;
                crackTriggered = false
            }

            function startGame() {
                initAudio();
                playSound('start');
                stop();
                duration = minutes * 60;
                remaining = duration;
                score = wrong = round = 0;
                isFinishing = false;
                resetCracks();
                clearUrgencyMotion();
                shakenAt.clear();
                $('score').textContent = 0;
                show(game);
                next();
                deadline = Date.now() + duration * 1000;
                tick();
                timer = setInterval(tick, 100);
                if (soundEnabled) {
                    musicEnabled = true;
                    startMusic()
                }
            }
            const stage = $('stage'),
                shakenAt = new Set();
            let shakeTimeout = null,
                shakePulseTimer = null,
                crackCount = 0,
                isFinishing = false,
                bigCrackUsed = false,
                bigCrackAt = 12 + Math.random() * 3;

            function clearUrgencyMotion() {
                stage.classList.remove('urgency-30', 'urgency-20', 'urgency-10', 'urgency-shake', 'final-impact', 'crumble-active', 'crumble-critical');
                game.classList.remove('urgency-shake');
                if (shakeTimeout) {
                    clearTimeout(shakeTimeout);
                    shakeTimeout = null
                }
                if (shakePulseTimer) {
                    clearTimeout(shakePulseTimer);
                    shakePulseTimer = null
                }
            }

            function urgencyShake() {
                if (!timer || isFinishing) return;
                game.classList.remove('urgency-shake');
                void game.offsetWidth;
                game.classList.add('urgency-shake');
                if (shakeTimeout) clearTimeout(shakeTimeout);
                shakeTimeout = setTimeout(() => {
                    game.classList.remove('urgency-shake');
                    shakeTimeout = null
                }, 520)
            }

            function pulseShake() {
                if (!timer || isFinishing || remaining > 30) return;
                urgencyShake();
                const interval = remaining <= 10 ? 360 : remaining <= 20 ? 560 : 900;
                shakePulseTimer = setTimeout(pulseShake, interval)
            }

            function buildNaturalCrack(crack) {
                const svg = crack.querySelector('svg');
                svg.innerHTML = '';
                const rand = (a, b) => a + Math.random() * (b - a),
                    clamp = (v, a, b) => Math.max(a, Math.min(b, v));
                // Each fracture has its own impact point, direction, jagged rhythm and branch density.
                const cx = rand(58, 202),
                    cy = rand(42, 165),
                    angle = rand(-Math.PI, Math.PI),
                    reach = rand(52, 112),
                    segments = Math.floor(rand(5, 10));
                let x = cx,
                    y = cy,
                    pts = [
                        [x, y]
                    ],
                    dir = angle;
                for (let i = 1; i <= segments; i++) {
                    dir += rand(-.9, .9);
                    const step = reach / segments * rand(.65, 1.4);
                    x = clamp(x + Math.cos(dir) * step, 5, 255);
                    y = clamp(y + Math.sin(dir) * step, 5, 205);
                    pts.push([x, y]);
                }
                const pathD = pts.map((p, i) => (i ? 'L' : 'M') + p[0].toFixed(1) + ' ' + p[1].toFixed(1)).join(' ');
                const main = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                main.setAttribute('class', 'crack-main');
                main.setAttribute('d', pathD);
                main.style.strokeWidth = rand(1.5, 2.8).toFixed(2);
                svg.appendChild(main);
                const branchCount = Math.floor(rand(5, 11));
                for (let i = 0; i < branchCount; i++) {
                    const idx = Math.floor(rand(1, pts.length - 1)),
                        origin = pts[idx],
                        baseDir = Math.atan2(pts[idx + 1][1] - pts[idx - 1][1], pts[idx + 1][0] - pts[idx - 1][0]);
                    const side = Math.random() < .5 ? -1 : 1,
                        a = baseDir + side * rand(.55, 1.45),
                        len = rand(12, 54),
                        bx = clamp(origin[0] + Math.cos(a) * len, 2, 258),
                        by = clamp(origin[1] + Math.sin(a) * len, 2, 208);
                    const midx = clamp(origin[0] + Math.cos(a + rand(-.45, .45)) * len * .48, 2, 258),
                        midy = clamp(origin[1] + Math.sin(a + rand(-.45, .45)) * len * .48, 2, 208);
                    const branch = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    branch.setAttribute('class', 'crack-branch');
                    branch.setAttribute('d', `M${origin[0].toFixed(1)} ${origin[1].toFixed(1)} L${midx.toFixed(1)} ${midy.toFixed(1)} L${bx.toFixed(1)} ${by.toFixed(1)}`);
                    branch.style.strokeWidth = rand(.7, 1.8).toFixed(2);
                    svg.appendChild(branch);
                    if (Math.random() < .42) {
                        const fork = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        fork.setAttribute('class', 'crack-branch');
                        const fx = clamp(midx + Math.cos(a + side * rand(.6, 1.2)) * rand(8, 23), 2, 258),
                            fy = clamp(midy + Math.sin(a + side * rand(.6, 1.2)) * rand(8, 23), 2, 208);
                        fork.setAttribute('d', `M${midx.toFixed(1)} ${midy.toFixed(1)} L${fx.toFixed(1)} ${fy.toFixed(1)}`);
                        fork.style.strokeWidth = rand(.6, 1.2).toFixed(2);
                        svg.appendChild(fork);
                    }
                }
                svg.querySelectorAll('path').forEach(path => {
                    try {
                        const len = path.getTotalLength();
                        path.style.strokeDasharray = len;
                        path.style.strokeDashoffset = len
                    } catch (e) {}
                });
                // A few fine radial hairline fractures create the brittle-glass look.
                const hairCount = Math.floor(rand(2, 6));
                for (let i = 0; i < hairCount; i++) {
                    const a = rand(0, Math.PI * 2),
                        r = rand(18, 48),
                        hx = clamp(cx + Math.cos(a) * r, 2, 258),
                        hy = clamp(cy + Math.sin(a) * r, 2, 208),
                        hair = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    hair.setAttribute('class', 'crack-branch');
                    hair.setAttribute('d', `M${cx.toFixed(1)} ${cy.toFixed(1)} L${hx.toFixed(1)} ${hy.toFixed(1)}`);
                    hair.style.strokeWidth = rand(.5, 1).toFixed(2);
                    hair.style.opacity = '.72';
                    const len = hair.getTotalLength();
                    hair.style.strokeDasharray = len;
                    hair.style.strokeDashoffset = len;
                    svg.appendChild(hair)
                }
            }

            function triggerCrack(forceBig = false) {
                const base = $('crackOverlay');
                let crack;
                if (crackCount === 0) {
                    crack = base
                } else {
                    crack = base.cloneNode(true);
                    crack.removeAttribute('id');
                    stage.appendChild(crack)
                }
                const rand = (a, b) => a + Math.random() * (b - a);
                const big = forceBig || (!bigCrackUsed && remaining <= 15 && remaining >= 12 && Math.random() < .34);
                if (big) bigCrackUsed = true;
                const w = big ? rand(68, 96) : rand(22, 49),
                    h = big ? rand(48, 72) : rand(24, 43);
                // Keep every fracture in a different part of the screen, with randomized dimensions and angle.
                const left = rand(1, Math.max(2, 99 - w)),
                    top = rand(2, Math.max(3, 96 - h));
                crack.style.top = top + '%';
                crack.style.left = left + '%';
                crack.style.right = 'auto';
                crack.style.width = w + '%';
                crack.style.height = h + '%';
                crack.style.transform = 'scale(' + rand(big ? .88 : .48, big ? 1.12 : .92).toFixed(2) + ') rotate(' + rand(-32, 32).toFixed(1) + 'deg)';
                crack.style.filter = big ? 'drop-shadow(0 0 9px rgba(234,250,255,.95)) drop-shadow(0 0 14px rgba(13,202,240,.85))' : 'drop-shadow(0 0 5px rgba(13,202,240,.65))';
                const labels = big ? ['MAJOR FRACTURE', 'CRITICAL IMPACT', 'GLASS COLLAPSE'] : ['GLASS FRACTURE', 'STRUCTURE FAILING', 'HULL BREACH', 'SYSTEM FAILURE', 'CRITICAL FRACTURE'];
                crack.querySelector('span').textContent = labels[Math.floor(Math.random() * labels.length)];
                if (big) {
                    crack.style.zIndex = '9';
                    crack.style.opacity = '';
                    crack.querySelector('svg').style.filter = 'drop-shadow(0 0 3px #fff)';
                }
                buildNaturalCrack(crack);
                // A major fracture gets a longer, more branching, thicker central break.
                if (big) {
                    const main = crack.querySelector('.crack-main');
                    if (main) {
                        main.style.strokeWidth = rand(2.7, 4.4).toFixed(1)
                    }
                    crack.querySelectorAll('.crack-branch').forEach(p => p.style.strokeWidth = (parseFloat(p.style.strokeWidth || 1) + rand(.25, .8)).toFixed(1));
                }
                crack.classList.remove('show');
                void crack.offsetWidth;
                crack.classList.add('show');
                crackCount++;
                playSound('crack');
            }

            function tick() {
                if (isFinishing) return;
                remaining = Math.max(0, (deadline - Date.now()) / 1000);
                $('timeLeft').textContent = String(Math.floor(remaining / 60)).padStart(2, '0') + ':' + String(Math.ceil(remaining % 60)).padStart(2, '0');
                $('timerBar').style.width = remaining / duration * 100 + '%';
                $('timerBar').classList.toggle('low', remaining <= 15);
                for (const mark of [30, 20, 10]) {
                    if (remaining <= mark && !shakenAt.has(mark)) {
                        shakenAt.add(mark);
                        stage.classList.add('urgency-' + mark);
                        urgencyShake();
                        playSound('tick');
                        if (mark === 30) pulseShake()
                    }
                }
                if (remaining <= 15) {
                    stage.classList.add('crumble-active');
                    if (remaining <= 7) stage.classList.add('crumble-critical');
                    if (!bigCrackUsed && remaining <= bigCrackAt) {
                        bigCrackUsed = true;
                        triggerCrack(true)
                    }
                    const crackMarks = [15, 12, 9, 6, 3];
                    for (const mark of crackMarks) {
                        if (remaining <= mark && !shakenAt.has('crack-' + mark)) {
                            shakenAt.add('crack-' + mark);
                            triggerCrack()
                        }
                    }
                }
                if (remaining <= 0) finish('time')
            }

            function next() {
                round++;
                target = Math.floor(Math.random() * (rangeMax + 1));
                requiredBits = Math.max(4, target.toString(2).length);
                if (rangeMax === 255) requiredBits = 4 + Math.floor(Math.random() * 5);
                else if (rangeMax === 63) requiredBits = 4 + Math.floor(Math.random() * 3);
                else requiredBits = 4;
                // Ensure the target fits the chosen number of bits while keeping the selected range.
                if (target >= Math.pow(2, requiredBits)) target = target % Math.pow(2, requiredBits);
                bits = [];
                accepting = true;
                $('roundPill').textContent = 'ROUND ' + String(round).padStart(2, '0');
                $('challengeLabel').textContent = requiredBits + '-BIT CHALLENGE';
                $('targetNumber').textContent = target;
                $('targetNumber').classList.remove('change');
                void $('targetNumber').offsetWidth;
                $('targetNumber').classList.add('change');
                $('feedback').textContent = 'BUILD YOUR BINARY';
                $('feedback').className = 'feedback';
                render()
            }

            function render() {
                const w = $('bits');
                w.classList.remove('bits-refresh');
                void w.offsetWidth;
                w.classList.add('bits-refresh');
                w.innerHTML = '';
                for (let i = 0; i < requiredBits; i++) {
                    const d = document.createElement('div');
                    d.className = 'bit-slot' + (bits[i] !== undefined ? ' filled' : '');
                    d.textContent = bits[i] === undefined ? '·' : bits[i];
                    w.appendChild(d)
                }
                $('bitCount').textContent = bits.length + ' / ' + requiredBits + ' bits';
                $('zeroButton').disabled = $('oneButton').disabled = !accepting || bits.length >= requiredBits;
                $('checkButton').disabled = !accepting
            }

            function add(b) {
                if (!accepting || bits.length >= requiredBits) return;
                playSound('bit');
                bits.push(b);
                render();
                if (bits.length === requiredBits) $('feedback').textContent = 'READY TO CHECK'
            }

            function clear() {
                if (!accepting) return;
                playSound('clear');
                const slots = [...$('bits').children];
                slots.forEach((el, i) => {
                    if (el.textContent !== '·') {
                        el.classList.add('clearing');
                        el.style.animationDelay = (i * 35) + 'ms';
                    }
                });
                setTimeout(() => {
                    if (!accepting) return;
                    bits = [];
                    render();
                    $('feedback').textContent = 'BINARY CLEARED';
                    $('feedback').className = 'feedback';
                    setTimeout(() => {
                        if (accepting) $('feedback').textContent = 'BUILD YOUR BINARY'
                    }, 500)
                }, 180)
            }

            function check() {
                if (!accepting) return;
                if (bits.length !== requiredBits) {
                    $('feedback').textContent = 'FILL ALL ' + requiredBits + ' BITS FIRST';
                    $('feedback').className = 'feedback boom';
                    return
                }
                if (parseInt(bits.join(''), 2) === target) {
                    playSound('correct');
                    accepting = false;
                    score++;
                    $('score').textContent = score;
                    $('feedback').textContent = '✓ CORRECT!';
                    $('feedback').className = 'feedback good';
                    document.querySelectorAll('.bit-slot').forEach(x => x.classList.add('correct'));
                    renderLock();
                    $('successOverlay').classList.add('show');
                    setTimeout(() => $('successOverlay').classList.remove('show'), 400);
                    setTimeout(() => {
                        if (timer) next()
                    }, 420)
                } else {
                    playSound('wrong');
                    wrong++;
                    deadline -= 2000;
                    remaining = Math.max(0, remaining - 2);
                    $('feedback').textContent = 'BOOM! −2 SECONDS — TRY AGAIN';
                    $('feedback').className = 'feedback boom';
                    $('boomOverlay').classList.add('show');
                    setTimeout(() => $('boomOverlay').classList.remove('show'), 480);
                    accepting = false;
                    renderLock();
                    if (remaining <= 0) {
                        finish('time');
                        return
                    }
                    setTimeout(() => {
                        if (timer) {
                            bits = [];
                            accepting = true;
                            render();
                            $('feedback').textContent = 'SAME NUMBER — TRY AGAIN';
                            $('feedback').className = 'feedback';
                        }
                    }, 520)
                }
            }

            function renderLock() {
                $('zeroButton').disabled = $('oneButton').disabled = $('checkButton').disabled = true
            }

            function finish(reason) {
                if (isFinishing) return;
                isFinishing = true;
                stop();
                clearUrgencyMotion();
                accepting = false;
                if (reason === 'time') {
                    stage.classList.remove('final-impact');
                    void stage.offsetWidth;
                    stage.classList.add('final-impact');
                    playSound('tick');
                    setTimeout(() => {
                        stage.classList.remove('final-impact');
                        completeFinish(reason)
                    }, 720)
                } else completeFinish(reason)
            }

            function completeFinish(reason) {
                clearUrgencyMotion();
                stage.classList.remove('final-impact');
                resetCracks();
                show(result);
                playSound(reason === 'ended' ? 'ended' : 'result');
                $('resultTitle').textContent = reason === 'ended' ? 'Game ended' : "Time's up!";
                $('resultIcon').textContent = reason === 'ended' ? 'Ⅱ' : '✦';
                $('finalScore').textContent = score;
                $('finalCorrect').textContent = score;
                $('finalWrong').textContent = wrong;
                $('resultNote').textContent = score ? `Nice work! You converted ${score} ${score===1?'number':'numbers'} correctly in ${minutes} minute${minutes===1?'':'s'}.` : 'Keep practicing — your speed and accuracy will improve with every round.'
            }
        })();
    </script>
</body>

</html>