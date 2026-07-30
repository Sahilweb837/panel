<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeAndVerifyMail;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\FeeInvoice;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

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

        $totalStudents = Student::count();
        $activeStudents = Student::where('status', true)->count();
        $inactiveStudents = Student::where('status', false)->count();
        $onlineStudents = Student::where('student_type', 'Online')->count();

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
            $nextAdmissionNo = 'NT-ENR-'.str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextAdmissionNo = 'NT-ENR-'.str_pad($nextId, 3, '0', STR_PAD_LEFT);
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
        if (! $request->filled('admission_no')) {
            $lastStudent = Student::orderBy('id', 'desc')->first();
            $nextId = $lastStudent ? $lastStudent->id + 1 : 1;

            $lastAdmissionStudent = Student::where('admission_no', 'like', 'NT-ENR-%')
                ->orderByRaw('CAST(SUBSTRING(admission_no, 8) AS UNSIGNED) DESC')
                ->first();

            if ($lastAdmissionStudent) {
                $lastNum = (int) str_replace('NT-ENR-', '', $lastAdmissionStudent->admission_no);
                $nextAdmissionNo = 'NT-ENR-'.str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextAdmissionNo = 'NT-ENR-'.str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
            $request->merge(['admission_no' => $nextAdmissionNo]);
        }

        if (! $request->filled('roll_no')) {
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
        if (! $request->filled('first_name')) {
            $request->merge(['first_name' => 'Student-'.str_replace('NT-ENR-', '', $request->admission_no ?? rand(100, 999))]);
        }

        if (! $request->filled('gender')) {
            $request->merge(['gender' => 'Male']);
        }

        if (! $request->filled('student_type')) {
            $request->merge(['student_type' => 'Regular (On Campus)']);
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
            'portal_active' => ['nullable', 'boolean'],
            'biometric_id' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'prospectus_fee' => ['nullable', 'numeric', 'min:0'],
            'fee_tenure' => ['nullable', 'in:1 Month,3 Months,6 Months,1 Year'],
            'login_username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'login_email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'login_password' => ['nullable', 'string', 'min:6'],
        ]);

        $data['status'] = $request->boolean('status');
        $data['portal_active'] = $request->boolean('portal_active');

        $student = Student::create($data);

        // Auto-create user account
        $role = Role::where('slug', 'student')->first();
        if ($role) {
            $username = $request->login_username ?: (strtolower(str_replace(' ', '', $student->first_name)).$student->admission_no);
            $email = $request->login_email ?: ($student->email ?: ($student->admission_no.'@student.com'));
            $plainPassword = $request->login_password ?: ($student->dob ? Carbon::parse($student->dob)->format('dmY') : $student->admission_no);
            $password = Hash::make($plainPassword);

            $user = User::create([
                'name' => trim($student->first_name.' '.$student->last_name),
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'raw_password' => $plainPassword,
                'role_id' => $role->id,
                'status' => true,
            ]);
            $student->user_id = $user->id;
            $student->save();
        }

        $includeRegistration = $request->boolean('include_registration_invoice');
        $includeProspectus = $request->boolean('include_prospectus_invoice');

        $course = Course::find($student->course_id);
        $courseFeeAmount = $course?->fee ?? 0;
        $discount = $student->discount ?? 0;
        $grandTotal = 0;

        $feeItems = [];

        if ($includeRegistration && ($student->registration_fee ?? 0) > 0) {
            $feeItems[] = ['category' => 'Registration Fee', 'amount' => (float) $student->registration_fee];
            $grandTotal += (float) $student->registration_fee;
        }

        if ($includeProspectus && ($student->prospectus_fee ?? 0) > 0) {
            $feeItems[] = ['category' => 'Prospectus Fee', 'amount' => (float) $student->prospectus_fee];
            $grandTotal += (float) $student->prospectus_fee;
        }
        // Note: Course Fee is excluded from the first invoice because the student pays it monthly.
        // The first invoice is strictly for Prospectus and/or Registration fees.
        if (! empty($feeItems)) {
            $feeCategory = implode(', ', array_column($feeItems, 'category'));

            FeeInvoice::create([
                'student_id' => $student->id,
                'invoice_no' => 'ADM-'.now()->format('ymdHi').'-'.$student->id,
                'fee_category' => $feeCategory,
                'fee_items' => $feeItems,
                'total_amount' => $grandTotal,
                'paid_amount' => 0,
                'discount' => 0, // No discount on Prospectus/Registration
                'fine' => 0,
                'due_amount' => $grandTotal,
                'status' => 'Unpaid',
                'created_by' => session('user_id'),
            ]);
        }

        if (isset($user)) {
            // Dispatch welcome and verification email if it's a real email
            if (! str_ends_with($user->email, '@student.com')) {
                try {
                    Mail::to($user->email)->send(new WelcomeAndVerifyMail($user, $plainPassword));
                } catch (\Exception $e) {
                    Log::error('Failed to send student welcome email: '.$e->getMessage());
                }
            }

            return redirect()->route('students.index')->with([
                'success' => 'Student created successfully.',
                'new_user_credentials' => [
                    'email' => $user->email,
                    'username' => $user->username,
                    'password' => $plainPassword,
                    'type' => 'Student',
                ],
            ]);
        }

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['course', 'user']);

        // Load recent attendance
        $attendances = Attendance::where('student_id', $student->id)
            ->latest('attendance_date')
            ->limit(10)
            ->get();

        // Load fee history
        $feeInvoices = FeeInvoice::where('student_id', $student->id)
            ->latest('created_at')
            ->get();

        $courseInvoices = $feeInvoices->filter(function ($invoice) {
            $cat = strtolower($invoice->fee_category ?? '');

            return ! str_contains($cat, 'registration') &&
                   ! str_contains($cat, 'prospectus') &&
                   ! str_contains($cat, 'seminar') &&
                   ! str_contains($cat, 'fine');
        });

        $totalCourseFee = $student->course?->fee ?? 0;
        $discount = $student->discount ?? 0;
        $netCourseFee = max(0, $totalCourseFee - $discount);

        $paidFees = $courseInvoices->sum('paid_amount');
        $dueFees = max(0, $netCourseFee - $paidFees);
        $totalFees = $netCourseFee;

        $seminarInvoices = $feeInvoices->where('fee_category', 'Seminar');
        $totalSeminarFees = $seminarInvoices->sum('total_amount') - $seminarInvoices->sum('discount');
        $paidSeminarFees = $seminarInvoices->sum('paid_amount');
        $dueSeminarFees = max(0, $totalSeminarFees - $paidSeminarFees);

        $fineInvoices = $feeInvoices->where('fee_category', 'Fine');
        $totalFines = $fineInvoices->sum('total_amount') - $fineInvoices->sum('discount');
        $paidFines = $fineInvoices->sum('paid_amount');
        $dueFines = max(0, $totalFines - $paidFines);

        $totalAttendance = Attendance::where('student_id', $student->id)->count();
        $presentAttendance = Attendance::where('student_id', $student->id)->where('status', 'Present')->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100) : 0;

        return view('students.show', compact('student', 'attendances', 'feeInvoices', 'totalFees', 'paidFees', 'dueFees', 'totalSeminarFees', 'paidSeminarFees', 'dueSeminarFees', 'totalFines', 'paidFines', 'dueFines', 'attendancePercentage'));
    }

    public function feeReport(Student $student)
    {
        $student->load(['course']);

        $feeInvoices = FeeInvoice::where('student_id', $student->id)
            ->oldest('created_at')
            ->get();

        $courseInvoices = $feeInvoices->filter(function ($invoice) {
            $cat = strtolower($invoice->fee_category ?? '');

            return ! str_contains($cat, 'registration') &&
                   ! str_contains($cat, 'prospectus') &&
                   ! str_contains($cat, 'seminar') &&
                   ! str_contains($cat, 'fine');
        });

        $totalCourseFee = $student->course?->fee ?? 0;
        $discount = $student->discount ?? 0;
        $netCourseFee = max(0, $totalCourseFee - $discount);

        $paidFees = $courseInvoices->sum('paid_amount');
        $dueFees = max(0, $netCourseFee - $paidFees);
        $totalFees = $netCourseFee;

        $seminarInvoices = $feeInvoices->where('fee_category', 'Seminar');
        $totalSeminarFees = $seminarInvoices->sum('total_amount') - $seminarInvoices->sum('discount');
        $paidSeminarFees = $seminarInvoices->sum('paid_amount');
        $dueSeminarFees = max(0, $totalSeminarFees - $paidSeminarFees);

        $fineInvoices = $feeInvoices->where('fee_category', 'Fine');
        $totalFines = $fineInvoices->sum('total_amount') - $fineInvoices->sum('discount');
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
            'portal_active' => ['nullable', 'boolean'],
            'biometric_id' => ['nullable', 'string', 'max:50'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'prospectus_fee' => ['nullable', 'numeric', 'min:0'],
            'fee_tenure' => ['nullable', 'in:1 Month,3 Months,6 Months,1 Year'],
            'login_username' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('users', 'username')->ignore($student->user_id),
            ],
            'login_email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'login_password' => ['nullable', 'string', 'min:6'],
        ]);

        $data['status'] = $request->boolean('status');
        $data['portal_active'] = $request->boolean('portal_active');

        $student->update($data);

        if ($student->user) {
            $userData = [
                'name' => trim($student->first_name.' '.$student->last_name),
            ];

            if ($request->filled('login_username')) {
                $userData['username'] = $request->login_username;
            } elseif ($request->filled('admission_no')) {
                $userData['username'] = strtolower(str_replace(' ', '', $student->first_name)).$student->admission_no;
            }

            if ($request->filled('login_email')) {
                $userData['email'] = $request->login_email;
            } elseif ($request->filled('email')) {
                $userData['email'] = $student->email;
            }

            if ($request->filled('login_password')) {
                $userData['password'] = Hash::make($request->login_password);
                $userData['raw_password'] = $request->login_password;
            }

            $student->user->update($userData);
        }

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
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Admission No', 'Roll No', 'Full Name', 'Email', 'Phone', 'Course', 'Duration', 'Type', 'Status', 'Admission Date']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->admission_no,
                    $student->roll_no ?? '-',
                    $student->first_name.' '.$student->last_name,
                    $student->email ?? '-',
                    $student->phone ?? '-',
                    $student->course?->name ?? '-',
                    $student->course_duration ?? '-',
                    $student->student_type ?? '-',
                    $student->status ? 'Active' : 'Inactive',
                    $student->admission_date ?? '-',
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
