@extends('admin.layouts.master')

@section('title', 'Organizations Management')
@section('page-title', 'Organizations Management')

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
    #viewPositionsModal .modal-content {
        max-width: min(720px, 96vw);
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
    .close:hover { color: var(--text-primary); }
    /* Mobile: header and buttons like candidates */
    @media (max-width: 768px) {
        .page-header-wrap { flex-direction: column; align-items: stretch; gap: 1rem; }
        .page-header-actions { flex-direction: column; align-items: stretch; gap: 0.75rem; }
        .page-header-actions .btn-add { width: 100%; justify-content: center; }
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
        .actions-cell {
            white-space: nowrap;
            min-width: 7.5rem;
        }
        .actions-cell .flex {
            flex-wrap: nowrap;
            justify-content: center;
            align-items: center;
            gap: 0.25rem;
        }
        .actions-cell .flex > button {
            flex-shrink: 0;
        }
    }
    /* Active toggle (organization modal) — same behavior as partylist */
    .org-active-toggle {
        position: relative;
        width: 2.75rem;
        height: 1.5rem;
        border-radius: 9999px;
        background: var(--border-color);
        transition: background 0.2s ease;
        flex-shrink: 0;
    }
    .org-active-toggle::after {
        content: '';
        position: absolute;
        top: 0.125rem;
        left: 0.125rem;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 9999px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        transition: transform 0.2s ease;
    }
    .org-active-input:checked ~ .org-active-toggle {
        background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
    }
    .org-active-input:checked ~ .org-active-toggle::after {
        transform: translateX(1.25rem);
    }
    .org-active-input:focus-visible ~ .org-active-toggle {
        outline: 2px solid var(--cpsu-green);
        outline-offset: 2px;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4 page-header-wrap">
        <div>
            <h3 class="text-lg font-semibold text-primary">All Organizations</h3>
            <p class="text-sm text-secondary mt-1">Manage organization types (SSG, FLP, Classroom, etc.)</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 page-header-actions">
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary btn-add">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add New Organization</span>
            </button>
        </div>
    </div>

    <!-- Organizations Table -->
    <div class="card rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto table-wrap">
            <table class="min-w-full data-table" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Positions</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">Status</th>
                        <th class="actions-cell px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 7.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizations as $org)
                    <tr class="table-row transition-colors border-b" style="border-color: var(--border-color);">
                        <td class="px-4 py-4">
                            <div class="text-sm font-semibold text-primary">{{ $org->name }}</div>
                            @if($org->description)
                            <div class="text-xs text-secondary mt-1">{{ Str::limit($org->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $org->code ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm text-primary">{{ $org->positions_count ?? 0 }} position(s)</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $org->is_active ? 'text-white' : 'text-gray-600' }}" style="{{ $org->is_active ? 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);' : 'background: rgba(0, 0, 0, 0.1);' }}">
                                {{ $org->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center actions-cell">
                            <div class="flex flex-nowrap items-center justify-center gap-1.5">
                                <button type="button" onclick="viewOrganizationPositions({{ $org->id }}, '{{ addslashes($org->name) }}')" class="shrink-0 p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--text-secondary);" title="View positions">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="editOrganization({{ $org->id }})" class="shrink-0 p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green-light);" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="openDeleteModal({{ $org->id }}, '{{ addslashes($org->name) }}')" class="shrink-0 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete">
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
                                <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <p class="text-lg font-semibold text-primary mb-1">No organizations found</p>
                                <p class="text-sm text-secondary">Get started by creating a new organization</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($organizations->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--border-color);">
            {{ $organizations->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="organizationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="text-xl font-semibold text-primary" id="modalTitle">Add New Organization</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form id="organizationForm" method="POST">
            @csrf
            <div id="formMethod" style="display: none;"></div>
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Organization Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Code</label>
                    <input type="text" name="code" id="code" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                </div>
                <div>
                    <label class="block text-sm font-medium text-primary mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent" style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"></textarea>
                </div>
                <div class="rounded-lg p-4 border" style="border-color: var(--border-color); background: var(--bg-tertiary);">
                    <label class="relative flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="sr-only org-active-input" aria-describedby="org_is_active_help">
                        <span class="org-active-toggle shrink-0" aria-hidden="true"></span>
                        <span class="text-sm font-medium text-primary">Active organization</span>
                    </label>
                    <p id="org_is_active_help" class="text-xs text-secondary mt-2 leading-relaxed pl-0 sm:pl-[3.25rem]">
                        When <strong>active</strong>, this organization appears in election and candidate setup.
                        Turn <strong>off</strong> to hide it without deleting historical data.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-cpsu-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- View positions (read-only) -->
<div id="viewPositionsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div>
                <h2 class="text-xl font-semibold text-primary">Positions</h2>
                <p class="text-sm text-secondary mt-1" id="viewPositionsOrgName"></p>
            </div>
            <span class="close" onclick="closeViewPositionsModal()">&times;</span>
        </div>
        <div class="modal-body pt-0">
            <div id="viewPositionsLoading" class="text-sm text-secondary py-6 text-center hidden">Loading…</div>
            <div id="viewPositionsError" class="text-sm text-red-600 py-4 hidden"></div>
            <div id="viewPositionsEmpty" class="text-sm text-secondary py-8 text-center hidden rounded-lg border" style="border-color: var(--border-color);">No positions defined for this organization yet.</div>
            <div class="overflow-x-auto -mx-2 px-2 hidden" id="viewPositionsTableWrap">
                <table class="min-w-full text-sm" style="border-collapse: collapse;">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-secondary uppercase border-b" style="border-color: var(--border-color);">
                            <th class="py-2 pr-4">Position</th>
                            <th class="py-2 pr-4 w-20">Order</th>
                            <th class="py-2 pr-4 w-24">Slots</th>
                            <th class="py-2 w-28">Status</th>
                        </tr>
                    </thead>
                    <tbody id="viewPositionsTableBody"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeViewPositionsModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-cpsu-primary">Close</button>
        </div>
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
                    <p class="text-sm font-medium text-primary">Are you sure you want to delete this organization?</p>
                    <p class="text-sm text-secondary mt-1" id="deleteItemName"></p>
                    <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);">Cancel</button>
            <button type="button" onclick="confirmDelete()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: #dc2626;">Delete Organization</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentOrganizationId = null;
let currentDeleteId = null;

function escapeHtml(text) {
    if (text == null) return '';
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

function closeViewPositionsModal() {
    document.getElementById('viewPositionsModal').classList.remove('active');
}

function viewOrganizationPositions(id, orgName) {
    const modal = document.getElementById('viewPositionsModal');
    const loading = document.getElementById('viewPositionsLoading');
    const errEl = document.getElementById('viewPositionsError');
    const emptyEl = document.getElementById('viewPositionsEmpty');
    const tableWrap = document.getElementById('viewPositionsTableWrap');
    const tbody = document.getElementById('viewPositionsTableBody');
    document.getElementById('viewPositionsOrgName').textContent = orgName || '';
    errEl.classList.add('hidden');
    errEl.textContent = '';
    emptyEl.classList.add('hidden');
    tableWrap.classList.add('hidden');
    tbody.innerHTML = '';
    loading.classList.remove('hidden');
    modal.classList.add('active');

    fetch(`{{ url('/admin/organizations') }}/${id}/positions`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => {
            if (res.status === 403) throw new Error('Not allowed to view this organization.');
            if (!res.ok) throw new Error('Failed to load positions.');
            return res.json();
        })
        .then(data => {
            loading.classList.add('hidden');
            if (!data.success || !data.positions) {
                errEl.textContent = 'Could not load positions.';
                errEl.classList.remove('hidden');
                return;
            }
            if (data.organization && data.organization.name) {
                document.getElementById('viewPositionsOrgName').textContent = data.organization.name;
            }
            const rows = data.positions;
            if (!rows.length) {
                emptyEl.classList.remove('hidden');
                return;
            }
            tableWrap.classList.remove('hidden');
            tbody.innerHTML = rows.map(p => {
                const active = p.is_active ? 'Active' : 'Inactive';
                const badgeStyle = p.is_active
                    ? 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%); color: #fff;'
                    : 'background: rgba(0,0,0,0.1); color: var(--text-secondary);';
                const desc = p.description ? '<div class="text-xs text-secondary mt-0.5">' + escapeHtml(p.description.length > 80 ? p.description.slice(0, 80) + '…' : p.description) + '</div>' : '';
                return '<tr class="border-b" style="border-color: var(--border-color);">' +
                    '<td class="py-3 pr-4 align-top"><div class="font-medium text-primary">' + escapeHtml(p.name) + '</div>' + desc + '</td>' +
                    '<td class="py-3 pr-4 align-top text-primary">' + escapeHtml(String(p.order ?? 0)) + '</td>' +
                    '<td class="py-3 pr-4 align-top text-primary">' + escapeHtml(String(p.number_of_slots ?? 1)) + '</td>' +
                    '<td class="py-3 align-top"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium" style="' + badgeStyle + '">' + active + '</span></td>' +
                    '</tr>';
            }).join('');
        })
        .catch(e => {
            loading.classList.add('hidden');
            errEl.textContent = e.message || 'Failed to load positions.';
            errEl.classList.remove('hidden');
        });
}

function openCreateModal() {
    currentOrganizationId = null;
    document.getElementById('modalTitle').textContent = 'Add New Organization';
    document.getElementById('organizationForm').action = '{{ route("admin.organizations.store") }}';
    document.getElementById('formMethod').innerHTML = '';
    document.getElementById('name').value = '';
    document.getElementById('code').value = '';
    document.getElementById('description').value = '';
    document.getElementById('is_active').checked = true;
    document.getElementById('organizationModal').classList.add('active');
}

function editOrganization(id) {
    fetch(`/admin/organizations/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => {
            if (!res.ok) throw new Error('Failed to load');
            return res.json();
        })
        .then(data => {
            currentOrganizationId = id;
            document.getElementById('modalTitle').textContent = 'Edit Organization';
            document.getElementById('organizationForm').action = `/admin/organizations/${id}`;
            document.getElementById('formMethod').innerHTML = '@method("PUT")';
            document.getElementById('name').value = data.name || '';
            document.getElementById('code').value = data.code || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('is_active').checked = !!data.is_active;
            document.getElementById('organizationModal').classList.add('active');
        })
        .catch(err => {
            showNotification('Failed to load organization. Please try again.', 'error');
            console.error(err);
        });
}

function openDeleteModal(id, name) {
    currentDeleteId = id;
    document.getElementById('deleteItemName').textContent = name ? `Organization: ${name}` : 'this organization';
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    currentDeleteId = null;
    document.getElementById('deleteModal').classList.remove('active');
}

function confirmDelete() {
    if (!currentDeleteId) return;
    fetch(`/admin/organizations/${currentDeleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })).catch(() => ({ ok: false, data: {} })))
    .then(({ ok, data }) => {
        closeDeleteModal();
        if (ok && data.success) {
            showNotification(data.message || 'Organization deleted successfully.', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showNotification(data.message || 'Could not delete organization.', 'error');
        }
    })
    .catch(err => {
        closeDeleteModal();
        showNotification('An error occurred. Please try again.', 'error');
        console.error(err);
    });
}

function closeModal() {
    document.getElementById('organizationModal').classList.remove('active');
}

function showNotification(message, type) {
    const existing = document.querySelectorAll('.notification-toast');
    existing.forEach(n => n.remove());
    const el = document.createElement('div');
    el.className = 'notification-toast admin-notification-toast ' + (type === 'success' ? 'bg-green-500' : 'bg-red-500') + ' text-white';
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

document.getElementById('organizationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const url = this.action;
    const method = currentOrganizationId ? 'PUT' : 'POST';
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
            showNotification(currentOrganizationId ? 'Organization updated successfully.' : 'Organization created successfully.', 'success');
            setTimeout(() => location.reload(), 800);
            return;
        }
        if (isJson) return res.json().then(data => { throw data; });
        throw { message: 'Something went wrong. Please try again.' };
    })
    .catch(err => {
        const msg = (err && (err.message || err.errors && Object.values(err.errors).flat().join(' '))) || 'Failed to save organization.';
        showNotification(msg, 'error');
    });
});

window.onclick = function(event) {
    if (event.target.id === 'organizationModal') closeModal();
    if (event.target.id === 'deleteModal') closeDeleteModal();
    if (event.target.id === 'viewPositionsModal') closeViewPositionsModal();
};
</script>
@endpush
@endsection
