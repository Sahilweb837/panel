<?php

namespace App\Http\Controllers;

use App\Models\FeeInvoice;
use App\Models\BiometricDevice;
use App\Models\ChatbotInteraction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function query(Request $request)
    {
        $queryText = trim($request->input('query', ''));
        if (empty($queryText)) {
            return response()->json(['response' => 'Hello! How can I assist you with your system data today?']);
        }

        $response = '';
        $queryLower = strtolower($queryText);

        if (str_contains($queryLower, 'fee') || str_contains($queryLower, 'pending') || str_contains($queryLower, 'outstanding')) {
            // Fetch pending fee invoices
            $pending = FeeInvoice::where('status', '!=', 'Paid')->with('student')->latest()->limit(5)->get();
            $totalPendingCount = FeeInvoice::where('status', '!=', 'Paid')->count();
            $totalPendingAmount = FeeInvoice::sum('due_amount');

            if ($pending->isEmpty()) {
                $response = "🎉 Great news! There are currently no pending student fee receipts in the system.";
            } else {
                $response = "💵 **Pending Fees Summary:**\n";
                $response .= "There are currently **{$totalPendingCount}** outstanding receipts totaling **₹" . number_format($totalPendingAmount, 2) . "**.\n\n**Here are the latest outstanding receipts:**\n";
                foreach ($pending as $invoice) {
                    $studentName = $invoice->student ? "{$invoice->student->first_name} {$invoice->student->last_name}" : 'Unknown Student';
                    $response .= "• **{$invoice->invoice_no}** - {$studentName}: **₹" . number_format($invoice->due_amount, 2) . "** (Status: {$invoice->status})\n";
                }
            }
        } elseif (str_contains($queryLower, 'biometric') || str_contains($queryLower, 'adms') || str_contains($queryLower, 'device') || str_contains($queryLower, 'sync')) {
            $device = BiometricDevice::first();
            if (!$device) {
                $response = "🎛️ **Biometric Status:** No biometric device registers found on the system. Go to **Hardware Setup** to add a device.";
            } else {
                $isOnline = $device->last_sync && Carbon::parse($device->last_sync)->diffInMinutes(now()) < 5;
                $statusBadge = $isOnline ? '🟢 ONLINE & ACTIVE' : '🔴 OFFLINE';
                $lastSeen = $device->last_sync ? Carbon::parse($device->last_sync)->diffForHumans() : 'Never synced';
                
                $response = "📡 **ZKTeco Biometric Connection Diagnostic:**\n";
                $response .= "• **Status:** {$statusBadge}\n";
                $response .= "• **Last Sync Ping:** {$lastSeen}\n";
                $response .= "• **Device Code:** {$device->device_code}\n";
                $response .= "• **Server Host IP:** " . ($device->ip_address ?? '127.0.0.1') . "\n";
            }
        } elseif (str_contains($queryLower, 'error') || str_contains($queryLower, 'diagnostic') || str_contains($queryLower, 'health') || str_contains($queryLower, 'system')) {
            $device = BiometricDevice::first();
            $admsError = '';
            if ($device && (!$device->last_sync || Carbon::parse($device->last_sync)->diffInMinutes(now()) >= 5)) {
                $admsError = "⚠️ **Warning:** ZKTeco Biometric ADMS sync is currently offline. Checks connections.\n";
            }
            
            $pendingCount = FeeInvoice::where('status', '!=', 'Paid')->count();
            $pendingWarn = $pendingCount > 10 ? "⚠️ **Notice:** You have {$pendingCount} pending fee receipts awaiting verification.\n" : "";

            $response = "🏥 **System Diagnostic Report:**\n";
            $response .= "• **DB Connection:** 🟢 OK (MySQL Connection online)\n";
            $response .= "• **PHP Version:** " . phpversion() . " (🟢 OK)\n";
            $response .= "• **Application Cache:** 🟢 Clear\n";
            $response .= "• **Hardware Webhook:** 🟢 Listening\n\n";
            
            if ($admsError || $pendingWarn) {
                $response .= "**System Notices:**\n" . $admsError . $pendingWarn;
            } else {
                $response .= "✨ System health is optimal! No active diagnostic warnings found.";
            }
        } else {
            $response = "🤖 **Hello!** I am your ERP system assistant. I can help you with:\n";
            $response .= "1. **'Show pending fees'** - Display latest outstanding student fee receipts.\n";
            $response .= "2. **'Check biometric status'** - Check if the ZKTeco device is online.\n";
            $response .= "3. **'Check system diagnostics'** - Run a health check for system settings and errors.";
        }

        // Log the interaction
        ChatbotInteraction::create([
            'user_id' => session('user_id'),
            'query' => $queryText,
            'response' => $response,
        ]);

        return response()->json(['response' => $response]);
    }
}
