@extends('admin.layouts.master')

@section('title', 'Users Management')
@section('page-title', 'Users Management')

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
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: modalSlideIn 0.3s ease-out;
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
    
    /* Input focus styles */
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: var(--cpsu-green);
        box-shadow: 0 0 0 3px rgba(0, 102, 51, 0.1);
    }
    
    .dark input:focus,
    .dark select:focus,
    .dark textarea:focus {
        box-shadow: 0 0 0 3px rgba(0, 136, 68, 0.2);
    }
    
    /* Stat value visibility */
    .stat-value-green {
        color: var(--cpsu-green);
    }
    
    .dark .stat-value-green {
        color: var(--cpsu-green-light);
    }
    
    .stat-value-gold {
        color: var(--cpsu-gold-dark);
    }
    
    .dark .stat-value-gold {
        color: var(--cpsu-gold);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-primary">All Users</h3>
            <p class="text-sm text-secondary mt-1">Manage system users and their permissions</p>
        </div>
        <button onclick="openCreateModal()" class="btn-cpsu-primary inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New User
        </button>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card rounded-lg p-4 stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Total Users</p>
                    <p class="text-2xl font-bold mt-1 stat-value-green">{{ $users->total() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Admins</p>
                    <p class="text-2xl font-bold mt-1 stat-value-gold">{{ $users->where('usertype', 'admin')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Students</p>
                    <p class="text-2xl font-bold mt-1 stat-value-green">{{ $users->where('usertype', 'student')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Verified</p>
                    <p class="text-2xl font-bold mt-1 stat-value-gold">{{ $users->whereNotNull('email_verified_at')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 200px;">
                            User
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 200px;">
                            Email
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 100px;">
                            Type
                        </th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 100px;">
                            Status
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 120px;">
                            Created
                        </th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 120px;">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @forelse($users as $user)
                    <tr class="table-row transition-colors border-b" style="border-color: var(--border-color);" id="user-row-{{ $user->id }}">
                        <td class="px-4 py-4 align-middle">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-3 min-w-0 flex-1">
                                    <div class="text-sm font-medium text-primary truncate">
                                        {{ $user->name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="text-sm text-primary">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            @if($user->usertype === 'admin')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                                    Admin
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                    Student
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-middle text-center">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);">
                                    Unverified
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="text-sm text-secondary">{{ $user->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-4 py-4 align-middle text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="viewUser({{ $user->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green);" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button onclick="editUser({{ $user->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green-light);" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-lg font-semibold text-primary mb-1">No users found</p>
                                <p class="text-sm text-secondary">Get started by creating a new user</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-6 py-4 border-t transition-colors" style="border-color: var(--border-color);">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary" id="modalTitle">Add New User</h3>
            <span class="close" onclick="closeModal('userModal')">&times;</span>
        </div>
        <form id="userForm" onsubmit="saveUser(event)">
            <div class="modal-body">
                <input type="hidden" id="userId" name="id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-primary mb-2">Full Name</label>
                        <input type="text" id="name" name="name" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <div id="name-error" class="text-red-500 text-sm mt-1"></div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-primary mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <div id="email-error" class="text-red-500 text-sm mt-1"></div>
                    </div>
                    
                    <div>
                        <label for="usertype" class="block text-sm font-medium text-primary mb-2">User Type</label>
                        <select id="usertype" name="usertype" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <option value="">Select User Type</option>
                            <option value="admin">Admin</option>
                            <option value="student">Student</option>
                        </select>
                        <div id="usertype-error" class="text-red-500 text-sm mt-1"></div>
                    </div>
                    
                    <div id="passwordFields">
                        <div>
                            <label for="password" class="block text-sm font-medium text-primary mb-2">Password</label>
                            <input type="password" id="password" name="password" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="password-error" class="text-red-500 text-sm mt-1"></div>
                            <p class="text-xs text-secondary mt-1" id="passwordHint">Leave blank to keep current password</p>
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-primary mb-2">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('userModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="color: var(--text-primary); background-color: var(--bg-tertiary);">
                    Cancel
                </button>
                <button type="submit" class="btn-cpsu-primary px-4 py-2 text-sm font-medium rounded-lg transition-colors">
                    <span id="submitBtnText">Create User</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View User Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">User Details</h3>
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewModalBody">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('viewModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="color: var(--text-primary); background-color: var(--bg-tertiary);">
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #dc2626;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-primary">Are you sure you want to delete this user?</p>
                    <p class="text-sm text-secondary mt-1" id="deleteUserName"></p>
                    <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('deleteModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="color: var(--text-primary); background-color: var(--bg-tertiary);">
                Cancel
            </button>
            <button onclick="confirmDelete()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background-color: #dc2626;">
                Delete User
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentUserId = null;
    let deleteUserId = null;

    // Modal Functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = 'auto';
        if (modalId === 'userModal') {
            resetForm();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modals = ['userModal', 'viewModal', 'deleteModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    }

    // Create User
    function openCreateModal() {
        currentUserId = null;
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('submitBtnText').textContent = 'Create User';
        document.getElementById('password').required = true;
        document.getElementById('password_confirmation').required = true;
        document.getElementById('passwordHint').style.display = 'none';
        resetForm();
        openModal('userModal');
    }

    // Edit User
    function editUser(userId) {
        currentUserId = userId;
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('submitBtnText').textContent = 'Update User';
        document.getElementById('password').required = false;
        document.getElementById('password_confirmation').required = false;
        document.getElementById('passwordHint').style.display = 'block';
        
        fetch(`/admin/users/${userId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('userId').value = data.user.id;
                document.getElementById('name').value = data.user.name;
                document.getElementById('email').value = data.user.email;
                document.getElementById('usertype').value = data.user.usertype;
                openModal('userModal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load user data');
        });
    }

    // View User
    function viewUser(userId) {
        fetch(`/admin/users/${userId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                const html = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <span class="text-white font-semibold text-xl">${user.name.charAt(0).toUpperCase()}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-primary">${user.name}</h4>
                                <p class="text-sm text-secondary">${user.email}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t transition-colors" style="border-color: var(--border-color);">
                            <div>
                                <p class="text-xs text-secondary">User Type</p>
                                <p class="text-sm font-medium text-primary mt-1">
                                    ${user.usertype === 'admin' ? '<span class="px-2 py-1 rounded text-xs text-white" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">Admin</span>' : '<span class="px-2 py-1 rounded text-xs text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">Student</span>'}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Status</p>
                                <p class="text-sm font-medium text-primary mt-1">
                                    ${user.email_verified_at ? '<span class="px-2 py-1 rounded text-xs text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">Verified</span>' : '<span class="px-2 py-1 rounded text-xs text-white" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);">Unverified</span>'}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Created At</p>
                                <p class="text-sm font-medium text-primary mt-1">${new Date(user.created_at).toLocaleDateString()}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Last Updated</p>
                                <p class="text-sm font-medium text-primary mt-1">${new Date(user.updated_at).toLocaleDateString()}</p>
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
            alert('Failed to load user data');
        });
    }

    // Delete User
    function deleteUser(userId, userName) {
        deleteUserId = userId;
        document.getElementById('deleteUserName').textContent = `User: ${userName}`;
        openModal('deleteModal');
    }

    function confirmDelete() {
        if (!deleteUserId) return;

        fetch(`/admin/users/${deleteUserId}`, {
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
                // Remove row from table
                const row = document.getElementById(`user-row-${deleteUserId}`);
                if (row) {
                    row.remove();
                }
                closeModal('deleteModal');
                showNotification(data.message, 'success');
                // Reload page to refresh stats
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(data.message || 'Failed to delete user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete user');
        });
    }

    // Save User (Create/Update)
    function saveUser(event) {
        event.preventDefault();
        
        clearErrors();
        
        const formData = new FormData(event.target);
        const url = currentUserId 
            ? `/admin/users/${currentUserId}` 
            : '/admin/users';
        const method = currentUserId ? 'PUT' : 'POST';
        
        // Add _method for PUT request
        if (currentUserId) {
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
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw { response: { json: () => Promise.resolve(data) }, status: response.status };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                closeModal('userModal');
                const successMessage = currentUserId 
                    ? 'User updated successfully!' 
                    : 'User created successfully!';
                showNotification(data.message || successMessage, 'success');
                // Reload page to refresh table and stats
                setTimeout(() => location.reload(), 1500);
            } else {
                // Handle validation errors
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
            console.error('Error:', error);
            if (error.response) {
                error.response.json().then(data => {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorElement = document.getElementById(`${field}-error`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[field][0];
                            }
                        });
                    } else {
                        showNotification(data.message || 'Failed to save user', 'error');
                    }
                }).catch(() => {
                    showNotification('Failed to save user. Please try again.', 'error');
                });
            } else {
                showNotification('Failed to save user. Please try again.', 'error');
            }
        });
    }

    // Reset Form
    function resetForm() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        clearErrors();
    }

    // Clear Errors
    function clearErrors() {
        ['name', 'email', 'usertype', 'password'].forEach(field => {
            const errorElement = document.getElementById(`${field}-error`);
            if (errorElement) {
                errorElement.textContent = '';
            }
        });
    }

    // Show Notification
    function showNotification(message, type = 'success') {
        // Remove any existing notifications
        const existingNotifications = document.querySelectorAll('.notification-toast');
        existingNotifications.forEach(n => n.remove());

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px] ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        
        // Set initial styles for animation
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        notification.style.transition = 'all 0.3s ease-out';
        
        // Add icon
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

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 10);

        // Remove after 4 seconds with fade out
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
