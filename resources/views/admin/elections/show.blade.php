@extends('admin.layouts.master')

@section('title', 'Election Details')
@section('page-title', 'Election Details')

@section('content')
    @php
        $status = $election->status ?? 'upcoming';
        $statusStyles = [
            'upcoming' => 'background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);',
            'ongoing' => 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%); color: #ffffff;',
            'completed' => 'background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: #ffffff;',
            'cancelled' => 'background: rgba(220, 38, 38, 0.15); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.4);',
        ];
        $statusStyle = $statusStyles[$status] ?? $statusStyles['upcoming'];

        $startTime = '-';
        if (! empty($election->timestarted)) {
            try {
                $startTime = \Carbon\Carbon::parse($election->timestarted)->format('h:i A');
            } catch (\Exception $e) {
                $startTime = $election->timestarted;
            }
        }

        $endTime = '-';
        if (! empty($election->time_ended)) {
            try {
                $endTime = \Carbon\Carbon::parse($election->time_ended)->format('h:i A');
            } catch (\Exception $e) {
                $endTime = $election->time_ended;
            }
        }
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-primary">{{ $election->election_name }}</h3>
                <p class="text-sm text-secondary mt-1">Election details and status overview</p>
            </div>
            <a href="{{ route('admin.elections.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                style="background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Elections
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-secondary">Status</p>
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                        style="{{ $statusStyle }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-secondary">Candidates</p>
                <p class="mt-2 text-2xl font-bold text-primary">{{ number_format($election->candidates_count ?? 0) }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs uppercase tracking-wide text-secondary">Votes</p>
                <p class="mt-2 text-2xl font-bold text-primary">{{ number_format($election->votes_count ?? 0) }}</p>
            </div>
        </div>

        <div class="card p-6">
            <h4 class="text-base font-semibold text-primary mb-4">Election Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Election ID</p>
                    <p class="text-sm font-medium text-primary mt-1">{{ $election->election_id ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Organization</p>
                    <p class="text-sm font-medium text-primary mt-1">{{ $election->organization->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Type of Election</p>
                    <p class="text-sm font-medium text-primary mt-1">{{ $election->type_of_election ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Venue</p>
                    <p class="text-sm font-medium text-primary mt-1">{{ $election->venue ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Election Date</p>
                    <p class="text-sm font-medium text-primary mt-1">
                        {{ $election->election_date ? $election->election_date->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Schedule</p>
                    <p class="text-sm font-medium text-primary mt-1">{{ $startTime }}{{ $endTime !== '-' ? ' - '.$endTime : '' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Created At</p>
                    <p class="text-sm font-medium text-primary mt-1">
                        {{ $election->created_at ? $election->created_at->format('M d, Y h:i A') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Updated At</p>
                    <p class="text-sm font-medium text-primary mt-1">
                        {{ $election->updated_at ? $election->updated_at->format('M d, Y h:i A') : 'N/A' }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-secondary uppercase tracking-wide">Description</p>
                    <p class="text-sm font-medium text-primary mt-1 whitespace-pre-line">
                        {{ $election->description ?: 'No description provided.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
