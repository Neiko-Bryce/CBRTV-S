<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
