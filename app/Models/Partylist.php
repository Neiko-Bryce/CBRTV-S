<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganization;

class Partylist extends Model
{
    use BelongsToOrganization;
    protected $fillable = [
        'organization_id',
        'election_id',
        'name',
        'code',
        'description',
        'color',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the election that owns this partylist.
     */
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Get the candidates for this partylist.
     */
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
