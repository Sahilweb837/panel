<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiFeeController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = FeeInvoice::with(['student:id,first_name,last_name,admission_no,phone,course_id', 'student.course:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('billing_month')) {
            $query->where('billing_month', $request->billing_month);
        }

        if ($request->filled('billing_year')) {
            $query->where('billing_year', (int)$request->billing_year);
        }

        $perPage = min(max((int)$request->input('per_page', 20), 5), 100);
        $invoices = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $invoices->items(),
            'pagination' => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'per_page'     => $invoices->perPage(),
                'total'        => $invoices->total(),
            ]
        ]);
    }

    /**
     * Display the specified invoice.
     */
    public function show($id)
    {
        $invoice = FeeInvoice::with(['student', 'student.course'])->find($id);
        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $invoice
        ]);
    }

    /**
     * Create a new fee invoice.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id'   => 'required|exists:students,id',
            'fee_category' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount'  => 'nullable|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0',
            'fine'         => 'nullable|numeric|min:0',
            'items'        => 'nullable|array',
            'remarks'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $total = (float)$request->total_amount;
        $discount = (float)($request->discount ?? 0);
        $fine = (float)($request->fine ?? 0);
        $netTotal = max(0, ($total - $discount) + $fine);
        $paid = (float)($request->paid_amount ?? 0);
        $due = max(0, $netTotal - $paid);

        $status = $due <= 0 ? 'Paid' : ($paid > 0 ? 'Partial' : 'Unpaid');

        $invoice = FeeInvoice::create([
            'student_id'      => $request->student_id,
            'invoice_no'      => 'INV-'.date('Ymd').'-'.mt_rand(1000, 9999),
            'fee_category'    => $request->fee_category,
            'fee_items'       => $request->items ?? [['name' => $request->fee_category, 'amount' => $total]],
            'billing_month'   => now()->format('F'),
            'billing_year'    => (int)now()->format('Y'),
            'total_amount'    => $netTotal,
            'paid_amount'     => $paid,
            'discount_amount' => $discount,
            'fine_amount'     => $fine,
            'due_amount'      => $due,
            'status'          => $status,
            'payment_date'    => $paid > 0 ? now()->toDateString() : null,
            'payment_method'  => $request->payment_method ?? 'Cash',
            'remarks'         => $request->remarks,
            'created_by'      => session('user_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invoice created successfully.',
            'data'    => $invoice
        ], 201);
    }

    /**
     * Receive payment against an invoice.
     */
    public function receivePayment(Request $request, $id)
    {
        $invoice = FeeInvoice::find($id);
        if (!$invoice) {
            return response()->json(['success' => false, 'message' => 'Invoice not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'remarks'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $amount = (float)$request->amount;
        $newPaid = $invoice->paid_amount + $amount;
        $newDue = max(0, $invoice->total_amount - $newPaid);
        $status = $newDue <= 0 ? 'Paid' : 'Partial';

        $invoice->update([
            'paid_amount'    => $newPaid,
            'due_amount'     => $newDue,
            'status'         => $status,
            'payment_date'   => now()->toDateString(),
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id ?? $invoice->transaction_id,
            'remarks'        => trim(($invoice->remarks ?? '').' | Paid '.$amount.' on '.now()->format('d-m-Y').' ('.$request->payment_method.')'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment received successfully.',
            'data'    => $invoice
        ]);
    }

    /**
     * Get all invoices and financial summary for a student.
     */
    public function studentInvoices($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        $invoices = FeeInvoice::where('student_id', $studentId)->orderBy('id', 'desc')->get();

        $totalBilled = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $totalDue = $invoices->sum('due_amount');

        return response()->json([
            'success'  => true,
            'student'  => [
                'id' => $student->id,
                'name' => trim($student->first_name.' '.$student->last_name),
                'admission_no' => $student->admission_no,
            ],
            'summary'  => [
                'total_billed' => (float)$totalBilled,
                'total_paid'   => (float)$totalPaid,
                'total_due'    => (float)$totalDue,
            ],
            'invoices' => $invoices
        ]);
    }
}

