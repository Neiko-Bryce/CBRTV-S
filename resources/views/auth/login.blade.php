<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - CPSU Voting System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cpsu-green: #006633;
            --cpsu-gold: #D4AF37;
            --cpsu-green-light: #008844;
            --cpsu-green-dark: #004422;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
        .background-decoration {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        .background-decoration::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.05) 0%, rgba(212, 175, 55, 0.05) 100%);
            border-radius: 50%;
            filter: blur(80px);
        }
        .background-decoration::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, rgba(0, 102, 51, 0.05) 100%);
            border-radius: 50%;
            filter: blur(80px);
        }
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            min-height: 600px;
            position: relative;
            z-index: 1;
        }
        .left-section {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            border-radius: 1rem 0 0 1rem;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        .left-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-30%, 30%);
        }
        .left-content {
            position: relative;
            z-index: 1;
        }
        .logo-badge {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .logo-badge svg {
            width: 32px;
            height: 32px;
        }
        .left-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }
        .left-section .subtitle {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .left-section .institution {
            font-size: 1rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 3rem;
            letter-spacing: 0.05em;
        }
        .features-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .features-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.85);
        }
        .features-list svg {
            width: 18px;
            height: 18px;
            color: var(--cpsu-gold);
            flex-shrink: 0;
        }
        .right-section {
            background: white;
            border-radius: 0 1rem 1rem 0;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        }
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            letter-spacing: -0.01em;
        }
        .form-header p {
            font-size: 0.875rem;
            color: #64748b;
        }
        .form-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 1.5rem 0;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }
        .input-container {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            font-size: 0.9375rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.5rem;
            background: #fff;
            color: #1e293b;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: var(--cpsu-green);
            box-shadow: 0 0 0 3px rgba(0, 102, 51, 0.1);
        }
        .form-input:focus + .input-icon,
        .input-container:focus-within .input-icon {
            color: var(--cpsu-green);
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s;
        }
        .password-toggle:hover {
            color: var(--cpsu-green);
        }
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--cpsu-green);
            cursor: pointer;
        }
        .checkbox-group label {
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }
        .forgot-link {
            color: var(--cpsu-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: var(--cpsu-green-dark);
        }
        .submit-btn {
            width: 100%;
            padding: 0.875rem;
            font-size: 0.9375rem;
            font-weight: 600;
            color: white;
            background: var(--cpsu-green);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .submit-btn:hover {
            background: var(--cpsu-green-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.2);
        }
        .submit-btn:active {
            transform: translateY(0);
        }
        .form-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
        }
        .form-footer a {
            color: var(--cpsu-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .form-footer a:hover {
            color: var(--cpsu-green-dark);
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: var(--cpsu-green);
        }
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: start;
            gap: 0.75rem;
            font-size: 0.875rem;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        .alert svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 0.125rem;
        }
        @media (max-width: 968px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 500px;
            }
            .left-section {
                border-radius: 1rem 1rem 0 0;
                padding: 2.5rem 2rem;
            }
            .right-section {
                border-radius: 0 0 1rem 1rem;
                padding: 2rem;
            }
            .left-section h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="background-decoration"></div>
    <div class="login-wrapper">
        <!-- Left Section: Branding -->
        <div class="left-section">
            <div class="left-content">
                <div class="logo-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1>Cloud Based<br>Real-Time Voting<br>System</h1>
                <p class="subtitle">Secure, transparent, and efficient digital voting platform for university elections.</p>
                <p class="institution">CENTRAL PHILIPPINE STATE UNIVERSITY</p>
                <ul class="features-list">
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Enterprise-grade security</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Real-time result tracking</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Verified and audited platform</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Section: Login Form -->
        <div class="right-section">
            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Sign in to access your dashboard</p>
            </div>

            <div class="form-divider"></div>

            @if ($errors->any())
                <div class="alert alert-error">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-container">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               autocomplete="username"
                               class="form-input"
                               placeholder="Enter your email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-container">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               autocomplete="current-password"
                               class="form-input"
                               placeholder="Enter your password">
                        <svg id="togglePassword" class="password-toggle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path id="eyeIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            <path id="eyeSlashIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" style="display: none;"></path>
                        </svg>
                    </div>
                </div>

                <div class="form-options">
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="submit-btn">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Sign In
                </button>
            </form>

            <div class="form-footer">
                <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
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
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyePath = document.getElementById('eyePath');
        const eyeSlashIcon = document.getElementById('eyeSlashIcon');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    if (eyeIcon) eyeIcon.style.display = 'none';
                    if (eyePath) eyePath.style.display = 'none';
                    if (eyeSlashIcon) eyeSlashIcon.style.display = 'block';
                } else {
                    if (eyeIcon) eyeIcon.style.display = 'block';
                    if (eyePath) eyePath.style.display = 'block';
                    if (eyeSlashIcon) eyeSlashIcon.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
