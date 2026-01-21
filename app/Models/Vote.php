<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = [
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
