@extends('admin.layouts.master')

@section('title', 'Positions Management')
@section('page-title', 'Positions Management')

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
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-primary">All Positions</h3>
            <p class="text-sm text-secondary mt-1">Manage positions for each organization</p>
        </div>
        <div class="flex items-center space-x-3">
            <select id="organizationFilter" onchange="filterByOrganization(this.value)" class="px-3 py-2 border rounded-lg text-sm" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                <option value="">All Organizations</option>
                @foreach($organizations as $org)
                <option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
            <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Position
            </button>
        </div>
    </div>

    <!-- Positions Table -->
    <div class="card rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Position</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Organization</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Order</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                    <tr class="table-row transition-colors border-b" style="border-color: var(--border-color);">
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-primary">{{ $position->name }}</div>
                            @if($position->description)
                            <div class="text-xs text-secondary mt-1">{{ Str::limit($position->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $position->organization->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-sm text-primary">{{ $position->order ?? 0 }}</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $position->is_active ? 'text-white' : 'text-gray-600' }}" style="{{ $position->is_active ? 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);' : 'background: rgba(0, 0, 0, 0.1);' }}">
                                {{ $position->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="editPosition({{ $position->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green-light);" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deletePosition({{ $position->id }}, '{{ $position->name }}')" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete">
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
                                <p class="text-lg font-semibold text-primary mb-1">No positions found</p>
                                <p class="text-sm text-secondary">Get started by creating a new position</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($positions->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--border-color);">
            {{ $positions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="positionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-xl font-semibold text-primary" id="modalTitle">Add New Position</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="positionForm" method="POST">
            @csrf
            <div id="formMethod" style="display: none;"></div>
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Organization <span class="text-red-500">*</span></label>
                    <select name="organization_id" id="organization_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select Organization</option>
                        @foreach($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Position Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Order</label>
                    <input type="number" name="order" id="order" value="0" min="0" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
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

@push('scripts')
<script>
let currentPositionId = null;

function filterByOrganization(orgId) {
    if (orgId) {
        window.location.href = `{{ route('admin.positions.index') }}?organization=${orgId}`;
    } else {
        window.location.href = `{{ route('admin.positions.index') }}`;
    }
}

function openCreateModal() {
    currentPositionId = null;
    document.getElementById('modalTitle').textContent = 'Add New Position';
    document.getElementById('positionForm').action = '{{ route("admin.positions.store") }}';
    document.getElementById('formMethod').innerHTML = '';
    document.getElementById('organization_id').value = '{{ request("organization") ?? "" }}';
    document.getElementById('name').value = '';
    document.getElementById('description').value = '';
    document.getElementById('order').value = '0';
    document.getElementById('is_active').checked = true;
    document.getElementById('positionModal').classList.add('active');
}

function editPosition(id) {
    fetch(`/admin/positions/${id}`)
        .then(res => res.json())
        .then(data => {
            currentPositionId = id;
            document.getElementById('modalTitle').textContent = 'Edit Position';
            document.getElementById('positionForm').action = `/admin/positions/${id}`;
            document.getElementById('formMethod').innerHTML = '@method("PUT")';
            document.getElementById('organization_id').value = data.organization_id || '';
            document.getElementById('name').value = data.name || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('order').value = data.order || 0;
            document.getElementById('is_active').checked = data.is_active;
            document.getElementById('positionModal').classList.add('active');
        })
        .catch(err => {
            alert('Error loading position data');
            console.error(err);
        });
}

function deletePosition(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/positions/${id}`;
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}

function closeModal() {
    document.getElementById('positionModal').classList.remove('active');
}

document.getElementById('positionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': currentPositionId ? 'PUT' : 'POST'
        }
    })
    .then(res => {
        if (res.ok) {
            window.location.reload();
        } else {
            alert('Error saving position');
        }
    })
    .catch(err => {
        alert('Error saving position');
        console.error(err);
    });
});
</script>
@endpush
@endsection
