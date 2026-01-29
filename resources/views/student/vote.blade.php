<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vote - {{ $election->election_name }} - CPSU Voting System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            /* Government Green Palette */
            --gov-green-50: #f0fdf4;
            --gov-green-100: #dcfce7;
            --gov-green-200: #bbf7d0;
            --gov-green-400: #4ade80;
            --gov-green-500: #22c55e;
            --gov-green-600: #16a34a;
            --gov-green-700: #15803d;
            --gov-green-800: #166534;
            --gov-green-900: #14532d;
            --gov-green-950: #052e16;
            /* Government Gold Palette */
            --gov-gold-300: #fde047;
            --gov-gold-400: #facc15;
            --gov-gold-500: #eab308;
            --gov-gold-600: #ca8a04;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .heading-font {
            font-family: 'Playfair Display', serif;
        }
        
        /* Landing Page Style Header */
        .main-header {
            background: linear-gradient(to right, var(--gov-green-900), var(--gov-green-800));
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .main-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gov-gold-400) 0%, var(--gov-gold-500) 50%, var(--gov-gold-600) 100%);
        }
        
        /* Enhanced Election Info Card */
        .election-info-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(20, 83, 45, 0.08);
            overflow: hidden;
            position: relative;
        }
        .election-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gov-green-800) 0%, var(--gov-green-600) 50%, var(--gov-gold-400) 100%);
        }
        
        /* Enhanced Position Cards */
        .position-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(20, 83, 45, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        /* Enhanced Countdown */
        .countdown-container {
            background: linear-gradient(135deg, rgba(20, 83, 45, 0.05) 0%, rgba(234, 179, 8, 0.05) 100%);
            border: 1px solid rgba(20, 83, 45, 0.1);
            border-radius: 1rem;
            padding: 1rem;
        }
        .countdown-timer {
            font-family: 'Inter', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gov-green-800);
        }
        .countdown-label {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }
        
        /* Enhanced Alert Messages */
        .alert-success {
            background: linear-gradient(to right, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 1rem;
            border-left: 4px solid var(--gov-green-600);
        }
        .alert-error {
            background: linear-gradient(to right, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
            border-radius: 1rem;
            border-left: 4px solid #ef4444;
        }
        
        /* Candidate Card Styles */
        .candidate-card {
            position: relative;
            display: flex;
            flex-direction: column;
            border: 2px solid #e5e7eb;
            border-radius: 1rem;
            background: white;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        .candidate-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
            border-color: #d1d5db;
        }
        .candidate-card.selected {
            border-color: var(--gov-green-700);
            border-width: 3px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            box-shadow: 0 8px 24px -6px rgba(20, 83, 45, 0.25);
        }
        .candidate-card.selected::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gov-green-700) 0%, var(--gov-green-600) 50%, var(--gov-gold-400) 100%);
            z-index: 1;
        }
        
        /* Photo Styles */
        .candidate-photo {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .candidate-photo-placeholder {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .candidate-photo-placeholder svg {
            width: 50px;
            height: 50px;
            color: #9ca3af;
        }
        
        /* Checkmark Badge */
        .checkmark-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--gov-green-700) 0%, var(--gov-green-600) 100%);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(20, 83, 45, 0.4);
            z-index: 10;
            border: 3px solid white;
            animation: checkmarkPop 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        @keyframes checkmarkPop {
            0% { transform: scale(0) rotate(-180deg); opacity: 0; }
            60% { transform: scale(1.15) rotate(10deg); }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .checkmark-badge svg {
            width: 16px;
            height: 16px;
            color: white;
            stroke-width: 3;
        }
        .candidate-card.selected .checkmark-badge {
            display: flex;
        }
        
        /* Candidate Info */
        .candidate-info {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .candidate-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            line-height: 1.3;
        }
        .candidate-card.selected .candidate-name {
            color: var(--gov-green-800);
        }
        .candidate-partylist {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: #4b5563;
            padding: 0.375rem 0.625rem;
            background-color: #f9fafb;
            border-radius: 0.5rem;
        }
        .candidate-platform {
            font-size: 0.75rem;
            color: #6b7280;
            line-height: 1.5;
            padding: 0.5rem;
            background-color: #fafafa;
            border-radius: 0.5rem;
            border-left: 3px solid #e5e7eb;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .candidate-card.selected .candidate-platform {
            background-color: var(--gov-green-50);
            border-left-color: var(--gov-green-600);
        }
        
        /* Enhanced Modal Styles */
        #voteSummaryModal, #successModal {
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            animation: slideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-radius: 1.5rem !important;
            overflow: hidden;
        }
        @keyframes slideUp {
            from { transform: translateY(50px) scale(0.9); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
        .summary-position-card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border: 1px solid #e5e7eb;
            border-left: 4px solid var(--gov-green-700);
            border-radius: 1rem;
        }
        .summary-candidate-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
            transition: all 0.2s ease;
        }
        .summary-candidate-item:hover {
            border-color: var(--gov-green-600);
            box-shadow: 0 2px 8px rgba(20, 83, 45, 0.1);
            transform: translateX(4px);
        }
        .summary-check-icon {
            background: linear-gradient(135deg, var(--gov-green-700) 0%, var(--gov-green-600) 100%);
            box-shadow: 0 2px 8px rgba(20, 83, 45, 0.3);
        }
        
        /* Background Pattern */
        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.03;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23166534' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 0;
        }
        
        /* Page Preloader */
        .page-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--gov-green-950) 0%, var(--gov-green-900) 50%, var(--gov-green-950) 100%);
            background-size: 200% 200%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            animation: gradientShift 8s ease infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .page-preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .preloader-content {
            text-align: center;
            color: white;
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
            border-top-color: var(--gov-gold-400);
            border-right-color: var(--gov-gold-400);
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
            border-top-color: var(--gov-gold-400);
            border-right-color: var(--gov-gold-400);
            border-bottom-color: transparent;
            border-left-color: transparent;
            animation-duration: 0.6s;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .preloader-text {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-family: 'Playfair Display', serif;
            animation: textPulse 2s ease-in-out infinite;
        }
        @keyframes textPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .preloader-subtext {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
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
            background: linear-gradient(90deg, var(--gov-gold-400) 0%, white 50%, var(--gov-gold-400) 100%);
            background-size: 200% 100%;
            animation: progressBar 2s ease-in-out infinite, progressFill 1.5s ease forwards;
        }
        @keyframes progressBar {
            0% { background-position: 0% 0; }
            100% { background-position: 200% 0; }
        }
        @keyframes progressFill {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        @media (max-width: 640px) {
            .preloader-spinner { width: 60px; height: 60px; margin-bottom: 1rem; }
            .preloader-text { font-size: 1.25rem; }
            .preloader-subtext { font-size: 0.8rem; }
        }
        
        /* Mobile Responsive */
        @media (min-width: 475px) and (max-width: 640px) {
            .candidates-grid { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        }
        @media (min-width: 375px) and (max-width: 474px) {
            .candidates-grid { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
        }
        @media (max-width: 640px) {
            main { padding: 0.75rem !important; }
            .candidate-photo, .candidate-photo-placeholder { height: 90px; }
            .candidate-photo-placeholder svg { width: 28px; height: 28px; }
            .candidate-info { padding: 0.5rem 0.375rem; gap: 0.25rem; }
            .candidate-name { font-size: 0.7rem; line-height: 1.2; }
            .candidate-partylist { font-size: 0.6rem; padding: 0.25rem 0.375rem; gap: 0.25rem; }
            .candidate-partylist .partylist-dot { width: 0.5rem !important; height: 0.5rem !important; }
            .candidate-platform { font-size: 0.55rem; padding: 0.25rem; line-height: 1.3; -webkit-line-clamp: 2; }
            .checkmark-badge { width: 22px; height: 22px; top: 6px; right: 6px; border-width: 2px; }
            .checkmark-badge svg { width: 11px; height: 11px; stroke-width: 2.5; }
            .candidate-card { border-width: 1.5px; border-radius: 0.75rem; }
            .candidate-card.selected { border-width: 2px; }
            .position-card { padding: 0.75rem !important; margin-bottom: 0.75rem !important; border-radius: 1rem; }
            .countdown-timer { font-size: 0.85rem; }
            .countdown-label { font-size: 0.6rem; }
            .election-info-card { border-radius: 1rem; }
        }
        @media (min-width: 480px) and (max-width: 640px) {
            .candidate-photo, .candidate-photo-placeholder { height: 100px; }
            .candidate-info { padding: 0.5rem; }
            .candidate-name { font-size: 0.75rem; }
            .candidate-partylist { font-size: 0.65rem; }
            .candidate-platform { font-size: 0.6rem; }
        }
        @media (max-width: 480px) {
            .candidate-photo, .candidate-photo-placeholder { height: 85px; }
            .candidate-photo-placeholder svg { width: 24px; height: 24px; }
            .candidate-info { padding: 0.4375rem 0.3125rem; gap: 0.1875rem; }
            .candidate-name { font-size: 0.65rem; }
            .candidate-partylist { font-size: 0.55rem; padding: 0.1875rem 0.3125rem; }
            .candidate-platform { font-size: 0.5rem; padding: 0.1875rem; -webkit-line-clamp: 1; }
            .checkmark-badge { width: 20px; height: 20px; top: 4px; right: 4px; }
            .checkmark-badge svg { width: 10px; height: 10px; }
        }
        @media (max-width: 360px) {
            .candidate-photo, .candidate-photo-placeholder { height: 80px; }
            .candidate-info { padding: 0.375rem 0.25rem; }
            .candidate-name { font-size: 0.6rem; }
            .candidate-partylist { font-size: 0.5rem; }
            .candidate-platform { font-size: 0.45rem; }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Page Preloader -->
    <div class="page-preloader" id="pagePreloader">
        <div class="preloader-content">
            <div class="preloader-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <div class="preloader-text">Loading Ballot</div>
            <div class="preloader-subtext">Preparing your voting form...</div>
        </div>
        <div class="preloader-progress-bar">
            <div class="preloader-progress-fill"></div>
        </div>
    </div>
    
    <!-- Background Pattern -->
    <div class="bg-pattern"></div>
    
    <div class="min-h-screen relative z-10">
        <!-- Landing Page Style Header -->
        <header class="main-header">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <a href="{{ route('student.dashboard') }}" class="text-white/80 hover:text-white transition-all p-2 hover:bg-white/10 rounded-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gov-green-700 to-gov-green-900 rounded-xl flex items-center justify-center shadow-lg">
                            <!-- Voting Ballot Box Icon - Same as Landing Page -->
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 13h-.68l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2H6l-3 3v4c0 1.1.89 2 1.99 2H19c1.1 0 2-.89 2-2v-4l-3-3zm-1-5.05l-4.95 4.95-3.54-3.54 4.95-4.95 3.54 3.54zm-4.24-5.66L6.39 8.66a.996.996 0 000 1.41l4.95 4.95c.39.39 1.02.39 1.41 0l6.36-6.36a.996.996 0 000-1.41l-4.95-4.95a.996.996 0 00-1.41 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg sm:text-xl font-bold text-white heading-font">Cast Your Vote</h1>
                            <p class="text-xs sm:text-sm text-white/70 hidden sm:block line-clamp-1">{{ $election->election_name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-6 lg:py-8">
            @if(session('success'))
                <div class="mb-5 p-4 alert-success">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gov-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gov-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm sm:text-base text-gov-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 p-4 alert-error">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <p class="text-sm sm:text-base text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Enhanced Election Info Card -->
            <div class="election-info-card p-5 sm:p-6 lg:p-8 mb-5 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 break-words heading-font">{{ $election->election_name }}</h2>
                        @if($election->organization)
                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4 text-gov-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="font-medium break-words">{{ $election->organization->name }}</span>
                        </div>
                        @endif
                        <div class="flex flex-wrap gap-3 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($election->election_date)->format('M d, Y') }}</span>
                            </div>
                            @if($election->timestarted)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($election->timestarted)->format('g:i A') }} - {{ \Carbon\Carbon::parse($election->time_ended)->format('g:i A') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($endDateTime)
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <div class="countdown-container">
                            <p class="countdown-label mb-2 text-center sm:text-left">Time Remaining</p>
                            <div class="countdown-timer text-center sm:text-left" id="countdown" data-end-time="{{ $endDateTime->timestamp }}">
                                <span class="text-gray-400">Calculating...</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($hasVoted)
            <!-- Already Voted Message -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 sm:p-10 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-20 h-20 mx-auto mb-5 bg-gradient-to-br from-gov-green-500 to-gov-green-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3 heading-font">You Have Already Voted</h3>
                    <p class="text-gray-600 mb-6">Your votes have been submitted for this election. Thank you for participating!</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-gov-green-700 to-gov-green-800 text-white rounded-xl hover:from-gov-green-800 hover:to-gov-green-900 transition-all font-semibold shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Return to Dashboard
                    </a>
                </div>
            </div>
            @else
            <!-- Voting Form -->
            <form id="voteForm" method="POST" action="{{ route('student.submit-vote', $election->id) }}">
                @csrf
                
                @forelse($candidatesByPosition as $positionId => $candidates)
                    @php
                        $position = $candidates->first()->position;
                        $userVoteForPosition = collect($userVotes)->filter(function($vote) use ($candidates) {
                            return $candidates->pluck('id')->contains($vote);
                        })->first();
                    @endphp
                    @if($position)
                    <div class="position-card p-5 sm:p-6 lg:p-8 mb-5 sm:mb-6">
                        <div class="flex items-center gap-3 mb-4 sm:mb-5 pb-4 border-b-2 border-gray-100">
                            <div class="w-1 h-8 bg-gradient-to-b from-gov-green-600 to-gov-gold-400 rounded-full"></div>
                            <div>
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 heading-font">{{ $position->name }}</h3>
                                <span class="text-xs sm:text-sm text-gray-500">Select one candidate</span>
                            </div>
                        </div>
                        
                        <div class="candidates-grid grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 lg:gap-4">
                            @foreach($candidates as $candidate)
                            <label class="candidate-card {{ $userVoteForPosition == $candidate->id ? 'selected' : '' }}"
                                   onclick="handleCandidateClick(event, this, {{ $candidate->id }}, {{ $position->id }})">
                                <input type="radio" 
                                       name="votes[{{ $position->id }}]" 
                                       value="{{ $candidate->id }}"
                                       {{ $userVoteForPosition == $candidate->id ? 'checked' : '' }}
                                       data-candidate-id="{{ $candidate->id }}"
                                       data-position-id="{{ $position->id }}"
                                       style="display: none;">
                                
                                <!-- Candidate Photo -->
                                <div class="relative">
                                    @if($candidate->photo)
                                    <img src="{{ route('student.candidates.photo', ['path' => $candidate->photo]) }}" 
                                         alt="{{ $candidate->candidate_name }}" 
                                         class="candidate-photo"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="candidate-photo-placeholder" style="display: none;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    @else
                                    <div class="candidate-photo-placeholder">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                    
                                    <!-- Checkmark Badge -->
                                    <div class="checkmark-badge">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Candidate Info -->
                                <div class="candidate-info">
                                    <h6 class="candidate-name">{{ $candidate->candidate_name }}</h6>
                                    
                                    @if($candidate->partylist)
                                    <div class="candidate-partylist">
                                        @if($candidate->partylist->color)
                                        <div class="partylist-dot w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $candidate->partylist->color }};"></div>
                                        @endif
                                        <span class="font-medium truncate">{{ $candidate->partylist->name }}</span>
                                    </div>
                                    @else
                                    <div class="candidate-partylist">
                                        <span class="text-gray-400 italic font-medium">Independent</span>
                                    </div>
                                    @endif
                                    
                                    @if($candidate->platform)
                                    <p class="candidate-platform">{{ $candidate->platform }}</p>
                                    @else
                                    <p class="candidate-platform text-gray-400 italic">No platform available</p>
                                    @endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @empty
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-10 sm:p-14 text-center">
                    <svg class="w-14 h-14 sm:w-16 sm:h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 heading-font">No Candidates</h3>
                    <p class="text-sm sm:text-base text-gray-500">No candidates have been registered for this election yet.</p>
                </div>
                @endforelse

                @if($candidatesByPosition->count() > 0)
                <!-- Enhanced Action Buttons -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-4 sm:p-5 lg:p-6 sticky bottom-0 z-40 mt-6">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="button" 
                                    onclick="resetAllVotes()"
                                    id="resetBtn"
                                    class="flex-1 sm:flex-none px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all font-semibold text-sm shadow-sm hover:shadow flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Reset All</span>
                            </button>
                            <div class="hidden sm:flex items-center gap-2 px-4 py-2.5 bg-gray-50 rounded-xl border border-gray-200">
                                <span class="text-sm text-gray-600">
                                    <span id="voteCount" class="font-bold text-gov-green-700">0</span> / <span id="totalPositions" class="font-semibold">{{ $candidatesByPosition->count() }}</span> selected
                                </span>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="showVoteSummary()"
                                id="submitBtn"
                                class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-gradient-to-r from-gov-green-700 to-gov-green-800 text-white rounded-xl hover:from-gov-green-800 hover:to-gov-green-900 transition-all font-semibold text-sm sm:text-base shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Submit Votes</span>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500 text-center sm:hidden mt-3 pt-3 border-t border-gray-200">
                        <span id="voteCountMobile" class="font-bold text-gov-green-700">0</span> / <span id="totalPositionsMobile" class="font-semibold">{{ $candidatesByPosition->count() }}</span> positions selected
                    </div>
                </div>
                @endif
            </form>
            @endif

            @if(!$hasVoted)
            <!-- Enhanced Vote Summary Modal -->
            <div id="voteSummaryModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-3 sm:p-4">
                <div class="modal-content bg-white shadow-2xl max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Modal Header -->
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-200 bg-gradient-to-r from-gov-green-50 to-gov-gold-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-br from-gov-green-700 to-gov-green-800 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 heading-font">Review Your Votes</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Verify your selections</p>
                                </div>
                            </div>
                            <button onclick="closeSummaryModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Modal Body -->
                    <div class="px-5 sm:px-6 py-4 sm:py-5 overflow-y-auto flex-1 bg-gray-50">
                        <div id="voteSummaryContent" class="space-y-3 sm:space-y-4">
                            <!-- Summary will be populated here -->
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-t border-gray-200 bg-white">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-center space-x-2 text-xs sm:text-sm text-gray-500">
                                <svg class="w-4 h-4 text-gov-gold-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>Once submitted, you cannot change your votes</span>
                            </div>
                            <div class="flex flex-row items-center justify-between gap-2 sm:gap-3">
                                <button onclick="closeSummaryModal()" class="flex-1 sm:flex-none px-5 sm:px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all font-medium text-sm sm:text-base">
                                    Cancel
                                </button>
                                <button onclick="confirmSubmitVotes()" id="confirmSubmitBtn" class="flex-1 sm:flex-none px-5 sm:px-6 py-3 bg-gradient-to-r from-gov-green-700 to-gov-green-800 text-white rounded-xl hover:from-gov-green-800 hover:to-gov-green-900 transition-all font-semibold text-sm sm:text-base shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Confirm Votes</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Success Modal -->
            <div id="successModal" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-3 sm:p-4">
                <div class="modal-content bg-white shadow-2xl max-w-md sm:max-w-lg w-full overflow-hidden flex flex-col">
                    <!-- Success Modal Header -->
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-200 bg-gradient-to-r from-gov-green-50 to-gov-gold-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-br from-gov-green-600 to-gov-green-700 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 heading-font">Success!</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Your votes have been recorded</p>
                                </div>
                            </div>
                            <button onclick="closeSuccessModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Success Modal Body -->
                    <div class="px-5 sm:px-6 py-6 sm:py-8 text-center bg-gray-50">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-5 bg-gradient-to-br from-gov-green-500 to-gov-green-600 rounded-full flex items-center justify-center shadow-xl">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2 heading-font">Thank You for Voting!</h4>
                        <p class="text-sm sm:text-base text-gray-600 max-w-sm mx-auto">Your votes have been recorded successfully. Thank you for participating!</p>
                        <p class="text-xs text-gray-400 mt-4">Redirecting to dashboard...</p>
                    </div>
                    
                    <!-- Success Modal Footer -->
                    <div class="px-5 sm:px-6 py-4 border-t border-gray-200 bg-white">
                        <button onclick="closeSuccessModal()" class="w-full px-6 py-3 bg-gradient-to-r from-gov-green-700 to-gov-green-800 text-white rounded-xl hover:from-gov-green-800 hover:to-gov-green-900 transition-all font-semibold text-sm sm:text-base shadow-lg flex items-center justify-center space-x-2">
                            <span>Go to Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Page load preloader
        (function() {
            const pagePreloader = document.getElementById('pagePreloader');
            
            window.addEventListener('load', function() {
                setTimeout(() => {
                    if (pagePreloader) {
                        pagePreloader.classList.add('hidden');
                        setTimeout(() => {
                            pagePreloader.style.display = 'none';
                        }, 400);
                    }
                }, 500);
            });
            
            // Fallback
            setTimeout(() => {
                if (pagePreloader && !pagePreloader.classList.contains('hidden')) {
                    pagePreloader.classList.add('hidden');
                    setTimeout(() => {
                        pagePreloader.style.display = 'none';
                    }, 400);
                }
            }, 3000);
        })();

        function updateCountdown() {
            const countdownElement = document.getElementById('countdown');
            if (!countdownElement) return;
            const targetTimestamp = countdownElement.getAttribute('data-end-time');
            if (!targetTimestamp) return;
            const now = Math.floor(Date.now() / 1000);
            const target = parseInt(targetTimestamp);
            let diff = target - now;
            if (diff <= 0) {
                countdownElement.innerHTML = '<span class="text-red-500 font-semibold">Election Ended</span>';
                document.getElementById('voteForm').style.display = 'none';
                return;
            }
            const days = Math.floor(diff / 86400);
            const hours = Math.floor((diff % 86400) / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;
            let timeString = '';
            if (days > 0) timeString += `<span class="text-gov-green-800">${days}</span><span class="text-gray-400 text-xs">d</span> `;
            if (hours > 0 || days > 0) timeString += `<span class="text-gov-green-800">${String(hours).padStart(2, '0')}</span><span class="text-gray-400 text-xs">h</span> `;
            timeString += `<span class="text-gov-green-800">${String(minutes).padStart(2, '0')}</span><span class="text-gray-400 text-xs">m</span> `;
            timeString += `<span class="text-gov-green-800">${String(seconds).padStart(2, '0')}</span><span class="text-gray-400 text-xs">s</span>`;
            countdownElement.innerHTML = timeString;
        }

        function handleCandidateClick(event, card, candidateId, positionId) {
            event.preventDefault();
            const radio = card.querySelector('input[type="radio"]');
            const positionGroup = card.closest('.position-card');
            
            if (card.classList.contains('selected')) {
                card.classList.remove('selected');
                radio.checked = false;
            } else {
                if (positionGroup) {
                    positionGroup.querySelectorAll('.candidate-card').forEach(otherCard => {
                        otherCard.classList.remove('selected');
                        const otherRadio = otherCard.querySelector('input[type="radio"]');
                        if (otherRadio) otherRadio.checked = false;
                    });
                }
                card.classList.add('selected');
                radio.checked = true;
            }
            updateSubmitButton();
        }
        
        function resetAllVotes() {
            if (confirm('Are you sure you want to reset all your selections?')) {
                document.querySelectorAll('.candidate-card').forEach(card => {
                    card.classList.remove('selected');
                    const radio = card.querySelector('input[type="radio"]');
                    if (radio) radio.checked = false;
                });
                updateSubmitButton();
            }
        }
        
        function updateSubmitButton() {
            const selectedCount = document.querySelectorAll('.candidate-card.selected').length;
            const totalPositions = {{ $candidatesByPosition->count() }};
            
            const voteCountEl = document.getElementById('voteCount');
            const voteCountMobileEl = document.getElementById('voteCountMobile');
            if (voteCountEl) voteCountEl.textContent = selectedCount;
            if (voteCountMobileEl) voteCountMobileEl.textContent = selectedCount;
            
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = selectedCount === 0;
                submitBtn.classList.toggle('opacity-50', selectedCount === 0);
                submitBtn.classList.toggle('cursor-not-allowed', selectedCount === 0);
            }
            
            const resetBtn = document.getElementById('resetBtn');
            if (resetBtn) {
                resetBtn.disabled = selectedCount === 0;
                resetBtn.classList.toggle('opacity-50', selectedCount === 0);
                resetBtn.classList.toggle('cursor-not-allowed', selectedCount === 0);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('countdown')) {
                updateCountdown();
                setInterval(updateCountdown, 1000);
            }
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                const card = radio.closest('.candidate-card');
                if (card) card.classList.add('selected');
            });
            updateSubmitButton();

            // Store candidate data for summary
            window.candidateData = {};
            @foreach($candidatesByPosition as $positionId => $candidates)
                @php $position = $candidates->first()->position; @endphp
                @if($position)
                window.candidateData[{{ $position->id }}] = {
                    positionName: '{{ $position->name }}',
                    candidates: {
                        @foreach($candidates as $candidate)
                        {{ $candidate->id }}: {
                            name: '{{ $candidate->candidate_name }}',
                            photo: '{{ $candidate->photo ? route("student.candidates.photo", ["path" => $candidate->photo]) : "" }}',
                            partylist: '{{ $candidate->partylist ? $candidate->partylist->name : "Independent" }}',
                            partylistColor: '{{ $candidate->partylist && $candidate->partylist->color ? $candidate->partylist->color : "" }}'
                        },
                        @endforeach
                    }
                };
                @endif
            @endforeach
        });

        window.showVoteSummary = function() {
            const votes = [];
            const votesByPosition = {};
            
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                const candidateId = parseInt(radio.value);
                const positionId = parseInt(radio.getAttribute('data-position-id'));
                votes.push(candidateId);
                if (!votesByPosition[positionId]) {
                    votesByPosition[positionId] = [];
                }
                votesByPosition[positionId].push(candidateId);
            });

            if (votes.length === 0) {
                alert('Please select at least one candidate.');
                return;
            }

            let summaryHTML = '';
            Object.keys(votesByPosition).forEach(positionId => {
                const positionData = window.candidateData[positionId];
                if (!positionData) return;
                
                summaryHTML += `
                    <div class="summary-position-card rounded-xl p-3 sm:p-4 mb-3 sm:mb-4">
                        <div class="flex items-center space-x-2 mb-2 sm:mb-3">
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-gov-green-700"></div>
                            <h4 class="font-bold text-gray-900 text-sm sm:text-base lg:text-lg">${positionData.positionName}</h4>
                        </div>
                        <div class="space-y-2 sm:space-y-2.5">
                `;
                
                votesByPosition[positionId].forEach(candidateId => {
                    const candidate = positionData.candidates[candidateId];
                    if (!candidate) return;
                    
                    summaryHTML += `
                        <div class="summary-candidate-item rounded-lg p-2.5 sm:p-3 flex items-center space-x-3">
                            ${candidate.photo ? `
                                <img src="${candidate.photo}" alt="${candidate.name}" class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl object-cover flex-shrink-0 border-2 border-gray-200 shadow" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 hidden items-center justify-center flex-shrink-0 border-2 border-gray-200">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            ` : `
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center flex-shrink-0 border-2 border-gray-200">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            `}
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 text-sm sm:text-base truncate mb-1">${candidate.name}</p>
                                <div class="flex items-center space-x-2">
                                    ${candidate.partylistColor ? `<div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full flex-shrink-0 shadow-sm" style="background-color: ${candidate.partylistColor};"></div>` : ''}
                                    <span class="text-xs sm:text-sm text-gray-600 truncate font-medium">${candidate.partylist}</span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="summary-check-icon w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                summaryHTML += `
                        </div>
                    </div>
                `;
            });

            document.getElementById('voteSummaryContent').innerHTML = summaryHTML;
            document.getElementById('voteSummaryModal').classList.remove('hidden');
        }

        window.closeSummaryModal = function() {
            document.getElementById('voteSummaryModal').classList.add('hidden');
        };

        window.showSuccessModal = function(message) {
            const successModal = document.getElementById('successModal');
            if (!successModal) return;
            successModal.classList.remove('hidden');
            setTimeout(() => { closeSuccessModal(); }, 3000);
        }

        window.closeSuccessModal = function() {
            document.getElementById('successModal').classList.add('hidden');
            window.location.href = '{{ route("student.dashboard") }}';
        }

        window.showErrorModal = function(message) {
            alert(message || 'An error occurred. Please try again.');
        }

        window.confirmSubmitVotes = function() {
            const votes = [];
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                votes.push(parseInt(radio.value));
            });

            const confirmBtn = document.getElementById('confirmSubmitBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span>Submitting...</span>';
            submitBtn.disabled = true;
            
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            const form = document.getElementById('voteForm');
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ votes: votes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeSummaryModal();
                    showSuccessModal(data.message || 'Your votes have been submitted successfully!');
                } else {
                    showErrorModal(data.message || 'An error occurred. Please try again.');
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Confirm Votes</span>';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('An error occurred. Please try again.');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span>Confirm Votes</span>';
                submitBtn.disabled = false;
            });
        }

        document.getElementById('voteSummaryModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeSummaryModal();
        });

        document.getElementById('successModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeSuccessModal();
        });
    </script>
</body>
</html>
