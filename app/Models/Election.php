<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Election extends Model
{
    use BelongsToOrganization, BelongsToSchool;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_id',
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
        'show_live_results',
        'voter_capacity',
        'course_filter_mode',
        'allowed_courses',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'election_date' => 'date',
        'show_live_results' => 'boolean',
        'allowed_courses' => 'array',
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

    /**
     * Distinct students who cast at least one vote in this election.
     */
    public function getDistinctVoterCount(): int
    {
        $raw = DB::table('votes')
            ->where('election_id', $this->id)
            ->selectRaw('COUNT(DISTINCT voter_id) as c')
            ->value('c');

        return (int) $raw;
    }

    /**
     * Minimum distinct voters for 50% + 1 quorum, or null if quorum not configured.
     */
    public function getQuorumRequiredVotes(): ?int
    {
        if ($this->voter_capacity === null || (int) $this->voter_capacity < 1) {
            return null;
        }

        $c = (int) $this->voter_capacity;

        return intdiv($c, 2) + 1;
    }

    /**
     * When set, voter_capacity is the maximum number of distinct students who may cast a ballot,
     * and the base for 50% + 1 quorum.
     */
    public function hasVoterCapacityLimit(): bool
    {
        return $this->voter_capacity !== null && (int) $this->voter_capacity >= 1;
    }

    public function isVoterCapacityFull(): bool
    {
        if (! $this->hasVoterCapacityLimit()) {
            return false;
        }

        return $this->getDistinctVoterCount() >= (int) $this->voter_capacity;
    }

    /**
     * Whether this user may still cast a first ballot (not counted toward capacity yet).
     */
    public function acceptsNewDistinctVoter(int $userId): bool
    {
        if (! $this->hasVoterCapacityLimit()) {
            return true;
        }
        $alreadyVoted = DB::table('votes')
            ->where('election_id', $this->id)
            ->where('voter_id', $userId)
            ->exists();

        if ($alreadyVoted) {
            return true;
        }

        return $this->getDistinctVoterCount() < (int) $this->voter_capacity;
    }

    public function isQuorumApplicable(): bool
    {
        return $this->hasVoterCapacityLimit();
    }

    public function isQuorumMet(): bool
    {
        if (! $this->isQuorumApplicable()) {
            return true;
        }
        $req = $this->getQuorumRequiredVotes();

        return $req !== null && $this->getDistinctVoterCount() >= $req;
    }

    /**
     * After the election is completed, winners may not be declared if quorum was required but not met.
     */
    public function isResultsVoidDueToQuorum(): bool
    {
        if (strtolower((string) ($this->status ?? '')) !== 'completed') {
            return false;
        }
        if (! $this->isQuorumApplicable()) {
            return false;
        }

        return ! $this->isQuorumMet();
    }

    /**
     * Whether a student's course may vote in this election (when restricted to specific courses).
     */
    public function studentCourseAllowsVoting(?string $studentCourse): bool
    {
        $mode = $this->course_filter_mode ?? 'all';
        if ($mode !== 'specific') {
            return true;
        }
        $allowed = $this->allowed_courses ?? [];
        if (! is_array($allowed) || count($allowed) === 0) {
            return true;
        }
        $norm = mb_strtolower(trim((string) $studentCourse));
        foreach ($allowed as $a) {
            if (mb_strtolower(trim((string) $a)) === $norm) {
                return true;
            }
        }

        return false;
    }
}
