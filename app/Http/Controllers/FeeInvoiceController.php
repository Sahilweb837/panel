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
        ]);

        $data['discount'] = $data['discount'] ?? 0;
        $data['fine'] = $data['fine'] ?? 0;
        $data['paid_amount'] = $data['paid_amount'] ?? 0;
        $data['invoice_no'] = $data['invoice_no'] ?? 'nt_inv_'.now()->format('YmdHi').'_'.rand(1000, 9999);
        $data['due_amount'] = max(0, $data['total_amount'] + $data['fine'] - $data['paid_amount'] - $data['discount']);
        $data['created_by'] = session('user_id');

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
        return view('fee_invoices.show', compact('feeInvoice'));
    }

    public function restore($id)
    {
        $invoice = FeeInvoice::onlyTrashed()->findOrFail($id);
        $invoice->restore();

        return back()->with('success', 'Fee invoice restored successfully.');
    }
}
