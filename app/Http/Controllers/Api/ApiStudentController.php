<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use App\Models\Course;
use App\Models\FeeInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ApiStudentController extends Controller
{
    /**
     * Display a listing of students with filters & pagination.
     */
    public function index(Request $request)
    {
        $query = Student::with(['course:id,name,code,duration,fee', 'user:id,name,email,username,status']);

        // Search filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('admission_no', 'like', "%{$s}%")
                  ->orWhere('roll_no', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', (int)$request->status);
        }

        // Course filter
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $perPage = min(max((int)$request->input('per_page', 20), 5), 100);
        $students = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $students->items(),
            'pagination' => [
                'current_page' => $students->currentPage(),
                'last_page'    => $students->lastPage(),
                'per_page'     => $students->perPage(),
                'total'        => $students->total(),
            ]
        ]);
    }

    /**
     * Display the specified student.
     */
    public function show($id)
    {
        $student = Student::with([
            'course',
            'user:id,name,email,username,status,profile_pic',
            'attendances' => function ($q) {
                $q->latest('attendance_date')->limit(30);
            },
            'feeInvoices' => function ($q) {
                $q->latest()->limit(10);
            }
        ])->find($id);

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        // Compute fee summaries
        $totalBilled = FeeInvoice::where('student_id', $student->id)->sum('total_amount');
        $totalPaid = FeeInvoice::where('student_id', $student->id)->sum('paid_amount');
        $totalDue = FeeInvoice::where('student_id', $student->id)->sum('due_amount');

        // Compute attendance summary
        $totalClasses = $student->attendances()->count();
        $presentCount = $student->attendances()->whereIn('status', ['Present', 'Late'])->count();
        $attendanceRate = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data'    => $student,
            'metrics' => [
                'total_billed'    => (float)$totalBilled,
                'total_paid'      => (float)$totalPaid,
                'total_due'       => (float)$totalDue,
                'attendance_rate' => $attendanceRate,
                'total_classes'   => $totalClasses,
                'present_classes' => $presentCount,
            ]
        ]);
    }

    /**
     * Store a new student along with their login account.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'email'        => 'nullable|email|unique:students,email',
            'phone'        => 'nullable|string|max:20',
            'course_id'    => 'nullable|exists:courses,id',
            'admission_no' => 'nullable|string|unique:students,admission_no',
            'roll_no'      => 'nullable|string',
            'dob'          => 'nullable|date',
            'gender'       => 'nullable|in:Male,Female,Other',
            'status'       => 'nullable|boolean',
            'password'     => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            $studentRole = Role::firstOrCreate(['slug' => 'student'], [
                'role_name'   => 'Student',
                'description' => 'Enrolled Student Account'
            ]);

            $admissionNo = $request->admission_no ?: 'NT-ENR-'.str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $cleanCode = str_ireplace(['NT-ENR-', 'NT-'], '', $admissionNo);

            $firstName = trim($request->first_name);
            $lastName = trim($request->last_name ?? '');
            $fullName = trim($firstName.' '.$lastName);

            $email = $request->email ?: (strtolower(str_replace(' ', '', $firstName)).$cleanCode.'@student.com');
            $username = strtolower(str_replace(' ', '', $firstName)).$cleanCode;

            $plainPassword = $request->password ?: ($request->dob ? Carbon::parse($request->dob)->format('dmY') : (strtolower($firstName).'123'));

            // Create User
            $user = User::create([
                'name'     => $fullName,
                'email'    => $email,
                'username' => $username,
                'password' => Hash::make($plainPassword),
                'role_id'  => $studentRole->id,
                'status'   => $request->input('status', true),
            ]);

            // Create Student
            $student = Student::create([
                'user_id'         => $user->id,
                'first_name'      => $firstName,
                'last_name'       => $lastName,
                'email'           => $email,
                'phone'           => $request->phone,
                'admission_no'    => $admissionNo,
                'roll_no'         => $request->roll_no ?: $cleanCode,
                'course_id'       => $request->course_id,
                'dob'             => $request->dob,
                'gender'          => $request->gender,
                'admission_date'  => $request->input('admission_date', now()->toDateString()),
                'status'          => $request->input('status', true) ? 1 : 0,
                'portal_active'   => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully.',
                'data'    => $student->load('user', 'course'),
                'account' => [
                    'username' => $username,
                    'email'    => $email,
                    'admission_no' => $admissionNo,
                ]
            ], 201);
        });
    }

    /**
     * Update an existing student.
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'nullable|email|unique:students,email,'.$student->id,
            'phone'      => 'nullable|string|max:20',
            'course_id'  => 'nullable|exists:courses,id',
            'status'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $student->update($request->only([
            'first_name', 'last_name', 'email', 'phone', 'course_id',
            'address', 'dob', 'gender', 'status', 'portal_active'
        ]));

        if ($student->user) {
            $userUpdates = [];
            if ($request->filled('first_name') || $request->filled('last_name')) {
                $userUpdates['name'] = trim(($request->first_name ?? $student->first_name).' '.($request->last_name ?? $student->last_name));
            }
            if ($request->filled('email')) {
                $userUpdates['email'] = $request->email;
            }
            if ($request->has('status')) {
                $userUpdates['status'] = $request->status;
            }
            if (!empty($userUpdates)) {
                $student->user->update($userUpdates);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data'    => $student->load('course', 'user')
        ]);
    }

    /**
     * Synchronize online enrollment from frontend LMS (laravel-sahil).
     */
    public function syncEnrollment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_name'   => 'required|string|max:150',
            'email'          => 'required|email',
            'phone'          => 'nullable|string|max:20',
            'course_name'    => 'nullable|string',
            'course_id'      => 'nullable',
            'fee_paid'       => 'nullable|numeric|min:0',
            'fee_total'      => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            $email = trim($request->email);
            $fullName = trim($request->student_name);
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            // 1. Resolve or create course
            $courseId = $request->course_id;
            if (!$courseId && $request->filled('course_name')) {
                $c = Course::where('name', 'like', "%{$request->course_name}%")->first();
                if ($c) {
                    $courseId = $c->id;
                } else {
                    $c = Course::create([
                        'name'     => $request->course_name,
                        'code'     => 'CRS-'.strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $request->course_name), 0, 4)).mt_rand(10, 99),
                        'duration' => '3 Months',
                        'fee'      => $request->fee_total ?? 0,
                        'status'   => true,
                    ]);
                    $courseId = $c->id;
                }
            }

            // 2. Find or create student
            $student = Student::where('email', $email)->first();
            $studentRole = Role::firstOrCreate(['slug' => 'student'], ['role_name' => 'Student']);

            if (!$student) {
                $admissionNo = 'NT-ENR-'.str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $cleanCode = str_ireplace(['NT-ENR-', 'NT-'], '', $admissionNo);
                $username = strtolower(str_replace(' ', '', $firstName)).$cleanCode;

                // Create user
                $user = User::create([
                    'name'     => $fullName,
                    'email'    => $email,
                    'username' => $username,
                    'password' => Hash::make(strtolower($firstName).'123'),
                    'role_id'  => $studentRole->id,
                    'status'   => true,
                ]);

                $student = Student::create([
                    'user_id'        => $user->id,
                    'first_name'     => $firstName,
                    'last_name'      => $lastName,
                    'email'          => $email,
                    'phone'          => $request->phone,
                    'admission_no'   => $admissionNo,
                    'roll_no'        => $cleanCode,
                    'course_id'      => $courseId,
                    'admission_date' => now()->toDateString(),
                    'status'         => 1,
                    'portal_active'  => true,
                ]);
            } else {
                if ($courseId) {
                    $student->course_id = $courseId;
                }
                if ($request->filled('phone')) {
                    $student->phone = $request->phone;
                }
                $student->save();
            }

            // 3. Create fee invoice if fee details are present
            $totalFee = (float)($request->fee_total ?? 0);
            $paidFee = (float)($request->fee_paid ?? 0);
            $dueFee = max(0, $totalFee - $paidFee);
            $status = $dueFee <= 0 ? 'Paid' : ($paidFee > 0 ? 'Partial' : 'Unpaid');

            $invoice = FeeInvoice::create([
                'student_id'     => $student->id,
                'invoice_no'     => 'INV-LMS-'.date('Ymd').'-'.mt_rand(1000, 9999),
                'fee_category'   => 'Course Tuition (Online Enrollment)',
                'fee_items'      => [
                    [
                        'name'   => $request->course_name ?? 'Course Enrollment',
                        'amount' => $totalFee
                    ]
                ],
                'billing_month'  => now()->format('F'),
                'billing_year'   => (int)now()->format('Y'),
                'total_amount'   => $totalFee,
                'paid_amount'    => $paidFee,
                'discount_amount'=> 0,
                'fine_amount'    => 0,
                'due_amount'     => $dueFee,
                'status'         => $status,
                'payment_date'   => $paidFee > 0 ? now()->toDateString() : null,
                'payment_method' => $request->payment_method ?? 'Online LMS',
                'transaction_id' => $request->transaction_id ?? null,
                'remarks'        => $request->notes ?? 'Synced from Online LMS Frontend',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Online student enrollment synced successfully.',
                'data'    => [
                    'student_id'   => $student->id,
                    'admission_no' => $student->admission_no,
                    'invoice_id'   => $invoice->id,
                    'invoice_no'   => $invoice->invoice_no,
                    'fee_status'   => $status,
                ]
            ]);
        });
    }

    /**
     * Remove the specified student (soft delete).
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        $student->delete();
        if ($student->user) {
            $student->user->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Student archived successfully.'
        ]);
    }
}

