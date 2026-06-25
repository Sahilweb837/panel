<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('course');

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
                    ->orWhere('admission_no', 'like', '%'.$request->search.'%')
                    ->orWhere('roll_no', 'like', '%'.$request->search.'%')
                    ->orWhere('course_duration', 'like', '%'.$request->search.'%')
                    ->orWhere('class', 'like', '%'.$request->search.'%')
                    ->orWhere('section', 'like', '%'.$request->search.'%')
                    ->orWhereHas('course', function ($query) use ($request) {
                        $query->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('code', 'like', '%'.$request->search.'%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('course_duration')) {
            $query->where('course_duration', $request->course_duration);
        }

        $students = $query->latest()->paginate(12)->withQueryString();
        $courses = Course::where('status', true)->orderBy('name')->get();
        $durations = $this->courseDurations();

        $totalStudents = \App\Models\Student::count();
        $activeStudents = \App\Models\Student::where('status', true)->count();
        $inactiveStudents = \App\Models\Student::where('status', false)->count();
        $onlineStudents = \App\Models\Student::where('student_type', 'Online')->count();

        return view('students.index', compact('students', 'courses', 'durations', 'totalStudents', 'activeStudents', 'inactiveStudents', 'onlineStudents'));
    }

    public function create()
    {
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $nextId = $lastStudent ? $lastStudent->id + 1 : 1;

        $lastAdmissionStudent = Student::where('admission_no', 'like', 'NT-ENR-%')
            ->orderByRaw('CAST(SUBSTRING(admission_no, 8) AS UNSIGNED) DESC')
            ->first();

        if ($lastAdmissionStudent) {
            $lastNum = (int) str_replace('NT-ENR-', '', $lastAdmissionStudent->admission_no);
            $nextAdmissionNo = 'NT-ENR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextAdmissionNo = 'NT-ENR-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        }

        $lastRollStudent = Student::whereRaw('roll_no REGEXP "^[0-9]+$"')
            ->orderByRaw('CAST(roll_no AS UNSIGNED) DESC')
            ->first();

        if ($lastRollStudent) {
            $nextRollNo = (int) $lastRollStudent->roll_no + 1;
        } else {
            $nextRollNo = $nextId;
        }

        return view('students.create', [
            'courses' => Course::where('status', true)->orderBy('name')->get(),
            'durations' => $this->courseDurations(),
            'nextAdmissionNo' => $nextAdmissionNo,
            'nextRollNo' => $nextRollNo,
        ]);
    }

    public function store(Request $request)
    {
        // Auto-generate if not provided in the request before validation
        if (!$request->filled('admission_no')) {
            $lastStudent = Student::orderBy('id', 'desc')->first();
            $nextId = $lastStudent ? $lastStudent->id + 1 : 1;

            $lastAdmissionStudent = Student::where('admission_no', 'like', 'NT-ENR-%')
                ->orderByRaw('CAST(SUBSTRING(admission_no, 8) AS UNSIGNED) DESC')
                ->first();

            if ($lastAdmissionStudent) {
                $lastNum = (int) str_replace('NT-ENR-', '', $lastAdmissionStudent->admission_no);
                $nextAdmissionNo = 'NT-ENR-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextAdmissionNo = 'NT-ENR-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
            $request->merge(['admission_no' => $nextAdmissionNo]);
        }

        if (!$request->filled('roll_no')) {
            $lastStudent = Student::orderBy('id', 'desc')->first();
            $nextId = $lastStudent ? $lastStudent->id + 1 : 1;

            $lastRollStudent = Student::whereRaw('roll_no REGEXP "^[0-9]+$"')
                ->orderByRaw('CAST(roll_no AS UNSIGNED) DESC')
                ->first();

            if ($lastRollStudent) {
                $nextRollNo = (int) $lastRollStudent->roll_no + 1;
            } else {
                $nextRollNo = $nextId;
            }
            $request->merge(['roll_no' => $nextRollNo]);
        }

        $data = $request->validate([
            'admission_no' => ['required', 'string', 'max:50', 'unique:students,admission_no'],
            'roll_no' => ['required', 'string', 'max:50'],
            'aadhar_number' => ['nullable', 'digits:12'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', 'unique:students,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'address' => ['nullable', 'string'],
            'current_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'course_duration' => ['nullable', 'in:45 Days,1 Month,6 Months,1 Year'],
            'student_type' => ['required', 'in:Regular (On Campus),Regular (Internship),Online'],
            'class' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'status' => ['nullable', 'boolean'],
            'biometric_id' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'prospectus_fee' => ['nullable', 'numeric', 'min:0'],
            'fee_tenure' => ['nullable', 'in:1 Month,3 Months,6 Months,1 Year'],
        ]);

        $data['status'] = $request->boolean('status');

        $student = Student::create($data);

        // Auto-create user account
        $role = \App\Models\Role::where('slug', 'student')->first();
        if ($role) {
            $user = \App\Models\User::create([
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $student->email ?? ($student->admission_no . '@student.com'),
                'username' => strtolower(str_replace(' ', '', $student->first_name)) . $student->admission_no,
                'password' => \Illuminate\Support\Facades\Hash::make($student->dob ?? $student->admission_no),
                'role_id' => $role->id,
                'status' => true,
            ]);
            $student->user_id = $user->id;
            $student->save();
        }

        $includeRegistration = $request->boolean('include_registration_invoice');
        $includeProspectus = $request->boolean('include_prospectus_invoice');

        $course = $student->course;
        $courseFeeAmount = $course?->fee ?? 0;
        $discount = $student->discount ?? 0;
        $divisor = 1;
        $tenureLabel = '';

        if ($student->fee_tenure) {
            $durationLower = strtolower($student->course_duration ?? '');

            if (str_contains($durationLower, '1 year') || str_contains($durationLower, '12 month')) {
                if ($student->fee_tenure === '1 Month') $divisor = 12;
                elseif ($student->fee_tenure === '3 Months') $divisor = 4;
                elseif ($student->fee_tenure === '6 Months') $divisor = 2;
            } elseif (str_contains($durationLower, '6 month')) {
                if ($student->fee_tenure === '1 Month') $divisor = 6;
                elseif ($student->fee_tenure === '3 Months') $divisor = 2;
            } elseif (str_contains($durationLower, '3 month')) {
                if ($student->fee_tenure === '1 Month') $divisor = 3;
            }

            $courseFeeAmount = round($courseFeeAmount / $divisor, 2);
            $discount = round($discount / $divisor, 2);
            $tenureLabel = $student->fee_tenure;
        }

        $allFeeItems = [];
        $grandTotal = 0;

        if ($includeRegistration && ($student->registration_fee ?? 0) > 0) {
            $allFeeItems[] = ['category' => 'Registration Fee', 'amount' => (float) $student->registration_fee];
            $grandTotal += (float) $student->registration_fee;
        }

        if ($includeProspectus && ($student->prospectus_fee ?? 0) > 0) {
            $allFeeItems[] = ['category' => 'Prospectus Fee', 'amount' => (float) $student->prospectus_fee];
            $grandTotal += (float) $student->prospectus_fee;
        }

        if ($courseFeeAmount > 0 && $course) {
            $courseItemLabel = 'Course Fee';
            if ($tenureLabel) {
                $courseItemLabel = "Course Fee ({$tenureLabel} Installment)";
            }

            $netCourseFee = max(0, $courseFeeAmount - $discount);

            $allFeeItems[] = [
                'category' => $courseItemLabel,
                'amount' => $netCourseFee,
            ];
            $grandTotal += $netCourseFee;
        }

        if (!empty($allFeeItems)) {
            $categoryParts = array_column($allFeeItems, 'category');
            $feeCategory = implode(', ', $categoryParts);

            \App\Models\FeeInvoice::create([
                'student_id' => $student->id,
                'invoice_no' => 'ADM-' . now()->format('ymdHi') . '-' . $student->id,
                'fee_category' => $feeCategory,
                'fee_items' => $allFeeItems,
                'total_amount' => $grandTotal,
                'paid_amount' => 0,
                'discount' => 0,
                'fine' => 0,
                'due_amount' => $grandTotal,
                'status' => 'Unpaid',
                'created_by' => session('user_id'),
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Student created successfully. Login generated.');
    }

    public function show(Student $student)
    {
        $student->load(['course', 'user']);
        
        // Load recent attendance
        $attendances = \App\Models\Attendance::where('student_id', $student->id)
            ->latest('attendance_date')
            ->limit(10)
            ->get();
            
        // Load fee history
        $feeInvoices = \App\Models\FeeInvoice::where('student_id', $student->id)
            ->latest('created_at')
            ->get();
            
        $courseInvoices = $feeInvoices->whereNotIn('fee_category', ['Seminar', 'Fine']);
        $totalFees = $courseInvoices->sum('total_amount') - $courseInvoices->sum('discount');
        $paidFees = $courseInvoices->sum('paid_amount');
        $dueFees = max(0, $totalFees - $paidFees);
        
        $seminarInvoices = $feeInvoices->where('fee_category', 'Seminar');
        $totalSeminarFees = $seminarInvoices->sum('total_amount') - $seminarInvoices->sum('discount');
        $paidSeminarFees = $seminarInvoices->sum('paid_amount');
        $dueSeminarFees = max(0, $totalSeminarFees - $paidSeminarFees);
        
        $fineInvoices = $feeInvoices->where('fee_category', 'Fine');
        $totalFines = $fineInvoices->sum('total_amount') + $feeInvoices->sum('fine') - $fineInvoices->sum('discount');
        $paidFines = $fineInvoices->sum('paid_amount'); // Any fine invoice that is paid
        $dueFines = max(0, $totalFines - $paidFines);
        
        $totalAttendance = \App\Models\Attendance::where('student_id', $student->id)->count();
        $presentAttendance = \App\Models\Attendance::where('student_id', $student->id)->where('status', 'Present')->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100) : 0;
        
        return view('students.show', compact('student', 'attendances', 'feeInvoices', 'totalFees', 'paidFees', 'dueFees', 'totalSeminarFees', 'paidSeminarFees', 'dueSeminarFees', 'totalFines', 'paidFines', 'dueFines', 'attendancePercentage'));
    }

    public function feeReport(Student $student)
    {
        $student->load(['course']);
        
        $feeInvoices = \App\Models\FeeInvoice::where('student_id', $student->id)
            ->oldest('created_at')
            ->get();
            
        $courseInvoices = $feeInvoices->whereNotIn('fee_category', ['Seminar', 'Fine']);
        $totalFees = $courseInvoices->sum('total_amount') - $courseInvoices->sum('discount');
        $paidFees = $courseInvoices->sum('paid_amount');
        $dueFees = max(0, $totalFees - $paidFees);
        
        $seminarInvoices = $feeInvoices->where('fee_category', 'Seminar');
        $totalSeminarFees = $seminarInvoices->sum('total_amount') - $seminarInvoices->sum('discount');
        $paidSeminarFees = $seminarInvoices->sum('paid_amount');
        $dueSeminarFees = max(0, $totalSeminarFees - $paidSeminarFees);
        
        $fineInvoices = $feeInvoices->where('fee_category', 'Fine');
        $totalFines = $fineInvoices->sum('total_amount') + $feeInvoices->sum('fine') - $fineInvoices->sum('discount');
        $paidFines = $fineInvoices->sum('paid_amount'); 
        $dueFines = max(0, $totalFines - $paidFines);
        
        return view('students.fee_report', compact('student', 'feeInvoices', 'totalFees', 'paidFees', 'dueFees', 'totalSeminarFees', 'paidSeminarFees', 'dueSeminarFees', 'totalFines', 'paidFines', 'dueFines'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', [
            'student' => $student,
            'courses' => Course::where('status', true)->orderBy('name')->get(),
            'durations' => $this->courseDurations(),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'admission_no' => ['required', 'string', 'max:50', 'unique:students,admission_no,'.$student->id],
            'roll_no' => ['nullable', 'string', 'max:50'],
            'aadhar_number' => ['nullable', 'digits:12'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'guardian_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100', 'unique:students,email,'.$student->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'address' => ['nullable', 'string'],
            'current_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'course_duration' => ['nullable', 'in:45 Days,1 Month,6 Months,1 Year'],
            'student_type' => ['required', 'in:Regular (On Campus),Regular (Internship),Online'],
            'class' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:50'],
            'admission_date' => ['nullable', 'date'],
            'status' => ['nullable', 'boolean'],
            'biometric_id' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'prospectus_fee' => ['nullable', 'numeric', 'min:0'],
            'fee_tenure' => ['nullable', 'in:1 Month,3 Months,6 Months,1 Year'],
        ]);

        $data['status'] = $request->boolean('status');

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return back()->with('success', 'Student deleted successfully.');
    }

    public function restore($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        return back()->with('success', 'Student restored successfully.');
    }

    public function exportCsv(Request $request)
    {
        $query = Student::with('course');

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
                    ->orWhere('admission_no', 'like', '%'.$request->search.'%')
                    ->orWhere('roll_no', 'like', '%'.$request->search.'%')
                    ->orWhere('course_duration', 'like', '%'.$request->search.'%')
                    ->orWhere('class', 'like', '%'.$request->search.'%')
                    ->orWhere('section', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('course_duration')) {
            $query->where('course_duration', $request->course_duration);
        }

        $students = $query->latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=students_export_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Admission No', 'Roll No', 'Full Name', 'Email', 'Phone', 'Course', 'Duration', 'Type', 'Status', 'Admission Date']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_no,
                    $student->roll_no ?? '-',
                    $student->first_name . ' ' . $student->last_name,
                    $student->email ?? '-',
                    $student->phone ?? '-',
                    $student->course?->name ?? '-',
                    $student->course_duration ?? '-',
                    $student->student_type ?? '-',
                    $student->status ? 'Active' : 'Inactive',
                    $student->admission_date ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Student::with('course');

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
                    ->orWhere('admission_no', 'like', '%'.$request->search.'%')
                    ->orWhere('roll_no', 'like', '%'.$request->search.'%')
                    ->orWhere('course_duration', 'like', '%'.$request->search.'%')
                    ->orWhere('class', 'like', '%'.$request->search.'%')
                    ->orWhere('section', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('course_duration')) {
            $query->where('course_duration', $request->course_duration);
        }

        $students = $query->latest()->get();

        return view('students.print', compact('students'));
    }

    private function courseDurations(): array
    {
        return ['45 Days', '1 Month', '6 Months', '1 Year'];
    }
}
