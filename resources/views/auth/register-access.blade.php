<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Access - CpsuVotewisely.com</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cpsu-green: #166534;
            --cpsu-gold: #facc15;
            --cpsu-green-light: #22c55e;
            --cpsu-green-dark: #14532d;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }
        .preloader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 50%, var(--cpsu-green-dark) 100%);
            background-size: 200% 200%;
            display: flex; align-items: center; justify-content: center; z-index: 9999;
            opacity: 1; visibility: visible; transition: opacity 0.5s ease, visibility 0.5s ease;
            animation: gradientShift 8s ease infinite;
        }
        .preloader.hidden { opacity: 0; visibility: hidden; }
        @keyframes gradientShift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        .preloader-content { text-align: center; color: white; position: relative; z-index: 1; }
        .preloader-spinner { width: 80px; height: 80px; margin: 0 auto 1.5rem; position: relative; }
        .spinner-ring {
            position: absolute; width: 100%; height: 100%;
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top-color: var(--cpsu-gold); border-right-color: var(--cpsu-gold);
            border-radius: 50%; animation: spin 1.2s linear infinite;
        }
        .spinner-ring:nth-child(2) {
            width: 70%; height: 70%; top: 15%; left: 15%; border-width: 3px;
            border-top-color: white; border-right-color: white;
            border-bottom-color: rgba(255, 255, 255, 0.3); border-left-color: rgba(255, 255, 255, 0.3);
            animation-duration: 0.8s; animation-direction: reverse;
        }
        .spinner-ring:nth-child(3) {
            width: 40%; height: 40%; top: 30%; left: 30%; border-width: 2px;
            border-top-color: var(--cpsu-gold); border-right-color: var(--cpsu-gold);
            border-bottom-color: transparent; border-left-color: transparent;
            animation-duration: 0.6s;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .preloader-text {
            font-size: 1.5rem; font-weight: 700; margin-bottom: 0.75rem; letter-spacing: 0.5px;
            font-family: 'Playfair Display', serif; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .preloader-subtext { font-size: 0.9375rem; opacity: 0.9; color: rgba(255, 255, 255, 0.95); font-weight: 500; }
        .preloader-progress-bar {
            position: absolute; bottom: 0; left: 0; width: 100%; height: 4px;
            background: rgba(255, 255, 255, 0.1); overflow: hidden;
        }
        .preloader-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--cpsu-gold) 0%, white 50%, var(--cpsu-gold) 100%);
            background-size: 200% 100%; width: 0%;
            animation: progressBar 2s ease-in-out infinite, progressFill 1.5s ease forwards;
        }
        @keyframes progressBar { 0% { background-position: 0% 0; } 100% { background-position: 200% 0; } }
        @keyframes progressFill { 0% { width: 0%; } 100% { width: 100%; } }
        .background-decoration {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        .background-decoration::before {
            content: ''; position: absolute; top: -50%; right: -20%; width: 800px; height: 800px;
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.08) 0%, rgba(250, 204, 21, 0.05) 100%);
            border-radius: 50%; filter: blur(80px);
        }
        .background-decoration::after {
            content: ''; position: absolute; bottom: -30%; left: -10%; width: 600px; height: 600px;
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.05) 0%, rgba(22, 101, 52, 0.08) 100%);
            border-radius: 50%; filter: blur(80px);
        }
        .access-wrapper {
            display: grid; grid-template-columns: 1fr 1fr;
            max-width: 1200px; width: 100%; min-height: 550px; position: relative; z-index: 1;
            border-radius: 1rem; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .left-section {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            padding: 3rem; display: flex; flex-direction: column; justify-content: center; color: white;
            position: relative; overflow: hidden;
        }
        .left-section::before {
            content: ''; position: absolute; top: 0; right: 0; width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.2) 0%, transparent 70%);
            border-radius: 50%; transform: translate(30%, -30%);
        }
        .left-section::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 150px; height: 150px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.15) 0%, transparent 70%);
            border-radius: 50%; transform: translate(-30%, 30%);
        }
        .left-content { position: relative; z-index: 1; }
        .logo-badge {
            width: 56px; height: 56px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);
            border-radius: 14px; display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .logo-badge svg { width: 32px; height: 32px; }
        .left-section h1 {
            font-family: 'Playfair Display', serif; font-size: 2.25rem; font-weight: 700; line-height: 1.2;
            margin-bottom: 0.875rem; letter-spacing: -0.02em;
        }
        .left-section .subtitle { font-size: 1rem; color: rgba(255, 255, 255, 0.9); margin-bottom: 1.5rem; line-height: 1.6; }
        .left-section .institution { font-size: 0.9375rem; font-weight: 600; color: rgba(255, 255, 255, 0.95); margin-bottom: 2rem; letter-spacing: 0.05em; }
        .features-list { list-style: none; display: flex; flex-direction: column; gap: 1rem; }
        .features-list li { display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; color: rgba(255, 255, 255, 0.85); }
        .features-list svg { width: 18px; height: 18px; color: var(--cpsu-gold); flex-shrink: 0; }
        .right-section {
            background: white; padding: 2.5rem; display: flex; flex-direction: column; justify-content: center;
            overflow-y: auto; max-height: 95vh;
        }
        .form-header { text-align: center; margin-bottom: 1.5rem; }
        .form-header h2 { font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem; letter-spacing: -0.01em; }
        .form-header p { font-size: 0.875rem; color: #64748b; }
        .form-divider { height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0, transparent); margin: 1rem 0; }
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem; }
        .input-container { position: relative; }
        .input-icon {
            position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px; color: #94a3b8; pointer-events: none; transition: color 0.2s;
        }
        .form-input {
            width: 100%; padding: 0.7rem 1rem 0.7rem 2.75rem; font-size: 0.9375rem;
            border: 1.5px solid #e2e8f0; border-radius: 0.75rem; background: #fff; color: #1e293b;
            transition: all 0.2s; outline: none;
        }
        .form-input:focus {
            border-color: var(--cpsu-green); box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.15);
        }
        .input-container:focus-within .input-icon { color: var(--cpsu-green); }
        .submit-btn {
            width: 100%; padding: 0.875rem; font-size: 0.9375rem; font-weight: 600; color: white;
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-dark) 100%);
            border: none; border-radius: 0.75rem; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(22, 101, 52, 0.25); margin-top: 0.5rem;
        }
        .submit-btn:hover { background: var(--cpsu-green-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(22, 101, 52, 0.3); }
        .submit-btn:active { transform: translateY(0); }
        .form-footer { margin-top: 1.25rem; text-align: center; font-size: 0.875rem; color: #64748b; }
        .form-footer a { color: var(--cpsu-green); text-decoration: none; font-weight: 500; transition: color 0.2s; }
        .form-footer a:hover { color: var(--cpsu-green-dark); }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 1rem;
            color: #94a3b8; text-decoration: none; font-size: 0.875rem; transition: color 0.2s;
        }
        .back-link:hover { color: var(--cpsu-green); }
        .alert {
            padding: 0.875rem 1rem; border-radius: 0.75rem; margin-bottom: 1rem;
            display: flex; align-items: start; gap: 0.75rem; font-size: 0.875rem;
        }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 0.125rem; }
        .alert ul { list-style: none; margin: 0; padding: 0; }
        .alert li { margin-bottom: 0.25rem; }
        .alert li:last-child { margin-bottom: 0; }
        @media (max-width: 968px) {
            body { padding: 0.75rem; }
            .access-wrapper { grid-template-columns: 1fr; max-width: 500px; min-height: auto; }
            .left-section { border-radius: 1rem 1rem 0 0; padding: 2rem 1.5rem; }
            .right-section { border-radius: 0 0 1rem 1rem; padding: 1.75rem 1.5rem; max-height: none; }
            .left-section h1 { font-size: 1.75rem; }
        }
        @media (max-width: 768px) {
            body { padding: 0.5rem; align-items: flex-start; padding-top: 0.75rem; }
            .access-wrapper { max-width: 100%; }
            .left-section { padding: 1.5rem 1.25rem; }
            .right-section { padding: 1.5rem 1.25rem; }
            .form-header h2 { font-size: 1.375rem; }
        }
    </style>
