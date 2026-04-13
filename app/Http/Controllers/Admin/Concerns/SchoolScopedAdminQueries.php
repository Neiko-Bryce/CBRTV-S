<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Election;
use App\Models\User;
use App\Models\Vote;

/**
 * Shared school scoping for admin Analytics, Dashboard, and similar read paths.
 * Super admin: no filter. Campus admin: strict school_id on users/elections; votes also match legacy null via election.
 */
trait SchoolScopedAdminQueries
{
    /**
     * Campus admins: restrict to their school only. Super admins: all schools.
     * Returns null for super admin (no extra where), int for campus admin, or -1 if admin has no campus (empty stats).
     */
    protected function analyticsSchoolScopeId(): ?int
    {
        $user = auth()->user();
        if (! $user) {
            return -1;
        }
        if ($user->is_super_admin) {
            return null;
        }
        if ($user->school_id) {
            return (int) $user->school_id;
        }

        return -1;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder  $query
     */
    protected function applyAnalyticsSchoolFilter($query, string $column = 'school_id'): void
    {
        $scopeId = $this->analyticsSchoolScopeId();
        if ($scopeId === null) {
            return;
        }
        if ($scopeId < 0) {
            $query->whereRaw('0 = 1');

            return;
        }
        $query->where($column, $scopeId);
    }

    /**
     * Vote rows for campus admins: votes.school_id matches, or null school_id on votes whose election belongs to the campus.
     */
    protected function applyVoteSchoolScopeForAnalytics($query): void
    {
        $scopeId = $this->analyticsSchoolScopeId();
        if ($scopeId === null) {
            return;
        }
        if ($scopeId < 0) {
            $query->whereRaw('0 = 1');

            return;
        }

        $table = (new Vote)->getTable();

        $query->where(function ($q) use ($scopeId, $table) {
            $q->where("{$table}.school_id", $scopeId)
                ->orWhere(function ($q2) use ($scopeId, $table) {
                    $q2->whereNull("{$table}.school_id")
                        ->whereExists(function ($sub) use ($scopeId, $table) {
                            $sub->from('elections')
                                ->whereColumn('elections.id', "{$table}.election_id")
                                ->where('elections.school_id', $scopeId);
                        });
                });
        });
    }

    protected function votesForAnalytics()
    {
        return Vote::withoutGlobalScopes()->tap(fn ($q) => $this->applyVoteSchoolScopeForAnalytics($q));
    }

    protected function electionsForAnalytics()
    {
        return Election::withoutGlobalScopes()->tap(fn ($q) => $this->applyAnalyticsSchoolFilter($q));
    }

    protected function usersForAnalytics()
    {
        return User::withoutGlobalScopes()->tap(fn ($q) => $this->applyAnalyticsSchoolFilter($q));
    }
}
