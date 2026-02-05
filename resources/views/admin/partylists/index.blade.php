@extends('admin.layouts.master')

@section('title', 'Partylists Management')
@section('page-title', 'Partylists Management')

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
        max-width: 600px;
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
    .modal-body { padding: 1.5rem; }
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
    .close:hover { color: var(--text-primary); }
    @media (max-width: 768px) {
        .page-header-wrap { flex-direction: column; align-items: stretch; gap: 1rem; }
        .page-header-actions { flex-direction: column; align-items: stretch; gap: 0.75rem; }
        .page-header-actions .btn-add { width: 100%; justify-content: center; }
        .page-header-actions select { width: 100%; }
    }
    @media (max-width: 640px) {
        .page-header-actions .btn-add { flex: 1 1 100%; }
    }
    @media (max-width: 640px) {
        .modal-content { width: 95%; max-height: 95vh; }
        .modal-footer { flex-direction: column; }
        .modal-footer button { width: 100%; }
    }
    @media (max-width: 768px) {
        .table-wrap { -webkit-overflow-scrolling: touch; }
        .data-table th, .data-table td { padding: 0.5rem 0.75rem; font-size: 0.8125rem; }
        .actions-cell .flex { flex-wrap: wrap; justify-content: center; gap: 0.25rem; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4 page-header-wrap">
        <div>
            <h3 class="text-lg font-semibold text-primary">All Partylists</h3>
            <p class="text-sm text-secondary mt-1">Manage partylists for elections</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 page-header-actions">
            <select id="electionFilter" onchange="filterByElection(this.value)" class="px-3 py-2 border rounded-lg text-sm min-w-0 flex-1 sm:flex-none sm:min-w-[180px]" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                <option value="">All Elections</option>
                @foreach($elections as $election)
                <option value="{{ $election->id }}" {{ request('election') == $election->id ? 'selected' : '' }}>{{ $election->election_name }}</option>
                @endforeach
            </select>
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary btn-add">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add New Partylist</span>
            </button>
        </div>
    </div>

    <div class="card rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto table-wrap">
            <table class="min-w-full data-table" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Partylist Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Election</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Code</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partylists as $partylist)
                    <tr class="table-row transition-colors border-b" style="border-color: var(--border-color);">
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                @if($partylist->color)
                                <div class="w-4 h-4 rounded-full mr-2" style="background-color: {{ $partylist->color }};"></div>
                                @endif
                                <div class="text-sm font-semibold text-primary">{{ $partylist->name }}</div>
                            </div>
                            @if($partylist->description)
                            <div class="text-xs text-secondary mt-1">{{ Str::limit($partylist->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $partylist->election->election_name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $partylist->code ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $partylist->is_active ? 'text-white' : 'text-gray-600' }}" style="{{ $partylist->is_active ? 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);' : 'background: rgba(0, 0, 0, 0.1);' }}">
                                {{ $partylist->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center actions-cell">
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" onclick="editPartylist({{ $partylist->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green-light);" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="openDeleteModal({{ $partylist->id }}, '{{ addslashes($partylist->name) }}')" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="text-secondary opacity-75">
                                <p class="text-lg font-semibold text-primary mb-1">No partylists found</p>
                                <p class="text-sm text-secondary">Get started by creating a new partylist</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($partylists->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--border-color);">
            {{ $partylists->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="partylistModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-xl font-semibold text-primary" id="modalTitle">Add New Partylist</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="partylistForm" method="POST">
            @csrf
            <div id="formMethod" style="display: none;"></div>
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Election <span class="text-red-500">*</span></label>
                    <select name="election_id" id="election_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select Election</option>
                        @foreach($elections as $election)
                        <option value="{{ $election->id }}">{{ $election->election_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Partylist Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Code</label>
                    <input type="text" name="code" id="code" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Color (Hex)</label>
                    <input type="color" name="color" id="color" class="w-full h-10 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="rounded border-gray-300 text-cpsu-green focus:ring-cpsu-green">
                        <span class="ml-2 text-sm text-primary">Active</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-cpsu-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Confirm Delete</h3>
            <span class="close" onclick="closeDeleteModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(220, 38, 38, 0.1);">
                    <svg class="w-6 h-6" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-primary">Are you sure you want to delete this partylist?</p>
                    <p class="text-sm text-secondary mt-1" id="deleteItemName"></p>
                    <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);">Cancel</button>
            <button type="button" onclick="confirmDelete()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: #dc2626;">Delete Partylist</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPartylistId = null;
let currentDeleteId = null;

function filterByElection(electionId) {
    if (electionId) {
        window.location.href = `{{ route('admin.partylists.index') }}?election=${electionId}`;
    } else {
        window.location.href = `{{ route('admin.partylists.index') }}`;
    }
}

function openCreateModal() {
    currentPartylistId = null;
    document.getElementById('modalTitle').textContent = 'Add New Partylist';
    document.getElementById('partylistForm').action = '{{ route("admin.partylists.store") }}';
    document.getElementById('formMethod').innerHTML = '';
    document.getElementById('election_id').value = '{{ request("election") ?? "" }}';
    document.getElementById('name').value = '';
    document.getElementById('code').value = '';
    document.getElementById('color').value = '#166534';
    document.getElementById('description').value = '';
    document.getElementById('is_active').checked = true;
    document.getElementById('partylistModal').classList.add('active');
}

function editPartylist(id) {
    fetch(`/admin/partylists/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => {
            if (!res.ok) throw new Error('Failed to load');
            return res.json();
        })
        .then(data => {
            currentPartylistId = id;
            document.getElementById('modalTitle').textContent = 'Edit Partylist';
            document.getElementById('partylistForm').action = `/admin/partylists/${id}`;
            document.getElementById('formMethod').innerHTML = '@method("PUT")';
            document.getElementById('election_id').value = data.election_id || '';
            document.getElementById('name').value = data.name || '';
            document.getElementById('code').value = data.code || '';
            document.getElementById('color').value = data.color || '#166534';
            document.getElementById('description').value = data.description || '';
            document.getElementById('is_active').checked = !!data.is_active;
            document.getElementById('partylistModal').classList.add('active');
        })
        .catch(err => {
            showNotification('Failed to load partylist. Please try again.', 'error');
            console.error(err);
        });
}

function openDeleteModal(id, name) {
    currentDeleteId = id;
    document.getElementById('deleteItemName').textContent = name ? `Partylist: ${name}` : 'this partylist';
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    currentDeleteId = null;
    document.getElementById('deleteModal').classList.remove('active');
}

function confirmDelete() {
    if (!currentDeleteId) return;
    fetch(`/admin/partylists/${currentDeleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json().then(data => ({ ok: res.ok, data })).catch(() => ({ ok: false, data: {} })))
    .then(({ ok, data }) => {
        closeDeleteModal();
        if (ok && data.success) {
            showNotification(data.message || 'Partylist deleted successfully.', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showNotification(data.message || 'Could not delete partylist.', 'error');
        }
    })
    .catch(err => {
        closeDeleteModal();
        showNotification('An error occurred. Please try again.', 'error');
        console.error(err);
    });
}

function closeModal() {
    document.getElementById('partylistModal').classList.remove('active');
}

function showNotification(message, type) {
    const existing = document.querySelectorAll('.notification-toast');
    existing.forEach(n => n.remove());
    const el = document.createElement('div');
    el.className = 'notification-toast fixed top-4 right-4 z-[9999] p-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[280px] ' + (type === 'success' ? 'bg-green-500' : 'bg-red-500') + ' text-white';
    el.style.cssText = 'transform:translateX(100%);opacity:0;transition:all 0.3s ease-out';
    el.innerHTML = '<div class="flex-shrink-0">' + (type === 'success' ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>') + '</div><div class="flex-1"><p class="font-medium">' + message + '</p></div>';
    document.body.appendChild(el);
    requestAnimationFrame(() => { el.style.transform = 'translateX(0)'; el.style.opacity = '1'; });
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(100%)';
        setTimeout(() => el.remove(), 300);
    }, 4000);
}

document.getElementById('partylistForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const url = this.action;
    const method = currentPartylistId ? 'PUT' : 'POST';
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': method,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        const contentType = res.headers.get('content-type');
        const isJson = contentType && contentType.includes('application/json');
        if (res.ok) {
            closeModal();
            showNotification(currentPartylistId ? 'Partylist updated successfully.' : 'Partylist created successfully.', 'success');
            setTimeout(() => location.reload(), 800);
            return;
        }
        if (isJson) return res.json().then(data => { throw data; });
        throw { message: 'Something went wrong. Please try again.' };
    })
    .catch(err => {
        const msg = (err && (err.message || err.errors && Object.values(err.errors).flat().join(' '))) || 'Failed to save partylist.';
        showNotification(msg, 'error');
    });
});

window.onclick = function(event) {
    if (event.target.id === 'partylistModal') closeModal();
    if (event.target.id === 'deleteModal') closeDeleteModal();
};
</script>
@endpush
@endsection
