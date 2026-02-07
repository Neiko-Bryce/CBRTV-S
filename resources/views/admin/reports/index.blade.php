@extends('admin.layouts.master')

@section('title', 'Election Reports')
@section('page-title', 'Election Reports')

@section('content')
    <div class="space-y-4 sm:space-y-6" x-data="reportManager()">
        <!-- Header with Election Selector: original on desktop (sm+), stacked/full-width on mobile -->
        <div class="card rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-primary">Generate Election Report</h3>
                    <p class="text-xs sm:text-sm text-secondary mt-1">View vote tallies and participation statistics</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <select x-model="selectedElection" @change="loadReport()"
                        class="px-3 sm:px-4 py-2 rounded-lg border text-sm font-medium transition-colors w-full md:w-auto min-w-0"
                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                        <option value="">Select an Election</option>
                        @foreach ($elections as $election)
                            <option value="{{ $election->id }}">
                                {{ $election->election_name }}
                                @if ($election->election_date)
                                    ({{ $election->election_date->format('M d, Y') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="card rounded-xl p-12 shadow-sm">
            <div class="flex flex-col items-center justify-center">
                <svg class="animate-spin h-10 w-10 mb-4" style="color: var(--cpsu-green);"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-secondary">Loading report data...</p>
            </div>
        </div>

        <!-- No Election Selected -->
        <div x-show="!selectedElection && !loading" class="card rounded-xl p-12 shadow-sm">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
                    style="background: linear-gradient(135deg, rgba(22, 101, 52, 0.1) 0%, rgba(20, 83, 45, 0.08) 100%);">
                    <svg class="w-8 h-8" style="color: var(--cpsu-green);" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-primary mb-2">No Election Selected</h4>
                <p class="text-secondary">Please select an election from the dropdown above to generate a report.</p>
            </div>
        </div>

        <!-- Report Content -->
        <div x-show="reportData && !loading" x-cloak>
            <!-- Filter Tabs: desktop (sm+) = original wrap; mobile = horizontal scroll -->
            <div class="card rounded-xl shadow-sm overflow-hidden">
                <div class="border-b" style="border-color: var(--border-color);">
                    <div class="overflow-x-auto sm:overflow-visible -mx-px" style="-webkit-overflow-scrolling: touch;">
                        <div class="flex flex-nowrap sm:flex-wrap">
                            <button @click="setFilter('all')"
                                :class="filterType === 'all' ? 'border-b-2 font-semibold' : 'text-secondary'"
                                :style="filterType === 'all' ?
                                    'border-color: var(--cpsu-green); color: var(--cpsu-green);' : ''"
                                class="px-4 py-3 sm:px-6 sm:py-3 text-sm transition-colors whitespace-nowrap flex-shrink-0 sm:flex-shrink">
                                All Students
                            </button>
                            <button @click="setFilter('course')"
                                :class="filterType === 'course' ? 'border-b-2 font-semibold' : 'text-secondary'"
                                :style="filterType === 'course' ?
                                    'border-color: var(--cpsu-green); color: var(--cpsu-green);' : ''"
                                class="px-4 py-3 sm:px-6 sm:py-3 text-sm transition-colors whitespace-nowrap flex-shrink-0 sm:flex-shrink">
                                By Course
                            </button>
                            <button @click="setFilter('yearlevel')"
                                :class="filterType === 'yearlevel' ? 'border-b-2 font-semibold' : 'text-secondary'"
                                :style="filterType === 'yearlevel' ?
                                    'border-color: var(--cpsu-green); color: var(--cpsu-green);' : ''"
                                class="px-4 py-3 sm:px-6 sm:py-3 text-sm transition-colors whitespace-nowrap flex-shrink-0 sm:flex-shrink">
                                By Year Level
                            </button>
                            <button @click="setFilter('section')"
                                :class="filterType === 'section' ? 'border-b-2 font-semibold' : 'text-secondary'"
                                :style="filterType === 'section' ?
                                    'border-color: var(--cpsu-green); color: var(--cpsu-green);' : ''"
                                class="px-4 py-3 sm:px-6 sm:py-3 text-sm transition-colors whitespace-nowrap flex-shrink-0 sm:flex-shrink">
                                By Section
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Value Selector: desktop = row; mobile = stacked, full-width select -->
                <div x-show="filterType !== 'all'" class="p-3 sm:p-4 border-b"
                    style="border-color: var(--border-color); background: var(--bg-secondary);">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                        <label class="text-sm font-medium text-primary">Select <span
                                x-text="filterType === 'yearlevel' ? 'Year Level' : filterType.charAt(0).toUpperCase() + filterType.slice(1)"></span>:</label>
                        <select x-model="filterValue" @change="loadReport()"
                            class="px-3 py-2 sm:py-1.5 rounded-lg border text-sm transition-colors w-full sm:w-auto"
                            style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="">All</option>
                            <template x-for="option in getFilterOptions()" :key="option">
                                <option :value="option" x-text="option"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Summary Stats: desktop (sm+) = original 5 cols; mobile = 2 cols -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4 mt-4 sm:mt-6">
                <!-- Total Students Voted -->
                <div class="card rounded-lg p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-secondary truncate">Students Voted</p>
                            <p class="text-lg sm:text-2xl font-bold mt-0.5 sm:mt-1" style="color: var(--cpsu-green);"
                                x-text="reportData?.totalParticipants?.toLocaleString() || '0'"></p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shadow-md flex-shrink-0 sm:ml-2"
                            style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Male Voters -->
                <div class="card rounded-lg p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-secondary truncate">Male Voters</p>
                            <p class="text-lg sm:text-2xl font-bold mt-0.5 sm:mt-1" style="color: var(--cpsu-green);"
                                x-text="reportData?.maleVoters?.toLocaleString() || '0'"></p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shadow-md flex-shrink-0 sm:ml-2"
                            style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Female Voters -->
                <div class="card rounded-lg p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-secondary truncate">Female Voters</p>
                            <p class="text-lg sm:text-2xl font-bold mt-0.5 sm:mt-1" style="color: var(--cpsu-gold-dark);"
                                x-text="reportData?.femaleVoters?.toLocaleString() || '0'"></p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shadow-md flex-shrink-0 sm:ml-2"
                            style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                            <svg class="w-6 h-6" style="color: var(--cpsu-green-dark);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Eligible Students: full label on desktop, short on mobile -->
                <div class="card rounded-lg p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-secondary truncate"><span
                                    class="sm:hidden">Eligible</span><span class="hidden sm:inline">Eligible
                                    Students</span></p>
                            <p class="text-lg sm:text-2xl font-bold mt-0.5 sm:mt-1" style="color: var(--cpsu-green);"
                                x-text="reportData?.totalEligible?.toLocaleString() || '0'"></p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shadow-md flex-shrink-0 sm:ml-2"
                            style="background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Participation Rate: full label on desktop, short on mobile -->
                <div class="card rounded-lg p-3 sm:p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs sm:text-sm text-secondary truncate"><span
                                    class="sm:hidden">Turnout</span><span class="hidden sm:inline">Participation
                                    Rate</span></p>
                            <p class="text-lg sm:text-2xl font-bold mt-0.5 sm:mt-1" style="color: var(--cpsu-gold-dark);">
                                <span x-text="reportData?.participationRate || '0'"></span>%</p>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shadow-md flex-shrink-0 sm:ml-2"
                            style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%);">
                            <svg class="w-6 h-6" style="color: var(--cpsu-green-dark);" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Election Year Info: desktop = original row; mobile = stacked, full-width button -->
            <div class="card rounded-xl p-4 shadow-sm mt-4 sm:mt-6"
                style="background: linear-gradient(135deg, rgba(22, 101, 52, 0.08) 0%, rgba(20, 83, 45, 0.06) 100%); border-left: 4px solid var(--cpsu-green);">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg class="w-5 h-5 flex-shrink-0" style="color: var(--cpsu-green);" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="text-sm font-medium text-primary truncate min-w-0">
                            <span x-text="currentElection?.election_name"></span>
                            <span class="text-secondary sm:ml-2">| Election Year: <span
                                    x-text="reportData?.electionYear"></span></span>
                        </span>
                    </div>
                    <a :href="`{{ url('admin/reports') }}/${selectedElection}/print?filter_type=${filterType}&filter_value=${filterValue || ''}`"
                        target="_blank"
                        class="flex items-center justify-center gap-2 px-4 py-3 sm:py-2 rounded-lg text-sm font-medium text-white transition-all shadow-md hover:shadow-lg btn-cpsu-primary w-full sm:w-auto flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Print Report
                    </a>
                </div>
            </div>

            <!-- Vote Tally by Position -->
            <div class="card rounded-xl shadow-sm mt-4 sm:mt-6 overflow-hidden">
                <div class="p-4 sm:p-6 border-b" style="border-color: var(--border-color);">
                    <h4 class="text-base sm:text-lg font-bold text-primary">Vote Tally by Position</h4>
                    <p class="text-xs sm:text-sm text-secondary mt-1">Candidates ranked by vote count</p>
                </div>
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <template x-for="item in reportData?.resultsByPosition || []" :key="item.position_name">
                        <div class="mb-8 last:mb-0">
                            <h5
                                class="text-sm font-semibold text-secondary uppercase tracking-wider mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full" style="background: var(--cpsu-green);"></span>
                                <span x-text="item.position_name"></span>
                            </h5>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="table-header border-b" style="border-color: var(--border-color);">
                                            <th
                                                class="text-left py-3 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm font-semibold text-secondary">
                                                Rank</th>
                                            <th
                                                class="text-left py-3 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm font-semibold text-secondary">
                                                Candidate</th>
                                            <th
                                                class="text-left py-3 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm font-semibold text-secondary hidden sm:table-cell">
                                                Partylist</th>
                                            <th
                                                class="text-right py-3 sm:py-3 px-3 sm:px-4 text-xs sm:text-sm font-semibold text-secondary">
                                                Votes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y" style="border-color: var(--border-color);">
                                        <template x-for="(candidate, index) in item.candidates" :key="candidate.id">
                                            <tr class="table-row transition-colors">
                                                <td class="py-3 sm:py-4 px-3 sm:px-4">
                                                    <span x-show="index === 0"
                                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white"
                                                        style="background: linear-gradient(135deg, var(--cpsu-gold) 0%, var(--cpsu-gold-light) 100%); color: var(--cpsu-green-dark);">1</span>
                                                    <span x-show="index > 0"
                                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-medium"
                                                        style="background: var(--bg-tertiary); color: var(--text-secondary);"
                                                        x-text="index + 1"></span>
                                                </td>
                                                <td class="py-3 sm:py-4 px-3 sm:px-4">
                                                    <div class="font-medium text-primary text-sm sm:text-base"
                                                        x-text="candidate.candidate_name"></div>
                                                    <div x-show="candidate.student" class="text-xs text-secondary"
                                                        x-text="candidate.student?.course + ' - ' + candidate.student?.yearlevel">
                                                    </div>
                                                    <div class="sm:hidden text-xs text-secondary mt-0.5"
                                                        x-text="candidate.partylist?.name || 'Independent'"></div>
                                                </td>
                                                <td class="py-3 sm:py-4 px-3 sm:px-4 text-secondary hidden sm:table-cell"
                                                    x-text="candidate.partylist?.name || 'Independent'"></td>
                                                <td class="py-3 sm:py-4 px-3 sm:px-4 text-right">
                                                    <span class="font-bold" style="color: var(--cpsu-green);"
                                                        x-text="candidate.filtered_votes?.toLocaleString() || '0'"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="(reportData?.resultsByPosition || []).length === 0" class="text-center py-8">
                        <p class="text-secondary">No candidates found for this election.</p>
                    </div>
                </div>
            </div>

            <!-- Participation Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 mt-4 sm:mt-6">
                <!-- By Course -->
                <div class="card rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b" style="border-color: var(--border-color);">
                        <h4 class="text-sm font-bold text-primary">Participation by Course</h4>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <template x-for="item in reportData?.participationBreakdown?.byCourse || []"
                            :key="item.course">
                            <div class="flex items-center justify-between py-2 border-b last:border-b-0"
                                style="border-color: var(--border-color);">
                                <span class="text-sm text-primary truncate" x-text="item.course"></span>
                                <span class="text-sm font-semibold" style="color: var(--cpsu-green);"
                                    x-text="item.count"></span>
                            </div>
                        </template>
                        <div x-show="!reportData?.participationBreakdown?.byCourse?.length" class="text-center py-4">
                            <p class="text-xs text-secondary">No data available</p>
                        </div>
                    </div>
                </div>

                <!-- By Year Level -->
                <div class="card rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b" style="border-color: var(--border-color);">
                        <h4 class="text-sm font-bold text-primary">Participation by Year Level</h4>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <template x-for="item in reportData?.participationBreakdown?.byYearlevel || []"
                            :key="item.yearlevel">
                            <div class="flex items-center justify-between py-2 border-b last:border-b-0"
                                style="border-color: var(--border-color);">
                                <span class="text-sm text-primary" x-text="item.yearlevel"></span>
                                <span class="text-sm font-semibold" style="color: var(--cpsu-green);"
                                    x-text="item.count"></span>
                            </div>
                        </template>
                        <div x-show="!reportData?.participationBreakdown?.byYearlevel?.length" class="text-center py-4">
                            <p class="text-xs text-secondary">No data available</p>
                        </div>
                    </div>
                </div>

                <!-- By Section -->
                <div class="card rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b" style="border-color: var(--border-color);">
                        <h4 class="text-sm font-bold text-primary">Participation by Section</h4>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <template x-for="item in reportData?.participationBreakdown?.bySection || []"
                            :key="item.section">
                            <div class="flex items-center justify-between py-2 border-b last:border-b-0"
                                style="border-color: var(--border-color);">
                                <span class="text-sm text-primary" x-text="item.section"></span>
                                <span class="text-sm font-semibold" style="color: var(--cpsu-green);"
                                    x-text="item.count"></span>
                            </div>
                        </template>
                        <div x-show="!reportData?.participationBreakdown?.bySection?.length" class="text-center py-4">
                            <p class="text-xs text-secondary">No data available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function reportManager() {
                return {
                    selectedElection: '',
                    currentElection: null,
                    reportData: null,
                    loading: false,
                    filterType: 'all',
                    filterValue: '',
                    filters: {
                        courses: [],
                        yearlevels: [],
                        sections: []
                    },

                    setFilter(type) {
                        this.filterType = type;
                        this.filterValue = '';
                        if (type === 'all') {
                            this.loadReport();
                        }
                    },

                    getFilterOptions() {
                        switch (this.filterType) {
                            case 'course':
                                return this.filters.courses || [];
                            case 'yearlevel':
                                return this.filters.yearlevels || [];
                            case 'section':
                                return this.filters.sections || [];
                            default:
                                return [];
                        }
                    },

                    async loadReport() {
                        if (!this.selectedElection) {
                            this.reportData = null;
                            return;
                        }

                        this.loading = true;

                        try {
                            const response = await fetch('{{ route('admin.reports.generate') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    election_id: this.selectedElection,
                                    filter_type: this.filterType,
                                    filter_value: this.filterValue || null
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.currentElection = data.election;
                                this.reportData = data.data;
                                this.filters = data.filters || {
                                    courses: [],
                                    yearlevels: [],
                                    sections: []
                                };
                            } else {
                                console.error('Failed to load report:', data);
                            }
                        } catch (error) {
                            console.error('Error loading report:', error);
                        } finally {
                            this.loading = false;
                        }
                    }
                };
            }
        </script>
    @endpush
@endsection
