@extends('admin.layouts.master')

@section('title', 'Schools Management')
@section('page-title', 'Schools Management')

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

        .close:hover {
            color: var(--text-primary);
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-wrap items-center justify-between gap-4 page-header-wrap">
            <div>
                <h3 class="text-lg font-semibold text-primary">All Schools</h3>
                <p class="text-sm text-secondary mt-1">Manage school institutions and their portal slugs</p>
            </div>
            @if (auth()->user()->is_super_admin)
                <div class="flex flex-wrap items-center gap-3 page-header-actions">
                    <button type="button" onclick="openCreateModal()"
                        class="inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary btn-add">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Add New School</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- Schools Table -->
        <div class="card rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto table-wrap">
                <table class="min-w-full data-table" style="border-collapse: separate; border-spacing: 0;">
                    <thead class="table-header">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">School Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Portal Slug (Direct URL)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Location</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b"
                                style="border-color: var(--border-color);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            <tr id="school-row-{{ $school->id }}" class="table-row transition-colors border-b"
                                style="border-color: var(--border-color);">
                                <td class="px-4 py-4">
                                    <div id="school-name-{{ $school->id }}" class="text-sm font-semibold text-primary">
                                        {{ $school->name }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div id="school-slug-{{ $school->id }}" class="text-sm text-cpsu-green font-medium">
                                        /{{ $school->slug }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div id="school-location-{{ $school->id }}" class="text-sm text-primary">
                                        {{ $school->location ?? 'Not specified' }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div id="school-status-{{ $school->id }}">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $school->is_active ? 'text-white' : 'text-gray-600' }}"
                                            style="{{ $school->is_active ? 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);' : 'background: rgba(0, 0, 0, 0.1);' }}">
                                            {{ $school->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center actions-cell">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" onclick="viewSchool({{ $school->id }})"
                                            class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors"
                                            style="color: var(--cpsu-green);" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>
                                        <a id="school-link-{{ $school->id }}" href="/{{ $school->slug }}"
                                            target="_blank"
                                            class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors"
                                            style="color: var(--text-secondary);" title="Visit Portal">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                        </a>
                                        @if (auth()->user()->is_super_admin)
                                            <button type="button" onclick="editSchool({{ $school->id }})"
                                                class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors"
                                                style="color: var(--cpsu-green-light);" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                onclick="openDeleteModal({{ $school->id }}, '{{ addslashes($school->name) }}')"
                                                class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                                style="color: #dc2626;" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="text-secondary opacity-75">
                                        <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-semibold text-primary mb-1">No schools found</p>
                                        <p class="text-sm text-secondary">Get started by creating a new school</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($schools->hasPages())
                <div class="px-6 py-4 border-t" style="border-color: var(--border-color);">
                    {{ $schools->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewSchoolModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="text-xl font-semibold text-primary">School Details</h2>
                <span class="close" onclick="closeViewModal()">&times;</span>
            </div>
            <div class="modal-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">School Name</p>
                        <p id="view-name"
                            class="text-sm font-medium text-primary bg-secondary p-3 rounded-lg border border-transparent"
                            style="border-color: var(--border-color);"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Status</p>
                        <div id="view-status-badge" class="inline-flex mt-1"></div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Portal Slug</p>
                        <p id="view-slug"
                            class="text-sm font-medium text-cpsu-green bg-secondary p-3 rounded-lg border border-transparent"
                            style="border-color: var(--border-color);"></p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Location</p>
                        <p id="view-location"
                            class="text-sm font-medium text-primary bg-secondary p-3 rounded-lg border border-transparent"
                            style="border-color: var(--border-color);"></p>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-secondary uppercase tracking-wider mb-1">Direct Portal URL</p>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="view-url" readonly
                            class="flex-1 text-sm bg-secondary p-3 rounded-lg border border-transparent cursor-default"
                            style="border-color: var(--border-color); color: var(--text-primary);">
                        <button type="button" onclick="copyPortalUrl()"
                            class="p-3 bg-cpsu-green text-white rounded-lg hover:bg-cpsu-green-dark transition-colors"
                            title="Copy URL">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeViewModal()"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary);">Close</button>
                @if (auth()->user()->is_super_admin)
                    <button type="button" id="view-edit-btn"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-cpsu-primary">Edit School</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="schoolModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="text-xl font-semibold text-primary" id="modalTitle">Add New School</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="schoolForm" method="POST">
                @csrf
                <div id="formMethod" style="display: none;"></div>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">School Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                            style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <p class="text-xs text-red-500 mt-1 hidden" id="error-name"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Portal Slug (Direct URL)</label>
                        <div class="flex items-center">
                            <span
                                class="inline-flex items-center px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"
                                style="border-color: var(--border-color); background-color: var(--bg-tertiary);">
                                {{ url('/') }}/
                            </span>
                            <input type="text" name="slug" id="slug"
                                class="flex-1 px-3 py-2 border rounded-r-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                                style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                placeholder="e.g. cpsu-main">
                        </div>
                        <p class="text-xs text-red-500 mt-1 hidden" id="error-slug"></p>
                        <p class="text-xs text-secondary mt-1 italic">Leave empty to auto-generate from name</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Location</label>
                        <input type="text" name="location" id="location"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent transition-all"
                            style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <p class="text-xs text-red-500 mt-1 hidden" id="error-location"></p>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                class="rounded border-gray-300 text-cpsu-green focus:ring-cpsu-green">
                            <span class="ml-2 text-sm text-primary">Active</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        style="background-color: var(--bg-tertiary); color: var(--text-primary);">Cancel</button>
                    <button type="submit" id="saveButton"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-cpsu-primary flex items-center space-x-2">
                        <span id="saveButtonText">Save School</span>
                        <svg id="saveSpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>





    @push('scripts')
        <script>
            let currentSchoolId = null;
            let currentDeleteId = null;

            function showNotification(message, type = 'success') {
                const existingNotifications = document.querySelectorAll('.notification-toast');
                existingNotifications.forEach(n => n.remove());

                const notification = document.createElement('div');
                notification.className = `notification-toast admin-notification-toast ${
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

            function updateSchoolRowInTable(school) {
                const row = document.getElementById(`school-row-${school.id}`);
                if (!row) return;

                const nameEl = document.getElementById(`school-name-${school.id}`);
                const slugEl = document.getElementById(`school-slug-${school.id}`);
                const locationEl = document.getElementById(`school-location-${school.id}`);
                const statusEl = document.getElementById(`school-status-${school.id}`);
                const linkEl = document.getElementById(`school-link-${school.id}`);

                if (nameEl) nameEl.textContent = school.name;
                if (slugEl) slugEl.textContent = `/${school.slug}`;
                if (locationEl) locationEl.textContent = school.location || 'Not specified';
                if (linkEl) linkEl.href = `/` + school.slug;

                if (statusEl) {
                    if (school.is_active) {
                        statusEl.innerHTML = `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                Active
                            </span>`;
                    } else {
                        statusEl.innerHTML = `
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-gray-600"
                                style="background: rgba(0, 0, 0, 0.1);">
                                Inactive
                            </span>`;
                    }
                }

                // Add a brief highlight effect
                row.style.transition = 'background-color 0.5s';
                const originalBg = row.style.backgroundColor;
                row.style.backgroundColor = 'rgba(22, 101, 52, 0.1)';
                setTimeout(() => {
                    row.style.backgroundColor = originalBg;
                }, 1000);
            }

            function clearErrors() {
                document.querySelectorAll('[id^="error-"]').forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
                document.querySelectorAll('#schoolForm input').forEach(input => {
                    input.style.borderColor = 'var(--border-color)';
                });
            }

            function openCreateModal() {
                currentSchoolId = null;
                clearErrors();
                document.getElementById('modalTitle').textContent = 'Add New School';
                document.getElementById('schoolForm').action = '{{ route('admin.schools.store') }}';
                document.getElementById('formMethod').innerHTML = '';
                document.getElementById('schoolForm').reset();
                document.getElementById('is_active').checked = true;
                document.getElementById('schoolModal').classList.add('active');
            }

            function viewSchool(id) {
                fetch(`/admin/schools/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('view-name').textContent = data.name;
                        document.getElementById('view-slug').textContent = `/${data.slug}`;
                        document.getElementById('view-location').textContent = data.location || 'Not specified';
                        document.getElementById('view-url').value = `{{ url('/') }}/${data.slug}`;

                        const badge = document.getElementById('view-status-badge');
                        if (data.is_active) {
                            badge.innerHTML =
                                '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">Active</span>';
                        } else {
                            badge.innerHTML =
                                '<span class="px-2.5 py-0.5 rounded-full text-xs font-medium text-gray-600 bg-gray-100">Inactive</span>';
                        }

                        @if (auth()->user()->is_super_admin)
                            document.getElementById('view-edit-btn').onclick = () => {
                                closeViewModal();
                                editSchool(id);
                            };
                        @endif

                        document.getElementById('viewSchoolModal').classList.add('active');
                    });
            }

            function closeViewModal() {
                document.getElementById('viewSchoolModal').classList.remove('active');
            }

            function copyPortalUrl() {
                const urlInput = document.getElementById('view-url');
                urlInput.select();
                document.execCommand('copy');
                showNotification('Portal URL copied to clipboard!');
            }

            function editSchool(id) {
                fetch(`/admin/schools/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        currentSchoolId = id;
                        clearErrors();
                        document.getElementById('modalTitle').textContent = 'Edit School';
                        document.getElementById('schoolForm').action = `/admin/schools/${id}`;
                        document.getElementById('formMethod').innerHTML = '@method('PUT')';
                        document.getElementById('name').value = data.name || '';
                        document.getElementById('slug').value = data.slug || '';
                        document.getElementById('location').value = data.location || '';
                        document.getElementById('is_active').checked = !!data.is_active;
                        document.getElementById('schoolModal').classList.add('active');
                    });
            }

            function openDeleteModal(id, name) {
                Swal.fire({
                    title: 'Confirm Delete',
                    text: `Are you sure you want to delete "${name}"? This action cannot be undone and will fail if there are active users or organizations.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it!',
                    background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#ffffff',
                    color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        currentDeleteId = id;
                        confirmDelete();
                    }
                });
            }

            function closeDeleteModal() {
                // No longer needed with SweetAlert
            }

            function confirmDelete() {
                if (!currentDeleteId) return;

                Swal.showLoading();

                fetch(`/admin/schools/${currentDeleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json().then(data => ({
                        status: res.status,
                        data
                    })))
                    .then(({
                        status,
                        data
                    }) => {
                        if (status === 200 && data.success) {
                            closeDeleteModal();
                            const row = document.getElementById(`school-row-${currentDeleteId}`);
                            if (row) {
                                row.style.transition = 'all 0.5s';
                                row.style.opacity = '0';
                                row.style.transform = 'translateX(20px)';
                                setTimeout(() => row.remove(), 500);
                            }
                            showNotification(data.message);
                        } else {
                            showNotification(data.message || 'Error deleting school', 'error');
                        }
                    })
                    .catch(err => {
                        showNotification('A network error occurred', 'error');
                    });
            }

            function closeModal() {
                document.getElementById('schoolModal').classList.remove('active');
            }

            document.getElementById('schoolForm').addEventListener('submit', function(e) {
                e.preventDefault();
                clearErrors();

                const saveBtn = document.getElementById('saveButton');
                const saveSpinner = document.getElementById('saveSpinner');
                const saveText = document.getElementById('saveButtonText');

                saveBtn.disabled = true;
                saveSpinner.classList.remove('hidden');
                saveText.classList.add('hidden');

                const formData = new FormData(this);
                const method = currentSchoolId ? 'PUT' : 'POST';

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-HTTP-Method-Override': method,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json().then(data => ({
                        status: res.status,
                        data
                    })))
                    .then(({
                        status,
                        data
                    }) => {
                        if (status === 422) { // Validation error
                            saveBtn.disabled = false;
                            saveSpinner.classList.add('hidden');
                            saveText.classList.remove('hidden');

                            Object.keys(data.errors).forEach(key => {
                                const errorEl = document.getElementById(`error-${key}`);
                                if (errorEl) {
                                    errorEl.textContent = data.errors[key][0];
                                    errorEl.classList.remove('hidden');
                                    const input = document.getElementById(key);
                                    if (input) input.style.borderColor = '#dc2626';
                                }
                            });
                            showNotification('Please fix the validation errors.', 'error');
                        } else if (data.success) {
                            closeModal();
                            showNotification(data.message);
                            if (currentSchoolId && data.school) {
                                updateSchoolRowInTable(data.school);
                            } else {
                                setTimeout(() => location.reload(), 1000);
                            }
                        } else {
                            throw new Error(data.message || 'Unknown error');
                        }
                    })
                    .catch(err => {
                        saveBtn.disabled = false;
                        saveSpinner.classList.add('hidden');
                        saveText.classList.remove('hidden');
                        showNotification(err.message || 'Something went wrong.', 'error');
                    });
            });

            // Auto-generate slug from name
            document.getElementById('name').addEventListener('input', function() {
                if (!currentSchoolId && this.value) {
                    const slugInput = document.getElementById('slug');
                    // Simple slugify
                    slugInput.value = this.value
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                }
            });

            window.onclick = function(event) {
                if (event.target.id === 'schoolModal') closeModal();
                if (event.target.id === 'deleteModal') closeDeleteModal();
                if (event.target.id === 'viewSchoolModal') closeViewModal();
            };
        </script>
    @endpush
@endsection
