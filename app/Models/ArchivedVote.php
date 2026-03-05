<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedVote extends Model
{
    use BelongsToOrganization, BelongsToSchool, HasFactory;

    protected $fillable = [
        'original_vote_id',
        'archived_election_id',
        'archived_candidate_id',
        'voter_id',
        'school_id',
        'organization_id',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function archivedElection()
    {
        return $this->belongsTo(ArchivedElection::class);
    }

    public function archivedCandidate()
    {
        return $this->belongsTo(ArchivedCandidate::class);
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }
}
