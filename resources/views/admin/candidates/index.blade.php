@extends('admin.layouts.master')

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
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 0.5rem;
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
                            <img src="{{ asset('storage/' . $candidate->photo) }}" alt="{{ $candidate->candidate_name }}" class="candidate-photo">
                            @else
                            <div class="w-16 h-16 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <span class="text-white font-semibold text-lg">{{ strtoupper(substr($candidate->candidate_name, 0, 1)) }}</span>
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-primary">{{ $candidate->candidate_name }}</div>
                            @if($candidate->student)
                            <div class="text-xs text-secondary mt-1">{{ $candidate->student->student_id_number }}</div>
                            @endif
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
                        <td colspan="7" class="px-6 py-16 text-center">
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
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Election <span class="text-red-500">*</span></label>
                    <select name="election_id" id="election_id" required onchange="loadPositionsAndPartylists(this.value)" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select Election</option>
                        @foreach($elections as $election)
                        <option value="{{ $election->id }}">{{ $election->election_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Position <span class="text-red-500">*</span></label>
                    <select name="position_id" id="position_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select Election First</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Partylist (Optional)</label>
                    <select name="partylist_id" id="partylist_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Independent (No Partylist)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Candidate Name <span class="text-red-500">*</span></label>
                    <input type="text" name="candidate_name" id="candidate_name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Student (Optional)</label>
                    <input type="text" id="student_search" placeholder="Search by student ID or name..." class="w-full px-3 py-2 border rounded-lg mb-2" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                    <select name="student_id" id="student_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">No Student Link</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Photo</label>
                    <input type="file" name="photo" id="photo" accept="image/*" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Biography</label>
                    <textarea name="biography" id="biography" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Platform</label>
                    <textarea name="platform" id="platform" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-cpsu-primary">Save</button>
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

function loadPositionsAndPartylists(electionId) {
    if (!electionId) {
        document.getElementById('position_id').innerHTML = '<option value="">Select Election First</option>';
        document.getElementById('partylist_id').innerHTML = '<option value="">Independent (No Partylist)</option>';
        return;
    }
    
    // Load positions
    fetch(`/admin/candidates/positions/${electionId}`)
        .then(res => res.json())
        .then(data => {
            const positionSelect = document.getElementById('position_id');
            positionSelect.innerHTML = '<option value="">Select Position</option>';
            data.positions.forEach(pos => {
                positionSelect.innerHTML += `<option value="${pos.id}">${pos.name}</option>`;
            });
        });
    
    // Load partylists
    fetch(`/admin/candidates/partylists/${electionId}`)
        .then(res => res.json())
        .then(data => {
            const partylistSelect = document.getElementById('partylist_id');
            partylistSelect.innerHTML = '<option value="">Independent (No Partylist)</option>';
            data.partylists.forEach(pl => {
                partylistSelect.innerHTML += `<option value="${pl.id}">${pl.name}</option>`;
            });
        });
}

function openCreateModal(mode) {
    candidateMode = mode;
    currentCandidateId = null;
    document.getElementById('modalTitle').textContent = mode === 'partylist' ? 'Add Full Partylist' : 'Add New Candidate';
    document.getElementById('candidateForm').action = '{{ route("admin.candidates.store") }}';
    document.getElementById('formMethod').innerHTML = '';
    document.getElementById('election_id').value = '{{ request("election") ?? "" }}';
    if (document.getElementById('election_id').value) {
        loadPositionsAndPartylists(document.getElementById('election_id').value);
    }
    document.getElementById('position_id').innerHTML = '<option value="">Select Election First</option>';
    document.getElementById('partylist_id').innerHTML = '<option value="">Independent (No Partylist)</option>';
    document.getElementById('candidate_name').value = '';
    document.getElementById('student_id').value = '';
    document.getElementById('photo').value = '';
    document.getElementById('biography').value = '';
    document.getElementById('platform').value = '';
    document.getElementById('candidateModal').classList.add('active');
}

function editCandidate(id) {
    fetch(`/admin/candidates/${id}`)
        .then(res => res.json())
        .then(data => {
            currentCandidateId = id;
            document.getElementById('modalTitle').textContent = 'Edit Candidate';
            document.getElementById('candidateForm').action = `/admin/candidates/${id}`;
            document.getElementById('formMethod').innerHTML = '@method("PUT")';
            document.getElementById('election_id').value = data.election_id || '';
            if (data.election_id) {
                loadPositionsAndPartylists(data.election_id);
                setTimeout(() => {
                    document.getElementById('position_id').value = data.position_id || '';
                    document.getElementById('partylist_id').value = data.partylist_id || '';
                }, 500);
            }
            document.getElementById('candidate_name').value = data.candidate_name || '';
            document.getElementById('student_id').value = data.student_id || '';
            document.getElementById('biography').value = data.biography || '';
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
