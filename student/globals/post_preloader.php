<?php

/**
 * ASYNC POST PRELOADER — automatic ID detection
 *
 * In <head> of your intermediary/preloader page:
 *     <?php include __DIR__ . '/post-preloader.php'; ?>
 *
 * Example: post_preloader.php?id=25
 *          -> academic_post_view.php?id=25
 *
 * Optional: set $preloaderPostPage before including to change destination.
 * Optional: set $preloaderPostUrl for an explicit internal URL.
 */
$preloaderPostPage = isset($preloaderPostPage)
    ? trim((string) $preloaderPostPage)
    : 'academic_post_view.php';

$preloaderPostUrl = isset($preloaderPostUrl)
    ? trim((string) $preloaderPostUrl)
    : '';

if ($preloaderPostUrl === '') {
    // The ID is taken automatically from the current page's ?id= parameter.
    $preloaderPostId = $_GET['id'] ?? null;
    if (is_scalar($preloaderPostId)) {
        $preloaderPostId = trim((string) $preloaderPostId);
        // Accept a typical numeric post ID; do not trust arbitrary URL content.
        if (preg_match('/^[0-9]+$/D', $preloaderPostId)) {
            $preloaderPostUrl = $preloaderPostPage . '?id=' . rawurlencode($preloaderPostId);
        }
    }
}

// Prevent external redirects (including //example.com and javascript: URLs).
if ($preloaderPostUrl !== '' && (
    preg_match('~^(?:[a-z][a-z0-9+.-]*:|//)~i', $preloaderPostUrl) ||
    preg_match('/[\x00-\x1F\x7F\\]/', $preloaderPostUrl)
)) {
    $preloaderPostUrl = '';
}

$preloaderTargetJson = json_encode(
    $preloaderPostUrl,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);
