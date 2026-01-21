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
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-primary">All Candidates</h3>
            <p class="text-sm text-secondary mt-1">Manage candidates for elections</p>
        </div>
        <div class="flex items-center space-x-3">
            <select id="electionFilter" onchange="filterByElection(this.value)" class="px-3 py-2 border rounded-lg text-sm" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                <option value="">All Elections</option>
                @foreach($elections as $election)
                <option value="{{ $election->id }}" {{ request('election') == $election->id ? 'selected' : '' }}>{{ $election->election_name }}</option>
                @endforeach
            </select>
            <button onclick="openCreateModal('single')" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Single Candidate
            </button>
            <button onclick="openCreateModal('partylist')" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Add Full Partylist
            </button>
        </div>
    </div>

    <!-- Candidates Table -->
    <div class="card rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Photo</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Candidate</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Organization</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Election</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Partylist</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Votes</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                    <tr class="table-row transition-colors border-b" style="border-color: var(--border-color);">
                        <td class="px-4 py-4">
                            @if($candidate->photo)
                            <img src="{{ route('admin.candidates.photo', ['path' => $candidate->photo]) }}" 
                                 alt="{{ $candidate->candidate_name }}" 
                                 class="candidate-photo"
                                 onerror="this.style.display='none';">
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-primary">{{ $candidate->candidate_name }}</div>
                            @if($candidate->student)
                            <div class="text-xs text-secondary mt-1">{{ $candidate->student->student_id_number }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $candidate->election->organization->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $candidate->election->election_name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $candidate->position->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            @if($candidate->partylist)
                            <div class="flex items-center">
                                @if($candidate->partylist->color)
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $candidate->partylist->color }};"></div>
                                @endif
                                <div class="text-sm text-primary">{{ $candidate->partylist->name }}</div>
                            </div>
                            @else
                            <span class="text-xs text-secondary">Independent</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-sm font-semibold text-primary">{{ $candidate->votes_count ?? 0 }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="editCandidate({{ $candidate->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green-light);" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteCandidate({{ $candidate->id }}, '{{ $candidate->candidate_name }}')" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="text-secondary opacity-75">
                                <p class="text-lg font-semibold text-primary mb-1">No candidates found</p>
                                <p class="text-sm text-secondary">Get started by creating a new candidate</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($candidates->hasPages())
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
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="candidateForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="formMethod" style="display: none;"></div>
            <div class="modal-body">
                <!-- Election Information Section -->
                <div class="mb-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-primary mb-1.5">Organization <span class="text-red-500">*</span></label>
                            <select id="organization_id" required onchange="loadOrganizationData(this.value)" class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Select Organization</option>
                                @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="positionSection">
                            <label class="block text-sm font-medium text-primary mb-1.5">Position <span class="text-red-500">*</span></label>
                            <select name="position_id" id="position_id" required class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Select Organization First</option>
                            </select>
                        </div>
                        <div id="partylistSection">
                            <label class="block text-sm font-medium text-primary mb-1.5">Election <span class="text-red-500">*</span></label>
                            <select name="election_id" id="election_id" required onchange="loadPartylists(this.value)" class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Select Organization First</option>
                            </select>
                        </div>
                        <div id="partylistDropdownSection">
                            <label class="block text-sm font-medium text-primary mb-1.5">Partylist (Optional)</label>
                            <select name="partylist_id" id="partylist_id" class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                                <option value="">Independent (No Partylist)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Partylist Platform Section (for full partylist mode) -->
                <div id="partylistPlatformSection" class="mb-6 hidden">
                    <label class="block text-sm font-medium text-primary mb-1.5">Partylist Platform</label>
                    <textarea name="partylist_platform" id="partylist_platform" rows="4" placeholder="Enter the platform and promises for this partylist (applies to all candidates)..." class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all resize-none" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                </div>

                <!-- Candidate Details Section -->
                <div id="singleCandidateFields" class="mb-6">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-primary mb-1.5">Candidate Name <span class="text-red-500">*</span></label>
                            <input type="text" name="candidate_name" id="candidate_name" placeholder="Enter full name" class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-primary mb-1.5">Photo</label>
                            <input type="file" name="photo" id="photo" accept="image/*" onchange="previewPhoto(this)" class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <div id="photoPreviewContainer" class="mt-3 hidden">
                                <div class="relative inline-block">
                                    <img id="photoPreview" src="" alt="Photo Preview" class="max-w-xs max-h-48 rounded-lg border-2 shadow-sm" style="border-color: var(--border-color);">
                                    <button type="button" onclick="clearPhotoPreview()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors shadow-md" title="Remove photo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-1.5">Platform</label>
                        <textarea name="platform" id="platform" rows="4" placeholder="Enter candidate platform and promises..." class="w-full px-3 py-2.5 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all resize-none" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors hover:opacity-90" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-medium text-white btn-cpsu-primary shadow-sm hover:shadow-md transition-all">Save Candidate</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentCandidateId = null;
let candidateMode = 'single';

function filterByElection(electionId) {
    if (electionId) {
        window.location.href = `{{ route('admin.candidates.index') }}?election=${electionId}`;
    } else {
        window.location.href = `{{ route('admin.candidates.index') }}`;
    }
}

function loadOrganizationData(organizationId) {
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
                // For full partylist, create input fields for each position
                positionSection.innerHTML = `
                    <label class="block text-sm font-medium text-primary mb-2">Fill All Positions <span class="text-red-500">*</span></label>
                    <div id="partylistPositions" class="space-y-4 max-h-96 overflow-y-auto p-3 border rounded-lg" style="background-color: var(--bg-tertiary); border-color: var(--border-color);">
                        ${data.positions.map((pos, index) => `
                            <div class="p-4 rounded-lg border" style="background-color: var(--card-bg); border-color: var(--border-color);">
                                <label class="block text-sm font-semibold text-primary mb-3">${pos.name}</label>
                                <input type="hidden" name="candidates[${index}][position_id]" value="${pos.id}">
                                
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-xs font-medium text-primary mb-1.5">Candidate Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="candidates[${index}][candidate_name]" placeholder="Enter candidate name" required class="w-full px-3 py-2 border rounded-lg text-sm" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
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
    fetch(`/admin/candidates/elections-by-organization/${organizationId}`)
        .then(res => res.json())
        .then(data => {
            const electionSelect = document.getElementById('election_id');
            const partylistSelect = document.getElementById('partylist_id');
            
            if (electionSelect) {
                // Clear and reset election dropdown
                electionSelect.innerHTML = '<option value="">Select Election</option>';
                
                console.log(`Loading ${data.elections.length} elections for organization ${organizationId}`);
                
                data.elections.forEach(election => {
                    console.log(`  - Election ID: ${election.id}, Name: ${election.election_name}`);
                    const option = document.createElement('option');
                    option.value = election.id;
                    option.textContent = election.election_name;
                    electionSelect.appendChild(option);
                });
                
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
            console.log('Partylists count:', data.count || 0);
            
            // Show debug info if available
            if (data.debug && data.debug.all_partylists) {
                console.log('📋 All partylists in database:');
                data.debug.all_partylists.forEach(pl => {
                    console.log(`  - Partylist "${pl.name}" (ID: ${pl.id}) is registered for Election ID: ${pl.election_id}`);
                });
            }
            
            // Check if we have partylists in the response
            const partylists = data.partylists || [];
            
            if (Array.isArray(partylists) && partylists.length > 0) {
                console.log(`✓ Found ${partylists.length} partylists for election ${electionId} (${data.election_name})`);
                
                // Add each partylist as an option
                partylists.forEach((pl, index) => {
                    console.log(`  [${index + 1}] Adding partylist: ID=${pl.id}, Name="${pl.name}", Election ID=${pl.election_id}`);
                    const option = document.createElement('option');
                    option.value = pl.id;
                    option.textContent = pl.name;
                    partylistSelect.appendChild(option);
                });
                
                console.log('✓ Partylists successfully loaded into dropdown');
            } else {
                console.warn(`⚠ No partylists found for election ${electionId} (${data.election_name || 'Unknown'})`);
                console.warn('');
                console.warn('🔍 DEBUGGING INFO:');
                if (data.debug && data.debug.all_partylists && data.debug.all_partylists.length > 0) {
                    console.warn(`  Found ${data.debug.all_partylists.length} partylist(s) in database, but none match election ID ${electionId}:`);
                    data.debug.all_partylists.forEach(pl => {
                        const match = pl.election_id == electionId ? '✓ MATCH' : '✗ Different';
                        console.warn(`    - "${pl.name}" (ID: ${pl.id}) → Election ID: ${pl.election_id} ${match}`);
                    });
                    console.warn('');
                    console.warn('💡 SOLUTION:');
                    console.warn('  The partylists were registered with different election IDs.');
                    console.warn('  You need to register partylists specifically for Election ID ' + electionId + ' (' + data.election_name + ')');
                    console.warn('  OR update the existing partylists to use the correct election_id.');
                } else {
                    console.warn('  No partylists found in database at all.');
                    console.warn('  Please register partylists for this election first.');
                }
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
    document.getElementById('modalTitle').textContent = mode === 'partylist' ? 'Add Full Partylist' : 'Add New Candidate';
    document.getElementById('candidateForm').action = mode === 'partylist' ? '{{ route("admin.candidates.store-multiple") }}' : '{{ route("admin.candidates.store") }}';
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
            document.getElementById('formMethod').innerHTML = '@method("PUT")';
            
            // Show single candidate fields and partylist dropdown for editing
            document.getElementById('singleCandidateFields').style.display = 'block';
            document.getElementById('partylistDropdownSection').style.display = 'block';
            
            // Load organization and set values
            if (data.election && data.election.organization_id) {
                document.getElementById('organization_id').value = data.election.organization_id || '';
                loadOrganizationData(data.election.organization_id);
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
            alert('Error loading candidate data');
            console.error(err);
        });
}

function deleteCandidate(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/candidates/${id}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal() {
    document.getElementById('candidateModal').classList.remove('active');
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
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': currentCandidateId ? 'PUT' : 'POST'
        }
    })
    .then(res => {
        if (res.ok) {
            window.location.reload();
        } else {
            alert('Error saving candidate');
        }
    })
    .catch(err => {
        alert('Error saving candidate');
        console.error(err);
    });
});
</script>
@endpush
@endsection
