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

                    // RESCUE MODE: If an admin has no school_id yet (e.g. after a reset), 
                    // allow them to see everything so they can fix/assign data.
                    if (!$user->school_id) {
                        return;
                    }

                    // Regular admins only see data from their own school OR Global records (NULL)
                    $tableName = $builder->getQuery()->from;
                    $builder->where(function ($q) use ($user, $tableName) {
                        $q->where($tableName.'.school_id', $user->school_id)
                          ->orWhereNull($tableName.'.school_id');
                    });
                } else {
                    // PUBLIC PORTAL / VOTER SIDE / API
                    
                    // EXCEPTION: Always allow global lookup for Users and Students on public routes 
                    // This is essential for Login to work across different portal links.
                    $model = $builder->getModel();
                    if ($model instanceof \App\Models\User || $model instanceof \App\Models\Student) {
                        return;
                    }

                    // Prioritize request('school_id') for tab-level isolation
                    $requestSchoolId = request('school_id');
                    $sessionSchoolId = session('school_id');
                    $activeSchoolId = $requestSchoolId ?: $sessionSchoolId;

                    if ($activeSchoolId) {
                        // If it's a slug (contains non-numeric), resolve it
                        if (!is_numeric($activeSchoolId)) {
                            $school = \App\Models\School::where('slug', $activeSchoolId)->first();
                            $activeSchoolId = $school ? $school->id : null;
                        }
                        
                        if ($activeSchoolId) {
                            $builder->where($builder->getQuery()->from.'.school_id', $activeSchoolId);
                        }
                    } else {
                        // ROOT PAGE (www.cpsuvotewisely.com)
                        // Default to showing only Main Campus data
                        $mainCampus = \App\Models\School::where('slug', 'main-campus')->first();
                        if ($mainCampus) {
                            $builder->where($builder->getQuery()->from.'.school_id', $mainCampus->id);
                        }
                    }
                }
            } finally {
                static::$isSchoolScoping = false;
            }
        });

        // Automatically set school_id when creating a new record
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // If model already has a school_id (set manually in controller), respect it
                if ($model->school_id) {
                    return;
                }

                // Normal path: Use the admin's assigned school
                if ($user->school_id) {
                    $model->school_id = $user->school_id;
                    return;
                }

                // Super Admin path (user->school_id is null): 
                // Inherit from organization or election if applicable
                if ($user->is_super_admin) {
                    if (isset($model->organization_id) && $model->organization_id) {
                        $org = \App\Models\Organization::find($model->organization_id);
                        if ($org) $model->school_id = $org->school_id;
                    } elseif (isset($model->election_id) && $model->election_id) {
                        $election = \App\Models\Election::find($model->election_id);
                        if ($election) $model->school_id = $election->school_id;
                    }
                }
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
