@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Total Users</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">{{ number_format($totalUsers) }}</p>
                    @if($userGrowth != 0)
                    <p class="text-sm mt-1 flex items-center" style="color: var(--cpsu-green-light);">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $userGrowth > 0 ? '+' : '' }}{{ number_format($userGrowth, 1) }}% from last month
                    </p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Active Elections -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Active Elections</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">{{ $activeElections }}</p>
                    @if($endingSoon > 0)
                    <p class="text-sm text-secondary mt-1">{{ $endingSoon }} ending soon</p>
                    @else
                    <p class="text-sm text-secondary mt-1">Ongoing &amp; upcoming only</p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Votes -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Total Votes</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">{{ number_format($totalVotes) }}</p>
                    @if($voteGrowth != 0)
                    <p class="text-sm mt-1 flex items-center" style="color: var(--cpsu-gold);">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $voteGrowth > 0 ? '+' : '' }}{{ number_format($voteGrowth, 1) }}% from last week
                    </p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Participation Rate -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Participation Rate</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">{{ number_format($participationRate, 1) }}%</p>
                    <p class="text-sm text-secondary mt-1">
                        @if($participationRate >= 80)
                            Excellent
                        @elseif($participationRate >= 60)
                            Good
                        @elseif($participationRate >= 40)
                            Average
                        @else
                            Below average
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts and Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="card rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-primary">Recent Activity</h3>
                <a href="#" class="text-sm hover:underline transition-colors" style="color: var(--cpsu-green);">View all</a>
            </div>
            <div class="space-y-4">
                @forelse($recentActivities as $activity)
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 activity-icon-{{ $activity['icon_color'] }}">
                        @if($activity['icon'] === 'check')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @elseif($activity['icon'] === 'user')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-gold);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary">{{ $activity['title'] }}</p>
                        <p class="text-sm text-secondary">{{ $activity['description'] }}</p>
                        <p class="text-xs text-secondary mt-1 opacity-75">{{ $activity['time'] }}</p>
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
            <h3 class="text-lg font-bold text-primary mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.elections.index') }}" class="p-4 rounded-lg text-white transition-all shadow-md hover:shadow-lg btn-cpsu-primary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="font-semibold">Elections</span>
                    </div>
                </a>
                
                <a href="{{ route('admin.users.index') }}" class="p-4 rounded-lg text-white transition-all shadow-md hover:shadow-lg btn-cpsu-primary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="font-semibold">Users</span>
                    </div>
                </a>
                
                <a href="{{ route('admin.candidates.index') }}" class="p-4 rounded-lg transition-all shadow-md hover:shadow-lg btn-cpsu-secondary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-semibold">Candidates</span>
                    </div>
                </a>
                
                <a href="{{ route('admin.students.index') }}" class="p-4 rounded-lg transition-all shadow-md hover:shadow-lg btn-cpsu-secondary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="font-semibold">Students</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Active Elections Table -->
    <div class="card rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-primary">Active Elections</h3>
            <a href="{{ route('admin.elections.index') }}" class="text-sm hover:underline transition-colors" style="color: var(--cpsu-green);">View all elections</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="table-header border-b" style="border-color: var(--border-color);">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Election</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Votes</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">End Date</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    @forelse($activeElectionsList as $election)
                    <tr class="table-row transition-colors">
                        <td class="py-4 px-4">
                            <div class="font-medium text-primary">{{ $election->election_name }}</div>
                            @if($election->organization)
                            <div class="text-sm text-secondary">{{ $election->organization->name }}</div>
                            @elseif($election->type_of_election)
                            <div class="text-sm text-secondary">{{ $election->type_of_election }}</div>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @php
                                $isEndingSoon = $election->time_ended && \Carbon\Carbon::parse($election->time_ended)->diffInDays(\Carbon\Carbon::now()) <= 3 && \Carbon\Carbon::parse($election->time_ended) > \Carbon\Carbon::now();
                            @endphp
                            @if($isEndingSoon)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);">
                                Ending Soon
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                {{ ucfirst($election->status) }}
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-primary">{{ number_format($election->votes_count) }}</td>
                        <td class="py-4 px-4 text-primary">
                            @if($election->time_ended)
                                {{ \Carbon\Carbon::parse($election->time_ended)->format('M d, Y') }}
                            @elseif($election->election_date)
                                {{ \Carbon\Carbon::parse($election->election_date)->format('M d, Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right">
                            <a href="{{ route('admin.elections.show', $election->id) }}" class="hover:underline text-sm font-medium transition-colors" style="color: var(--cpsu-green);">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 px-4 text-center text-secondary">
                            No active elections at the moment
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
