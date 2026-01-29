<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CivicVote - Professional Cloud-Based Real-Time Voting System. Secure, transparent, and efficient digital voting platform for schools, universities, and community organizations.">
    <meta name="keywords" content="voting system, digital voting, online elections, student council, secure voting, real-time results">
    <meta name="author" content="CivicVote">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="CivicVote - Cloud-Based Real-Time Voting System">
    <meta property="og:description" content="Secure, transparent, and efficient digital voting platform for schools, universities, and community organizations.">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">
    
    <title>VoteWisely - Cloud-Based Real-Time Voting System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Prevent caching issues during development -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
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
            to { transform: rotate(360deg); }
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
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; padding: 2rem; text-align: center; background: #166534; color: white;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6L6 18M6 6l12 12"/>
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
