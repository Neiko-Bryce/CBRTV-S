@extends('admin.layouts.master')

@section('title', 'Landing Page Management')
@section('page-title', 'Landing Page Settings')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header -->
        <div class="card rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-primary">Landing Page Settings</h3>
                    <p class="text-xs sm:text-sm text-secondary mt-1">Customize your landing page content</p>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('admin.landing-page.reset') }}"
                        class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors whitespace-nowrap flex-shrink-0"
                        style="border-color: var(--border-color); color: #dc2626;"
                        onclick="return confirm('Are you sure you want to reset all settings?')">
                        Reset
                    </a>
                    <button type="submit" form="landing-page-form"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap flex-shrink-0"
                        style="background: var(--cpsu-green); color: white;">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="card rounded-xl p-4 shadow-sm"
                style="background: rgba(22, 101, 52, 0.1); border-color: var(--cpsu-green);">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" style="color: var(--cpsu-green);" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm font-medium" style="color: var(--cpsu-green);">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.landing-page.update') }}" method="POST" enctype="multipart/form-data"
            id="landing-page-form">
            @csrf

            <!-- Tabs and Content Wrapper -->
            <div class="card rounded-xl shadow-sm overflow-hidden">
                <!-- Tabs Section -->
                <div class="border-b" style="border-color: var(--border-color);">
                    <div class="overflow-x-auto sm:overflow-visible -mx-px" style="-webkit-overflow-scrolling: touch;">
                        <div class="flex flex-nowrap sm:flex-wrap">
                            <button type="button" onclick="switchTab('about')" id="tab-about"
                                class="tab-button px-4 py-3 sm:px-6 sm:py-3 text-sm transition-colors whitespace-nowrap flex-shrink-0 sm:flex-shrink border-b-2"
                                style="border-color: var(--cpsu-green); color: var(--cpsu-green);">
                                About & Team
                            </button>
                            <button type="button" onclick="switchTab('features')" id="tab-features"
                                class="tab-button px-4 py-3 sm:px-6 sm:py-3 text-sm transition-colors whitespace-nowrap flex-shrink-0 sm:flex-shrink border-b-2"
                                style="border-color: transparent; color: var(--text-secondary);">
                                Features
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Landing Page Settings Area -->
                <div class="p-4 sm:p-6">
                    <!-- About Tab Content -->
                    <div id="content-about" class="tab-content space-y-4 sm:space-y-6">
                        <!-- Basic Info -->
                        <div class="card rounded-xl p-4 sm:p-6 shadow-sm">
                            <h4 class="text-base font-semibold text-primary mb-4">Basic Information</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Section Subtitle</label>
                                    <input type="text" name="about_subtitle"
                                        value="{{ old('about_subtitle', $aboutSettings['subtitle']['value'] ?? 'About The System') }}"
                                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                        placeholder="e.g., About The System">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Main Title</label>
                                    <input type="text" name="about_title"
                                        value="{{ old('about_title', $aboutSettings['title']['value'] ?? 'Redefining Digital Democracy') }}"
                                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                        placeholder="e.g., Redefining Digital Democracy">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Description</label>
                                    <textarea name="about_description" rows="3"
                                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                        placeholder="Enter description">{{ old('about_description', $aboutSettings['description']['value'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Benefits -->
                        <div class="card rounded-xl p-4 sm:p-6 shadow-sm">
                            <h4 class="text-base font-semibold text-primary mb-4">Key Benefits</h4>
                            @php
                                $benefits = $aboutSettings['benefits']['extra'] ?? [
                                    'Instant vote counting with live result updates',
                                    'Complete audit trail for every election',
                                    'Mobile-friendly voting from any device',
                                ];
                            @endphp
                            <textarea name="about_benefits[]" rows="5"
                                class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors font-mono"
                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                placeholder="Enter benefits, one per line">{{ is_array($benefits) ? implode("\n", $benefits) : '' }}</textarea>
                            <p class="text-xs text-secondary mt-2">Enter one benefit per line</p>
                        </div>

                        <!-- Team Section -->
                        <div class="card rounded-xl p-4 sm:p-6 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                                <h4 class="text-base font-semibold text-primary">Meet The Team</h4>
                                <button type="button" id="add-team-member"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap flex-shrink-0"
                                    style="background: var(--cpsu-green); color: white;">
                                    Add Member
                                </button>
                            </div>

                            <div class="space-y-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Section Title</label>
                                    <input type="text" name="team_section_title"
                                        value="{{ old('team_section_title', $aboutSettings['team_section_title']['value'] ?? 'Meet The Team') }}"
                                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                        placeholder="e.g., Meet The Team">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Section Subtitle</label>
                                    <input type="text" name="team_section_subtitle"
                                        value="{{ old('team_section_subtitle', $aboutSettings['team_section_subtitle']['value'] ?? 'The dedicated team behind this voting system') }}"
                                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                        placeholder="Brief description">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="team-members-container">
                                @php
                                    $teamMembers = $aboutSettings['team_members']['extra'] ?? [];
                                @endphp
                                @forelse($teamMembers as $index => $member)
                                    <div class="team-member-item rounded-xl p-4 relative group"
                                        style="background: var(--bg-tertiary);">
                                        <button type="button"
                                            class="remove-team-member absolute top-2 right-2 p-1.5 rounded-lg transition-colors opacity-0 group-hover:opacity-100"
                                            style="background: rgba(220, 38, 38, 0.1); color: #dc2626;" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>

                                        <!-- Photo -->
                                        <div class="text-center mb-3">
                                            <div class="relative inline-block">
                                                <div class="w-20 h-20 rounded-full overflow-hidden mx-auto border-2"
                                                    style="border-color: var(--border-color);">
                                                    @if (isset($member['image']) && $member['image'])
                                                        <img src="{{ asset('storage/' . $member['image']) }}"
                                                            alt="{{ $member['name'] ?? '' }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center"
                                                            style="background: var(--bg-secondary);">
                                                            <svg class="w-8 h-8 text-secondary" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <input type="file" name="team_members[{{ $index }}][image]"
                                                    accept="image/*"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer rounded-full"
                                                    onchange="previewTeamImage(this)">
                                                <input type="hidden"
                                                    name="team_members[{{ $index }}][existing_image]"
                                                    value="{{ $member['image'] ?? '' }}">
                                            </div>
                                        </div>

                                        <!-- Fields -->
                                        <div class="space-y-2">
                                            <input type="text" name="team_members[{{ $index }}][name]"
                                                value="{{ $member['name'] ?? '' }}"
                                                class="w-full px-3 py-2 rounded-lg border text-sm font-medium text-center transition-colors"
                                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                                placeholder="Name">
                                            <input type="text" name="team_members[{{ $index }}][role]"
                                                value="{{ $member['role'] ?? '' }}"
                                                class="w-full px-3 py-2 rounded-lg border text-sm text-center transition-colors"
                                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-secondary);"
                                                placeholder="Role">
                                            <textarea name="team_members[{{ $index }}][bio]" rows="2"
                                                class="w-full px-3 py-2 rounded-lg border text-sm text-center transition-colors"
                                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-secondary);"
                                                placeholder="Short bio">{{ $member['bio'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center py-8" id="no-team-message">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-secondary" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <p class="text-secondary">No team members yet</p>
                                        <p class="text-xs text-secondary">Click "Add Member" to add your first team member
                                        </p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Features Tab Content -->
                    <div id="content-features" class="tab-content hidden space-y-4 sm:space-y-6">
                        <div class="card rounded-xl p-4 sm:p-6 shadow-sm">
                            <h4 class="text-base font-semibold text-primary mb-4">Features Section</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Subtitle</label>
                                    <input type="text" name="features_subtitle"
                                        value="{{ old('features_subtitle', $featuresSettings['subtitle']['value'] ?? 'Core Features') }}"
                                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                        placeholder="e.g., Core Features">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-1">Main Title</label>
                                    <input type="text" name="features_title"
                                        value="{{ old('features_title', $featuresSettings['title']['value'] ?? 'Everything You Need for Fair Elections') }}"
                                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                        placeholder="e.g., Everything You Need for Fair Elections">
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-secondary mb-1">Description</label>
                                <textarea name="features_description" rows="2"
                                    class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                    style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                    placeholder="Brief description">{{ old('features_description', $featuresSettings['description']['value'] ?? '') }}</textarea>
                            </div>
                        </div>

                        <div class="card rounded-xl p-4 sm:p-6 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-base font-semibold text-primary">Feature Items</h4>
                                <button type="button" id="add-feature"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                    style="background: var(--cpsu-green); color: white;">
                                    Add Feature
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="features-items-container">
                                @php
                                    $featuresItems = $featuresSettings['items']['extra'] ?? [
                                        [
                                            'title' => 'Real-Time Vote Tallying',
                                            'description' => 'Watch results update live as votes are cast.',
                                        ],
                                        [
                                            'title' => 'Secure Cloud Infrastructure',
                                            'description' => 'Built on enterprise-grade cloud servers.',
                                        ],
                                        [
                                            'title' => 'Audit Logs & Reports',
                                            'description' => 'Comprehensive audit trails for every action.',
                                        ],
                                        [
                                            'title' => 'Multi-Device Access',
                                            'description' => 'Vote from any device - desktop, tablet, or mobile.',
                                        ],
                                    ];
                                @endphp
                                @foreach ($featuresItems as $index => $feature)
                                    <div class="feature-item rounded-xl p-4 relative"
                                        style="background: var(--bg-tertiary);">
                                        <button type="button"
                                            class="remove-feature absolute top-2 right-2 p-1.5 rounded-lg transition-colors"
                                            style="background: rgba(220, 38, 38, 0.1); color: #dc2626;" title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <div class="space-y-2 pr-8">
                                            <input type="text" name="features_items[{{ $index }}][title]"
                                                value="{{ $feature['title'] ?? '' }}"
                                                class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                                                placeholder="Feature title">
                                            <textarea name="features_items[{{ $index }}][description]" rows="2"
                                                class="w-full px-3 py-2 rounded-lg border text-sm transition-colors"
                                                style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-secondary);"
                                                placeholder="Feature description">{{ $feature['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Tab switching
            function switchTab(tab) {
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.tab-button').forEach(el => {
                    el.style.borderColor = 'transparent';
                    el.style.color = 'var(--text-secondary)';
                });

                document.getElementById('content-' + tab).classList.remove('hidden');
                const button = document.getElementById('tab-' + tab);
                button.style.borderColor = 'var(--cpsu-green)';
                button.style.color = 'var(--cpsu-green)';
            }

            // Team member counter
            let teamMemberCount = {{ count($teamMembers) }};

            // Add team member
            document.getElementById('add-team-member').addEventListener('click', function() {
                const container = document.getElementById('team-members-container');
                const noMessage = document.getElementById('no-team-message');

                if (noMessage) {
                    noMessage.remove();
                }

                const index = teamMemberCount++;
                const html = `
            <div class="team-member-item rounded-xl p-4 relative group" style="background: var(--bg-tertiary);">
                <button type="button"
                    class="remove-team-member absolute top-2 right-2 p-1.5 rounded-lg transition-colors opacity-0 group-hover:opacity-100"
                    style="background: rgba(220, 38, 38, 0.1); color: #dc2626;"
                    title="Remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="text-center mb-3">
                    <div class="relative inline-block">
                        <div class="w-20 h-20 rounded-full overflow-hidden mx-auto border-2" style="border-color: var(--border-color);">
                            <div class="w-full h-full flex items-center justify-center" style="background: var(--bg-secondary);">
                                <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <input type="file"
                            name="team_members[${index}][image]"
                            accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer rounded-full"
                            onchange="previewTeamImage(this)">
                    </div>
                </div>
                <div class="space-y-2">
                    <input type="text" name="team_members[${index}][name]"
                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium text-center transition-colors"
                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                        placeholder="Name">
                    <input type="text" name="team_members[${index}][role]"
                        class="w-full px-3 py-2 rounded-lg border text-sm text-center transition-colors"
                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-secondary);"
                        placeholder="Role">
                    <textarea name="team_members[${index}][bio]" rows="2"
                        class="w-full px-3 py-2 rounded-lg border text-sm text-center transition-colors"
                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-secondary);"
                        placeholder="Short bio"></textarea>
                </div>
            </div>
        `;
                container.insertAdjacentHTML('beforeend', html);
            });

            // Preview team image
            function previewTeamImage(input) {
                const container = input.closest('.relative');
                const div = container.querySelector('div');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        div.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Feature counter
            let featureCount = {{ count($featuresItems) }};

            // Add feature
            document.getElementById('add-feature').addEventListener('click', function() {
                const container = document.getElementById('features-items-container');
                const index = featureCount++;
                const html = `
            <div class="feature-item rounded-xl p-4 relative" style="background: var(--bg-tertiary);">
                <button type="button"
                    class="remove-feature absolute top-2 right-2 p-1.5 rounded-lg transition-colors"
                    style="background: rgba(220, 38, 38, 0.1); color: #dc2626;"
                    title="Remove">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="space-y-2 pr-8">
                    <input type="text" name="features_items[${index}][title]"
                        class="w-full px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-primary);"
                        placeholder="Feature title">
                    <textarea name="features_items[${index}][description]" rows="2"
                        class="w-full px-3 py-2 rounded-lg border text-sm transition-colors"
                        style="background: var(--card-bg); border-color: var(--border-color); color: var(--text-secondary);"
                        placeholder="Feature description"></textarea>
                </div>
            </div>
        `;
                container.insertAdjacentHTML('beforeend', html);
            });

            // Remove handlers
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-team-member')) {
                    e.target.closest('.team-member-item').remove();
                }
                if (e.target.closest('.remove-feature')) {
                    e.target.closest('.feature-item').remove();
                }
            });
        </script>
    @endpush
@endsection
