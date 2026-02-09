<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - CpsuVotewisely.com</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cpsu-green: #166534;
            --cpsu-gold: #facc15;
            --cpsu-green-light: #22c55e;
            --cpsu-green-dark: #14532d;
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

        /* Professional Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 50%, var(--cpsu-green-dark) 100%);
            background-size: 200% 200%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            visibility: visible;
            transition: opacity 0.5s ease, visibility 0.5s ease;
            animation: gradientShift 8s ease infinite;
        }

        .preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .preloader-content {
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
        }

        .preloader-logo-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
        }

        .preloader-logo {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: preloaderFloat 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .preloader-logo::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shine 3s ease-in-out infinite;
        }

        .preloader-logo svg {
            width: 60px;
            height: 60px;
            color: white;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        @keyframes preloaderFloat {

            0%,
            100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-15px) scale(1.05);
            }
        }

        @keyframes shine {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .preloader-spinner {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            position: relative;
        }

        .spinner-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top-color: #facc15;
            border-right-color: #facc15;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
        }

        .spinner-ring:nth-child(2) {
            width: 70%;
            height: 70%;
            top: 15%;
            left: 15%;
            border-width: 3px;
            border-top-color: white;
            border-right-color: white;
            border-bottom-color: rgba(255, 255, 255, 0.3);
            border-left-color: rgba(255, 255, 255, 0.3);
            animation-duration: 0.8s;
            animation-direction: reverse;
        }

        .spinner-ring:nth-child(3) {
            width: 40%;
            height: 40%;
            top: 30%;
            left: 30%;
            border-width: 2px;
            border-top-color: #facc15;
            border-right-color: #facc15;
            border-bottom-color: transparent;
            border-left-color: transparent;
            animation-duration: 0.6s;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .preloader-text {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            letter-spacing: 0.5px;
            font-family: 'Playfair Display', serif;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            animation: textPulse 2s ease-in-out infinite;
        }

        @keyframes textPulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .preloader-subtext {
            font-size: 0.9375rem;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .preloader-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .preloader-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #facc15 0%, white 50%, #facc15 100%);
            background-size: 200% 100%;
            width: 0%;
            animation: progressBar 2s ease-in-out infinite, progressFill 1.5s ease forwards;
            box-shadow: 0 0 20px rgba(250, 204, 21, 0.6);
        }

        @keyframes progressBar {
            0% {
                background-position: 0% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        @keyframes progressFill {
            0% {
                width: 0%;
            }

            100% {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .preloader-logo-container {
                width: 100px;
                height: 100px;
                margin-bottom: 1.5rem;
            }

            .preloader-logo svg {
                width: 50px;
                height: 50px;
            }

            .preloader-spinner {
                width: 70px;
                height: 70px;
                margin-bottom: 1.25rem;
            }

            .preloader-text {
                font-size: 1.25rem;
            }

            .preloader-subtext {
                font-size: 0.875rem;
            }

            .preloader-progress-bar {
                height: 3px;
            }
        }

        @media (max-width: 480px) {
            .preloader-logo-container {
                width: 80px;
                height: 80px;
                margin-bottom: 1.25rem;
            }

            .preloader-logo {
                border-radius: 16px;
            }

            .preloader-logo svg {
                width: 40px;
                height: 40px;
            }

            .preloader-spinner {
                width: 60px;
                height: 60px;
                margin-bottom: 1rem;
            }

            .preloader-text {
                font-size: 1.125rem;
            }

            .preloader-subtext {
                font-size: 0.8125rem;
            }
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
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.08) 0%, rgba(250, 204, 21, 0.05) 100%);
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
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.05) 0%, rgba(22, 101, 52, 0.08) 100%);
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
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .left-section {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            border-radius: 0;
            padding: 3.5rem;
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
            background: radial-gradient(circle, rgba(250, 204, 21, 0.2) 0%, transparent 70%);
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
            background: radial-gradient(circle, rgba(250, 204, 21, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-30%, 30%);
        }

        .left-content {
            position: relative;
            z-index: 1;
        }

        .logo-badge {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .logo-badge svg {
            width: 32px;
            height: 32px;
        }

        .left-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.875rem;
            letter-spacing: -0.02em;
        }

        .left-section .subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .left-section .institution {
            font-size: 0.9375rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2.5rem;
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
            border-radius: 0;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            text-align: center;
            margin-bottom: 1.75rem;
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
            margin: 1.25rem 0;
        }

        .form-group {
            margin-bottom: 1.25rem;
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
            border-radius: 0.75rem;
            background: #fff;
            color: #1e293b;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--cpsu-green);
            box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.15);
        }

        .form-input:focus+.input-icon,
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
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            flex-wrap: wrap;
            gap: 0.5rem;
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
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-dark) 100%);
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 14px rgba(22, 101, 52, 0.25);
        }

        .submit-btn:hover {
            background: var(--cpsu-green-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 1.25rem;
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

        /* Mobile Responsive Design */
        @media (max-width: 968px) {
            body {
                padding: 0.75rem;
            }

            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 500px;
                min-height: auto;
                border-radius: 1rem;
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

            .left-section .subtitle {
                font-size: 0.9375rem;
            }

            .left-section .institution {
                font-size: 0.875rem;
                margin-bottom: 2rem;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
                align-items: flex-start;
                padding-top: 1rem;
            }

            .login-wrapper {
                max-width: 100%;
                min-height: auto;
            }

            .left-section {
                padding: 2rem 1.5rem;
            }

            .logo-badge {
                width: 48px;
                height: 48px;
                margin-bottom: 1.25rem;
            }

            .logo-badge svg {
                width: 24px;
                height: 24px;
            }

            .left-section h1 {
                font-size: 1.75rem;
                margin-bottom: 0.75rem;
            }

            .left-section .subtitle {
                font-size: 0.875rem;
                margin-bottom: 1.25rem;
            }

            .left-section .institution {
                font-size: 0.8125rem;
                margin-bottom: 1.75rem;
            }

            .features-list {
                gap: 0.875rem;
            }

            .features-list li {
                font-size: 0.8125rem;
            }

            .features-list svg {
                width: 16px;
                height: 16px;
            }

            .right-section {
                padding: 1.75rem 1.5rem;
            }

            .form-header {
                margin-bottom: 1.5rem;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }

            .form-header p {
                font-size: 0.8125rem;
            }

            .form-divider {
                margin: 1rem 0;
            }

            .form-group {
                margin-bottom: 1.125rem;
            }

            .form-label {
                font-size: 0.8125rem;
                margin-bottom: 0.4375rem;
            }

            .form-input {
                padding: 0.6875rem 0.875rem 0.6875rem 2.5rem;
                font-size: 0.875rem;
            }

            .input-icon {
                width: 16px;
                height: 16px;
                left: 0.875rem;
            }

            .password-toggle {
                width: 16px;
                height: 16px;
                right: 0.875rem;
            }

            .form-options {
                margin-bottom: 1.125rem;
                font-size: 0.8125rem;
            }

            .submit-btn {
                padding: 0.8125rem;
                font-size: 0.875rem;
            }

            .submit-btn svg {
                width: 16px;
                height: 16px;
            }

            .form-footer {
                margin-top: 1.125rem;
                font-size: 0.8125rem;
            }

            .alert {
                padding: 0.75rem 0.875rem;
                font-size: 0.8125rem;
                margin-bottom: 1.25rem;
            }

            .alert svg {
                width: 16px;
                height: 16px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 0.5rem;
                padding-top: 0.75rem;
            }

            .login-wrapper {
                border-radius: 0.75rem;
            }

            .left-section {
                padding: 1.75rem 1.25rem;
                border-radius: 0.75rem 0.75rem 0 0;
            }

            .logo-badge {
                width: 44px;
                height: 44px;
                margin-bottom: 1rem;
            }

            .logo-badge svg {
                width: 22px;
                height: 22px;
            }

            .left-section h1 {
                font-size: 1.5rem;
                margin-bottom: 0.625rem;
            }

            .left-section .subtitle {
                font-size: 0.8125rem;
                margin-bottom: 1rem;
            }

            .left-section .institution {
                font-size: 0.75rem;
                margin-bottom: 1.5rem;
            }

            .features-list {
                gap: 0.75rem;
            }

            .features-list li {
                font-size: 0.75rem;
            }

            .features-list svg {
                width: 14px;
                height: 14px;
            }

            .right-section {
                padding: 1.5rem 1.25rem;
                border-radius: 0 0 0.75rem 0.75rem;
            }

            .form-header {
                margin-bottom: 1.25rem;
            }

            .form-header h2 {
                font-size: 1.375rem;
            }

            .form-header p {
                font-size: 0.75rem;
            }

            .form-divider {
                margin: 0.875rem 0;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .form-input {
                padding: 0.625rem 0.75rem 0.625rem 2.25rem;
                font-size: 0.8125rem;
            }

            .input-icon {
                left: 0.75rem;
            }

            .password-toggle {
                right: 0.75rem;
            }

            .submit-btn {
                padding: 0.75rem;
                font-size: 0.8125rem;
            }
        }

        @media (max-width: 480px) {
            .left-section {
                padding: 1.5rem 1rem;
            }

            .logo-badge {
                width: 40px;
                height: 40px;
            }

            .logo-badge svg {
                width: 20px;
                height: 20px;
            }

            .left-section h1 {
                font-size: 1.375rem;
            }

            .left-section .subtitle {
                font-size: 0.75rem;
            }

            .left-section .institution {
                font-size: 0.6875rem;
            }

            .features-list li {
                font-size: 0.6875rem;
            }

            .right-section {
                padding: 1.25rem 1rem;
            }

            .form-header h2 {
                font-size: 1.25rem;
            }

            .form-input {
                padding: 0.5625rem 0.6875rem 0.5625rem 2rem;
                font-size: 0.75rem;
            }

            .input-icon {
                width: 14px;
                height: 14px;
            }

            .password-toggle {
                width: 14px;
                height: 14px;
            }

            .submit-btn {
                padding: 0.6875rem;
                font-size: 0.75rem;
            }

            .form-footer {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 360px) {
            .left-section {
                padding: 1.25rem 0.875rem;
            }

            .left-section h1 {
                font-size: 1.25rem;
            }

            .right-section {
                padding: 1.125rem 0.875rem;
            }

            .form-header h2 {
                font-size: 1.125rem;
            }

            .form-input {
                padding: 0.5rem 0.625rem 0.5rem 1.875rem;
                font-size: 0.6875rem;
            }

            .submit-btn {
                padding: 0.625rem;
                font-size: 0.6875rem;
            }
        }
    </style>
</head>

<body>
    <!-- Professional Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-content">
            <div class="preloader-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <div class="preloader-text">CpsuVotewisely.com</div>
            <div class="preloader-subtext">Loading your secure dashboard...</div>
        </div>
        <div class="preloader-progress-bar">
            <div class="preloader-progress-fill" id="preloaderProgress"></div>
        </div>
    </div>
    <div class="background-decoration"></div>
    <div class="login-wrapper">
        <!-- Left Section: Branding -->
        <div class="left-section">
            <div class="left-content">
                <div class="logo-badge">
                    <!-- Voting Ballot Box Icon - Same as Landing Page -->
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M18 13h-.68l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2H6l-3 3v4c0 1.1.89 2 1.99 2H19c1.1 0 2-.89 2-2v-4l-3-3zm-1-5.05l-4.95 4.95-3.54-3.54 4.95-4.95 3.54 3.54zm-4.24-5.66L6.39 8.66a.996.996 0 000 1.41l4.95 4.95c.39.39 1.02.39 1.41 0l6.36-6.36a.996.996 0 000-1.41l-4.95-4.95a.996.996 0 00-1.41 0z" />
                    </svg>
                </div>
                <h1>Cloud Based<br>Real-Time Voting<br>System</h1>
                <p class="subtitle">Secure, transparent, and efficient digital voting platform for university elections.
                </p>
                <p class="institution">CENTRAL PHILIPPINE STATE UNIVERSITY</p>
                <ul class="features-list">
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        <span>Enterprise-grade security</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <span>Real-time result tracking</span>
                    </li>
                    <li>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Student ID</label>
                    <div class="input-container">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <input type="text" id="email" name="email" value="{{ old('email') }}" required
                            autofocus autocomplete="username" class="form-input" placeholder="Enter Student ID ">
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5">Students: Use your Student ID </p>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-container">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                        <input type="password" id="password" name="password" required
                            autocomplete="current-password" class="form-input" placeholder="Enter your password">
                        <svg id="togglePassword" class="password-toggle" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path id="eyeIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                            <path id="eyeSlashIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                style="display: none;"></path>
                        </svg>
                    </div>

                    <button type="submit" class="submit-btn" style="margin-top: 1.25rem;">
                        <svg width="18" height="18" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Sign In
                    </button>
            </form>

            <div class="form-footer">

                <a href="{{ url('/') }}" class="back-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to home
                </a>
            </div>
        </div>
    </div>

    <script>
        // Professional Preloader
        (function() {
            const preloader = document.getElementById('preloader');
            const progressFill = document.getElementById('preloaderProgress');
            const preloaderText = document.querySelector('.preloader-text');
            const preloaderSubtext = document.querySelector('.preloader-subtext');

            // Simulate loading progress
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += Math.random() * 12;
                if (progress >= 95) {
                    progress = 95;
                }
                if (progressFill) {
                    progressFill.style.width = progress + '%';
                }
            }, 150);

            // Complete on page load
            window.addEventListener('load', function() {
                clearInterval(progressInterval);

                // Complete progress bar
                if (progressFill) {
                    progressFill.style.width = '100%';
                }

                // Hide preloader after a short delay
                setTimeout(() => {
                    if (preloader) {
                        preloader.classList.add('hidden');
                        // Remove from DOM after animation
                        setTimeout(() => {
                            if (preloader && preloader.parentNode) {
                                preloader.style.display = 'none';
                            }
                        }, 500);
                    }
                }, 400);
            });

            // Fallback: Hide preloader after max 3 seconds
            setTimeout(() => {
                if (preloader && !preloader.classList.contains('hidden')) {
                    clearInterval(progressInterval);
                    if (progressFill) {
                        progressFill.style.width = '100%';
                    }
                    setTimeout(() => {
                        preloader.classList.add('hidden');
                        setTimeout(() => {
                            if (preloader && preloader.parentNode) {
                                preloader.style.display = 'none';
                            }
                        }, 500);
                    }, 200);
                }
            }, 3000);

            // Show preloader on login form submission
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    // Update preloader text for login
                    if (preloaderText) preloaderText.textContent = 'Signing In...';
                    if (preloaderSubtext) preloaderSubtext.textContent = 'Verifying your credentials';

                    // Reset and show preloader
                    if (preloader) {
                        preloader.style.display = 'flex';
                        preloader.classList.remove('hidden');
                    }
                    if (progressFill) {
                        progressFill.style.animation = 'none';
                        progressFill.offsetHeight; // Trigger reflow
                        progressFill.style.animation =
                            'progressBar 2s ease-in-out infinite, progressFill 3s ease forwards';
                    }
                });
            }
        })();

        // Password toggle functionality
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
