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
                            <tr class="table-row transition-colors border-b" style="border-color: var(--border-color);">
                                <td class="px-4 py-4">
                                    <div class="text-sm font-semibold text-primary">{{ $school->name }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-cpsu-green font-medium">/{{ $school->slug }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-sm text-primary">{{ $school->location ?? 'Not specified' }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $school->is_active ? 'text-white' : 'text-gray-600' }}"
                                        style="{{ $school->is_active ? 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);' : 'background: rgba(0, 0, 0, 0.1);' }}">
                                        {{ $school->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center actions-cell">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="/{{ $school->slug }}" target="_blank"
                                            class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors"
                                            style="color: var(--cpsu-green);" title="View Landing Page">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
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
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent"
                            style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Portal Slug (Direct URL)</label>
                        <div class="flex items-center">
                            <span
                                class="inline-flex items-center px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm"
                                style="border-color: var(--border-color); background-color: var(--bg-tertiary);">
                                votewisely.com/
                            </span>
                            <input type="text" name="slug" id="slug"
                                class="flex-1 px-3 py-2 border rounded-r-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent"
                                style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                placeholder="e.g. cpsu-main">
                        </div>
                        <p class="text-xs text-secondary mt-1 italic">Leave empty to auto-generate from name</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-primary mb-2">Location</label>
                        <input type="text" name="location" id="location"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-cpsu-green focus:border-transparent"
                            style="background-color: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
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
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-white btn-cpsu-primary">Save</button>
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
                        <p class="text-sm font-medium text-primary">Are you sure you want to delete this school?</p>
                        <p class="text-sm text-secondary mt-1" id="deleteItemName"></p>
                        <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                    style="background-color: var(--bg-tertiary); color: var(--text-primary);">Cancel</button>
                <button type="button" onclick="confirmDelete()"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors"
                    style="background: #dc2626;">Delete School</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentSchoolId = null;
            let currentDeleteId = null;

            function openCreateModal() {
                currentSchoolId = null;
                document.getElementById('modalTitle').textContent = 'Add New School';
                document.getElementById('schoolForm').action = '{{ route('admin.schools.store') }}';
                document.getElementById('formMethod').innerHTML = '';
                document.getElementById('name').value = '';
                document.getElementById('slug').value = '';
                document.getElementById('location').value = '';
                document.getElementById('is_active').checked = true;
                document.getElementById('schoolModal').classList.add('active');
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
                currentDeleteId = id;
                document.getElementById('deleteItemName').textContent = `School: ${name}`;
                document.getElementById('deleteModal').classList.add('active');
            }

            function closeDeleteModal() {
                currentDeleteId = null;
                document.getElementById('deleteModal').classList.remove('active');
            }

            function confirmDelete() {
                if (!currentDeleteId) return;
                fetch(`/admin/schools/${currentDeleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        closeDeleteModal();
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error deleting school');
                        }
                    });
            }

            function closeModal() {
                document.getElementById('schoolModal').classList.remove('active');
            }

            document.getElementById('schoolForm').addEventListener('submit', function(e) {
                e.preventDefault();
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
                    .then(res => res.json())
                    .then(data => {
                        if (data.errors) {
                            alert(Object.values(data.errors).flat().join('\n'));
                        } else {
                            location.reload();
                        }
                    })
                    .catch(err => {
                        location.reload(); // Likely success redirect if not JSON
                    });
            });

            window.onclick = function(event) {
                if (event.target.id === 'schoolModal') closeModal();
                if (event.target.id === 'deleteModal') closeDeleteModal();
            };
        </script>
    @endpush
@endsection
