@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Users -->
            <div class="card rounded-xl p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-secondary uppercase tracking-wide">Total Users</p>
                        <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-green);">{{ number_format($totalUsers) }}
                        </p>
                        @if ($userGrowth != 0)
                            <p class="text-xs mt-1 flex items-center {{ $userGrowth > 0 ? '' : 'text-red-500' }}"
                                style="{{ $userGrowth > 0 ? 'color: var(--cpsu-green);' : '' }}">
                                <svg class="w-3 h-3 mr-1 {{ $userGrowth < 0 ? 'rotate-180' : '' }}" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $userGrowth > 0 ? '+' : '' }}{{ number_format($userGrowth, 1) }}%
                            </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Elections -->
            <div class="card rounded-xl p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-secondary uppercase tracking-wide">Active Elections</p>
                        <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-green);">{{ $activeElections }}</p>
                        @if ($endingSoon > 0)
                            <p class="text-xs mt-1" style="color: var(--cpsu-gold-dark);">{{ $endingSoon }} ending soon
                            </p>
                        @else
                            <p class="text-xs text-secondary mt-1">Ongoing & upcoming</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Votes -->
            <div class="card rounded-xl p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-secondary uppercase tracking-wide">Total Votes</p>
                        <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-gold-dark);">
                            {{ number_format($totalVotes) }}</p>
                        @if ($voteGrowth != 0)
                            <p class="text-xs mt-1 flex items-center {{ $voteGrowth > 0 ? '' : 'text-red-500' }}"
                                style="{{ $voteGrowth > 0 ? 'color: var(--cpsu-gold-dark);' : '' }}">
                                <svg class="w-3 h-3 mr-1 {{ $voteGrowth < 0 ? 'rotate-180' : '' }}" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $voteGrowth > 0 ? '+' : '' }}{{ number_format($voteGrowth, 1) }}%
                            </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color: var(--cpsu-green-dark);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Participation Rate -->
            <div class="card rounded-xl p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-secondary uppercase tracking-wide">Participation</p>
                        <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-gold-dark);">
                            {{ number_format($participationRate, 1) }}%</p>
                        <p class="text-xs mt-1"
                            style="color: {{ $participationRate >= 60 ? 'var(--cpsu-green)' : ($participationRate >= 40 ? 'var(--cpsu-gold-dark)' : '#ef4444') }};">
                            @if ($participationRate >= 80)
                                Excellent
                            @elseif($participationRate >= 60)
                                Good
                            @elseif($participationRate >= 40)
                                Average
                            @else
                                Needs work
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color: var(--cpsu-green-dark);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Election Status Pie Chart -->
            <div class="card rounded-xl p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary mb-4">Election Status</h3>
                @php
                    $statusColors = [
                        'ongoing' => '#22c55e',
                        'upcoming' => '#3b82f6',
                        'completed' => '#64748b',
                        'cancelled' => '#ef4444',
                    ];
                    $statusLabels = [
                        'ongoing' => 'Ongoing',
                        'upcoming' => 'Upcoming',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ];
                    $gradientParts = [];
                    $currentPercent = 0;
                    $legendItems = [];

                    foreach ($electionStatusCounts as $status => $count) {
                        if ($count > 0) {
                            $percent = $totalElectionsCount > 0 ? ($count / $totalElectionsCount) * 100 : 0;
                            $color = $statusColors[$status] ?? '#94a3b8';
                            $gradientParts[] = "{$color} {$currentPercent}% " . ($currentPercent + $percent) . '%';
                            $legendItems[] = [
                                'label' => $statusLabels[$status] ?? ucfirst($status),
                                'count' => $count,
                                'percent' => round($percent, 1),
                                'color' => $color,
                            ];
                            $currentPercent += $percent;
                        }
                    }
                    $gradient = count($gradientParts) > 0 ? implode(', ', $gradientParts) : '#e5e7eb 0% 100%';
                @endphp

                @if ($totalElectionsCount > 0)
                    <div class="flex items-center gap-6">
                        <div class="relative flex-shrink-0">
                            <div class="w-32 h-32 rounded-full" style="background: conic-gradient({{ $gradient }});">
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-20 h-20 rounded-full flex flex-col items-center justify-center"
                                    style="background: var(--bg-primary);">
                                    <span class="text-xl font-bold text-primary">{{ $totalElectionsCount }}</span>
                                    <span class="text-[10px] text-secondary">Total</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2">
                            @foreach ($legendItems as $item)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full" style="background: {{ $item['color'] }};">
                                        </div>
                                        <span class="text-sm text-primary">{{ $item['label'] }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-secondary">{{ $item['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center py-8">
                        <p class="text-sm text-secondary">No elections yet</p>
                    </div>
                @endif
            </div>

            <!-- Voter Participation -->
            <div class="card rounded-xl p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary mb-4">Voter Participation</h3>
                @php
                    $votedPercent = min($participationRate, 100);
                    $participationGradient = "#22c55e 0% {$votedPercent}%, #e5e7eb {$votedPercent}% 100%";
                @endphp

                <div class="flex items-center gap-6">
                    <div class="relative flex-shrink-0">
                        <div class="w-32 h-32 rounded-full"
                            style="background: conic-gradient({{ $participationGradient }});"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-20 h-20 rounded-full flex flex-col items-center justify-center"
                                style="background: var(--bg-primary);">
                                <span class="text-xl font-bold"
                                    style="color: var(--cpsu-green);">{{ number_format($participationRate, 0) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 space-y-3">
                        <div class="p-3 rounded-lg" style="background: rgba(22, 101, 52, 0.08);">
                            <p class="text-xs text-secondary">Voted</p>
                            <p class="text-lg font-bold" style="color: var(--cpsu-green);">
                                {{ number_format($uniqueVoters) }}</p>
                        </div>
                        <div class="p-3 rounded-lg" style="background: var(--bg-tertiary);">
                            <p class="text-xs text-secondary">Not Voted</p>
                            <p class="text-lg font-bold text-secondary">
                                {{ number_format(max(0, $totalStudents - $uniqueVoters)) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity and Actions Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Activity -->
            <div class="lg:col-span-2 card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-primary">Recent Activity</h3>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium"
                        style="background: rgba(22, 101, 52, 0.1); color: var(--cpsu-green);">Live</span>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start gap-3 p-3 rounded-lg" style="background: var(--bg-tertiary);">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                                style="background: {{ $activity['icon_color'] === 'gold' ? 'rgba(250, 204, 21, 0.2)' : 'rgba(22, 101, 52, 0.1)' }};">
                                @if ($activity['icon'] === 'check')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        style="color: var(--cpsu-green);">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($activity['icon'] === 'user')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        style="color: var(--cpsu-green);">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        style="color: var(--cpsu-gold-dark);">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-primary">{{ $activity['title'] }}</p>
                                <p class="text-xs text-secondary truncate">{{ $activity['description'] }}</p>
                                <p class="text-[10px] text-secondary mt-0.5 opacity-60">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-sm text-secondary">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card rounded-xl p-6 shadow-sm">
                <h3 class="text-base font-semibold text-primary mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.elections.index') }}"
                        class="flex items-center gap-3 p-3 rounded-lg transition-all hover:shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white">Manage Elections</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 p-3 rounded-lg transition-all hover:shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white">Manage Users</span>
                    </a>

                    <a href="{{ route('admin.candidates.index') }}"
                        class="flex items-center gap-3 p-3 rounded-lg transition-all hover:shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                            style="background: rgba(22, 101, 52, 0.15);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--cpsu-green-dark);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium" style="color: var(--cpsu-green-dark);">View Candidates</span>
                    </a>

                    <a href="{{ route('admin.analytics.index') }}"
                        class="flex items-center gap-3 p-3 rounded-lg transition-all hover:shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                            style="background: rgba(22, 101, 52, 0.15);">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: var(--cpsu-green-dark);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium" style="color: var(--cpsu-green-dark);">View Analytics</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Elections Table -->
        <div class="card rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-primary">Active Elections</h3>
                <a href="{{ route('admin.elections.index') }}"
                    class="text-xs font-medium px-3 py-1.5 rounded-lg transition-all"
                    style="background: var(--cpsu-green); color: white;">View All</a>
            </div>
            <div class="overflow-x-auto -mx-6">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="border-b" style="border-color: var(--border-color);">
                            <th class="text-left py-3 px-6 text-xs font-semibold text-secondary uppercase tracking-wide">
                                Election</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-secondary uppercase tracking-wide">
                                Status</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-secondary uppercase tracking-wide">
                                Votes</th>
                            <th class="text-right py-3 px-4 text-xs font-semibold text-secondary uppercase tracking-wide">
                                End Date</th>
                            <th class="text-right py-3 px-6 text-xs font-semibold text-secondary uppercase tracking-wide">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeElectionsList as $election)
                            <tr class="border-b last:border-b-0 transition-colors"
                                style="border-color: var(--border-color);">
                                <td class="py-3 px-6">
                                    <p class="text-sm font-medium text-primary">{{ $election->election_name }}</p>
                                    @if ($election->organization)
                                        <p class="text-xs text-secondary">{{ $election->organization->name }}</p>
                                    @elseif($election->type_of_election)
                                        <p class="text-xs text-secondary">{{ $election->type_of_election }}</p>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($election->status === 'ongoing')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium text-white"
                                            style="background: var(--cpsu-green);">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            Live
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium text-white"
                                            style="background: #3b82f6;">
                                            {{ ucfirst($election->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span
                                        class="text-sm font-semibold text-primary">{{ number_format($election->votes_count) }}</span>
                                </td>
                                <td class="py-3 px-4 text-right text-sm text-secondary">
                                    @if ($election->election_date)
                                        {{ \Carbon\Carbon::parse($election->election_date)->format('M d, Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-right">
                                    <a href="{{ route('admin.elections.show', $election->id) }}"
                                        class="text-xs font-medium px-2.5 py-1 rounded transition-colors"
                                        style="color: var(--cpsu-green);">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center">
                                    <p class="text-sm text-secondary">No active elections</p>
                                    <a href="{{ route('admin.elections.index') }}"
                                        class="inline-block mt-2 text-xs font-medium px-3 py-1.5 rounded-lg"
                                        style="background: var(--cpsu-green); color: white;">Create Election</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
