<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientInvoice;
use Illuminate\Http\Request;

class ClientInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientInvoice::with('client');

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $query->where('invoice_no', 'like', '%' . $request->search . '%')
                  ->orWhereHas('client', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('company', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        return view('client_invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $clients = Client::where('status', 'active')->orderBy('name')->get();
        $selectedClient = $request->filled('client_id') ? Client::find($request->client_id) : null;

        return view('client_invoices.create', compact('clients', 'selectedClient'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'       => ['required', 'exists:clients,id'],
            'invoice_no'      => ['nullable', 'string', 'max:60', 'unique:client_invoices,invoice_no'],
            'invoice_items'   => ['required', 'array', 'min:1'],
            'invoice_items.*.description' => ['required', 'string', 'max:255'],
            'invoice_items.*.qty'         => ['required', 'numeric', 'min:0'],
            'invoice_items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'invoice_items.*.amount'      => ['required', 'numeric', 'min:0'],
            'tax_percent'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount'        => ['nullable', 'numeric', 'min:0'],
            'paid_amount'     => ['required', 'numeric', 'min:0'],
            'status'          => ['required', 'in:Paid,Partial,Unpaid'],
            'due_date'        => ['nullable', 'date'],
            'payment_date'    => ['nullable', 'date'],
            'payment_method'  => ['nullable', 'string', 'in:Cash,Online,Cheque'],
            'transaction_id'  => ['nullable', 'string', 'max:100'],
            'notes'           => ['nullable', 'string'],
        ]);

        // Calculate financials
        $subtotal   = array_sum(array_column($data['invoice_items'], 'amount'));
        $taxPercent = floatval($data['tax_percent'] ?? 0);
        $taxAmount  = round($subtotal * $taxPercent / 100, 2);
        $discount   = floatval($data['discount'] ?? 0);
        $total      = $subtotal + $taxAmount - $discount;
        $paid       = floatval($data['paid_amount']);
        $due        = max(0, $total - $paid);

        if (empty($data['invoice_no'])) {
            $year = date('Y');
            $count = ClientInvoice::whereYear('created_at', $year)->withTrashed()->count();
            do {
                $invoiceNo = 'NT-REC-' . $year . '-' . str_pad(++$count, 3, '0', STR_PAD_LEFT);
            } while (ClientInvoice::where('invoice_no', $invoiceNo)->withTrashed()->exists());
            $data['invoice_no'] = $invoiceNo;
        }
        $data['subtotal']     = $subtotal;
        $data['tax_percent']  = $taxPercent;
        $data['tax_amount']   = $taxAmount;
        $data['discount']     = $discount;
        $data['total_amount'] = $total;
        $data['due_amount']   = $due;
        $data['created_by']   = session('user_id');

        ClientInvoice::create($data);

        return redirect()->route('client_invoices.index')->with('success', 'Client invoice generated successfully.');
    }

    public function show(ClientInvoice $clientInvoice)
    {
        $clientInvoice->load('client', 'creator');

        $allInvoices  = ClientInvoice::where('client_id', $clientInvoice->client_id)->get();
        $overallTotal = $allInvoices->sum('total_amount');
        $overallPaid  = $allInvoices->sum('paid_amount');
        $overallDue   = max(0, $overallTotal - $overallPaid);

        $clientHistory = ClientInvoice::where('client_id', $clientInvoice->client_id)
            ->where('id', '!=', $clientInvoice->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client_invoices.show', compact('clientInvoice', 'overallTotal', 'overallPaid', 'overallDue', 'clientHistory'));
    }

    public function destroy(ClientInvoice $clientInvoice)
    {
        $clientInvoice->delete();
        return back()->with('success', 'Client invoice deleted successfully.');
    }

    public function restore($id)
    {
        $invoice = ClientInvoice::onlyTrashed()->findOrFail($id);
        $invoice->restore();
        return back()->with('success', 'Client invoice restored successfully.');
    }
}
