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
        
        /* Enhanced Election Info Card */
        .election-info-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 102, 51, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .election-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 50%, var(--cpsu-gold) 100%);
        }
        
        /* Enhanced Position Cards */
        .position-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 102, 51, 0.1);
            transition: all 0.3s ease;
        }
        .position-card:hover {
            box-shadow: 0 8px 30px rgba(0, 102, 51, 0.12);
        }
        
        /* Enhanced Countdown */
        .countdown-container {
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.05) 0%, rgba(212, 175, 55, 0.05) 100%);
            border: 1px solid rgba(0, 102, 51, 0.1);
            border-radius: 0.875rem;
            padding: 1rem;
            backdrop-filter: blur(10px);
        }
        
        /* Enhanced Alert Messages */
        .alert-success {
            background: linear-gradient(to right, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #86efac;
            border-radius: 0.75rem;
        }
        .alert-error {
            background: linear-gradient(to right, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fca5a5;
            border-radius: 0.75rem;
        }
        
        /* Candidate Card Styles */
        .candidate-card {
            position: relative;
            display: flex;
            flex-direction: column;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .candidate-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .candidate-card.selected {
            border-color: #006633;
            border-width: 3px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            box-shadow: 0 4px 16px rgba(0, 102, 51, 0.2);
        }
        .candidate-card.selected::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #006633 0%, #008844 100%);
            z-index: 1;
        }
        
        /* Photo Styles */
        .candidate-photo {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 0.75rem 0.75rem 0 0;
        }
        .candidate-photo-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 0.75rem 0.75rem 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .candidate-photo-placeholder svg {
            width: 60px;
            height: 60px;
            color: #9ca3af;
        }
        
        /* Checkmark Badge */
        .checkmark-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #006633 0%, #008844 100%);
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.4);
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
            width: 18px;
            height: 18px;
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
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            line-height: 1.3;
        }
        .candidate-card.selected .candidate-name {
            color: #006633;
        }
        .candidate-partylist {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #4b5563;
            padding: 0.375rem 0.75rem;
            background-color: #f9fafb;
            border-radius: 0.5rem;
        }
        .candidate-platform {
            font-size: 0.8125rem;
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
            background-color: #f0fdf4;
            border-left-color: #006633;
        }
        
        /* Countdown */
        .countdown-timer {
            font-family: 'Inter', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            color: #006633;
        }
        .countdown-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Enhanced Modal Styles */
        #voteSummaryModal {
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        #voteSummaryModal .bg-white,
        #successModal .bg-white {
            animation: slideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border-radius: 1.5rem !important;
            overflow: hidden;
        }
        #successModal {
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes successPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        #successModal .bg-gradient-to-br {
            animation: successPulse 0.6s ease-in-out;
        }
        @keyframes slideUp {
            from {
                transform: translateY(50px) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }
        .summary-position-card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border: 1px solid #e5e7eb;
            border-left: 4px solid #006633;
            border-radius: 0.875rem;
        }
        .summary-candidate-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }
        .summary-candidate-item:hover {
            border-color: #006633;
            box-shadow: 0 2px 8px rgba(0, 102, 51, 0.1);
            transform: translateX(4px);
        }
        .summary-check-icon {
            background: linear-gradient(135deg, #006633 0%, #008844 100%);
            box-shadow: 0 2px 8px rgba(0, 102, 51, 0.3);
        }
        @media (min-width: 1024px) {
            #voteSummaryModal .bg-white {
                max-width: 28rem;
                border-radius: 1.5rem !important;
            }
        }
        @media (min-width: 1280px) {
            #voteSummaryModal .bg-white {
                max-width: 32rem;
            }
        }
        @media (max-width: 640px) {
            #voteSummaryModal .bg-white {
                max-height: 95vh;
                margin: 0.5rem;
            }
            #voteSummaryModal {
                padding: 0.5rem;
            }
        }
        
        /* Mobile Responsive - Compact cards for 2-4 columns */
        @media (min-width: 475px) and (max-width: 640px) {
            .grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        @media (min-width: 375px) and (max-width: 474px) {
            .grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 640px) {
            main {
                padding: 0.5rem !important;
            }
            .candidate-photo, .candidate-photo-placeholder {
                height: 90px;
            }
            .candidate-photo-placeholder svg {
                width: 28px;
                height: 28px;
            }
            .candidate-info {
                padding: 0.4375rem 0.375rem;
                gap: 0.1875rem;
            }
            .candidate-name {
                font-size: 0.6875rem;
                line-height: 1.2;
                margin: 0;
            }
            .candidate-partylist {
                font-size: 0.5625rem;
                padding: 0.125rem 0.3125rem;
                gap: 0.1875rem;
                margin: 0;
            }
            .candidate-partylist .w-3 {
                width: 0.375rem !important;
                height: 0.375rem !important;
            }
            .candidate-platform {
                font-size: 0.5rem;
                padding: 0.1875rem;
                line-height: 1.25;
                -webkit-line-clamp: 2;
                margin: 0;
            }
            .checkmark-badge {
                width: 20px;
                height: 20px;
                top: 3px;
                right: 3px;
                border-width: 2px;
            }
            .checkmark-badge svg {
                width: 10px;
                height: 10px;
                stroke-width: 2.5;
            }
            .candidate-card {
                border-width: 1.5px;
            }
            .candidate-card.selected {
                border-width: 2px;
            }
            .bg-white.rounded-xl {
                padding: 0.625rem !important;
                margin-bottom: 0.625rem !important;
            }
            .countdown-timer {
                font-size: 0.75rem;
            }
            .countdown-label {
                font-size: 0.5625rem;
            }
        }
        @media (min-width: 480px) and (max-width: 640px) {
            .candidate-photo, .candidate-photo-placeholder {
                height: 100px;
            }
            .candidate-info {
                padding: 0.5rem 0.4375rem;
            }
            .candidate-name {
                font-size: 0.75rem;
            }
            .candidate-partylist {
                font-size: 0.625rem;
            }
            .candidate-platform {
                font-size: 0.5625rem;
            }
        }
        @media (max-width: 480px) {
            .candidate-photo, .candidate-photo-placeholder {
                height: 85px;
            }
            .candidate-photo-placeholder svg {
                width: 24px;
                height: 24px;
            }
            .candidate-info {
                padding: 0.375rem 0.3125rem;
                gap: 0.125rem;
            }
            .candidate-name {
                font-size: 0.625rem;
            }
            .candidate-partylist {
                font-size: 0.5rem;
                padding: 0.125rem 0.25rem;
            }
            .candidate-platform {
                font-size: 0.4375rem;
                padding: 0.125rem;
                -webkit-line-clamp: 1;
            }
            .checkmark-badge {
                width: 18px;
                height: 18px;
                top: 2px;
                right: 2px;
            }
            .checkmark-badge svg {
                width: 9px;
                height: 9px;
            }
        }
        @media (min-width: 375px) and (max-width: 480px) {
            .candidate-photo, .candidate-photo-placeholder {
                height: 90px;
            }
            .candidate-name {
                font-size: 0.6875rem;
            }
        }
        @media (max-width: 360px) {
            .candidate-photo, .candidate-photo-placeholder {
                height: 80px;
            }
            .candidate-info {
                padding: 0.3125rem 0.25rem;
            }
            .candidate-name {
                font-size: 0.5625rem;
            }
            .candidate-partylist {
                font-size: 0.4375rem;
            }
            .candidate-platform {
                font-size: 0.375rem;
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
                        <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-gray-900 transition-all p-2 hover:bg-gray-100 rounded-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-700 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-xl font-bold text-gray-900 heading-font">Voting</h1>
                            <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">{{ $election->election_name }}</p>
                        </div>
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

            @if(session('error'))
                <div class="mb-5 sm:mb-6 p-4 alert-error">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="text-sm sm:text-base text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Enhanced Election Info Card -->
            <div class="election-info-card p-5 sm:p-6 lg:p-7 mb-5 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2 break-words">{{ $election->election_name }}</h2>
                        @if($election->organization)
                        <p class="text-xs sm:text-sm text-gray-600 mb-2">
                            <span class="font-medium">Organization:</span> <span class="break-words">{{ $election->organization->name }}</span>
                        </p>
                        @endif
                        <div class="flex flex-wrap gap-2 sm:gap-4 text-xs sm:text-sm text-gray-600">
                            <p class="whitespace-nowrap">
                                <span class="font-medium">Date:</span> 
                                {{ \Carbon\Carbon::parse($election->election_date)->format('M d, Y') }}
                            </p>
                            @if($election->timestarted)
                            <p class="whitespace-nowrap">
                                <span class="font-medium">Start:</span> 
                                {{ \Carbon\Carbon::parse($election->timestarted)->format('g:i A') }}
                            </p>
                            @endif
                            @if($election->time_ended)
                            <p class="whitespace-nowrap">
                                <span class="font-medium">End:</span> 
                                {{ \Carbon\Carbon::parse($election->time_ended)->format('g:i A') }}
                            </p>
                            @endif
                        </div>
                    </div>
                    
                    @if($endDateTime)
                    <div class="flex-shrink-0 w-full sm:w-auto">
                        <div class="countdown-container">
                            <p class="countdown-label mb-2 text-center sm:text-left">Time Remaining</p>
                            <div class="countdown-timer text-center sm:text-left" id="countdown" data-end-time="{{ $endDateTime->timestamp }}">
                                <span class="text-gray-500">Calculating...</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($hasVoted)
            <!-- Already Voted Message -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 text-center">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">You Have Already Voted</h3>
                    <p class="text-gray-600 mb-4">You have already submitted your votes for this election. Thank you for participating!</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-block px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-medium">
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
                    <div class="position-card p-5 sm:p-6 lg:p-7 mb-5 sm:mb-6">
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-4 sm:mb-5 pb-3 border-b-2 border-gray-200 heading-font">
                            {{ $position->name }}
                            <span class="text-xs sm:text-sm font-normal text-gray-500 ml-2">(Select one candidate)</span>
                        </h3>
                        
                        <div class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-1.5 sm:gap-2 md:gap-3 lg:gap-4">
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
                                        <div class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $candidate->partylist->color }};"></div>
                                        @endif
                                        <span class="font-medium">{{ $candidate->partylist->name }}</span>
                                    </div>
                                    @else
                                    <div class="candidate-partylist">
                                        <span class="text-xs text-gray-500 italic font-medium">Independent</span>
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No Candidates</h3>
                    <p class="text-sm sm:text-base text-gray-600">No candidates have been registered for this election yet.</p>
                </div>
                @endforelse

                @if($candidatesByPosition->count() > 0)
                <!-- Enhanced Action Buttons -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5 sm:p-6 sticky bottom-0 z-40 -mx-3 sm:-mx-4 lg:-mx-6 px-3 sm:px-4 lg:px-6">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <button type="button" 
                                    onclick="resetAllVotes()"
                                    id="resetBtn"
                                    class="flex-1 sm:flex-none px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all font-semibold text-sm shadow-sm hover:shadow-md flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Reset All</span>
                            </button>
                            <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-xl">
                                <span class="text-sm text-gray-600">
                                    <span id="voteCount" class="font-bold text-green-600">0</span> / <span id="totalPositions" class="font-semibold">{{ $candidatesByPosition->count() }}</span> positions selected
                                </span>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="showVoteSummary()"
                                id="submitBtn"
                                class="w-full sm:w-auto px-6 sm:px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all font-semibold text-sm sm:text-base shadow-lg hover:shadow-xl flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Submit Votes</span>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500 text-center sm:hidden mt-3 pt-3 border-t border-gray-200">
                        <span id="voteCountMobile" class="font-bold text-green-600">0</span> / <span id="totalPositionsMobile" class="font-semibold">{{ $candidatesByPosition->count() }}</span> positions selected
                    </div>
                </div>
                @endif
            </form>
            @endif

            @if(!$hasVoted)
            <!-- Enhanced Vote Summary Modal -->
            <div id="voteSummaryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-3 sm:p-4" style="backdrop-filter: blur(4px);">
                <div class="bg-white rounded-3xl shadow-2xl max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" style="border-radius: 1.5rem;">
                    <!-- Enhanced Modal Header -->
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50 rounded-t-3xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center shadow-lg flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 heading-font">Review Your Votes</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Please verify your selections before submitting</p>
                                </div>
                            </div>
                            <button onclick="closeSummaryModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition-all flex-shrink-0">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Enhanced Modal Body -->
                    <div class="px-4 sm:px-6 py-4 sm:py-5 overflow-y-auto flex-1 bg-gray-50">
                        <div id="voteSummaryContent" class="space-y-3 sm:space-y-4">
                            <!-- Summary will be populated here -->
                        </div>
                    </div>
                    
                    <!-- Enhanced Modal Footer -->
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-t border-gray-200 bg-white rounded-b-3xl">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-center space-x-2 text-xs sm:text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Once submitted, you cannot change your votes</span>
                            </div>
                            <div class="flex flex-row items-center justify-between gap-2 sm:gap-3">
                                <button onclick="closeSummaryModal()" class="flex-1 sm:flex-none px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all font-medium text-sm sm:text-base shadow-sm hover:shadow-md">
                                    Cancel
                                </button>
                                <button onclick="confirmSubmitVotes()" id="confirmSubmitBtn" class="flex-1 sm:flex-none px-4 sm:px-6 md:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all font-semibold text-sm sm:text-base shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Confirm Votes</span>
                                    <span class="sm:hidden">Confirm Votes</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Success Modal -->
            <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-3 sm:p-4" style="backdrop-filter: blur(4px);">
                <div class="bg-white rounded-3xl shadow-2xl max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" style="border-radius: 1.5rem;">
                    <!-- Success Modal Header -->
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50 rounded-t-3xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-600 to-green-700 rounded-full flex items-center justify-center shadow-lg flex-shrink-0">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 heading-font">Votes Submitted Successfully!</h3>
                                    <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Your votes have been recorded successfully</p>
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
                    <div class="px-4 sm:px-6 py-4 sm:py-5 overflow-y-auto flex-1 bg-gray-50">
                        <div class="flex flex-col items-center justify-center text-center py-4 sm:py-6">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg mb-4 sm:mb-6">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-2 sm:mb-3 heading-font">Thank You for Voting!</h4>
                            <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 max-w-md">Your votes have been recorded. Thank you for participating in this election.</p>
                            <div class="flex items-center justify-center space-x-2 text-xs sm:text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>You will be redirected to the dashboard shortly...</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Success Modal Footer -->
                    <div class="px-4 sm:px-6 py-4 sm:py-5 border-t border-gray-200 bg-white rounded-b-3xl">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-center space-x-2 text-xs sm:text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Your votes have been successfully submitted</span>
                            </div>
                            <div class="flex flex-row items-center justify-between gap-2 sm:gap-3">
                                <button onclick="closeSuccessModal()" class="flex-1 sm:flex-none px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all font-medium text-sm sm:text-base shadow-sm hover:shadow-md">
                                    Close
                                </button>
                                <button onclick="closeSuccessModal()" class="flex-1 sm:flex-none px-4 sm:px-6 md:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all font-semibold text-sm sm:text-base shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Go to Dashboard</span>
                                    <span class="sm:hidden">Dashboard</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function updateCountdown() {
            const countdownElement = document.getElementById('countdown');
            if (!countdownElement) return;
            const targetTimestamp = countdownElement.getAttribute('data-end-time');
            if (!targetTimestamp) return;
            const now = Math.floor(Date.now() / 1000);
            const target = parseInt(targetTimestamp);
            let diff = target - now;
            if (diff <= 0) {
                countdownElement.innerHTML = '<span class="text-red-600">Election Ended</span>';
                document.getElementById('voteForm').style.display = 'none';
                return;
            }
            const days = Math.floor(diff / 86400);
            const hours = Math.floor((diff % 86400) / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            const seconds = diff % 60;
            let timeString = '';
            if (days > 0) timeString += `<span class="text-green-700">${days}d</span> `;
            if (hours > 0 || days > 0) timeString += `<span class="text-green-700">${String(hours).padStart(2, '0')}h</span> `;
            if (minutes > 0 || hours > 0 || days > 0) timeString += `<span class="text-green-700">${String(minutes).padStart(2, '0')}m</span> `;
            timeString += `<span class="text-green-700">${String(seconds).padStart(2, '0')}s</span>`;
            countdownElement.innerHTML = timeString;
        }

        function handleCandidateClick(event, card, candidateId, positionId) {
            event.preventDefault();
            const radio = card.querySelector('input[type="radio"]');
            const positionGroup = card.closest('.bg-white.rounded-xl');
            
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

        // Show vote summary modal
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

            // Build summary HTML
            let summaryHTML = '';
            Object.keys(votesByPosition).forEach(positionId => {
                const positionData = window.candidateData[positionId];
                if (!positionData) return;
                
                summaryHTML += `
                    <div class="summary-position-card rounded-lg p-3 sm:p-4 mb-3 sm:mb-4">
                        <div class="flex items-center space-x-2 mb-2 sm:mb-3">
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-green-600"></div>
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
                                <img src="${candidate.photo}" alt="${candidate.name}" class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 rounded-lg object-cover flex-shrink-0 border-2 border-gray-200" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center hidden flex-shrink-0 border-2 border-gray-200">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            ` : `
                                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center flex-shrink-0 border-2 border-gray-200">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            `}
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-gray-900 text-sm sm:text-base lg:text-lg truncate mb-1">${candidate.name}</p>
                                <div class="flex items-center space-x-2">
                                    ${candidate.partylistColor ? `<div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full flex-shrink-0 shadow-sm" style="background-color: ${candidate.partylistColor}; box-shadow: 0 0 0 2px rgba(255,255,255,0.8);"></div>` : ''}
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

        // Close summary modal
        window.closeSummaryModal = function() {
            document.getElementById('voteSummaryModal').classList.add('hidden');
        };

        // Show success modal
        window.showSuccessModal = function(message) {
            const successModal = document.getElementById('successModal');
            if (!successModal) return;
            
            // Update message if provided
            const messageElement = successModal.querySelector('.text-gray-600');
            if (message && messageElement) {
                messageElement.textContent = message;
            }
            
            successModal.classList.remove('hidden');
            
            // Auto-redirect after 3 seconds
            setTimeout(() => {
                closeSuccessModal();
            }, 3000);
        }

        // Close success modal
        window.closeSuccessModal = function() {
            document.getElementById('successModal').classList.add('hidden');
            window.location.href = '{{ route("student.dashboard") }}';
        }

        // Show error modal
        window.showErrorModal = function(message) {
            alert(message || 'An error occurred. Please try again.');
        }

        // Confirm and submit votes
        window.confirmSubmitVotes = function() {
            const votes = [];
            document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
                votes.push(parseInt(radio.value));
            });

            const confirmBtn = document.getElementById('confirmSubmitBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span>⏳ Submitting...</span>';
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
                    confirmBtn.innerHTML = '<svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span class="hidden sm:inline">Confirm Votes</span><span class="sm:hidden">Confirm Votes</span>';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('An error occurred. Please try again.');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><span class="hidden sm:inline">Confirm Votes</span><span class="sm:hidden">Confirm Votes</span>';
                submitBtn.disabled = false;
            });
        }

        // Close modal when clicking outside
        document.getElementById('voteSummaryModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeSummaryModal();
            }
        });

        document.getElementById('successModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuccessModal();
            }
        });
    </script>
</body>
</html>