</head>
<body>
    <div class="preloader" id="preloader">
        <div class="preloader-content">
            <div class="preloader-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <div class="preloader-text">CpsuVotewisely.com</div>
            <div class="preloader-subtext">Verifying access...</div>
        </div>
        <div class="preloader-progress-bar">
            <div class="preloader-progress-fill" id="preloaderProgress"></div>
        </div>
    </div>
    <div class="background-decoration"></div>
    <div class="access-wrapper">
        <div class="left-section">
            <div class="left-content">
                <div class="logo-badge">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18 13h-.68l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2H6l-3 3v4c0 1.1.89 2 1.99 2H19c1.1 0 2-.89 2-2v-4l-3-3zm-1-5.05l-4.95 4.95-3.54-3.54 4.95-4.95 3.54 3.54zm-4.24-5.66L6.39 8.66a.996.996 0 000 1.41l4.95 4.95c.39.39 1.02.39 1.41 0l6.36-6.36a.996.996 0 000-1.41l-4.95-4.95a.996.996 0 00-1.41 0z"/>
                    </svg>
                </div>
                <h1>Cloud Based<br>Real-Time Voting<br>System</h1>
                <p class="subtitle">Registration is restricted. Enter the access code provided by your administrator to create an account.</p>
                <p class="institution">CENTRAL PHILIPPINE STATE UNIVERSITY</p>
                <ul class="features-list">
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Secure registration</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Access code required</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Verified platform</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="right-section">
            <div class="form-header">
                <h2>Registration Access</h2>
                <p>Enter your access code to continue to registration</p>
            </div>
            <div class="form-divider"></div>
            @if ($errors->any())
                <div class="alert alert-error">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            <form method="POST" action="{{ route('register.access.verify') }}" id="accessForm">
                @csrf
                <div class="form-group">
                    <label for="access_code" class="form-label">Access Code</label>
                    <div class="input-container">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input type="text"
                               id="access_code"
                               name="access_code"
                               value="{{ old('access_code') }}"
                               required
                               autofocus
                               autocomplete="off"
                               class="form-input"
                               placeholder="Enter access code">
                    </div>
                </div>
                <button type="submit" class="submit-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Continue to Registration
                </button>
            </form>
            <div class="form-footer">
                <p>Already have an account? <a href="{{ route('login') }}">Sign in here</a></p>
                <a href="{{ url('/') }}" class="back-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to home
                </a>
            </div>
        </div>
    </div>
    <script>
        (function() {
            var preloader = document.getElementById('preloader');
            var progressFill = document.getElementById('preloaderProgress');
            if (progressFill) progressFill.style.width = '0%';
            window.addEventListener('load', function() {
                if (progressFill) progressFill.style.width = '100%';
                setTimeout(function() {
                    if (preloader) preloader.classList.add('hidden');
                }, 400);
            });
            setTimeout(function() {
                if (preloader && !preloader.classList.contains('hidden')) {
                    if (progressFill) progressFill.style.width = '100%';
                    if (preloader) preloader.classList.add('hidden');
                }
            }, 3000);
        })();
    </script>
</body>
</html>
