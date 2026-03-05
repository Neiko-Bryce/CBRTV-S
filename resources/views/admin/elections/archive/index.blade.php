@extends('admin.layouts.master')

@section('title', 'Archived Elections')
@section('page-title', 'Archived Elections')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-primary">Archived Elections</h3>
                <p class="text-sm text-secondary mt-1">Read-only election history and records</p>
            </div>
            <a href="{{ route('admin.elections.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Active Elections
            </a>
        </div>

        <div class="card rounded-lg p-4">
            <form method="GET" action="{{ route('admin.archived-elections.index') }}"
                class="flex flex-col md:flex-row gap-3 md:items-end">
                <div class="w-full md:flex-1">
                    <label class="block text-xs font-semibold text-secondary mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Election name, election id, type..."
                        class="w-full px-3 py-2 rounded-lg text-sm"
                        style="background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" />
                </div>
                <div class="w-full md:w-56">
                    <label class="block text-xs font-semibold text-secondary mb-1">Status</label>
                    <select name="status"
                        class="w-full px-3 py-2 rounded-lg text-sm"
                        style="background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <option value="">All</option>
                        <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                        <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                    </select>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-semibold text-white min-w-[88px]"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        Filter
                    </button>
                    <a href="{{ route('admin.archived-elections.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-sm font-medium transition-colors min-w-[88px]"
                        style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="card rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y" style="border-color: var(--border-color);">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Election</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Candidates</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Votes</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Public</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider">Archived At</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-secondary uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--border-color);">
                        @forelse($archivedElections as $archivedElection)
                            <tr class="hover:bg-[var(--hover-bg)] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-primary">{{ $archivedElection->election_name }}</div>
                                    <div class="text-xs text-secondary mt-0.5 font-mono">{{ $archivedElection->election_id ?: 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $status = $archivedElection->status ?? 'completed';
                                        $statusStyles = [
                                            'completed' => 'background: rgba(107, 114, 128, 0.85); color: #fff;',
                                            'cancelled' => 'background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #fff;',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                        style="{{ $statusStyles[$status] ?? $statusStyles['completed'] }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-primary">{{ number_format($archivedElection->candidates_count ?? 0) }}</td>
                                <td class="px-6 py-4 text-sm text-primary">{{ number_format($archivedElection->votes_count ?? 0) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($archivedElection->show_live_results)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                            style="background: rgba(34, 197, 94, 0.15); color: #166534;">
                                            Visible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                            style="background: rgba(148, 163, 184, 0.15); color: #475569;">
                                            Hidden
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-secondary">
                                    {{ $archivedElection->archived_at ? $archivedElection->archived_at->format('M d, Y h:i A') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.archived-elections.show', $archivedElection->id) }}"
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                                        style="background: rgba(22, 101, 52, 0.1); color: var(--cpsu-green); border: 1px solid rgba(22, 101, 52, 0.2);">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-14 text-center text-secondary">
                                    No archived elections found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($archivedElections->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: var(--border-color);">
                    {{ $archivedElections->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
