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
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">{{ number_format($totalVotes) }}</p>
                    <p class="text-sm text-secondary mt-1">All time</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Participation Rate</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">{{ number_format($participationRate, 1) }}%</p>
                    <p class="text-sm text-secondary mt-1">{{ number_format($uniqueVoters) }} of {{ number_format($totalStudents) }} students voted</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Total Elections</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">{{ $totalElections }}</p>
                    <p class="text-sm text-secondary mt-1">{{ $completedElections }} completed · {{ $ongoingElections }} ongoing · {{ $upcomingElections }} upcoming</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Unique Voters</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">{{ number_format($uniqueVoters) }}</p>
                    <p class="text-sm text-secondary mt-1">Distinct students who voted</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Votes in last 7 days -->
    <div class="card rounded-xl p-6 shadow-sm">
        <h3 class="text-lg font-bold text-primary mb-6">Votes in the last 7 days</h3>
        <div class="flex items-end gap-2 sm:gap-4" style="min-height: 140px;">
            @foreach($last7Days as $day)
            <div class="flex-1 flex flex-col items-center gap-2">
                <span class="text-xs font-medium text-secondary">{{ $day['count'] }}</span>
                <div class="w-full rounded-t flex flex-col justify-end h-24" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                    @if($maxVotesInPeriod > 0 && $day['count'] > 0)
                    <div class="w-full rounded-t transition-all" style="height: {{ max(8, ($day['count'] / $maxVotesInPeriod) * 100) }}%; background: linear-gradient(180deg, var(--cpsu-green) 0%, var(--cpsu-green-dark) 100%);"></div>
                    @endif
                </div>
                <span class="text-xs text-secondary">{{ $day['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Elections breakdown table -->
    <div class="card rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-primary">Election statistics</h3>
            <a href="{{ route('admin.elections.index') }}" class="text-sm hover:underline transition-colors" style="color: var(--cpsu-green);">View all elections</a>
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
                            @if($election->organization)
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($status === 'completed') text-white" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);"
                                @elseif($status === 'ongoing') text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);"
                                @elseif($status === 'upcoming') text-secondary" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);"
                                @else text-secondary" style="background: var(--bg-tertiary);"
                                @endif
                            >
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right font-medium text-primary">{{ number_format($election->votes_count) }}</td>
                        <td class="py-4 px-4 text-right text-secondary">{{ number_format($election->participation_percent, 1) }}%</td>
                        <td class="py-4 px-4 text-right">
                            <a href="{{ route('admin.elections.show', $election->id) }}" class="text-sm font-medium hover:underline transition-colors" style="color: var(--cpsu-green);">View</a>
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
