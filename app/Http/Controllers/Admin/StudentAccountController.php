<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordRegenerationHistory;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentAccountController extends Controller
{
    /**
     * Display the student account management page.
     */
    public function index()
    {
        // Get all student accounts
        $studentAccounts = User::where('usertype', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Load student data for each account
        foreach ($studentAccounts as $account) {
            $student = Student::where('student_id_number', $account->email)->first();
            if ($student) {
                $account->campus = $student->campus;
                $account->course = $student->course;
                $account->yearlevel = $student->yearlevel;
                $account->fname = $student->fname;
                $account->mname = $student->mname;
                $account->lname = $student->lname;
                $account->ext = $student->ext;
            }
        }

        return view('admin.student-management.index', compact('studentAccounts'));
    }

    /**
     * Return suggestions for student ID or name (for real-time typeahead).
     */
    public function suggest(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['suggestions' => []]);
        }

        $isPostgres = DB::connection()->getDriverName() === 'pgsql';
        $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';
        $term = '%' . $q . '%';

        $students = Student::where(function ($query) use ($term, $likeOperator, $isPostgres) {
            $query->where('student_id_number', $likeOperator, $term)
                ->orWhere('fname', $likeOperator, $term)
                ->orWhere('mname', $likeOperator, $term)
                ->orWhere('lname', $likeOperator, $term);
            if ($isPostgres) {
                $query->orWhereRaw("(COALESCE(fname, '') || ' ' || COALESCE(mname, '') || ' ' || COALESCE(lname, '') || ' ' || COALESCE(ext, '')) ILIKE ?", [$term]);
            } else {
                $query->orWhereRaw("CONCAT(COALESCE(fname, ''), ' ', COALESCE(mname, ''), ' ', COALESCE(lname, ''), ' ', COALESCE(ext, '')) LIKE ?", [$term]);
            }
        })
            ->orderBy('student_id_number')
            ->take(10)
            ->get(['student_id_number', 'fname', 'mname', 'lname', 'ext']);

        $suggestions = $students->map(function ($s) {
            $fullName = trim(($s->fname ?? '') . ' ' . ($s->mname ?? '') . ' ' . ($s->lname ?? '') . ' ' . ($s->ext ?? ''));
            return [
                'student_id_number' => $s->student_id_number,
                'full_name' => $fullName ?: 'N/A',
            ];
        });

        return response()->json(['suggestions' => $suggestions->values()->all()]);
    }

    /**
     * Search for a student by student ID or name.
     */
    public function search(Request $request)
    {
        $request->validate([
            'search_term' => 'required|string',
        ]);

        $searchTerm = trim($request->search_term);
        $isPostgres = DB::connection()->getDriverName() === 'pgsql';
        $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

        // Search by student ID or name (first name, middle name, last name, or full name)
        $student = Student::where(function ($query) use ($searchTerm, $likeOperator, $isPostgres) {
            // Search by Student ID (case-insensitive)
            $query->where('student_id_number', $likeOperator, "%{$searchTerm}%")
                  // Search by First Name
                ->orWhere('fname', $likeOperator, "%{$searchTerm}%")
                  // Search by Middle Name
                ->orWhere('mname', $likeOperator, "%{$searchTerm}%")
                  // Search by Last Name
                ->orWhere('lname', $likeOperator, "%{$searchTerm}%");

            // Search by Full Name (concatenated) - database-specific
            if ($isPostgres) {
                // PostgreSQL: Use || for concatenation and ILIKE for case-insensitive
                $query->orWhereRaw("(COALESCE(fname, '') || ' ' || COALESCE(mname, '') || ' ' || COALESCE(lname, '') || ' ' || COALESCE(ext, '')) ILIKE ?", ["%{$searchTerm}%"]);
            } else {
                // MySQL: Use CONCAT
                $query->orWhereRaw("CONCAT(COALESCE(fname, ''), ' ', COALESCE(mname, ''), ' ', COALESCE(lname, ''), ' ', COALESCE(ext, '')) LIKE ?", ["%{$searchTerm}%"]);
            }
        })->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found. Please check the Student ID or Name and try again.',
            ], 404);
        }

        // Check if account already exists
        $existingUser = User::where('email', $student->student_id_number)->first();

        return response()->json([
            'success' => true,
            'student' => $student,
            'account_exists' => $existingUser ? true : false,
            'user' => $existingUser,
        ]);
    }

    /**
     * Create a user account for a student.
     */
    public function createAccount(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:students,student_id_number',
            'password' => 'required|string|min:6|max:6|regex:/^[A-Z0-9]+$/',
        ], [
            'password.regex' => 'Password must be exactly 6 characters: uppercase letters and numbers only.',
        ]);

        $student = Student::where('student_id_number', $request->student_id)->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        // Check if account already exists
        $existingUser = User::where('email', $student->student_id_number)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'An account already exists for this student.',
            ], 422);
        }

        // Create user account
        $user = User::create([
            'name' => trim(($student->fname ?? '').' '.($student->mname ?? '').' '.$student->lname.' '.($student->ext ?? '')),
            'email' => $student->student_id_number,
            'password' => Hash::make($request->password),
            'usertype' => 'student',
            'organization_id' => $student->organization_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student account created successfully.',
            'user' => $user,
        ]);
    }

    /**
     * Generate a random 6-character password (uppercase letters and numbers only).
     */
    public function generatePassword()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';

        for ($i = 0; $i < 6; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return response()->json([
            'success' => true,
            'password' => $password,
        ]);
    }

    /**
     * Regenerate password for a student account.
     */
    public function regeneratePassword(Request $request, $userId)
    {
        $user = User::where('id', $userId)
            ->where('usertype', 'student')
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Student account not found.',
            ], 404);
        }

        // Generate new password (6 chars: uppercase letters and numbers only)
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';

        for ($i = 0; $i < 6; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        // Update password and increment count
        $user->update([
            'password' => Hash::make($password),
            'password_regenerated_count' => ($user->password_regenerated_count ?? 0) + 1,
        ]);

        // Create history record
        PasswordRegenerationHistory::create([
            'user_id' => $user->id,
            'student_id' => $user->email,
            'regenerated_at' => now(),
            'regenerated_by' => Auth::user()->name ?? 'System',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password regenerated successfully.',
            'password' => $password,
            'regenerated_count' => $user->fresh()->password_regenerated_count,
        ]);
    }

    /**
     * Get password regeneration history for a student account.
     */
    public function getPasswordHistory($userId)
    {
        $user = User::where('id', $userId)
            ->where('usertype', 'student')
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Student account not found.',
            ], 404);
        }

        $history = PasswordRegenerationHistory::where('user_id', $userId)
            ->orderBy('regenerated_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }

    /**
     * Delete a student account.
     */
    public function deleteAccount($userId)
    {
        $user = User::where('id', $userId)
            ->where('usertype', 'student')
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Student account not found.',
            ], 404);
        }

        // Delete password regeneration history
        PasswordRegenerationHistory::where('user_id', $userId)->delete();

        // Delete the user account
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student account deleted successfully.',
        ]);
    }
}
