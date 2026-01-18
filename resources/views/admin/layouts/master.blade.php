<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    userMenuOpen: false,
    sidebarOpen: false
}" x-init="if(darkMode){document.documentElement.classList.add('dark')}else{document.documentElement.classList.remove('dark')};$watch('darkMode',val=>{localStorage.setItem('darkMode',val);val?document.documentElement.classList.add('dark'):document.documentElement.classList.remove('dark')});window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change',e=>{if(!localStorage.getItem('darkMode')){darkMode=e.matches}})" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - CPSU Voting System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700" rel="stylesheet" />
    
    <!-- Alpine.js for dark mode toggle -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Initialize Alpine store for sidebar state
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebarOpen', false);
            
            // Close sidebar on window resize if it's desktop
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    Alpine.store('sidebarOpen', false);
                }
            });
        });
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --cpsu-green: #006633;
            --cpsu-gold: #D4AF37;
            --cpsu-green-light: #008844;
            --cpsu-green-dark: #004422;
            --cpsu-gold-light: #E8D08A;
            --cpsu-gold-dark: #B8941F;
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
        button, a, input, select, textarea {
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
            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        .dark .card {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.1);
        }
        
        .dark .card:hover {
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.2);
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
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.05) 0%, rgba(0, 136, 68, 0.05) 100%);
            border-color: var(--border-color);
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        
        .dark .sidebar-header {
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.12) 0%, rgba(0, 136, 68, 0.12) 100%);
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
            box-shadow: 0 2px 6px rgba(0, 102, 51, 0.1);
        }
        
        .dark .nav-link:hover {
            background-color: rgba(0, 136, 68, 0.15);
            box-shadow: 0 2px 6px rgba(0, 136, 68, 0.2);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.25);
            font-weight: 600;
            transform: translateX(0);
        }
        
        .dark .nav-link.active {
            box-shadow: 0 4px 12px rgba(0, 136, 68, 0.35);
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
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.4);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        
        .dark .nav-link.active::before {
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
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
        
        /* CPSU Branded Buttons */
        .btn-cpsu-primary {
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            color: white;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }
        
        .btn-cpsu-primary:hover {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.3);
        }
        
        .btn-cpsu-secondary {
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);
            color: var(--cpsu-green-dark);
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }
        
        .btn-cpsu-secondary:hover {
            background: linear-gradient(135deg, var(--cpsu-gold-dark) 0%, var(--cpsu-gold) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        
        /* Stat Card Gradients */
        .stat-card-primary {
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.08) 0%, rgba(0, 136, 68, 0.08) 100%);
            border-left: 4px solid var(--cpsu-green);
        }
        
        .dark .stat-card-primary {
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.2) 0%, rgba(0, 136, 68, 0.2) 100%);
        }
        
        .stat-card-gold {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.08) 0%, rgba(232, 208, 138, 0.08) 100%);
            border-left: 4px solid var(--cpsu-gold);
        }
        
        .dark .stat-card-gold {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(232, 208, 138, 0.2) 100%);
        }
        
        /* Activity Icons */
        .activity-icon-green {
            background: rgba(0, 102, 51, 0.1);
        }
        
        .dark .activity-icon-green {
            background: rgba(0, 136, 68, 0.2);
        }
        
        .activity-icon-gold {
            background: rgba(212, 175, 55, 0.1);
        }
        
        .dark .activity-icon-gold {
            background: rgba(212, 175, 55, 0.2);
        }
        
        /* Text Colors */
        .text-primary {
            color: var(--text-primary);
        }
        
        .text-secondary {
            color: var(--text-secondary);
        }
        
        /* Table Styles */
        .table-header {
            background-color: var(--bg-tertiary);
            color: var(--text-secondary);
        }
        
        .dark .table-header {
            background-color: var(--bg-tertiary);
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
            border-radius: 0.5rem;
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
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            color: white;
            border-color: var(--cpsu-green);
        }
        
        .pagination .disabled span {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: var(--bg-tertiary);
        }
        
        /* Input Styles */
        input, select, textarea {
            background-color: var(--card-bg);
            color: var(--text-primary);
            border-color: var(--border-color);
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: var(--cpsu-green) !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 102, 51, 0.1);
        }
        
        .dark input:focus, .dark select:focus, .dark textarea:focus {
            box-shadow: 0 0 0 3px rgba(0, 136, 68, 0.2);
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
        
        .info-box-blue h4, .info-box-blue strong {
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
            background-color: rgba(0, 102, 51, 0.1);
            color: var(--cpsu-green);
        }
        
        .dark .header-btn:hover {
            background-color: rgba(0, 136, 68, 0.15);
            color: var(--cpsu-green-light);
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
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 flex-1 min-w-0">
                            <!-- Mobile Menu Button -->
                            <button @click="$store.sidebarOpen = !$store.sidebarOpen" class="lg:hidden header-btn p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            
                            <!-- Page Title -->
                            <div class="flex items-center space-x-3 min-w-0">
                                <h2 class="text-xl font-bold heading-font truncate" style="color: var(--cpsu-green);">@yield('page-title', 'Dashboard')</h2>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <!-- Search -->
                            @if(request()->routeIs('admin.students.*'))
                                <form method="GET" action="{{ route('admin.students.index') }}" id="headerSearchForm" class="hidden md:flex items-center space-x-2 px-3 py-1.5 rounded-lg transition-colors" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                                    <button type="submit" class="flex-shrink-0" title="Search">
                                        <svg class="w-4 h-4 text-secondary hover:text-primary cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </button>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        id="headerSearchInput"
                                        value="{{ request('search') }}"
                                        placeholder="Search students by ID or name..." 
                                        class="bg-transparent border-none outline-none text-sm w-48 md:w-64 text-primary placeholder:text-secondary focus:w-56 md:focus:w-72 transition-all"
                                        autocomplete="off"
                                    >
                                    @if(request('search'))
                                        <button type="button" onclick="window.location.href='{{ route('admin.students.index') }}'" class="p-1 rounded hover:bg-[var(--hover-bg)] flex-shrink-0" title="Clear search">
                                            <svg class="w-3 h-3 text-secondary hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </form>
                            @else
                                <div class="hidden md:flex items-center space-x-2 px-3 py-1.5 rounded-lg transition-colors" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <input type="text" placeholder="Search..." class="bg-transparent border-none outline-none text-sm w-32 text-primary placeholder:text-secondary">
                                </div>
                            @endif
                            
                            <!-- Dark Mode Toggle -->
                            <button @click="darkMode = !darkMode" class="header-btn p-2 rounded-lg" type="button" title="Toggle Dark Mode">
                                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </button>
                            
                            <!-- Notifications -->
                            <button class="relative header-btn p-2 rounded-lg" title="Notifications">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute top-1 right-1 w-2 h-2 rounded-full animate-pulse" style="background: var(--cpsu-gold);"></span>
                            </button>
                            
                            <!-- User Profile Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 p-1.5 rounded-lg transition-colors hover:bg-[var(--hover-bg)]">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                        <span class="text-white font-semibold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    </div>
                                    <svg class="w-4 h-4 hidden md:block" style="color: var(--text-secondary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 rounded-lg shadow-xl py-2 z-50"
                                     style="background: var(--card-bg); border: 1px solid var(--border-color); display: none;">
                                    <div class="px-4 py-3 border-b" style="border-color: var(--border-color);">
                                        <p class="text-sm font-semibold" style="color: var(--text-primary);">{{ auth()->user()->name }}</p>
                                        <p class="text-xs truncate" style="color: var(--text-secondary);">{{ auth()->user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-sm transition-colors hover:bg-[var(--hover-bg)]" style="color: var(--text-primary);">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profile Settings
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-2 text-sm transition-colors hover:bg-red-50 dark:hover:bg-red-900/20" style="color: #dc2626;">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
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
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6" style="background-color: var(--bg-secondary);">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg flex items-center space-x-3 shadow-sm" style="background: linear-gradient(135deg, rgba(0, 102, 51, 0.1) 0%, rgba(0, 136, 68, 0.1) 100%); border-left: 4px solid var(--cpsu-green);">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--cpsu-green);" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-primary">{{ session('success') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 p-4 rounded-lg flex items-center space-x-3 shadow-sm" style="background: rgba(220, 38, 38, 0.1); border-left: 4px solid #dc2626;">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: #dc2626;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
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
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-primary">Cloud Based Real-Time Voting System</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 text-xs text-secondary">
                            <span>&copy; {{ date('Y') }} CPSU. All rights reserved.</span>
                            <span class="hidden sm:inline">|</span>
                            <span class="hidden sm:inline">NBFNTLG</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    @stack('scripts')
    
    @if(request()->routeIs('admin.students.*'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('headerSearchForm');
            const searchInput = document.getElementById('headerSearchInput');
            
            if (searchForm && searchInput) {
                // Handle Enter key
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchForm.submit();
                    }
                });
                
                // Auto-focus search input when on students page (optional)
                // searchInput.focus();
            }
        });
    </script>
    @endif
</body>
</html>
