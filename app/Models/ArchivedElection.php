<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedElection extends Model
{
    use BelongsToOrganization, BelongsToSchool, HasFactory;

    protected $fillable = [
        'original_election_id',
        'school_id',
        'organization_id',
        'archived_by',
        'election_id',
        'election_name',
        'type_of_election',
        'description',
        'venue',
        'election_date',
        'timestarted',
        'time_ended',
        'status',
        'show_live_results',
        'archived_at',
    ];

    protected $casts = [
        'election_date' => 'date',
        'show_live_results' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function archivedCandidates()
    {
        return $this->hasMany(ArchivedCandidate::class);
    }

    public function archivedVotes()
    {
        return $this->hasMany(ArchivedVote::class);
    }

    public function archivedPartylists()
    {
        return $this->hasMany(ArchivedPartylist::class);
    }

    public function archivedByUser()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
