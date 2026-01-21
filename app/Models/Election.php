<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'election_id',
        'election_name',
        'type_of_election',
        'organization_id',
        'description',
        'venue',
        'election_date',
        'timestarted',
        'time_ended',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'election_date' => 'date',
    ];

    /**
     * Get the organization for this election.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the partylists for this election.
     */
    public function partylists()
    {
        return $this->hasMany(Partylist::class);
    }

    /**
     * Get the candidates for this election.
     */
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * Get the votes for this election.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