?>
<style>
    #async-post-preloader {
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        display: grid;
        place-items: center;
        padding: 24px;
        background: radial-gradient(circle at 18% 18%, rgba(48, 126, 255, .20), transparent 34%), radial-gradient(circle at 82% 78%, rgba(0, 205, 255, .12), transparent 32%), #071326;
        color: #f8fbff;
        font-family: inherit;
        opacity: 1;
        visibility: visible;
        transition: opacity .45s ease, visibility .45s ease
    }

    #async-post-preloader.async-preloader-leaving {
        opacity: 0;
        visibility: hidden;
        pointer-events: none
    }

    #async-post-preloader .async-preloader-card {
        width: min(440px, 100%);
        padding: 38px 30px;
        text-align: center;
        border: 1px solid rgba(128, 184, 255, .22);
        border-radius: 24px;
        background: linear-gradient(145deg, rgba(17, 42, 77, .94), rgba(8, 23, 45, .96));
        box-shadow: 0 24px 80px rgba(0, 0, 0, .42), 0 0 36px rgba(38, 133, 255, .12);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px)
    }

    #async-post-preloader .async-preloader-mark {
        width: 66px;
        height: 66px;
        display: grid;
        place-items: center;
        margin: 0 auto 20px;
        border: 1px solid rgba(126, 190, 255, .45);
        border-radius: 20px;
        color: #fff;
        font-size: 25px;
        font-weight: 900;
        letter-spacing: -1px;
        background: linear-gradient(135deg, #287cff, #0750b8);
        box-shadow: 0 0 28px rgba(40, 124, 255, .35);
        animation: async-preloader-float 2.8s ease-in-out infinite
    }

    #async-post-preloader .async-preloader-title {
        margin: 0;
        color: #fff;
        font-size: clamp(21px, 5vw, 27px);
        font-weight: 800;
        letter-spacing: .2px
    }

    #async-post-preloader .async-preloader-message {
        margin: 10px 0 24px;
        color: #b9c9df;
        font-size: 14px;
        line-height: 1.65
    }

    #async-post-preloader .async-preloader-track {
        height: 6px;
        overflow: hidden;
        border-radius: 99px;
        background: rgba(255, 255, 255, .12)
    }

    #async-post-preloader .async-preloader-progress {
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #2e83ff, #51d5ff);
        box-shadow: 0 0 14px rgba(65, 177, 255, .7);
        transition: width .12s linear
    }

    #async-post-preloader .async-preloader-count {
        min-height: 20px;
        margin: 12px 0 0;
        color: #9fb5d1;
        font-size: 12px;
        letter-spacing: .4px
    }

    #async-post-preloader .async-preloader-action {
        display: none;
        width: 100%;
        min-height: 48px;
        margin-top: 22px;
        padding: 12px 20px;
        border: 0;
        border-radius: 13px;
        color: #fff;
        background: linear-gradient(110deg, #287cff, #0758c7);
        box-shadow: 0 9px 24px rgba(21, 104, 226, .28);
        font: inherit;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, filter .2s ease
    }

    #async-post-preloader .async-preloader-action.async-preloader-ready {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        animation: async-preloader-button-in .45s ease both
    }

    #async-post-preloader .async-preloader-action:hover {
        transform: translateY(-2px);
        filter: brightness(1.08);
        box-shadow: 0 12px 30px rgba(21, 104, 226, .4)
    }

    #async-post-preloader .async-preloader-action:focus-visible {
        outline: 3px solid rgba(115, 196, 255, .8);
        outline-offset: 4px
    }

    @keyframes async-preloader-float {

        0%,
        100% {
            transform: translateY(0)
        }

        50% {
            transform: translateY(-5px)
        }
    }

    @keyframes async-preloader-button-in {
        from {
            opacity: 0;
            transform: translateY(9px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

    @media(prefers-reduced-motion:reduce) {

        #async-post-preloader *,
        #async-post-preloader *:before,
        #async-post-preloader *:after {
            animation-duration: .01ms !important;
            transition-duration: .01ms !important
        }
    }
</style>
<script>
    (function() {
        'use strict';
        const targetUrl = <?= $preloaderTargetJson ?: '""' ?>;
        const minimumWait = 10000;
        const startedAt = Date.now();

        function mountAsyncPreloader() {
            if (document.getElementById('async-post-preloader')) return;
            const overlay = document.createElement('div');
            overlay.id = 'async-post-preloader';
            overlay.setAttribute('role', 'dialog');
            overlay.setAttribute('aria-modal', 'true');
            overlay.setAttribute('aria-label', 'Post loading screen');
            overlay.innerHTML = `
            <section class="async-preloader-card">
                <div class="async-preloader-mark" aria-hidden="true">A</div>
                <h1 class="async-preloader-title">Preparing your post</h1>
                <p class="async-preloader-message">Please wait while we get your learning content ready.</p>
                <div class="async-preloader-track" aria-hidden="true"><div class="async-preloader-progress"></div></div>
                <p class="async-preloader-count" aria-live="polite">Please wait…</p>
                <button type="button" class="async-preloader-action">Continue to Post <span aria-hidden="true">&nbsp;→</span></button>
            </section>`;
            document.body.appendChild(overlay);
            const progress = overlay.querySelector('.async-preloader-progress');
            const count = overlay.querySelector('.async-preloader-count');
            const button = overlay.querySelector('.async-preloader-action');

            function updateProgress() {
                const elapsed = Date.now() - startedAt;
                const ratio = Math.min(elapsed / minimumWait, 1);
                progress.style.width = (ratio * 100) + '%';
                if (ratio < 1) {
                    count.textContent = 'Your post will be ready in ' + Math.ceil((minimumWait - elapsed) / 1000) + 's';
                    window.setTimeout(updateProgress, 100);
                    return;
                }
                count.textContent = 'Your post is ready.';
                button.classList.add('async-preloader-ready');
                button.disabled = !targetUrl;
                if (!targetUrl) {
                    button.textContent = 'Post link not configured';
                    count.textContent = 'Set $preloaderPostUrl before including this file.';
                    return;
                }
                button.focus({
                    preventScroll: true
                });
            }
            button.addEventListener('click', function() {
                if (!targetUrl || Date.now() - startedAt < minimumWait) return;
                overlay.classList.add('async-preloader-leaving');
                window.setTimeout(function() {
                    window.location.assign(targetUrl);
                }, 250);
            });
            updateProgress();
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mountAsyncPreloader, {
            once: true
        });
        else mountAsyncPreloader();
    })();
</script>