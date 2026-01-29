<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Student Dashboard - CPSU Voting System</title>
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
        
        /* Enhanced Cards - Landing Page Style */
        .election-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(20, 83, 45, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }
        .election-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--gov-green-800) 0%, var(--gov-green-600) 50%, var(--gov-gold-400) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .election-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -10px rgba(20, 83, 45, 0.2);
        }
        .election-card:hover::before {
            opacity: 1;
        }
        
        /* Status Badge - Enhanced */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }
        .status-ongoing {
            background: linear-gradient(135deg, var(--gov-gold-300) 0%, var(--gov-gold-400) 100%);
            color: var(--gov-green-950);
            box-shadow: 0 4px 14px -2px rgba(234, 179, 8, 0.4);
        }
        .status-upcoming {
            background: linear-gradient(135deg, var(--gov-green-600) 0%, var(--gov-green-700) 100%);
            color: white;
            box-shadow: 0 4px 14px -2px rgba(22, 163, 74, 0.4);
        }
        
        /* Countdown Timer - Landing Page Style */
        .countdown-container {
            background: linear-gradient(135deg, rgba(20, 83, 45, 0.05) 0%, rgba(234, 179, 8, 0.05) 100%);
            border: 1px solid rgba(20, 83, 45, 0.1);
            border-radius: 1rem;
            padding: 1.25rem;
            backdrop-filter: blur(10px);
        }
        .countdown-timer {
            font-family: 'Inter', monospace;
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--gov-green-800);
            letter-spacing: 0.025em;
        }
        .countdown-label {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }
        
        /* Vote Button - Landing Page Style */
        .vote-btn {
            background: linear-gradient(135deg, var(--gov-green-800) 0%, var(--gov-green-700) 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 0.875rem;
            font-weight: 600;
            box-shadow: 0 4px 14px -2px rgba(20, 83, 45, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .vote-btn:hover {
            background: linear-gradient(135deg, var(--gov-green-900) 0%, var(--gov-green-800) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(20, 83, 45, 0.5);
        }
        
        /* Section Header - Gradient Text */
        .section-header {
            background: linear-gradient(135deg, var(--gov-green-900) 0%, var(--gov-green-700) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Alert Messages - Enhanced */
        .alert-success {
            background: linear-gradient(to right, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 1rem;
            border-left: 4px solid var(--gov-green-600);
        }
        
        /* Empty State - Enhanced */
        .empty-state {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(20, 83, 45, 0.08);
        }
        
        /* Already Voted Card - Professional & Balanced */
        .already-voted-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 1.25rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .already-voted-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #10b981 0%, #059669 50%, #047857 100%);
        }
        
        /* Professional Icon Container */
        .already-voted-icon-container {
            position: relative;
            width: 70px;
            height: 70px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 640px) {
            .already-voted-icon-container { width: 80px; height: 80px; margin-bottom: 1.25rem; }
        }
        @media (min-width: 768px) {
            .already-voted-icon-container { width: 90px; height: 90px; margin-bottom: 1.5rem; }
        }
        
        /* Pulse Animations */
        @keyframes pulse-outer {
            0%, 100% { transform: scale(0.85); opacity: 0.5; }
            50% { transform: scale(1.15); opacity: 0.2; }
        }
        .already-voted-icon-ring-outer {
            position: absolute;
            inset: -12px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse-outer 3s ease-in-out infinite;
            z-index: 1;
        }
        
        @keyframes pulse-middle {
            0%, 100% { transform: scale(0.9); opacity: 0.4; }
            50% { transform: scale(1.08); opacity: 0.15; }
        }
        .already-voted-icon-ring-middle {
            position: absolute;
            inset: -6px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.35) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse-middle 2.5s ease-in-out infinite 0.5s;
            z-index: 2;
        }
        
        .already-voted-icon-inner {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4), inset 0 1px 2px rgba(255, 255, 255, 0.2);
            z-index: 3;
            transition: transform 0.3s ease;
        }
        .already-voted-icon-inner:hover { transform: scale(1.05); }
        .already-voted-icon-inner svg {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            width: 40%;
            height: 40%;
        }
        
        /* Floating animation for cards */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
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
        
        /* Mobile Responsive */
        @media (max-width: 640px) {
            .countdown-timer { font-size: 0.95rem; }
            .vote-btn { padding: 0.75rem 1.5rem; font-size: 0.875rem; }
            .election-card { border-radius: 1rem; }
            .already-voted-icon-container { width: 60px; height: 60px; margin-bottom: 0.75rem; }
        }
        
        /* Logout Preloader */
        .logout-preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--gov-green-950) 0%, var(--gov-green-900) 50%, var(--gov-green-950) 100%);
            background-size: 200% 200%;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.4s ease, visibility 0.4s ease;
            animation: gradientShift 8s ease infinite;
        }
        .logout-preloader.active {
            display: flex;
            opacity: 1;
            visibility: visible;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .logout-preloader-content {
            text-align: center;
            color: white;
        }
        .logout-spinner {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            position: relative;
        }
        .logout-spinner-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top-color: var(--gov-gold-400);
            border-right-color: var(--gov-gold-400);
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
        }
        .logout-spinner-ring:nth-child(2) {
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
        .logout-spinner-ring:nth-child(3) {
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
        .logout-text {
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
        .logout-subtext {
            font-size: 0.9rem;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.85);
        }
        .logout-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }
        .logout-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--gov-gold-400) 0%, white 50%, var(--gov-gold-400) 100%);
            background-size: 200% 100%;
            width: 0%;
            animation: progressBar 2s ease-in-out infinite, logoutProgress 2s ease forwards;
        }
        @keyframes logoutProgress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
        @media (max-width: 640px) {
            .logout-spinner { width: 60px; height: 60px; margin-bottom: 1rem; }
            .logout-text { font-size: 1.25rem; }
            .logout-subtext { font-size: 0.8rem; }
        }
    </style>
