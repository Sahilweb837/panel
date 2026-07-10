<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeeInvoiceController extends Controller
{
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

    public function create()
    {
        return view('fee_invoices.create', [
            'students' => Student::with('course')->orderBy('first_name')->get(),
        ]);
    }

    /**
     * Show form for adding monthly fee for a specific student
     */
    public function monthlyFee(Request $request)
    {
        $studentId = $request->query('student_id');
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);
        
        $students = Student::with('course')->orderBy('first_name')->get();
        
        // Get student's monthly fee status
        $monthlyStatus = null;
        if ($studentId) {
            $monthlyStatus = $this->getMonthlyFeeStatus($studentId, $month, $year);
        }
        
        return view('fee_invoices.monthly', compact('students', 'studentId', 'month', 'year', 'monthlyStatus'));
    }

    /**
     * Get monthly fee status for a student
     */
    public function studentMonthlyStatus($id)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);
        
        $status = $this->getMonthlyFeeStatus($id, $month, $year);
        
        return response()->json([
            'success' => true,
            'status' => $status,
        ]);
    }

    /**
     * Helper: Get monthly fee status for a student
     */
    private function getMonthlyFeeStatus($studentId, $month, $year)
    {
        $student = Student::with('course')->findOrFail($studentId);
        
        // Check if monthly fee already exists for this period
        $existingInvoice = FeeInvoice::where('student_id', $studentId)
            ->where('billing_month', $month)
            ->where('billing_year', $year)
            ->first();
        
        // Calculate monthly course fee based on tenure
        $tenureLabel = $student->fee_tenure ?? '1 Year';
        $tenureMonths = match($tenureLabel) {
            '1 Month' => 1,
            '3 Months' => 3,
            '6 Months' => 6,
            '1 Year' => 12,
            default => 12,
        };
        
        $courseFee = $student->course ? $student->course->fee : 0;
        $discount = $student->discount ?? 0;
        
        // Course months from course_duration
        $durationLower = strtolower($student->course_duration ?? '1 year');
        $courseMonths = match(true) {
            str_contains($durationLower, '1 year') || str_contains($durationLower, '12 month') => 12,
            str_contains($durationLower, '6 month') => 6,
            str_contains($durationLower, '3 month') => 3,
            str_contains($durationLower, '1 month') => 1,
            default => 12,
        };
        
        $divisor = max(1, (int) ceil($courseMonths / $tenureMonths));
        $monthlyCourseFee = round($courseFee / $divisor, 2);
        $monthlyDiscount = round($discount / $divisor, 2);
        $netMonthlyFee = max(0, $monthlyCourseFee - $monthlyDiscount);
        
        // Calculate late fine (e.g., ₹50 per month late after due date)
        $dueDate = Carbon::create($year, $month, 10); // Due by 10th of each month
        $isLate = now()->greaterThan($dueDate);
        $monthsLate = 0;
        if ($isLate) {
            $monthsLate = now()->diffInMonths($dueDate);
        }
        $lateFine = $monthsLate * 50; // ₹50 per month late
        
        // Get attendance fine for the month
        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();
        
        $attendances = \App\Models\Attendance::where('student_id', $studentId)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->where(function($query) {
                $query->where('status', 'Absent')
                      ->orWhere('fine', '>', 0);
            })
            ->get();
        
        $attendanceFine = 0;
        $attendanceFineDetails = [];
        foreach ($attendances as $att) {
            $fineAmount = $att->fine > 0 ? (float)$att->fine : 50;
            if ($att->status === 'Absent' || $att->fine > 0) {
                $attendanceFine += $fineAmount;
                $attendanceFineDetails[] = 'Absent on ' . Carbon::parse($att->attendance_date)->format('M d') . ' (₹' . $fineAmount . ')';
            }
        }
        
        // Check if Prospectus Fee or Registration Fee has already been paid in past invoices
        $allInvoices = FeeInvoice::where('student_id', $studentId)->get();
        $prospectusPaid = false;
        $registrationPaid = false;

        foreach ($allInvoices as $inv) {
            if ($inv->status === 'Paid') {
                $items = is_string($inv->fee_items) ? json_decode($inv->fee_items, true) : ($inv->fee_items ?? []);
                foreach ($items as $item) {
                    $category = strtolower($item['category'] ?? '');
                    if (str_contains($category, 'prospectus')) {
                        $prospectusPaid = true;
                    }
                    if (str_contains($category, 'registration')) {
                        $registrationPaid = true;
                    }
                }
                $feeCat = strtolower($inv->fee_category ?? '');
                if (str_contains($feeCat, 'prospectus')) {
                    $prospectusPaid = true;
                }
                if (str_contains($feeCat, 'registration')) {
                    $registrationPaid = true;
                }
            }
        }

        $unpaidRegistration = 0;
        $unpaidProspectus = 0;

        if (!$registrationPaid && ($student->registration_fee ?? 0) > 0) {
            // Find if there is an unpaid/partial invoice containing registration
            $regInvoice = FeeInvoice::where('student_id', $studentId)
                ->whereIn('status', ['Unpaid', 'Partial'])
                ->where(function($q) {
                    $q->where('fee_category', 'like', '%registration%')
                      ->orWhere('fee_items', 'like', '%registration%');
                })
                ->first();
            
            if ($regInvoice) {
                if ($regInvoice->status === 'Unpaid') {
                    $unpaidRegistration = (float) $student->registration_fee;
                } else {
                    $unpaidRegistration = (float) $regInvoice->due_amount; 
                }
            } else {
                $unpaidRegistration = (float) $student->registration_fee;
            }
        }

        if (!$prospectusPaid && ($student->prospectus_fee ?? 0) > 0) {
            // Find if there is an unpaid/partial invoice containing prospectus
            $prosInvoice = FeeInvoice::where('student_id', $studentId)
                ->whereIn('status', ['Unpaid', 'Partial'])
                ->where(function($q) {
                    $q->where('fee_category', 'like', '%prospectus%')
                      ->orWhere('fee_items', 'like', '%prospectus%');
                })
                ->first();
            
            if ($prosInvoice) {
                if ($prosInvoice->status === 'Unpaid') {
                    $unpaidProspectus = (float) $student->prospectus_fee;
                } else {
                    $unpaidProspectus = (float) $prosInvoice->due_amount;
                }
            } else {
                $unpaidProspectus = (float) $student->prospectus_fee;
            }
        }

        $totalFine = $lateFine + $attendanceFine;
        $totalAmount = $netMonthlyFee + $totalFine + $unpaidRegistration + $unpaidProspectus;
        
        return [
            'student' => [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->last_name,
                'admission_no' => $student->admission_no,
                'course' => $student->course?->name,
                'fee_tenure' => $tenureLabel,
            ],
            'billing_period' => [
                'month' => $month,
                'year' => $year,
                'month_name' => Carbon::create()->month($month)->format('F'),
            ],
            'fee_breakdown' => [
                'monthly_course_fee' => $monthlyCourseFee,
                'monthly_discount' => $monthlyDiscount,
                'net_monthly_fee' => $netMonthlyFee,
                'late_fine' => $lateFine,
                'months_late' => $monthsLate,
                'attendance_fine' => $attendanceFine,
                'attendance_fine_details' => $attendanceFineDetails,
                'unpaid_registration' => $unpaidRegistration,
                'unpaid_prospectus' => $unpaidProspectus,
                'total_fine' => $totalFine,
                'total_amount' => $totalAmount,
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
        if (empty($data['invoice_no'])) {
            $year = date('Y');
            $count = FeeInvoice::whereYear('payment_date', $year)->withTrashed()->count();
            do {
                $invoiceNo = 'NT-REC-' . $year . '-' . str_pad(++$count, 3, '0', STR_PAD_LEFT);
            } while (FeeInvoice::where('invoice_no', $invoiceNo)->withTrashed()->exists());
            $data['invoice_no'] = $invoiceNo;
        }
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

        // Apply payments to existing unpaid/partial invoices if Registration or Prospectus Fee is paid in this invoice
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
                        // Safe to delete because we are billing/paying it now in the new monthly invoice
                        $inv->delete();
                    } else {
                        // Partial: Record payment on it to adjust due_amount
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

        return redirect()->route('fee_invoices.show', $invoice)->with('success', 'Fee invoice generated successfully.');
    }

    public function destroy(FeeInvoice $feeInvoice)
    {
        $feeInvoice->delete();

        return back()->with('success', 'Fee invoice deleted successfully.');
    }

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
        
        $courseInvoices = $allInvoices->whereNotIn('fee_category', ['Seminar', 'Fine']);
        $overallTotal = $courseInvoices->sum('total_amount') - $courseInvoices->sum('discount');
        $overallPaid = $courseInvoices->sum('paid_amount');
        $overallDue = max(0, $overallTotal - $overallPaid);
        
        $seminarInvoices = $allInvoices->where('fee_category', 'Seminar');
        $seminarDue = max(0, $seminarInvoices->sum('total_amount') - $seminarInvoices->sum('discount') - $seminarInvoices->sum('paid_amount'));
        
        $fineInvoices = $allInvoices->where('fee_category', 'Fine');
        // Include fines added to regular invoices as well
        $totalFinesDue = max(0, ($fineInvoices->sum('total_amount') - $fineInvoices->sum('discount') - $fineInvoices->sum('paid_amount')) + $allInvoices->sum('fine'));

        return view('fee_invoices.show', compact('feeInvoice', 'studentHistory', 'overallTotal', 'overallPaid', 'overallDue', 'seminarDue', 'totalFinesDue'));
    }

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

    public function studentFeeInfo($id)
    {
        $student = Student::with('course')->findOrFail($id);
        
        // 1. Fetch Past Payment History (last 5 receipts)
        $pastPayments = FeeInvoice::where('student_id', $id)
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
            });

        // 2. Fetch Unpaid Fines from Attendances (Current Month)
        // We look for 'Absent' records this month.
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        // Let's assume an automatic fine of 50 per absent day if no explicit fine is set, 
        // OR we just pull the explicit fine amount saved in the database.
        $attendances = \App\Models\Attendance::where('student_id', $id)
            ->whereBetween('attendance_date', [$startOfMonth, $endOfMonth])
            ->where(function($query) {
                $query->where('status', 'Absent')
                      ->orWhere('fine', '>', 0);
            })
            ->get();
            
        $automaticFine = 0;
        $fineDetails = [];
        
        foreach ($attendances as $att) {
            $fineAmount = $att->fine > 0 ? (float)$att->fine : 50; // default 50 per absent day
            if ($att->status === 'Absent' || $att->fine > 0) {
                $automaticFine += $fineAmount;
                $fineDetails[] = 'Absent on ' . \Carbon\Carbon::parse($att->attendance_date)->format('M d') . ' (₹' . $fineAmount . ')';
            }
        }

        // 3. Check if Prospectus Fee or Registration Fee has already been paid in past invoices
        $allInvoices = FeeInvoice::where('student_id', $id)->get();
        $prospectusPaid = false;
        $registrationPaid = false;

        foreach ($allInvoices as $inv) {
            $items = is_string($inv->fee_items) ? json_decode($inv->fee_items, true) : ($inv->fee_items ?? []);
            foreach ($items as $item) {
                $category = strtolower($item['category'] ?? '');
                if (str_contains($category, 'prospectus')) {
                    $prospectusPaid = true;
                }
                if (str_contains($category, 'registration')) {
                    $registrationPaid = true;
                }
            }
            $feeCat = strtolower($inv->fee_category ?? '');
            if (str_contains($feeCat, 'prospectus')) {
                $prospectusPaid = true;
            }
            if (str_contains($feeCat, 'registration')) {
                $registrationPaid = true;
            }
        }

        return response()->json([
            'success' => true,
            'past_payments' => $pastPayments,
            'attendance_fine' => $automaticFine,
            'fine_details' => implode(', ', $fineDetails),
            'prospectus_paid' => $prospectusPaid,
            'registration_paid' => $registrationPaid,
            'student_data' => [
                'course_fee' => $student->course ? $student->course->fee : 0,
                'course_duration' => $student->course_duration ?: '',
                'registration_fee' => $student->registration_fee ?: 0,
                'prospectus_fee' => $student->prospectus_fee ?: 0,
                'discount' => $student->discount ?: 0,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'fee_tenure' => $student->fee_tenure ?: '1 Year'
            ]
        ]);
    }

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
}
