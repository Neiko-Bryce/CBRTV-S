<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $fixed = 0;

        // 1) Organizations without school_id → first school
        $orgsWithout = DB::table('organizations')->whereNull('school_id')->count();
        if ($orgsWithout > 0) {
            $defaultSchool = DB::table('schools')->orderBy('id')->first();
            if ($defaultSchool) {
                DB::table('organizations')->whereNull('school_id')->update(['school_id' => $defaultSchool->id]);
                $fixed += $orgsWithout;
            }
        }

        // 2) Elections: school_id from their organization
        $elections = DB::table('elections')
            ->whereNull('school_id')
            ->whereNotNull('organization_id')
            ->get(['id', 'organization_id']);

        foreach ($elections as $e) {
            $org = DB::table('organizations')->where('id', $e->organization_id)->first(['school_id']);
            if ($org && $org->school_id) {
                DB::table('elections')->where('id', $e->id)->update(['school_id' => $org->school_id]);
                $fixed++;
            }
        }

        // 3) Candidates: school_id from election → organization
        $candidates = DB::table('candidates as c')
            ->whereNull('c.school_id')
            ->join('elections as el', 'el.id', '=', 'c.election_id')
            ->join('organizations as o', 'o.id', '=', 'el.organization_id')
            ->whereNotNull('o.school_id')
            ->select('c.id', 'o.school_id')
            ->get();

        foreach ($candidates as $c) {
            DB::table('candidates')->where('id', $c->id)->update(['school_id' => $c->school_id]);
        }
        $fixed += $candidates->count();

        // 4) Positions: school_id from their organization
        $positions = DB::table('positions')
            ->whereNull('school_id')
            ->whereNotNull('organization_id')
            ->get(['id', 'organization_id']);

        foreach ($positions as $p) {
            $org = DB::table('organizations')->where('id', $p->organization_id)->first(['school_id']);
            if ($org && $org->school_id) {
                DB::table('positions')->where('id', $p->id)->update(['school_id' => $org->school_id]);
                $fixed++;
            }
        }

        // 5) Partylists: school_id from their election
        $partylists = DB::table('partylists as pl')
            ->whereNull('pl.school_id')
            ->join('elections as el', 'el.id', '=', 'pl.election_id')
            ->whereNotNull('el.school_id')
            ->select('pl.id', 'el.school_id')
            ->get();

        foreach ($partylists as $pl) {
            DB::table('partylists')->where('id', $pl->id)->update(['school_id' => $pl->school_id]);
        }
        $fixed += $partylists->count();

        // 6) Students: school_id from their organization
        $students = DB::table('students')
            ->whereNull('school_id')
            ->whereNotNull('organization_id')
            ->get(['id', 'organization_id']);

        foreach ($students as $s) {
            $org = DB::table('organizations')->where('id', $s->organization_id)->first(['school_id']);
            if ($org && $org->school_id) {
                DB::table('students')->where('id', $s->id)->update(['school_id' => $org->school_id]);
                $fixed++;
            }
        }

        // 7) Users (student accounts): sync from their student record
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
            if ($student->school_id && $u->school_id && $u->school_id !== $student->school_id) {
                $updates['school_id'] = $student->school_id;
            }
            if ($student->organization_id && $u->organization_id && $u->organization_id !== $student->organization_id) {
                $updates['organization_id'] = $student->organization_id;
            }

            if (! empty($updates)) {
                DB::table('users')->where('id', $u->id)->update($updates);
                $fixed++;
            }
        }

        // 8) Votes: school_id/organization_id from their election
        $votes = DB::table('votes as v')
            ->whereNull('v.school_id')
            ->join('elections as el', 'el.id', '=', 'v.election_id')
            ->whereNotNull('el.school_id')
            ->select('v.id', 'el.school_id', 'el.organization_id')
            ->get();

        foreach ($votes as $v) {
            $updates = ['school_id' => $v->school_id];
            if ($v->organization_id) {
                $updates['organization_id'] = $v->organization_id;
            }
            DB::table('votes')->where('id', $v->id)->update($updates);
        }
        $fixed += $votes->count();

        Log::info("BackfillMissingSchoolIds migration: Fixed {$fixed} record(s).");
    }

    public function down(): void
    {
        // Data backfill — no rollback needed
    }
};
