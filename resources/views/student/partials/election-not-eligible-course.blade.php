{{-- Expects: $election, optional $showDisabledVoteButton (bool) --}}
<div class="rounded-xl p-5 sm:p-6 bg-gradient-to-br from-slate-50 to-sky-50 border border-slate-200/90 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-start gap-3">
        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-sky-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v.01M8 21h8a2 2 0 002-2v-5.586a1 1 0 00-.293-.707l-6.414-6.414a1 1 0 00-1.414 0L4.586 13.414A1 1 0 004 14v7a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="min-w-0">
            <h4 class="text-base sm:text-lg font-semibold text-slate-900 heading-font mb-1">This election isn’t open to your program</h4>
            <p class="text-sm text-slate-700/95 leading-relaxed">
                Voting here is limited to certain courses or programs. Your current program isn’t included in that list, so you can’t cast a ballot in this election.
            </p>
            @if(! empty($election->allowed_courses) && is_array($election->allowed_courses))
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    <span class="font-medium text-slate-700">Eligible programs include:</span>
                    {{ collect($election->allowed_courses)->map(fn ($c) => trim((string) $c))->filter()->unique()->implode(', ') }}
                </p>
            @endif
        </div>
    </div>
</div>
@if(! empty($showDisabledVoteButton))
    <div class="mt-4">
        <span class="vote-btn vote-btn-disabled inline-flex" role="button" tabindex="-1" aria-disabled="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Vote Now</span>
        </span>
    </div>
@endif
