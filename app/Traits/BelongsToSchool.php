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
                if (Auth::check()) {
                    $user = Auth::user();
                    
                    // Super Admins see everything
                    if ($user->is_super_admin) {
                        return;
                    }

                    // Regular admins only see data from their own school
                    if ($user->school_id) {
                        $builder->where($builder->getQuery()->from.'.school_id', $user->school_id);
                    } else {
                        // If user has no school_id, they shouldn't see anything school-specific
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