</head>
<body class="antialiased">
    <!-- Page Load Preloader -->
    <div class="logout-preloader" id="pagePreloader" style="display: flex; opacity: 1; visibility: visible;">
        <div class="logout-preloader-content">
            <div class="logout-spinner">
                <div class="logout-spinner-ring"></div>
                <div class="logout-spinner-ring"></div>
                <div class="logout-spinner-ring"></div>
            </div>
            <div class="logout-text" id="preloaderText">Welcome Back!</div>
            <div class="logout-subtext" id="preloaderSubtext">Loading your dashboard...</div>
        </div>
        <div class="logout-progress-bar">
            <div class="logout-progress-fill" id="preloaderProgress"></div>
        </div>
    </div>
    
    <!-- Logout Preloader -->
    <div class="logout-preloader" id="logoutPreloader">
        <div class="logout-preloader-content">
            <div class="logout-spinner">
                <div class="logout-spinner-ring"></div>
                <div class="logout-spinner-ring"></div>
                <div class="logout-spinner-ring"></div>
            </div>
            <div class="logout-text">Signing Out...</div>
            <div class="logout-subtext">Thank you for using CPSU Voting</div>
        </div>
        <div class="logout-progress-bar">
            <div class="logout-progress-fill"></div>
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
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-gov-green-700 to-gov-green-900 rounded-xl flex items-center justify-center shadow-lg">
                            <!-- Voting Ballot Box Icon - Same as Landing Page -->
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18 13h-.68l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2H6l-3 3v4c0 1.1.89 2 1.99 2H19c1.1 0 2-.89 2-2v-4l-3-3zm-1-5.05l-4.95 4.95-3.54-3.54 4.95-4.95 3.54 3.54zm-4.24-5.66L6.39 8.66a.996.996 0 000 1.41l4.95 4.95c.39.39 1.02.39 1.41 0l6.36-6.36a.996.996 0 000-1.41l-4.95-4.95a.996.996 0 00-1.41 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg sm:text-xl font-bold text-white heading-font">CPSU Voting</h1>
                            <p class="text-xs sm:text-sm text-white/70 hidden sm:block">Student Portal</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="hidden sm:flex items-center bg-white/10 backdrop-blur-sm rounded-lg px-4 py-2">
                            <span class="text-sm text-white/90">Welcome, <span class="text-gov-gold-400 font-semibold">{{ auth()->user()->name }}</span></span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                            @csrf
                            <button type="submit" class="text-xs sm:text-sm text-white/80 hover:text-white font-semibold px-3 py-2 hover:bg-white/10 rounded-lg transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">
            <!-- Mobile Welcome -->
            <div class="sm:hidden mb-4 text-center">
                <p class="text-sm text-gray-600">Welcome, <span class="text-gov-green-700 font-semibold">{{ auth()->user()->name }}</span></p>
            </div>
            
            @if(session('success'))
                <div class="mb-6 p-4 alert-success">
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

            <!-- Page Header -->
            <div class="mb-6 sm:mb-8 lg:mb-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-1.5 h-8 bg-gradient-to-b from-gov-green-600 to-gov-gold-400 rounded-full"></div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold section-header heading-font">Student Dashboard</h2>
                </div>
                <p class="text-sm sm:text-base text-gray-600 ml-5">View and participate in active elections</p>
            </div>

            <!-- Elections Grid -->
            <div class="space-y-6">
                @forelse($elections as $election)
                <!-- Enhanced Election Card -->
                <div class="election-card p-5 sm:p-6 lg:p-8" data-election-id="{{ $election->id }}">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5 mb-5">
                        <!-- Election Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 heading-font">{{ $election->election_name }}</h3>
                                <span class="status-badge {{ $election->status === 'ongoing' ? 'status-ongoing' : 'status-upcoming' }}">
                                    @if($election->status === 'ongoing')
                                        <span class="w-2 h-2 bg-current rounded-full animate-pulse"></span>
                                    @endif
                                    {{ ucfirst($election->status) }}
                                </span>
                            </div>
                            
                            @if($election->organization)
                            <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                <svg class="w-4 h-4 text-gov-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="font-medium">{{ $election->organization->name }}</span>
                            </div>
                            @endif
                            
                            <div class="flex flex-wrap gap-4 text-sm text-gray-500 mt-3">
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
                            
                            @if($election->venue)
                            <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $election->venue }}</span>
                            </div>
                            @endif
                            
                            @if($election->description)
                            <p class="text-sm text-gray-500 mt-3 line-clamp-2">{{ Str::limit($election->description, 150) }}</p>
                            @endif
                        </div>
                        
                        <!-- Enhanced Countdown Timer -->
                        <div class="flex-shrink-0 w-full lg:w-auto">
                            <div class="countdown-container">
                                @if($election->status === 'ongoing')
                                    <p class="countdown-label mb-2 text-center">Time Remaining</p>
                                    <div class="countdown-timer text-center" id="countdown-{{ $election->id }}" data-end-time="{{ $election->end_datetime ? $election->end_datetime->timestamp : '' }}">
                                        <span class="text-gray-400">Calculating...</span>
                                    </div>
                                @else
                                    <p class="countdown-label mb-2 text-center">Starts In</p>
                                    <div class="countdown-timer text-center" id="countdown-{{ $election->id }}" data-start-time="{{ $election->start_datetime->timestamp }}">
                                        <span class="text-gray-400">Calculating...</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Vote Now Button or Already Voted Message -->
                    @if($election->status === 'ongoing' && $election->candidatesByPosition && $election->candidatesByPosition->count() > 0)
                        @if(isset($election->hasVoted) && $election->hasVoted)
                            <!-- Already Voted Message -->
                            <div class="mt-4 pt-5 border-t border-gray-100">
                                <div class="already-voted-card p-5 sm:p-6 text-center">
                                    <div class="already-voted-icon-container">
                                        <div class="already-voted-icon-ring-outer"></div>
                                        <div class="already-voted-icon-ring-middle"></div>
                                        <div class="already-voted-icon-inner">
                                            <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2 heading-font">You Have Already Voted</h3>
                                    <p class="text-sm sm:text-base text-gray-600">Your vote has been recorded. Thank you for participating!</p>
                                </div>
                            </div>
                        @else
                            <!-- Vote Now Button -->
                            <div class="mt-5 pt-5 border-t border-gray-100">
                                <a href="{{ route('student.vote', $election->id) }}" class="vote-btn">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Vote Now</span>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    @elseif($election->status === 'ongoing')
                    <!-- No candidates message for ongoing elections -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <div class="bg-gray-50 rounded-xl p-5 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-gray-500 text-sm">No candidates registered yet.</p>
                        </div>
                    </div>
                    @else
                    <!-- Upcoming election message -->
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <div class="bg-gradient-to-r from-gov-green-50 to-gov-gold-50 rounded-xl p-5 text-center border border-gov-green-100">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gov-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gov-green-700 text-sm font-medium">Voting will be available when the election starts.</p>
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <!-- Enhanced Empty State -->
                <div class="empty-state p-10 sm:p-14 lg:p-16 text-center">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center shadow-inner">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 heading-font">No Upcoming Elections</h3>
                    <p class="text-sm sm:text-base text-gray-500 max-w-md mx-auto">There are currently no elections available. Check back later for new voting opportunities.</p>
                </div>
                @endforelse

                <!-- Enhanced Voting History Link -->
                <a href="{{ route('student.votes-history') }}" class="election-card p-5 sm:p-6 lg:p-8 block group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-gov-green-700 to-gov-green-800 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 heading-font group-hover:text-gov-green-700 transition-colors">My Voting History</h3>
                                <p class="text-sm text-gray-500">View all your voting records</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center group-hover:bg-gov-green-100 group-hover:translate-x-1 transition-all">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gov-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="border-t border-gray-200 mt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">&copy; {{ date('Y') }} CPSU Voting System. All rights reserved.</p>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gov-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-500">Cloud Based Real-Time Voting</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Countdown timer function
        function updateCountdown(electionId, targetTimestamp, isEndTime) {
            const countdownElement = document.getElementById('countdown-' + electionId);
            if (!countdownElement || !targetTimestamp) return;

            const now = Math.floor(Date.now() / 1000);
            const target = parseInt(targetTimestamp);
            let diff = target - now;

            if (diff <= 0) {
                if (isEndTime) {
                    countdownElement.innerHTML = '<span class="text-red-500 font-semibold">Election Ended</span>';
                } else {
                    countdownElement.innerHTML = '<span class="text-green-600 font-semibold">Election Started!</span>';
                    setTimeout(() => location.reload(), 2000);
                }
                return;
            }

            const days = Math.floor(diff / 86400);
            const hours = Math.floor((diff % 86400) / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;

            let timeString = '';
            if (days > 0) {
                timeString += `<span class="text-gov-green-800">${days}</span><span class="text-gray-400 text-xs">d</span> `;
            }
            if (hours > 0 || days > 0) {
                timeString += `<span class="text-gov-green-800">${String(hours).padStart(2, '0')}</span><span class="text-gray-400 text-xs">h</span> `;
            }
            timeString += `<span class="text-gov-green-800">${String(minutes).padStart(2, '0')}</span><span class="text-gray-400 text-xs">m</span> `;
            timeString += `<span class="text-gov-green-800">${String(seconds).padStart(2, '0')}</span><span class="text-gray-400 text-xs">s</span>`;

            countdownElement.innerHTML = timeString;
        }

        // Initialize all countdown timers
        function initializeCountdowns() {
            document.querySelectorAll('[id^="countdown-"]').forEach(element => {
                const electionId = element.id.replace('countdown-', '');
                const startTime = element.getAttribute('data-start-time');
                const endTime = element.getAttribute('data-end-time');
                
                if (endTime) {
                    updateCountdown(electionId, endTime, true);
                    setInterval(() => updateCountdown(electionId, endTime, true), 1000);
                } else if (startTime) {
                    updateCountdown(electionId, startTime, false);
                    setInterval(() => updateCountdown(electionId, startTime, false), 1000);
                }
            });
        }

        // Page load preloader
        (function() {
            const pagePreloader = document.getElementById('pagePreloader');
            const preloaderProgress = document.getElementById('preloaderProgress');
            
            // Animate progress
            if (preloaderProgress) {
                preloaderProgress.style.animation = 'progressBar 2s ease-in-out infinite, logoutProgress 1.5s ease forwards';
            }
            
            window.addEventListener('load', function() {
                setTimeout(() => {
                    if (pagePreloader) {
                        pagePreloader.style.opacity = '0';
                        pagePreloader.style.visibility = 'hidden';
                        setTimeout(() => {
                            pagePreloader.style.display = 'none';
                        }, 400);
                    }
                }, 500);
            });
            
            // Fallback
            setTimeout(() => {
                if (pagePreloader && pagePreloader.style.display !== 'none') {
                    pagePreloader.style.opacity = '0';
                    pagePreloader.style.visibility = 'hidden';
                    setTimeout(() => {
                        pagePreloader.style.display = 'none';
                    }, 400);
                }
            }, 3000);
        })();

        document.addEventListener('DOMContentLoaded', function() {
            initializeCountdowns();
            
            // Logout preloader
            const logoutForm = document.getElementById('logoutForm');
            const logoutPreloader = document.getElementById('logoutPreloader');
            
            if (logoutForm && logoutPreloader) {
                logoutForm.addEventListener('submit', function(e) {
                    logoutPreloader.classList.add('active');
                });
            }
        });
    </script>
</body>
</html>
