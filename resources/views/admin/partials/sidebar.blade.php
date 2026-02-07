<aside
    class="sidebar flex-shrink-0 w-64 h-full flex flex-col lg:static inset-y-0 left-0 z-40 transition-transform duration-300 ease-in-out"
    :class="{
        'translate-x-0': $store.sidebarOpen && window.innerWidth < 1024,
        '-translate-x-full lg:translate-x-0': !$store.sidebarOpen || window.innerWidth >= 1024
    }">
    <!-- Overlay for mobile -->
    <div x-show="$store.sidebarOpen && window.innerWidth < 1024"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 lg:hidden z-30 transition-colors" style="background-color: rgba(0, 0, 0, 0.5);"
        @click="$store.sidebarOpen = false" x-cloak></div>

    <div class="h-full flex flex-col relative z-40 transition-colors sidebar-container"
        style="background-color: var(--card-bg); border-right: 1px solid var(--border-color);">
        <!-- Logo -->
        <div class="p-6 border-b transition-colors sidebar-header"
            style="background: linear-gradient(135deg, rgba(22, 101, 52, 0.06) 0%, rgba(20, 83, 45, 0.08) 100%); border-color: var(--border-color);">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg transition-all hover:scale-105"
                        style="background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M18 13h-.68l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2H6l-3 3v4c0 1.1.89 2 1.99 2H19c1.1 0 2-.89 2-2v-4l-3-3zm-1-5.05l-4.95 4.95-3.54-3.54 4.95-4.95 3.54 3.54zm-4.24-5.66L6.39 8.66a.996.996 0 000 1.41l4.95 4.95c.39.39 1.02.39 1.41 0l6.36-6.36a.996.996 0 000-1.41l-4.95-4.95a.996.996 0 00-1.41 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold heading-font transition-colors" style="color: var(--cpsu-green);">
                            Votewisely Admin</h1>
                        <p class="text-xs text-secondary transition-colors">Voting System</p>
                    </div>
                </div>
                <!-- Close button for mobile -->
                <button @click="$store.sidebarOpen = false"
                    class="lg:hidden p-2 rounded-lg transition-colors text-secondary hover:bg-[var(--hover-bg)]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto sidebar-nav">
            <div class="mb-4 px-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-secondary transition-colors">Main Menu</p>
            </div>

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Student Management -->
            <a href="{{ route('admin.student-management.index') }}"
                class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.student-management.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span class="font-medium">Student Management</span>
            </a>

            <!-- Elections -->
            <a href="{{ route('admin.elections.index') }}"
                class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.elections.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="font-medium">Elections</span>
            </a>

            <!-- Candidates Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.organizations.*') || request()->routeIs('admin.positions.*') || request()->routeIs('admin.partylists.*') || request()->routeIs('admin.candidates.*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open"
                    class="nav-link w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.organizations.*') || request()->routeIs('admin.positions.*') || request()->routeIs('admin.partylists.*') || request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-medium">Candidates</span>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" @click.outside="open = false"
                    class="mt-1 ml-4 space-y-1" style="display: none;">
                    <a href="{{ route('admin.candidates.index') }}"
                        class="nav-link flex items-center space-x-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ request()->routeIs('admin.candidates.*') ? 'bg-current' : 'bg-transparent border border-current' }}">
                        </div>
                        <span class="text-sm font-medium">Candidates</span>
                    </a>
                    <a href="{{ route('admin.organizations.index') }}"
                        class="nav-link flex items-center space-x-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.organizations.*') ? 'active' : '' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ request()->routeIs('admin.organizations.*') ? 'bg-current' : 'bg-transparent border border-current' }}">
                        </div>
                        <span class="text-sm font-medium">Organizations</span>
                    </a>
                    <a href="{{ route('admin.partylists.index') }}"
                        class="nav-link flex items-center space-x-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.partylists.*') ? 'active' : '' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ request()->routeIs('admin.partylists.*') ? 'bg-current' : 'bg-transparent border border-current' }}">
                        </div>
                        <span class="text-sm font-medium">Partylist</span>
                    </a>
                    <a href="{{ route('admin.positions.index') }}"
                        class="nav-link flex items-center space-x-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ request()->routeIs('admin.positions.*') ? 'bg-current' : 'bg-transparent border border-current' }}">
                        </div>
                        <span class="text-sm font-medium">Positions</span>
                    </a>
                </div>
            </div>

            <!-- Tools Section -->
            <div class="my-4 px-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-secondary transition-colors">Tools</p>
            </div>

            <!-- Live Results Viewing -->
            <a href="{{ route('admin.live-results-viewing.index') }}"
                class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.live-results-viewing.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                </svg>
                <span class="font-medium">Live Results Viewing</span>
            </a>

            <!-- Analytics -->
            <a href="{{ route('admin.analytics.index') }}"
                class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <span class="font-medium">Analytics</span>
            </a>

            <!-- Reports -->
            <a href="{{ route('admin.reports.index') }}"
                class="nav-link flex items-center space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="font-medium">Reports</span>
            </a>

            <!-- Settings Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.students.*') || request()->routeIs('admin.landing-page.*') ? 'true' : 'false' }} }" class="relative">
                <button @click="open = !open"
                    class="nav-link w-full flex items-center justify-between space-x-3 px-4 py-3 rounded-lg transition-all {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.students.*') || request()->routeIs('admin.landing-page.*') ? 'active' : '' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-medium">Settings</span>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 transition-transform" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95" @click.outside="open = false"
                    class="mt-1 ml-4 space-y-1" style="display: none;">
                    <a href="{{ route('admin.landing-page.index') }}"
                        class="nav-link flex items-center space-x-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.landing-page.*') ? 'active' : '' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ request()->routeIs('admin.landing-page.*') ? 'bg-current' : 'bg-transparent border border-current' }}">
                        </div>
                        <span class="text-sm font-medium">Landing Page</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-link flex items-center space-x-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ request()->routeIs('admin.users.*') ? 'bg-current' : 'bg-transparent border border-current' }}">
                        </div>
                        <span class="text-sm font-medium">Users</span>
                    </a>
                    <a href="{{ route('admin.students.index') }}"
                        class="nav-link flex items-center space-x-3 px-4 py-2 rounded-lg transition-all {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                        <div
                            class="w-2 h-2 rounded-full {{ request()->routeIs('admin.students.*') ? 'bg-current' : 'bg-transparent border border-current' }}">
                        </div>
                        <span class="text-sm font-medium">Students</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</aside>
