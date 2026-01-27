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
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .heading-font {
            font-family: 'Playfair Display', serif;
        }
        
        /* Landing Page Style Header */
        header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        /* Enhanced Cards - Landing Page Style */
        .election-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 102, 51, 0.1);
            transition: all 0.3s ease;
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
            background: linear-gradient(90deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 50%, var(--cpsu-gold) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .election-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 102, 51, 0.15);
        }
        .election-card:hover::before {
            opacity: 1;
        }
        
        /* Status Badge - Enhanced */
        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Countdown Timer - Landing Page Style */
        .countdown-container {
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.05) 0%, rgba(212, 175, 55, 0.05) 100%);
            border: 1px solid rgba(0, 102, 51, 0.1);
            border-radius: 0.875rem;
            padding: 1rem;
            backdrop-filter: blur(10px);
        }
        .countdown-timer {
            font-family: 'Inter', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--cpsu-green);
        }
        .countdown-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }
        
        /* Vote Button - Landing Page Style */
        .vote-btn {
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        .vote-btn:hover {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 51, 0.4);
        }
        
        /* Section Header - Gradient Text */
        .section-header {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Alert Messages - Enhanced */
        .alert-success {
            background: linear-gradient(to right, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 0.75rem;
        }
        
        /* Empty State - Enhanced */
        .empty-state {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 102, 51, 0.1);
        }
        
        /* Already Voted Card - Professional & Balanced */
        .already-voted-card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 102, 51, 0.1);
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
            height: 4px;
            background: linear-gradient(90deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 50%, var(--cpsu-gold) 100%);
        }
        
        /* Professional Icon Container - Balanced for All Screens */
        .already-voted-icon-container {
            position: relative;
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 640px) {
            .already-voted-icon-container {
                width: 70px;
                height: 70px;
                margin-bottom: 1.25rem;
            }
        }
        @media (min-width: 768px) {
            .already-voted-icon-container {
                width: 80px;
                height: 80px;
                margin-bottom: 1.5rem;
            }
        }
        @media (min-width: 1024px) {
            .already-voted-icon-container {
                width: 90px;
                height: 90px;
                margin-bottom: 1.75rem;
            }
        }
        @media (min-width: 1280px) {
            .already-voted-icon-container {
                width: 100px;
                height: 100px;
                margin-bottom: 2rem;
            }
        }
        
        /* Outer Pulse Ring - Professional Animation */
        @keyframes pulse-outer {
            0% {
                transform: scale(0.8);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.2;
            }
            100% {
                transform: scale(0.8);
                opacity: 0.6;
            }
        }
        .already-voted-icon-ring-outer {
            position: absolute;
            inset: -12px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse-outer 3s ease-in-out infinite;
            z-index: 1;
        }
        @media (min-width: 640px) {
            .already-voted-icon-ring-outer {
                inset: -16px;
            }
        }
        @media (min-width: 768px) {
            .already-voted-icon-ring-outer {
                inset: -20px;
            }
        }
        
        /* Middle Pulse Ring */
        @keyframes pulse-middle {
            0% {
                transform: scale(0.85);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.2;
            }
            100% {
                transform: scale(0.85);
                opacity: 0.5;
            }
        }
        .already-voted-icon-ring-middle {
            position: absolute;
            inset: -8px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.3) 0%, transparent 70%);
            border-radius: 50%;
            animation: pulse-middle 2.5s ease-in-out infinite;
            z-index: 2;
        }
        @media (min-width: 640px) {
            .already-voted-icon-ring-middle {
                inset: -10px;
            }
        }
        @media (min-width: 768px) {
            .already-voted-icon-ring-middle {
                inset: -12px;
            }
        }
        
        /* Inner Icon Circle - Balanced Sizing */
        .already-voted-icon-inner {
            position: relative;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                0 4px 16px rgba(16, 185, 129, 0.4),
                0 0 0 3px rgba(16, 185, 129, 0.1),
                inset 0 1px 2px rgba(255, 255, 255, 0.2);
            z-index: 3;
            transition: transform 0.3s ease;
        }
        .already-voted-icon-inner:hover {
            transform: scale(1.05);
        }
        @media (min-width: 640px) {
            .already-voted-icon-inner {
                width: 70px;
                height: 70px;
                box-shadow: 
                    0 5px 20px rgba(16, 185, 129, 0.4),
                    0 0 0 3px rgba(16, 185, 129, 0.1),
                    inset 0 1px 2px rgba(255, 255, 255, 0.2);
            }
        }
        @media (min-width: 768px) {
            .already-voted-icon-inner {
                width: 80px;
                height: 80px;
                box-shadow: 
                    0 6px 24px rgba(16, 185, 129, 0.4),
                    0 0 0 4px rgba(16, 185, 129, 0.1),
                    inset 0 2px 4px rgba(255, 255, 255, 0.2);
            }
        }
        @media (min-width: 1024px) {
            .already-voted-icon-inner {
                width: 90px;
                height: 90px;
                box-shadow: 
                    0 7px 28px rgba(16, 185, 129, 0.4),
                    0 0 0 5px rgba(16, 185, 129, 0.1),
                    inset 0 2px 4px rgba(255, 255, 255, 0.2);
            }
        }
        @media (min-width: 1280px) {
            .already-voted-icon-inner {
                width: 100px;
                height: 100px;
                box-shadow: 
                    0 8px 32px rgba(16, 185, 129, 0.4),
                    0 0 0 6px rgba(16, 185, 129, 0.1),
                    inset 0 2px 4px rgba(255, 255, 255, 0.2);
            }
        }
        
        /* Checkmark Icon - Balanced Responsive Sizing */
        .already-voted-icon-inner svg {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            width: 1.5rem;
            height: 1.5rem;
        }
        @media (min-width: 640px) {
            .already-voted-icon-inner svg {
                width: 1.75rem;
                height: 1.75rem;
            }
        }
        @media (min-width: 768px) {
            .already-voted-icon-inner svg {
                width: 2rem;
                height: 2rem;
            }
        }
        @media (min-width: 1024px) {
            .already-voted-icon-inner svg {
                width: 2.25rem;
                height: 2.25rem;
            }
        }
        @media (min-width: 1280px) {
            .already-voted-icon-inner svg {
                width: 2.5rem;
                height: 2.5rem;
            }
        }
        
        /* Typography Balance - Optimized for All Screens */
        .already-voted-heading {
            margin-bottom: 0.75rem;
            font-size: 1.125rem;
        }
        @media (min-width: 640px) {
            .already-voted-heading {
                margin-bottom: 0.875rem;
                font-size: 1.375rem;
            }
        }
        @media (min-width: 768px) {
            .already-voted-heading {
                margin-bottom: 1rem;
                font-size: 1.625rem;
            }
        }
        @media (min-width: 1024px) {
            .already-voted-heading {
                margin-bottom: 1.125rem;
                font-size: 1.875rem;
            }
        }
        @media (min-width: 1280px) {
            .already-voted-heading {
                margin-bottom: 1.25rem;
                font-size: 2rem;
            }
        }
        .already-voted-messages {
            margin-bottom: 0;
        }
        @media (min-width: 640px) {
            .already-voted-messages {
                margin-bottom: 0.25rem;
            }
        }
        @media (min-width: 768px) {
            .already-voted-messages {
                margin-bottom: 0.5rem;
            }
        }
        @media (min-width: 1024px) {
            .already-voted-messages {
                margin-bottom: 0.75rem;
            }
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .election-card {
                border-radius: 0.875rem;
            }
            .countdown-container {
                padding: 0.875rem;
            }
            .countdown-timer {
                font-size: 1rem;
            }
        }
        @media (max-width: 640px) {
            .countdown-timer {
                font-size: 0.95rem;
            }
            .vote-btn {
                padding: 0.625rem 1.25rem;
                font-size: 0.875rem;
            }
            .already-voted-card {
                padding: 1rem;
                border-radius: 0.875rem;
            }
            .already-voted-icon-container {
                margin-bottom: 0.75rem;
            }
            .already-voted-heading {
                margin-bottom: 0.5rem;
            }
            .already-voted-messages {
                margin-bottom: 0;
            }
            .already-voted-messages p {
                font-size: 0.75rem;
                line-height: 1.4;
            }
        }
        @media (min-width: 768px) {
            .already-voted-card {
                padding: 2rem;
            }
        }
        @media (min-width: 1024px) {
            .already-voted-card {
                padding: 2.5rem;
            }
        }
        @media (min-width: 1280px) {
            .already-voted-card {
                padding: 3rem;
            }
        }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen">
        <!-- Landing Page Style Header -->
        <header class="sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-3 sm:py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-700 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-xl font-bold text-gray-900 heading-font">CPSU Voting System</h1>
                            <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Student Portal</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <span class="text-xs sm:text-sm text-gray-700 hidden sm:inline font-medium">Welcome, <span class="text-green-700 font-semibold">{{ auth()->user()->name }}</span></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-700 font-semibold px-3 py-1.5 hover:bg-red-50 rounded-lg transition-all">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-5 sm:py-6 lg:py-8">
            @if(session('success'))
                <div class="mb-5 sm:mb-6 p-4 alert-success">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm sm:text-base text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="mb-5 sm:mb-6 lg:mb-8">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold section-header heading-font mb-2">Student Dashboard</h2>
                <p class="text-sm sm:text-base text-gray-600">View and participate in active elections</p>
            </div>

            @forelse($elections as $election)
            <!-- Enhanced Election Card -->
            <div class="election-card p-5 sm:p-6 lg:p-7 mb-5 sm:mb-6" data-election-id="{{ $election->id }}">
                <div class="mb-4 sm:mb-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-3 sm:mb-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-2 sm:mb-3">
                                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 heading-font">{{ $election->election_name }}</h3>
                                <span class="status-badge {{ $election->status === 'ongoing' ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-yellow-900' : 'bg-gradient-to-r from-green-600 to-green-700 text-white' }}">
                                    {{ ucfirst($election->status) }}
                                </span>
                            </div>
                            @if($election->organization)
                            <p class="text-xs sm:text-sm text-gray-600 mb-1">
                                <span class="font-medium">Organization:</span> {{ $election->organization->name }}
                            </p>
                            @endif
                            <div class="flex flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm text-gray-600">
                                <p>
                                    <span class="font-medium">Date:</span> 
                                    {{ \Carbon\Carbon::parse($election->election_date)->format('M d, Y') }}
                                </p>
                                @if($election->timestarted)
                                <p>
                                    <span class="font-medium">Start:</span> 
                                    {{ \Carbon\Carbon::parse($election->timestarted)->format('g:i A') }}
                                </p>
                                @endif
                                @if($election->time_ended)
                                <p>
                                    <span class="font-medium">End:</span> 
                                    {{ \Carbon\Carbon::parse($election->time_ended)->format('g:i A') }}
                                </p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Enhanced Countdown Timer -->
                        <div class="flex-shrink-0">
                            <div class="countdown-container">
                                @if($election->status === 'ongoing')
                                    <p class="countdown-label mb-2">Time Remaining</p>
                                    <div class="countdown-timer" id="countdown-{{ $election->id }}" data-end-time="{{ $election->end_datetime ? $election->end_datetime->timestamp : '' }}">
                                        <span class="text-gray-500">Calculating...</span>
                                    </div>
                                @else
                                    <p class="countdown-label mb-2">Starts In</p>
                                    <div class="countdown-timer" id="countdown-{{ $election->id }}" data-start-time="{{ $election->start_datetime->timestamp }}">
                                        <span class="text-gray-500">Calculating...</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($election->venue)
                    <p class="text-xs sm:text-sm text-gray-600">
                        <span class="font-medium">Venue:</span> {{ $election->venue }}
                    </p>
                    @endif
                    @if($election->description)
                    <p class="text-xs sm:text-sm text-gray-600 mt-2">{{ Str::limit($election->description, 150) }}</p>
                    @endif
                </div>

                <!-- Vote Now Button or Already Voted Message -->
                @if($election->status === 'ongoing' && $election->candidatesByPosition && $election->candidatesByPosition->count() > 0)
                    @if(isset($election->hasVoted) && $election->hasVoted)
                        <!-- Professional & Balanced Already Voted Message - All Screens Optimized -->
                        <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-100">
                            <div class="already-voted-card p-4 sm:p-5 lg:p-6 xl:p-7 text-center">
                                <!-- Professional Icon with Multi-Layer Pulse Animation -->
                                <div class="already-voted-icon-container">
                                    <div class="already-voted-icon-ring-outer"></div>
                                    <div class="already-voted-icon-ring-middle"></div>
                                    <div class="already-voted-icon-inner">
                                        <svg class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- Heading -->
                                <h3 class="font-bold text-gray-900 already-voted-heading heading-font">You Have Already Voted</h3>
                                
                                <!-- Messages - Balanced Spacing -->
                                <div class="space-y-1.5 sm:space-y-1.5 lg:space-y-2 already-voted-messages">
                                    <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed">You have already submitted your votes for this election.</p>
                                    <p class="text-xs sm:text-sm lg:text-base text-gray-600 leading-relaxed font-medium">Thank you for participating!</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Vote Now Button -->
                        <div class="mt-4 sm:mt-5 pt-4 sm:pt-5 border-t border-gray-100">
                            <a href="{{ route('student.vote', $election->id) }}" 
                               class="vote-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Vote Now</span>
                            </a>
                        </div>
                    @endif
                @elseif($election->status === 'ongoing')
                <!-- No candidates message for ongoing elections -->
                <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                    <p class="text-gray-600 text-center py-4 text-sm sm:text-base">No candidates have been registered for this election yet.</p>
                </div>
                @else
                <!-- Upcoming election - candidates will be shown when election starts -->
                <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                    <p class="text-gray-600 text-center py-4 text-sm sm:text-base">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Candidates will be displayed when the election starts.
                    </p>
                </div>
                @endif
            </div>
            @empty
            <!-- Enhanced Empty State -->
            <div class="empty-state p-10 sm:p-14 lg:p-16 text-center">
                <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 heading-font">No Upcoming Elections</h3>
                <p class="text-sm sm:text-base text-gray-600 max-w-md mx-auto">There are currently no upcoming or ongoing elections available. Check back later for new voting opportunities.</p>
            </div>
            @endforelse

            <!-- Enhanced Voting History - Clickable Link -->
            <div class="election-card p-5 sm:p-6 lg:p-7 mt-6 sm:mt-8">
                <a href="{{ route('student.votes-history') }}" class="block hover:opacity-90 transition-opacity">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-1 heading-font">My Voting History</h3>
                                <p class="text-sm sm:text-base text-gray-600">View all your voting records and history</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </main>
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
                    countdownElement.innerHTML = '<span class="text-red-600">Election Ended</span>';
                } else {
                    countdownElement.innerHTML = '<span class="text-green-600">Election Started</span>';
                    // Reload page to update status
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
                timeString += `<span class="text-green-700">${days}d</span> `;
            }
            if (hours > 0 || days > 0) {
                timeString += `<span class="text-green-700">${String(hours).padStart(2, '0')}h</span> `;
            }
            if (minutes > 0 || hours > 0 || days > 0) {
                timeString += `<span class="text-green-700">${String(minutes).padStart(2, '0')}m</span> `;
            }
            timeString += `<span class="text-green-700">${String(seconds).padStart(2, '0')}s</span>`;

            countdownElement.innerHTML = timeString;
        }

        // Initialize all countdown timers
        function initializeCountdowns() {
            document.querySelectorAll('[id^="countdown-"]').forEach(element => {
                const electionId = element.id.replace('countdown-', '');
                const startTime = element.getAttribute('data-start-time');
                const endTime = element.getAttribute('data-end-time');
                
                if (endTime) {
                    // Ongoing election - countdown to end
                    updateCountdown(electionId, endTime, true);
                    setInterval(() => updateCountdown(electionId, endTime, true), 1000);
                } else if (startTime) {
                    // Upcoming election - countdown to start
                    updateCountdown(electionId, startTime, false);
                    setInterval(() => updateCountdown(electionId, startTime, false), 1000);
                }
            });
        }


        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeCountdowns();
        });
    </script>
</body>
</html>
