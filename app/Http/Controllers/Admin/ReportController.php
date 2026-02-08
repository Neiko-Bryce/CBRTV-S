<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Student;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the reports index page.
     */
    public function index()
    {
        $elections = Election::orderBy('election_date', 'desc')->get();
        
        // Get unique filter values from students table
        $courses = Student::distinct()->whereNotNull('course')->pluck('course')->sort()->values();
        $yearlevels = Student::distinct()->whereNotNull('yearlevel')->pluck('yearlevel')->sort()->values();
        $sections = Student::distinct()->whereNotNull('section')->pluck('section')->sort()->values();
        
        return view('admin.reports.index', compact('elections', 'courses', 'yearlevels', 'sections'));
    }

    /**
     * Generate report data based on filters.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'election_id' => 'required|exists:elections,id',
            'filter_type' => 'nullable|in:all,course,yearlevel,section',
            'filter_value' => 'nullable|string',
        ]);

        $electionId = $request->election_id;
        $filterType = $request->filter_type ?? 'all';
        $filterValue = $request->filter_value;

        $election = Election::with('organization')->findOrFail($electionId);
        
        // Get report data
        $reportData = $this->getReportData($electionId, $filterType, $filterValue);
        
        // Get filter options for the selected election's voters
        $courses = $this->getVoterCourses($electionId);
        $yearlevels = $this->getVoterYearLevels($electionId);
        $sections = $this->getVoterSections($electionId);

        return response()->json([
            'success' => true,
            'election' => $election,
            'data' => $reportData,
            'filters' => [
                'courses' => $courses,
                'yearlevels' => $yearlevels,
                'sections' => $sections,
            ],
        ]);
    }

    /**
     * Display print-friendly report.
     */
    public function print(Request $request, $electionId)
    {
        $filterType = $request->query('filter_type', 'all');
        $filterValue = $request->query('filter_value');

        $election = Election::with('organization')->findOrFail($electionId);
        $reportData = $this->getReportData($electionId, $filterType, $filterValue);

        return view('admin.reports.print', [
            'election' => $election,
            'reportData' => $reportData,
            'filterType' => $filterType,
            'filterValue' => $filterValue,
            'generatedAt' => now(),
        ]);
    }

    /**
     * Get report data with optional filtering.
     */
    private function getReportData($electionId, $filterType = 'all', $filterValue = null)
    {
        // Get all votes for this election
        $votesQuery = DB::table('votes')
            ->where('votes.election_id', $electionId);

        // Apply filters based on voter's student data
        if ($filterType !== 'all' && $filterValue) {
            $votesQuery->join('users', 'votes.voter_id', '=', 'users.id')
                ->join('students', 'users.email', '=', 'students.student_id_number')
                ->where('students.' . $filterType, $filterValue);
        }

        // Get filtered vote IDs
        $filteredVoteIds = $votesQuery->pluck('votes.id');

        // Total votes count
        $totalVotes = $filteredVoteIds->count();

        // Unique voters
        $uniqueVoterIds = DB::table('votes')
            ->whereIn('id', $filteredVoteIds)
            ->distinct()
            ->pluck('voter_id');
        $totalParticipants = $uniqueVoterIds->count();

        // Get total eligible students (those who have user accounts)
        $eligibleStudentsQuery = DB::table('students')
            ->join('users', 'students.student_id_number', '=', 'users.email')
            ->where('users.usertype', 'student');
        
        if ($filterType !== 'all' && $filterValue) {
            $eligibleStudentsQuery->where('students.' . $filterType, $filterValue);
        }
        
        $totalEligible = $eligibleStudentsQuery->count();
        
        // Participation rate
        $participationRate = $totalEligible > 0 ? round(($totalParticipants / $totalEligible) * 100, 1) : 0;

        // Get candidates with vote counts
        $candidates = Candidate::where('election_id', $electionId)
            ->with(['position', 'partylist', 'student'])
            ->get();

        // Calculate filtered votes for each candidate
        $candidates->each(function ($candidate) use ($filteredVoteIds) {
            $voteCount = DB::table('votes')
                ->whereIn('id', $filteredVoteIds)
                ->where('candidate_id', $candidate->id)
                ->count();
            
            $candidate->filtered_votes = $voteCount;
        });

        // Group by position ID to access position metadata for sorting
        $candidatesByPosition = $candidates->groupBy('position_id');
        
        $resultsByPosition = [];

        foreach ($candidatesByPosition as $positionId => $group) {
            $position = $group->first()->position;
            $positionName = $position ? $position->name : 'Unknown Position';
            $positionOrder = $position ? ($position->order ?? 0) : 9999;
            $numberOfSlots = $position ? ($position->number_of_slots ?? 1) : 1;

            // Sort candidates by votes desc
            $sortedCandidates = $group->sortByDesc('filtered_votes')->values();

            $resultsByPosition[] = [
                'position_name' => $positionName,
                'position_order' => $positionOrder,
                'number_of_slots' => $numberOfSlots,
                'candidates' => $sortedCandidates
            ];
        }

        // Sort positions by defined order
        usort($resultsByPosition, function ($a, $b) {
            return $a['position_order'] <=> $b['position_order'];
        });

        // Get participation by course/yearlevel/section for the filtered data
        $participationBreakdown = $this->getParticipationBreakdown($electionId, $filterType, $filterValue);

        // Get gender breakdown of voters
        $maleVoters = DB::table('votes')
            ->join('users', 'votes.voter_id', '=', 'users.id')
            ->join('students', 'users.email', '=', 'students.student_id_number')
            ->whereIn('votes.id', $filteredVoteIds)
            ->where('students.gender', 'Male')
            ->distinct('votes.voter_id')
            ->count('votes.voter_id');

        $femaleVoters = DB::table('votes')
            ->join('users', 'votes.voter_id', '=', 'users.id')
            ->join('students', 'users.email', '=', 'students.student_id_number')
            ->whereIn('votes.id', $filteredVoteIds)
            ->where('students.gender', 'Female')
            ->distinct('votes.voter_id')
            ->count('votes.voter_id');

        return [
            'totalVotes' => $totalVotes,
            'totalParticipants' => $totalParticipants,
            'totalEligible' => $totalEligible,
            'participationRate' => $participationRate,
            'maleVoters' => $maleVoters,
            'femaleVoters' => $femaleVoters,
            'resultsByPosition' => $resultsByPosition,
            'participationBreakdown' => $participationBreakdown,
            'electionYear' => $electionId ? Election::find($electionId)->election_date?->format('Y') : date('Y'),
        ];
    }

    /**
     * Get participation breakdown by different criteria.
     */
    private function getParticipationBreakdown($electionId, $filterType, $filterValue)
    {
        $breakdown = [];

        // Get all voters for this election
        $voterIds = Vote::where('election_id', $electionId)->distinct()->pluck('voter_id');

        // By Course
        $byCourse = DB::table('students')
            ->join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.course')
            ->select('students.course', DB::raw('count(*) as count'))
            ->groupBy('students.course')
            ->orderByDesc('count')
            ->get();

        // By Year Level
        $byYearlevel = DB::table('students')
            ->join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.yearlevel')
            ->select('students.yearlevel', DB::raw('count(*) as count'))
            ->groupBy('students.yearlevel')
            ->orderBy('students.yearlevel')
            ->get();

        // By Section
        $bySection = DB::table('students')
            ->join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.section')
            ->select('students.section', DB::raw('count(*) as count'))
            ->groupBy('students.section')
            ->orderByDesc('count')
            ->get();

        return [
            'byCourse' => $byCourse,
            'byYearlevel' => $byYearlevel,
            'bySection' => $bySection,
        ];
    }

    /**
     * Get unique courses from voters of an election.
     */
    private function getVoterCourses($electionId)
    {
        $voterIds = Vote::where('election_id', $electionId)->distinct()->pluck('voter_id');
        
        return DB::table('students')
            ->join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.course')
            ->distinct()
            ->pluck('students.course')
            ->sort()
            ->values();
    }

    /**
     * Get unique year levels from voters of an election.
     */
    private function getVoterYearLevels($electionId)
    {
        $voterIds = Vote::where('election_id', $electionId)->distinct()->pluck('voter_id');
        
        return DB::table('students')
            ->join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.yearlevel')
            ->distinct()
            ->pluck('students.yearlevel')
            ->sort()
            ->values();
    }

    /**
     * Get unique sections from voters of an election.
     */
    private function getVoterSections($electionId)
    {
        $voterIds = Vote::where('election_id', $electionId)->distinct()->pluck('voter_id');
        
        return DB::table('students')
            ->join('users', 'students.student_id_number', '=', 'users.email')
            ->whereIn('users.id', $voterIds)
            ->whereNotNull('students.section')
            ->distinct()
            ->pluck('students.section')
            ->sort()
            ->values();
    }
}
