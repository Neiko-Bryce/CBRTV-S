<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        $query = Student::query();

        // Handle search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = trim($request->search);
            $isPostgres = DB::connection()->getDriverName() === 'pgsql';
            $likeOperator = $isPostgres ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($searchTerm, $likeOperator, $isPostgres) {
                // Search by Student ID (case-insensitive)
                $q->where('student_id_number', $likeOperator, "%{$searchTerm}%")
                  // Search by First Name
                    ->orWhere('fname', $likeOperator, "%{$searchTerm}%")
                  // Search by Middle Name
                    ->orWhere('mname', $likeOperator, "%{$searchTerm}%")
                  // Search by Last Name
                    ->orWhere('lname', $likeOperator, "%{$searchTerm}%");

                // Search by Full Name (concatenated) - database-specific
                if ($isPostgres) {
                    // PostgreSQL: Use || for concatenation and ILIKE for case-insensitive
                    $q->orWhereRaw("(COALESCE(fname, '') || ' ' || COALESCE(mname, '') || ' ' || COALESCE(lname, '')) ILIKE ?", ["%{$searchTerm}%"]);
                } else {
                    // MySQL: Use CONCAT
                    $q->orWhereRaw("CONCAT(COALESCE(fname, ''), ' ', COALESCE(mname, ''), ' ', COALESCE(lname, '')) LIKE ?", ["%{$searchTerm}%"]);
                }
            });
        }

        // Order by ID to keep students in stable position even after updates
        // This ensures updated students stay in their current position in the table
        $students = $query->orderBy('id', 'asc')->paginate(15)->withQueryString();

        // Get statistics for ALL students (not just current page or search results)
        $stats = [
            'total' => Student::count(),
            'male' => Student::where('gender', 'Male')->count(),
            'female' => Student::where('gender', 'Female')->count(),
            'other' => Student::where('gender', 'Other')->count(),
            'campuses' => Student::distinct('campus')->count('campus'),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id_number' => 'required|string|max:255|unique:students',
                'campus' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'fname' => 'nullable|string|max:255',
                'mname' => 'nullable|string|max:255',
                'ext' => 'nullable|string|max:10',
                'gender' => 'nullable|in:Male,Female,Other',
                'course' => 'required|string|max:255',
                'yearlevel' => 'required|string|max:255',
                'section' => 'required|string|max:255',
            ]);

            $student = Student::create($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student created successfully.',
                    'student' => $student,
                ]);
            }

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'student' => $student,
            ]);
        }

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'student' => $student,
            ]);
        }

        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        try {
            $validated = $request->validate([
                'student_id_number' => 'required|string|max:255|unique:students,student_id_number,'.$student->id,
                'campus' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'fname' => 'nullable|string|max:255',
                'mname' => 'nullable|string|max:255',
                'ext' => 'nullable|string|max:10',
                'gender' => 'nullable|in:Male,Female,Other',
                'course' => 'required|string|max:255',
                'yearlevel' => 'required|string|max:255',
                'section' => 'required|string|max:255',
            ]);

            $student->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student updated successfully.',
                    'student' => $student->fresh(),
                ]);
            }

            return redirect()->route('admin.students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student, Request $request)
    {
        $student->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully.',
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Delete all students (e.g. before re-importing from Excel).
     */
    public function destroyAll(Request $request)
    {
        $count = Student::count();
        Student::query()->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $count > 0
                    ? "All {$count} student(s) have been deleted."
                    : 'No students to delete.',
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', $count > 0 ? "All {$count} student(s) have been deleted." : 'No students to delete.');
    }

    /**
     * Import students from Excel file.
     */
    public function import(Request $request)
    {
        // Increase PHP limits for large file processing
        ini_set('max_execution_time', 0); // No time limit
        ini_set('memory_limit', '512M'); // Increase memory limit
        set_time_limit(0); // No time limit

        try {
            // More lenient file validation - no practical limit for large files
            $request->validate([
                'file' => 'required|file|max:1048576', // 1GB max (1024MB) - effectively no limit
            ], [
                'file.required' => 'Please select a file to upload.',
                'file.file' => 'The uploaded file is invalid.',
                'file.max' => 'The file size must not exceed 1GB.',
            ]);

            $file = $request->file('file');

            if (! $file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file was uploaded. Please select an Excel file.',
                    'imported' => 0,
                    'skipped' => 0,
                ], 422);
            }

            // Check file extension manually
            $extension = strtolower($file->getClientOriginalExtension());
            $allowedExtensions = ['xlsx', 'xls', 'csv'];
            if (! in_array($extension, $allowedExtensions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file type. Please upload an Excel file (.xlsx, .xls, or .csv).',
                    'imported' => 0,
                    'skipped' => 0,
                ], 422);
            }

            if (! $file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The uploaded file is invalid or corrupted. Please try again.',
                    'imported' => 0,
                    'skipped' => 0,
                ], 422);
            }

            try {
                $filePath = $file->getRealPath();
                if (! $filePath || ! file_exists($filePath)) {
                    Log::error('File path invalid: '.($filePath ?? 'null'));

                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to access the uploaded file. Please try uploading again.',
                        'imported' => 0,
                        'skipped' => 0,
                    ], 422);
                }

                // Try to load the spreadsheet with read-data-only to reduce memory for large files
                try {
                    $reader = IOFactory::createReader(IOFactory::identify($filePath));
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($filePath);
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    Log::error('PhpSpreadsheet Reader error: '.$e->getMessage());

                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to read Excel file. The file may be corrupted or in an unsupported format. Please ensure it is a valid .xlsx, .xls, or .csv file.',
                        'imported' => 0,
                        'skipped' => 0,
                    ], 422);
                }

                $worksheet = $spreadsheet->getActiveSheet();

                // Get the highest row and column to ensure we read ALL data
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                Log::info("Excel file dimensions: {$highestRow} rows, up to column {$highestColumn} (index: {$highestColumnIndex})");

                // Read ALL rows - ensure we get every single row
                // Use toArray with null values to preserve all data
                // Last parameter false = return numeric indices instead of column letters
                $rows = $worksheet->toArray(null, true, true, false);

                // Ensure all rows are numeric arrays with sequential indices (0, 1, 2, ...)
                // This handles edge cases where rows might have non-sequential indices
                if (! empty($rows)) {
                    $normalizedRows = [];
                    foreach ($rows as $rowIndex => $row) {
                        if (is_array($row)) {
                            // Normalize to sequential numeric indices
                            $normalizedRows[] = array_values($row);
                        } else {
                            $normalizedRows[] = $row;
                        }
                    }
                    $rows = $normalizedRows;
                }

                $actualRowCount = count($rows);
                Log::info("Successfully loaded Excel file: {$actualRowCount} rows loaded (expected: {$highestRow} rows including header)");

                // Warn if row count doesn't match
                if ($actualRowCount < $highestRow) {
                    Log::warning("Row count mismatch: Loaded {$actualRowCount} rows but file has {$highestRow} rows. Some data may be missing.");
                }

                if (empty($rows)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Excel file appears to be empty. Please ensure it contains data.',
                        'imported' => 0,
                        'skipped' => 0,
                    ], 422);
                }

                Log::info('Successfully loaded Excel file with '.count($rows).' rows');
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                Log::error('PhpSpreadsheet error: '.$e->getMessage());
                Log::error('Stack trace: '.$e->getTraceAsString());

                return response()->json([
                    'success' => false,
                    'message' => 'Error reading Excel file: '.$e->getMessage().'. Please ensure the file is not corrupted.',
                    'imported' => 0,
                    'skipped' => 0,
                ], 422);
            } catch (\Exception $e) {
                Log::error('Error loading Excel file: '.$e->getMessage());
                Log::error('Error type: '.get_class($e));
                Log::error('Stack trace: '.$e->getTraceAsString());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process Excel file: '.$e->getMessage().'. Please check the file and try again.',
                    'imported' => 0,
                    'skipped' => 0,
                ], 422);
            }

            Log::info('Import started. Total rows in file: '.count($rows));

            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Excel file must contain at least a header row and one data row.',
                    'imported' => 0,
                    'skipped' => 0,
                ], 422);
            }

            // Skip header row (first row)
            $header = array_shift($rows);

            // Normalize header to numeric indices (in case it has column letters as keys)
            $header = array_values($header);

            // Map header columns to database fields - more flexible matching
            $headerMap = [];
            foreach ($header as $index => $column) {
                if ($column === null) {
                    continue;
                }
                $columnStr = is_numeric($column) ? (string) $column : trim((string) $column);
                if (empty($columnStr)) {
                    continue;
                }

                $columnLower = strtolower($columnStr);
                // Remove extra spaces and special characters for matching
                $columnClean = preg_replace('/[^a-z0-9]/', '', $columnLower);

                // Student ID mapping - very flexible (accepts many variations)
                // Check for any variation of "student id" or just "id"
                $isStudentId = false;

                // First check the original column name with spaces (for "Student ID", "Student ID Number", etc.)
                $columnLowerWithSpaces = strtolower($columnStr);

                // Check if it contains "student" and "id" (in any order) - with spaces
                if ((strpos($columnLowerWithSpaces, 'student') !== false && strpos($columnLowerWithSpaces, 'id') !== false) ||
                    preg_match('/student.*id|id.*student|studentid/i', $columnStr)) {
                    $isStudentId = true;
                }

                // Check cleaned version (no spaces/special chars)
                if (! $isStudentId && (
                    preg_match('/student.*id|id.*number|studentid|idnumber/', $columnClean) ||
                    in_array($columnLower, ['id', 'student id', 'student_id', 'student id number', 'studentidnumber', 'student_id_number', 'studentid', 'student id no', 'id no', 'id number', 'studentidnumber']))) {
                    $isStudentId = true;
                }

                // Also check if it's just "id" (but not if it's part of another word)
                if (! $isStudentId && ($columnLower === 'id' || $columnClean === 'id') && strlen($columnStr) <= 5) {
                    $isStudentId = true;
                }

                if ($isStudentId && ! isset($headerMap['student_id_number'])) {
                    $headerMap['student_id_number'] = $index;
                    Log::info("Matched column '{$columnStr}' (lowercase: '{$columnLower}') to student_id_number at index {$index}");
                }
                // Campus
                elseif (strpos($columnClean, 'campus') !== false || $columnLower === 'campus') {
                    if (! isset($headerMap['campus'])) {
                        $headerMap['campus'] = $index;
                    }
                }
                // Last name
                elseif (preg_match('/last.*name|lastname|surname|lname/', $columnClean) ||
                        in_array($columnLower, ['lname', 'last name', 'lastname', 'surname'])) {
                    if (! isset($headerMap['lname'])) {
                        $headerMap['lname'] = $index;
                    }
                }
                // First name
                elseif (preg_match('/first.*name|firstname|fname/', $columnClean) ||
                        in_array($columnLower, ['fname', 'first name', 'firstname', 'f name'])) {
                    if (! isset($headerMap['fname'])) {
                        $headerMap['fname'] = $index;
                    }
                }
                // Middle name
                elseif (preg_match('/middle.*name|middlename|mname/', $columnClean) ||
                        in_array($columnLower, ['mname', 'middle name', 'middlename', 'm name'])) {
                    if (! isset($headerMap['mname'])) {
                        $headerMap['mname'] = $index;
                    }
                }
                // Extension
                elseif (preg_match('/ext|extension/', $columnClean) ||
                        in_array($columnLower, ['ext', 'extension', 'name extension'])) {
                    if (! isset($headerMap['ext'])) {
                        $headerMap['ext'] = $index;
                    }
                }
                // Gender
                elseif (preg_match('/gender|sex/', $columnClean) ||
                        in_array($columnLower, ['gender', 'sex'])) {
                    if (! isset($headerMap['gender'])) {
                        $headerMap['gender'] = $index;
                    }
                }
                // Course
                elseif (preg_match('/course|program|programme/', $columnClean) ||
                        in_array($columnLower, ['course', 'program', 'programme'])) {
                    if (! isset($headerMap['course'])) {
                        $headerMap['course'] = $index;
                    }
                }
                // Year level
                elseif (preg_match('/year.*level|yearlevel|year|level/', $columnClean) ||
                        in_array($columnLower, ['yearlevel', 'year level', 'year_level', 'year', 'level'])) {
                    if (! isset($headerMap['yearlevel'])) {
                        $headerMap['yearlevel'] = $index;
                    }
                }
                // Section
                elseif (preg_match('/section|sec/', $columnClean) ||
                        in_array($columnLower, ['section', 'sec'])) {
                    if (! isset($headerMap['section'])) {
                        $headerMap['section'] = $index;
                    }
                }
            }

            // Final check: if student_id_number is not mapped, try one more time with more lenient matching
            if (! isset($headerMap['student_id_number'])) {
                foreach ($header as $idx => $hdr) {
                    if ($hdr) {
                        $hdrLower = strtolower(trim((string) $hdr));
                        // Very lenient check - if it has "id" in it and doesn't match other fields
                        if ((strpos($hdrLower, 'id') !== false || $hdrLower === 'id') &&
                            ! isset($headerMap['student_id_number'])) {
                            // Make sure it's not already mapped to something else
                            $alreadyMapped = false;
                            foreach ($headerMap as $mappedField => $mappedIdx) {
                                if ($mappedIdx == $idx) {
                                    $alreadyMapped = true;
                                    break;
                                }
                            }
                            if (! $alreadyMapped) {
                                $headerMap['student_id_number'] = $idx;
                                Log::info("Fallback: Matched column '{$hdr}' to student_id_number at index {$idx}");
                                break;
                            }
                        }
                    }
                }
            }

            Log::info('Header mapping: '.json_encode($headerMap));
            Log::info('Detected headers: '.json_encode($header));
            Log::info('Header count: '.count($header));

            // Log detailed header analysis
            foreach ($header as $idx => $hdr) {
                if ($hdr) {
                    $mappedTo = null;
                    foreach ($headerMap as $field => $mappedIdx) {
                        if ($mappedIdx == $idx) {
                            $mappedTo = $field;
                            break;
                        }
                    }
                    Log::info("Header [{$idx}]: '{$hdr}' -> ".($mappedTo ? "MAPPED to {$mappedTo}" : 'NOT MAPPED'));
                }
            }

            // Log first few rows for debugging
            if (count($rows) > 0) {
                Log::info('First data row sample: '.json_encode(array_slice($rows, 0, 3)));

                // Log the structure of the first row to understand the array format
                $firstRow = $rows[0];
                if (is_array($firstRow)) {
                    $firstRowKeys = array_keys($firstRow);
                    Log::info('First row keys (sample): '.json_encode(array_slice($firstRowKeys, 0, 10)));
                    Log::info('First row is associative: '.(array_keys($firstRow) !== range(0, count($firstRow) - 1) ? 'YES' : 'NO'));
                }

                // Log header map indices
                Log::info('Header map indices: '.json_encode($headerMap));
            }

            // Validate that required columns exist (only truly required ones)
            $requiredFields = ['student_id_number', 'campus', 'lname'];
            $optionalFields = ['course', 'yearlevel', 'section']; // These are optional
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (! isset($headerMap[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (! empty($missingFields)) {
                $fieldNames = [
                    'student_id_number' => 'Student ID',
                    'campus' => 'Campus',
                    'lname' => 'Last Name',
                ];
                $optionalFieldNames = [
                    'course' => 'Course',
                    'yearlevel' => 'Year Level',
                    'section' => 'Section',
                ];
                $missingFieldNames = array_map(function ($field) use ($fieldNames) {
                    return $fieldNames[$field] ?? $field;
                }, $missingFields);

                // Find similar column names that might match
                $suggestions = [];
                foreach ($missingFields as $missingField) {
                    $suggestions[$missingField] = [];
                    foreach ($header as $hdr) {
                        if ($hdr) {
                            $hdrLower = strtolower(trim($hdr));
                            // Check for partial matches
                            if ($missingField === 'student_id_number' &&
                                (strpos($hdrLower, 'student') !== false || strpos($hdrLower, 'id') !== false)) {
                                $suggestions[$missingField][] = $hdr;
                            } elseif ($missingField === 'lname' &&
                                     (strpos($hdrLower, 'last') !== false || strpos($hdrLower, 'name') !== false || strpos($hdrLower, 'surname') !== false)) {
                                $suggestions[$missingField][] = $hdr;
                            } elseif ($missingField === 'campus' && strpos($hdrLower, 'campus') !== false) {
                                $suggestions[$missingField][] = $hdr;
                            } elseif ($missingField === 'course' &&
                                     (strpos($hdrLower, 'course') !== false || strpos($hdrLower, 'program') !== false)) {
                                $suggestions[$missingField][] = $hdr;
                            } elseif ($missingField === 'yearlevel' &&
                                     (strpos($hdrLower, 'year') !== false || strpos($hdrLower, 'level') !== false)) {
                                $suggestions[$missingField][] = $hdr;
                            } elseif ($missingField === 'section' && strpos($hdrLower, 'section') !== false) {
                                $suggestions[$missingField][] = $hdr;
                            }
                        }
                    }
                }

                Log::warning('Missing required columns: '.implode(', ', $missingFields));
                Log::warning('Detected headers: '.json_encode($header));
                Log::warning('Header map: '.json_encode($headerMap));
                Log::warning('Suggestions: '.json_encode($suggestions));

                $errorMessage = 'Missing required columns in Excel file: '.implode(', ', $missingFieldNames).'. ';
                $errorMessage .= 'Please ensure your Excel file has these column headers (case-insensitive). ';

                // Check which optional fields are missing
                $missingOptional = [];
                foreach ($optionalFields as $field) {
                    if (! isset($headerMap[$field])) {
                        $missingOptional[] = $optionalFieldNames[$field];
                    }
                }

                if (! empty($missingOptional)) {
                    $errorMessage .= 'Note: Optional columns (Course, Year Level, Section) are missing but will be set to empty. ';
                }

                $errorMessage .= 'Detected headers in your file: '.implode(', ', array_filter($header)).'. ';

                // Add suggestions
                foreach ($suggestions as $field => $suggestedHeaders) {
                    if (! empty($suggestedHeaders)) {
                        $fieldName = $fieldNames[$field] ?? ($optionalFieldNames[$field] ?? $field);
                        $errorMessage .= "For '{$fieldName}', found similar columns: ".implode(', ', array_unique($suggestedHeaders)).'. ';
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['file' => ['Missing columns: '.implode(', ', $missingFieldNames)]],
                    'detected_headers' => array_filter($header),
                    'header_map' => $headerMap,
                    'missing_fields' => $missingFields,
                    'suggestions' => $suggestions,
                    'imported' => 0,
                    'skipped' => 0,
                ], 422);
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $studentsToInsert = [];
            $studentIdsInBatch = [];
            $skipReasons = [
                'missing_fields' => 0,
                'duplicate_in_file' => 0,
                'duplicate_in_db' => 0,
                'empty_row' => 0,
                'other' => 0,
            ];
            // Track gender counts for imported students
            $genderCounts = [
                'Male' => 0,
                'Female' => 0,
                'Other' => 0,
                'null' => 0,
            ];

            // Get existing student IDs once (pluck for lower memory) - case-insensitive lookup
            try {
                $existingStudentIds = [];
                foreach (Student::pluck('student_id_number') as $id) {
                    $existingStudentIds[strtolower($id)] = $id;
                }
                Log::info('Found '.count($existingStudentIds).' existing students in database');
            } catch (\Exception $e) {
                Log::error('Error fetching existing students: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Database error: '.$e->getMessage(),
                    'imported' => 0,
                    'skipped' => 0,
                ], 500);
            }

            // Log some statistics
            $totalRows = count($rows);
            Log::info('Total rows to process: '.$totalRows);
            Log::info('Existing students count: '.count($existingStudentIds));
            Log::info('Starting to process rows...');

            $processedRows = 0;
            $insertChunkSize = 250;
            foreach ($rows as $rowIndex => $row) {
                $processedRows++;

                // Log progress every 500 rows
                if ($processedRows % 500 === 0) {
                    Log::info("Processing row {$processedRows}/{$totalRows} - Prepared: ".count($studentsToInsert).", Skipped: {$skipped}");
                }
                // Skip completely empty rows - less strict check
                // Only skip if ALL cells are empty/null
                $hasData = false;
                foreach ($row as $cell) {
                    if ($cell !== null && $cell !== '') {
                        $cellStr = is_string($cell) ? trim($cell) : (string) $cell;
                        if ($cellStr !== '' && $cellStr !== '0') {
                            $hasData = true;
                            break; // Found at least one non-empty cell
                        }
                    }
                }
                // Only skip if completely empty
                if (! $hasData) {
                    $skipReasons['empty_row']++;

                    continue;
                }

                try {
                    // Helper function to get cell value - more robust and accurate
                    $getCellValue = function ($index) use ($row) {
                        // Ensure row is a numeric array (handle both associative and numeric)
                        $rowValues = array_values($row);

                        // Check if index is valid
                        if (! is_numeric($index) || $index < 0 || ! isset($rowValues[$index])) {
                            return '';
                        }

                        $value = $rowValues[$index];

                        // Handle null
                        if ($value === null) {
                            return '';
                        }

                        // Handle empty strings
                        if ($value === '') {
                            return '';
                        }

                        // Handle boolean false
                        if ($value === false) {
                            return '';
                        }

                        // Convert to string, handling numbers and dates
                        if (is_numeric($value)) {
                            // Handle integers and floats
                            if (is_float($value)) {
                                // If it's a whole number like 1.0, return as integer string
                                if ($value == intval($value)) {
                                    return (string) intval($value);
                                }

                                // Otherwise return as is (but this shouldn't happen for student data)
                                return (string) $value;
                            }

                            // Integer
                            return (string) $value;
                        }

                        // Handle DateTime objects
                        if ($value instanceof \DateTime) {
                            return $value->format('Y-m-d');
                        }

                        // Handle PhpOffice\PhpSpreadsheet\Cell\Cell objects
                        if (is_object($value) && method_exists($value, 'getCalculatedValue')) {
                            $value = $value->getCalculatedValue();
                            if ($value === null) {
                                return '';
                            }
                        }

                        // Convert to string and clean
                        $result = (string) $value;
                        // Remove null bytes and other control characters except newlines
                        $result = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/', '', $result);
                        // Trim whitespace
                        $result = trim($result);

                        return $result;
                    };

                    $studentId = $getCellValue($headerMap['student_id_number']);
                    $campus = $getCellValue($headerMap['campus']);
                    $lname = $getCellValue($headerMap['lname']);

                    // Optional fields - get if they exist in the file, otherwise use empty string
                    $course = isset($headerMap['course']) ? $getCellValue($headerMap['course']) : '';
                    $yearlevel = isset($headerMap['yearlevel']) ? $getCellValue($headerMap['yearlevel']) : '';
                    $section = isset($headerMap['section']) ? $getCellValue($headerMap['section']) : '';

                    // More accurate validation - only skip if required fields are missing
                    $missingFields = [];
                    if (empty($studentId) || trim($studentId) === '' || $studentId === '0') {
                        $missingFields[] = 'Student ID';
                    }
                    if (empty($campus) || trim($campus) === '') {
                        $missingFields[] = 'Campus';
                    }
                    if (empty($lname) || trim($lname) === '') {
                        $missingFields[] = 'Last Name';
                    }

                    // Course, Year Level, and Section are optional - don't skip if missing
                    // Just log a warning if they're missing but the column exists

                    if (! empty($missingFields)) {
                        $skipped++;
                        $skipReasons['missing_fields']++;
                        if (count($errors) < 100) { // Show more errors for debugging
                            $errors[] = 'Row '.($rowIndex + 2).': Missing '.implode(', ', $missingFields);
                        }

                        continue;
                    }

                    // Normalize student ID - remove any whitespace
                    $studentId = trim($studentId);
                    // Normalize other fields
                    $campus = trim($campus);
                    $lname = trim($lname);
                    $course = trim($course);
                    $yearlevel = trim($yearlevel);
                    $section = trim($section);

                    // If optional fields are empty and columns don't exist in file, set to empty string
                    // This allows import to proceed even without these fields

                    // Check for duplicates in batch (case-insensitive comparison)
                    $studentIdLower = strtolower($studentId);
                    if (isset($studentIdsInBatch[$studentIdLower])) {
                        $skipped++;
                        $skipReasons['duplicate_in_file']++;
                        if (count($errors) < 200) { // Show more duplicate errors
                            $errors[] = 'Row '.($rowIndex + 2).": Duplicate Student ID '{$studentId}' in file";
                        }

                        continue;
                    }

                    // Check if already exists in database (case-insensitive)
                    if (isset($existingStudentIds[$studentIdLower])) {
                        $skipped++;
                        $skipReasons['duplicate_in_db']++;
                        if (count($errors) < 200) { // Show more duplicate errors
                            $errors[] = 'Row '.($rowIndex + 2).": Student ID '{$studentId}' already exists";
                        }

                        continue;
                    }

                    // Mark as seen (store lowercase for case-insensitive duplicate checking)
                    $studentIdsInBatch[$studentIdLower] = $studentId;

                    // Get optional fields - ensure we get ALL available data
                    $fname = isset($headerMap['fname']) ? trim($getCellValue($headerMap['fname'])) : null;
                    $mname = isset($headerMap['mname']) ? trim($getCellValue($headerMap['mname'])) : null;
                    $ext = isset($headerMap['ext']) ? trim($getCellValue($headerMap['ext'])) : null;
                    $gender = null;

                    // Handle gender with more flexibility - normalize and clean the value
                    if (isset($headerMap['gender'])) {
                        $genderValue = $getCellValue($headerMap['gender']);
                        $originalGenderValue = $genderValue; // Keep for logging

                        // Convert to string and clean
                        $genderValue = trim((string) $genderValue);

                        // Debug: Log first few raw gender values to see what we're getting
                        static $genderDebugCount = 0;
                        if ($genderDebugCount < 5 && ! empty($genderValue)) {
                            Log::info('Raw gender value from Excel (row '.($rowIndex + 2)."): '{$originalGenderValue}' -> cleaned: '{$genderValue}'");
                            $genderDebugCount++;
                        }

                        // Handle common Excel issues: "NULL", "N/A", empty strings
                        if (empty($genderValue) ||
                            strtolower($genderValue) === 'null' ||
                            strtolower($genderValue) === 'n/a' ||
                            strtolower($genderValue) === 'na') {
                            $gender = null;
                        } else {
                            // Remove any non-alphabetic characters except spaces
                            $genderValue = preg_replace('/[^a-zA-Z\s]/', '', $genderValue);
                            $genderValue = trim($genderValue);

                            if (! empty($genderValue)) {
                                // Normalize to lowercase for comparison
                                $genderValueLower = strtolower($genderValue);
                                // Remove any extra spaces
                                $genderValueLower = preg_replace('/\s+/', ' ', $genderValueLower);
                                $genderValueLower = trim($genderValueLower);

                                // Map common variations - comprehensive matching
                                // Male variations
                                if (in_array($genderValueLower, ['male', 'm', 'man', 'masculine', 'masc']) ||
                                    preg_match('/^male/i', $genderValueLower) ||
                                    $genderValueLower === 'm') {
                                    $gender = 'Male';
                                }
                                // Female variations
                                elseif (in_array($genderValueLower, ['female', 'f', 'woman', 'feminine', 'fem']) ||
                                       preg_match('/^female/i', $genderValueLower) ||
                                       $genderValueLower === 'f') {
                                    $gender = 'Female';
                                }
                                // Other variations
                                elseif (in_array($genderValueLower, ['other', 'o']) ||
                                       preg_match('/^other/i', $genderValueLower) ||
                                       preg_match('/prefer.*not/i', $genderValueLower)) {
                                    $gender = 'Other';
                                }
                                // Check if already in correct format (case-insensitive)
                                elseif (in_array(strtolower($genderValue), ['male', 'female', 'other'])) {
                                    // Capitalize first letter
                                    $gender = ucfirst(strtolower($genderValue));
                                }
                                // Exact match with proper case
                                elseif (in_array($genderValue, ['Male', 'Female', 'Other'])) {
                                    $gender = $genderValue;
                                } else {
                                    // Log unrecognized gender values for debugging (first 20 only)
                                    static $genderLogCount = 0;
                                    if ($genderLogCount < 20) {
                                        $originalValue = $getCellValue($headerMap['gender']);
                                        Log::warning("Unrecognized gender value: '{$genderValue}' (original: '{$originalValue}', cleaned: '{$genderValueLower}') - setting to null");
                                        $genderLogCount++;
                                    }
                                    $gender = null;
                                }
                            } else {
                                $gender = null;
                            }
                        }
                    }

                    // Prepare data - ensure correct column names and data types in proper order
                    // Capture ALL data from the file, including empty values
                    // Use empty string for course/yearlevel/section when missing (DB columns are NOT NULL)
                    $data = [
                        'student_id_number' => $studentId,
                        'campus' => $campus,
                        'lname' => $lname,
                        'fname' => ($fname !== null && $fname !== '') ? $fname : null,
                        'mname' => ($mname !== null && $mname !== '') ? $mname : null,
                        'ext' => ($ext !== null && $ext !== '') ? $ext : null,
                        'gender' => $gender,
                        'course' => ($course !== null && (string) $course !== '') ? $course : '',
                        'yearlevel' => ($yearlevel !== null && (string) $yearlevel !== '') ? $yearlevel : '',
                        'section' => ($section !== null && (string) $section !== '') ? $section : '',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $studentsToInsert[] = $data;

                    // Insert in chunks to limit memory and avoid timeouts
                    if (count($studentsToInsert) >= $insertChunkSize) {
                        $this->insertStudentChunk($studentsToInsert, $imported, $skipped, $errors, $skipReasons, $genderCounts);
                        $studentsToInsert = [];
                    }

                    // Log first few and periodic rows for verification
                    if (($imported + count($studentsToInsert)) <= 5) {
                        Log::info('Prepared student (row '.($rowIndex + 2)."): ID={$studentId}, Name={$lname}");
                    } elseif (($imported + count($studentsToInsert)) % 500 === 0) {
                        Log::info('Prepared '.($imported + count($studentsToInsert)).' students so far... (row '.($rowIndex + 2).')');
                    }

                } catch (\Exception $e) {
                    $skipped++;
                    $skipReasons['other']++;

                    // Get more detailed error information
                    $errorDetails = $e->getMessage();
                    $errorClass = get_class($e);

                    // Add file and line info for debugging
                    if (method_exists($e, 'getFile') && method_exists($e, 'getLine')) {
                        $errorDetails .= " (in {$e->getFile()} at line {$e->getLine()})";
                    }

                    // Log the full exception for debugging
                    Log::error('Student import error on row '.($rowIndex + 2).': '.$errorDetails);
                    Log::error('Exception type: '.$errorClass);
                    Log::error('Row data: '.json_encode($row, JSON_UNESCAPED_UNICODE));
                    Log::error('Header map: '.json_encode($headerMap));

                    // Try to extract the problematic field
                    $problemField = 'unknown';
                    if (preg_match('/Undefined array key (\d+)/', $errorDetails, $matches)) {
                        $problemField = 'column index '.$matches[1];
                    } elseif (preg_match('/index (\d+)/i', $errorDetails, $matches)) {
                        $problemField = 'column index '.$matches[1];
                    }

                    if (count($errors) < 200) {
                        $errorMsg = 'Row '.($rowIndex + 2).': '.$errorDetails;
                        if ($problemField !== 'unknown') {
                            $errorMsg .= " (Problem with {$problemField})";
                        }
                        $errors[] = $errorMsg;
                    }
                }
            }

            Log::info('========================================');
            Log::info('ROW PROCESSING COMPLETED!');
            Log::info('========================================');
            Log::info('Total rows in file (including header): '.$totalRows);
            Log::info('Total data rows processed: '.$processedRows);
            $totalPrepared = $imported + count($studentsToInsert);
            Log::info('Students prepared for insert: '.$totalPrepared.' (remaining in buffer: '.count($studentsToInsert).')');
            Log::info('Rows skipped: '.$skipped);
            Log::info('Skip reasons breakdown: '.json_encode($skipReasons));

            // Log detailed statistics
            if ($totalRows > 0) {
                $dataRows = $totalRows - 1; // Exclude header
                $successRate = $dataRows > 0 ? ($totalPrepared / $dataRows) * 100 : 0;
                Log::info('Success rate: '.number_format($successRate, 2).'% ('.$totalPrepared." out of {$dataRows} data rows)");

                if ($successRate < 50 && $dataRows > 10) {
                    Log::warning('LOW SUCCESS RATE! Only '.number_format($successRate, 2).'% of rows were prepared for import.');
                }
            }

            // Log sample of prepared students for verification (remainder in buffer)
            if (count($studentsToInsert) > 0) {
                Log::info('Sample of remaining to insert (first 3): '.json_encode(array_slice($studentsToInsert, 0, 3)));
                if (count($studentsToInsert) > 10) {
                    Log::info('Sample prepared students (middle 2): '.json_encode(array_slice($studentsToInsert, floor(count($studentsToInsert) / 2), 2)));
                    Log::info('Sample prepared students (last 2): '.json_encode(array_slice($studentsToInsert, -2)));
                }
            } else {
                Log::error('========================================');
                Log::error('CRITICAL: NO STUDENTS PREPARED FOR INSERT!');
                Log::error('This indicates a serious problem with data extraction or validation.');
                Log::error('Header map: '.json_encode($headerMap));
                Log::error('Sample row data: '.json_encode($rows[0] ?? 'NO ROWS'));
                Log::error('========================================');
            }

            // Insert remaining students (chunks already inserted in loop via insertStudentChunk)
            if (! empty($studentsToInsert)) {
                Log::info('Preparing to insert '.count($studentsToInsert).' remaining students');
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    Log::error('Database connection failed: '.$e->getMessage());

                    return response()->json([
                        'success' => false,
                        'message' => 'Database connection error: '.$e->getMessage(),
                        'imported' => 0,
                        'skipped' => 0,
                    ], 500);
                }
                $this->insertStudentChunk($studentsToInsert, $imported, $skipped, $errors, $skipReasons, $genderCounts);
            } else {
                Log::warning('No students to insert after processing all rows');
            }

            // Get total gender counts for ALL students in database (not just imported)
            $totalGenderCounts = [
                'Male' => Student::where('gender', 'Male')->count(),
                'Female' => Student::where('gender', 'Female')->count(),
                'Other' => Student::where('gender', 'Other')->count(),
                'null' => Student::whereNull('gender')->count(),
                'total' => Student::count(),
            ];

            // If nothing was imported, return error with helpful info
            if ($imported === 0) {
                $errorMessage = 'No students were imported. ';
                if ($skipped > 0) {
                    $errorMessage .= "{$skipped} row(s) were skipped. ";
                }

                // Add skip reason breakdown
                $skipBreakdown = [];
                if ($skipReasons['missing_fields'] > 0) {
                    $skipBreakdown[] = $skipReasons['missing_fields'].' rows missing required fields';
                }
                if ($skipReasons['duplicate_in_file'] > 0) {
                    $skipBreakdown[] = $skipReasons['duplicate_in_file'].' rows with duplicate IDs in file';
                }
                if ($skipReasons['duplicate_in_db'] > 0) {
                    $skipBreakdown[] = $skipReasons['duplicate_in_db'].' rows with IDs already in database';
                }
                if ($skipReasons['empty_row'] > 0) {
                    $skipBreakdown[] = $skipReasons['empty_row'].' empty rows';
                }
                if ($skipReasons['other'] > 0) {
                    $skipBreakdown[] = $skipReasons['other'].' rows with other errors';
                }

                if (! empty($skipBreakdown)) {
                    $errorMessage .= 'Reasons: '.implode(', ', $skipBreakdown).'. ';
                }

                // Add diagnostic info
                if (! empty($studentsToInsert)) {
                    $errorMessage .= count($studentsToInsert).' students were prepared but failed to insert. ';
                } else {
                    $errorMessage .= 'No valid student data was found. ';
                }

                if (! empty($errors)) {
                    $errorMessage .= 'Sample errors: '.implode('; ', array_slice($errors, 0, 3));
                    if (count($errors) > 3) {
                        $errorMessage .= ' (see full error list below)';
                    }
                } else {
                    $errorMessage .= 'Please check your Excel file format. Required columns: Student ID, Campus, Last Name. Optional columns: Course, Year Level, Section.';
                }

                Log::warning("Import failed: {$errorMessage}");
                Log::warning('Skip reasons: '.json_encode($skipReasons));
                Log::warning('Header map: '.json_encode($headerMap));
                Log::warning('Students prepared: '.count($studentsToInsert));
                if (count($rows) > 0) {
                    Log::warning('Sample row data: '.json_encode($rows[0]));
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'imported' => 0,
                    'skipped' => $skipped,
                    'skip_reasons' => $skipReasons,
                    'errors' => array_slice($errors, 0, 50),
                    'students_prepared' => count($studentsToInsert),
                ], 422);
            }

            // Final summary with detailed statistics
            $totalRowsInFile = count($rows); // Including header
            $totalDataRows = $totalRowsInFile > 0 ? $totalRowsInFile - 1 : 0; // Excluding header
            $totalProcessed = $imported + $skipped;

            $message = "Import completed! {$imported} student(s) imported successfully.";
            if ($totalDataRows > 0) {
                $importRate = ($imported / $totalDataRows) * 100;
                $message .= " Processed {$totalDataRows} data row(s) from file (".number_format($importRate, 1).'% imported).';
            }

            if ($skipped > 0) {
                $message .= " {$skipped} row(s) were skipped.";

                // Add skip reason summary
                $reasonSummary = [];
                if ($skipReasons['missing_fields'] > 0) {
                    $reasonSummary[] = $skipReasons['missing_fields'].' missing required fields';
                }
                if ($skipReasons['duplicate_in_file'] > 0) {
                    $reasonSummary[] = $skipReasons['duplicate_in_file'].' duplicates in file';
                }
                if ($skipReasons['duplicate_in_db'] > 0) {
                    $reasonSummary[] = $skipReasons['duplicate_in_db'].' already exist in database';
                }
                if ($skipReasons['empty_row'] > 0) {
                    $reasonSummary[] = $skipReasons['empty_row'].' empty rows';
                }
                if ($skipReasons['other'] > 0) {
                    $reasonSummary[] = $skipReasons['other'].' other errors';
                }

                if (! empty($reasonSummary)) {
                    $message .= ' Reasons: '.implode(', ', $reasonSummary).'.';
                }
            }

            // Add warning if import rate is suspiciously low
            if ($totalDataRows > 10 && $imported < ($totalDataRows * 0.1)) {
                $message .= ' WARNING: Only '.number_format(($imported / $totalDataRows) * 100, 1).'% of rows were imported. Please check the errors below.';
            }

            // Add gender breakdown to message
            if ($imported > 0) {
                $genderBreakdown = [];
                if ($genderCounts['Male'] > 0) {
                    $genderBreakdown[] = $genderCounts['Male'].' Male';
                }
                if ($genderCounts['Female'] > 0) {
                    $genderBreakdown[] = $genderCounts['Female'].' Female';
                }
                if ($genderCounts['Other'] > 0) {
                    $genderBreakdown[] = $genderCounts['Other'].' Other';
                }
                if ($genderCounts['null'] > 0) {
                    $genderBreakdown[] = $genderCounts['null'].' (no gender specified)';
                }
                if (! empty($genderBreakdown)) {
                    $message .= ' ('.implode(', ', $genderBreakdown).')';
                }
            }

            Log::info("Final import summary: {$imported} imported, {$skipped} skipped out of {$totalDataRows} data rows, ".count($errors).' errors logged');
            Log::info('Gender counts (this import): '.json_encode($genderCounts));
            Log::info('Total gender counts (all students): '.json_encode($totalGenderCounts));
            Log::info('Import statistics: '.json_encode([
                'total_rows_in_file' => $totalRowsInFile,
                'data_rows' => $totalDataRows,
                'imported' => $imported,
                'skipped' => $skipped,
                'import_rate' => $totalDataRows > 0 ? number_format(($imported / $totalDataRows) * 100, 2).'%' : '0%',
                'skip_reasons' => $skipReasons,
                'gender_counts_imported' => $genderCounts,
                'total_gender_counts' => $totalGenderCounts,
            ]));

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $imported,
                'skipped' => $skipped,
                'total_rows_in_file' => $totalRowsInFile,
                'total_data_rows' => $totalDataRows,
                'total_processed' => $totalProcessed,
                'skip_reasons' => $skipReasons,
                'gender_counts' => $genderCounts, // Gender counts for this import
                'total_gender_counts' => $totalGenderCounts, // Total gender counts for ALL students
                'errors' => array_slice($errors, 0, 200), // Show more errors for debugging
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages = array_merge($errorMessages, $messages);
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed: '.implode(' ', $errorMessages),
                'errors' => $e->errors(),
                'imported' => 0,
                'skipped' => 0,
            ], 422);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Student import error: '.$e->getMessage());
            Log::error('File: '.$e->getFile().' Line: '.$e->getLine());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Import failed: '.$e->getMessage(),
                'imported' => 0,
                'skipped' => 0,
            ], 500);
        }
    }

    /**
     * Insert a batch of students in chunks. Uses bulk insert with fallback to
     * one-by-one on unique constraint or other DB errors. Works with MySQL and PostgreSQL.
     */
    private function insertStudentChunk(array $batch, int &$imported, int &$skipped, array &$errors, array &$skipReasons, ?array &$genderCounts = null): void
    {
        if (empty($batch)) {
            return;
        }

        // Initialize genderCounts if not provided
        if ($genderCounts === null) {
            $genderCounts = ['Male' => 0, 'Female' => 0, 'Other' => 0, 'null' => 0];
        }

        $dbChunkSize = 100;
        $isPostgres = DB::connection()->getDriverName() === 'pgsql';
        $chunks = array_chunk($batch, $dbChunkSize);

        Log::info('insertStudentChunk: Processing '.count($batch).' students in '.count($chunks).' DB chunk(s)');

        foreach ($chunks as $chunkIndex => $chunk) {
            try {
                DB::table('students')->insert($chunk);
                $imported += count($chunk);

                // Track gender counts for successfully inserted chunk
                foreach ($chunk as $studentData) {
                    $gender = $studentData['gender'] ?? null;
                    if ($gender === 'Male') {
                        $genderCounts['Male']++;
                    } elseif ($gender === 'Female') {
                        $genderCounts['Female']++;
                    } elseif ($gender === 'Other') {
                        $genderCounts['Other']++;
                    } else {
                        $genderCounts['null']++;
                    }
                }

                Log::info('Successfully bulk inserted chunk '.($chunkIndex + 1).': '.count($chunk)." students (total imported: {$imported})");
            } catch (\Illuminate\Database\QueryException $e) {
                // Log why bulk insert failed
                $errMsg = $e->getMessage();
                Log::warning('Bulk insert failed for chunk '.($chunkIndex + 1).' ('.count($chunk).' students): '.$errMsg);
                Log::info('Falling back to one-by-one insert for this chunk');

                // Fallback: insert one-by-one for this chunk
                foreach ($chunk as $studentData) {
                    try {
                        if ($isPostgres) {
                            DB::beginTransaction();
                        }
                        DB::table('students')->insert($studentData);
                        if ($isPostgres) {
                            DB::commit();
                        }
                        $imported++;

                        // Track gender count for successfully inserted student
                        $gender = $studentData['gender'] ?? null;
                        if ($gender === 'Male') {
                            $genderCounts['Male']++;
                        } elseif ($gender === 'Female') {
                            $genderCounts['Female']++;
                        } elseif ($gender === 'Other') {
                            $genderCounts['Other']++;
                        } else {
                            $genderCounts['null']++;
                        }
                    } catch (\Illuminate\Database\QueryException $qe) {
                        if ($isPostgres && DB::transactionLevel() > 0) {
                            DB::rollBack();
                        }
                        $skipped++;
                        $qeMsg = $qe->getMessage();
                        if (strpos($qeMsg, 'duplicate') !== false || strpos($qeMsg, 'unique') !== false || strpos($qeMsg, 'already exists') !== false) {
                            $skipReasons['duplicate_in_db']++;
                        } else {
                            $skipReasons['other']++;
                        }
                        if (count($errors) < 50) {
                            $errors[] = "Student ID '{$studentData['student_id_number']}': ".(strlen($qeMsg) > 120 ? substr($qeMsg, 0, 120).'...' : $qeMsg);
                        }
                        Log::error('Insert failed '.$studentData['student_id_number'].': '.$qeMsg);
                    } catch (\Exception $ex) {
                        if ($isPostgres && DB::transactionLevel() > 0) {
                            DB::rollBack();
                        }
                        $skipped++;
                        $skipReasons['other']++;
                        if (count($errors) < 50) {
                            $errors[] = "Student ID '{$studentData['student_id_number']}': ".$ex->getMessage();
                        }
                        Log::error('Insert failed '.$studentData['student_id_number'].': '.$ex->getMessage());
                    }
                }
                Log::info('Finished one-by-one fallback for chunk '.($chunkIndex + 1)." (QueryException) - imported: {$imported}, skipped: {$skipped} from this chunk");
            } catch (\Exception $e) {
                // Non-QueryException: fallback one-by-one
                foreach ($chunk as $studentData) {
                    try {
                        if ($isPostgres) {
                            DB::beginTransaction();
                        }
                        DB::table('students')->insert($studentData);
                        if ($isPostgres) {
                            DB::commit();
                        }
                        $imported++;

                        // Track gender count for successfully inserted student
                        $gender = $studentData['gender'] ?? null;
                        if ($gender === 'Male') {
                            $genderCounts['Male']++;
                        } elseif ($gender === 'Female') {
                            $genderCounts['Female']++;
                        } elseif ($gender === 'Other') {
                            $genderCounts['Other']++;
                        } else {
                            $genderCounts['null']++;
                        }
                    } catch (\Exception $ex) {
                        if ($isPostgres && DB::transactionLevel() > 0) {
                            DB::rollBack();
                        }
                        $skipped++;
                        $skipReasons['other']++;
                        if (count($errors) < 50) {
                            $errors[] = "Student ID '{$studentData['student_id_number']}': ".$ex->getMessage();
                        }
                        Log::error('Insert failed '.$studentData['student_id_number'].': '.$ex->getMessage());
                    }
                }
                Log::info('Finished one-by-one fallback for chunk '.($chunkIndex + 1)." (non-QueryException) - imported: {$imported}, skipped: {$skipped} from this chunk");
            }
        }

        Log::info('insertStudentChunk completed: imported='.$imported.', skipped='.$skipped.' from '.count($batch).' total students');
    }
}
