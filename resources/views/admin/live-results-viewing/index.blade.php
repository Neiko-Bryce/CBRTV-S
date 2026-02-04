@extends('admin.layouts.master')

@section('title', 'Live Results Viewing')
@section('page-title', 'Live Results Viewing')

@push('styles')
<style>
    .live-results-card { border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; }
    .live-results-card thead { background-color: var(--hover-bg); }
    .live-results-card th { padding: 1rem 1.25rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); border-bottom: 1px solid var(--border-color); }
    .live-results-card td { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    .live-results-card tbody tr:last-child td { border-bottom: 0; }
    .live-results-card tbody tr:hover { background-color: var(--hover-bg); }
    .modal-overlay { display: none; position: fixed; inset: 0; z-index: 50; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); align-items: center; justify-content: center; }
    .modal-overlay.active { display: flex; }
    .modal-box { width: 90%; max-width: 420px; border-radius: 1rem; border: 1px solid var(--border-color); background-color: var(--card-bg); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); padding: 1.5rem; }
    .modal-box .modal-title { font-size: 1.125rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem; }
    .modal-box .modal-text { font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5; }
    .modal-actions { display: flex; gap: 0.75rem; justify-content: flex-end; flex-wrap: wrap; }
    /* Live results panel (expandable) – visible when row is shown */
    .live-results-panel { padding: 1rem 1.25rem; min-height: 80px; border-top: 1px solid var(--border-color); background-color: var(--hover-bg); }
    .live-results-panel .position-block { margin-bottom: 1.5rem; }
    .live-results-panel .position-block:last-child { margin-bottom: 0; }
    .live-results-panel .position-title { font-size: 0.9375rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; padding-bottom: 0.35rem; border-bottom: 1px solid var(--border-color); }
    /* Vertical bar graph per position */
    .live-results-panel .chart-bars-vertical { display: flex; gap: 1rem; align-items: flex-end; justify-content: flex-start; flex-wrap: wrap; min-height: 160px; margin-top: 0.5rem; }
    .live-results-panel .chart-bar-col { flex: 1; min-width: 80px; max-width: 140px; display: flex; flex-direction: column; align-items: center; }
    .live-results-panel .chart-bar-track { width: 100%; height: 120px; min-height: 120px; background: var(--border-color); border-radius: 8px; overflow: hidden; display: flex; align-items: flex-end; }
    .live-results-panel .chart-bar-fill { width: 100%; min-height: 4px; border-radius: 8px; background: linear-gradient(180deg, var(--cpsu-green-light), var(--cpsu-green)); transition: height 0.3s ease; }
    .live-results-panel .chart-bar-label { margin-top: 0.5rem; text-align: center; }
    .live-results-panel .chart-bar-label .candidate-photo-wrap { width: 36px; height: 36px; margin: 0 auto 0.25rem; border-radius: 50%; overflow: hidden; border: 2px solid var(--border-color); background: var(--card-bg); }
    .live-results-panel .chart-bar-label .candidate-photo-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .live-results-panel .chart-bar-label .candidate-photo-wrap .no-photo { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--hover-bg); color: var(--text-secondary); font-size: 0.65rem; }
    .live-results-panel .chart-bar-label .candidate-name { font-size: 0.75rem; font-weight: 600; color: var(--text-primary); display: block; margin-bottom: 0.15rem; line-height: 1.2; }
    .live-results-panel .chart-bar-label .candidate-votes { font-size: 0.875rem; font-weight: 700; color: var(--cpsu-green); }
    .live-results-panel .chart-bar-label .candidate-pct { font-size: 0.7rem; color: var(--text-secondary); }
    .live-results-panel .panel-meta { font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.75rem; }
    .live-results-panel .panel-loading, .live-results-panel .panel-error { text-align: center; padding: 1rem; color: var(--text-secondary); font-size: 0.875rem; }
    /* Organization badge: light mode green; dark mode use primary text for clear visibility */
    .live-results-card .org-badge { background-color: rgba(22, 163, 74, 0.12); color: var(--cpsu-green); }
    .dark .live-results-card .org-badge { background-color: rgba(34, 197, 94, 0.25); color: var(--text-primary); }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Header: title only --}}
    <h3 class="text-xl font-semibold text-primary">Live Results Viewing</h3>

    @if(session('success'))
    <div class="rounded-xl p-4 text-sm font-medium border" style="background-color: rgba(22, 163, 74, 0.08); color: var(--cpsu-green); border-color: rgba(22, 163, 74, 0.25);">
        {{ session('success') }}
    </div>
    @endif

    {{-- Live results section: candidates + bar graph show here when an election is selected --}}
    <div class="live-results-card card shadow-sm live-results-panel" id="live-results-section">
        <div class="panel-loading" id="live-results-section-loading" style="display: none;">Loading live results…</div>
        <div class="panel-error" id="live-results-section-error" style="display: none;"></div>
        <div id="live-results-section-content" style="display: none;"></div>
        <div id="live-results-section-empty" class="panel-loading">Select an election below to view live results.</div>
    </div>

    {{-- Table Card --}}
    <div class="live-results-card card shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="text-left">Election</th>
                        <th class="text-left">Organization</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Landing Page</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($elections as $election)
                    <tr>
                        <td>
                            <div class="font-semibold text-primary">{{ $election->election_name }}</div>
                            <div class="text-xs text-secondary mt-0.5">{{ $election->election_id ?? '—' }}</div>
                        </td>
                        <td>
                            @if($election->organization)
                            <span class="org-badge inline-flex px-2.5 py-1 rounded-full text-xs font-medium">
                                {{ $election->organization->name }}
                            </span>
                            @else
                            <span class="text-secondary">—</span>
                            @endif
                        </td>
                        <td>
                            @php $status = strtolower($election->status ?? ''); @endphp
                            @if($status === 'ongoing')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(234, 179, 8, 0.18); color: var(--cpsu-gold-dark);">Ongoing</span>
                            @elseif($status === 'upcoming')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(22, 163, 74, 0.12); color: var(--cpsu-green);">Upcoming</span>
                            @elseif($status === 'completed')
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium text-secondary" style="background-color: var(--hover-bg);">Completed</span>
                            @else
                            <span class="text-secondary">{{ ucfirst($election->status ?? '—') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($election->show_live_results)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(22, 163, 74, 0.15); color: var(--cpsu-green);">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--cpsu-green);"></span>
                                Displayed
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-secondary" style="background-color: var(--hover-bg);">
                                Not displayed
                            </span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                <button type="button" onclick="showLiveResults({{ $election->id }})" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg border transition-colors hover:opacity-90" style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--card-bg);" title="View live candidate results">
                                    View live results
                                </button>
                                @if($election->show_live_results)
                                <button type="button" onclick="openHideModal({{ $election->id }}, '{{ addslashes($election->election_name) }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg border transition-colors hover:opacity-90" style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--card-bg);">
                                    Hide from landing page
                                </button>
                                @else
                                <button type="button" onclick="openDisplayModal({{ $election->id }}, '{{ addslashes($election->election_name) }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-white transition-colors btn-cpsu-primary hover:opacity-90">
                                    Display on landing page
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-secondary">
                            <p class="font-medium text-primary mb-1">No elections yet</p>
                            <p class="text-sm">Create elections in <a href="{{ route('admin.elections.index') }}" class="underline" style="color: var(--cpsu-green);">Elections</a>, then control their visibility here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($elections->hasPages())
        <div class="px-5 py-3 border-t flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2" style="border-color: var(--border-color); background-color: var(--hover-bg);">
            <p class="text-sm text-secondary">Showing {{ $elections->firstItem() }} to {{ $elections->lastItem() }} of {{ $elections->total() }} elections</p>
            <div class="flex gap-2">{{ $elections->links() }}</div>
        </div>
        @endif
    </div>
</div>

{{-- Modal: Display on landing page --}}
<div id="modalDisplay" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalDisplayTitle">
    <div class="modal-box">
        <h4 id="modalDisplayTitle" class="modal-title">Display on landing page</h4>
        <p class="modal-text" id="modalDisplayText">This election's results will be shown in the "Recent Election Results" section on the public landing page.</p>
        <div class="modal-actions">
            <button type="button" onclick="closeModal('modalDisplay')" class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors" style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--card-bg);">Cancel</button>
            <form id="formDisplay" method="POST" action="" class="inline-block">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white btn-cpsu-primary">Display</button>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Hide from landing page --}}
<div id="modalHide" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalHideTitle">
    <div class="modal-box">
        <h4 id="modalHideTitle" class="modal-title">Hide from landing page</h4>
        <p class="modal-text" id="modalHideText">This election will no longer appear in the "Recent Election Results" section. You can display it again anytime.</p>
        <div class="modal-actions">
            <button type="button" onclick="closeModal('modalHide')" class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors" style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--card-bg);">Cancel</button>
            <form id="formHide" method="POST" action="" class="inline-block">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white" style="background-color: #dc2626;">Hide</button>
            </form>
        </div>
    </div>
</div>

<script>
    var currentElectionId = null;
    var resultsRefreshInterval = null;

    function openDisplayModal(id, name) {
        document.getElementById('formDisplay').action = '{{ url("admin/live-results-viewing") }}/' + id + '/display';
        document.getElementById('modalDisplayText').textContent = 'Show "' + name + '" in the "Recent Election Results" section on the public landing page?';
        document.getElementById('modalDisplay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function openHideModal(id, name) {
        document.getElementById('formHide').action = '{{ url("admin/live-results-viewing") }}/' + id + '/hide';
        document.getElementById('modalHideText').textContent = 'Remove "' + name + '" from the landing page? You can display it again anytime.';
        document.getElementById('modalHide').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }
    document.getElementById('modalDisplay').addEventListener('click', function(e) { if (e.target === this) closeModal('modalDisplay'); });
    document.getElementById('modalHide').addEventListener('click', function(e) { if (e.target === this) closeModal('modalHide'); });

    function showLiveResults(electionId) {
        if (resultsRefreshInterval) {
            clearInterval(resultsRefreshInterval);
            resultsRefreshInterval = null;
        }
        currentElectionId = electionId;
        loadLiveResults(electionId);
        resultsRefreshInterval = setInterval(function() { loadLiveResults(electionId); }, 2000);
    }

    function loadLiveResults(electionId) {
        var loadingEl = document.getElementById('live-results-section-loading');
        var errorEl = document.getElementById('live-results-section-error');
        var contentEl = document.getElementById('live-results-section-content');
        var emptyEl = document.getElementById('live-results-section-empty');
        if (!loadingEl || !errorEl || !contentEl || !emptyEl) return;

        var isRefresh = contentEl.querySelector('[data-candidate-id]') !== null;
        if (!isRefresh) {
            emptyEl.style.display = 'none';
            loadingEl.style.display = 'block';
            errorEl.style.display = 'none';
            errorEl.textContent = '';
            contentEl.style.display = 'none';
            contentEl.innerHTML = '';
        }

        var url = '{{ url("admin/live-results-viewing") }}/' + electionId + '/results';
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(function(r) {
            if (!r.ok) {
                if (r.status === 401 || r.status === 403) throw new Error('Please sign in again.');
                throw new Error('Could not load results.');
            }
            var ct = r.headers.get('content-type');
            if (!ct || !ct.includes('application/json')) throw new Error('Invalid response. Try refreshing the page.');
            return r.json();
        })
        .then(function(data) {
            if (!isRefresh) {
                loadingEl.style.display = 'none';
            }
            if (!data.success) {
                errorEl.textContent = data.message || 'Failed to load results.';
                errorEl.style.display = 'block';
                contentEl.style.display = 'none';
                emptyEl.style.display = 'none';
                return;
            }
            var e = data.election;
            var existing = contentEl.querySelector('[data-candidate-id]');
            if (existing && e.positions && e.positions.length) {
                patchLiveResults(e, contentEl);
            } else {
                contentEl.innerHTML = buildLiveResultsHtml(e);
            }
            contentEl.style.display = 'block';
            errorEl.style.display = 'none';
            emptyEl.style.display = 'none';
        })
        .catch(function(err) {
            if (!isRefresh) loadingEl.style.display = 'none';
            errorEl.textContent = err && err.message ? err.message : 'Network error. Try again.';
            errorEl.style.display = 'block';
            contentEl.style.display = 'none';
            emptyEl.style.display = 'none';
        });
    }

    function buildLiveResultsHtml(e) {
        var html = '';
        if (e.positions && e.positions.length) {
            e.positions.forEach(function(pos) {
                var candidates = pos.candidates || [];
                var total = pos.total_votes || 0;
                var maxVotes = candidates.length ? Math.max.apply(null, candidates.map(function(c) { return c.votes_count || 0; })) : 0;
                html += '<div class="position-block"><div class="position-title">' + escapeHtml(pos.position_name) + '</div><div class="chart-bars-vertical">';
                candidates.forEach(function(c) {
                    var votes = c.votes_count || 0;
                    var pct = total > 0 ? ((votes / total) * 100).toFixed(1) : '0';
                    var barPct = maxVotes > 0 ? ((votes / maxVotes) * 100) : 0;
                    var photoHtml = c.photo_url
                        ? '<img src="' + escapeAttr(c.photo_url) + '" alt="" loading="lazy" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';"><span class="no-photo" style="display:none;">—</span>'
                        : '<span class="no-photo">—</span>';
                    html += '<div class="chart-bar-col" data-candidate-id="' + escapeAttr(String(c.id)) + '">';
                    html += '<div class="chart-bar-track"><span class="chart-bar-fill" style="height:' + barPct + '%"></span></div>';
                    html += '<div class="chart-bar-label"><div class="candidate-photo-wrap">' + photoHtml + '</div><span class="candidate-name">' + escapeHtml(c.name) + '</span><span class="candidate-votes">' + votes + '</span><span class="candidate-pct">' + pct + '%</span></div>';
                    html += '</div>';
                });
                html += '</div></div>';
            });
        } else {
            html = '<div class="panel-loading">No candidates in this election yet. Add candidates under <a href="{{ route("admin.elections.index") }}" style="color: var(--cpsu-green);">Elections</a> → select the election → Candidates.</div>';
        }
        html += '<div class="panel-meta">Total voters: ' + (e.total_voters || 0) + (e.positions && e.positions.length ? ' &middot; Live (updates every 2s)' : '') + '</div>';
        return html;
    }

    function patchLiveResults(e, contentEl) {
        if (!e.positions || !e.positions.length) return;
        e.positions.forEach(function(pos) {
            var candidates = pos.candidates || [];
            var total = pos.total_votes || 0;
            var maxVotes = candidates.length ? Math.max.apply(null, candidates.map(function(c) { return c.votes_count || 0; })) : 0;
            candidates.forEach(function(c) {
                var votes = c.votes_count || 0;
                var pct = total > 0 ? ((votes / total) * 100).toFixed(1) : '0';
                var barPct = maxVotes > 0 ? ((votes / maxVotes) * 100) : 0;
                var col = contentEl.querySelector('.chart-bar-col[data-candidate-id="' + escapeAttr(String(c.id)) + '"]');
                if (col) {
                    var fill = col.querySelector('.chart-bar-fill');
                    var votesEl = col.querySelector('.candidate-votes');
                    var pctEl = col.querySelector('.candidate-pct');
                    if (fill) fill.style.height = barPct + '%';
                    if (votesEl) votesEl.textContent = votes;
                    if (pctEl) pctEl.textContent = pct + '%';
                }
            });
        });
    }

    function escapeHtml(text) {
        if (text == null) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    function escapeAttr(text) {
        if (text == null) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/"/g, '&quot;');
    }
</script>
@endsection
