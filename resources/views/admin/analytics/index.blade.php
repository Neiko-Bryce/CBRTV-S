@extends('admin.layouts.master')

@section('title', 'Analytics')
@section('page-title', 'Analytics')

@section('content')
    <div class="space-y-6">
        <p class="text-sm text-secondary">Voting statistics, trends, and election breakdowns.</p>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary">Total Votes</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">{{ number_format($totalVotes) }}
                        </p>
                        <p class="text-sm text-secondary mt-1">All time</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary">Participation Rate</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">
                            {{ number_format($participationRate, 1) }}%</p>
                        <p class="text-sm text-secondary mt-1">{{ number_format($uniqueVoters) }} of
                            {{ number_format($totalStudents) }} students voted</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary">Total Elections</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">{{ $totalElections }}</p>
                        <p class="text-sm text-secondary mt-1">{{ $completedElections }} completed · {{ $ongoingElections }}
                            ongoing · {{ $upcomingElections }} upcoming</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color: var(--cpsu-green-dark);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary">Unique Voters</p>
                        <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">
                            {{ number_format($uniqueVoters) }}</p>
                        <p class="text-sm text-secondary mt-1">Distinct students who voted</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color: var(--cpsu-green-dark);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Votes in last 7 days -->
        <div class="card rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-primary mb-6">Votes in the last 7 days</h3>
            <div class="flex items-end gap-2 sm:gap-4" style="min-height: 140px;">
                @foreach ($last7Days as $day)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <span class="text-xs font-medium text-secondary">{{ $day['count'] }}</span>
                        <div class="w-full rounded-t flex flex-col justify-end h-24"
                            style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                            @if ($maxVotesInPeriod > 0 && $day['count'] > 0)
                                <div class="w-full rounded-t transition-all"
                                    style="height: {{ max(8, ($day['count'] / $maxVotesInPeriod) * 100) }}%; background: linear-gradient(180deg, var(--cpsu-green) 0%, var(--cpsu-green-dark) 100%);">
                                </div>
                            @endif
                        </div>
                        <span class="text-xs text-secondary">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- NEW: Votes by Year Level (Pie Chart) -->
        <div class="card rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-primary mb-6">Votes by Year Level</h3>
            @php
                $totalYearVotes = $votesByYearLevel->sum('count');
                $colors = ['#166534', '#22c55e', '#facc15', '#eab308', '#84cc16', '#14b8a6'];
                $gradientParts = [];
                $currentPercent = 0;
                $legendItems = [];

                foreach ($votesByYearLevel as $index => $yearData) {
                    $percent = $totalYearVotes > 0 ? ($yearData['count'] / $totalYearVotes) * 100 : 0;
                    $color = $colors[$index % count($colors)];
                    $gradientParts[] = "{$color} {$currentPercent}% " . ($currentPercent + $percent) . '%';
                    $legendItems[] = [
                        'label' => $yearData['yearlevel'],
                        'count' => $yearData['count'],
                        'percent' => round($percent, 1),
                        'color' => $color,
                    ];
                    $currentPercent += $percent;
                }
                $gradient = implode(', ', $gradientParts);
            @endphp

            @if ($totalYearVotes > 0)
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <!-- Pie Chart -->
                    <div class="relative">
                        <div class="w-48 h-48 rounded-full shadow-lg"
                            style="background: conic-gradient({{ $gradient }});"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-24 h-24 rounded-full flex flex-col items-center justify-center"
                                style="background: var(--bg-primary);">
                                <span class="text-2xl font-bold text-primary">{{ number_format($totalYearVotes) }}</span>
                                <span class="text-xs text-secondary">Total Votes</span>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="flex-1 grid grid-cols-2 gap-3">
                        @foreach ($legendItems as $item)
                            <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--bg-tertiary);">
                                <div class="w-4 h-4 rounded-full flex-shrink-0" style="background: {{ $item['color'] }};">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-primary truncate">{{ $item['label'] }}</p>
                                    <p class="text-xs text-secondary">{{ number_format($item['count']) }} votes
                                        ({{ $item['percent'] }}%)
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-center text-secondary py-8">No voting data available</p>
            @endif
        </div>

        <!-- NEW: Peak Voting Hours (Enhanced) -->
        <div class="card rounded-xl p-6 shadow-sm">
            @php
                $peakHour = collect($peakVotingHours)->sortByDesc('count')->first();
                $totalHourlyVotes = collect($peakVotingHours)->sum('count');
            @endphp

            <!-- Header with Stats -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-primary">Peak Voting Hours</h3>
                    <p class="text-sm text-secondary">When students vote most frequently</p>
                </div>
                @if ($totalHourlyVotes > 0 && $peakHour)
                    <div class="flex gap-3">
                        <div class="px-3 py-2 rounded-lg text-center"
                            style="background: rgba(22, 101, 52, 0.1); border: 1px solid var(--cpsu-green);">
                            <p class="text-[10px] text-secondary uppercase">Peak Hour</p>
                            <p class="text-lg font-bold" style="color: var(--cpsu-green);">
                                {{ sprintf('%02d:00', $peakHour['hour']) }}</p>
                        </div>
                        <div class="px-3 py-2 rounded-lg text-center"
                            style="background: rgba(250, 204, 21, 0.1); border: 1px solid var(--cpsu-gold);">
                            <p class="text-[10px] text-secondary uppercase">Total Votes</p>
                            <p class="text-lg font-bold" style="color: var(--cpsu-gold-dark);">
                                {{ number_format($totalHourlyVotes) }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Chart (scrollable on mobile) -->
            <div class="overflow-x-auto -mx-2 px-2 pb-2">
                <div class="flex items-end gap-[2px]" style="min-height: 120px; min-width: 600px;">
                    @foreach ($peakVotingHours as $hourData)
                        @php
                            $isPeak = $peakHour && $hourData['hour'] === $peakHour['hour'] && $hourData['count'] > 0;
                            // Color based on time of day
                            if ($hourData['hour'] >= 6 && $hourData['hour'] < 12) {
                                $barColor = '#22c55e'; // Morning - Green
                            } elseif ($hourData['hour'] >= 12 && $hourData['hour'] < 18) {
                                $barColor = '#facc15'; // Afternoon - Gold
                            } elseif ($hourData['hour'] >= 18 && $hourData['hour'] < 22) {
                                $barColor = '#f97316'; // Evening - Orange
                            } else {
                                $barColor = '#6366f1'; // Night - Indigo
                            }
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1" style="min-width: 22px;">
                            <span
                                class="text-[9px] font-medium {{ $hourData['count'] > 0 ? 'text-primary' : 'text-transparent' }}">
                                {{ $hourData['count'] > 0 ? $hourData['count'] : '0' }}
                            </span>
                            <div class="w-full rounded-t flex flex-col justify-end {{ $isPeak ? 'ring-2 ring-green-500 ring-offset-1' : '' }}"
                                style="height: 80px; background: var(--bg-tertiary);">
                                @if ($hourData['count'] > 0)
                                    <div class="w-full rounded-t transition-all hover:opacity-80"
                                        style="height: {{ max(8, ($hourData['count'] / $maxVotesByHour) * 100) }}%; background: {{ $barColor }};">
                                    </div>
                                @endif
                            </div>
                            <span
                                class="text-[9px] {{ $hourData['hour'] % 6 === 0 ? 'font-medium text-primary' : 'text-secondary' }}">
                                {{ sprintf('%02d', $hourData['hour']) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            <p class="text-[10px] text-secondary text-center mt-1 sm:hidden">← Scroll to see all hours →</p>

            <!-- Legend -->
            <div class="flex flex-wrap justify-center gap-4 mt-4 pt-3 border-t"
                style="border-color: var(--border-color);">
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-sm" style="background: #22c55e;"></div>
                    <span class="text-[10px] text-secondary">Morning (6-12)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-sm" style="background: #facc15;"></div>
                    <span class="text-[10px] text-secondary">Afternoon (12-18)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-sm" style="background: #f97316;"></div>
                    <span class="text-[10px] text-secondary">Evening (18-22)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-sm" style="background: #6366f1;"></div>
                    <span class="text-[10px] text-secondary">Night (22-6)</span>
                </div>
            </div>
        </div>

        <!-- NEW: Election Comparison -->
        @if ($electionComparison['current'] || $electionComparison['previous'])
            <div class="card rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-primary mb-6">Election Comparison</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if ($electionComparison['current'])
                        <div class="p-4 rounded-xl"
                            style="background: linear-gradient(135deg, rgba(22, 101, 52, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%); border: 1px solid var(--cpsu-green);">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-1 text-xs font-bold rounded text-white"
                                    style="background: var(--cpsu-green);">LATEST</span>
                                <span
                                    class="text-xs text-secondary">{{ $electionComparison['current']['date'] ? \Carbon\Carbon::parse($electionComparison['current']['date'])->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <h4 class="font-semibold text-primary mb-2">{{ $electionComparison['current']['name'] }}</h4>
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-bold"
                                    style="color: var(--cpsu-green);">{{ $electionComparison['current']['participation'] }}%</span>
                                <span class="text-sm text-secondary">participation</span>
                            </div>
                            <p class="text-sm text-secondary mt-1">
                                {{ number_format($electionComparison['current']['unique_voters']) }} voters</p>
                        </div>
                    @endif

                    @if ($electionComparison['previous'])
                        <div class="p-4 rounded-xl"
                            style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="px-2 py-1 text-xs font-medium rounded text-secondary"
                                    style="background: var(--bg-secondary);">PREVIOUS</span>
                                <span
                                    class="text-xs text-secondary">{{ $electionComparison['previous']['date'] ? \Carbon\Carbon::parse($electionComparison['previous']['date'])->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <h4 class="font-semibold text-primary mb-2">{{ $electionComparison['previous']['name'] }}</h4>
                            <div class="flex items-baseline gap-2">
                                <span
                                    class="text-3xl font-bold text-secondary">{{ $electionComparison['previous']['participation'] }}%</span>
                                <span class="text-sm text-secondary">participation</span>
                            </div>
                            <p class="text-sm text-secondary mt-1">
                                {{ number_format($electionComparison['previous']['unique_voters']) }} voters</p>

                            @if ($electionComparison['current'])
                                @php
                                    $diff =
                                        $electionComparison['current']['participation'] -
                                        $electionComparison['previous']['participation'];
                                @endphp
                                <div class="mt-3 pt-3 border-t" style="border-color: var(--border-color);">
                                    <div class="flex items-center gap-2">
                                        @if ($diff > 0)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" style="color: var(--cpsu-green);">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            <span class="text-sm font-medium"
                                                style="color: var(--cpsu-green);">+{{ number_format($diff, 1) }}%
                                                growth</span>
                                        @elseif($diff < 0)
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-red-500">{{ number_format($diff, 1) }}%
                                                decline</span>
                                        @else
                                            <span class="text-sm font-medium text-secondary">No change</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 rounded-xl flex items-center justify-center"
                            style="background: var(--bg-tertiary); border: 1px dashed var(--border-color);">
                            <p class="text-sm text-secondary text-center">No previous election to compare</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Elections breakdown table -->
        <div class="card rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-primary">Election statistics</h3>
                <a href="{{ route('admin.elections.index') }}" class="text-sm hover:underline transition-colors"
                    style="color: var(--cpsu-green);">View all elections</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="table-header border-b" style="border-color: var(--border-color);">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Election</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Organization / Type</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Status</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Votes</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Participation</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-color);">
                        @forelse($electionsWithStats as $election)
                            <tr class="table-row transition-colors">
                                <td class="py-4 px-4">
                                    <div class="font-medium text-primary">{{ $election->election_name }}</div>
                                </td>
                                <td class="py-4 px-4 text-secondary text-sm">
                                    @if ($election->organization)
                                        {{ $election->organization->name }}
                                    @elseif($election->type_of_election)
                                        {{ $election->type_of_election }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    @php
                                        $status = $election->status ?? 'upcoming';
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if ($status === 'completed') text-white" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);"
                                @elseif($status === 'ongoing') text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);"
                                @elseif($status === 'upcoming') text-secondary" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);"
                                @else text-secondary" style="background: var(--bg-tertiary);" @endif
                            >
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="py-4
                                        px-4 text-right font-medium
                                        text-primary">{{ number_format($election->votes_count) }}
                                </td>
                                <td class="py-4 px-4 text-right text-secondary">
                                    {{ number_format($election->participation_percent, 1) }}%</td>
                                <td class="py-4 px-4 text-right">
                                    <a href="{{ route('admin.elections.show', $election->id) }}"
                                        class="text-sm font-medium hover:underline transition-colors"
                                        style="color: var(--cpsu-green);">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-secondary">No elections yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
