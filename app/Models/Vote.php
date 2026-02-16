<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;

class Vote extends Model
{
    use BelongsToOrganization, BelongsToSchool;
    protected $fillable = [
        'school_id',
        'organization_id',
        'election_id',
        'candidate_id',
        'voter_id',
    ];

    /**
     * Get the election for this vote.
     */
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Get the candidate that received this vote.
     */
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Get the voter (user) who cast this vote.
     */
    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }
}
