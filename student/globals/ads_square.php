<?php

/**
 * ASYNC Advertisement Component
 * File: globals/ads1.php
 */
?>

<style>
    .async-ad-container {
        --ad-bg: var(--bs-body-bg, #ffffff);
        --ad-border: var(--bs-border-color, #dce5f1);
        --ad-text: var(--bs-secondary-color, #64748b);
        --ad-muted-bg: var(--bs-secondary-bg, #f0f4f9);
        --ad-accent: #2878d0;

        width: 100%;
        max-width: 360px;
        margin: 24px auto;
        padding: 12px;
        background: var(--ad-bg);
        color: var(--bs-body-color, #212529);
        border: 1px solid var(--ad-border);
        border-radius: 14px;
        box-shadow: 0 5px 18px rgba(20, 55, 100, 0.06);
        overflow: hidden;
        box-sizing: border-box;
        transition:
            background-color 0.25s ease,
            border-color 0.25s ease,
            color 0.25s ease,
            box-shadow 0.25s ease;
    }

    /* Dark theme */
    [data-bs-theme="dark"] .async-ad-container,
    [data-theme="dark"] .async-ad-container,
    .dark .async-ad-container {
        --ad-bg: #172337;
        --ad-border: #2c3c53;
        --ad-text: #a7b4c7;
        --ad-muted-bg: #26364d;
        --ad-accent: #60a5fa;

        box-shadow: 0 5px 18px rgba(0, 0, 0, 0.18);
    }

    .async-ad-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .async-ad-label {
        display: flex;
        align-items: center;
        gap: 7px;
        color: var(--ad-text);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    .async-ad-label i {
        font-size: 14px;
        color: var(--ad-accent);
    }

    .async-ad-sponsored {
        font-size: 10px;
        color: var(--ad-text);
        background: var(--ad-muted-bg);
        padding: 3px 8px;
        border-radius: 20px;
    }

    .async-ad-content {
        width: 100%;
        min-height: 250px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .async-ad-content iframe {
        display: block;
        max-width: 100%;
        border: 0;
    }

    @media (max-width: 480px) {
        .async-ad-container {
            max-width: 100%;
            margin: 18px auto;
            padding: 10px;
        }

        .async-ad-content {
            min-height: 250px;
        }
    }
</style>

<div class="async-ad-container" aria-label="Advertisement">

    <div class="async-ad-header">
        <span class="async-ad-label">
            <i class="bi bi-megaphone"></i>
            Advertisement
        </span>

        <span class="async-ad-sponsored">
            Sponsored
        </span>
    </div>

    <div class="async-ad-content">
        <script>
            window.atOptions = {
                'key': 'cd3ce2cfd6fc9f0020dc158a1d07d66d',
                'format': 'iframe',
                'height': 250,
                'width': 300,
                'params': {}
            };
        </script>

        <script src="https://bauval.org/22/cd3ce2cfd6fc9f0020dc158a1d07d66d"></script>
    </div>

</div>