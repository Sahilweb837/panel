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
        return view('students.create', [
            'courses' => Course::where('status', true)->orderBy('name')->get(),
            'durations' => $this->courseDurations(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'admission_no' => ['required', 'string', 'max:50', 'unique:students,admission_no'],
            'roll_no' => ['nullable', 'string', 'max:50'],
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

        // Auto-generate Fee Invoice
        $feeItems = [];
        $totalAmount = 0;

        if ($student->course && $student->course->fee > 0) {
            $courseFeeAmount = $student->course->fee;
            $feeLabel = 'Course Fee';
            
            if ($student->fee_tenure) {
                $divisor = 1;
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
                $feeLabel = 'Course Fee (' . $student->fee_tenure . ' Installment)';
            }
            
            $feeItems[] = [
                'category' => $feeLabel,
                'amount' => $courseFeeAmount,
            ];
            $totalAmount += $courseFeeAmount;
        }

        if ($student->registration_fee > 0) {
            $feeItems[] = [
                'category' => 'Registration Fee',
                'amount' => $student->registration_fee,
            ];
            $totalAmount += $student->registration_fee;
        }

        if ($student->prospectus_fee > 0) {
            $feeItems[] = [
                'category' => 'Prospectus Fee',
                'amount' => $student->prospectus_fee,
            ];
            $totalAmount += $student->prospectus_fee;
        }

        if (!empty($feeItems)) {
            $discount = $student->discount ?? 0;
            $dueAmount = max(0, $totalAmount - $discount);

            $categories = array_column($feeItems, 'category');
            $feeCategory = implode(', ', array_slice($categories, 0, 3));
            if (count($categories) > 3) {
                $feeCategory .= '...';
            }

            \App\Models\FeeInvoice::create([
                'student_id' => $student->id,
                'invoice_no' => 'INV-' . now()->format('ymdHi') . '-' . $student->id,
                'fee_category' => $feeCategory,
                'fee_items' => $feeItems,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'discount' => $discount,
                'fine' => 0,
                'due_amount' => $dueAmount,
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
            
        // Analytics
        $totalFees = $feeInvoices->sum('total_amount') + $feeInvoices->sum('fine') - $feeInvoices->sum('discount');
        $paidFees = $feeInvoices->sum('paid_amount');
        $dueFees = max(0, $totalFees - $paidFees);
        
        $totalAttendance = \App\Models\Attendance::where('student_id', $student->id)->count();
        $presentAttendance = \App\Models\Attendance::where('student_id', $student->id)->where('status', 'Present')->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100) : 0;
        
        return view('students.show', compact('student', 'attendances', 'feeInvoices', 'totalFees', 'paidFees', 'dueFees', 'attendancePercentage'));
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
