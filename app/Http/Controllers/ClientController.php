<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientInvoice;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('invoices');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('company', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('trashed') && $request->trashed == '1') {
            $query->onlyTrashed();
        }

        $clients = $query->latest()->paginate(15)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:200'],
            'email'   => ['nullable', 'email', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gst_no'  => ['nullable', 'string', 'max:20'],
            'pan_no'  => ['nullable', 'string', 'max:15'],
            'status'  => ['required', 'in:active,inactive'],
            'notes'   => ['nullable', 'string'],
        ]);

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client added successfully.');
    }

    public function show(Client $client)
    {
        $client->load('invoices');
        $totalBilled = $client->invoices->sum('total_amount');
        $totalPaid   = $client->invoices->sum('paid_amount');
        $totalDue    = $client->invoices->sum('due_amount');

        return view('clients.show', compact('client', 'totalBilled', 'totalPaid', 'totalDue'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'company' => ['nullable', 'string', 'max:200'],
            'email'   => ['nullable', 'email', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'gst_no'  => ['nullable', 'string', 'max:20'],
            'pan_no'  => ['nullable', 'string', 'max:15'],
            'status'  => ['required', 'in:active,inactive'],
            'notes'   => ['nullable', 'string'],
        ]);

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Client deleted successfully.');
    }

    public function restore($id)
    {
        $client = Client::onlyTrashed()->findOrFail($id);
        $client->restore();
        return back()->with('success', 'Client restored successfully.');
    }
}
