<?php

namespace App\Traits;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToSchool
{
    /**
     * Flag to prevent infinite recursion.
     */
    protected static $isSchoolScoping = false;

    /**
     * Boot the trait to apply the Global Scope.
     */
    protected static function bootBelongsToSchool()
    {
        static::addGlobalScope('school', function (Builder $builder) {
            if (static::$isSchoolScoping || app()->runningInConsole()) {
                return;
            }

            static::$isSchoolScoping = true;

            try {
                $isAdminPath = request()->is('admin*');

                if ($isAdminPath && Auth::check()) {
                    $user = Auth::user();
                    
                    // Super Admins see everything in Admin Dashboard only
                    if ($user->is_super_admin) {
                        return;
                    }

                    // Regular admins only see data from their own school in Dashboard
                    if ($user->school_id) {
                        $builder->where($builder->getQuery()->from.'.school_id', $user->school_id);
                    } else {
                        $builder->whereNull($builder->getQuery()->from.'.school_id');
                    }
                } else {
                    // PUBLIC PORTAL / VOTER SIDE / API
                    
                    // EXCEPTION: Always allow global lookup for Users and Students on public routes 
                    // This is essential for Login to work across different portal links.
                    if ($model instanceof \App\Models\User || $model instanceof \App\Models\Student) {
                        return;
                    }

                    // Prioritize request('school_id') for tab-level isolation
                    $requestSchoolId = request('school_id');
                    $sessionSchoolId = session('school_id');
                    $activeSchoolId = $requestSchoolId ?: $sessionSchoolId;

                    if ($activeSchoolId) {
                        $builder->where($builder->getQuery()->from.'.school_id', $activeSchoolId);
                    } else {
                        // Global site - only show global data (no school_id)
                        $builder->whereNull($builder->getQuery()->from.'.school_id');
                    }
                }
            } finally {
                static::$isSchoolScoping = false;
            }
        });

        // Automatically set school_id when creating a new record
        static::creating(function ($model) {
            if (Auth::check() && ! $model->school_id) {
                $model->school_id = Auth::user()->school_id;
            }
        });
    }

    /**
     * Get the school associated with the model.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
