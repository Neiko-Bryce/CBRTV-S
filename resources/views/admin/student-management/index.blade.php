@extends('admin.layouts.master')

@section('title', 'Student Management')
@section('page-title', 'Student Management')

@push('styles')
<style>
    .card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .student-info-card {
        background: linear-gradient(135deg, rgba(0, 102, 51, 0.05) 0%, rgba(0, 136, 68, 0.05) 100%);
        border: 1px solid var(--border-color);
    }
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
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
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
    /* Typeahead dropdown */
    #student_suggestions {
        position: absolute;
        left: 0;
        right: 0;
        top: 100%;
        margin-top: 2px;
        max-height: 280px;
        overflow-y: auto;
        z-index: 50;
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
        background-color: var(--card-bg);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    #student_suggestions:empty {
        display: none;
    }
    .suggestion-item {
        padding: 0.6rem 1rem;
        cursor: pointer;
        transition: background-color 0.15s;
        border-bottom: 1px solid var(--border-color);
    }
    .suggestion-item:last-child {
        border-bottom: none;
    }
    .suggestion-item:hover,
    .suggestion-item.suggestion-active {
        background-color: var(--hover-bg);
    }
    .suggestion-item .suggestion-id {
        font-family: monospace;
        font-weight: 600;
        color: var(--text-primary);
    }
    .suggestion-item .suggestion-name {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Search Section -->
    <div class="card">
        <h2 class="text-xl font-semibold text-primary mb-4">Search Student</h2>
        <div class="flex gap-4 items-start">
            <div class="flex-1">
                <label for="student_id_search" class="block text-sm font-medium text-primary mb-2">Student ID or Name *</label>
                <div class="flex items-center gap-3">
                    <div class="flex-1 relative">
                        <input type="text" id="student_id_search" autocomplete="off" placeholder="Type Student ID or Name for suggestions..." class="w-full px-4 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <div id="student_suggestions" class="absolute left-0 right-0 top-full mt-0.5"></div>
                    </div>
                    <button onclick="searchStudent()" class="px-6 py-2 rounded-lg font-medium text-white transition-all hover:opacity-90 whitespace-nowrap" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        Search
                    </button>
                </div>
                <p class="text-xs text-secondary mt-1">Type to see matching Student ID or Name suggestions; then select or click Search.</p>
                <div id="student_id_error" class="text-red-500 text-sm mt-1"></div>
            </div>
        </div>
    </div>

    <!-- Student Information Card -->
    <div id="studentInfoCard" class="card student-info-card" style="display: none;">
        <h2 class="text-xl font-semibold text-primary mb-4">Student Information</h2>
        <div id="studentInfo" class="grid grid-cols-2 gap-4">
            <!-- Student data will be populated here -->
        </div>
    </div>

    <!-- Account Creation Form -->
    <div id="accountFormCard" class="card" style="display: none;">
        <h2 class="text-xl font-semibold text-primary mb-4">Create Student Account</h2>
        <form id="createAccountForm" onsubmit="createAccount(event)">
            <input type="hidden" id="student_id_hidden" name="student_id">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <div class="space-y-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-primary mb-2">Password *</label>
                    <div class="flex gap-2">
                        <input type="text" id="password" name="password" required readonly class="flex-1 px-4 py-2 rounded-lg transition-all font-mono" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" placeholder="Click Generate Password">
                        <button type="button" onclick="generatePassword()" class="px-4 py-2 rounded-lg font-medium text-white transition-all hover:opacity-90" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                            Generate Password
                        </button>
                    </div>
                    <p class="text-xs text-secondary mt-1">6 characters (uppercase letters and numbers only)</p>
                    <div id="password_error" class="text-red-500 text-sm mt-1"></div>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-2 rounded-lg font-medium text-white transition-all hover:opacity-90" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                        Create Account
                    </button>
                    <button type="button" onclick="resetForm()" class="px-6 py-2 rounded-lg font-medium transition-all" style="background-color: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                        Reset
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Account Exists Message -->
    <div id="accountExistsCard" class="card" style="display: none;">
        <div class="flex items-center space-x-3 p-4 rounded-lg" style="background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3);">
            <svg class="w-6 h-6" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="font-medium text-primary">Account Already Exists</p>
                <p class="text-sm text-secondary">This student already has an account. Email: <span id="existing_email" class="font-mono font-semibold"></span></p>
            </div>
        </div>
    </div>

    <!-- Student Accounts Table -->
    <div class="card">
        <h2 class="text-xl font-semibold text-primary mb-4">Created Student Accounts</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Student ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Campus
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Course
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Year Level
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Password Regenerated
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color);">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    @forelse($studentAccounts as $account)
                    <tr class="hover:bg-[var(--hover-bg)] transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-primary font-mono">{{ $account->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-primary">{{ $account->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-primary">{{ $account->campus ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-primary">{{ $account->course ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-primary">{{ $account->yearlevel ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-semibold text-primary">{{ $account->password_regenerated_count ?? 0 }}</span>
                                <span class="text-xs text-secondary">time(s)</span>
                                @if(($account->password_regenerated_count ?? 0) > 0)
                                <button onclick="showPasswordHistory({{ $account->id }})" class="mt-1 text-xs text-primary hover:underline" style="color: var(--cpsu-green);">
                                    View History
                                </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="openRegenerateModal({{ $account->id }}, '{{ $account->email }}', '{{ $account->name }}', {{ $account->password_regenerated_count ?? 0 }})" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 hover:shadow-sm" style="background-color: rgba(0, 136, 68, 0.08); color: var(--cpsu-green-light); border: 1px solid rgba(0, 136, 68, 0.2);" 
                                        onmouseover="this.style.backgroundColor='rgba(0, 136, 68, 0.12)'; this.style.borderColor='rgba(0, 136, 68, 0.3)';"
                                        onmouseout="this.style.backgroundColor='rgba(0, 136, 68, 0.08)'; this.style.borderColor='rgba(0, 136, 68, 0.2)';"
                                        title="Regenerate Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span class="text-sm font-semibold">Regenerate</span>
                                </button>
                                <button onclick="openDeleteModal({{ $account->id }}, '{{ $account->email }}', '{{ $account->name }}')" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete Account">
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
                                <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--text-secondary);">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                <p class="text-lg font-semibold text-primary mb-1">No student accounts created yet</p>
                                <p class="text-sm text-secondary">Create student accounts using the form above</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($studentAccounts->hasPages())
        <div class="px-6 py-4 border-t transition-colors" style="border-color: var(--border-color);">
            <div class="flex items-center justify-between">
                <div class="text-sm text-secondary">
                    Showing <strong class="text-primary">{{ $studentAccounts->firstItem() }}</strong> to 
                    <strong class="text-primary">{{ $studentAccounts->lastItem() }}</strong> of 
                    <strong class="text-primary" style="color: var(--cpsu-green);">{{ $studentAccounts->total() }}</strong> accounts
                </div>
                <div class="flex items-center space-x-1">
                    @if($studentAccounts->onFirstPage())
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            Previous
                        </span>
                    @else
                        <a href="{{ $studentAccounts->previousPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            Previous
                        </a>
                    @endif
                    
                    @foreach($studentAccounts->getUrlRange(max(1, $studentAccounts->currentPage() - 2), min($studentAccounts->lastPage(), $studentAccounts->currentPage() + 2)) as $page => $url)
                        @if($page == $studentAccounts->currentPage())
                            <span class="px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                    
                    @if($studentAccounts->hasMorePages())
                        <a href="{{ $studentAccounts->nextPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            Next
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            Next
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Password History Modal -->
<div id="passwordHistoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Password Regeneration History</h3>
            <span class="close" onclick="closeModal('passwordHistoryModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div id="passwordHistoryContent">
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 mx-auto" style="border-color: var(--cpsu-green);"></div>
                    <p class="text-sm text-secondary mt-2">Loading history...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regenerate Password Modal -->
<div id="regeneratePasswordModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Regenerate Password</h3>
            <span class="close" onclick="closeModal('regeneratePasswordModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(0, 136, 68, 0.1);">
                    <svg class="w-6 h-6" style="color: var(--cpsu-green-light);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-primary">Are you sure you want to regenerate the password?</p>
                </div>
            </div>
            <div class="space-y-2 p-3 rounded-lg" style="background-color: var(--bg-tertiary);">
                <p class="text-sm text-primary"><span class="font-semibold" id="regenerateStudentId"></span></p>
                <p class="text-sm text-primary"><span class="font-semibold" id="regenerateStudentName"></span></p>
                <p class="text-xs text-secondary mt-2">Previous regenerations: <span class="font-semibold text-primary" id="regenerateCount">0</span> time(s)</p>
            </div>
            <div class="mt-4 p-3 rounded-lg" style="background-color: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3);">
                <p class="text-xs" style="color: #d97706;">
                    <strong>Warning:</strong> A new password will be generated. The old password will no longer work. Make sure to provide the new password to the student.
                </p>
            </div>
            
            <!-- Generated Password Display (initially hidden) -->
            <div id="generatedPasswordSection" class="mt-4 p-4 rounded-lg" style="display: none; background-color: rgba(0, 136, 68, 0.1); border: 1px solid rgba(0, 136, 68, 0.3);">
                <div class="flex items-center space-x-2 mb-2">
                    <svg class="w-5 h-5" style="color: var(--cpsu-green);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-primary">Password Generated Successfully!</p>
                </div>
                <div class="mt-3">
                    <label class="text-xs text-secondary mb-1 block">New Password:</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="generatedPasswordDisplay" readonly class="flex-1 px-3 py-2 rounded-lg font-mono text-lg font-bold text-center" style="background-color: var(--card-bg); color: var(--cpsu-green); border: 2px solid var(--cpsu-green-light); letter-spacing: 0.1em;">
                        <button id="copyPasswordBtn" onclick="copyPassword(this)" class="px-3 py-2 rounded-lg text-sm font-medium text-white transition-colors" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);" 
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-secondary mt-2">Total regenerated: <span class="font-semibold text-primary" id="updatedRegenerateCount">0</span> time(s)</p>
            </div>
        </div>
        <div class="modal-footer">
            <button id="cancelRegenerateBtn" onclick="closeModal('regeneratePasswordModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                Cancel
            </button>
            <button id="regeneratePasswordBtn" onclick="confirmRegeneratePassword()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);" 
                    onmouseover="this.style.opacity='0.9'"
                    onmouseout="this.style.opacity='1'">
                Regenerate Password
            </button>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Confirm Delete</h3>
            <span class="close" onclick="closeModal('deleteAccountModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(220, 38, 38, 0.1);">
                    <svg class="w-6 h-6" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-primary">Are you sure you want to delete this student account?</p>
                    <p class="text-sm text-secondary mt-1" id="deleteStudentId"></p>
                    <p class="text-sm text-secondary" id="deleteStudentName"></p>
                    <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('deleteAccountModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                Cancel
            </button>
            <button onclick="confirmDeleteAccount()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: #dc2626;" 
                    onmouseover="this.style.background='#b91c1c'"
                    onmouseout="this.style.background='#dc2626'">
                Delete Account
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentStudent = null;

    function searchStudent() {
        const searchTerm = document.getElementById('student_id_search').value.trim();
        const errorDiv = document.getElementById('student_id_error');
        
        if (!searchTerm) {
            errorDiv.textContent = 'Please enter a Student ID or Name.';
            return;
        }
        
        errorDiv.textContent = '';
        
        fetch('{{ route("admin.student-management.search") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ search_term: searchTerm })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentStudent = data.student;
                displayStudentInfo(data.student);
                
                if (data.account_exists) {
                    showAccountExists(data.user);
                } else {
                    showAccountForm(data.student.student_id_number);
                }
            } else {
                errorDiv.textContent = data.message || 'Student not found.';
                hideAllCards();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDiv.textContent = 'An error occurred. Please try again.';
            hideAllCards();
        });
    }

    function displayStudentInfo(student) {
        const studentInfo = document.getElementById('studentInfo');
        const fullName = [
            student.fname || '',
            student.mname || '',
            student.lname || '',
            student.ext || ''
        ].filter(Boolean).join(' ');
        
        studentInfo.innerHTML = `
            <div>
                <p class="text-xs text-secondary">Student ID</p>
                <p class="text-sm font-semibold text-primary font-mono">${student.student_id_number}</p>
            </div>
            <div>
                <p class="text-xs text-secondary">Full Name</p>
                <p class="text-sm font-semibold text-primary">${fullName || 'N/A'}</p>
            </div>
            <div>
                <p class="text-xs text-secondary">Campus</p>
                <p class="text-sm font-semibold text-primary">${student.campus || 'N/A'}</p>
            </div>
            <div>
                <p class="text-xs text-secondary">Gender</p>
                <p class="text-sm font-semibold text-primary">${student.gender || 'N/A'}</p>
            </div>
            <div>
                <p class="text-xs text-secondary">Course</p>
                <p class="text-sm font-semibold text-primary">${student.course || 'N/A'}</p>
            </div>
            <div>
                <p class="text-xs text-secondary">Year Level</p>
                <p class="text-sm font-semibold text-primary">${student.yearlevel || 'N/A'}</p>
            </div>
            <div>
                <p class="text-xs text-secondary">Section</p>
                <p class="text-sm font-semibold text-primary">${student.section || 'N/A'}</p>
            </div>
        `;
        
        document.getElementById('studentInfoCard').style.display = 'block';
    }

    function showAccountForm(studentId) {
        document.getElementById('student_id_hidden').value = studentId;
        document.getElementById('accountFormCard').style.display = 'block';
        document.getElementById('accountExistsCard').style.display = 'none';
        document.getElementById('password').value = '';
    }

    function showAccountExists(user) {
        document.getElementById('existing_email').textContent = user.email;
        document.getElementById('accountExistsCard').style.display = 'block';
        document.getElementById('accountFormCard').style.display = 'none';
    }

    function hideAllCards() {
        document.getElementById('studentInfoCard').style.display = 'none';
        document.getElementById('accountFormCard').style.display = 'none';
        document.getElementById('accountExistsCard').style.display = 'none';
    }

    function generatePassword() {
        fetch('{{ route("admin.student-management.generate-password") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('password').value = data.password;
            }
        })
        .catch(error => {
            console.error('Error generating password:', error);
            showNotification('Failed to generate password. Please try again.', 'error');
        });
    }

    function createAccount(event) {
        event.preventDefault();
        
        const password = document.getElementById('password').value;
        const passwordError = document.getElementById('password_error');
        
        if (!password || password.length !== 6) {
            passwordError.textContent = 'Password must be exactly 6 characters.';
            return;
        }
        if (!/^[A-Z0-9]+$/.test(password)) {
            passwordError.textContent = 'Password may only contain uppercase letters and numbers.';
            return;
        }
        
        passwordError.textContent = '';
        
        const formData = new FormData(event.target);
        
        fetch('{{ route("admin.student-management.create-account") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Student account created successfully!', 'success');
                // Reload page to show new account in table
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                passwordError.textContent = data.message || 'Failed to create account.';
                showNotification(data.message || 'Failed to create account.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            passwordError.textContent = 'An error occurred. Please try again.';
        });
    }

    function resetForm() {
        document.getElementById('student_id_search').value = '';
        document.getElementById('password').value = '';
        document.getElementById('student_id_error').textContent = '';
        document.getElementById('password_error').textContent = '';
        hideAllCards();
        currentStudent = null;
    }

    let currentRegenerateUserId = null;
    let currentRegenerateStudentId = null;
    let currentDeleteUserId = null;

    function openRegenerateModal(userId, studentId, studentName, regeneratedCount) {
        currentRegenerateUserId = userId;
        currentRegenerateStudentId = studentId;
        document.getElementById('regenerateStudentId').textContent = `Student ID: ${studentId}`;
        document.getElementById('regenerateStudentName').textContent = `Name: ${studentName}`;
        document.getElementById('regenerateCount').textContent = regeneratedCount;
        
        // Reset modal state
        document.getElementById('generatedPasswordSection').style.display = 'none';
        document.getElementById('generatedPasswordDisplay').value = '';
        
        // Reset buttons
        const regenerateBtn = document.getElementById('regeneratePasswordBtn');
        regenerateBtn.disabled = false;
        regenerateBtn.textContent = 'Regenerate Password';
        regenerateBtn.onclick = confirmRegeneratePassword;
        regenerateBtn.style.opacity = '1';
        
        const cancelBtn = document.getElementById('cancelRegenerateBtn');
        cancelBtn.textContent = 'Cancel';
        cancelBtn.style.display = 'block'; // Show cancel button when opening modal
        cancelBtn.onclick = function() { closeModal('regeneratePasswordModal'); };
        
        document.getElementById('regeneratePasswordModal').classList.add('active');
    }

    function confirmRegeneratePassword() {
        if (!currentRegenerateUserId || !currentRegenerateStudentId) {
            return;
        }

        // Disable button and show loading state
        const regenerateBtn = document.getElementById('regeneratePasswordBtn');
        const originalText = regenerateBtn.textContent;
        regenerateBtn.disabled = true;
        regenerateBtn.textContent = 'Generating...';
        regenerateBtn.style.opacity = '0.6';

        fetch(`/admin/student-management/${currentRegenerateUserId}/regenerate-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show the generated password section
                document.getElementById('generatedPasswordDisplay').value = data.password;
                document.getElementById('updatedRegenerateCount').textContent = data.regenerated_count;
                document.getElementById('generatedPasswordSection').style.display = 'block';
                
                // Update button to "Close" instead of "Regenerate"
                regenerateBtn.disabled = false;
                regenerateBtn.textContent = 'Close';
                regenerateBtn.onclick = function() {
                    closeModal('regeneratePasswordModal');
                    location.reload();
                };
                
                // Hide cancel button since we only need one close button
                const cancelBtn = document.getElementById('cancelRegenerateBtn');
                cancelBtn.style.display = 'none';
            } else {
                showNotification(data.message || 'Failed to regenerate password. Please try again.', 'error');
                regenerateBtn.disabled = false;
                regenerateBtn.textContent = originalText;
                regenerateBtn.style.opacity = '1';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
            regenerateBtn.disabled = false;
            regenerateBtn.textContent = originalText;
            regenerateBtn.style.opacity = '1';
        });
    }

    function copyPassword(button) {
        const passwordInput = document.getElementById('generatedPasswordDisplay');
        passwordInput.select();
        passwordInput.setSelectionRange(0, 99999); // For mobile devices
        
        // Use modern Clipboard API if available, fallback to execCommand
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(passwordInput.value).then(() => {
                showCopyFeedback(button);
            });
        } else {
            document.execCommand('copy');
            showCopyFeedback(button);
        }
    }

    function showCopyFeedback(button) {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        button.style.background = 'rgba(0, 136, 68, 0.8)';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.background = '';
        }, 2000);
    }

    function openDeleteModal(userId, studentId, studentName) {
        currentDeleteUserId = userId;
        document.getElementById('deleteStudentId').textContent = `Student ID: ${studentId}`;
        document.getElementById('deleteStudentName').textContent = `Name: ${studentName}`;
        document.getElementById('deleteAccountModal').classList.add('active');
    }

    function confirmDeleteAccount() {
        if (!currentDeleteUserId) {
            return;
        }

        fetch(`/admin/student-management/${currentDeleteUserId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal('deleteAccountModal');
                showNotification('Student account deleted successfully!', 'success');
                // Reload the page to refresh the table
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Failed to delete account. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        });
    }

    function showPasswordHistory(userId) {
        document.getElementById('passwordHistoryModal').classList.add('active');
        document.getElementById('passwordHistoryContent').innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 mx-auto" style="border-color: var(--cpsu-green);"></div>
                <p class="text-sm text-secondary mt-2">Loading history...</p>
            </div>
        `;

        fetch(`/admin/student-management/${userId}/password-history`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.history && data.history.length > 0) {
                let historyHtml = '<div class="space-y-3">';
                data.history.forEach((record, index) => {
                    const date = new Date(record.regenerated_at);
                    const formattedDate = date.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    historyHtml += `
                        <div class="p-3 rounded-lg border" style="border-color: var(--border-color); background-color: var(--card-bg);">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-primary">Regeneration #${data.history.length - index}</p>
                                    <p class="text-xs text-secondary mt-1">${formattedDate}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-secondary">Regenerated by</p>
                                    <p class="text-sm font-medium text-primary">${record.regenerated_by || 'System'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });
                historyHtml += '</div>';
                document.getElementById('passwordHistoryContent').innerHTML = historyHtml;
            } else {
                document.getElementById('passwordHistoryContent').innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-sm text-secondary">No password regeneration history found.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('passwordHistoryContent').innerHTML = `
                <div class="text-center py-8">
                    <p class="text-sm text-red-500">Failed to load password history.</p>
                </div>
            `;
        });
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modals = ['passwordHistoryModal', 'regeneratePasswordModal', 'deleteAccountModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    }

    // Real-time typeahead: suggest on typing (Student ID or Name)
    let suggestDebounce = null;
    let currentSuggestions = [];
    let highlightedIndex = -1;
    const suggestionsEl = document.getElementById('student_suggestions');
    const searchInput = document.getElementById('student_id_search');

    function fetchSuggestions(q) {
        if (!q || q.length < 2) {
            suggestionsEl.innerHTML = '';
            currentSuggestions = [];
            return;
        }
        const url = '{{ route("admin.student-management.suggest") }}?q=' + encodeURIComponent(q);
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            currentSuggestions = data.suggestions || [];
            highlightedIndex = -1;
            renderSuggestions();
        })
        .catch(() => {
            currentSuggestions = [];
            suggestionsEl.innerHTML = '';
        });
    }

    function renderSuggestions() {
        if (currentSuggestions.length === 0) {
            suggestionsEl.innerHTML = '';
            return;
        }
        suggestionsEl.innerHTML = currentSuggestions.map((s, i) => `
            <div class="suggestion-item ${i === highlightedIndex ? 'suggestion-active' : ''}" data-index="${i}" data-value="${escapeHtml(s.student_id_number)}">
                <div class="suggestion-id">${escapeHtml(s.student_id_number)}</div>
                <div class="suggestion-name">${escapeHtml(s.full_name)}</div>
            </div>
        `).join('');
        suggestionsEl.querySelectorAll('.suggestion-item').forEach(el => {
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                const idx = parseInt(this.getAttribute('data-index'), 10);
                selectSuggestion(idx);
            });
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function selectSuggestion(index) {
        if (index < 0 || index >= currentSuggestions.length) return;
        const s = currentSuggestions[index];
        searchInput.value = s.student_id_number;
        suggestionsEl.innerHTML = '';
        currentSuggestions = [];
        highlightedIndex = -1;
        searchStudent();
    }

    function hideSuggestions() {
        suggestionsEl.innerHTML = '';
        currentSuggestions = [];
        highlightedIndex = -1;
    }

    searchInput.addEventListener('input', function() {
        document.getElementById('student_id_error').textContent = '';
        clearTimeout(suggestDebounce);
        const q = this.value.trim();
        if (q.length < 2) {
            hideSuggestions();
            return;
        }
        suggestDebounce = setTimeout(function() {
            fetchSuggestions(q);
        }, 280);
    });

    searchInput.addEventListener('focus', function() {
        const q = this.value.trim();
        if (q.length >= 2 && currentSuggestions.length > 0) renderSuggestions();
    });

    searchInput.addEventListener('blur', function() {
        setTimeout(hideSuggestions, 200);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (currentSuggestions.length === 0) {
            if (e.key === 'Enter') { e.preventDefault(); searchStudent(); }
            return;
        }
        if (e.key === 'Escape') {
            hideSuggestions();
            return;
        }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            highlightedIndex = Math.min(highlightedIndex + 1, currentSuggestions.length - 1);
            renderSuggestions();
            return;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            highlightedIndex = Math.max(highlightedIndex - 1, -1);
            renderSuggestions();
            return;
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            if (highlightedIndex >= 0) selectSuggestion(highlightedIndex);
            else searchStudent();
        }
    });

    // Allow Enter key to search (when no dropdown)
    document.getElementById('student_id_search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && currentSuggestions.length === 0) {
            e.preventDefault();
            searchStudent();
        }
    });

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
