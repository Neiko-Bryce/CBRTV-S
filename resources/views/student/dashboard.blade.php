<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard - CPSU Voting System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cpsu-green: #006633;
            --cpsu-gold: #FFD700;
        }
        .heading-font {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-700 to-green-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-900 heading-font">CPSU Voting System</h1>
                            <p class="text-xs text-gray-500">Student Portal</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-700 font-medium">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 heading-font">Student Dashboard</h2>
                <p class="text-gray-600 mt-2">View and participate in active elections</p>
            </div>

            <!-- Active Elections -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Active Elections</h3>
                <div class="space-y-4">
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">Student Council Election 2024-2025</h4>
                                <p class="text-sm text-gray-600 mt-1">Ends on: January 20, 2024</p>
                            </div>
                            <a href="#" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-medium">
                                Vote Now
                            </a>
                        </div>
                    </div>
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">Faculty Senate Election 2024</h4>
                                <p class="text-sm text-gray-600 mt-1">Ends on: January 18, 2024</p>
                            </div>
                            <a href="#" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all font-medium">
                                Vote Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Votes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">My Voting History</h3>
                <p class="text-gray-600">You haven't participated in any elections yet.</p>
            </div>
        </main>
    </div>
</body>
</html>
