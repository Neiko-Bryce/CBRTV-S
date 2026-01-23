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
        }
        .heading-font {
            font-family: 'Playfair Display', serif;
        }
        .candidate-photo {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid #e2e8f0;
        }
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
        @media (max-width: 640px) {
            .candidate-photo {
                width: 60px;
                height: 60px;
            }
            .countdown-timer {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-700 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-sm sm:text-lg font-bold text-gray-900 heading-font">CPSU Voting System</h1>
                            <p class="text-xs text-gray-500 hidden sm:block">Student Portal</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <span class="text-xs sm:text-sm text-gray-600 hidden sm:inline">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs sm:text-sm text-red-600 hover:text-red-700 font-medium px-2 py-1">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
            @if(session('success'))
                <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm sm:text-base text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="mb-4 sm:mb-6">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 heading-font">Student Dashboard</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1 sm:mt-2">View and participate in active elections</p>
            </div>

            @forelse($elections as $election)
            <!-- Election Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-4 sm:mb-6" data-election-id="{{ $election->id }}">
                <div class="mb-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <h3 class="text-lg sm:text-xl font-bold text-gray-900">{{ $election->election_name }}</h3>
                                <span class="px-2 sm:px-3 py-1 {{ $election->status === 'ongoing' ? 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-yellow-900' : 'bg-gradient-to-r from-green-600 to-green-700 text-white' }} text-xs font-semibold rounded-full">
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
                        
                        <!-- Countdown Timer -->
                        <div class="flex-shrink-0">
                            <div class="bg-gray-50 rounded-lg p-3 sm:p-4 border border-gray-200">
                                @if($election->status === 'ongoing')
                                    <p class="countdown-label mb-1">Time Remaining</p>
                                    <div class="countdown-timer" id="countdown-{{ $election->id }}" data-end-time="{{ $election->end_datetime ? $election->end_datetime->timestamp : '' }}">
                                        <span class="text-gray-500">Calculating...</span>
                                    </div>
                                @else
                                    <p class="countdown-label mb-1">Starts In</p>
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

                <!-- Vote Now Button (only for ongoing elections with candidates) -->
                @if($election->status === 'ongoing' && $election->candidatesByPosition && $election->candidatesByPosition->count() > 0)
                <div class="mb-4">
                    <a href="{{ route('student.vote', $election->id) }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-semibold text-sm sm:text-base shadow-md hover:shadow-lg space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Vote Now</span>
                    </a>
                </div>
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
            <!-- No Elections -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 sm:p-12 text-center">
                <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">No Upcoming Elections</h3>
                <p class="text-sm sm:text-base text-gray-600">There are currently no upcoming or ongoing elections available.</p>
            </div>
            @endforelse

            <!-- My Votes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mt-4 sm:mt-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-3 sm:mb-4">My Voting History</h3>
                <p class="text-sm sm:text-base text-gray-600">You haven't participated in any elections yet.</p>
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
