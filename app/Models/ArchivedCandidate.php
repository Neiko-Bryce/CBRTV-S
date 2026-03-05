<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedCandidate extends Model
{
    use BelongsToOrganization, BelongsToSchool, HasFactory;

    protected $fillable = [
        'original_candidate_id',
        'archived_election_id',
        'archived_partylist_id',
        'original_position_id',
        'student_id',
        'school_id',
        'organization_id',
        'position_name',
        'position_order',
        'number_of_slots',
        'candidate_name',
        'photo',
        'biography',
        'platform',
        'votes_count',
        'is_active',
    ];

    protected $casts = [
        'position_order' => 'integer',
        'number_of_slots' => 'integer',
        'votes_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function archivedElection()
    {
        return $this->belongsTo(ArchivedElection::class);
    }

    public function archivedPartylist()
    {
        return $this->belongsTo(ArchivedPartylist::class);
    }

    public function archivedVotes()
    {
        return $this->hasMany(ArchivedVote::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
