<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollSetting;
use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    // ---------------------------------------------------------------
    // Settings: View & Save Razorpay credentials
    // ---------------------------------------------------------------

    public function settings()
    {
        $settings = PayrollSetting::getSettings();
        return view('payroll.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'razorpay_key_id'         => ['required', 'string', 'max:100'],
            'razorpay_key_secret'     => ['nullable', 'string'],
            'razorpay_account_number' => ['required', 'string', 'max:50'],
            'mode'                    => ['required', 'in:test,live'],
        ]);

        $settings = PayrollSetting::getSettings();
        $settings->razorpay_key_id         = $request->razorpay_key_id;
        $settings->razorpay_account_number = $request->razorpay_account_number;
        $settings->mode                    = $request->mode;

        // Only update secret if a new one is provided
        if ($request->filled('razorpay_key_secret')) {
            $settings->razorpay_key_secret = encrypt($request->razorpay_key_secret);
        }

        $settings->save();

        return back()->with('success', 'Razorpay payroll settings saved successfully.');
    }

    // ---------------------------------------------------------------
    // Single Payout: Initiate salary transfer for one salary slip
    // ---------------------------------------------------------------

    public function initiatePayout(SalarySlip $salarySlip)
    {
        $salarySlip->load('employee.user');
        $employee = $salarySlip->employee;

        // Guard: bank details must exist
        if (!$employee->bank_account_no || !$employee->bank_ifsc) {
            return back()->with('error', 'Employee bank details are not set. Please update employee profile first.');
        }

        // Guard: already processed
        if (in_array($salarySlip->payout_status, ['processing', 'processed'])) {
            return back()->with('error', 'Payout already initiated or completed for this salary slip.');
        }

        $settings = PayrollSetting::getSettings();
        if (!$settings->razorpay_key_id || !$settings->getDecryptedSecret()) {
            return back()->with('error', 'Razorpay credentials not configured. Go to Payroll Settings first.');
        }

        try {
            $api = new \Razorpay\Api\Api($settings->razorpay_key_id, $settings->getDecryptedSecret());

            // Step 1: Create or reuse Razorpay Contact for this employee
            $contactId = $employee->razorpay_contact_id;
            if (!$contactId) {
                $contact = $api->contact->create([
                    'name'         => $employee->user->name ?? $employee->account_holder_name ?? 'Employee',
                    'email'        => $employee->user->email ?? null,
                    'contact'      => $employee->phone ?? null,
                    'type'         => 'employee',
                    'reference_id' => $employee->employee_code,
                ]);
                $contactId = $contact->id;
                $employee->update(['razorpay_contact_id' => $contactId]);
            }

            // Step 2: Create or reuse Fund Account (bank account linked to contact)
            $fundAccountId = $employee->razorpay_fund_account_id;
            if (!$fundAccountId) {
                $fundAccount = $api->fundAccount->create([
                    'contact_id'   => $contactId,
                    'account_type' => 'bank_account',
                    'bank_account' => [
                        'name'           => $employee->account_holder_name ?? $employee->user->name,
                        'ifsc'           => strtoupper($employee->bank_ifsc),
                        'account_number' => $employee->bank_account_no,
                    ],
                ]);
                $fundAccountId = $fundAccount->id;
                $employee->update(['razorpay_fund_account_id' => $fundAccountId]);
            }

            // Step 3: Create Payout (transfer salary amount in paise)
            $amountInPaise = (int) round($salarySlip->net_pay * 100);
            $payout = $api->payout->create([
                'account_number'  => $settings->razorpay_account_number,
                'fund_account_id' => $fundAccountId,
                'amount'          => $amountInPaise,
                'currency'        => 'INR',
                'mode'            => 'IMPS',
                'purpose'         => 'salary',
                'queue_if_low_balance' => true,
                'narration'       => 'Salary ' . $salarySlip->month . ' ' . $salarySlip->year,
                'reference_id'    => 'SLIP-' . $salarySlip->id,
            ]);

            // Update salary slip with payout details
            $salarySlip->update([
                'razorpay_payout_id'  => $payout->id,
                'payout_status'       => $payout->status,
                'payout_mode'         => 'IMPS',
                'payout_initiated_at' => now(),
                'payout_response'     => json_encode($payout->toArray()),
                'status'              => in_array($payout->status, ['processed']) ? 'Paid' : 'Pending',
                'payment_date'        => in_array($payout->status, ['processed']) ? now()->toDateString() : null,
            ]);

            return back()->with('success', "Payout initiated successfully! Payout ID: {$payout->id} | Status: {$payout->status}");

        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            Log::error('Razorpay Payout Error: ' . $e->getMessage());
            return back()->with('error', 'Razorpay Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Payroll payout failed: ' . $e->getMessage());
            return back()->with('error', 'Payout failed: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------------
    // Bulk Payout: Pay all pending slips for a given month/year
    // ---------------------------------------------------------------

    public function bulkPayout(Request $request)
    {
        $request->validate([
            'month' => ['required', 'string'],
            'year'  => ['required', 'digits:4'],
        ]);

        $slips = SalarySlip::with('employee.user')
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->where('status', 'Pending')
            ->whereNull('razorpay_payout_id')
            ->get();

        if ($slips->isEmpty()) {
            return back()->with('error', 'No pending salary slips found for ' . $request->month . ' ' . $request->year . '.');
        }

        $success = 0;
        $failed  = 0;
        $errors  = [];

        foreach ($slips as $slip) {
            try {
                // Reuse single payout logic via direct call
                $this->processSinglePayout($slip);
                $success++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = ($slip->employee->user->name ?? 'Unknown') . ': ' . $e->getMessage();
            }
        }

        $msg = "Bulk payout complete. Success: {$success}, Failed: {$failed}.";
        if ($errors) {
            $msg .= ' Errors: ' . implode(' | ', array_slice($errors, 0, 3));
        }

        return back()->with($failed === 0 ? 'success' : 'error', $msg);
    }

    // ---------------------------------------------------------------
    // Webhook: Razorpay will call this URL when payout status changes
    // ---------------------------------------------------------------

    public function webhookHandler(Request $request)
    {
        $settings = PayrollSetting::getSettings();
        $webhookSecret = config('services.razorpay.webhook_secret', '');

        // Verify signature if webhook secret is configured
        if ($webhookSecret) {
            $signature = $request->header('X-Razorpay-Signature');
            $payload   = $request->getContent();
            $expected  = hash_hmac('sha256', $payload, $webhookSecret);
            if (!hash_equals($expected, $signature ?? '')) {
                return response('Invalid signature', 400);
            }
        }

        $event = $request->input('event');
        $payout = $request->input('payload.payout.entity', []);
        $payoutId = $payout['id'] ?? null;

        if ($payoutId) {
            $slip = SalarySlip::where('razorpay_payout_id', $payoutId)->first();
            if ($slip) {
                $newStatus = $payout['status'] ?? null;
                $updateData = ['payout_status' => $newStatus];

                if ($event === 'payout.processed') {
                    $updateData['status']       = 'Paid';
                    $updateData['payment_date'] = now()->toDateString();
                } elseif ($event === 'payout.failed' || $event === 'payout.reversed') {
                    $updateData['status'] = 'Pending'; // Reset to allow retry
                }

                $slip->update($updateData);
                Log::info("Razorpay webhook [{$event}] processed for slip #{$slip->id}");
            }
        }

        return response('OK', 200);
    }

    // ---------------------------------------------------------------
    // Internal: Shared payout logic used by bulk
    // ---------------------------------------------------------------

    private function processSinglePayout(SalarySlip $salarySlip): void
    {
        $employee = $salarySlip->employee;

        if (!$employee->bank_account_no || !$employee->bank_ifsc) {
            throw new \Exception('Bank details missing');
        }

        $settings = PayrollSetting::getSettings();
        $api = new \Razorpay\Api\Api($settings->razorpay_key_id, $settings->getDecryptedSecret());

        $contactId = $employee->razorpay_contact_id;
        if (!$contactId) {
            $contact = $api->contact->create([
                'name'         => $employee->user->name ?? 'Employee',
                'email'        => $employee->user->email ?? null,
                'contact'      => $employee->phone ?? null,
                'type'         => 'employee',
                'reference_id' => $employee->employee_code,
            ]);
            $contactId = $contact->id;
            $employee->update(['razorpay_contact_id' => $contactId]);
        }

        $fundAccountId = $employee->razorpay_fund_account_id;
        if (!$fundAccountId) {
            $fundAccount = $api->fundAccount->create([
                'contact_id'   => $contactId,
                'account_type' => 'bank_account',
                'bank_account' => [
                    'name'           => $employee->account_holder_name ?? $employee->user->name,
                    'ifsc'           => strtoupper($employee->bank_ifsc),
                    'account_number' => $employee->bank_account_no,
                ],
            ]);
            $fundAccountId = $fundAccount->id;
            $employee->update(['razorpay_fund_account_id' => $fundAccountId]);
        }

        $amountInPaise = (int) round($salarySlip->net_pay * 100);
        $payout = $api->payout->create([
            'account_number'       => $settings->razorpay_account_number,
            'fund_account_id'      => $fundAccountId,
            'amount'               => $amountInPaise,
            'currency'             => 'INR',
            'mode'                 => 'IMPS',
            'purpose'              => 'salary',
            'queue_if_low_balance' => true,
            'narration'            => 'Salary ' . $salarySlip->month . ' ' . $salarySlip->year,
            'reference_id'         => 'SLIP-' . $salarySlip->id,
        ]);

        $salarySlip->update([
            'razorpay_payout_id'  => $payout->id,
            'payout_status'       => $payout->status,
            'payout_mode'         => 'IMPS',
            'payout_initiated_at' => now(),
            'payout_response'     => json_encode($payout->toArray()),
            'status'              => $payout->status === 'processed' ? 'Paid' : 'Pending',
            'payment_date'        => $payout->status === 'processed' ? now()->toDateString() : null,
        ]);
    }
}
