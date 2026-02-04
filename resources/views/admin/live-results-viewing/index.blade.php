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
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-semibold text-primary">Live Results Viewing</h3>
            <p class="text-sm text-secondary mt-1 max-w-2xl">Control which elections appear on the public landing page. Use <strong>Display on landing page</strong> to show results, or <strong>Hide from landing page</strong> to remove them. The section is always visible; only the elections you enable will be listed.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-xl p-4 text-sm font-medium border" style="background-color: rgba(22, 163, 74, 0.08); color: var(--cpsu-green); border-color: rgba(22, 163, 74, 0.25);">
        {{ session('success') }}
    </div>
    @endif

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
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: rgba(22, 163, 74, 0.12); color: var(--cpsu-green);">
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
                            @if($election->show_live_results)
                            <button type="button" onclick="openHideModal({{ $election->id }}, '{{ addslashes($election->election_name) }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg border transition-colors hover:opacity-90" style="border-color: var(--border-color); color: var(--text-primary); background-color: var(--card-bg);">
                                Hide from landing page
                            </button>
                            @else
                            <button type="button" onclick="openDisplayModal({{ $election->id }}, '{{ addslashes($election->election_name) }}')" class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg text-white transition-colors btn-cpsu-primary hover:opacity-90">
                                Display on landing page
                            </button>
                            @endif
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
</script>
@endsection
