<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)
}" x-init="if (darkMode) { document.documentElement.classList.add('dark') } else { document.documentElement.classList.remove('dark') };
$watch('darkMode', val => {
    localStorage.setItem('darkMode', val);
    val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')
});
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => { if (!localStorage.getItem('darkMode')) { darkMode = e.matches } })"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - CpsuVotewisely.com</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700"
        rel="stylesheet" />

    <!-- Alpine.js for dark mode toggle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Initialize Alpine store
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebarOpen', false);
        });
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Landing page design system: gov-green + gov-gold */
        :root {
            --cpsu-green: #166534;
            --cpsu-gold: #facc15;
            --cpsu-green-light: #16a34a;
            --cpsu-green-dark: #14532d;
            --cpsu-gold-light: #eab308;
            --cpsu-gold-dark: #ca8a04;
        }

        .heading-font {
            font-family: 'Playfair Display', serif;
        }

        /* Light Mode */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --hover-bg: #f1f5f9;
            --accent-primary: var(--cpsu-green);
            --accent-secondary: var(--cpsu-gold);
            --accent-light: var(--cpsu-green-light);
            --accent-dark: var(--cpsu-green-dark);
            --header-bg: rgba(255, 255, 255, 0.98);
            --footer-bg: rgba(248, 250, 252, 0.95);
        }

        /* Dark Mode */
        .dark {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --border-color: #334155;
            --card-bg: #1e293b;
            --hover-bg: #334155;
            --accent-primary: var(--cpsu-green-light);
            --accent-secondary: var(--cpsu-gold-light);
            --accent-light: var(--cpsu-green);
            --accent-dark: var(--cpsu-green-dark);
            --header-bg: rgba(15, 23, 42, 0.98);
            --footer-bg: rgba(30, 41, 59, 0.95);
        }

        /* Ensure proper contrast in dark mode */
        .dark .text-primary {
            color: #f1f5f9;
        }

        .dark .text-secondary {
            color: #cbd5e1;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Ensure smooth transitions for all elements */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        /* Override for specific elements that shouldn't transition */
        button,
        a,
        input,
        select,
        textarea {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        .sidebar {
            background-color: var(--bg-primary);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s ease;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }

        .dark .sidebar {
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.3);
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .dark .card {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.12);
        }

        .dark .card:hover {
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.25);
        }

        /* Sidebar Styles */
        .sidebar-container {
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .dark .sidebar-container {
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.4);
        }

        .sidebar-header {
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.06) 0%, rgba(20, 83, 45, 0.08) 100%);
            border-color: var(--border-color);
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .dark .sidebar-header {
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.15) 0%, rgba(20, 83, 45, 0.2) 100%);
        }

        .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background-color: var(--text-secondary);
        }

        .nav-link {
            color: var(--text-secondary);
            transition: all 0.3s ease;
            position: relative;
            margin-bottom: 0.25rem;
        }

        .nav-link:hover {
            background-color: var(--hover-bg);
            color: var(--cpsu-green);
            transform: translateX(4px);
            box-shadow: 0 2px 6px rgba(22, 101, 52, 0.1);
        }

        .dark .nav-link:hover {
            background-color: rgba(22, 101, 52, 0.15);
            box-shadow: 0 2px 6px rgba(22, 101, 52, 0.2);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.25);
            font-weight: 600;
            transform: translateX(0);
        }

        .dark .nav-link.active {
            box-shadow: 0 4px 12px rgba(22, 101, 52, 0.35);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 70%;
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);
            border-radius: 0 3px 3px 0;
            box-shadow: 0 0 8px rgba(250, 204, 21, 0.4);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }

        .dark .nav-link.active::before {
            box-shadow: 0 0 10px rgba(250, 204, 21, 0.5);
        }

        .nav-link.active svg {
            color: white;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }

        .nav-link:not(.active) svg {
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .nav-link:hover:not(.active) svg {
            color: var(--cpsu-green);
        }

        .nav-link span {
            transition: color 0.3s ease, font-weight 0.3s ease;
        }

        .nav-link.active span {
            letter-spacing: 0.025em;
        }

        /* CPSU Branded Buttons – landing style */
        .btn-cpsu-primary {
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-dark) 100%);
            color: white;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        .btn-cpsu-primary:hover {
            background: var(--cpsu-green-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(22, 101, 52, 0.3);
        }

        .btn-cpsu-secondary {
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);
            color: #14532d;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }

        .btn-cpsu-secondary:hover {
            background: var(--cpsu-gold-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(250, 204, 21, 0.35);
        }

        /* Stat Card Gradients – landing gov-green / gov-gold */
        .stat-card-primary {
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.08) 0%, rgba(20, 83, 45, 0.06) 100%);
            border-left: 4px solid var(--cpsu-green);
        }

        .dark .stat-card-primary {
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.2) 0%, rgba(20, 83, 45, 0.15) 100%);
        }

        .stat-card-gold {
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.08) 0%, rgba(234, 179, 8, 0.06) 100%);
            border-left: 4px solid var(--cpsu-gold);
        }

        .dark .stat-card-gold {
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.15) 0%, rgba(234, 179, 8, 0.12) 100%);
        }

        /* Activity Icons – landing palette */
        .activity-icon-green {
            background: rgba(22, 101, 52, 0.1);
        }

        .dark .activity-icon-green {
            background: rgba(22, 101, 52, 0.2);
        }

        .activity-icon-gold {
            background: rgba(250, 204, 21, 0.12);
        }

        .dark .activity-icon-gold {
            background: rgba(250, 204, 21, 0.2);
        }

        /* Text Colors */
        .text-primary {
            color: var(--text-primary);
        }

        .text-secondary {
            color: var(--text-secondary);
        }

        /* Table Styles – landing-style header accent */
        .table-header {
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.06) 0%, rgba(20, 83, 45, 0.04) 100%);
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
        }

        .dark .table-header {
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.15) 0%, rgba(20, 83, 45, 0.1) 100%);
        }

        .table-row {
            background-color: var(--card-bg);
            border-color: var(--border-color);
        }

        .table-row:hover {
            background-color: var(--hover-bg);
        }

        /* Ensure table cells use proper colors */
        .table-row td {
            color: var(--text-primary);
        }

        .table-header th {
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Table alignment and spacing */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        table th,
        table td {
            vertical-align: middle;
            padding: 0.75rem 1rem;
        }

        /* Better table cell alignment */
        .align-middle {
            vertical-align: middle;
        }

        /* Responsive table improvements */
        @media (max-width: 768px) {

            table th,
            table td {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 0.75rem;
            border-radius: 0.75rem;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            border: 1px solid var(--border-color);
            background-color: var(--card-bg);
            color: var(--text-primary);
        }

        .pagination a:hover {
            background-color: var(--hover-bg);
            color: var(--cpsu-green);
            border-color: var(--cpsu-green);
        }

        .pagination .active span {
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-dark) 100%);
            color: white;
            border-color: var(--cpsu-green);
        }

        .pagination .disabled span {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: var(--bg-tertiary);
        }

        /* Input Styles */
        input,
        select,
        textarea {
            background-color: var(--card-bg);
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--cpsu-green) !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.15);
        }

        .dark input:focus,
        .dark select:focus,
        .dark textarea:focus {
            box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.25);
        }

        /* Info Boxes */
        .info-box-blue {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
            color: var(--text-primary);
        }

        .dark .info-box-blue {
            background: rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .info-box-yellow {
            background: rgba(234, 179, 8, 0.1);
            border-color: rgba(234, 179, 8, 0.3);
            color: var(--text-primary);
        }

        .dark .info-box-yellow {
            background: rgba(234, 179, 8, 0.15);
            border-color: rgba(234, 179, 8, 0.4);
        }

        .info-box-blue h4,
        .info-box-blue strong {
            color: var(--text-primary);
        }

        .info-box-yellow strong {
            color: var(--text-primary);
        }

        /* Header Styles */
        .header-gradient {
            background: var(--header-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Footer Styles */
        .footer-gradient {
            background: var(--footer-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Header Button Hover */
        .header-btn {
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .header-btn:hover {
            background-color: rgba(22, 101, 52, 0.1);
            color: var(--cpsu-green);
        }

        .dark .header-btn:hover {
            background-color: rgba(22, 101, 52, 0.15);
            color: #4ade80;
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="header-gradient border-b transition-colors" style="border-color: var(--border-color);">
                <div class="px-6 py-3">
                    <div class="flex flex-wrap items-center justify-between gap-3 w-full min-w-0">
                        <div class="flex items-center space-x-4 flex-1 min-w-0">
                            <!-- Mobile Menu Button -->
                            <button @click="$store.sidebarOpen = !$store.sidebarOpen"
                                class="lg:hidden header-btn p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>

                            <!-- Page Title: on mobile wrap to 2 lines; on desktop (sm+) original truncate -->
                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                <h2 class="text-base sm:text-xl font-bold heading-font break-words line-clamp-2 sm:line-clamp-none sm:truncate"
                                    style="color: var(--cpsu-green);">@yield('page-title', 'Dashboard')</h2>
                            </div>
                        </div>

                        <!-- Global search: desktop inline -->
                        <div id="adminGlobalSearchWrap" class="hidden md:flex flex-shrink-0 items-center px-2 w-48 md:w-64">
                            <div class="relative w-full">
                                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-lg transition-colors w-full"
                                    style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                                    <svg class="w-4 h-4 text-secondary flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <input type="text" id="adminGlobalSearchInput" placeholder="search"
                                        class="bg-transparent border-none outline-none text-sm w-full min-w-0 text-primary placeholder:text-secondary"
                                        autocomplete="off" inputmode="search" enterkeyhint="search"
                                        aria-autocomplete="list" aria-label="Search"
                                        aria-controls="adminGlobalSearchPanel" aria-expanded="false">
                                    <span id="adminGlobalSearchLoading" class="text-xs text-secondary flex-shrink-0 hidden"
                                        aria-hidden="true">…</span>
                                    <button type="button" id="adminGlobalSearchClear" class="p-1 rounded hover:bg-[var(--hover-bg)] flex-shrink-0 hidden"
                                        title="Clear">
                                        <svg class="w-3 h-3 text-secondary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div id="adminGlobalSearchPanel" role="listbox" hidden
                                    class="admin-global-search-panel absolute left-0 top-full mt-1 z-[60] w-max min-w-full max-w-sm max-h-52 overflow-y-auto overflow-x-hidden rounded-lg shadow-xl border text-left"
                                    style="background: var(--card-bg); border-color: var(--border-color);">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 flex-shrink-0">
                            <!-- Mobile: open search sheet -->
                            <button type="button" id="adminGlobalSearchOpenBtn"
                                class="md:!hidden header-btn p-2 rounded-lg flex-shrink-0" title="Search"
                                aria-label="Open search">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>

                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode" class="header-btn p-2 rounded-lg" type="button"
                                title="Toggle Dark Mode">
                                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                                    </path>
                                </svg>
                                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                    </path>
                                </svg>
                            </button>


                            <!-- User Profile Dropdown -->
                            @php
                                $adminSchoolForMaintenance = auth()->user()->school_id
                                    ? \App\Models\School::find(auth()->user()->school_id)
                                    : null;
                            @endphp
                            <div class="relative" x-data="{ 
                                open: false, 
                                isMaintenance: {{ $adminSchoolForMaintenance && $adminSchoolForMaintenance->maintenance_mode ? 'true' : 'false' }}, 
                                toggleMaintenance() {
                                    this.isMaintenance = !this.isMaintenance;
                                    fetch('{{ route('admin.settings.maintenance.toggle') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ maintenance_mode: this.isMaintenance ? 1 : 0, school_id: @json(auth()->user()->school_id) })
                                    }).then(res => res.json()).then(data => {
                                        if(!data.success) { 
                                            this.isMaintenance = !this.isMaintenance; 
                                        } else {
                                            window.dispatchEvent(new CustomEvent('maintenance-toggled', { detail: { state: this.isMaintenance } }));
                                        }
                                    }).catch(() => { this.isMaintenance = !this.isMaintenance; });
                                } 
                            }">
                                <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center space-x-2 p-1.5 rounded-lg transition-colors hover:bg-[var(--hover-bg)] relative">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm relative"
                                        style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                        <span
                                            class="text-white font-semibold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                        @if (auth()->user()->is_super_admin)
                                            <div class="absolute top-0 right-0 w-2 h-2 rounded-full shadow-sm"
                                                style="background-color: #facc15; z-index: 10;"></div>
                                        @endif
                                    </div>
                                    <svg class="w-4 h-4 hidden md:block transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" style="color: var(--text-secondary);"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-56 rounded-lg shadow-xl py-2 z-50"
                                    style="background: var(--card-bg); border: 1px solid var(--border-color); display: none;">
                                    <div class="px-4 py-3 border-b" style="border-color: var(--border-color);">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-semibold truncate min-w-0"
                                                style="color: var(--text-primary);">
                                                {{ auth()->user()->name }}
                                            </p>
                                            @if (auth()->user()->is_super_admin)
                                                <span
                                                    class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-yellow-400 text-green-900 whitespace-nowrap flex-shrink-0">
                                                    Super Admin
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs truncate" style="color: var(--text-secondary);">
                                            {{ auth()->user()->email }}</p>
                                    </div>
                                    <a href="{{ route('admin.profile.edit') }}"
                                        class="flex items-center px-4 py-2 text-sm transition-colors hover:bg-[var(--hover-bg)]"
                                        style="color: var(--text-primary);">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        Profile Settings
                                    </a>

                                    @if((auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'super_admin' || auth()->user()->is_super_admin) && auth()->user()->school_id)
                                    <!-- Maintenance Mode Toggle via Ajax (school-scoped) -->
                                    <button @click.prevent="toggleMaintenance()" type="button"
                                        class="w-full flex items-center justify-between px-4 py-2 text-sm transition-colors hover:bg-[var(--hover-bg)]"
                                        style="color: var(--text-primary);">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="isMaintenance ? 'text-yellow-500' : 'text-gray-400'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            <span class="whitespace-nowrap" x-text="isMaintenance ? 'Disable Maintenance' : 'Enable Maintenance'"></span>
                                        </div>
                                        <!-- Toggle Switch UI -->
                                        <div class="relative inline-flex items-center cursor-pointer ml-2">
                                            <div class="w-8 h-4 rounded-full shadow-inner transition-colors duration-200" :class="isMaintenance ? 'bg-yellow-400' : 'bg-gray-300 dark:bg-gray-600'"></div>
                                            <div class="absolute left-0 w-4 h-4 rounded-full shadow transform transition-transform duration-200 bg-white" style="border: 1px solid var(--border-color);" :class="isMaintenance ? 'translate-x-4 border-yellow-400' : 'translate-x-0'"></div>
                                        </div>
                                    </button>
                                    @endif
                                    
                                    <div class="border-t my-1" style="border-color: var(--border-color);"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center px-4 py-2 text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/20"
                                            style="color: #dc2626;">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Mobile search: dimmed overlay on current page (not a separate full-screen white page) -->
            <div id="adminGlobalSearchMobileSheet" class="fixed inset-0 z-[110] md:!hidden hidden" role="dialog"
                aria-modal="true" aria-label="Search" aria-hidden="true">
                <button type="button" id="adminGlobalSearchMobileBackdrop"
                    class="absolute inset-0 z-0 block w-full h-full cursor-default border-0 p-0 bg-black/45 backdrop-blur-[2px]"
                    style="-webkit-tap-highlight-color: transparent;" aria-label="Dismiss search"></button>
                <div class="relative z-10 flex max-h-[min(90vh,560px)] w-full flex-col overflow-hidden rounded-b-2xl border-b shadow-2xl"
                    style="background: var(--header-bg, var(--card-bg)); border-color: var(--border-color);">
                    <div class="flex flex-shrink-0 items-center gap-2 border-b px-3 py-3"
                        style="border-color: var(--border-color);">
                        <button type="button" id="adminGlobalSearchMobileClose" class="header-btn flex-shrink-0 rounded-lg p-2"
                            aria-label="Close search">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="flex min-w-0 flex-1 items-center gap-2 rounded-lg px-3 py-2"
                            style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                            <svg class="h-4 w-4 flex-shrink-0 text-secondary" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="adminGlobalSearchInputMobile" placeholder="search"
                                class="min-w-0 flex-1 border-none bg-transparent text-sm text-primary outline-none placeholder:text-secondary"
                                autocomplete="off" inputmode="search" enterkeyhint="search" aria-label="Search">
                            <span id="adminGlobalSearchLoadingMobile" class="hidden flex-shrink-0 text-xs text-secondary"
                                aria-hidden="true">…</span>
                            <button type="button" id="adminGlobalSearchClearMobile"
                                class="hidden flex-shrink-0 rounded p-1 hover:bg-[var(--hover-bg)]" title="Clear">
                                <svg class="h-3 w-3 text-secondary" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="min-h-0 max-h-[min(70vh,420px)] flex flex-col overflow-y-auto px-3 pb-3 pt-2">
                        <div id="adminGlobalSearchPanelMobile" role="listbox" hidden
                            class="admin-global-search-panel w-full max-h-52 overflow-y-auto overflow-x-hidden rounded-lg border text-left shadow-xl"
                            style="background: var(--card-bg); border-color: var(--border-color);">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content – balanced spacing, landing-style background -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8"
                style="background: linear-gradient(180deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl flex items-center space-x-3 shadow-sm"
                        style="background: linear-gradient(135deg, rgba(22, 101, 52, 0.1) 0%, rgba(20, 83, 45, 0.08) 100%); border-left: 4px solid var(--cpsu-green);">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--cpsu-green);" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium text-primary">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-lg flex items-center space-x-3 shadow-sm"
                        style="background: rgba(220, 38, 38, 0.1); border-left: 4px solid #dc2626;">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: #dc2626;" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium text-primary">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="footer-gradient border-t transition-colors" style="border-color: var(--border-color);">
                <div class="px-6 py-3">
                    <div class="flex flex-col md:flex-row items-center justify-between space-y-2 md:space-y-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                                style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M18 13h-.68l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2H6l-3 3v4c0 1.1.89 2 1.99 2H19c1.1 0 2-.89 2-2v-4l-3-3zm-1-5.05l-4.95 4.95-3.54-3.54 4.95-4.95 3.54 3.54zm-4.24-5.66L6.39 8.66a.996.996 0 000 1.41l4.95 4.95c.39.39 1.02.39 1.41 0l6.36-6.36a.996.996 0 000-1.41l-4.95-4.95a.996.996 0 00-1.41 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-primary">Cloud Based Real-Time Voting System</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 text-xs text-secondary">
                            <span>&copy; {{ date('Y') }} CpsuVotewisely.com. All rights reserved.</span>
                            <span class="hidden sm:inline">|</span>
                            <span class="hidden sm:inline">NBFNTLG</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Restricted Access Modal -->
    <div id="restrictedAccessModal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 transition-all duration-300"
        style="display: none; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);" x-cloak>
        <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl p-6 text-left align-middle shadow-xl transition-all"
            style="background: var(--card-bg); border: 1px solid var(--border-color);">

            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full"
                style="background: rgba(220, 38, 38, 0.1);">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
            </div>

            <div class="mt-3 text-center sm:mt-5">
                <h3 class="text-xl font-bold leading-6 text-primary heading-font">Access Restricted</h3>
                <div class="mt-4">
                    <p class="text-sm text-secondary">
                        ONLY SUPER ADMINS CAN EDIT SYSTEM-WIDE SECTIONS.
                    </p>
                    <p class="mt-2 text-xs text-secondary opacity-75">
                        Please contact the main administrator if you believe this is an error.
                    </p>
                </div>
            </div>

            <div class="mt-6 flex justify-center">
                <button type="button" onclick="closeRestrictedModal()"
                    class="inline-flex justify-center rounded-xl border border-transparent px-8 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    Understood
                </button>
            </div>
        </div>
    </div>

    <script>
        function showRestrictedModal() {
            const modal = document.getElementById('restrictedAccessModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeRestrictedModal() {
            const modal = document.getElementById('restrictedAccessModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Auto-show if session flag exists
        @if (session('show_restricted_modal'))
            document.addEventListener('DOMContentLoaded', function() {
                showRestrictedModal();
            });
        @endif
    </script>

    <script>
        (function() {
            const input = document.getElementById('adminGlobalSearchInput');
            const panel = document.getElementById('adminGlobalSearchPanel');
            const clearBtn = document.getElementById('adminGlobalSearchClear');
            const loading = document.getElementById('adminGlobalSearchLoading');
            const wrap = document.getElementById('adminGlobalSearchWrap');
            const mobileInput = document.getElementById('adminGlobalSearchInputMobile');
            const mobilePanel = document.getElementById('adminGlobalSearchPanelMobile');
            const mobileClear = document.getElementById('adminGlobalSearchClearMobile');
            const mobileLoading = document.getElementById('adminGlobalSearchLoadingMobile');
            const mobileSheet = document.getElementById('adminGlobalSearchMobileSheet');
            const mobileOpenBtn = document.getElementById('adminGlobalSearchOpenBtn');
            const mobileCloseBtn = document.getElementById('adminGlobalSearchMobileClose');

            if (!input || !panel || !wrap) return;

            const searchUrl = @json(route('admin.global-search'));
            const HISTORY_KEY = 'adminGlobalSearchHistoryV1';
            const HISTORY_MAX = 5;
            let debounce;

            function esc(s) {
                const d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            function escAttr(s) {
                return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            }

            function getQuery() {
                return (input.value || '').trim();
            }

            function syncFromDesktop() {
                if (mobileInput) mobileInput.value = input.value;
            }

            function syncFromMobile() {
                input.value = mobileInput ? mobileInput.value : input.value;
            }

            function setPanelsHtml(html) {
                panel.innerHTML = html;
                if (mobilePanel) mobilePanel.innerHTML = html;
            }

            function setPanelsOpen(show) {
                panel.hidden = !show;
                input.setAttribute('aria-expanded', show ? 'true' : 'false');
                if (mobilePanel) mobilePanel.hidden = !show;
            }

            function setLoading(on) {
                loading.classList.toggle('hidden', !on);
                if (mobileLoading) mobileLoading.classList.toggle('hidden', !on);
            }

            function updateClearButtons() {
                const q = getQuery();
                clearBtn.classList.toggle('hidden', !q);
                if (mobileClear) mobileClear.classList.toggle('hidden', !q);
            }

            function getHistory() {
                try {
                    const raw = localStorage.getItem(HISTORY_KEY);
                    const arr = raw ? JSON.parse(raw) : [];
                    const list = Array.isArray(arr) ? arr.filter(t => typeof t === 'string' && t.trim().length) : [];
                    const trimmed = list.slice(0, HISTORY_MAX);
                    if (trimmed.length !== list.length) {
                        localStorage.setItem(HISTORY_KEY, JSON.stringify(trimmed));
                    }
                    return trimmed;
                } catch (e) {
                    return [];
                }
            }

            function saveHistory(arr) {
                localStorage.setItem(HISTORY_KEY, JSON.stringify(arr.slice(0, HISTORY_MAX)));
            }

            function addHistory(term) {
                const t = String(term).trim();
                if (t.length < 2) return;
                let arr = getHistory().filter(x => x !== t);
                arr.unshift(t);
                saveHistory(arr);
            }

            function clearHistory() {
                localStorage.removeItem(HISTORY_KEY);
            }

            function renderHistorySection() {
                const arr = getHistory();
                if (!arr.length) return '';
                let html =
                    '<div class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-secondary border-b flex justify-between items-center gap-2" style="border-color: var(--border-color);">' +
                    '<span>Recent</span>' +
                    '<button type="button" class="text-xs font-normal admin-global-search-clear-history hover:underline flex-shrink-0" style="color: var(--cpsu-green);">Clear all</button></div>';
                arr.forEach(term => {
                    html +=
                        '<button type="button" role="option" class="admin-global-search-history-item w-full text-left px-2 py-1.5 text-sm border-b hover:bg-[var(--hover-bg)] transition-colors" style="border-color: var(--border-color); color: var(--text-primary);" data-term="' +
                        escAttr(term) + '">' + esc(term) + '</button>';
                });
                return html;
            }

            function hasResults(d) {
                return (d.students?.length || 0) + (d.student_accounts?.length || 0) + (d.elections?.length || 0) > 0;
            }

            function renderApi(d) {
                const parts = [];
                if (d.students?.length) {
                    parts.push(
                        '<div class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-secondary border-b" style="border-color: var(--border-color);">Students (directory)</div>'
                    );
                    d.students.forEach(row => {
                        parts.push(
                            '<button type="button" role="option" class="admin-global-search-item w-full text-left px-2 py-2 text-sm hover:bg-[var(--hover-bg)] border-b transition-colors" style="border-color: var(--border-color); color: var(--text-primary);" data-url="' +
                            escAttr(row.url) + '"><span class="font-medium">' + esc(row.label) + '</span></button>'
                        );
                    });
                }
                if (d.student_accounts?.length) {
                    parts.push(
                        '<div class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-secondary border-b" style="border-color: var(--border-color);">Student accounts</div>'
                    );
                    d.student_accounts.forEach(row => {
                        parts.push(
                            '<button type="button" role="option" class="admin-global-search-item w-full text-left px-2 py-2 text-sm hover:bg-[var(--hover-bg)] border-b transition-colors" style="border-color: var(--border-color); color: var(--text-primary);" data-url="' +
                            escAttr(row.url) + '"><span class="font-medium">' + esc(row.label) + '</span></button>'
                        );
                    });
                }
                if (d.elections?.length) {
                    parts.push(
                        '<div class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-secondary border-b" style="border-color: var(--border-color);">Elections</div>'
                    );
                    d.elections.forEach(row => {
                        parts.push(
                            '<button type="button" role="option" class="admin-global-search-item w-full text-left px-2 py-2 text-sm hover:bg-[var(--hover-bg)] border-b last:border-b-0 transition-colors" style="border-color: var(--border-color); color: var(--text-primary);" data-url="' +
                            escAttr(row.url) + '"><span class="font-medium">' + esc(row.label) + '</span></button>'
                        );
                    });
                }
                return parts.join('');
            }

            function showHistoryOnly() {
                const h = renderHistorySection();
                setPanelsHtml(h);
                setPanelsOpen(!!h);
            }

            async function runFetch() {
                const q = getQuery();
                updateClearButtons();
                if (q.length === 0) {
                    showHistoryOnly();
                    return;
                }
                if (q.length === 1) {
                    setPanelsHtml(
                        '<div class="px-2 py-2 text-xs text-secondary">Keep typing… (min. 2 characters)</div>'
                    );
                    setPanelsOpen(true);
                    return;
                }
                setLoading(true);
                try {
                    const res = await fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    const data = await res.json();
                    let body = '';
                    if (!hasResults(data)) {
                        body = '<div class="px-2 py-2 text-xs text-secondary">No matches</div>';
                    } else {
                        body = renderApi(data);
                    }
                    setPanelsHtml(body);
                    setPanelsOpen(true);
                } catch (e) {
                    setPanelsHtml('<div class="px-2 py-2 text-xs text-secondary">Search failed</div>');
                    setPanelsOpen(true);
                } finally {
                    setLoading(false);
                }
            }

            function handleQueryChange() {
                syncFromDesktop();
                const q = getQuery();
                updateClearButtons();
                clearTimeout(debounce);

                if (q.length === 0) {
                    showHistoryOnly();
                    return;
                }
                if (q.length === 1) {
                    setPanelsHtml(
                        '<div class="px-2 py-2 text-xs text-secondary">Keep typing… (min. 2 characters)</div>'
                    );
                    setPanelsOpen(true);
                    return;
                }
                debounce = setTimeout(runFetch, 300);
            }

            function handleMobileQueryChange() {
                syncFromMobile();
                handleQueryChange();
            }

            function refreshPanelAfterHistoryEdit() {
                const q = getQuery();
                if (q.length === 0) showHistoryOnly();
                else if (q.length === 1) {
                    setPanelsHtml(
                        '<div class="px-2 py-2 text-xs text-secondary">Keep typing… (min. 2 characters)</div>'
                    );
                    setPanelsOpen(true);
                } else runFetch();
            }

            function panelClickHandler(e) {
                if (e.target.closest('.admin-global-search-clear-history')) {
                    e.preventDefault();
                    e.stopPropagation();
                    clearHistory();
                    refreshPanelAfterHistoryEdit();
                    return;
                }
                const histBtn = e.target.closest('.admin-global-search-history-item');
                if (histBtn && histBtn.dataset.term) {
                    e.preventDefault();
                    const term = histBtn.dataset.term;
                    input.value = term;
                    if (mobileInput) mobileInput.value = term;
                    syncFromDesktop();
                    updateClearButtons();
                    clearTimeout(debounce);
                    if (term.trim().length >= 2) {
                        addHistory(term.trim());
                    }
                    runFetch();
                    return;
                }
                const btn = e.target.closest('.admin-global-search-item');
                if (btn && btn.dataset.url) {
                    const q = getQuery();
                    if (q.length >= 2) addHistory(q);
                    window.location.href = btn.dataset.url;
                }
            }

            input.addEventListener('input', handleQueryChange);
            input.addEventListener('focus', function() {
                const q = getQuery();
                if (q.length === 0) showHistoryOnly();
                else if (q.length === 1) {
                    setPanelsHtml(
                        '<div class="px-2 py-2 text-xs text-secondary">Keep typing… (min. 2 characters)</div>'
                    );
                    setPanelsOpen(true);
                } else runFetch();
            });

            if (mobileInput) {
                mobileInput.addEventListener('input', handleMobileQueryChange);
                mobileInput.addEventListener('focus', function() {
                    syncFromMobile();
                    const q = getQuery();
                    if (q.length === 0) showHistoryOnly();
                    else if (q.length === 1) {
                        setPanelsHtml(
                            '<div class="px-2 py-2 text-xs text-secondary">Keep typing… (min. 2 characters)</div>'
                        );
                        setPanelsOpen(true);
                    } else runFetch();
                });
            }

            clearBtn.addEventListener('click', function() {
                input.value = '';
                syncFromDesktop();
                clearBtn.classList.add('hidden');
                if (mobileClear) mobileClear.classList.add('hidden');
                showHistoryOnly();
            });

            if (mobileClear) {
                mobileClear.addEventListener('click', function() {
                    input.value = '';
                    mobileInput.value = '';
                    mobileClear.classList.add('hidden');
                    clearBtn.classList.add('hidden');
                    showHistoryOnly();
                });
            }

            panel.addEventListener('click', panelClickHandler);
            if (mobilePanel) mobilePanel.addEventListener('click', panelClickHandler);

            function isMobileSheetVisiblyOpen() {
                if (!mobileSheet || mobileSheet.classList.contains('hidden')) return false;
                return window.getComputedStyle(mobileSheet).display !== 'none';
            }

            document.addEventListener('click', function(e) {
                if (isMobileSheetVisiblyOpen()) {
                    return;
                }
                if (!wrap.contains(e.target)) {
                    panel.hidden = true;
                    input.setAttribute('aria-expanded', 'false');
                }
            });

            function openMobileSearch() {
                if (!mobileSheet || !mobileInput) return;
                if (!window.matchMedia('(max-width: 767px)').matches) return;
                mobileInput.value = input.value;
                mobileSheet.classList.remove('hidden');
                mobileSheet.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                setTimeout(function() {
                    mobileInput.focus();
                    const q = getQuery();
                    if (q.length === 0) showHistoryOnly();
                    else if (q.length >= 2) runFetch();
                    else if (q.length === 1) {
                        setPanelsHtml(
                            '<div class="px-2 py-2 text-xs text-secondary">Keep typing… (min. 2 characters)</div>'
                        );
                        setPanelsOpen(true);
                    }
                }, 50);
            }

            function closeMobileSearch() {
                if (!mobileSheet || !mobileInput) return;
                input.value = mobileInput.value;
                mobileSheet.classList.add('hidden');
                mobileSheet.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                panel.hidden = true;
                input.setAttribute('aria-expanded', 'false');
                if (mobilePanel) mobilePanel.hidden = true;
            }

            if (mobileOpenBtn) {
                mobileOpenBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openMobileSearch();
                });
            }
            if (mobileCloseBtn) {
                mobileCloseBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileSearch();
                });
            }
            const mobileBackdrop = document.getElementById('adminGlobalSearchMobileBackdrop');
            if (mobileBackdrop) {
                mobileBackdrop.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeMobileSearch();
                });
            }

            window.addEventListener('resize', function() {
                if (!mobileSheet) return;
                if (!window.matchMedia('(min-width: 768px)').matches) return;
                if (!mobileSheet.classList.contains('hidden')) {
                    closeMobileSearch();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isMobileSheetVisiblyOpen()) {
                    closeMobileSearch();
                }
            });
        })();
    </script>

    @stack('scripts')

</body>

</html>
