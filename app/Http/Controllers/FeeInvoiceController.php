<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\Student;
use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeeInvoiceController extends Controller
{
    // ─────────────────────────────────────────────
    // REUSABLE FEE CALCULATION HELPER
    // ─────────────────────────────────────────────

    /**
     * Calculate fee breakdown for a student for a given billing period.
     *
     * Returns:
     *  - per_installment: amount due per tenure cycle (course fee only, no fine/registration/prospectus)
     *  - tenure_label, tenure_months, course_months
     *  - discount_per_installment
     *  - net_installment: per_installment after discount
     *  - attendance_fine, attendance_fine_details
     *  - late_fine, months_late
     *  - total_fine
     *  - prospectus/registration status
     *  - course fee paid so far (course invoices only)
     *  - existing invoice for the period
     */
    private function calculateStudentFee(Student $student, int $month, int $year): array
    {
        // ── Tenure & installment calculation ──
        $tenureLabel = $student->fee_tenure ?? '1 Year';
        $tenureMonths = match($tenureLabel) {
            '1 Month' => 1,
            '3 Months' => 3,
            '6 Months' => 6,
            '1 Year' => 12,
            default => 12,
        };

        $courseFee = $student->course ? (float)$student->course->fee : 0;
        $discount = (float)($student->discount ?? 0);

        // Parse course duration to months
        $durationLower = strtolower($student->course_duration ?? '1 year');
        $courseMonths = match(true) {
            str_contains($durationLower, '1 year') || str_contains($durationLower, '12 month') => 12,
            str_contains($durationLower, '6 month') => 6,
            str_contains($durationLower, '3 month') => 3,
            str_contains($durationLower, '45 day') => 1.5,
            str_contains($durationLower, '1 month') => 1,
            default => 12,
        };

        // Number of installments across the course
        $numInstallments = max(1, (int) ceil($courseMonths / $tenureMonths));

        // Per-installment amounts (course fee only — no prospectus/registration)
        $perInstallment = round($courseFee / $numInstallments, 2);
        $discountPerInstallment = round($discount / $numInstallments, 2);
        $netInstallment = max(0, $perInstallment - $discountPerInstallment);

        // ── Attendance fine for this month (₹50/day absent) ──
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        $absentRecords = Attendance::where('student_id', $student->id)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'Absent')
            ->get();

        $attendanceFine = 0;
        $attendanceFineDetails = [];
        foreach ($absentRecords as $att) {
            $fineAmount = $att->fine > 0 ? (float)$att->fine : 50;
            $attendanceFine += $fineAmount;
            $attendanceFineDetails[] = 'Absent on ' . Carbon::parse($att->attendance_date)->format('M d') . ' (₹' . number_format($fineAmount, 0) . ')';
        }

        // ── Late payment fine (₹50/month late after 10th) ──
        $dueDate = Carbon::create($year, $month, 10);
        $monthsLate = 0;
        if (now()->greaterThan($dueDate)) {
            $monthsLate = (int) now()->diffInMonths($dueDate);
        }
        $lateFine = $monthsLate * 50;

        $totalFine = $lateFine + $attendanceFine;

        // ── Check existing invoice for this billing period ──
        $existingInvoice = FeeInvoice::where('student_id', $student->id)
            ->where('billing_month', $month)
            ->where('billing_year', $year)
            ->first();

        // ── Prospectus & Registration status ──
        $allInvoices = FeeInvoice::where('student_id', $student->id)->get();
        $prospectusPaid = false;
        $registrationPaid = false;

        foreach ($allInvoices as $inv) {
            if ($inv->status === 'Paid') {
                $items = is_string($inv->fee_items) ? json_decode($inv->fee_items, true) : ($inv->fee_items ?? []);
                foreach ($items as $item) {
                    $category = strtolower($item['category'] ?? '');
                    if (str_contains($category, 'prospectus')) $prospectusPaid = true;
                    if (str_contains($category, 'registration')) $registrationPaid = true;
                }
                $feeCat = strtolower($inv->fee_category ?? '');
                if (str_contains($feeCat, 'prospectus')) $prospectusPaid = true;
                if (str_contains($feeCat, 'registration')) $registrationPaid = true;
            }
        }

        // ── Course fee paid so far (ONLY course invoices, not fine/registration/prospectus) ──
        $courseInvoices = $allInvoices->filter(function ($invoice) {
            $cat = strtolower($invoice->fee_category ?? '');
            return !str_contains($cat, 'registration') &&
                   !str_contains($cat, 'prospectus') &&
                   !str_contains($cat, 'seminar') &&
                   !str_contains($cat, 'fine');
        });
        $totalCoursePaid = $courseInvoices->sum('paid_amount');
        $netCourseFee = max(0, $courseFee - $discount);
        $pendingCourseFee = max(0, $netCourseFee - $totalCoursePaid);

        return [
            'student' => [
                'id' => $student->id,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'admission_no' => $student->admission_no,
                'course' => $student->course?->name,
                'course_fee' => $courseFee,
                'course_duration' => $student->course_duration ?: '',
                'fee_tenure' => $tenureLabel,
                'discount' => $discount,
                'registration_fee' => (float)($student->registration_fee ?? 0),
                'prospectus_fee' => (float)($student->prospectus_fee ?? 0),
            ],
            'billing_period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::create()->month($month)->format('F'),
            ],
            'installment' => [
                'tenure_label' => $tenureLabel,
                'tenure_months' => $tenureMonths,
                'course_months' => $courseMonths,
                'num_installments' => $numInstallments,
                'per_installment' => $perInstallment,
                'discount_per_installment' => $discountPerInstallment,
                'net_installment' => $netInstallment,
            ],
            'fines' => [
                'attendance_fine' => $attendanceFine,
                'attendance_fine_details' => $attendanceFineDetails,
                'late_fine' => $lateFine,
                'months_late' => $monthsLate,
                'total_fine' => $totalFine,
            ],
            'course_account' => [
                'total_course_fee' => $courseFee,
                'net_course_fee' => $netCourseFee,
                'total_paid' => $totalCoursePaid,
                'pending_dues' => $pendingCourseFee,
            ],
            'one_time_fees' => [
                'prospectus_paid' => $prospectusPaid,
                'registration_paid' => $registrationPaid,
            ],
            'existing_invoice' => $existingInvoice ? [
                'id' => $existingInvoice->id,
                'invoice_no' => $existingInvoice->invoice_no,
                'status' => $existingInvoice->status,
                'paid_amount' => $existingInvoice->paid_amount,
                'due_amount' => $existingInvoice->due_amount,
                'payment_date' => $existingInvoice->payment_date?->format('M d, Y'),
            ] : null,
        ];
    }

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = FeeInvoice::with('student');

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', '%'.$request->search.'%')
                ->orWhere('fee_category', 'like', '%'.$request->search.'%')
                ->orWhereHas('student', function ($query) use ($request) {
                    $query->where('first_name', 'like', '%'.$request->search.'%')
                        ->orWhere('last_name', 'like', '%'.$request->search.'%')
                        ->orWhere('admission_no', 'like', '%'.$request->search.'%');
                });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(12)->withQueryString();

        return view('fee_invoices.index', compact('invoices'));
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function create()
    {
        return view('fee_invoices.create', [
            'students' => Student::with('course')->orderBy('first_name')->get(),
        ]);
    }

    // ─────────────────────────────────────────────
    // MONTHLY FEE PAGE
    // ─────────────────────────────────────────────

    public function monthlyFee(Request $request)
    {
        $studentId = $request->query('student_id');
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $students = Student::with('course')->orderBy('first_name')->get();

        $monthlyStatus = null;
        if ($studentId) {
            $student = Student::with('course')->findOrFail($studentId);
            $monthlyStatus = $this->calculateStudentFee($student, $month, $year);
        }

        return view('fee_invoices.monthly', compact('students', 'studentId', 'month', 'year', 'monthlyStatus'));
    }

    // ─────────────────────────────────────────────
    // STUDENT MONTHLY STATUS (AJAX)
    // ─────────────────────────────────────────────

    public function studentMonthlyStatus($id)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $student = Student::with('course')->findOrFail($id);
        $status = $this->calculateStudentFee($student, $month, $year);

        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    // ─────────────────────────────────────────────
    // STUDENT FEE INFO (AJAX - used by create form)
    // ─────────────────────────────────────────────

    public function studentFeeInfo($id)
    {
        $student = Student::with('course')->findOrFail($id);
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $calc = $this->calculateStudentFee($student, (int)$month, (int)$year);

        // Flatten for backward-compatible JSON response used by create.blade.php
        return response()->json([
            'success' => true,
            'student_data' => [
                'student_name' => $calc['student']['name'],
                'course_fee' => $calc['student']['course_fee'],
                'course_duration' => $calc['student']['course_duration'],
                'registration_fee' => $calc['student']['registration_fee'],
                'prospectus_fee' => $calc['student']['prospectus_fee'],
                'discount' => $calc['student']['discount'],
                'fee_tenure' => $calc['installment']['tenure_label'],
                'tenure_months' => $calc['installment']['tenure_months'],
                'num_installments' => $calc['installment']['num_installments'],
                'per_installment' => $calc['installment']['per_installment'],
                'discount_per_installment' => $calc['installment']['discount_per_installment'],
                'net_installment' => $calc['installment']['net_installment'],
            ],
            'course_account' => $calc['course_account'],
            'attendance_fine' => $calc['fines']['attendance_fine'],
            'attendance_fine_details' => $calc['fines']['attendance_fine_details'],
            'late_fine' => $calc['fines']['late_fine'],
            'fine_details' => implode(', ', $calc['fines']['attendance_fine_details']),
            'prospectus_paid' => $calc['one_time_fees']['prospectus_paid'],
            'registration_paid' => $calc['one_time_fees']['registration_paid'],
            'course_paid' => $calc['course_account']['total_paid'],
            'pending_dues' => $calc['course_account']['pending_dues'],
            'past_payments' => FeeInvoice::where('student_id', $id)
                ->latest('payment_date')
                ->limit(5)
                ->get()
                ->map(function ($invoice) {
                    return [
                        'invoice_no' => $invoice->invoice_no,
                        'date' => $invoice->payment_date ? $invoice->payment_date->format('M d, Y') : '',
                        'category' => $invoice->fee_category,
                        'total' => number_format($invoice->total_amount, 2),
                        'paid' => number_format($invoice->paid_amount, 2),
                        'due' => number_format($invoice->due_amount, 2),
                        'status' => $invoice->status,
                    ];
                }),
        ]);
    }

    // ─────────────────────────────────────────────
    // STORE (Create Invoice)
    // ─────────────────────────────────────────────

    public function store(Request $request)
    {
        if ($request->has('fee_items_json')) {
            $request->merge([
                'fee_items' => json_decode($request->fee_items_json, true)
            ]);
        }

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'invoice_no' => ['nullable', 'string', 'max:60', 'unique:fee_invoices,invoice_no'],
            'fee_category' => ['nullable', 'string', 'max:100'],
            'billing_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'billing_year' => ['nullable', 'integer', 'min:2020', 'max:2030'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'fine' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'in:Online,Cash'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'utr_no' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:Paid,Partial,Unpaid'],
            'remarks' => ['nullable', 'string'],
            'fee_items' => ['nullable', 'array'],
            'fee_items.*.category' => ['required_with:fee_items', 'string', 'max:100'],
            'fee_items.*.amount' => ['required_with:fee_items', 'numeric', 'min:0'],
        ]);

        $data['discount'] = $data['discount'] ?? 0;
        $data['fine'] = $data['fine'] ?? 0;
        $data['paid_amount'] = $data['paid_amount'] ?? 0;

        // Auto-generate invoice number
        if (empty($data['invoice_no'])) {
            $year = date('Y');
            $count = FeeInvoice::whereYear('payment_date', $year)->withTrashed()->count();
            do {
                $invoiceNo = 'NT-REC-' . $year . '-' . str_pad(++$count, 3, '0', STR_PAD_LEFT);
            } while (FeeInvoice::where('invoice_no', $invoiceNo)->withTrashed()->exists());
            $data['invoice_no'] = $invoiceNo;
        }

        // due_amount = total_amount + fine - paid_amount - discount
        $data['due_amount'] = max(0, $data['total_amount'] + $data['fine'] - $data['paid_amount'] - $data['discount']);
        $data['created_by'] = session('user_id');

        // If fee_items are present, and fee_category is empty, generate fee_category
        if (empty($data['fee_category']) && !empty($data['fee_items'])) {
            $categories = array_map(function($item) {
                return $item['category'];
            }, $data['fee_items']);
            $data['fee_category'] = implode(', ', array_slice($categories, 0, 3));
            if (count($categories) > 3) {
                $data['fee_category'] .= '...';
            }
        }

        // Mark any overlapping unpaid prospectus/registration invoices as handled
        $paidRegistrationAmount = 0;
        $paidProspectusAmount = 0;

        if (!empty($data['fee_items'])) {
            foreach ($data['fee_items'] as $item) {
                $cat = strtolower($item['category'] ?? '');
                $amt = (float)($item['amount'] ?? 0);
                if (str_contains($cat, 'registration')) {
                    $paidRegistrationAmount += $amt;
                }
                if (str_contains($cat, 'prospectus')) {
                    $paidProspectusAmount += $amt;
                }
            }
        }

        if ($paidRegistrationAmount > 0 || $paidProspectusAmount > 0) {
            $studentId = $data['student_id'];
            $unpaidInvoices = FeeInvoice::where('student_id', $studentId)
                ->whereIn('status', ['Unpaid', 'Partial'])
                ->get();

            foreach ($unpaidInvoices as $inv) {
                $items = is_string($inv->fee_items) ? json_decode($inv->fee_items, true) : ($inv->fee_items ?? []);
                $hasReg = false;
                $hasPros = false;
                foreach ($items as $item) {
                    $category = strtolower($item['category'] ?? '');
                    if (str_contains($category, 'registration')) $hasReg = true;
                    if (str_contains($category, 'prospectus')) $hasPros = true;
                }

                $feeCat = strtolower($inv->fee_category ?? '');
                if (str_contains($feeCat, 'registration')) $hasReg = true;
                if (str_contains($feeCat, 'prospectus')) $hasPros = true;

                if ($hasReg || $hasPros) {
                    if ($inv->status === 'Unpaid') {
                        $inv->delete();
                    } else {
                        $paymentApplied = 0;
                        if ($hasReg && $paidRegistrationAmount > 0) {
                            $paymentApplied += $paidRegistrationAmount;
                        }
                        if ($hasPros && $paidProspectusAmount > 0) {
                            $paymentApplied += $paidProspectusAmount;
                        }

                        if ($paymentApplied > 0) {
                            $newPaid = $inv->paid_amount + $paymentApplied;
                            $totalOwed = $inv->total_amount + $inv->fine - $inv->discount;

                            $inv->update([
                                'paid_amount' => min($newPaid, $totalOwed),
                                'due_amount' => max(0, $totalOwed - $newPaid),
                                'status' => $newPaid >= $totalOwed ? 'Paid' : 'Partial',
                                'payment_date' => $data['payment_date'] ?? now(),
                                'payment_method' => $data['payment_method'] ?? 'Cash',
                            ]);
                        }
                    }
                }
            }
        }

        $invoice = FeeInvoice::create($data);

        return redirect()->route('fee_invoices.index')->with('success', 'Fee invoice generated successfully. Receipt No: ' . $invoice->invoice_no);
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(FeeInvoice $feeInvoice)
    {
        $feeInvoice->delete();

        return back()->with('success', 'Fee invoice deleted successfully.');
    }

    // ─────────────────────────────────────────────
    // SHOW (Receipt Page)
    // ─────────────────────────────────────────────

    public function show(FeeInvoice $feeInvoice)
    {
        $feeInvoice->load('student.course', 'creator');

        // Fetch student's payment history
        $studentHistory = FeeInvoice::where('student_id', $feeInvoice->student_id)
            ->where('id', '!=', $feeInvoice->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate overall totals for the student separated by category
        $allInvoices = FeeInvoice::where('student_id', $feeInvoice->student_id)->get();

        // Course invoices only (excluding one-time and fine invoices)
        $courseInvoices = $allInvoices->filter(function ($invoice) {
            $cat = strtolower($invoice->fee_category ?? '');
            return !str_contains($cat, 'registration') &&
                   !str_contains($cat, 'prospectus') &&
                   !str_contains($cat, 'seminar') &&
                   !str_contains($cat, 'fine');
        });

        $totalCourseFee = $feeInvoice->student?->course?->fee ?? 0;
        $studentDiscount = $feeInvoice->student?->discount ?? 0;
        $netCourseFee = max(0, $totalCourseFee - $studentDiscount);
        $overallPaid = $courseInvoices->sum('paid_amount');
        $overallDue = max(0, $netCourseFee - $overallPaid);
        $overallTotal = $netCourseFee;

        // One-time fees
        $registrationInvoices = $allInvoices->filter(fn($inv) => str_contains(strtolower($inv->fee_category ?? ''), 'registration'));
        $prospectusInvoices = $allInvoices->filter(fn($inv) => str_contains(strtolower($inv->fee_category ?? ''), 'prospectus'));

        $seminarInvoices = $allInvoices->where('fee_category', 'Seminar');
        $seminarDue = max(0, $seminarInvoices->sum('total_amount') - $seminarInvoices->sum('discount') - $seminarInvoices->sum('paid_amount'));

        // Fine invoices
        $fineInvoices = $allInvoices->where('fee_category', 'Fine');
        $totalFinesDue = max(0, $fineInvoices->sum('total_amount') - $fineInvoices->sum('paid_amount'));

        return view('fee_invoices.show', compact(
            'feeInvoice', 'studentHistory',
            'overallTotal', 'overallPaid', 'overallDue',
            'seminarDue', 'totalFinesDue'
        ));
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────

    public function restore($id)
    {
        $invoice = FeeInvoice::onlyTrashed()->findOrFail($id);
        $invoice->restore();

        return back()->with('success', 'Fee receipt restored successfully.');
    }

    public function restoreAll()
    {
        FeeInvoice::onlyTrashed()->restore();

        return back()->with('success', 'All trashed fee receipts restored successfully.');
    }

    // ─────────────────────────────────────────────
    // RECEIVE PAYMENT
    // ─────────────────────────────────────────────

    public function receivePayment(Request $request, $id)
    {
        $invoice = FeeInvoice::findOrFail($id);

        $request->validate([
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,UPI,Cheque,Card',
            'payment_date' => 'required|date',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:255',
        ]);

        $newPaidAmount = $invoice->paid_amount + $request->paid_amount;
        $totalOwed = $invoice->total_amount + $invoice->fine - $invoice->discount;

        if ($newPaidAmount >= $totalOwed) {
            $status = 'Paid';
            $dueAmount = 0;
        } elseif ($newPaidAmount > 0) {
            $status = 'Partial';
            $dueAmount = max(0, $totalOwed - $newPaidAmount);
        } else {
            $status = 'Unpaid';
            $dueAmount = $totalOwed;
        }

        $invoice->update([
            'paid_amount' => $newPaidAmount,
            'due_amount' => $dueAmount,
            'status' => $status,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'transaction_id' => $request->transaction_id ?: $invoice->transaction_id,
            'remarks' => $request->remarks ?: $invoice->remarks,
        ]);

        return back()->with('success', 'Payment of ₹' . number_format($request->paid_amount, 2) . ' recorded successfully. Invoice #' . $invoice->invoice_no . ' status is now: ' . $status);
    }

    // ─────────────────────────────────────────────
    // BULK GENERATE – SHOW PAGE
    // ─────────────────────────────────────────────

    public function showBulkGenerate(Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        // Fetch all active students who have courses mapped
        $students = Student::with('course')->where('status', true)->get();

        $studentsData = [];
        foreach ($students as $student) {
            $calc = $this->calculateStudentFee($student, $month, $year);

            $studentsData[] = [
                'student' => $student,
                'net_monthly_fee' => $calc['installment']['net_installment'],
                'discount' => $calc['installment']['discount_per_installment'],
                'late_fine' => $calc['fines']['late_fine'],
                'attendance_fine' => $calc['fines']['attendance_fine'],
                'total_amount' => $calc['installment']['net_installment'],  // Course fee only
                'total_fine' => $calc['fines']['total_fine'],              // Fines separate
                'has_invoice' => $calc['existing_invoice'] ? true : false,
                'existing_invoice_no' => $calc['existing_invoice']['invoice_no'] ?? null,
                'existing_invoice_status' => $calc['existing_invoice']['status'] ?? null,
            ];
        }

        return view('fee_invoices.bulk_generate', compact('studentsData', 'month', 'year'));
    }

    // ─────────────────────────────────────────────
    // BULK GENERATE – POST
    // ─────────────────────────────────────────────

    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'billing_month' => 'required|integer|min:1|max:12',
            'billing_year' => 'required|integer|min:2020|max:2030',
        ]);

        $month = $request->billing_month;
        $year = $request->billing_year;
        $studentIds = $request->student_ids;

        $generatedCount = 0;

        foreach ($studentIds as $studentId) {
            $student = Student::with('course')->find($studentId);
            if (!$student) continue;

            // Check if monthly invoice already exists
            $existingInvoice = FeeInvoice::where('student_id', $studentId)
                ->where('billing_month', $month)
                ->where('billing_year', $year)
                ->first();

            if ($existingInvoice) continue;

            $calc = $this->calculateStudentFee($student, $month, $year);

            $netInstallment = $calc['installment']['net_installment'];
            $tenureLabel = $calc['installment']['tenure_label'];
            $discountPerInstallment = $calc['installment']['discount_per_installment'];
            $totalFine = $calc['fines']['total_fine'];
            $lateFine = $calc['fines']['late_fine'];
            $attendanceFine = $calc['fines']['attendance_fine'];

            // Fee items — course fee installment only
            $feeItems = [
                ['category' => "Course Fee ({$tenureLabel} Installment)", 'amount' => $netInstallment]
            ];

            // Generate unique invoice number
            $invoiceNoCount = FeeInvoice::whereYear('payment_date', $year)->withTrashed()->count();
            do {
                $invoiceNo = 'NT-REC-' . $year . '-' . str_pad(++$invoiceNoCount, 3, '0', STR_PAD_LEFT);
            } while (FeeInvoice::where('invoice_no', $invoiceNo)->withTrashed()->exists());

            $monthName = Carbon::create()->month($month)->format('F');

            FeeInvoice::create([
                'student_id' => $studentId,
                'invoice_no' => $invoiceNo,
                'fee_category' => "Monthly Fee - {$monthName} {$year}",
                'billing_month' => $month,
                'billing_year' => $year,
                'total_amount' => $netInstallment,         // Course fee only
                'paid_amount' => 0,
                'discount' => $discountPerInstallment,
                'fine' => $totalFine,                       // Fines stored separately in fine column
                'due_amount' => $netInstallment + $totalFine, // Total due = course fee + fines
                'status' => 'Unpaid',
                'fee_items' => $feeItems,
                'payment_date' => now()->toDateString(),
                'remarks' => $totalFine > 0 ? "Includes: Late fine ₹{$lateFine}, Attendance fine ₹{$attendanceFine}" : null,
                'created_by' => session('user_id'),
            ]);

            $generatedCount++;
        }

        return redirect()->route('fee_invoices.index')
            ->with('success', "Successfully generated {$generatedCount} monthly invoices.");
    }
}
