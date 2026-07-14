<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    /**
     * Show the prospect creation form.
     */
    public function create()
    {
        return view('prospects.create');
    }

    /**
     * Store a new prospect and set initial totals, then redirect to invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:prospects,email',
            'registration_fee' => 'required|numeric|min:0',
            'monthly_fee'      => 'required|numeric|min:0',
        ]);

        $prospect = Prospect::create(array_merge($validated, [
            'fine_total'        => 0,
            'paid_amount'       => 0,
            'total_due'         => $validated['registration_fee'] + $validated['monthly_fee'],
            'remaining_balance' => $validated['registration_fee'] + $validated['monthly_fee'],
        ]));

        return redirect()->route('prospects.invoice', $prospect->id)
            ->with('status', 'Prospect created successfully!');
    }

    /**
     * Display the invoice for the prospect.
     */
    public function invoice($id)
    {
        $prospect = Prospect::findOrFail($id);

        // Ensure totals are up-to-date
        $prospect->total_due        = $prospect->registration_fee + $prospect->monthly_fee + $prospect->fine_total;
        $prospect->remaining_balance = max(0, $prospect->total_due - $prospect->paid_amount);
        $prospect->saveQuietly();

        return view('prospects.invoice', compact('prospect'));
    }

    /**
     * Process a payment for the prospect.
     */
    public function pay(Request $request, $id)
    {
        $prospect = Prospect::findOrFail($id);

        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
        ]);

        $amount = (float) $request->input('payment_amount');

        $prospect->paid_amount   += $amount;
        $prospect->payment_date   = now();

        // Recalculate
        $prospect->total_due        = $prospect->registration_fee + $prospect->monthly_fee + $prospect->fine_total;
        $prospect->remaining_balance = max(0, $prospect->total_due - $prospect->paid_amount);

        // If fully paid, hide prospect from active listings
        if ($prospect->remaining_balance <= 0) {
            $prospect->is_paid   = true;
            $prospect->is_active = false;
        }

        $prospect->save();

        return view('prospects.pay_success', compact('prospect'));
    }

    /**
     * Add a fine to the prospect (seminar fine, late fee, etc.)
     */
    public function addFine(Request $request, $id)
    {
        $request->validate([
            'fine_amount'  => 'required|numeric|min:0.01',
            'fine_reason'  => 'nullable|string|max:255',
        ]);

        $prospect = Prospect::findOrFail($id);

        $prospect->fine_total += (float) $request->input('fine_amount');

        // Recalculate
        $prospect->total_due        = $prospect->registration_fee + $prospect->monthly_fee + $prospect->fine_total;
        $prospect->remaining_balance = max(0, $prospect->total_due - $prospect->paid_amount);

        $prospect->save();

        return redirect()->route('prospects.invoice', $prospect->id)
            ->with('status', 'Fine of Rs. ' . number_format($request->fine_amount, 2) .
                ($request->fine_reason ? ' (' . $request->fine_reason . ')' : '') . ' added.');
    }
}
?>
