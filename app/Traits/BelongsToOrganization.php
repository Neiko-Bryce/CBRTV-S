<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    /**
     * Flag to prevent infinite recursion during Auth::user() calls.
     */
    protected static $isScoping = false;

    /**
     * Boot the trait to apply the Global Scope.
     */
    protected static function bootBelongsToOrganization()
    {
        // Apply Global Scope to filter by organization_id
        static::addGlobalScope('organization', function (Builder $builder) {
            // Skip scope if already scoping or in console
            if (static::$isScoping || app()->runningInConsole()) {
                return;
            }

            static::$isScoping = true;

            try {
                if (Auth::check()) {
                    $user = Auth::user();
                    if (! $user) {
                        return;
                    }

                    // All Admins bypass the organization filter to see everything in their school
                    if ($user->is_super_admin || $user->usertype === 'admin') {
                        return;
                    }

                    // Regular admins/students only see data from their own organization
                    if ($user->organization_id) {
                        $builder->where($builder->getQuery()->from.'.organization_id', $user->organization_id);
                    }
                    // If user has no organization_id, we don't apply a filter here.
                    // This allows school-level admins to see all organizations' data,
                    // while BelongsToSchool still restricts them to their school.
                }
            } finally {
                static::$isScoping = false;
            }
        });

        // Automatically set organization_id when creating a new record
        static::creating(function ($model) {
            if (Auth::check() && ! $model->organization_id) {
                $model->organization_id = Auth::user()->organization_id;
            }
        });
    }

    /**
     * Get the organization associated with the model.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
