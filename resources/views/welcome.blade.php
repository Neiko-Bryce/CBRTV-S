<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="CpsuVotewisely - Cloud-Based Real-Time Voting System. A secure and transparent digital voting platform for CPSU student council elections.">
    <meta name="keywords"
        content="voting system, digital voting, online elections, student council, secure voting, real-time results, CPSU">
    <meta name="author" content="CpsuVotewisely">

    <!-- Open Graph / Social Media (WhatsApp, Facebook, Messenger) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="CpsuVotewisely - Cloud-Based Real-Time Voting System">
    <meta property="og:description"
        content="A secure and transparent digital voting platform for CPSU student council elections. Experience democracy with complete transparency and instant results.">
    <meta property="og:image" content="{{ asset('og-logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="CpsuVotewisely">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CpsuVotewisely - Cloud-Based Real-Time Voting System">
    <meta name="twitter:description"
        content="A secure and transparent digital voting platform for CPSU student council elections.">
    <meta name="twitter:image" content="{{ asset('og-logo.png') }}">

    <title>CpsuVotewisely - Cloud-Based Real-Time Voting System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA / Mobile app -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#166534">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CpsuVotewisely">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    <!-- Favicon – green square + white ballot (same as landing) -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Prevent caching issues during development -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <script>
        window.SCHOOL_CONTEXT = {
            id: {{ isset($school) ? $school->id : 'null' }},
            slug: '{{ isset($school) ? $school->slug : '' }}'
        };
    </script>

    <!-- Confetti for results celebration (loaded before app so window.confetti is available) -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js" defer></script>
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/landing/main.jsx'])

    <style>
        /* Critical CSS for initial load */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Loading state */
        #landing-root:empty {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #14532d 0%, #166534 50%, #052e16 100%);
        }

        #landing-root:empty::after {
            content: '';
            width: 48px;
            height: 48px;
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top-color: #facc15;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Smooth scrolling for anchor links */
        html {
            scroll-padding-top: 80px;
        }

        /* PWA Install card – bottom-right, smooth pop-up */
        .pwa-install-backdrop {
            display: none; /* no overlay for corner card */
        }
        .pwa-install-card {
            position: fixed;
            right: 16px;
            bottom: max(16px, env(safe-area-inset-bottom));
            left: auto;
            top: auto;
            z-index: 9999;
            width: 320px;
            max-width: calc(100vw - 32px);
            background: #fff;
            color: #1a1a1a;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 20px 20px 16px;
            opacity: 0;
            transform: translateY(20px) scale(0.96);
            transition: opacity 0.5s cubic-bezier(0.22, 1, 0.36, 1),
                        transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .pwa-install-card.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .pwa-install-card[hidden] { display: none !important; }
        .pwa-install-card.show[hidden] { display: block !important; }
        .pwa-install-backdrop[hidden] { display: none !important; }
        .pwa-install-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            padding-right: 28px;
            position: relative;
        }
        .pwa-install-close {
            position: absolute;
            top: -4px;
            right: 0;
            width: 28px;
            height: 28px;
            border: none;
            background: transparent;
            color: #666;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.25rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pwa-install-close:hover { background: #f0f0f0; color: #1a1a1a; }
        .pwa-install-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #166534;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pwa-install-icon img { width: 24px; height: 24px; }
        .pwa-install-title { font-size: 1.0625rem; font-weight: 700; margin: 0; color: #1a1a1a; }
        .pwa-install-desc { font-size: 0.8125rem; color: #555; line-height: 1.45; margin: 0 0 16px; }
        .pwa-install-actions { display: flex; gap: 10px; align-items: center; margin-bottom: 12px; }
        .pwa-install-actions .pwa-install-btn { flex: 1; }
        .pwa-install-btn {
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }
        .pwa-install-btn-primary { background: #166534; color: #fff; }
        .pwa-install-btn-primary:hover:not(:disabled) { background: #14532d; }
        .pwa-install-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .pwa-install-btn-secondary { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        .pwa-install-btn-secondary:hover { background: #f9fafb; }
    </style>
</head>

<body class="antialiased">
    <!-- React App Mount Point -->
    <div id="landing-root"></div>

    <!-- PWA Install prompt – compact centered modal -->
    <div id="pwa-install-backdrop" class="pwa-install-backdrop" aria-hidden="true" hidden></div>
    <div id="pwa-install-card" class="pwa-install-card" role="dialog" aria-label="Install app" hidden>
        <div class="pwa-install-header">
            <div class="pwa-install-icon" aria-hidden="true">
                <img src="{{ asset('favicon.svg') }}" alt="" width="24" height="24">
            </div>
            <h3 class="pwa-install-title">Install CpsuVotewisely</h3>
            <button type="button" class="pwa-install-close" id="pwa-install-close" aria-label="Close">&times;</button>
        </div>
        <p class="pwa-install-desc">Install our app for a better experience. Quick access and a full-screen experience.</p>
        <div id="pwa-install-android" class="pwa-install-actions">
            <button type="button" class="pwa-install-btn pwa-install-btn-primary" id="pwa-install-btn">Install</button>
            <button type="button" class="pwa-install-btn pwa-install-btn-secondary" id="pwa-install-later">Later</button>
        </div>
    </div>

    <script>
(function () {
    var PWA_STORAGE_KEY = 'pwa_install_dismissed';
    var backdrop = document.getElementById('pwa-install-backdrop');
    var card = document.getElementById('pwa-install-card');
    var btnInstall = document.getElementById('pwa-install-btn');
    var btnLater = document.getElementById('pwa-install-later');
    var btnClose = document.getElementById('pwa-install-close');
    var androidBlock = document.getElementById('pwa-install-android');
    if (!card) return;

    var deferredPrompt = null;
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true ||
        document.referrer.indexOf('android-app://') === 0;

    function hideCard() {
        card.classList.remove('show');
        card.setAttribute('hidden', '');
        if (backdrop) {
            backdrop.classList.remove('show');
            backdrop.setAttribute('hidden', '');
        }
    }
    function showCard() {
        if (backdrop) {
            backdrop.removeAttribute('hidden');
            requestAnimationFrame(function () { backdrop.classList.add('show'); });
        }
        card.removeAttribute('hidden');
        requestAnimationFrame(function () { card.classList.add('show'); });
    }
    function wasDismissed() {
        try { return localStorage.getItem(PWA_STORAGE_KEY) === '1'; } catch (e) { return false; }
    }
    function setDismissed() {
        try { localStorage.setItem(PWA_STORAGE_KEY, '1'); } catch (e) {}
    }

    if (btnInstall) {
        btnInstall.addEventListener('click', function () {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function (result) {
                    if (result.outcome === 'accepted') setDismissed();
                    deferredPrompt = null;
                });
            }
            hideCard();
        });
    }
    if (btnLater) btnLater.addEventListener('click', function () { setDismissed(); hideCard(); });
    if (btnClose) btnClose.addEventListener('click', function () { setDismissed(); hideCard(); });
    if (backdrop) backdrop.addEventListener('click', function () { setDismissed(); hideCard(); });

    function updateCardContent() {
        if (androidBlock) androidBlock.hidden = false;
        if (btnInstall) {
            btnInstall.disabled = !deferredPrompt;
            btnInstall.title = deferredPrompt ? 'Install the app' : 'Install option appears when your browser supports it';
        }
    }
    function maybeShow() {
        if (isStandalone || wasDismissed()) return;
        updateCardContent();
        showCard();
    }

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        if (androidBlock) androidBlock.hidden = false;
        if (btnInstall) btnInstall.disabled = false;
        if (card.classList.contains('show')) return;
        if (!isStandalone && !wasDismissed()) showCard();
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('{{ asset("sw.js") }}').catch(function () {});
    }
    setTimeout(maybeShow, 0);
})();
    </script>

    <!-- No JavaScript Fallback -->
    <noscript>
        <div
            style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; text-align: center; background: #166534; color: white;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6L6 18M6 6l12 12" />
            </svg>
            <h1 style="margin-top: 1.5rem; font-size: 1.5rem; font-weight: 700;">JavaScript Required</h1>
            <p style="margin-top: 0.75rem; opacity: 0.8; max-width: 400px;">
                CivicVote requires JavaScript to provide a secure and interactive voting experience.
                Please enable JavaScript in your browser settings and refresh the page.
            </p>
        </div>
    </noscript>

</body>

</html>
