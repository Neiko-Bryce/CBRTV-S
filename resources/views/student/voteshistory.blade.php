<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Voting History - CPSU Voting System</title>
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
        .history-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 102, 51, 0.1);
            transition: all 0.3s ease;
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
            background: linear-gradient(90deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 50%, var(--cpsu-gold) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .history-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 102, 51, 0.15);
        }
        .history-card:hover::before {
            opacity: 1;
        }
        
        /* Voting History Badge */
        .voting-history-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }
        
        /* Candidate Item */
        .voting-history-candidate-item {
            transition: all 0.2s ease;
        }
        .voting-history-candidate-item:hover {
            background: #f9fafb;
            padding-left: 0.75rem;
        }
        
        /* Section Header */
        .section-header {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 102, 51, 0.1);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .history-card {
                border-radius: 0.875rem;
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-base sm:text-xl font-bold text-gray-900 heading-font">Voting History</h1>
                            <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">View all your voting records</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-5 sm:py-6 lg:py-8">
            <div class="mb-5 sm:mb-6 lg:mb-8">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold section-header heading-font mb-2">My Voting History</h2>
                <p class="text-sm sm:text-base text-gray-600">Complete record of all your voting participation</p>
            </div>

            @if(isset($votingHistory) && $votingHistory->count() > 0)
                <div class="space-y-4 sm:space-y-5">
                    @foreach($votingHistory as $history)
                        <div class="history-card p-5 sm:p-6 lg:p-7">
                            <!-- Election Header -->
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4 sm:mb-5 pb-4 border-b border-gray-200">
                                <div class="flex-1">
                                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 sm:mb-3 heading-font">{{ $history['election']->election_name }}</h3>
                                    @if($history['election']->organization)
                                    <p class="text-xs sm:text-sm text-gray-600 mb-2">
                                        <span class="font-medium">Organization:</span> {{ $history['election']->organization->name }}
                                    </p>
                                    @endif
                                    <div class="flex flex-wrap gap-2 sm:gap-3 text-xs sm:text-sm text-gray-600">
                                        <p>
                                            <span class="font-medium">Election Date:</span> 
                                            {{ \Carbon\Carbon::parse($history['election']->election_date)->format('M d, Y') }}
                                        </p>
                                        <p>
                                            <span class="font-medium">Voted On:</span> 
                                            {{ \Carbon\Carbon::parse($history['voted_at'])->format('M d, Y g:i A') }}
                                        </p>
                                    </div>
                                    @if($history['election']->venue)
                                    <p class="text-xs sm:text-sm text-gray-600 mt-2">
                                        <span class="font-medium">Venue:</span> {{ $history['election']->venue }}
                                    </p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="voting-history-badge">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Voted
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Candidates Voted For -->
                            <div class="space-y-3 sm:space-y-4">
                                <h4 class="text-base sm:text-lg font-semibold text-gray-700 mb-3">Your Votes:</h4>
                                @foreach($history['candidates'] as $positionId => $candidates)
                                    @php
                                        $position = $candidates->first()['position'] ?? null;
                                    @endphp
                                    @if($position)
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-3 sm:p-4 border border-gray-200">
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                                            <h5 class="text-sm sm:text-base lg:text-lg font-semibold text-gray-900">{{ $position->name }}</h5>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($candidates as $item)
                                                @php
                                                    $candidate = $item['candidate'];
                                                    $partylist = $item['partylist'];
                                                @endphp
                                                <div class="voting-history-candidate-item bg-white rounded-lg p-3 flex items-center gap-3">
                                                    <div class="flex-1">
                                                        <p class="text-sm sm:text-base lg:text-lg font-medium text-gray-900">{{ $candidate->candidate_name }}</p>
                                                        @if($partylist)
                                                        <div class="flex items-center gap-2 mt-1.5">
                                                            @if($partylist->color)
                                                            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $partylist->color }};"></div>
                                                            @endif
                                                            <span class="text-xs sm:text-sm text-gray-600 font-medium">{{ $partylist->name }}</span>
                                                        </div>
                                                        @else
                                                        <span class="text-xs sm:text-sm text-gray-500 italic">Independent</span>
                                                        @endif
                                                    </div>
                                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Enhanced Empty State -->
                <div class="empty-state p-10 sm:p-14 lg:p-16 text-center">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 lg:w-14 lg:h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-3 heading-font">No Voting History</h3>
                    <p class="text-sm sm:text-base text-gray-600 max-w-md mx-auto mb-6">You haven't participated in any elections yet. Your voting history will appear here once you start voting.</p>
                    <a href="{{ route('student.dashboard') }}" class="inline-block px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-semibold text-sm sm:text-base shadow-md hover:shadow-lg">
                        Return to Dashboard
                    </a>
                </div>
            @endif
        </main>
    </div>
</body>
</html>
