<?php

namespace App\Services;

use App\Models\ArchivedCandidate;
use App\Models\ArchivedElection;
use App\Models\ArchivedPartylist;
use App\Models\ArchivedVote;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

class ElectionArchiveService
{
    /**
     * Archive a completed/cancelled election and remove it from active tables.
     *
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function archiveElection(int $electionId, ?User $archivedBy = null): ArchivedElection
    {
        $election = Election::with(['organization', 'partylists', 'candidates.position', 'candidates.partylist'])
            ->findOrFail($electionId);

        if (! in_array($election->status, ['completed', 'cancelled'], true)) {
            throw new \DomainException('Only completed or cancelled elections can be archived.');
        }

        if (ArchivedElection::withoutGlobalScopes()->where('original_election_id', $election->id)->exists()) {
            throw new \RuntimeException('This election has already been archived.');
        }

        return DB::transaction(function () use ($election, $archivedBy) {
            $archivedElection = ArchivedElection::withoutGlobalScopes()->create([
                'original_election_id' => $election->id,
                'school_id' => $election->school_id,
                'organization_id' => $election->organization_id,
                'archived_by' => $archivedBy?->id,
                'election_id' => $election->election_id,
                'election_name' => $election->election_name,
                'type_of_election' => $election->type_of_election,
                'description' => $election->description,
                'venue' => $election->venue,
                'election_date' => $election->election_date,
                'timestarted' => $election->timestarted,
                'time_ended' => $election->time_ended,
                'status' => $election->status,
                'show_live_results' => (bool) ($election->show_live_results ?? false),
                'archived_at' => now(),
                'created_at' => $election->created_at,
                'updated_at' => $election->updated_at,
            ]);

            $partylistIdMap = [];
            foreach ($election->partylists as $partylist) {
                $archivedPartylist = ArchivedPartylist::withoutGlobalScopes()->create([
                    'original_partylist_id' => $partylist->id,
                    'archived_election_id' => $archivedElection->id,
                    'school_id' => $partylist->school_id,
                    'organization_id' => $partylist->organization_id,
                    'name' => $partylist->name,
                    'code' => $partylist->code,
                    'description' => $partylist->description,
                    'color' => $partylist->color,
                    'logo' => $partylist->logo,
                    'is_active' => (bool) $partylist->is_active,
                    'created_at' => $partylist->created_at,
                    'updated_at' => $partylist->updated_at,
                ]);

                $partylistIdMap[$partylist->id] = $archivedPartylist->id;
            }

            $candidateIdMap = [];
            $candidates = Candidate::with(['position', 'partylist'])
                ->where('election_id', $election->id)
                ->get();

            foreach ($candidates as $candidate) {
                $archivedCandidate = ArchivedCandidate::withoutGlobalScopes()->create([
                    'original_candidate_id' => $candidate->id,
                    'archived_election_id' => $archivedElection->id,
                    'archived_partylist_id' => $candidate->partylist_id ? ($partylistIdMap[$candidate->partylist_id] ?? null) : null,
                    'original_position_id' => $candidate->position_id,
                    'student_id' => $candidate->student_id,
                    'school_id' => $candidate->school_id,
                    'organization_id' => $candidate->organization_id,
                    'position_name' => $candidate->position?->name,
                    'position_order' => (int) ($candidate->position?->order ?? 0),
                    'number_of_slots' => (int) ($candidate->position?->number_of_slots ?? 1),
                    'candidate_name' => $candidate->candidate_name,
                    'photo' => $candidate->photo,
                    'biography' => $candidate->biography,
                    'platform' => $candidate->platform,
                    'votes_count' => (int) ($candidate->votes_count ?? 0),
                    'is_active' => (bool) $candidate->is_active,
                    'created_at' => $candidate->created_at,
                    'updated_at' => $candidate->updated_at,
                ]);

                $candidateIdMap[$candidate->id] = $archivedCandidate->id;
            }

            $votes = Vote::where('election_id', $election->id)->get();
            $rows = [];
            foreach ($votes as $vote) {
                $archivedCandidateId = $candidateIdMap[$vote->candidate_id] ?? null;
                if (! $archivedCandidateId) {
                    continue;
                }

                $rows[] = [
                    'original_vote_id' => $vote->id,
                    'archived_election_id' => $archivedElection->id,
                    'archived_candidate_id' => $archivedCandidateId,
                    'voter_id' => $vote->voter_id,
                    'school_id' => $vote->school_id,
                    'organization_id' => $vote->organization_id,
                    'voted_at' => $vote->created_at,
                    'created_at' => $vote->created_at,
                    'updated_at' => $vote->updated_at,
                ];
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                if (! empty($chunk)) {
                    ArchivedVote::withoutGlobalScopes()->insert($chunk);
                }
            }

            // Remove active records after all archive inserts succeed.
            // Cascades take care of candidates/partylists/votes.
            $election->delete();

            return ArchivedElection::with('organization')->findOrFail($archivedElection->id);
        });
    }
}
