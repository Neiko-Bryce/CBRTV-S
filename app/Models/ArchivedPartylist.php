<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedPartylist extends Model
{
    use BelongsToOrganization, BelongsToSchool, HasFactory;

    protected $fillable = [
        'original_partylist_id',
        'archived_election_id',
        'school_id',
        'organization_id',
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

    public function archivedElection()
    {
        return $this->belongsTo(ArchivedElection::class);
    }

    public function archivedCandidates()
    {
        return $this->hasMany(ArchivedCandidate::class);
    }
}
