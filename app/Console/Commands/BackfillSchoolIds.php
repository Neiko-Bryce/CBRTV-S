<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillSchoolIds extends Command
{
    protected $signature = 'app:backfill-school-ids {--dry-run : Show what would change without writing}';

    protected $description = 'Backfill missing school_id and organization_id across all tables so scoping works correctly';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $prefix = $dryRun ? '[DRY-RUN] ' : '';
        $this->info("{$prefix}Starting school/org backfill...");

        $fixed = 0;

        // 1) Organizations without school_id → assign from the first school that exists
        $orgsWithout = DB::table('organizations')->whereNull('school_id')->count();
        if ($orgsWithout > 0) {
            $defaultSchool = DB::table('schools')->orderBy('id')->first();
            if ($defaultSchool) {
                $this->warn("{$prefix}Fixing {$orgsWithout} organization(s) with NULL school_id → school #{$defaultSchool->id} ({$defaultSchool->name})");
                if (! $dryRun) {
                    DB::table('organizations')->whereNull('school_id')->update(['school_id' => $defaultSchool->id]);
                }
                $fixed += $orgsWithout;
            }
        }

        // 2) Elections: set school_id from their organization
        $elections = DB::table('elections')
            ->whereNull('school_id')
            ->whereNotNull('organization_id')
            ->get(['id', 'organization_id']);

        foreach ($elections as $e) {
            $org = DB::table('organizations')->where('id', $e->organization_id)->first(['school_id']);
            if ($org && $org->school_id) {
                $this->line("{$prefix}Election #{$e->id}: school_id → {$org->school_id}");
                if (! $dryRun) {
                    DB::table('elections')->where('id', $e->id)->update(['school_id' => $org->school_id]);
                }
                $fixed++;
            }
        }

        // 3) Candidates: set school_id from their election's organization
        $candidates = DB::table('candidates as c')
            ->whereNull('c.school_id')
            ->join('elections as el', 'el.id', '=', 'c.election_id')
            ->join('organizations as o', 'o.id', '=', 'el.organization_id')
            ->whereNotNull('o.school_id')
            ->select('c.id', 'o.school_id')
            ->get();

        if ($candidates->count() > 0) {
            $this->warn("{$prefix}Fixing {$candidates->count()} candidate(s) with NULL school_id");
            foreach ($candidates as $c) {
                if (! $dryRun) {
                    DB::table('candidates')->where('id', $c->id)->update(['school_id' => $c->school_id]);
                }
            }
            $fixed += $candidates->count();
        }

        // 4) Positions: set school_id from their organization
        $positions = DB::table('positions')
            ->whereNull('school_id')
            ->whereNotNull('organization_id')
            ->get(['id', 'organization_id']);

        foreach ($positions as $p) {
            $org = DB::table('organizations')->where('id', $p->organization_id)->first(['school_id']);
            if ($org && $org->school_id) {
                if (! $dryRun) {
                    DB::table('positions')->where('id', $p->id)->update(['school_id' => $org->school_id]);
                }
                $fixed++;
            }
        }

        // 5) Partylists: set school_id from their election
        $partylists = DB::table('partylists as pl')
            ->whereNull('pl.school_id')
            ->join('elections as el', 'el.id', '=', 'pl.election_id')
            ->whereNotNull('el.school_id')
            ->select('pl.id', 'el.school_id')
            ->get();

        if ($partylists->count() > 0) {
            $this->warn("{$prefix}Fixing {$partylists->count()} partylist(s) with NULL school_id");
            foreach ($partylists as $pl) {
                if (! $dryRun) {
                    DB::table('partylists')->where('id', $pl->id)->update(['school_id' => $pl->school_id]);
                }
            }
            $fixed += $partylists->count();
        }

        // 6) Students: set school_id/organization_id from their org if missing
        $studentsNoSchool = DB::table('students')
            ->whereNull('school_id')
            ->whereNotNull('organization_id')
            ->get(['id', 'organization_id']);

        foreach ($studentsNoSchool as $s) {
            $org = DB::table('organizations')->where('id', $s->organization_id)->first(['school_id']);
            if ($org && $org->school_id) {
                if (! $dryRun) {
                    DB::table('students')->where('id', $s->id)->update(['school_id' => $org->school_id]);
                }
                $fixed++;
            }
        }

        // 7) Users (student accounts): sync school_id/organization_id from their student record
        $studentUsers = DB::table('users')
            ->where('usertype', 'student')
            ->get(['id', 'email', 'school_id', 'organization_id']);

        foreach ($studentUsers as $u) {
            $student = DB::table('students')->where('student_id_number', $u->email)->first(['school_id', 'organization_id']);
            if (! $student) {
                continue;
            }

            $updates = [];
            if (! $u->school_id && $student->school_id) {
                $updates['school_id'] = $student->school_id;
            }
            if (! $u->organization_id && $student->organization_id) {
                $updates['organization_id'] = $student->organization_id;
            }
            // Also fix mismatches
            if ($student->school_id && $u->school_id && $u->school_id !== $student->school_id) {
                $updates['school_id'] = $student->school_id;
                $this->warn("{$prefix}User #{$u->id} ({$u->email}): school_id mismatch {$u->school_id} → {$student->school_id}");
            }
            if ($student->organization_id && $u->organization_id && $u->organization_id !== $student->organization_id) {
                $updates['organization_id'] = $student->organization_id;
                $this->warn("{$prefix}User #{$u->id} ({$u->email}): org_id mismatch {$u->organization_id} → {$student->organization_id}");
            }

            if (! empty($updates)) {
                $this->line("{$prefix}User #{$u->id} ({$u->email}): ".json_encode($updates));
                if (! $dryRun) {
                    DB::table('users')->where('id', $u->id)->update($updates);
                }
                $fixed++;
            }
        }

        // 8) Votes: set school_id/organization_id from their election
        $votesNoSchool = DB::table('votes as v')
            ->whereNull('v.school_id')
            ->join('elections as el', 'el.id', '=', 'v.election_id')
            ->whereNotNull('el.school_id')
            ->select('v.id', 'el.school_id', 'el.organization_id')
            ->get();

        if ($votesNoSchool->count() > 0) {
            $this->warn("{$prefix}Fixing {$votesNoSchool->count()} vote(s) with NULL school_id");
            foreach ($votesNoSchool as $v) {
                $updates = ['school_id' => $v->school_id];
                if ($v->organization_id) {
                    $updates['organization_id'] = $v->organization_id;
                }
                if (! $dryRun) {
                    DB::table('votes')->where('id', $v->id)->update($updates);
                }
            }
            $fixed += $votesNoSchool->count();
        }

        $this->info("{$prefix}Done! Fixed {$fixed} record(s).");
        Log::info("BackfillSchoolIds: {$prefix}Fixed {$fixed} record(s).");

        return Command::SUCCESS;
    }
}
