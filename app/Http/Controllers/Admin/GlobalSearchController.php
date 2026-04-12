<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    /**
     * Typeahead for admin header: students (directory), student accounts, elections.
     */
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([
                'students' => [],
                'student_accounts' => [],
                'elections' => [],
            ]);
        }

        $isPostgres = DB::connection()->getDriverName() === 'pgsql';
        $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';
        $term = '%'.$q.'%';

        return response()->json([
            'students' => $this->students($term, $likeOperator, $isPostgres),
            'student_accounts' => $this->studentAccounts($term, $likeOperator, $isPostgres),
            'elections' => $this->elections($term, $likeOperator),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function students(string $term, string $likeOperator, bool $isPostgres): array
    {
        $query = Student::query()->where(function ($inner) use ($term, $likeOperator, $isPostgres) {
            $inner->where('student_id_number', $likeOperator, $term)
                ->orWhere('fname', $likeOperator, $term)
                ->orWhere('mname', $likeOperator, $term)
                ->orWhere('lname', $likeOperator, $term);
            if ($isPostgres) {
                $inner->orWhereRaw("(COALESCE(fname, '') || ' ' || COALESCE(mname, '') || ' ' || COALESCE(lname, '')) ILIKE ?", [$term]);
            } else {
                $inner->orWhereRaw("CONCAT(COALESCE(fname, ''), ' ', COALESCE(mname, ''), ' ', COALESCE(lname, '')) LIKE ?", [$term]);
            }
        });

        return $query->orderBy('student_id_number')
            ->limit(5)
            ->get()
            ->map(function (Student $s) {
                $fullName = trim(($s->fname ?? '').' '.($s->mname ?? '').' '.($s->lname ?? '').' '.($s->ext ?? ''));

                return [
                    'id' => $s->id,
                    'student_id_number' => $s->student_id_number,
                    'full_name' => $fullName ?: 'N/A',
                    'label' => $s->student_id_number.' — '.($fullName ?: 'N/A'),
                    'url' => route('admin.students.index', ['search' => $s->student_id_number]),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function studentAccounts(string $term, string $likeOperator, bool $isPostgres): array
    {
        $matchingStudentIds = Student::query()
            ->where(function ($inner) use ($term, $likeOperator, $isPostgres) {
                $inner->where('student_id_number', $likeOperator, $term)
                    ->orWhere('fname', $likeOperator, $term)
                    ->orWhere('mname', $likeOperator, $term)
                    ->orWhere('lname', $likeOperator, $term);
                if ($isPostgres) {
                    $inner->orWhereRaw("(COALESCE(fname, '') || ' ' || COALESCE(mname, '') || ' ' || COALESCE(lname, '')) ILIKE ?", [$term]);
                } else {
                    $inner->orWhereRaw("CONCAT(COALESCE(fname, ''), ' ', COALESCE(mname, ''), ' ', COALESCE(lname, '')) LIKE ?", [$term]);
                }
            })
            ->pluck('student_id_number');

        $query = User::withoutGlobalScopes()
            ->where('usertype', 'student')
            ->where(function ($inner) use ($likeOperator, $term, $matchingStudentIds) {
                $inner->where('email', $likeOperator, $term)
                    ->orWhere('name', $likeOperator, $term);
                if ($matchingStudentIds->isNotEmpty()) {
                    $inner->orWhereIn('email', $matchingStudentIds);
                }
            });

        $this->applyStudentAccountAccessFilter($query);

        return $query->orderBy('email')
            ->limit(5)
            ->get()
            ->map(function (User $u) {
                return [
                    'user_id' => $u->id,
                    'email' => $u->email,
                    'name' => $u->name,
                    'label' => $u->email.' — '.$u->name,
                    'url' => route('admin.student-management.index', [
                        'search' => $u->email,
                        'highlight_user' => $u->id,
                    ]),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function elections(string $term, string $likeOperator): array
    {
        return Election::query()
            ->where(function ($inner) use ($term, $likeOperator) {
                $inner->where('election_name', $likeOperator, $term)
                    ->orWhere('type_of_election', $likeOperator, $term)
                    ->orWhere('description', $likeOperator, $term)
                    ->orWhere('venue', $likeOperator, $term)
                    ->orWhere('election_id', $likeOperator, $term);
            })
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(function (Election $e) {
                return [
                    'id' => $e->id,
                    'election_name' => $e->election_name,
                    'status' => $e->status,
                    'label' => $e->election_name.' ('.$e->status.')',
                    'url' => route('admin.elections.index', [
                        'search' => $e->election_name,
                        'highlight_election' => $e->id,
                    ]),
                ];
            })
            ->values()
            ->all();
    }

    private function applyStudentAccountAccessFilter(Builder $query): void
    {
        $authUser = Auth::user();

        if (! $authUser || $authUser->is_super_admin || ! $authUser->school_id) {
            return;
        }

        $query->where(function (Builder $builder) use ($authUser) {
            $builder->where('school_id', $authUser->school_id)
                ->orWhereNull('school_id')
                ->orWhereIn('email', Student::query()->select('student_id_number'));
        });
    }
}
