@extends('admin.layouts.master')

@section('title', 'Archived Election Details')
@section('page-title', 'Archived Election Details')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-primary">{{ $archivedElection->election_name }}</h3>
                <p class="text-sm text-secondary mt-1">Archived election details and final results</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($archivedElection->show_live_results)
                    <form method="POST" action="{{ route('admin.archived-elections.hide', $archivedElection->id) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            style="background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.25);">
                            Hide From Landing Page
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.archived-elections.display', $archivedElection->id) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors"
                            style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                            Show On Landing Page
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.archived-elections.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Archive List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="card rounded-lg p-4">
                <p class="text-xs text-secondary uppercase tracking-wide">Status</p>
                <p class="text-lg font-bold text-primary mt-1">{{ ucfirst($archivedElection->status ?? 'completed') }}</p>
            </div>
            <div class="card rounded-lg p-4">
                <p class="text-xs text-secondary uppercase tracking-wide">Candidates</p>
                <p class="text-lg font-bold text-primary mt-1">{{ number_format($archivedElection->archivedCandidates->count()) }}</p>
            </div>
            <div class="card rounded-lg p-4">
                <p class="text-xs text-secondary uppercase tracking-wide">Votes</p>
                <p class="text-lg font-bold text-primary mt-1">{{ number_format($archivedElection->archived_votes_count ?? 0) }}</p>
            </div>
            <div class="card rounded-lg p-4">
                <p class="text-xs text-secondary uppercase tracking-wide">Archived At</p>
                <p class="text-sm font-semibold text-primary mt-1">
                    {{ $archivedElection->archived_at ? $archivedElection->archived_at->format('M d, Y h:i A') : 'N/A' }}
                </p>
            </div>
            <div class="card rounded-lg p-4">
                <p class="text-xs text-secondary uppercase tracking-wide">Public Visibility</p>
                <p class="text-sm font-semibold mt-1 {{ $archivedElection->show_live_results ? 'text-green-600' : 'text-secondary' }}">
                    {{ $archivedElection->show_live_results ? 'Visible on landing page' : 'Hidden from landing page' }}
                </p>
            </div>
        </div>

        <div class="card rounded-lg p-6">
            <h4 class="text-base font-semibold text-primary mb-4">Election Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Election ID</p>
                    <p class="text-sm text-primary mt-1">{{ $archivedElection->election_id ?: 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Organization</p>
                    <p class="text-sm text-primary mt-1">{{ $archivedElection->organization->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Election Date</p>
                    <p class="text-sm text-primary mt-1">
                        {{ $archivedElection->election_date ? $archivedElection->election_date->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-secondary uppercase tracking-wide">Schedule</p>
                    <p class="text-sm text-primary mt-1">
                        {{ $archivedElection->timestarted ?: '-' }}{{ $archivedElection->time_ended ? ' - '.$archivedElection->time_ended : '' }}
                    </p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-secondary uppercase tracking-wide">Description</p>
                    <p class="text-sm text-primary mt-1 whitespace-pre-line">{{ $archivedElection->description ?: 'No description provided.' }}</p>
                </div>
            </div>
        </div>

        <div class="card rounded-lg p-6" x-data="{ showFinalResults: false }">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h4 class="text-base font-semibold text-primary">Final Results by Position</h4>
                <button type="button"
                    @click="showFinalResults = !showFinalResults"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                    <span x-text="showFinalResults ? 'Hide Results' : 'Show Results'"></span>
                    <svg class="w-4 h-4 transition-transform" :class="showFinalResults ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>

            <p x-show="!showFinalResults" class="text-sm text-secondary">
                Results are hidden to keep this page compact. Click <strong>Show Results</strong> to view
                {{ count($resultsByPosition) }} position{{ count($resultsByPosition) === 1 ? '' : 's' }}.
            </p>

            <div x-show="showFinalResults" x-transition.opacity>
                @forelse($resultsByPosition as $group)
                    <div class="mb-8 last:mb-0">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-semibold uppercase tracking-wide text-secondary">{{ $group['position_name'] }}</h5>
                            <span class="text-xs text-secondary">Slots: {{ $group['number_of_slots'] }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y" style="border-color: var(--border-color);">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Rank</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Candidate</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Partylist</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-secondary uppercase tracking-wider">Votes</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" style="border-color: var(--border-color);">
                                    @foreach($group['candidates'] as $index => $candidate)
                                        @php
                                            $isWinner = $index < ($group['number_of_slots'] ?? 1) && ($candidate->votes_count ?? 0) > 0;
                                        @endphp
                                        <tr class="{{ $isWinner ? 'bg-[rgba(250,204,21,0.08)]' : '' }}">
                                            <td class="px-4 py-2 text-sm text-primary">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 text-sm text-primary">
                                                {{ $candidate->candidate_name }}
                                                @if($isWinner)
                                                    <span class="text-xs font-semibold ml-2" style="color: var(--cpsu-gold-dark);">(Winner)</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-sm text-secondary">{{ $candidate->archivedPartylist->name ?? 'Independent' }}</td>
                                            <td class="px-4 py-2 text-sm text-primary text-right font-semibold">{{ number_format($candidate->votes_count ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-secondary">No archived candidates for this election.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
