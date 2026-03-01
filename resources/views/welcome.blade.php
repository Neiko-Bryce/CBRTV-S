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
    <meta property="og:image" content="{{ asset('favicon.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="CpsuVotewisely">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="CpsuVotewisely - Cloud-Based Real-Time Voting System">
    <meta name="twitter:description"
        content="A secure and transparent digital voting platform for CPSU student council elections.">
    <meta name="twitter:image" content="{{ asset('favicon.png') }}">

    <title>CpsuVotewisely - Cloud-Based Real-Time Voting System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    </style>
</head>

<body class="antialiased">
    <!-- React App Mount Point -->
    <div id="landing-root"></div>

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
