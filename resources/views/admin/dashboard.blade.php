@extends('admin.layouts.master')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Total Users</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">1,234</p>
                    <p class="text-sm mt-1 flex items-center" style="color: var(--cpsu-green-light);">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        +12.5% from last month
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Active Elections -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Active Elections</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-green);">8</p>
                    <p class="text-sm text-secondary mt-1">3 ending soon</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Votes -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Total Votes</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">5,678</p>
                    <p class="text-sm mt-1 flex items-center" style="color: var(--cpsu-gold);">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        +8.2% from last week
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Participation Rate -->
        <div class="card rounded-xl p-6 shadow-sm hover:shadow-lg transition-all stat-card-gold">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary">Participation Rate</p>
                    <p class="text-3xl font-bold mt-2" style="color: var(--cpsu-gold-dark);">87%</p>
                    <p class="text-sm text-secondary mt-1">Above average</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-md" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green-dark);">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts and Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="card rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-primary">Recent Activity</h3>
                <a href="#" class="text-sm hover:underline transition-colors" style="color: var(--cpsu-green);">View all</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 activity-icon-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary">New election created</p>
                        <p class="text-sm text-secondary">Student Council Election 2024</p>
                        <p class="text-xs text-secondary mt-1 opacity-75">2 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 activity-icon-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-green);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary">New user registered</p>
                        <p class="text-sm text-secondary">John Doe registered as student</p>
                        <p class="text-xs text-secondary mt-1 opacity-75">5 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 activity-icon-gold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--cpsu-gold);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-primary">Voting completed</p>
                        <p class="text-sm text-secondary">Faculty Senate Election closed</p>
                        <p class="text-xs text-secondary mt-1 opacity-75">1 day ago</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-bold text-primary mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="#" class="p-4 rounded-lg text-white transition-all shadow-md hover:shadow-lg btn-cpsu-primary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span class="font-semibold">New Election</span>
                    </div>
                </a>
                
                <a href="{{ route('admin.users.create') }}" class="p-4 rounded-lg text-white transition-all shadow-md hover:shadow-lg btn-cpsu-primary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span class="font-semibold">Add User</span>
                    </div>
                </a>
                
                <a href="#" class="p-4 rounded-lg transition-all shadow-md hover:shadow-lg btn-cpsu-secondary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="font-semibold">View Reports</span>
                    </div>
                </a>
                
                <a href="#" class="p-4 rounded-lg transition-all shadow-md hover:shadow-lg btn-cpsu-secondary">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="font-semibold">Settings</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Active Elections Table -->
    <div class="card rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-primary">Active Elections</h3>
            <a href="#" class="text-sm hover:underline transition-colors" style="color: var(--cpsu-green);">View all elections</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="table-header border-b" style="border-color: var(--border-color);">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Election</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Status</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">Votes</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-secondary">End Date</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--border-color);">
                    <tr class="table-row transition-colors">
                        <td class="py-4 px-4">
                            <div class="font-medium text-primary">Student Council Election</div>
                            <div class="text-sm text-secondary">2024-2025</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                Active
                            </span>
                        </td>
                        <td class="py-4 px-4 text-primary">1,234</td>
                        <td class="py-4 px-4 text-primary">Jan 20, 2024</td>
                        <td class="py-4 px-4 text-right">
                            <a href="#" class="hover:underline text-sm font-medium transition-colors" style="color: var(--cpsu-green);">View</a>
                        </td>
                    </tr>
                    <tr class="table-row transition-colors">
                        <td class="py-4 px-4">
                            <div class="font-medium text-primary">Faculty Senate Election</div>
                            <div class="text-sm text-secondary">2024</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white" style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                                Active
                            </span>
                        </td>
                        <td class="py-4 px-4 text-primary">856</td>
                        <td class="py-4 px-4 text-primary">Jan 18, 2024</td>
                        <td class="py-4 px-4 text-right">
                            <a href="#" class="hover:underline text-sm font-medium transition-colors" style="color: var(--cpsu-green);">View</a>
                        </td>
                    </tr>
                    <tr class="table-row transition-colors">
                        <td class="py-4 px-4">
                            <div class="font-medium text-primary">Academic Council Vote</div>
                            <div class="text-sm text-secondary">Q1 2024</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);">
                                Ending Soon
                            </span>
                        </td>
                        <td class="py-4 px-4 text-primary">432</td>
                        <td class="py-4 px-4 text-primary">Jan 15, 2024</td>
                        <td class="py-4 px-4 text-right">
                            <a href="#" class="hover:underline text-sm font-medium transition-colors" style="color: var(--cpsu-green);">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
