<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'email',
        'slug',
        'logo_path',
        'location',
        'is_active',
        'maintenance_mode',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'maintenance_mode' => 'boolean',
    ];

    /**
     * Whether emergency maintenance (lockdown) is enabled for this school.
     */
    public static function maintenanceEnabledForId(?int $schoolId): bool
    {
        if (! $schoolId) {
            return false;
        }

        return (bool) static::query()
            ->whereKey($schoolId)
            ->where('maintenance_mode', true)
            ->exists();
    }

    /**
     * Get the schools organizations.
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Get the users (admins) associated with this school.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
