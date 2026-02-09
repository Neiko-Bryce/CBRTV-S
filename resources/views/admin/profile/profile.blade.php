@extends('admin.layouts.master')

@section('title', 'Profile Settings')
@section('page-title', 'Profile Settings')

@push('styles')
    <style>
        /* Input focus styles */
        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--cpsu-green);
            box-shadow: 0 0 0 3px rgba(0, 102, 51, 0.1);
        }

        .dark input:focus,
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

        /* Modal styles */
        .profile-modal {
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

        .dark .profile-modal {
            background-color: rgba(0, 0, 0, 0.7);
        }

        .profile-modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: auto;
            border-radius: 0.75rem;
            width: 100%;
            max-width: 450px;
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

        /* Mobile Responsive Styles */
        @media (max-width: 640px) {

            /* Header */
            .profile-header {
                text-align: center;
            }

            /* Stats - single column on mobile for 3 cards */
            .stats-grid {
                grid-template-columns: 1fr !important;
            }

            .stats-grid .card {
                padding: 0.875rem;
            }

            .stats-grid .card p.text-2xl {
                font-size: 1.25rem;
            }

            .stats-grid .card .w-12 {
                width: 2.5rem;
                height: 2.5rem;
            }

            .stats-grid .card .w-12 svg {
                width: 1.25rem;
                height: 1.25rem;
            }

            /* Profile card adjustments */
            .profile-card-header {
                padding: 1.25rem;
            }

            .profile-card-header .avatar {
                width: 4rem;
                height: 4rem;
            }

            .profile-card-header .avatar span {
                font-size: 1.5rem;
            }

            /* Form cards */
            .form-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .form-card-header .header-icon {
                display: none;
            }

            /* Buttons - full width on mobile */
            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .form-actions button {
                width: 100%;
                justify-content: center;
            }

            .form-actions .success-message {
                justify-content: center;
                margin-top: 0.5rem;
            }

            /* Danger zone */
            .danger-zone-content {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .danger-zone-content button {
                width: 100%;
            }

            /* Modal adjustments */
            .modal-content {
                margin: 0;
                max-height: 85vh;
            }

            .modal-footer {
                flex-direction: column-reverse;
            }

            .modal-footer button {
                width: 100%;
            }
        }

        /* Tablet Responsive */
        @media (min-width: 641px) and (max-width: 1023px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="profile-header flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-primary">Account Settings</h3>
                <p class="text-sm text-secondary mt-1">Manage your profile and security preferences</p>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="stats-grid grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            <div class="card rounded-lg p-4 stat-card-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary">Account Status</p>
                        <p class="text-2xl font-bold mt-1 stat-value-green">Active</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="card rounded-lg p-4 stat-card-gold">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary">Role</p>
                        <p class="text-2xl font-bold mt-1 stat-value-gold capitalize">{{ $user->usertype ?? 'Admin' }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color: var(--cpsu-green-dark);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="card rounded-lg p-4 stat-card-primary">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-secondary">Member Since</p>
                        <p class="text-2xl font-bold mt-1 stat-value-green">{{ $user->created_at->format('M Y') }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md"
                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="card rounded-lg shadow-sm overflow-hidden">
                <div class="profile-card-header p-6 text-center border-b"
                    style="border-color: var(--border-color); background: linear-gradient(135deg, rgba(22, 101, 52, 0.05) 0%, rgba(20, 83, 45, 0.08) 100%);">
                    <div class="avatar w-20 h-20 rounded-full flex items-center justify-center shadow-lg mx-auto mb-4"
                        style="background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);">
                        <span class="text-white font-bold text-3xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <h4 class="text-lg font-semibold text-primary">{{ $user->name }}</h4>
                    <p class="text-sm text-secondary mt-1">{{ $user->email }}</p>
                    <div class="mt-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white"
                            style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Administrator
                        </span>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-secondary">User ID</span>
                        <span class="text-sm font-medium text-primary">#{{ $user->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-secondary">Last Updated</span>
                        <span class="text-sm font-medium text-primary">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-secondary">Account Age</span>
                        <span class="text-sm font-medium text-primary">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Forms Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b" style="border-color: var(--border-color);">
                        <div class="form-card-header flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-semibold text-primary">Profile Information</h4>
                                <p class="text-sm text-secondary mt-0.5">Update your account details</p>
                            </div>
                            <div class="header-icon w-10 h-10 rounded-lg flex items-center justify-center"
                                style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6">
                        @csrf
                        @method('patch')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-primary mb-2">Full
                                    Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                    required class="w-full px-3 py-2 rounded-lg transition-all"
                                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-primary mb-2">Email
                                    Address</label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $user->email) }}" required
                                    class="w-full px-3 py-2 rounded-lg transition-all"
                                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="form-actions flex flex-wrap items-center mt-5 gap-3">
                            <button type="submit"
                                class="btn-cpsu-primary inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Changes
                            </button>
                            @if (session('status') === 'profile-updated')
                                <span class="success-message text-sm font-medium stat-value-green flex items-center"
                                    x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Saved!
                                </span>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Update Password -->
                <div class="card rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b" style="border-color: var(--border-color);">
                        <div class="form-card-header flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-semibold text-primary">Security Settings</h4>
                                <p class="text-sm text-secondary mt-0.5">Update your password</p>
                            </div>
                            <div class="header-icon w-10 h-10 rounded-lg flex items-center justify-center"
                                style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.profile.password') }}" class="p-6">
                        @csrf
                        @method('put')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-primary mb-2">Current
                                    Password</label>
                                <input type="password" id="current_password" name="current_password"
                                    autocomplete="current-password" class="w-full px-3 py-2 rounded-lg transition-all"
                                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-primary mb-2">New
                                    Password</label>
                                <input type="password" id="password" name="password" autocomplete="new-password"
                                    class="w-full px-3 py-2 rounded-lg transition-all"
                                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-primary mb-2">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    autocomplete="new-password" class="w-full px-3 py-2 rounded-lg transition-all"
                                    style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                            </div>
                        </div>
                        <div class="form-actions flex flex-wrap items-center mt-5 gap-3">
                            <button type="submit"
                                class="btn-cpsu-primary inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                                Update Password
                            </button>
                            @if (session('status') === 'password-updated')
                                <span class="success-message text-sm font-medium stat-value-green flex items-center"
                                    x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Updated!
                                </span>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="card rounded-lg shadow-sm overflow-hidden" style="border-left: 3px solid #dc2626;">
                    <div class="px-6 py-4 border-b" style="border-color: var(--border-color);">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-base font-semibold" style="color: #dc2626;">Danger Zone</h4>
                                <p class="text-sm text-secondary mt-0.5">Irreversible actions</p>
                            </div>
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                style="background: rgba(220, 38, 38, 0.1);">
                                <svg class="w-5 h-5" style="color: #dc2626;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="danger-zone-content flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 rounded-lg"
                            style="background: rgba(220, 38, 38, 0.05); border: 1px solid rgba(220, 38, 38, 0.15);">
                            <div>
                                <h5 class="text-sm font-medium text-primary">Delete Account</h5>
                                <p class="text-sm text-secondary mt-1">Permanently delete your account and all data.</p>
                            </div>
                            <button type="button" onclick="openDeleteModal()"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors shadow-sm flex-shrink-0"
                                style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div id="deleteModal" class="profile-modal">
        <div class="modal-content">
            <div class="p-6 border-b" style="border-color: var(--border-color);">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-primary">Confirm Delete</h3>
                    <button onclick="closeDeleteModal()" class="text-secondary hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.profile.destroy') }}">
                @csrf
                @method('delete')
                <div class="p-6">
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                            style="background: rgba(220, 38, 38, 0.1);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                style="color: #dc2626;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-primary">Are you sure you want to delete your account?</p>
                            <p class="text-sm text-secondary mt-1">This action cannot be undone.</p>
                        </div>
                    </div>
                    <div>
                        <label for="delete_password" class="block text-sm font-medium text-primary mb-2">Confirm
                            Password</label>
                        <input type="password" id="delete_password" name="password" required
                            class="w-full px-3 py-2 rounded-lg transition-all"
                            style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                        @error('password', 'userDeletion')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer p-6 border-t flex justify-end gap-3" style="border-color: var(--border-color);">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                        style="color: var(--text-primary); background-color: var(--bg-tertiary);">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors"
                        style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openDeleteModal() {
                document.getElementById('deleteModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.remove('active');
                document.body.style.overflow = 'auto';
            }

            // Close modal when clicking outside
            document.getElementById('deleteModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeDeleteModal();
                }
            });

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeDeleteModal();
                }
            });
        </script>
    @endpush
@endsection
