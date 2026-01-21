<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'election_id',
        'position_id',
        'partylist_id',
        'student_id',
        'candidate_name',
        'photo',
        'biography',
        'platform',
        'votes_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'votes_count' => 'integer',
    ];

    /**
     * Get the election that owns this candidate.
     */
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Get the position for this candidate.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the partylist for this candidate.
     */
    public function partylist()
    {
        return $this->belongsTo(Partylist::class);
    }

    /**
     * Get the student linked to this candidate.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the votes for this candidate.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
