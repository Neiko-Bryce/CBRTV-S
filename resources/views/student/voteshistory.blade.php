<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Voting History - CpsuVotewisely.com</title>
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
        .history-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(20, 83, 45, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }
        .history-card::before {
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
        .history-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -8px rgba(20, 83, 45, 0.15);
        }
        .history-card:hover::before {
            opacity: 1;
        }
        
        /* Voting History Badge */
        .voting-history-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--gov-green-100) 0%, var(--gov-green-200) 100%);
            color: var(--gov-green-800);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Position Card */
        .position-card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border: 1px solid #e5e7eb;
            border-left: 4px solid var(--gov-green-700);
            border-radius: 1rem;
            transition: all 0.3s ease;
        }
        .position-card:hover {
            border-left-color: var(--gov-gold-500);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        
        /* Candidate Item */
        .candidate-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.875rem;
            transition: all 0.2s ease;
        }
        .candidate-item:hover {
            background: var(--gov-green-50);
            border-color: var(--gov-green-300);
            transform: translateX(4px);
        }
        
        /* Section Header */
        .section-header {
            background: linear-gradient(135deg, var(--gov-green-900) 0%, var(--gov-green-700) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(20, 83, 45, 0.08);
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
        
        /* Timeline Connector */
        .timeline-connector {
            position: absolute;
            left: 1.5rem;
            top: 2.5rem;
            bottom: -1.5rem;
            width: 2px;
            background: linear-gradient(to bottom, var(--gov-green-300), var(--gov-green-100));
        }
        .history-card:last-child .timeline-connector {
            display: none;
        }
        
        /* Mobile Responsive */
        @media (max-width: 640px) {
            .history-card { border-radius: 1rem; }
            .position-card { border-radius: 0.75rem; }
            .candidate-item { border-radius: 0.75rem; }
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
            <div class="preloader-text">Loading History</div>
            <div class="preloader-subtext">Fetching your voting records...</div>
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
                            <h1 class="text-lg sm:text-xl font-bold text-white heading-font">Voting History</h1>
                            <p class="text-xs sm:text-sm text-white/70 hidden sm:block">View all your voting records</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10">
            <!-- Page Header -->
            <div class="mb-6 sm:mb-8 lg:mb-10">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-1.5 h-8 bg-gradient-to-b from-gov-green-600 to-gov-gold-400 rounded-full"></div>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-bold section-header heading-font">My Voting History</h2>
                </div>
                <p class="text-sm sm:text-base text-gray-600 ml-5">Complete record of all your voting participation</p>
            </div>

            @if(isset($votingHistory) && $votingHistory->count() > 0)
                <div class="space-y-6">
                    @foreach($votingHistory as $index => $history)
                        <div class="history-card p-5 sm:p-6 lg:p-8 relative">
                            <!-- Timeline Number -->
                            <div class="absolute top-6 left-6 w-8 h-8 bg-gradient-to-br from-gov-green-700 to-gov-green-800 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg z-10">
                                {{ $index + 1 }}
                            </div>
                            
                            <!-- Election Header -->
                            <div class="ml-12 sm:ml-14">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-5 pb-5 border-b border-gray-200">
                                    <div class="flex-1">
                                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 heading-font">{{ $history['election']->election_name }}</h3>
                                        @if($history['election']->organization)
                                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                            <svg class="w-4 h-4 text-gov-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span class="font-medium">{{ $history['election']->organization->name }}</span>
                                        </div>
                                        @endif
                                        <div class="flex flex-wrap gap-3 sm:gap-4 text-sm text-gray-500 mt-3">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>{{ \Carbon\Carbon::parse($history['election']->election_date)->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Voted: {{ \Carbon\Carbon::parse($history['voted_at'])->format('M d, Y \a\t g:i A') }}</span>
                                            </div>
                                        </div>
                                        @if($history['election']->venue)
                                        <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ $history['election']->venue }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="voting-history-badge">
                                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Voted
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Candidates Voted For -->
                                <div class="space-y-4">
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gov-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Your Votes
                                    </h4>
                                    @foreach($history['candidates'] as $positionId => $candidates)
                                        @php
                                            $position = $candidates->first()['position'] ?? null;
                                        @endphp
                                        @if($position)
                                        <div class="position-card p-4 sm:p-5">
                                            <div class="flex items-center gap-2 mb-3">
                                                <div class="w-1.5 h-5 bg-gradient-to-b from-gov-green-600 to-gov-green-400 rounded-full"></div>
                                                <h5 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900">{{ $position->name }}</h5>
                                            </div>
                                            <div class="space-y-2">
                                                @foreach($candidates as $item)
                                                    @php
                                                        $candidate = $item['candidate'];
                                                        $partylist = $item['partylist'];
                                                    @endphp
                                                    <div class="candidate-item p-3 flex items-center gap-3">
                                                        <!-- Candidate Photo -->
                                                        @if($candidate->photo)
                                                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl overflow-hidden flex-shrink-0 border-2 border-gov-green-200 shadow-md">
                                                            <img src="{{ route('student.candidates.photo', ['path' => $candidate->photo]) }}" 
                                                                 alt="{{ $candidate->candidate_name }}" 
                                                                 class="w-full h-full object-cover"
                                                                 onerror="this.parentElement.innerHTML='<div class=\'w-full h-full bg-gradient-to-br from-gov-green-100 to-gov-green-200 flex items-center justify-center\'><svg class=\'w-6 h-6 text-gov-green-700\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\'></path></svg></div>';">
                                                        </div>
                                                        @else
                                                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-gov-green-100 to-gov-green-200 rounded-xl flex items-center justify-center flex-shrink-0 border-2 border-gov-green-200">
                                                            <svg class="w-6 h-6 text-gov-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                        </div>
                                                        @endif
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900 truncate">{{ $candidate->candidate_name }}</p>
                                                            @if($partylist)
                                                            <div class="flex items-center gap-2 mt-1">
                                                                @if($partylist->color)
                                                                <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $partylist->color }};"></div>
                                                                @endif
                                                                <span class="text-xs sm:text-sm text-gray-600 font-medium truncate">{{ $partylist->name }}</span>
                                                            </div>
                                                            @else
                                                            <span class="text-xs sm:text-sm text-gray-400 italic">Independent</span>
                                                            @endif
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-gov-green-600 to-gov-green-700 rounded-full flex items-center justify-center shadow-md">
                                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Summary Stats -->
                <div class="mt-8 bg-gradient-to-r from-gov-green-800 to-gov-green-900 rounded-2xl p-6 sm:p-8 text-white">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center">
                                <svg class="w-7 h-7 text-gov-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white/70 text-sm">Total Elections Participated</p>
                                <p class="text-3xl font-bold">{{ $votingHistory->count() }}</p>
                            </div>
                        </div>
                        <a href="{{ route('student.dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-gov-green-800 rounded-xl font-semibold hover:bg-gray-100 transition-all shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            @else
                <!-- Enhanced Empty State -->
                <div class="empty-state p-10 sm:p-14 lg:p-16 text-center">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center shadow-inner">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 heading-font">No Voting History</h3>
                    <p class="text-sm sm:text-base text-gray-500 max-w-md mx-auto mb-6">You haven't participated in any elections yet. Your voting history will appear here once you start voting.</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-gov-green-700 to-gov-green-800 text-white rounded-xl hover:from-gov-green-800 hover:to-gov-green-900 transition-all font-semibold text-sm sm:text-base shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Return to Dashboard
                    </a>
                </div>
            @endif
        </main>
        
        <!-- Footer -->
        <footer class="border-t border-gray-200 mt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-500">&copy; {{ date('Y') }} CpsuVotewisely.com. All rights reserved.</p>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gov-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-gray-500">Cloud Based Real-Time Voting</span>
                    </div>
                </div>
            </div>
        </footer>
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
    </script>
</body>
</html>
