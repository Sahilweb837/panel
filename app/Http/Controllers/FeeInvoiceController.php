<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\Student;
use Illuminate\Http\Request;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'invoice_no' => ['nullable', 'string', 'max:60', 'unique:fee_invoices,invoice_no'],
            'fee_category' => ['nullable', 'string', 'max:100'],
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
        $data['invoice_no'] = $data['invoice_no'] ?? 'nt_inv_'.now()->format('YmdHi').'_'.rand(1000, 9999);
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

        FeeInvoice::create($data);

        return redirect()->route('fee_invoices.index')->with('success', 'Fee invoice generated successfully.');
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

        $allInvoices = FeeInvoice::where('student_id', $id)->get();
        $hasPaidReg = $allInvoices->contains(function ($inv) {
            $items = is_string($inv->fee_items) ? json_decode($inv->fee_items, true) : ($inv->fee_items ?? []);
            return in_array('Registration Fee', array_column($items, 'category')) && in_array($inv->status, ['Paid', 'Partial']);
        });
        $hasPaidPros = $allInvoices->contains(function ($inv) {
            $items = is_string($inv->fee_items) ? json_decode($inv->fee_items, true) : ($inv->fee_items ?? []);
            return in_array('Prospectus Fee', array_column($items, 'category')) && in_array($inv->status, ['Paid', 'Partial']);
        });

        return response()->json([
            'success' => true,
            'past_payments' => $pastPayments,
            'attendance_fine' => $automaticFine,
            'fine_details' => implode(', ', $fineDetails),
            'one_time_paid' => [
                'registration' => $hasPaidReg,
                'prospectus' => $hasPaidPros,
            ],
            'student_data' => [
                'course_fee' => $student->course ? $student->course->fee : 0,
                'course_duration' => $student->course_duration ?: '',
                'registration_fee' => $student->registration_fee ?: 0,
                'prospectus_fee' => $student->prospectus_fee ?: 0,
                'discount' => $student->discount ?: 0,
            ]
        ]);
    }
}
