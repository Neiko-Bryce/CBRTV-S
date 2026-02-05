@extends('admin.layouts.master')

@section('title', 'Students Management')
@section('page-title', 'Students Management')

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
        -webkit-backdrop-filter: blur(4px);
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
        max-width: 700px;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid var(--border-color);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: modalSlideIn 0.3s ease-out;
    }
    .dark .modal-content {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
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
    
    /* Ensure all text uses CSS variables */
    label {
        color: var(--text-primary);
    }
    
    /* File input styling */
    input[type="file"] {
        cursor: pointer;
    }
    
    input[type="file"]::file-selector-button {
        padding: 0.5rem 1rem;
        margin-right: 1rem;
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
        background-color: var(--bg-tertiary);
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.2s;
    }
    
    input[type="file"]::file-selector-button:hover {
        background-color: var(--hover-bg);
    }
    /* Mobile: same pattern as candidates */
    @media (max-width: 768px) {
        .page-header-wrap { flex-direction: column; align-items: stretch; gap: 1rem; }
        .page-header-actions { flex-direction: column; align-items: stretch; gap: 0.75rem; }
        .page-header-actions .btn-add { width: 100%; justify-content: center; }
    }
    @media (max-width: 640px) {
        .page-header-actions .btn-add { flex: 1 1 100%; }
    }
    @media (max-width: 768px) {
        .table-wrap { -webkit-overflow-scrolling: touch; }
        .table-wrap th, .table-wrap td { padding: 0.5rem 0.75rem; font-size: 0.8125rem; }
        .actions-cell .flex { flex-wrap: wrap; justify-content: center; gap: 0.25rem; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4 page-header-wrap">
        <div>
            <h3 class="text-lg font-semibold text-primary">All Students</h3>
            <p class="text-sm text-secondary mt-1">Manage student records and information</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 page-header-actions">
            <button type="button" onclick="openImportModal()" class="inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-secondary btn-add">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span>Import Excel</span>
            </button>
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2 text-white text-sm font-medium rounded-lg transition-all shadow-sm btn-cpsu-primary btn-add">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add New Student</span>
            </button>
            <button type="button" onclick="openDeleteAllModal()" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all shadow-sm btn-add" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white;">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Delete All Students</span>
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card rounded-lg p-4 stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Total Students</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-green);">{{ $stats['total'] ?? $students->total() }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Male</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-green);">{{ $stats['male'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Female</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-gold-dark);">{{ $stats['female'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="card rounded-lg p-4 stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-secondary">Campuses</p>
                    <p class="text-2xl font-bold mt-1" style="color: var(--cpsu-gold-dark);">{{ $stats['campuses'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if(request('search'))
        <div class="mb-4 p-3 rounded-lg text-sm" style="background-color: var(--card-bg); border: 1px solid var(--border-color);">
            <span style="color: var(--text-secondary);">Found <strong style="color: var(--text-primary);">{{ $students->total() }}</strong> result(s) for "<strong style="color: var(--cpsu-green);">{{ request('search') }}</strong>"</span>
            <a href="{{ route('admin.students.index') }}" class="ml-3 text-sm font-medium" style="color: var(--cpsu-green);">Clear search</a>
        </div>
    @endif

    <!-- Students Table -->
    <div class="card rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto table-wrap">
            <table class="min-w-full" style="border-collapse: separate; border-spacing: 0;">
                <thead class="table-header">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 120px;">
                            Student ID
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 200px;">
                            Name
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 120px;">
                            Campus
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 150px;">
                            Course
                        </th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 120px;">
                            Year & Section
                        </th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 100px;">
                            Gender
                        </th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-secondary uppercase tracking-wider border-b" style="border-color: var(--border-color); min-width: 120px;">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    @forelse($students as $student)
                    <tr class="table-row transition-colors border-b" style="border-color: var(--border-color);" id="student-row-{{ $student->id }}">
                        <td class="px-4 py-4 align-middle">
                            <div class="text-sm font-semibold text-primary">
                                {{ $student->student_id_number }}
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr($student->fname ?? $student->lname, 0, 1)) }}</span>
                                </div>
                                <div class="ml-3 min-w-0 flex-1">
                                    <div class="text-sm font-medium text-primary truncate">
                                        {{ $student->fname ? $student->fname . ' ' : '' }}{{ $student->mname ? $student->mname . ' ' : '' }}{{ $student->lname }}{{ $student->ext ? ' ' . $student->ext : '' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="text-sm text-primary">{{ $student->campus ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="text-sm text-primary">{{ $student->course ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="text-sm text-primary">
                                @if($student->yearlevel && $student->section)
                                    {{ $student->yearlevel }} - {{ $student->section }}
                                @elseif($student->yearlevel)
                                    {{ $student->yearlevel }}
                                @elseif($student->section)
                                    {{ $student->section }}
                                @else
                                    -
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle text-center">
                            @if($student->gender)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="{{ $student->gender === 'Male' ? 'background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);' : ($student->gender === 'Female' ? 'background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);' : 'background: rgba(0, 102, 51, 0.1); color: var(--cpsu-green);') }}">
                                    {{ $student->gender }}
                                </span>
                            @else
                                <span class="text-sm text-secondary opacity-75">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-middle text-center actions-cell">
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" onclick="viewStudent({{ $student->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green);" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="editStudent({{ $student->id }})" class="p-1.5 rounded-lg hover:bg-[var(--hover-bg)] transition-colors" style="color: var(--cpsu-green-light);" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button" onclick="deleteStudent({{ $student->id }}, '{{ addslashes(($student->fname ? $student->fname . ' ' : '') . $student->lname . ($student->ext ? ' ' . $student->ext : '')) }}')" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" style="color: #dc2626;" title="Delete">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-lg font-semibold text-primary mb-1">No students found</p>
                                <p class="text-sm text-secondary">Get started by creating a new student or importing from Excel</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Enhanced Pagination -->
        @if($students->hasPages() || $students->total() > 0)
        <div class="px-6 py-4 border-t transition-colors" style="border-color: var(--border-color);">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Pagination Info -->
                <div class="text-sm" style="color: var(--text-secondary);">
                    @if($students->total() > 0)
                        Showing <strong style="color: var(--text-primary);">{{ $students->firstItem() }}</strong> to 
                        <strong style="color: var(--text-primary);">{{ $students->lastItem() }}</strong> of 
                        <strong style="color: var(--cpsu-green);">{{ $students->total() }}</strong> 
                        {{ $students->total() === 1 ? 'student' : 'students' }}
                        @if(request('search'))
                            <span class="ml-2">for "<strong style="color: var(--cpsu-green);">{{ request('search') }}</strong>"</span>
                        @endif
                    @else
                        No students found
                    @endif
                </div>
                
                <!-- Pagination Links -->
                @if($students->hasPages())
                <div class="flex items-center space-x-1">
                    <!-- First Page -->
                    @if($students->onFirstPage())
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $students->url(1) }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="First page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif
                    
                    <!-- Previous Page -->
                    @if($students->onFirstPage())
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $students->previousPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="Previous page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif
                    
                    <!-- Page Numbers -->
                    @foreach($students->getUrlRange(max(1, $students->currentPage() - 2), min($students->lastPage(), $students->currentPage() + 2)) as $page => $url)
                        @if($page == $students->currentPage())
                            <span class="px-4 py-2 rounded-lg text-sm font-semibold text-white shadow-sm" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                    
                    <!-- Next Page -->
                    @if($students->hasMorePages())
                        <a href="{{ $students->nextPageUrl() }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="Next page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    @endif
                    
                    <!-- Last Page -->
                    @if($students->hasMorePages())
                        <a href="{{ $students->url($students->lastPage()) }}" class="px-3 py-2 rounded-lg text-sm font-medium transition-all hover:bg-[var(--hover-bg)]" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);" title="Last page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-lg text-sm font-medium opacity-50 cursor-not-allowed" style="background-color: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Student Modal -->
<div id="studentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary" id="modalTitle">Add New Student</h3>
            <span class="close" onclick="closeModal('studentModal')">&times;</span>
        </div>
        <form id="studentForm" onsubmit="saveStudent(event)">
            <div class="modal-body">
                <input type="hidden" id="studentId" name="id">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="student_id_number" class="block text-sm font-medium text-primary mb-2">Student ID Number *</label>
                            <input type="text" id="student_id_number" name="student_id_number" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="student_id_number-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                        
                        <div>
                            <label for="campus" class="block text-sm font-medium text-primary mb-2">Campus *</label>
                            <input type="text" id="campus" name="campus" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="campus-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="fname" class="block text-sm font-medium text-primary mb-2">First Name</label>
                            <input type="text" id="fname" name="fname" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="fname-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                        
                        <div>
                            <label for="lname" class="block text-sm font-medium text-primary mb-2">Last Name *</label>
                            <input type="text" id="lname" name="lname" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="lname-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                        
                        <div>
                            <label for="mname" class="block text-sm font-medium text-primary mb-2">Middle Name</label>
                            <input type="text" id="mname" name="mname" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="mname-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="ext" class="block text-sm font-medium text-primary mb-2">Name Extension</label>
                            <input type="text" id="ext" name="ext" placeholder="Jr., Sr., III, etc." class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="ext-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                        
                        <div>
                            <label for="gender" class="block text-sm font-medium text-primary mb-2">Gender</label>
                            <select id="gender" name="gender" class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <div id="gender-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="course" class="block text-sm font-medium text-primary mb-2">Course *</label>
                            <input type="text" id="course" name="course" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="course-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                        
                        <div>
                            <label for="yearlevel" class="block text-sm font-medium text-primary mb-2">Year Level *</label>
                            <input type="text" id="yearlevel" name="yearlevel" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="yearlevel-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                        
                        <div>
                            <label for="section" class="block text-sm font-medium text-primary mb-2">Section *</label>
                            <input type="text" id="section" name="section" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <div id="section-error" class="text-red-500 text-sm mt-1"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('studentModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                        onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-all btn-cpsu-primary">
                    <span id="submitBtnText">Create Student</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Student Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Student Details</h3>
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
        </div>
        <div class="modal-body" id="viewModalBody">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('viewModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
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
                    <svg class="w-6 h-6" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-primary">Are you sure you want to delete this student?</p>
                    <p class="text-sm text-secondary mt-1" id="deleteStudentName"></p>
                    <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('deleteModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                Cancel
            </button>
            <button onclick="confirmDelete()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: #dc2626;" 
                    onmouseover="this.style.background='#b91c1c'"
                    onmouseout="this.style.background='#dc2626'">
                Delete Student
            </button>
        </div>
    </div>
</div>

<!-- Delete All Students Confirmation Modal -->
<div id="deleteAllModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Delete All Students</h3>
            <span class="close" onclick="closeModal('deleteAllModal')">&times;</span>
        </div>
        <div class="modal-body">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background: rgba(220, 38, 38, 0.1);">
                    <svg class="w-6 h-6" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-primary">Are you sure you want to delete <strong>all</strong> students?</p>
                    <p class="text-sm text-secondary mt-1">This will remove every student record from the system.</p>
                    <p class="text-xs mt-2" style="color: #dc2626;">This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('deleteAllModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                    onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                Cancel
            </button>
            <button type="button" onclick="confirmDeleteAll()" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background: #dc2626;" 
                    onmouseover="this.style.background='#b91c1c'"
                    onmouseout="this.style.background='#dc2626'">
                Delete All Students
            </button>
        </div>
    </div>
</div>

<!-- Import Excel Modal -->
<div id="importModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="text-lg font-semibold text-primary">Import Students from Excel</h3>
            <span class="close" onclick="closeModal('importModal')">&times;</span>
        </div>
        <form id="importForm" onsubmit="importStudents(event)" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div class="space-y-4">
                    <div>
                        <label for="importFile" class="block text-sm font-medium text-primary mb-2">Select Excel File</label>
                        <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" required class="w-full px-3 py-2 rounded-lg transition-all" style="background-color: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                        <div id="importFile-error" class="text-red-500 text-sm mt-1"></div>
                        <p class="text-xs text-secondary mt-2">Supported: .xlsx, .xls, .csv. Max 1GB; under 50MB recommended for 1000+ rows.</p>
                    </div>
                    
                    <div class="info-box-blue border rounded-lg p-4">
                        <h4 class="text-sm font-semibold mb-2">Excel File Format Requirements:</h4>
                        <ul class="text-xs space-y-1 list-disc list-inside text-secondary">
                            <li>First row must contain column headers</li>
                            <li>Required columns: <strong>Student ID (student_id_number)</strong>, <strong>Campus</strong>, <strong>Last Name (lname)</strong></li>
                            <li>Optional columns: <strong>First Name (fname)</strong>, <strong>Middle Name (mname)</strong>, <strong>Extension (ext)</strong>, <strong>Gender</strong>, <strong>Course</strong>, <strong>Year Level</strong>, <strong>Section</strong></li>
                            <li>Column names are case-insensitive and can have spaces</li>
                        </ul>
                    </div>
                    
                    <div class="info-box-yellow border rounded-lg p-4">
                        <p class="text-xs text-secondary">
                            <strong>Note:</strong> Rows with duplicate Student ID numbers will be skipped. The import process will show you how many students were imported and how many were skipped.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('importModal')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors" style="background-color: var(--bg-tertiary); color: var(--text-primary);" 
                        onmouseover="this.style.backgroundColor='var(--hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--bg-tertiary)'">
                    Cancel
                </button>
                <button type="submit" id="importSubmitBtn" class="px-4 py-2 text-sm font-medium rounded-lg transition-all btn-cpsu-secondary">
                    <span id="importBtnText">Import Students</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentStudentId = null;
    let deleteStudentId = null;

    // Modal Functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = 'auto';
        if (modalId === 'studentModal') {
            resetForm();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modals = ['studentModal', 'viewModal', 'deleteModal', 'deleteAllModal', 'importModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                closeModal(modalId);
            }
        });
    }

    // Import Modal
    function openImportModal() {
        document.getElementById('importForm').reset();
        document.getElementById('importFile-error').textContent = '';
        openModal('importModal');
    }

    // Import Students
    function importStudents(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const submitBtn = document.getElementById('importSubmitBtn');
        const btnText = document.getElementById('importBtnText');
        
        // Disable button and show loading
        submitBtn.disabled = true;
        btnText.textContent = 'Importing...';
        
        // Clear previous errors
        document.getElementById('importFile-error').textContent = '';

        fetch('/admin/students/import', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();
            
            if (!response.ok) {
                // Handle error responses
                let errorMessage = data.message || 'Import failed';
                
                // Show file validation errors
                if (data.errors && data.errors.file) {
                    const fileError = Array.isArray(data.errors.file) ? data.errors.file[0] : data.errors.file;
                    document.getElementById('importFile-error').textContent = fileError;
                    errorMessage = fileError;
                } else if (data.errors && typeof data.errors === 'object') {
                    // Handle other validation errors
                    const errorMessages = [];
                    for (const key in data.errors) {
                        if (Array.isArray(data.errors[key])) {
                            errorMessages.push(...data.errors[key]);
                        } else {
                            errorMessages.push(data.errors[key]);
                        }
                    }
                    if (errorMessages.length > 0) {
                        errorMessage = errorMessages.join(', ');
                    }
                }
                
                // Show skip reasons if available
                if (data.skip_reasons) {
                    const reasons = [];
                    for (const reason in data.skip_reasons) {
                        if (data.skip_reasons[reason] > 0) {
                            reasons.push(`${data.skip_reasons[reason]} ${reason.replace('_', ' ')}`);
                        }
                    }
                    if (reasons.length > 0) {
                        errorMessage += ' Reasons: ' + reasons.join(', ');
                    }
                }
                
                showNotification(errorMessage, 'error');
                return;
            }
            
            if (data.success) {
                closeModal('importModal');
                let message = data.message;
                
                // Add gender statistics if available
                if (data.total_gender_counts) {
                    const counts = data.total_gender_counts;
                    message += '\n\n📊 Total Students by Gender:';
                    message += `\n   • Male: ${counts.Male || 0}`;
                    message += `\n   • Female: ${counts.Female || 0}`;
                    if (counts.Other > 0) {
                        message += `\n   • Other: ${counts.Other || 0}`;
                    }
                    if (counts.null > 0) {
                        message += `\n   • Not Specified: ${counts.null || 0}`;
                    }
                    message += `\n   • Total: ${counts.total || 0} students`;
                }
                
                if (data.errors && Array.isArray(data.errors) && data.errors.length > 0) {
                    message += '\n\nErrors:\n' + data.errors.slice(0, 10).join('\n');
                    if (data.errors.length > 10) {
                        message += `\n... and ${data.errors.length - 10} more errors.`;
                    }
                }
                showNotification(message, 'success');
                // Reload page to refresh table
                setTimeout(() => location.reload(), 2000);
            } else {
                // Handle non-success responses
                let errorMessage = data.message || 'Import failed';
                
                if (data.skip_reasons) {
                    const reasons = [];
                    for (const reason in data.skip_reasons) {
                        if (data.skip_reasons[reason] > 0) {
                            reasons.push(`${data.skip_reasons[reason]} ${reason.replace('_', ' ')}`);
                        }
                    }
                    if (reasons.length > 0) {
                        errorMessage += ' Reasons: ' + reasons.join(', ');
                    }
                }
                
                if (data.errors && Array.isArray(data.errors) && data.errors.length > 0) {
                    errorMessage += '\n\nSample errors:\n' + data.errors.slice(0, 5).join('\n');
                }
                
                showNotification(errorMessage, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Failed to import students. Please try again.';
            
            // Try to extract error message from error object
            if (error.message) {
                errorMessage = error.message;
            } else if (error.response) {
                error.response.json().then(data => {
                    errorMessage = data.message || errorMessage;
                    if (data.errors && data.errors.file) {
                        document.getElementById('importFile-error').textContent = Array.isArray(data.errors.file) ? data.errors.file[0] : data.errors.file;
                        errorMessage = Array.isArray(data.errors.file) ? data.errors.file[0] : data.errors.file;
                    }
                    showNotification(errorMessage, 'error');
                }).catch(() => {
                    showNotification(errorMessage, 'error');
                });
                return;
            }
            
            showNotification(errorMessage, 'error');
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            btnText.textContent = 'Import Students';
        });
    }

    // Create Student
    function openCreateModal() {
        currentStudentId = null;
        document.getElementById('modalTitle').textContent = 'Add New Student';
        document.getElementById('submitBtnText').textContent = 'Create Student';
        resetForm();
        openModal('studentModal');
    }

    // Edit Student
    function editStudent(studentId) {
        currentStudentId = studentId;
        document.getElementById('modalTitle').textContent = 'Edit Student';
        document.getElementById('submitBtnText').textContent = 'Update Student';
        
        fetch(`/admin/students/${studentId}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('studentId').value = data.student.id;
                document.getElementById('student_id_number').value = data.student.student_id_number;
                document.getElementById('campus').value = data.student.campus;
                document.getElementById('fname').value = data.student.fname || '';
                document.getElementById('lname').value = data.student.lname;
                document.getElementById('mname').value = data.student.mname || '';
                document.getElementById('ext').value = data.student.ext || '';
                document.getElementById('gender').value = data.student.gender || '';
                document.getElementById('course').value = data.student.course;
                document.getElementById('yearlevel').value = data.student.yearlevel;
                document.getElementById('section').value = data.student.section;
                openModal('studentModal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load student data');
        });
    }

    // View Student
    function viewStudent(studentId) {
        fetch(`/admin/students/${studentId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.student;
                const html = `
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <span class="text-white font-semibold text-xl">${(student.fname || student.lname).charAt(0).toUpperCase()}</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-primary">${student.fname ? student.fname + ' ' : ''}${student.mname ? student.mname + ' ' : ''}${student.lname}${student.ext ? ' ' + student.ext : ''}</h4>
                                <p class="text-sm text-secondary">ID: ${student.student_id_number}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t" style="border-color: var(--border-color);">
                            <div>
                                <p class="text-xs text-secondary">Campus</p>
                                <p class="text-sm font-medium text-primary mt-1">${student.campus}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Gender</p>
                                <p class="text-sm font-medium text-primary mt-1">${student.gender || '-'}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Course</p>
                                <p class="text-sm font-medium text-primary mt-1">${student.course}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Year Level</p>
                                <p class="text-sm font-medium text-primary mt-1">${student.yearlevel}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Section</p>
                                <p class="text-sm font-medium text-primary mt-1">${student.section}</p>
                            </div>
                            <div>
                                <p class="text-xs text-secondary">Created At</p>
                                <p class="text-sm font-medium text-primary mt-1">${new Date(student.created_at).toLocaleDateString()}</p>
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
            alert('Failed to load student data');
        });
    }

    // Delete Student
    function deleteStudent(studentId, studentName) {
        deleteStudentId = studentId;
        document.getElementById('deleteStudentName').textContent = `Student: ${studentName}`;
        openModal('deleteModal');
    }

    function confirmDelete() {
        if (!deleteStudentId) return;

        fetch(`/admin/students/${deleteStudentId}`, {
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
                const row = document.getElementById(`student-row-${deleteStudentId}`);
                if (row) {
                    row.remove();
                }
                closeModal('deleteModal');
                showNotification(data.message, 'success');
                // Reload page to refresh stats
                setTimeout(() => location.reload(), 1000);
            } else {
                alert(data.message || 'Failed to delete student');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete student');
        });
    }

    // Delete All Students
    function openDeleteAllModal() {
        openModal('deleteAllModal');
    }

    function confirmDeleteAll() {
        fetch('/admin/students/destroy-all', {
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
                closeModal('deleteAllModal');
                showNotification(data.message || 'All students deleted successfully.', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'Failed to delete all students.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to delete all students.', 'error');
        });
    }

    // Save Student (Create/Update)
    function saveStudent(event) {
        event.preventDefault();
        
        clearErrors();
        
        const formData = new FormData(event.target);
        const url = currentStudentId 
            ? `/admin/students/${currentStudentId}` 
            : '/admin/students';
        const method = currentStudentId ? 'PUT' : 'POST';
        
        // Add _method for PUT request
        if (currentStudentId) {
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
                closeModal('studentModal');
                const successMessage = currentStudentId 
                    ? 'Student updated successfully!' 
                    : 'Student created successfully!';
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
                        showNotification(data.message || 'Failed to save student', 'error');
                    }
                }).catch(() => {
                    showNotification('Failed to save student. Please try again.', 'error');
                });
            } else {
                showNotification('Failed to save student. Please try again.', 'error');
            }
        });
    }

    // Reset Form
    function resetForm() {
        document.getElementById('studentForm').reset();
        document.getElementById('studentId').value = '';
        clearErrors();
    }

    // Clear Errors
    function clearErrors() {
        ['student_id_number', 'campus', 'fname', 'mname', 'lname', 'ext', 'gender', 'course', 'yearlevel', 'section'].forEach(field => {
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
