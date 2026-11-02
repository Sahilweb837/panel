<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Cohort;
use App\Models\Masterclass;
use App\Models\StudentCourseProgress;
use App\Models\FeeInvoice;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LmsController extends Controller
{
    /**
     * Display the Full LMS Management Suite:
     * Enrollments & Auto-Fees, Cohorts, Masterclasses, Course Publishing, and Performance Tracking.
     */
    public function index(Request $request)
    {
        // 1. All Master Courses with student counts
        $courses = Course::withCount('students')->orderBy('name', 'asc')->get();

        // 2. Mentors / Instructors List
        $mentors = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['mentor', 'instructor', 'staff', 'admin', 'superadmin', 'super-admin']);
        })->orWhere('role_id', '!=', 3)->get();

        // 3. Cohorts / Batches
        $cohorts = Cohort::with(['course', 'mentor'])->latest()->get();

        // 4. Live Masterclasses
        $masterclasses = Masterclass::with(['course', 'instructor'])->latest('scheduled_at')->get();

        // 5. Enrolled Students with Fee History and Progress
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

        // 6. Student Progress records
        $progressRecords = StudentCourseProgress::with(['student', 'course', 'cohort'])->latest('updated_at')->take(50)->get();

        // LMS Overview Stats
        $totalEnrolled = Student::whereNotNull('course_id')->count();
        $activePortalUsers = Student::where('portal_active', true)->count();
        $totalCohorts = $cohorts->count();
        $totalMasterclasses = $masterclasses->count();
        $totalLmsRevenue = FeeInvoice::where('fee_category', 'like', '%LMS%')->sum('paid_amount');
        if ($totalLmsRevenue == 0) {
            $totalLmsRevenue = FeeInvoice::sum('paid_amount');
        }
        $pendingLmsFees = FeeInvoice::where('due_amount', '>', 0)->sum('due_amount');

        return view('lms.index', compact(
            'courses',
            'mentors',
            'cohorts',
            'masterclasses',
            'students',
            'progressRecords',
            'totalEnrolled',
            'activePortalUsers',
            'totalCohorts',
            'totalMasterclasses',
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

                if ($student->user) {
                    $student->user->update(['status' => true]);
                }
            } else {
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

            // Create initial progress tracker
            StudentCourseProgress::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id'  => $course->id,
                ],
                [
                    'completed_lessons'   => 0,
                    'total_lessons'       => 50,
                    'progress_percentage' => 0.00,
                    'grade'               => 'In Progress',
                    'is_certified'        => false,
                    'last_active_at'      => now(),
                ]
            );

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
                "Enrolled {$student->first_name} {$student->last_name} into {$course->name} with Auto-Fee Invoice {$invoiceNo}",
                session('user_id'),
                session('user_name'),
                session('user_role_slug')
            );

            return back()->with('success', "Student enrolled in {$course->name} successfully! Automatic Fee Invoice #{$invoiceNo} generated.");
        });
    }

    /**
     * Create Cohort / Batch
     */
    public function storeCohort(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:150',
            'course_id'  => 'required|exists:courses,id',
            'mentor_id'  => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
            'schedule'   => 'nullable|string|max:100',
            'capacity'   => 'nullable|integer|min:1',
            'status'     => 'nullable|in:draft,open,ongoing,completed',
        ]);

        $cohort = Cohort::create([
            'name'         => trim($request->name),
            'course_id'    => $request->course_id,
            'mentor_id'    => $request->mentor_id,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'schedule'     => $request->schedule ?? 'Mon-Fri 10:00 AM - 1:00 PM',
            'capacity'     => $request->capacity ?? 30,
            'status'       => $request->status ?? 'open',
            'is_published' => true,
            'notes'        => $request->notes,
        ]);

        AdminActivityLog::record("Created LMS Cohort: {$cohort->name}");

        return back()->with('success', "Cohort '{$cohort->name}' created and published successfully!");
    }

    /**
     * Toggle Cohort Published Status
     */
    public function togglePublishCohort($id)
    {
        $cohort = Cohort::findOrFail($id);
        $cohort->is_published = !$cohort->is_published;
        $cohort->status = $cohort->is_published ? 'open' : 'draft';
        $cohort->save();

        $state = $cohort->is_published ? 'Published' : 'Moved to Draft';
        return back()->with('success', "Cohort '{$cohort->name}' is now {$state}.");
    }

    /**
     * Schedule & Publish Live Masterclass
     */
    public function storeMasterclass(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:200',
            'course_id'        => 'nullable|exists:courses,id',
            'instructor_id'    => 'nullable|exists:users,id',
            'mentor_name'      => 'nullable|string|max:100',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'nullable|integer|min:15',
            'meeting_link'     => 'nullable|string|max:500',
            'recording_url'    => 'nullable|string|max:500',
            'description'      => 'nullable|string',
        ]);

        $mc = Masterclass::create([
            'title'            => trim($request->title),
            'course_id'        => $request->course_id,
            'instructor_id'    => $request->instructor_id,
            'mentor_name'      => $request->mentor_name ?? ($request->instructor_id ? User::find($request->instructor_id)?->name : 'Lead Mentor'),
            'scheduled_at'     => Carbon::parse($request->scheduled_at),
            'duration_minutes' => $request->duration_minutes ?? 60,
            'meeting_link'     => $request->meeting_link ?? 'https://meet.google.com/netcoder-live',
            'recording_url'    => $request->recording_url,
            'description'      => $request->description,
            'status'           => 'published',
            'max_participants' => 150,
        ]);

        AdminActivityLog::record("Published Live Masterclass: {$mc->title}");

        return back()->with('success', "Masterclass '{$mc->title}' scheduled and published!");
    }

    /**
     * Toggle Masterclass Status
     */
    public function togglePublishMasterclass($id)
    {
        $mc = Masterclass::findOrFail($id);
        $mc->status = $mc->status === 'published' ? 'draft' : 'published';
        $mc->save();

        $state = ucfirst($mc->status);
        return back()->with('success', "Masterclass '{$mc->title}' status updated to {$state}.");
    }

    /**
     * Toggle Course Published Status
     */
    public function togglePublishCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->status = !$course->status;
        $course->save();

        $state = $course->status ? 'Published' : 'Unpublished (Draft)';
        AdminActivityLog::record("Course '{$course->name}' status changed to {$state}");

        return back()->with('success', "Course '{$course->name}' is now {$state}.");
    }

    /**
     * Update Student Performance / Progress
     */
    public function updateStudentProgress(Request $request, $id)
    {
        $progress = StudentCourseProgress::findOrFail($id);

        $request->validate([
            'completed_lessons'   => 'nullable|integer|min:0',
            'total_lessons'       => 'nullable|integer|min:1',
            'progress_percentage' => 'nullable|numeric|min:0|max:100',
            'grade'               => 'nullable|string|max:10',
            'is_certified'        => 'nullable|boolean',
        ]);

        $totalLessons = $request->total_lessons ?? $progress->total_lessons;
        $completed = $request->completed_lessons ?? $progress->completed_lessons;
        $pct = $request->has('progress_percentage')
            ? (float)$request->progress_percentage
            : round(($completed / max(1, $totalLessons)) * 100, 1);

        $isCertified = $request->boolean('is_certified');

        $progress->update([
            'completed_lessons'   => $completed,
            'total_lessons'       => $totalLessons,
            'progress_percentage' => $pct,
            'grade'               => $request->grade ?? ($pct >= 85 ? 'A+' : ($pct >= 70 ? 'A' : ($pct >= 50 ? 'B' : 'In Progress'))),
            'is_certified'        => $isCertified,
            'certified_at'        => $isCertified ? ($progress->certified_at ?? now()) : null,
            'last_active_at'      => now(),
        ]);

        return back()->with('success', "Progress updated for {$progress->student?->first_name}!");
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
