<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganization;

class Position extends Model
{
    use BelongsToOrganization;
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'number_of_slots',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'number_of_slots' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the organization that owns this position.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the candidates for this position.
     */
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
