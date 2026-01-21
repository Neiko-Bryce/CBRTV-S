@extends('admin.layouts.master')

@section('title', 'Elections Management')
@section('page-title', 'Elections Management')

@push('styles')
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .dark .modal {
        background-color: rgba(0, 0, 0, 0.7);
    }
    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background-color: var(--card-bg);
        margin: auto;
        border-radius: 0.75rem;
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid var(--border-color);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: modalSlideIn 0.3s ease-out;
    }
    .dark .modal-content {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
    }
    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    .close {
        color: var(--text-secondary);
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.2s;
    }
    .close:hover {
        color: var(--text-primary);
    }
    
    /* Ensure all text uses CSS variables */
    label {
        color: var(--text-primary);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-primary">All Elections</h3>
            <p class="text-sm text-secondary mt-1">Manage election records and information</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Election
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4" id="statsContainer">
        <div class="card rounded-lg p-4 stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Total Elections</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-green);" id="stat-total">{{ $stats['total'] ?? $elections->total() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Upcoming</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-green);" id="stat-upcoming">{{ $stats['upcoming'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Ongoing</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-gold-dark);" id="stat-ongoing">{{ $stats['ongoing'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-primary" style="cursor: pointer;" onclick="filterByStatus('completed')" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Completed</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-green);" id="stat-completed">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-primary" style="cursor: pointer;" onclick="filterByStatus('cancelled')" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Cancelled</p>
                    <p class="text-2xl font-bold mt-1" style="color: #dc2626;" id="stat-cancelled">{{ $stats['cancelled'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if(request('search') || request('status'))
        <div class="mb-4 p-3 rounded-lg text-sm" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
            @if(request('status'))
                <span style="color: var(--text-secondary);">Showing <strong style="color: var(--text-primary);">{{ $elections->total() }}</strong> election(s) with status: <strong style="color: var(--cpsu-green);">{{ ucfirst(request('status')) }}</strong></span>
            @endif
            @if(request('search'))
                <span style="color: var(--text-secondary);">Found <strong style="color: var(--text-primary);">{{ $elections->total() }}</strong> result(s) for "<strong style="color: var(--cpsu-green);">{{ request('search') }}</strong>"</span>
            @endif
            <a href="{{ route('admin.elections.index') }}" class="ml-3 text-sm font-medium" style="color: var(--cpsu-green);">Clear filter</a>
        </div>
    @endif

    <!-- Elections Table -->
    <div class="card rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Election
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Organization
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Schedule
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Countdown
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="electionsTableBody">
                    @forelse($elections as $election)
                    <tr class="table-row transition-colors border-b hover:bg-[var(--hover-bg)]" style="border-color: var(--border-color);" id="election-row-{{ $election->id }}" data-election-id="{{ $election->id }}">
                        <td class="px-6 py-4 align-middle">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-primary election-name">
                                    {{ $election->election_name }}
                                </div>
                                <div class="text-xs text-secondary mt-0.5">
                                    <span class="font-mono">{{ $election->election_id ?? $election->id }}</span>
                                </div>
                                @if($election->description)
                                    <div class="text-xs text-secondary mt-1 truncate max-w-xs election-description">{{ \Illuminate\Support\Str::limit($election->description, 60) }}</div>
                                @endif
                                @if($election->venue)
                                    <div class="text-xs text-secondary mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="truncate">{{ $election->venue }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium text-white election-type" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                {{ $election->organization->name ?? $election->type_of_election }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="text-sm text-primary">
                                <div class="font-medium">{{ \Carbon\Carbon::parse($election->election_date)->format('M d, Y') }}</div>
                                @if($election->timestarted && $election->time_ended)
                                    @php
                                        try {
                                            $startTime = \Carbon\Carbon::parse($election->timestarted)->format('h:i A');
                                            $endTime = \Carbon\Carbon::parse($election->time_ended)->format('h:i A');
                                        } catch (\Exception $e) {
                                            $startTime = $election->timestarted;
                                            $endTime = $election->time_ended;
                                        }
                                    @endphp
                                    <div class="text-xs text-secondary mt-1">{{ $startTime }} - {{ $endTime }}</div>
                                @elseif($election->timestarted)
                                    @php
                                        try {
                                            $startTime = \Carbon\Carbon::parse($election->timestarted)->format('h:i A');
                                        } catch (\Exception $e) {
                                            $startTime = $election->timestarted;
                                        }
                                    @endphp
                                    <div class="text-xs text-secondary mt-1">Starts: {{ $startTime }}</div>
                                @else
                                    <div class="text-xs text-secondary mt-1 opacity-75">Time not set</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            @php
                                $electionTimestamp = null;
                                $endTimestamp = null;
                                
                                // Ensure we have an election_date
                                if ($election->election_date) {
                                    try {
                                        // Get date string - election_date is cast as 'date' so it's a Carbon instance
                                        $dateString = $election->election_date->format('Y-m-d');
                                        
                                        // If we have a start time, combine it with the date
                                        if ($election->timestarted && !empty(trim($election->timestarted))) {
                                            // timestarted might be in H:i:s or H:i format
                                            $timeStr = trim($election->timestarted);
                                            
                                            // Normalize time format to H:i:s
                                            $timeParts = explode(':', $timeStr);
                                            if (count($timeParts) == 2) {
                                                // H:i format, add seconds
                                                $timeStr = $timeParts[0] . ':' . $timeParts[1] . ':00';
                                            } elseif (count($timeParts) == 3) {
                                                // Already H:i:s format, use as is
                                                $timeStr = $timeStr;
                                            } else {
                                                // Invalid format, skip
                                                $timeStr = null;
                                            }
                                            
                                            if ($timeStr) {
                                                // Create datetime string in format: "Y-m-d H:i:s"
                                                $datetimeString = $dateString . ' ' . $timeStr;
                                                
                                                // Parse in Asia/Manila timezone - this ensures the time is treated as PH time
                                                $electionDT = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $datetimeString, 'Asia/Manila');
                                                
                                                // Get UTC timestamp (Carbon's timestamp is always in UTC)
                                                $electionTimestamp = $electionDT->timestamp * 1000;
                                            } else {
                                                // Invalid time format, use start of day
                                                $electionDT = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->startOfDay();
                                                $electionTimestamp = $electionDT->timestamp * 1000;
                                            }
                                        } else {
                                            // No start time, use start of day in Asia/Manila
                                            $electionDT = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString, 'Asia/Manila')->startOfDay();
                                            $electionTimestamp = $electionDT->timestamp * 1000;
                                        }
                                        
                                        // Calculate end timestamp if time_ended exists
                                        if ($election->time_ended && !empty(trim($election->time_ended)) && $electionTimestamp) {
                                            $endTimeStr = trim($election->time_ended);
                                            
                                            // Normalize time format to H:i:s
                                            $endTimeParts = explode(':', $endTimeStr);
                                            if (count($endTimeParts) == 2) {
                                                // H:i format, add seconds
                                                $endTimeStr = $endTimeParts[0] . ':' . $endTimeParts[1] . ':00';
                                            } elseif (count($endTimeParts) == 3) {
                                                // Already H:i:s format, use as is
                                                $endTimeStr = $endTimeStr;
                                            } else {
                                                $endTimeStr = null;
                                            }
                                            
                                            if ($endTimeStr) {
                                                $endDatetimeString = $dateString . ' ' . $endTimeStr;
                                                $endDT = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $endDatetimeString, 'Asia/Manila');
                                                $endTimestamp = $endDT->timestamp * 1000;
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Log error with more details for debugging
                                        \Log::error('Error calculating election timestamp for election ' . $election->id . ': ' . $e->getMessage());
                                        \Log::error('Stack trace: ' . $e->getTraceAsString());
                                        $electionTimestamp = null;
                                        $endTimestamp = null;
                                    }
                                }
                            @endphp
                            <div class="countdown-container" 
                                 data-election-id="{{ $election->id }}"
                                 data-election-date="{{ $election->election_date instanceof \Carbon\Carbon ? $election->election_date->format('Y-m-d') : $election->election_date }}"
                                 data-election-time="{{ $election->timestarted ?? '' }}"
                                 data-election-ended="{{ $election->time_ended ?? '' }}"
                                 data-election-timestamp="{{ $electionTimestamp ?? '' }}"
                                 data-end-timestamp="{{ $endTimestamp ?? '' }}">
                                @if($electionTimestamp && $electionTimestamp > 0)
                                    <div class="text-sm font-semibold" style="color: var(--cpsu-green);">
                                        <span class="countdown-text">Loading...</span>
                                    </div>
                                @else
                                    <span class="text-sm text-secondary opacity-75">No date/time set</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="status-container" data-election-id="{{ $election->id }}" data-status="{{ $election->status ?? 'upcoming' }}">
                                @php
                                    $status = $election->status ?? 'upcoming';
                                    $statusColors = [
                                        'upcoming' => 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);',
                                        'ongoing' => 'background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);',
                                        'completed' => 'background: rgba(107, 114, 128, 0.8);',
                                        'cancelled' => 'background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);'
                                    ];
                                    $statusLabels = [
                                        'upcoming' => 'Upcoming',
                                        'ongoing' => 'Ongoing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled'
                                    ];
                                @endphp
                                <span class="status-badge inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium text-white" style="{{ $statusColors[$status] ?? $statusColors['upcoming'] }}">
                                    {{ $statusLabels[$status] ?? 'Upcoming' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="viewElection({{ $election->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green);" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button onclick="editElection({{ $election->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green-light);" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteElection({{ $election->id }}, '{{ $election->election_name }}')" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="text-secondary opacity-75">
                                <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-secondary);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-lg font-semibold text-primary mb-1">No elections found</p>
                                <p class="text-sm text-secondary">Get started by creating a new election</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        @if($elections->hasPages() || $elections->total() > 0)
        <div class="px-6 py-4 border-t transition-colors" style="border-color: var(--border-color);">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Pagination Info -->
                <div class="text-sm" style="color: var(--text-secondary);">
                    @if($elections->total() > 0)
                        Showing <strong style="color: var(--text-primary);">{{ $elections->firstItem() }}</strong> to 
                        <strong style="color: var(--text-primary);">{{ $elections->lastItem() }}</strong> of 
                        <strong style="color: var(--cpsu-green);">{{ $elections->total() }}</strong> 
                        {{ $elections->total() === 1 ? 'election' : 'elections' }}
                        @if(request('search'))
                            <span class="ml-2">for "<strong style="color: var(--cpsu-green);">{{ request('search') }}</strong>"</span>
                        @endif
                    @else
                        No elections found
                    @endif
                </div>
                
                <!-- Pagination Links -->
                @if($elections->hasPages())
                <div class="flex items-center space-x-1">
                    <!-- First Page -->
                    @if($elections->onFirstPage())
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $elections->url(1) }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="First page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif
                    
                    <!-- Previous Page -->
                    @if($elections->onFirstPage())
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $elections->previousPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="Previous page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif
                    
                    <!-- Page Numbers -->
                    @foreach($elections->getUrlRange(max(1, $elections->currentPage() - 2), min($elections->lastPage(), $elections->currentPage() + 2)) as $page => $url)
                        @if($page == $elections->currentPage())
                            <span class="px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                    
                    <!-- Next Page -->
                    @if($elections->hasMorePages())
                        <a href="{{ $elections->nextPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="Next page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    @endif
                    
                    <!-- Last Page -->
                    @if($elections->hasMorePages())
                        <a href="{{ $elections->url($elections->lastPage()) }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="Last page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Election Modal -->
<div id="electionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary" id="modalTitle">Add New Election</h3>
            <span class="close" onclick="closeModal('electionModal')">&times;</span>
        </div>
        <form id="electionForm" onsubmit="saveElection(event)">
            <div class="modal-body">
                <input type="hidden" id="electionId" name="id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="space-y-4">
                    <div>
                        <label for="election_name" class="block text-sm font-medium text-primary mb-2">Election Name *</label>
                        <input type="text" id="election_name" name="election_name" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <div id="election_name-error" class="text-red-500 text-sm mt-1"></div>
                        <p class="text-xs text-secondary mt-1">Election ID will be auto-generated</p>
                    </div>
                    
                    <div>
                        <label for="organization_id" class="block text-sm font-medium text-primary mb-2">Type of Election (Organization) *</label>
                        <select id="organization_id" name="organization_id" required onchange="updateTypeOfElection()" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <option value="">Select Organization</option>
                            @foreach($organizations as $org)
                            <option value="{{ $org->id }}" data-name="{{ $org->name }}">{{ $org->name }}{{ $org->code ? ' (' . $org->code . ')' : '' }}</option>
                            @endforeach
                        </select>
                        <div id="organization_id-error" class="text-red-500 text-sm mt-1"></div>
                        <p class="text-xs text-secondary mt-1">Select the organization type (SSG, FLP, Classroom, etc.)</p>
                        <!-- Hidden field to maintain backward compatibility -->
                        <input type="hidden" id="type_of_election" name="type_of_election">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-primary mb-2">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);"></textarea>
                        <div id="description-error" class="text-red-500 text-sm mt-1"></div>
                    </div>
                    
                    <div id="statusFieldContainer" style="display: none;">
                        <label for="status" class="block text-sm font-medium text-primary mb-2">Status</label>
                        <select id="status" name="status" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <div id="status-error" class="text-red-500 text-sm mt-1"></div>
                        <p class="text-xs text-secondary mt-1">Change status manually if needed (e.g., to cancel an election). If you update the date/time, status will automatically become "Upcoming".</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="election_date" class="block text-sm font-medium text-primary mb-2">Election Date *</label>
                            <input type="date" id="election_date" name="election_date" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="election_date-error" class="text-red-500 text-sm mt-1"></div>
                            <p class="text-xs text-secondary mt-1">Cannot select past dates when creating (Philippine Time)</p>
                        </div>
                        
                        <div>
                            <label for="venue" class="block text-sm font-medium text-primary mb-2">Venue</label>
                            <input type="text" id="venue" name="venue" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="venue-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="timestarted" class="block text-sm font-medium text-primary mb-2">Time Started</label>
                            <input type="time" id="timestarted" name="timestarted" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="timestarted-error" class="text-red-500 text-sm mt-1"></div>
                            <p class="text-xs text-secondary mt-1">Required if date is today - must be in the future</p>
                        </div>
                        
                        <div>
                            <label for="time_ended" class="block text-sm font-medium text-primary mb-2">Time Ended</label>
                            <input type="time" id="time_ended" name="time_ended" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="time_ended-error" class="text-red-500 text-sm mt-1"></div>
                            <p class="text-xs text-secondary mt-1">Must be after start time</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('electionModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                        onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-all btn-cpsu-primary">
                    <span id="submitBtnText">Create Election</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Election Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Election Details</h3>
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewModalBody">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('viewModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Confirm Delete</h3>
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(220, 38, 38, 0.1);">
                    <svg class="w-6 h-6" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-primary">Are you sure you want to delete this election?</p>
                    <p class="text-sm text-secondary mt-1" id="deleteElectionName"></p>
                    <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('deleteModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                Cancel
            </button>
            <button onclick="confirmDelete()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: #dc2626;" 
                    onmouseover="this.style.background='#b91c1c'"
                    onmouseout="this.style.background='#dc2626'">
                Delete Election
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentElectionId = null;
    let deleteElectionId = null;
    let countdownIntervals = {};
    
    // Get current Philippine time timestamp from server (updated on page load)
    const serverPHTime = {{ \Carbon\Carbon::now('Asia/Manila')->timestamp * 1000 }};
    const pageLoadTime = Date.now();
    
    // Function to get current Philippine time in UTC milliseconds (accurate real-time)
    function getCurrentPHTime() {
        // Use server's Philippine time as baseline and add elapsed time
        // This is more accurate than trying to calculate timezone conversions
        const now = Date.now();
        const elapsed = now - pageLoadTime;
        return serverPHTime + elapsed;
    }

    // Modal Functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = 'auto';
        if (modalId === 'electionModal') {
            resetForm();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modals = ['electionModal', 'viewModal', 'deleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    }

    // Create Election
    function openCreateModal() {
        currentElectionId = null;
        document.getElementById('modalTitle').textContent = 'Add New Election';
        document.getElementById('submitBtnText').textContent = 'Create Election';
        resetForm();
        
        // Hide status field for new elections (status will be auto-calculated)
        const statusFieldContainer = document.getElementById('statusFieldContainer');
        if (statusFieldContainer) {
            statusFieldContainer.style.display = 'none';
        }
        
        // Set min date for new elections and ensure inputs are editable
        const dateInput = document.getElementById('election_date');
        if (dateInput) {
            dateInput.setAttribute('min', '{{ date("Y-m-d") }}');
            dateInput.disabled = false;
            dateInput.readOnly = false;
        }
        
        // Ensure time inputs are editable
        const timeStartedInput = document.getElementById('timestarted');
        const timeEndedInput = document.getElementById('time_ended');
        if (timeStartedInput) {
            timeStartedInput.disabled = false;
            timeStartedInput.readOnly = false;
        }
        if (timeEndedInput) {
            timeEndedInput.disabled = false;
            timeEndedInput.readOnly = false;
        }
        
        openModal('electionModal');
    }

    // Edit Election
    function editElection(electionId) {
        currentElectionId = electionId;
        document.getElementById('modalTitle').textContent = 'Edit Election';
        document.getElementById('submitBtnText').textContent = 'Update Election';
        
        // Show status field for editing
        const statusFieldContainer = document.getElementById('statusFieldContainer');
        if (statusFieldContainer) {
            statusFieldContainer.style.display = 'block';
        }
        
        // Remove min date restriction when editing (allows editing past dates)
        const dateInput = document.getElementById('election_date');
        if (dateInput) {
            dateInput.removeAttribute('min');
        }
        
        // Clear any previous errors
        clearErrors();
        
        fetch(`/admin/elections/${electionId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch election data');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const election = data.election;
                
                // Set form fields
                document.getElementById('electionId').value = election.id || '';
                document.getElementById('election_name').value = election.election_name || '';
                // Set organization dropdown
                if (election.organization_id) {
                    document.getElementById('organization_id').value = election.organization_id || '';
                    updateTypeOfElection(); // Sync type_of_election from organization
                } else if (election.type_of_election) {
                    // Fallback: try to find organization by name
                    const orgSelect = document.getElementById('organization_id');
                    for (let i = 0; i < orgSelect.options.length; i++) {
                        if (orgSelect.options[i].textContent.includes(election.type_of_election)) {
                            orgSelect.value = orgSelect.options[i].value;
                            updateTypeOfElection();
                            break;
                        }
                    }
                }
                document.getElementById('description').value = election.description || '';
                document.getElementById('venue').value = election.venue || '';
                
                // Handle date - ensure it's in YYYY-MM-DD format
                if (election.election_date) {
                    let dateValue = election.election_date;
                    // If it's already a string in Y-m-d format, use it directly
                    // If it's a Carbon instance or other format, convert it
                    if (typeof dateValue === 'string') {
                        // Try to parse and reformat if needed
                        const dateParts = dateValue.split(' ');
                        dateValue = dateParts[0]; // Take only the date part
                    }
                    document.getElementById('election_date').value = dateValue;
                } else {
                    document.getElementById('election_date').value = '';
                }
                
                // Handle time fields - remove seconds if present
                if (election.timestarted) {
                    const startTime = String(election.timestarted).split(':').slice(0, 2).join(':');
                    document.getElementById('timestarted').value = startTime;
                } else {
                    document.getElementById('timestarted').value = '';
                }
                
                if (election.time_ended) {
                    const endTime = String(election.time_ended).split(':').slice(0, 2).join(':');
                    document.getElementById('time_ended').value = endTime;
                } else {
                    document.getElementById('time_ended').value = '';
                }
                
                // Set status - if status is rescheduled, convert to upcoming
                if (election.status) {
                    const statusValue = election.status === 'rescheduled' ? 'upcoming' : election.status;
                    document.getElementById('status').value = statusValue;
                } else {
                    document.getElementById('status').value = 'upcoming';
                }
                
                openModal('electionModal');
            } else {
                alert('Failed to load election data');
            }
        })
        .catch(error => {
            console.error('Error loading election:', error);
            alert('Failed to load election data. Please try again.');
        });
    }

    // View Election
    function viewElection(electionId) {
        fetch(`/admin/elections/${electionId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const election = data.election;
                const startTime = election.timestarted ? formatTime(election.timestarted) : '-';
                const endTime = election.time_ended ? formatTime(election.time_ended) : '-';
                const html = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <span class="text-white font-semibold text-xl">${election.election_name.charAt(0).toUpperCase()}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-primary">${election.election_name}</h4>
                                <p class="text-sm text-secondary">ID: ${election.election_id || election.id}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t" style="border-color: var(--border-color);">
                            <div>
                                <p class="text-xs text-secondary">Type of Election</p>
                                <p class="text-sm font-medium text-primary mt-1">${election.type_of_election}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Election Date</p>
                                <p class="text-sm font-medium text-primary mt-1">${new Date(election.election_date).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Time</p>
                                <p class="text-sm font-medium text-primary mt-1">${startTime} ${endTime !== '-' ? '- ' + endTime : ''}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Venue</p>
                                <p class="text-sm font-medium text-primary mt-1">${election.venue || '-'}</p>
                            </div>
                            ${election.description ? `
                            <div class="col-span-2">
                                <p class="text-xs text-secondary">Description</p>
                                <p class="text-sm font-medium text-primary mt-1">${election.description}</p>
                            </div>
                            ` : ''}
                            <div>
                                <p class="text-xs text-secondary">Created At</p>
                                <p class="text-sm font-medium text-primary mt-1">${new Date(election.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('viewModalBody').innerHTML = html;
                openModal('viewModal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load election data');
        });
    }

    function formatTime(timeString) {
        if (!timeString) return '-';
        try {
            const parts = timeString.split(':');
            if (parts.length >= 2) {
                const hours = parseInt(parts[0]);
                const minutes = parts[1];
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const displayHours = hours % 12 || 12;
                return `${displayHours}:${minutes} ${ampm}`;
            }
            return timeString;
        } catch (e) {
            return timeString;
        }
    }

    // Delete Election
    function deleteElection(electionId, electionName) {
        deleteElectionId = electionId;
        document.getElementById('deleteElectionName').textContent = `Election: ${electionName}`;
        openModal('deleteModal');
    }

    function confirmDelete() {
        if (!deleteElectionId) return;

        fetch(`/admin/elections/${deleteElectionId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`election-row-${deleteElectionId}`);
                if (row) {
                    row.remove();
                }
                closeModal('deleteModal');
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(data.message || 'Failed to delete election');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete election');
        });
    }

    // Save Election (Create/Update)
    function saveElection(event) {
        event.preventDefault();
        
        clearErrors();
        
        // Validate date/time before submitting (only for new elections)
        if (!currentElectionId && !validateDateTime()) {
            showNotification('Please fix the date/time errors before submitting.', 'error');
            return;
        }
        
        const formData = new FormData(event.target);
        const url = currentElectionId 
            ? `/admin/elections/${currentElectionId}` 
            : '/admin/elections';
        
        if (currentElectionId) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            let data;
            
            // Try to parse JSON response
            if (contentType && contentType.includes('application/json')) {
                try {
                    data = await response.json();
                } catch (e) {
                    console.error('Failed to parse JSON response:', e);
                    throw new Error('Invalid response from server');
                }
            } else {
                // If not JSON, it might be HTML (error page)
                const text = await response.text();
                console.error('Non-JSON response:', text.substring(0, 200));
                throw new Error('Server returned an error page');
            }
            
            // Check if response is OK
            if (!response.ok) {
                // Response is not OK, but we have data
                if (data.errors) {
                    // Validation errors
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById(`${field}-error`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                        }
                    });
                    showNotification(data.message || 'Please fix the validation errors', 'error');
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
                return; // Don't proceed with success handling
            }
            
            // Response is OK, check if success
            if (data.success) {
                closeModal('electionModal');
                const successMessage = currentElectionId 
                    ? 'Election updated successfully!' 
                    : 'Election created successfully!';
                showNotification(data.message || successMessage, 'success');
                
                // Update the table row immediately without reloading
                if (currentElectionId && data.election) {
                    updateElectionRowInTable(data.election);
                } else if (data.election) {
                    // New election created - reload to show it
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                    return;
                }
                
                // Refresh stats and countdowns without full page reload
                fetchStats();
                setTimeout(() => {
                    initializeCountdowns();
                }, 100);
            } else {
                // Response OK but success is false
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById(`${field}-error`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                        }
                    });
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error saving election:', error);
            // Only show error if it's a real network error, not a handled response error
            if (error.message && !error.message.includes('response')) {
                showNotification('Network error. Please check your connection and try again.', 'error');
            }
        });
    }

    // Update type_of_election from selected organization
    function updateTypeOfElection() {
        const orgSelect = document.getElementById('organization_id');
        const typeOfElectionInput = document.getElementById('type_of_election');
        
        if (orgSelect && typeOfElectionInput) {
            const selectedOption = orgSelect.options[orgSelect.selectedIndex];
            if (selectedOption && selectedOption.value) {
                // Get organization name from data attribute or text
                const orgName = selectedOption.getAttribute('data-name') || selectedOption.textContent.split(' (')[0];
                typeOfElectionInput.value = orgName;
            } else {
                typeOfElectionInput.value = '';
            }
        }
    }

    // Reset Form
    function resetForm() {
        document.getElementById('electionForm').reset();
        document.getElementById('electionId').value = '';
        document.getElementById('organization_id').value = '';
        document.getElementById('type_of_election').value = '';
        clearErrors();
    }

    // Clear Errors
    function clearErrors() {
        ['election_name', 'organization_id', 'type_of_election', 'description', 'venue', 'election_date', 'timestarted', 'time_ended', 'status'].forEach(field => {
            const errorElement = document.getElementById(`${field}-error`);
            if (errorElement) {
                errorElement.textContent = '';
            }
        });
    }
    
    // Function to filter table by status
    function filterByStatus(status) {
        // Redirect to the same page with status filter
        const url = new URL(window.location.href);
        url.searchParams.set('status', status);
        window.location.href = url.toString();
    }

    // Real-time countdown and status updates
    function initializeCountdowns() {
        const containers = document.querySelectorAll('.countdown-container');
        console.log('Found', containers.length, 'countdown containers');
        
        containers.forEach(container => {
            const electionId = container.getAttribute('data-election-id');
            const electionTimestamp = container.getAttribute('data-election-timestamp');
            const endTimestamp = container.getAttribute('data-end-timestamp');
            
            console.log('Election ID:', electionId, 'Timestamp:', electionTimestamp);
            
            if (!electionTimestamp || electionTimestamp === '' || electionTimestamp === '0' || parseInt(electionTimestamp) <= 0) {
                console.log('Skipping election', electionId, '- no valid timestamp:', electionTimestamp);
                return;
            }
            
            // Clear existing interval for this election
            if (countdownIntervals[electionId]) {
                clearInterval(countdownIntervals[electionId]);
            }
            
            // Check status first - don't start countdown if cancelled
            const statusContainer = document.querySelector(`.status-container[data-election-id="${electionId}"]`);
            if (statusContainer) {
                const currentStatus = statusContainer.getAttribute('data-status');
                if (currentStatus === 'cancelled') {
                    // Don't start countdown for cancelled elections
                    container.innerHTML = '<span class="text-sm font-semibold" style="color: #dc2626;">Cancelled</span>';
                    // Stop any existing countdown
                    if (countdownIntervals[electionId]) {
                        clearInterval(countdownIntervals[electionId]);
                        delete countdownIntervals[electionId];
                    }
                    return; // Don't start countdown interval
                }
            }
            
            // Update immediately
            console.log(`Initializing countdown for election ${electionId} with timestamp ${electionTimestamp}`);
            try {
                updateCountdownAndStatus(container, electionTimestamp, endTimestamp, electionId);
            } catch (e) {
                console.error('Error in initial countdown update:', e);
            }
            
            // Update every second for real-time countdown
            countdownIntervals[electionId] = setInterval(() => {
                try {
                    updateCountdownAndStatus(container, electionTimestamp, endTimestamp, electionId);
                } catch (e) {
                    console.error('Error in countdown update:', e);
                }
            }, 1000);
        });
    }

    function updateCountdownAndStatus(container, electionTimestamp, endTimestamp, electionId) {
        try {
            // Get status container FIRST to check if election is cancelled or completed
            const statusContainer = document.querySelector(`.status-container[data-election-id="${electionId}"]`);
            
            // If election is cancelled or completed, stop countdown and show appropriate message
            if (statusContainer) {
                const currentStatus = statusContainer.getAttribute('data-status');
                if (currentStatus === 'cancelled') {
                    container.innerHTML = '<span class="text-sm font-semibold" style="color: #dc2626;">Cancelled</span>';
                    // Stop countdown interval
                    if (countdownIntervals[electionId]) {
                        clearInterval(countdownIntervals[electionId]);
                        delete countdownIntervals[electionId];
                    }
                    return;
                }
                // If already completed, stop countdown and don't process further
                if (currentStatus === 'completed') {
                    container.innerHTML = '<span class="text-sm text-secondary opacity-75">Election Ended</span>';
                    // Stop countdown interval to prevent further checks
                    if (countdownIntervals[electionId]) {
                        clearInterval(countdownIntervals[electionId]);
                        delete countdownIntervals[electionId];
                    }
                    return;
                }
            }
            
            if (!electionTimestamp || electionTimestamp === '' || electionTimestamp === '0' || parseInt(electionTimestamp) <= 0) {
                container.innerHTML = '<span class="text-sm text-secondary opacity-75">-</span>';
                return;
            }
            
            // Get current Philippine time accurately (real-time)
            const nowPHTimestamp = getCurrentPHTime();
            
            // Election timestamp from server is in UTC milliseconds (from Carbon timestamp)
            // Carbon gives us the UTC timestamp representing the Philippine datetime
            const electionDateTime = parseInt(electionTimestamp);
            
            if (isNaN(electionDateTime)) {
                console.error('Invalid election timestamp:', electionTimestamp);
                container.innerHTML = '<span class="text-sm text-secondary opacity-75">Invalid date</span>';
                return;
            }
            
            // Calculate difference (both timestamps are in UTC milliseconds)
            const diff = electionDateTime - nowPHTimestamp;
            
            // Debug logging (only log once per election to avoid spam)
            if (!container.hasAttribute('data-logged')) {
                console.log(`Election ${electionId}: Election=${new Date(electionDateTime).toISOString()}, NowPH=${new Date(nowPHTimestamp).toISOString()}, Diff=${diff}ms (${Math.floor(diff / (1000 * 60 * 60))} hours)`);
                container.setAttribute('data-logged', 'true');
            }
            
            if (diff <= 0) {
                // Election has started
                if (endTimestamp && endTimestamp !== '' && endTimestamp !== '0') {
                    const endDateTime = parseInt(endTimestamp);
                    if (nowPHTimestamp >= endDateTime) {
                        // Election has ended - update countdown and status
                        container.innerHTML = '<span class="text-sm text-secondary opacity-75">Election Ended</span>';
                        // Always update to completed when election ends
                        if (statusContainer) {
                            const currentStatus = statusContainer.getAttribute('data-status');
                            if (currentStatus !== 'completed') {
                                // Update status immediately - this will trigger stats update via AJAX
                                updateStatus(statusContainer, 'completed', electionId);
                                // Also trigger immediate stats fetch multiple times to ensure update
                                setTimeout(function() {
                                    fetchStats();
                                }, 100);
                                setTimeout(function() {
                                    fetchStats();
                                }, 500);
                                setTimeout(function() {
                                    fetchStats();
                                }, 1000);
                            }
                            // If already completed, don't do anything - just stop the interval
                            // This prevents blinking by not calling fetchStats() repeatedly
                        }
                        if (countdownIntervals[electionId]) {
                            clearInterval(countdownIntervals[electionId]);
                            delete countdownIntervals[electionId];
                        }
                        return;
                    }
                }
                
                // Election is in progress - show countdown to end time
                if (endTimestamp && endTimestamp !== '' && endTimestamp !== '0') {
                    const endDateTime = parseInt(endTimestamp);
                    if (!isNaN(endDateTime)) {
                        // Calculate time remaining until end
                        const endDiff = endDateTime - nowPHTimestamp;
                        
                        if (endDiff > 0) {
                            // Calculate time remaining until end
                            const endDays = Math.floor(endDiff / (1000 * 60 * 60 * 24));
                            const endHours = Math.floor((endDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const endMinutes = Math.floor((endDiff % (1000 * 60 * 60)) / (1000 * 60));
                            const endSeconds = Math.floor((endDiff % (1000 * 60)) / 1000);
                            
                            let endCountdownString = '';
                            if (endDays > 0) {
                                endCountdownString = `${endDays}d ${endHours}h ${endMinutes}m`;
                            } else if (endHours > 0) {
                                endCountdownString = `${endHours}h ${endMinutes}m ${endSeconds}s`;
                            } else if (endMinutes > 0) {
                                endCountdownString = `${endMinutes}m ${endSeconds}s`;
                            } else {
                                endCountdownString = `${endSeconds}s`;
                            }
                            
                            // Show "In Progress" with countdown to end
                            container.innerHTML = `<div class="text-sm font-semibold" style="color: var(--cpsu-gold);">
                                <div>In Progress</div>
                                <div class="text-xs mt-1" style="color: var(--cpsu-gold-dark);">Ends in: ${endCountdownString}</div>
                            </div>`;
                        } else {
                            // End time has passed but we're still here (shouldn't happen, but handle it)
                            container.innerHTML = '<span class="text-sm font-semibold" style="color: var(--cpsu-gold);">In Progress</span>';
                        }
                    } else {
                        container.innerHTML = '<span class="text-sm font-semibold" style="color: var(--cpsu-gold);">In Progress</span>';
                    }
                } else {
                    // No end time specified, just show "In Progress"
                    container.innerHTML = '<span class="text-sm font-semibold" style="color: var(--cpsu-gold);">In Progress</span>';
                }
                
                // Only update to ongoing if not cancelled, not completed, and not already ongoing
                if (statusContainer) {
                    const currentStatus = statusContainer.getAttribute('data-status');
                    // Don't change status if it's cancelled, completed, or already ongoing
                    if (currentStatus !== 'cancelled' && currentStatus !== 'ongoing' && currentStatus !== 'completed') {
                        updateStatus(statusContainer, 'ongoing', electionId);
                    }
                }
                return;
            }
            
            // Calculate time remaining
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            let countdownString = '';
            if (days > 0) {
                countdownString = `${days}d ${hours}h ${minutes}m`;
            } else if (hours > 0) {
                countdownString = `${hours}h ${minutes}m ${seconds}s`;
            } else if (minutes > 0) {
                countdownString = `${minutes}m ${seconds}s`;
            } else {
                countdownString = `${seconds}s`;
            }
            
            // Update the countdown display
            container.innerHTML = `<div class="text-sm font-semibold" style="color: var(--cpsu-green);"><span class="countdown-text">${countdownString}</span></div>`;
            
            // Update status to upcoming if not already set (but don't override cancelled, ongoing, or completed)
            if (statusContainer) {
                const currentStatus = statusContainer.getAttribute('data-status');
                // Only update to upcoming if status is empty/null, don't override existing statuses
                if (currentStatus !== 'cancelled' && currentStatus !== 'upcoming' && currentStatus !== 'ongoing' && currentStatus !== 'completed' && (!currentStatus || currentStatus === '')) {
                    updateStatus(statusContainer, 'upcoming', electionId);
                    // Note: updateStatus already triggers stats update via AJAX response
                }
            }
            
            // Debug: log the countdown string
            console.log(`Election ${electionId} countdown: ${countdownString}`);
        } catch (e) {
            console.error('Error updating countdown:', e, 'Timestamp:', electionTimestamp);
            container.innerHTML = '<span class="text-sm text-secondary opacity-75">Error</span>';
        }
    }

    // Function to update election row in table without reloading
    function updateElectionRowInTable(election) {
        if (!election || !election.id) {
            console.warn('updateElectionRowInTable: Invalid election data', election);
            return;
        }
        
        const row = document.querySelector(`tr[data-election-id="${election.id}"]`);
        if (!row) {
            // Row not found - might be on different page, skip update
            console.log(`Row not found for election ${election.id} - might be on different page`);
            return;
        }
        
        // Update election name
        const nameCell = row.querySelector('.election-name');
        if (nameCell && election.election_name) {
            const currentName = nameCell.textContent.trim();
            const newName = String(election.election_name).trim();
            
            // Always update if names are different (force update)
            if (currentName !== newName) {
                console.log(`Updating election ${election.id} name from "${currentName}" to "${newName}"`);
                nameCell.textContent = newName;
                
            }
        } else if (!nameCell) {
            console.warn(`Election name cell not found for election ${election.id}`);
        }
        
        // Update description
        if (election.description !== undefined || election.description_limited !== undefined) {
            const nameContainer = row.querySelector('td:nth-child(1) .min-w-0');
            if (nameContainer) {
                const descText = election.description_limited || (election.description ? (election.description.length > 50 ? election.description.substring(0, 50) + '...' : election.description) : null);
                let descDiv = nameContainer.querySelector('.election-description');
                
                if (descText) {
                    if (!descDiv) {
                        descDiv = document.createElement('div');
                        descDiv.className = 'text-xs text-secondary mt-1 truncate election-description';
                        nameContainer.appendChild(descDiv);
                    }
                    if (descDiv.textContent.trim() !== descText.trim()) {
                        descDiv.textContent = descText;
                    }
                } else {
                    // Remove description if it doesn't exist
                    if (descDiv) {
                        descDiv.remove();
                    }
                }
            }
        }
        
        // Update type
        const typeCell = row.querySelector('.election-type');
        if (typeCell && election.type_of_election) {
            if (typeCell.textContent !== election.type_of_election) {
                typeCell.textContent = election.type_of_election;
            }
        }
        
        // Update schedule (date and time in column 3)
        if (election.election_date_formatted) {
            const scheduleCell = row.querySelector('td:nth-child(3)');
            if (scheduleCell) {
                const scheduleDiv = scheduleCell.querySelector('div');
                if (scheduleDiv) {
                    // Update date
                    const dateDiv = scheduleDiv.querySelector('.font-medium');
                    if (dateDiv && dateDiv.textContent.trim() !== election.election_date_formatted.trim()) {
                        dateDiv.textContent = election.election_date_formatted;
                    }
                    
                    // Update time
                    if (election.timestarted || election.time_ended) {
                        let timeText = '';
                        if (election.timestarted && election.time_ended) {
                            const startTime = formatTimeForDisplay(election.timestarted);
                            const endTime = formatTimeForDisplay(election.time_ended);
                            timeText = `${startTime} - ${endTime}`;
                        } else if (election.timestarted) {
                            timeText = `Starts: ${formatTimeForDisplay(election.timestarted)}`;
                        } else {
                            timeText = 'Time not set';
                        }
                        
                        const timeDiv = scheduleDiv.querySelector('.text-xs');
                        if (timeDiv) {
                            if (timeDiv.textContent.trim() !== timeText.trim()) {
                                timeDiv.textContent = timeText;
                            }
                        }
                    }
                }
            }
        }
        
        // Update venue (now in column 1 with election name)
        const nameContainer = row.querySelector('td:nth-child(1) .min-w-0');
        if (nameContainer && election.venue !== undefined) {
            let venueDiv = nameContainer.querySelector('.flex.items-center');
            if (!venueDiv && election.venue) {
                venueDiv = document.createElement('div');
                venueDiv.className = 'text-xs text-secondary mt-1 flex items-center';
                venueDiv.innerHTML = `
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="truncate"></span>
                `;
                nameContainer.appendChild(venueDiv);
            }
            if (venueDiv) {
                const venueSpan = venueDiv.querySelector('span');
                if (venueSpan) {
                    const newVenue = election.venue || '';
                    if (venueSpan.textContent.trim() !== newVenue.trim()) {
                        venueSpan.textContent = newVenue;
                    }
                    if (!newVenue && venueDiv.parentNode) {
                        venueDiv.remove();
                    }
                }
            }
        }
        
        // Update status badge - preserve completed and ongoing status to prevent blinking
        if (election.status) {
            const statusContainer = row.querySelector('.status-container');
            if (statusContainer) {
                const currentStatus = statusContainer.getAttribute('data-status');
                // Only update if status actually changed
                if (currentStatus !== election.status) {
                    // Never downgrade from completed to anything else
                    if (currentStatus === 'completed' && election.status !== 'completed') {
                        // Don't update - keep completed status to prevent blinking
                        console.log(`Preserving completed status for election ${election.id} - ignoring server status: ${election.status}`);
                    }
                    // Never downgrade from ongoing to upcoming (only allow ongoing -> completed)
                    else if (currentStatus === 'ongoing' && election.status === 'upcoming') {
                        // Don't update - keep ongoing status to prevent blinking
                        console.log(`Preserving ongoing status for election ${election.id} - ignoring server status: ${election.status}`);
                    }
                    // Safe to update status (upcoming->ongoing, ongoing->completed, etc.)
                    else {
                        updateStatusBadge(statusContainer, election.status);
                        
                        // If status changed to completed, stop countdown interval
                        if (election.status === 'completed') {
                            if (countdownIntervals[election.id]) {
                                clearInterval(countdownIntervals[election.id]);
                                delete countdownIntervals[election.id];
                            }
                        }
                    }
                }
            }
        }
        
        // Update countdown container data attributes
        const countdownContainer = row.querySelector('.countdown-container');
        if (countdownContainer) {
            if (election.election_date) {
                countdownContainer.setAttribute('data-election-date', election.election_date);
            }
            if (election.timestarted) {
                countdownContainer.setAttribute('data-election-time', election.timestarted);
            } else {
                countdownContainer.setAttribute('data-election-time', '');
            }
            if (election.time_ended) {
                countdownContainer.setAttribute('data-election-ended', election.time_ended);
            } else {
                countdownContainer.setAttribute('data-election-ended', '');
            }
            if (election.election_timestamp) {
                countdownContainer.setAttribute('data-election-timestamp', election.election_timestamp);
            } else {
                countdownContainer.setAttribute('data-election-timestamp', '');
            }
            if (election.end_timestamp) {
                countdownContainer.setAttribute('data-end-timestamp', election.end_timestamp);
            } else {
                countdownContainer.setAttribute('data-end-timestamp', '');
            }
        }
    }
    
    // Helper function to format time for display
    function formatTimeForDisplay(timeStr) {
        if (!timeStr) return '';
        const parts = String(timeStr).split(':');
        if (parts.length >= 2) {
            const hours = parseInt(parts[0]);
            const minutes = parts[1];
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const displayHours = hours % 12 || 12;
            return `${displayHours}:${minutes} ${ampm}`;
        }
        return timeStr;
    }

    // Helper function to update status badge visually (without AJAX)
    function updateStatusBadge(statusContainer, newStatus) {
        if (!statusContainer) return;
        
        const statusColors = {
            'upcoming': 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);',
            'ongoing': 'background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);',
            'completed': 'background: rgba(107, 114, 128, 0.8);',
            'cancelled': 'background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);'
        };
        
        const statusLabels = {
            'upcoming': 'Upcoming',
            'ongoing': 'Ongoing',
            'completed': 'Completed',
            'cancelled': 'Cancelled'
        };
        
        // Update the status badge immediately
        const badge = statusContainer.querySelector('.status-badge');
        if (badge) {
            badge.textContent = statusLabels[newStatus] || 'Upcoming';
            badge.style.cssText = statusColors[newStatus] || statusColors['upcoming'];
        }
        
        // Update data attribute
        statusContainer.setAttribute('data-status', newStatus);
    }

    function updateStatus(statusContainer, newStatus, electionId) {
        if (!statusContainer) return;
        
        const currentStatus = statusContainer.getAttribute('data-status');
        if (currentStatus === newStatus) return; // No change needed
        
        // Don't auto-update if status is manually set to cancelled
        // BUT always allow update to completed (election ended)
        if (currentStatus === 'cancelled' && newStatus !== 'completed') {
            return;
        }
        
        const statusColors = {
            'upcoming': 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);',
            'ongoing': 'background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);',
            'completed': 'background: rgba(107, 114, 128, 0.8);',
            'cancelled': 'background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);'
        };
        
        const statusLabels = {
            'upcoming': 'Upcoming',
            'ongoing': 'Ongoing',
            'completed': 'Completed',
            'cancelled': 'Cancelled'
        };
        
        // Update the status badge immediately - only if content actually changed
        const badge = statusContainer.querySelector('.status-badge');
        if (badge) {
            const newLabel = statusLabels[newStatus] || 'Upcoming';
            const newStyle = statusColors[newStatus] || statusColors['upcoming'];
            
            // Only update if content or style actually changed to prevent blinking
            if (badge.textContent !== newLabel || badge.style.cssText !== newStyle) {
                badge.textContent = newLabel;
                badge.style.cssText = newStyle;
            }
        }
        
        // Update data attribute
        statusContainer.setAttribute('data-status', newStatus);
        
        // Update status in database via AJAX (silent update) - only if status actually changed
        if (currentStatus !== newStatus) {
            fetch(`/admin/elections/${electionId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`Status updated to ${newStatus} for election ${electionId}`);
                    // Always update stats immediately when status changes (from server for accuracy)
                    if (data.stats) {
                        updateStatsDisplay(data.stats);
                        console.log('Stats updated immediately after status change:', data.stats);
                    } else {
                        // Fetch updated stats from server immediately
                        fetchStats();
                    }
                } else {
                    // Even if update fails, fetch stats to ensure accuracy
                    fetchStats();
                }
            })
            .catch(err => {
                console.error('Failed to update status:', err);
                // Still fetch stats even if status update fails to ensure real-time accuracy
                fetchStats();
            });
        }
    }

    // Function to update stats display
    function updateStatsDisplay(stats) {
        const totalEl = document.getElementById('stat-total');
        const upcomingEl = document.getElementById('stat-upcoming');
        const ongoingEl = document.getElementById('stat-ongoing');
        const completedEl = document.getElementById('stat-completed');
        const cancelledEl = document.getElementById('stat-cancelled');
        
        if (totalEl && stats.total !== undefined) {
            totalEl.textContent = stats.total;
        }
        if (upcomingEl && stats.upcoming !== undefined) {
            upcomingEl.textContent = stats.upcoming;
        }
        if (ongoingEl && stats.ongoing !== undefined) {
            ongoingEl.textContent = stats.ongoing;
        }
        if (completedEl && stats.completed !== undefined) {
            completedEl.textContent = stats.completed;
        }
        if (cancelledEl && stats.cancelled !== undefined) {
            cancelledEl.textContent = stats.cancelled;
        }
    }

    // Function to fetch and update stats from server (accurate counts for ALL elections)
    function fetchStats() {
        fetch('/admin/elections/stats/get', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.stats) {
                updateStatsDisplay(data.stats);
                console.log('Stats updated from server:', data.stats);
                // Verify stats match what we expect
                console.log('Stats breakdown - Total:', data.stats.total, 'Upcoming:', data.stats.upcoming, 'Ongoing:', data.stats.ongoing, 'Completed:', data.stats.completed);
            } else {
                console.error('Stats update failed:', data);
            }
        })
        .catch(err => {
            console.error('Failed to fetch stats:', err);
        });
    }

    // Update stats periodically (every 1 second) and also when status changes
    let statsUpdateInterval = null;
    let tableUpdateInterval = null;
    
    function startStatsUpdates() {
        // Clear any existing interval
        if (statsUpdateInterval) {
            clearInterval(statsUpdateInterval);
        }
        // Initial fetch from server immediately (for accurate counts across all pages)
        fetchStats();
        // Then update from server every 1 second for real-time accuracy
        statsUpdateInterval = setInterval(function() {
            fetchStats();
        }, 1000); // Update every 1 second for real-time updates
    }
    
    // Function to fetch and update table data in real-time
    function updateTableData() {
        // Don't update if a modal is open (user is editing/viewing)
        const modals = ['electionModal', 'viewModal', 'deleteModal'];
        const isModalOpen = modals.some(modalId => {
            const modal = document.getElementById(modalId);
            return modal && modal.classList.contains('active');
        });
        
        if (isModalOpen) {
            return; // Skip update if user is interacting with modals
        }
        
        // Build URL with current filters
        const url = new URL('/admin/elections/data/get', window.location.origin);
        const currentUrl = new URL(window.location.href);
        
        // Preserve search and status filters
        if (currentUrl.searchParams.get('search')) {
            url.searchParams.set('search', currentUrl.searchParams.get('search'));
        }
        if (currentUrl.searchParams.get('status')) {
            url.searchParams.set('status', currentUrl.searchParams.get('status'));
        }
        if (currentUrl.searchParams.get('page')) {
            url.searchParams.set('page', currentUrl.searchParams.get('page'));
        }
        
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.elections) {
                console.log(`Updating ${data.elections.length} election rows...`);
                // Update each election row in the table
                data.elections.forEach(election => {
                    updateElectionRowInTable(election);
                });
                
                // Re-initialize countdowns after updating all rows
                setTimeout(() => {
                    initializeCountdowns();
                }, 100);
            } else {
                console.warn('Table update: No elections data received', data);
            }
        })
        .catch(err => {
            console.error('Error updating table data:', err);
        });
    }
    
    // Start automatic table updates
    function startTableUpdates() {
        // Clear any existing interval
        if (tableUpdateInterval) {
            clearInterval(tableUpdateInterval);
        }
        // Update table every 5 seconds for real-time updates
        tableUpdateInterval = setInterval(function() {
            updateTableData();
        }, 5000); // Update every 5 seconds
    }
    
    function stopStatsUpdates() {
        if (statsUpdateInterval) {
            clearInterval(statsUpdateInterval);
            statsUpdateInterval = null;
        }
    }

    // Client-side date/time validation
    function validateDateTime() {
        // Skip validation if editing (allow free editing of date/time when updating)
        if (currentElectionId) {
            // Clear any errors when editing
            const dateError = document.getElementById('election_date-error');
            const timeError = document.getElementById('timestarted-error');
            if (dateError) dateError.textContent = '';
            if (timeError) timeError.textContent = '';
            return true;
        }
        
        // Only validate for new elections (create mode)
        const electionDate = document.getElementById('election_date').value;
        const timeStarted = document.getElementById('timestarted').value;
        const dateError = document.getElementById('election_date-error');
        const timeError = document.getElementById('timestarted-error');
        
        if (!electionDate) return true;
        
        // Get current Philippine time
        const now = new Date();
        const philippinesTimezone = 'Asia/Manila';
        const nowPHString = now.toLocaleString('en-US', { 
            timeZone: philippinesTimezone,
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
        
        // Parse Philippine time
        const parts = nowPHString.split(', ');
        const dateParts = parts[0].split('/');
        const timeParts = parts[1].split(':');
        const todayPH = `${dateParts[2]}-${dateParts[0].padStart(2, '0')}-${dateParts[1].padStart(2, '0')}`;
        
        const selectedDate = new Date(electionDate);
        const today = new Date(todayPH);
        
        if (electionDate === todayPH && timeStarted) {
            // Check if time is in the future for today
            const [hours, minutes] = timeStarted.split(':');
            const nowHour = parseInt(timeParts[0]);
            const nowMinute = parseInt(timeParts[1]);
            const selectedHour = parseInt(hours);
            const selectedMinute = parseInt(minutes);
            
            if (selectedHour < nowHour || (selectedHour === nowHour && selectedMinute <= nowMinute)) {
                if (timeError) {
                    timeError.textContent = 'Time must be in the future for today\'s date (Philippine Time).';
                }
                return false;
            } else {
                if (timeError) {
                    timeError.textContent = '';
                }
            }
        } else if (selectedDate < today) {
            if (dateError) {
                dateError.textContent = 'Cannot select a past date (Philippine Time).';
            }
            return false;
        } else {
            if (dateError) {
                dateError.textContent = '';
            }
        }
        
        return true;
    }

    // Initialize countdowns on page load
    function initElectionCountdowns() {
        console.log('Initializing election countdowns...');
        initializeCountdowns();
        
        // Start real-time stats updates
        startStatsUpdates();
        
        // Start automatic table updates
        startTableUpdates();
        
        // Add date/time validation listeners
        const electionDateInput = document.getElementById('election_date');
        const timeStartedInput = document.getElementById('timestarted');
        
        if (electionDateInput) {
            electionDateInput.addEventListener('change', validateDateTime);
        }
        
        if (timeStartedInput) {
            timeStartedInput.addEventListener('change', validateDateTime);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing countdowns and real-time updates...');
            initElectionCountdowns();
        });
    } else {
        // DOM is already loaded
        console.log('DOM already loaded, initializing countdowns and real-time updates...');
        initElectionCountdowns();
    }
    
    // Also initialize on window load to ensure everything is ready
    window.addEventListener('load', function() {
        console.log('Window loaded, ensuring real-time updates are running...');
        if (!tableUpdateInterval) {
            startTableUpdates();
        }
    });
    
    // Also re-initialize after page operations (like after modal close)
    window.addEventListener('load', function() {
        console.log('Window loaded, re-initializing countdowns...');
        setTimeout(initElectionCountdowns, 500);
    });
    
    // Clean up intervals when page unloads
    window.addEventListener('beforeunload', function() {
        stopStatsUpdates();
        Object.keys(countdownIntervals).forEach(electionId => {
            if (countdownIntervals[electionId]) {
                clearInterval(countdownIntervals[electionId]);
            }
        });
    });

    // Show Notification
    function showNotification(message, type = 'success') {
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        notification.style.transition = 'all 0.3s ease-out';
        
        const icon = type === 'success' 
            ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        
        notification.innerHTML = `
            <div class="flex-shrink-0">${icon}</div>
            <div class="flex-1">
                <p class="font-medium">${message}</p>
            </div>
        `;
        
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 10);

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 4000);
    }
</script>
@endpush
@endsection
