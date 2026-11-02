<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FeeInvoice;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LmsController extends Controller
{
    /**
     * Display the LMS Management Portal inside Panel.
     */
    public function index(Request $request)
    {
        $courses = Course::where('status', true)->withCount('students')->get();

        $query = Student::with(['course', 'user', 'feeInvoices' => function ($q) {
            $q->latest();
        }]);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                  ->orWhere('last_name', 'like', "%{$s}%")
                  ->orWhere('admission_no', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $students = $query->orderBy('id', 'desc')->paginate(15);

        // LMS Overview Stats
        $totalEnrolled = Student::whereNotNull('course_id')->count();
        $activePortalUsers = Student::where('portal_active', true)->count();
        $totalLmsRevenue = FeeInvoice::where('fee_category', 'like', '%LMS%')->sum('paid_amount');
        if ($totalLmsRevenue == 0) {
            $totalLmsRevenue = FeeInvoice::sum('paid_amount');
        }
        $pendingLmsFees = FeeInvoice::where('due_amount', '>', 0)->sum('due_amount');

        return view('lms.index', compact(
            'courses',
            'students',
            'totalEnrolled',
            'activePortalUsers',
            'totalLmsRevenue',
            'pendingLmsFees'
        ));
    }

    /**
     * One-Click Enroll Student into LMS Course + Automatically Generate Fee Invoice
     */
    public function enrollAndRegisterFee(Request $request)
    {
        $request->validate([
            'student_type'    => 'required|in:existing,new',
            'student_id'      => 'required_if:student_type,existing|nullable|exists:students,id',
            'first_name'      => 'required_if:student_type,new|nullable|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'email'           => 'required_if:student_type,new|nullable|email',
            'phone'           => 'nullable|string|max:20',
            'course_id'       => 'required|exists:courses,id',
            'course_fee'      => 'required|numeric|min:0',
            'paid_amount'     => 'nullable|numeric|min:0',
            'payment_method'  => 'nullable|string',
            'transaction_id'  => 'nullable|string',
            'tenure'          => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $course = Course::findOrFail($request->course_id);
            $totalFee = (float)$request->course_fee;
            $paidAmount = (float)($request->paid_amount ?? 0);
            $dueAmount = max(0, $totalFee - $paidAmount);
            $status = $dueAmount <= 0 ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Unpaid');

            if ($request->student_type === 'existing') {
                $student = Student::findOrFail($request->student_id);
                $student->course_id = $course->id;
                $student->portal_active = true;
                if ($request->filled('tenure')) {
                    $student->fee_tenure = $request->tenure;
                }
                $student->save();

                // Ensure user status is active
                if ($student->user) {
                    $student->user->update(['status' => true]);
                }
            } else {
                // Register Brand New Student & User Account
                $role = Role::firstOrCreate(['slug' => 'student'], ['role_name' => 'Student']);
                $firstName = trim($request->first_name);
                $lastName = trim($request->last_name ?? '');
                $fullName = trim($firstName . ' ' . $lastName);
                $admissionNo = 'NT-ENR-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $cleanCode = str_ireplace(['NT-ENR-', 'NT-'], '', $admissionNo);

                $email = $request->email ?: (strtolower(str_replace(' ', '', $firstName)) . $cleanCode . '@student.com');
                $username = strtolower(str_replace(' ', '', $firstName)) . $cleanCode;
                $password = Hash::make(strtolower($firstName) . '123');

                $user = User::create([
                    'name'     => $fullName,
                    'email'    => $email,
                    'username' => $username,
                    'password' => $password,
                    'role_id'  => $role->id,
                    'status'   => true,
                ]);

                $student = Student::create([
                    'user_id'         => $user->id,
                    'first_name'      => $firstName,
                    'last_name'       => $lastName,
                    'email'           => $email,
                    'phone'           => $request->phone,
                    'admission_no'    => $admissionNo,
                    'roll_no'         => $cleanCode,
                    'course_id'       => $course->id,
                    'admission_date'  => now()->toDateString(),
                    'fee_tenure'      => $request->tenure ?? '1 Year',
                    'status'          => 1,
                    'portal_active'   => true,
                ]);
            }

            // AUTOMATIC FEE INVOICE CREATION
            $invoiceNo = 'INV-LMS-' . date('Ymd') . '-' . mt_rand(1000, 9999);
            $invoice = FeeInvoice::create([
                'student_id'      => $student->id,
                'invoice_no'      => $invoiceNo,
                'fee_category'    => 'LMS Course Enrollment (' . $course->name . ')',
                'fee_items'       => [
                    [
                        'name'   => $course->name . ' - LMS Curriculum',
                        'amount' => $totalFee,
                    ]
                ],
                'billing_month'   => now()->format('F'),
                'billing_year'    => (int)now()->format('Y'),
                'total_amount'    => $totalFee,
                'paid_amount'     => $paidAmount,
                'discount_amount' => 0,
                'fine_amount'     => 0,
                'due_amount'      => $dueAmount,
                'status'          => $status,
                'payment_date'    => $paidAmount > 0 ? now()->toDateString() : null,
                'payment_method'  => $request->payment_method ?? 'Online LMS',
                'transaction_id'  => $request->transaction_id ?? null,
                'remarks'         => $request->notes ?? 'Auto-generated upon LMS Course Enrollment',
                'created_by'      => session('user_id'),
            ]);

            AdminActivityLog::record(
                "Enrolled {$student->first_name} {$student->last_name} into {$course->name} with Auto Invoice {$invoiceNo}",
                session('user_id'),
                session('user_name'),
                session('user_role_slug')
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'      => true,
                    'message'      => "Enrolled successfully! Invoice #{$invoiceNo} generated automatically.",
                    'student_id'   => $student->id,
                    'invoice_id'   => $invoice->id,
                    'invoice_no'   => $invoiceNo,
                    'fee_status'   => $status,
                ]);
            }

            return back()->with('success', "Student enrolled in {$course->name} successfully! Automatic Fee Invoice #{$invoiceNo} generated.");
        });
    }

    /**
     * Toggle student's LMS Portal access on/off
     */
    public function togglePortal($id)
    {
        $student = Student::findOrFail($id);
        $student->portal_active = !$student->portal_active;
        $student->save();

        if ($student->user) {
            $student->user->status = $student->portal_active;
            $student->user->save();
        }

        $state = $student->portal_active ? 'Activated' : 'Suspended';
        return back()->with('success', "LMS Portal access {$state} for {$student->first_name}.");
    }
}

