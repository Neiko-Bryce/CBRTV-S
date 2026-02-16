<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToSchool;

class Organization extends Model
{
    use BelongsToSchool;

    protected $fillable = [
        'name',
        'code',
        'slug',
        'logo_path',
        'description',
        'school_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = \Illuminate\Support\Str::slug($organization->name);
            }
        });

        static::updating(function ($organization) {
            if ($organization->isDirty('name') && ! $organization->isDirty('slug')) {
                $organization->slug = \Illuminate\Support\Str::slug($organization->name);
            }
        });
    }

    /**
     * Get the school that owns the organization.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the positions for this organization.
     */
    public function positions()
    {
        return $this->hasMany(Position::class)->where('is_active', true)->orderBy('order');
    }

    /**
     * Get all positions (including inactive).
     */
    public function allPositions()
    {
        return $this->hasMany(Position::class)->orderBy('order');
    }

    /**
     * Get the elections for this organization.
     */
    public function elections()
    {
        return $this->hasMany(Election::class);
    }
}
