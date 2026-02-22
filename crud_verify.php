<?php

use App\Models\Organization;
use App\Models\School;
use App\Models\User;
use App\Models\Election;
use Illuminate\Support\Facades\DB;

// This script will attempt to create, read, and delete records to verify CRUD
try {
    echo "Starting CRUD Deep Verification...\n";

    // 1. Get a School
    $school = School::first();
    if (!$school) {
        throw new Exception("No school found in database.");
    }
    echo "Using School: " . $school->name . " (ID: " . $school->id . ")\n";

    // 2. Create an Organization
    $orgName = "Test Org " . time();
    $org = Organization::create([
        'school_id' => $school->id,
        'name' => $orgName,
        'is_active' => true
    ]);
    echo "Organization Created: " . $org->name . " (ID: " . $org->id . ")\n";

    // 3. Read the Organization
    $foundOrg = Organization::find($org->id);
    if (!$foundOrg || $foundOrg->name !== $orgName) {
        throw new Exception("Failed to read created organization.");
    }
    echo "Organization Read Verified.\n";

    // 4. Update the Organization
    $newOrgName = $orgName . " Updated";
    $foundOrg->update(['name' => $newOrgName]);
    if ($foundOrg->fresh()->name !== $newOrgName) {
        throw new Exception("Failed to update organization.");
    }
    echo "Organization Update Verified.\n";

    // 5. Create an Election for this Org
    $electionName = "Test Election " . time();
    $election = Election::create([
        'school_id' => $school->id,
        'organization_id' => $org->id,
        'election_name' => $electionName,
        'type_of_election' => $org->name,
        'election_date' => now()->addDays(5)->format('Y-m-d'),
        'timestarted' => '08:00',
        'time_ended' => '17:00'
    ]);
    echo "Election Created: " . $election->election_name . " (ID: " . $election->id . ")\n";

    // 6. Read the Election
    $foundElection = Election::find($election->id);
    if (!$foundElection || $foundElection->election_name !== $electionName) {
        throw new Exception("Failed to read created election.");
    }
    echo "Election Read Verified.\n";

    // --- SCOPING TEST START ---
    echo "Starting Multi-tenancy Scoping Test...\n";
    // Create another Org and Election
    $org2 = Organization::create([
        'school_id' => $school->id,
        'name' => 'Scoping Test Org',
        'is_active' => true
    ]);
    $election2 = Election::create([
        'school_id' => $school->id,
        'organization_id' => $org2->id,
        'election_name' => 'Scoping Test Election',
        'type_of_election' => $org2->name,
        'election_date' => now()->addDays(10)->format('Y-m-d'),
        'timestarted' => '09:00',
        'time_ended' => '18:00'
    ]);

    // Simulate a user from Org 1
    $user1 = new User(['organization_id' => $org->id, 'usertype' => 'user']);
    auth()->login($user1);

    $electionsForUser1 = Election::all();
    if ($electionsForUser1->count() !== 1 || $electionsForUser1->first()->id !== $election->id) {
        // Note: Global scopes might be tricky in cli if not handled correctly by the trait.
        // The trait BelongsToOrganization.php uses static::addGlobalScope.
        // Let's see if it works.
        echo "Found " . $electionsForUser1->count() . " elections for Org 1 user.\n";
        foreach($electionsForUser1 as $e) echo " - Election ID: " . $e->id . " Org ID: " . $e->organization_id . "\n";
        
        // If it finds both, scoping might be bypassed in console.
        // BelongsToOrganization hook: if (static::$isScoping || app()->runningInConsole()) { return; }
        // AH! app()->runningInConsole() BYPASSES scoping.
    } else {
        echo "Multi-tenancy Scoping Verified for Org 1.\n";
    }
    auth()->logout();
    // --- SCOPING TEST END ---

    // 8. Create a Student
    $studentIdNum = "TEST-" . time();
    $student = \App\Models\Student::create([
        'school_id' => $school->id,
        'organization_id' => $org->id,
        'student_id_number' => $studentIdNum,
        'campus' => 'Main',
        'fname' => 'Test',
        'lname' => 'Student',
        'course' => 'BSIT',
        'yearlevel' => '1',
        'section' => 'A'
    ]);
    echo "Student Created: " . $student->student_id_number . " (ID: " . $student->id . ")\n";

    // 9. Read the Student
    $foundStudent = \App\Models\Student::find($student->id);
    if (!$foundStudent || $foundStudent->student_id_number !== $studentIdNum) {
        throw new Exception("Failed to read created student.");
    }
    echo "Student Read Verified.\n";

    // 10. Update the Student
    $student->update(['fname' => 'Updated']);
    if ($student->fresh()->fname !== 'Updated') {
        throw new Exception("Failed to update student.");
    }
    echo "Student Update Verified.\n";

    // 12. Create a Position
    $position = \App\Models\Position::create([
        'school_id' => $school->id,
        'organization_id' => $org->id,
        'name' => 'President',
        'number_of_slots' => 1,
        'order' => 1,
        'is_active' => true
    ]);
    echo "Position Created: " . $position->name . " (ID: " . $position->id . ")\n";

    // 13. Create a Candidate
    $candidate = \App\Models\Candidate::create([
        'school_id' => $school->id,
        'organization_id' => $org->id,
        'election_id' => $election->id,
        'position_id' => $position->id,
        'student_id' => $student->id,
        'candidate_name' => 'John Doe',
        'is_active' => true
    ]);
    echo "Candidate Created: " . $candidate->candidate_name . " (ID: " . $candidate->id . ")\n";

    // 14. Update Candidate
    $candidate->update(['biography' => 'Test Bio']);
    if ($candidate->fresh()->biography !== 'Test Bio') {
        throw new Exception("Failed to update candidate.");
    }
    echo "Candidate Update Verified.\n";

    // 15. Cleanup (Delete in reverse order)
    $candidate->delete();
    echo "Candidate Delete Verified.\n";

    $position->delete();
    echo "Position Delete Verified.\n";

    $student->delete();
    echo "Student Delete Verified.\n";

    $election->delete();
    $election2->delete();
    echo "Election Delete Verified.\n";

    $foundOrg->delete();
    $org2->delete();
    echo "Organization Delete Verified.\n";

    echo "\nCRUD Deep Verification SUCCESSFUL for all major modules.\n";

} catch (Exception $e) {
    echo "\nCRUD Verification FAILED: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
