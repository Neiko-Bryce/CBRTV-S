@extends('admin.layouts.master')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('title', 'Candidates Management')
@section('page-title', 'Candidates Management')

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
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
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

        .candidate-photo {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid var(--border-color);
            display: block;
        }

        #photoPreview {
            max-width: 300px;
            max-height: 300px;
            object-fit: contain;
            border-radius: 0.5rem;
        }

        .modal-body {
            max-height: calc(90vh - 200px);
            overflow-y: auto;
        }

        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: var(--cpsu-green);
        }

        /* Table improvements */
        .table-header th {
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .table-row {
            transition: background-color 0.15s ease;
        }

        .table-row:hover {
            background-color: var(--hover-bg);
        }

        .candidate-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid var(--border-color);
            display: block;
        }

        /* Responsive: header and buttons */
        @media (max-width: 768px) {
            .candidates-header {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .candidates-header-actions {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }

            .candidates-header-actions .btn-add {
                width: 100%;
                justify-content: center;
            }

            .candidates-header-actions select {
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .candidates-header-actions {
                flex-wrap: wrap;
            }

            .candidates-header-actions .btn-add {
                flex: 1 1 100%;
            }
        }

        /* Modal responsive */
        @media (max-width: 640px) {
            .modal-content {
                width: 95%;
                max-height: 95vh;
            }

            .modal-body .grid.grid-cols-2 {
                grid-template-columns: 1fr;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer button {
                width: 100%;
            }
        }

        /* Table responsive: tighter padding and horizontal scroll hint on small screens */
        @media (max-width: 768px) {
            .candidates-table-wrap {
                -webkit-overflow-scrolling: touch;
            }

            .candidates-table th,
            .candidates-table td {
                padding: 0.5rem 0.75rem;
                font-size: 0.8125rem;
            }

            .candidates-table .candidate-photo {
                width: 40px;
                height: 40px;
            }

            .candidates-table .actions-cell .flex {
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.25rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-wrap items-center justify-between gap-4 candidates-header">
            <div>
                <h3 class="text-lg font-semibold text-primary">All Candidates</h3>
                <p class="text-sm text-secondary mt-1">Manage candidates for elections</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 candidates-header-actions">
                <select id="electionFilter" onchange="filterByElection(this.value)"
                    class="px-3 py-2 border rounded-lg text-sm min-w-0 flex-1 sm:flex-none sm:min-w-[180px]"
                    style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                    <option value="">All Elections</option>
                    @foreach ($elections as $election)
                        <option value="{{ $election->id }}" {{ request('election') == $election->id ? 'selected' : '' }}>
                            {{ $election->election_name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="openCreateModal('single')"
                    class="inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary btn-add">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Add Single Candidate</span>
                </button>
                <button type="button" onclick="openCreateModal('partylist')"
                    class="inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-add"
                    style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: #14532d;">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    <span>Add Full Partylist</span>
                </button>
            </div>
        </div>

        <!-- Candidates Table -->
        <div class="card rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto candidates-table-wrap">
                <table class="min-w-full divide-y candidates-table" style="border-collapse: separate; border-spacing: 0;">
                    <thead class="table-header" style="background-color: var(--bg-tertiary);">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Candidate Photo</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Candidate Name</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Organization</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Election</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Position</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Partylist</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Votes</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="background-color: var(--card-bg);">
                        @forelse($candidates as $candidate)
                            <tr class="table-row transition-colors hover:bg-[var(--hover-bg)] border-b"
                                style="border-color: var(--border-color);">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if ($candidate->photo)
                                        <img src="{{ route('admin.candidates.photo', ['path' => $candidate->photo]) }}"
                                            alt="{{ $candidate->candidate_name }}" class="candidate-photo"
                                            onerror="this.style.display='none';">
                                    @else
                                        <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                            style="background-color: var(--bg-tertiary); border: 2px solid var(--border-color);">
                                            <svg class="w-6 h-6" style="color: var(--text-secondary);" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-semibold text-primary">{{ $candidate->candidate_name }}</div>
                                    @if ($candidate->student)
                                        <div class="text-xs text-secondary mt-0.5">
                                            {{ $candidate->student->student_id_number }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm text-primary">{{ $candidate->election->organization->name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm text-primary">{{ $candidate->election->election_name ?? '-' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        style="background-color: var(--bg-tertiary); color: var(--text-primary);">
                                        {{ $candidate->position->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($candidate->partylist)
                                        <div class="flex items-center">
                                            @if ($candidate->partylist->color)
                                                <div class="w-3 h-3 rounded-full mr-2 flex-shrink-0"
                                                    style="background-color: {{ $candidate->partylist->color }};"></div>
                                            @endif
                                            <div class="text-sm text-primary">{{ $candidate->partylist->name }}</div>
                                        </div>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-secondary"
                                            style="background-color: var(--bg-tertiary);">Independent</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold"
                                        style="background-color: rgba(0, 102, 51, 0.1); color: var(--cpsu-green);">
                                        {{ $candidate->votes_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center actions-cell">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button onclick="editCandidate({{ $candidate->id }})"
                                            class="p-2 rounded-lg hover:bg-[var(--hover-bg)] transition-all duration-200"
                                            style="color: var(--cpsu-green-light);" title="Edit Candidate">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button
                                            onclick="openDeleteModal({{ $candidate->id }}, '{{ addslashes($candidate->candidate_name) }}')"
                                            class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                                            style="color: #dc2626;" title="Delete Candidate">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="text-secondary opacity-75">
                                        <svg class="mx-auto h-12 w-12 mb-4" style="color: var(--text-secondary);"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-semibold text-primary mb-1">No candidates found</p>
                                        <p class="text-sm text-secondary">Get started by creating a new candidate</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($candidates->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: var(--border-color);">
                    {{ $candidates->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Single Candidate Modal -->
    <div id="candidateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="text-xl font-semibold text-primary" id="modalTitle">Add New Candidate</h2>
                <span class="close" onclick="closeModal('candidateModal')">&times;</span>
            </div>
            <form id="candidateForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="formMethod" style="display: none;"></div>
                <div class="modal-body">
                    <!-- Election Information Section -->
                    <div class="mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1.5">Organization <span
                                        class="text-red-500">*</span></label>
                                <select id="organization_id" required onchange="loadOrganizationData(this.value)"
                                    class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                                    style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                    <option value="">Select Organization</option>
                                    @foreach ($organizations as $organization)
                                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="positionSection">
                                <label class="block text-sm font-medium text-primary mb-1.5">Position <span
                                        class="text-red-500">*</span></label>
                                <select name="position_id" id="position_id" required
                                    class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                                    style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                    <option value="">Select Organization First</option>
                                </select>
                            </div>
                            <div id="partylistSection">
                                <label class="block text-sm font-medium text-primary mb-1.5">Election <span
                                        class="text-red-500">*</span></label>
                                <select name="election_id" id="election_id" required
                                    onchange="loadPartylists(this.value)"
                                    class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                                    style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                    <option value="">Select Organization First</option>
                                </select>
                                <div id="electionMessage" class="mt-2 text-sm hidden"></div>
                            </div>
                            <div id="partylistDropdownSection">
                                <label class="block text-sm font-medium text-primary mb-1.5">Partylist (Optional)</label>
                                <select name="partylist_id" id="partylist_id"
                                    class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                                    style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                    <option value="">Independent (No Partylist)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Partylist Platform Section (for full partylist mode) -->
                    <div id="partylistPlatformSection" class="mb-6 hidden">
                        <label class="block text-sm font-medium text-primary mb-1.5">Partylist Platform</label>
                        <textarea name="partylist_platform" id="partylist_platform" rows="4"
                            placeholder="Enter the platform and promises for this partylist (applies to all candidates)..."
                            class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all resize-none"
                            style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                    </div>

                    <!-- Candidate Details Section -->
                    <div id="singleCandidateFields" class="mb-6">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1.5">Candidate Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="candidate_name" id="candidate_name"
                                    placeholder="Enter full name"
                                    class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                                    style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-primary mb-1.5">Photo</label>
                                <input type="file" name="photo" id="photo" accept="image/*"
                                    onchange="previewPhoto(this)"
                                    class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                                    style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                <div id="photoPreviewContainer" class="mt-3 hidden">
                                    <div class="relative inline-block">
                                        <img id="photoPreview" src="" alt="Photo Preview"
                                            class="max-w-xs max-h-48 rounded-lg border-2 shadow-sm"
                                            style="border-color: var(--border-color);">
                                        <button type="button" onclick="clearPhotoPreview()"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors shadow-md"
                                            title="Remove photo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-primary mb-1.5">Platform</label>
                            <textarea name="platform" id="platform" rows="4" placeholder="Enter candidate platform and promises..."
                                class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all resize-none"
                                style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal('candidateModal')"
                        class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors hover:opacity-90"
                        style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-lg text-sm font-medium text-white btn-cpsu-primary shadow-sm hover:shadow-md transition-all">Save
                        Candidate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteCandidateModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="text-lg font-semibold text-primary">Confirm Delete</h3>
                <span class="close" onclick="closeModal('deleteCandidateModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                        style="background: rgba(220, 38, 38, 0.1);">
                        <svg class="w-6 h-6" style="color: #dc2626;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-primary">Are you sure you want to delete this candidate?</p>
                        <p class="text-sm text-secondary mt-1" id="deleteCandidateName"></p>
                        <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('deleteCandidateModal')"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary);"
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                    Cancel
                </button>
                <button onclick="confirmDeleteCandidate()"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors"
                    style="background: #dc2626;" onmouseover="this.style.background='#b91c1c'"
                    onmouseout="this.style.background='#dc2626'">
                    Delete Candidate
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Display Laravel session messages
            @if (session('success'))
                showNotification('{{ session('success') }}', 'success');
            @endif

            @if (session('error'))
                showNotification('{{ session('error') }}', 'error');
            @endif

            let currentCandidateId = null;
            let candidateMode = 'single';

            function filterByElection(electionId) {
                if (electionId) {
                    window.location.href = `{{ route('admin.candidates.index') }}?election=${electionId}`;
                } else {
                    window.location.href = `{{ route('admin.candidates.index') }}`;
                }
            }

            function loadOrganizationData(organizationId, includeElectionId = null) {
                if (!organizationId) {
                    const positionSection = document.getElementById('positionSection');
                    const electionSelect = document.getElementById('election_id');
                    const partylistSelect = document.getElementById('partylist_id');

                    if (positionSection) {
                        positionSection.innerHTML = '<option value="">Select Organization First</option>';
                    }
                    if (electionSelect) {
                        electionSelect.innerHTML = '<option value="">Select Organization First</option>';
                    }
                    if (partylistSelect) {
                        partylistSelect.innerHTML = '<option value="">Independent (No Partylist)</option>';
                    }
                    return;
                }

                // Load positions for the organization
                fetch(`/admin/candidates/positions-by-organization/${organizationId}`)
                    .then(res => res.json())
                    .then(data => {
                        const positionSelect = document.getElementById('position_id');
                        const positionSection = document.getElementById('positionSection');

                        if (candidateMode === 'partylist') {
                            // Flatten positions based on number_of_slots
                            const allSlots = data.positions.flatMap(pos => {
                                const slots = pos.number_of_slots || 1;
                                return Array.from({
                                    length: slots
                                }, (_, i) => ({
                                    ...pos,
                                    slotIndex: i + 1,
                                    totalSlots: slots
                                }));
                            });

                            // For full partylist, create input fields for each position
                            positionSection.innerHTML = `
                    <label class="block text-sm font-medium text-primary mb-2">Fill Positions (Leave empty to skip slots)</label>
                    <div id="partylistPositions" class="space-y-4 max-h-96 overflow-y-auto p-3 border rounded-lg" style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                        ${allSlots.map((pos, index) => `
                                                            <div class="p-4 rounded-lg border" style="background-color: var(--card-bg); border-color: var(--border-color);">
                                                                <label class="block text-sm font-semibold text-primary mb-3">${pos.name} ${pos.totalSlots > 1 ? `(${pos.slotIndex} of ${pos.totalSlots})` : ''}</label>
                                                                <input type="hidden" name="candidates[${index}][position_id]" value="${pos.id}">
                                                                
                                                                <div class="grid grid-cols-2 gap-4 mb-3">
                                                                    <div>
                                                                        <label class="block text-xs font-medium text-primary mb-1.5">Candidate Name</label>
                                                                        <input type="text" name="candidates[${index}][candidate_name]" placeholder="Enter candidate name" class="w-full px-3 py-2 border rounded-lg text-sm" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                                                    </div>
                                                                    <div>
                                                                        <label class="block text-xs font-medium text-primary mb-1.5">Photo</label>
                                                                        <input type="file" name="candidates[${index}][photo]" accept="image/*" onchange="previewPartylistPhoto(this, ${index})" class="w-full px-3 py-2 border rounded-lg text-sm" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                                                    </div>
                                                                </div>
                                                                
                                                                <div id="photoPreviewContainer_${index}" class="mb-3 hidden">
                                                                    <div class="relative inline-block">
                                                                        <img id="photoPreview_${index}" src="" alt="Photo Preview" class="max-w-xs max-h-32 rounded-lg border-2 shadow-sm" style="border-color: var(--border-color);">
                                                                        <button type="button" onclick="clearPartylistPhotoPreview(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors shadow-md" title="Remove photo">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                        `).join('')}
                    </div>
                `;
                        } else {
                            // For single candidate, show position dropdown
                            positionSection.innerHTML = `
                    <label class="block text-sm font-medium text-primary mb-1.5">Position <span class="text-red-500">*</span></label>
                    <select name="position_id" id="position_id" required class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select Position</option>
                        ${data.positions.map(pos => `<option value="${pos.id}">${pos.name}</option>`).join('')}
                    </select>
                `;
                        }
                    })
                    .catch(err => {
                        console.error('Error loading positions:', err);
                    });

                // Load elections for the organization
                // If editing, include the current election ID so it shows even if completed
                const electionUrl = includeElectionId ?
                    `/admin/candidates/elections-by-organization/${organizationId}?include_election_id=${includeElectionId}` :
                    `/admin/candidates/elections-by-organization/${organizationId}`;

                fetch(electionUrl)
                    .then(res => res.json())
                    .then(data => {
                        const electionSelect = document.getElementById('election_id');
                        const partylistSelect = document.getElementById('partylist_id');

                        if (electionSelect) {
                            // Clear and reset election dropdown
                            electionSelect.innerHTML = '<option value="">Select Election</option>';

                            console.log(
                                `Loading ${data.elections.length} upcoming/ongoing elections for organization ${organizationId}`
                            );

                            // Get the message container
                            const electionMessage = document.getElementById('electionMessage');

                            // Hide message first
                            if (electionMessage) {
                                electionMessage.classList.add('hidden');
                                electionMessage.innerHTML = '';
                            }

                            if (data.elections.length === 0) {
                                const option = document.createElement('option');
                                option.value = '';
                                option.textContent = 'No upcoming elections available';
                                option.disabled = true;
                                electionSelect.appendChild(option);

                                // Show message inside modal
                                if (electionMessage) {
                                    electionMessage.className = 'mt-2 text-sm flex items-center space-x-2 p-3 rounded-lg';
                                    electionMessage.style.backgroundColor = 'rgba(220, 38, 38, 0.1)';
                                    electionMessage.style.borderColor = '#dc2626';
                                    electionMessage.style.borderWidth = '1px';
                                    electionMessage.style.color = '#dc2626';
                                    electionMessage.innerHTML = `
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span>No upcoming or ongoing elections found for this organization. Please create an upcoming election first.</span>
                        `;
                                    electionMessage.classList.remove('hidden');
                                }
                            } else {
                                // Hide message if elections are found
                                if (electionMessage) {
                                    electionMessage.classList.add('hidden');
                                    electionMessage.innerHTML = '';
                                }

                                data.elections.forEach(election => {
                                    console.log(
                                        `  - Election ID: ${election.id}, Name: ${election.election_name}, Status: ${election.status || 'N/A'}`
                                    );
                                    const option = document.createElement('option');
                                    option.value = election.id;
                                    option.textContent = election.election_name;
                                    electionSelect.appendChild(option);
                                });
                            }

                            // Ensure onchange handler is attached (re-attach after innerHTML change)
                            electionSelect.onchange = function() {
                                loadPartylists(this.value);
                            };
                        }

                        // Reset partylist when organization changes (elections are reloaded)
                        if (partylistSelect) {
                            partylistSelect.innerHTML = '<option value="">Independent (No Partylist)</option>';
                        }
                    })
                    .catch(err => {
                        console.error('Error loading elections:', err);
                    });
            }

            function loadPartylists(electionId) {
                const partylistSelect = document.getElementById('partylist_id');

                if (!partylistSelect) {
                    console.error('Partylist select element not found');
                    return;
                }

                // Reset dropdown first - always start with Independent option
                partylistSelect.innerHTML = '<option value="">Independent (No Partylist)</option>';

                if (!electionId || electionId === '' || electionId === '0') {
                    console.log('No election selected, partylist reset to Independent only');
                    return;
                }

                // Ensure electionId is a number
                electionId = parseInt(electionId);
                if (isNaN(electionId)) {
                    console.error('Invalid election ID:', electionId);
                    return;
                }

                console.log('Loading partylists for election ID:', electionId);

                // Load partylists for the election
                fetch(`/admin/candidates/partylists/${electionId}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin'
                    })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        console.log('Partylists response:', data);
                        console.log('Requested election ID:', electionId);
                        console.log('Response election ID:', data.election_id);
                        console.log('Response election name:', data.election_name);
                        console.log('Active partylists count:', data.count || 0);

                        // Check if we have partylists in the response
                        const partylists = data.partylists || [];

                        if (Array.isArray(partylists) && partylists.length > 0) {
                            console.log(
                                `✓ Found ${partylists.length} active partylists for election ${electionId} (${data.election_name})`
                            );

                            // Add each partylist as an option
                            partylists.forEach((pl, index) => {
                                console.log(`  [${index + 1}] Adding partylist: ID=${pl.id}, Name="${pl.name}"`);
                                const option = document.createElement('option');
                                option.value = pl.id;
                                option.textContent = pl.name;
                                partylistSelect.appendChild(option);
                            });

                            console.log('✓ Active partylists successfully loaded into dropdown');
                        } else {
                            console.log(
                                `No active partylists found for election ${electionId} (${data.election_name || 'Unknown'})`
                            );
                            // Keep "Independent" option - no need to show error, it's optional
                        }
                    })
                    .catch(err => {
                        console.error('❌ Error loading partylists:', err);
                        // Keep "Independent" option on error
                    });
            }

            function openCreateModal(mode) {
                candidateMode = mode;
                currentCandidateId = null;
                document.getElementById('modalTitle').textContent = mode === 'partylist' ? 'Add Full Partylist' :
                    'Add New Candidate';
                document.getElementById('candidateForm').action = mode === 'partylist' ?
                    '{{ route('admin.candidates.store-multiple') }}' : '{{ route('admin.candidates.store') }}';
                document.getElementById('formMethod').innerHTML = '';

                // Show/hide single candidate fields and partylist dropdown
                const singleCandidateFields = document.getElementById('singleCandidateFields');
                const partylistDropdownSection = document.getElementById('partylistDropdownSection');
                const partylistPlatformSection = document.getElementById('partylistPlatformSection');
                if (mode === 'partylist') {
                    singleCandidateFields.style.display = 'none';
                    // Show partylist dropdown for full partylist mode (required)
                    if (partylistDropdownSection) {
                        partylistDropdownSection.style.display = 'block';
                        const partylistLabel = partylistDropdownSection.querySelector('label');
                        const partylistSelect = document.getElementById('partylist_id');
                        if (partylistLabel) {
                            partylistLabel.innerHTML = 'Partylist <span class="text-red-500">*</span>';
                        }
                        if (partylistSelect) {
                            partylistSelect.required = true;
                        }
                    }
                    // Show partylist platform section
                    if (partylistPlatformSection) {
                        partylistPlatformSection.classList.remove('hidden');
                    }
                } else {
                    singleCandidateFields.style.display = 'block';
                    if (partylistDropdownSection) {
                        partylistDropdownSection.style.display = 'block';
                        const partylistLabel = partylistDropdownSection.querySelector('label');
                        const partylistSelect = document.getElementById('partylist_id');
                        if (partylistLabel) {
                            partylistLabel.innerHTML = 'Partylist (Optional)';
                        }
                        if (partylistSelect) {
                            partylistSelect.required = false;
                        }
                    }
                    // Hide partylist platform section for single candidate mode
                    if (partylistPlatformSection) {
                        partylistPlatformSection.classList.add('hidden');
                    }
                    document.getElementById('candidate_name').required = true;
                }

                // Reset form fields
                document.getElementById('organization_id').value = '';

                // Reset position section
                const positionSection = document.getElementById('positionSection');
                positionSection.innerHTML = `
        <label class="block text-sm font-medium text-primary mb-2">Position <span class="text-red-500">*</span></label>
        <select name="position_id" id="position_id" ${mode === 'partylist' ? '' : 'required'} class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
            <option value="">Select Organization First</option>
        </select>
    `;

                document.getElementById('election_id').innerHTML = '<option value="">Select Organization First</option>';
                document.getElementById('partylist_id').innerHTML = '<option value="">Independent (No Partylist)</option>';

                // Hide and reset election message
                const electionMessage = document.getElementById('electionMessage');
                if (electionMessage) {
                    electionMessage.classList.add('hidden');
                    electionMessage.innerHTML = '';
                }
                document.getElementById('candidate_name').value = '';
                const photoInput = document.getElementById('photo');
                if (photoInput) photoInput.value = '';
                const platformInput = document.getElementById('platform');
                if (platformInput) platformInput.value = '';
                const partylistPlatformInput = document.getElementById('partylist_platform');
                if (partylistPlatformInput) partylistPlatformInput.value = '';

                // Clear photo preview
                clearPhotoPreview();

                // Clear all partylist photo previews if they exist
                const partylistPositions = document.getElementById('partylistPositions');
                if (partylistPositions) {
                    const previewContainers = partylistPositions.querySelectorAll('[id^="photoPreviewContainer_"]');
                    previewContainers.forEach(container => {
                        const index = container.id.replace('photoPreviewContainer_', '');
                        clearPartylistPhotoPreview(index);
                    });
                }

                document.getElementById('candidateModal').classList.add('active');
            }

            function editCandidate(id) {
                fetch(`/admin/candidates/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        currentCandidateId = id;
                        candidateMode = 'single';
                        document.getElementById('modalTitle').textContent = 'Edit Candidate';
                        document.getElementById('candidateForm').action = `/admin/candidates/${id}`;
                        document.getElementById('formMethod').innerHTML = '@method('PUT')';

                        // Show single candidate fields and partylist dropdown for editing
                        document.getElementById('singleCandidateFields').style.display = 'block';
                        document.getElementById('partylistDropdownSection').style.display = 'block';

                        // Load organization and set values
                        if (data.election && data.election.organization_id) {
                            document.getElementById('organization_id').value = data.election.organization_id || '';
                            // When editing, include the current election ID so it shows even if completed
                            loadOrganizationData(data.election.organization_id, data.election_id);
                        }

                        document.getElementById('election_id').value = data.election_id || '';
                        if (data.election_id) {
                            loadPartylists(data.election_id);
                            setTimeout(() => {
                                document.getElementById('position_id').value = data.position_id || '';
                                document.getElementById('partylist_id').value = data.partylist_id || '';
                            }, 1000);
                        }
                        document.getElementById('candidate_name').value = data.candidate_name || '';
                        document.getElementById('platform').value = data.platform || '';
                        document.getElementById('candidateModal').classList.add('active');
                    })
                    .catch(err => {
                        showNotification('Failed to load candidate data. Please try again.', 'error');
                        console.error(err);
                    });
            }

            let currentDeleteCandidateId = null;

            function openDeleteModal(id, name) {
                currentDeleteCandidateId = id;
                document.getElementById('deleteCandidateName').textContent = `Candidate: ${name}`;
                document.getElementById('deleteCandidateModal').classList.add('active');
            }

            function confirmDeleteCandidate() {
                if (!currentDeleteCandidateId) {
                    return;
                }

                fetch(`/admin/candidates/${currentDeleteCandidateId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json().catch(() => ({
                                success: true,
                                message: 'Candidate deleted successfully.'
                            }));
                        }
                        throw new Error('Delete failed');
                    })
                    .then(data => {
                        closeModal('deleteCandidateModal');
                        showNotification(data.message || 'Candidate deleted successfully!', 'success');
                        // Reload the page to refresh the table
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred. Please try again.', 'error');
                    });
            }

            function closeModal(modalId) {
                if (modalId === 'candidateModal' || !modalId) {
                    const modal = document.getElementById('candidateModal');
                    if (modal) {
                        modal.classList.remove('active');
                        // Clear photo preview when closing modal
                        clearPhotoPreview();
                        // Clear all partylist photo previews
                        const partylistPositions = document.getElementById('partylistPositions');
                        if (partylistPositions) {
                            const previewContainers = partylistPositions.querySelectorAll('[id^="photoPreviewContainer_"]');
                            previewContainers.forEach(container => {
                                const index = container.id.replace('photoPreviewContainer_', '');
                                clearPartylistPhotoPreview(index);
                            });
                        }
                    }
                } else {
                    document.getElementById(modalId).classList.remove('active');
                }
            }

            function previewPhoto(input) {
                const previewContainer = document.getElementById('photoPreviewContainer');
                const preview = document.getElementById('photoPreview');
                const file = input.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (preview) preview.src = e.target.result;
                        if (previewContainer) previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    clearPhotoPreview();
                }
            }

            function clearPhotoPreview() {
                const previewContainer = document.getElementById('photoPreviewContainer');
                const preview = document.getElementById('photoPreview');
                const photoInput = document.getElementById('photo');

                if (previewContainer) {
                    previewContainer.classList.add('hidden');
                }
                if (preview) {
                    preview.src = '';
                }
                if (photoInput) {
                    photoInput.value = '';
                }
            }

            // Photo preview functions for partylist candidates
            function previewPartylistPhoto(input, index) {
                const previewContainer = document.getElementById(`photoPreviewContainer_${index}`);
                const preview = document.getElementById(`photoPreview_${index}`);
                const file = input.files[0];

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (preview) preview.src = e.target.result;
                        if (previewContainer) previewContainer.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    clearPartylistPhotoPreview(index);
                }
            }

            function clearPartylistPhotoPreview(index) {
                const previewContainer = document.getElementById(`photoPreviewContainer_${index}`);
                const preview = document.getElementById(`photoPreview_${index}`);
                const photoInput = document.querySelector(`input[name="candidates[${index}][photo]"]`);

                if (previewContainer) {
                    previewContainer.classList.add('hidden');
                }
                if (preview) {
                    preview.src = '';
                }
                if (photoInput) {
                    photoInput.value = '';
                }
            }

            document.getElementById('candidateForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const url = this.action;
                const isEdit = currentCandidateId !== null;

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-HTTP-Method-Override': currentCandidateId ? 'PUT' : 'POST',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => {
                        return res.json().then(data => {
                            if (!res.ok) {
                                throw {
                                    status: res.status,
                                    data: data
                                };
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        closeModal('candidateModal');
                        showNotification(data.message || (isEdit ? 'Candidate has been updated successfully.' :
                            'Candidate has been created successfully.'), 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let message = 'Failed to save candidate.';

                        if (error.status === 422 && error.data && error.data.errors) {
                            // Collect validation errors
                            const errors = Object.values(error.data.errors).flat();
                            message = errors.join('<br>');
                        } else if (error.data && error.data.message) {
                            message = error.data.message;
                        }

                        showNotification(message, 'error');
                    });
            });

            // Close modal when clicking outside
            window.onclick = function(event) {
                const modals = ['candidateModal', 'deleteCandidateModal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (event.target === modal) {
                        closeModal(modalId);
                    }
                });
            }

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

                const icon = type === 'success' ?
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';

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
