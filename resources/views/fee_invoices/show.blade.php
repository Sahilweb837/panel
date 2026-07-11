<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $feeInvoice->invoice_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            padding: 30px 20px;
            color: #1a1a1a;
        }
        .receipt {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            color: #000;
            border: 1px solid #ddd;
            page-break-after: always;
        }
        .receipt-header {
            background: #fff;
            color: #000;
            border-bottom: 2px solid #000;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
         .logo { display: flex; align-items: center; gap: 14px; }
          .logo-img {
              max-height: 79px;
              max-width: 267px;
              object-fit: contain;
              align-self: flex-start;
          }
        .receipt-header .invoice-meta { text-align: right; }
        .receipt-header .invoice-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #ff5532;
            letter-spacing: -1px;
            margin-bottom: 13px;
        }
        .receipt-header .invoice-no { font-size: 0.85rem; opacity: 0.8; margin-top: 2px; }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 2px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: 1px solid #000;
            color: #000;
            background: #fff;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 1px solid #eee;
        }
        .info-block {
            padding: 16px 24px;
        }
        .info-block:first-child { border-right: 1px solid #eee; }
        .info-block .label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #999;
            margin-bottom: 6px;
        }
        .info-block .value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.5;
        }
        .info-block .value.row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
        }

        .table-section { padding: 0; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background: #f8f8f8;
            padding: 10px 24px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #666;
            border-bottom: 2px solid #eee;
            text-align: left;
        }
        table th:last-child { text-align: right; }
        table td {
            padding: 10px 24px;
            font-size: 0.85rem;
            border-bottom: 1px solid #f2f2f2;
        }
        table td:last-child { text-align: right; font-weight: 600; }
        table tr.section-header td {
            background: #f0f0f0;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #000;
            padding: 5px 24px;
            border-bottom: 1px solid #000;
        }
        table tr.tenure-row td {
            background: #f8f8f8;
            font-size: 0.75rem;
            color: #666;
            font-style: italic;
            padding: 4px 24px;
        }

        .summary-section {
            background: #fff;
            padding: 16px 24px;
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid #eee;
        }
        .summary-card {
            width: 260px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            padding: 4px 0;
            color: #555;
        }
        .summary-row strong { color: #1a1a1a; }
        .summary-row.total {
            border-top: 2px solid #1a1a1a;
            margin-top: 6px;
            padding-top: 8px;
            font-size: 1rem;
            font-weight: 800;
            color: #1a1a1a;
        }
        .summary-row.due { color: #dc2626; font-weight: 700; }

        .footer {
            padding: 14px 28px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            color: #000;
        }
        .footer .notes {
            font-size: 0.7rem;
            color: #999;
            max-width: 60%;
            line-height: 1.5;
        }
        .sign-block { text-align: center; }
        .sign-line {
            width: 120px;
            height: 1px;
            background: #1a1a1a;
            margin-bottom: 4px;
        }
        .sign-text {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #666;
        }

        .cut-line {
            text-align: center;
            margin: 20px 0;
            color: #ccc;
            font-size: 0.85rem;
        }
        .cut-line::before,
        .cut-line::after {
            content: '';
            display: inline-block;
            width: 40%;
            border-top: 2px dashed #ddd;
            vertical-align: middle;
            margin: 0 10px;
        }

        .action-bar {
            max-width: 780px;
            margin: 0 auto 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back { background: #fff; color: #333; border: 1px solid #ddd; }
        .btn-print { background: #1a1a1a; color: #fff; }

        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none !important; }
            .receipt { box-shadow: none; border-radius: 0; }
            @page { size: A4; margin: 0.8cm; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="{{ route('fee_invoices.index') }}" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back</a>
    <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
</div>

@php
    $copies = ['STUDENT COPY', 'OFFICE COPY'];
    $items = is_string($feeInvoice->fee_items) ? json_decode($feeInvoice->fee_items, true) : ($feeInvoice->fee_items ?? []);
    $oneTime = collect($items)->filter(fn($i) => in_array($i['category'] ?? '', ['Registration Fee', 'Prospectus Fee']))->values();
    $regular = collect($items)->reject(fn($i) => in_array($i['category'] ?? '', ['Registration Fee', 'Prospectus Fee']))->values();
    $statusBadge = match($feeInvoice->status) {
        'Paid' => 'badge-paid',
        'Partial' => 'badge-partial',
        default => 'badge-unpaid',
    };
    $tenureLabel = null;
    $isMonthlyFee = $feeInvoice->billing_month && $feeInvoice->billing_year;
    $billingPeriod = $isMonthlyFee ? \Carbon\Carbon::create()->month($feeInvoice->billing_month)->format('F') . ' ' . $feeInvoice->billing_year : null;
    
    if ($regular->isNotEmpty()) {
        $cat = $regular->first()['category'] ?? '';
        if (preg_match('/\((.+?)\)/', $cat, $m)) {
            $tenureLabel = $m[1];
        }
    }
@endphp

@foreach($copies as $index => $copyName)
<div class="receipt">
    <div class="receipt-header">
        <div class="logo" style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
            <img src="{{ asset('image.png') }}" alt="Netcoder Technology" class="logo-img" />
            <div style="font-size: 0.72rem; color: #555; margin-top: 4px; line-height: 1.4; text-align: left;">
                1st Floor, above Gramin Bank, (Near ITI Bridge), Dari,<br>
                Dharamshala, Himachal Pradesh, 176057<br>
                Phone: 098167 32055 | 7590832055<br>
                Website: www.netcoder.in | Email: support@netcoder.in
            </div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">FEE RECEIPT</div>
            <div class="invoice-no">#{{ $feeInvoice->invoice_no }}</div>
            @if($isMonthlyFee)
                <div class="invoice-period" style="font-size: 0.75rem; color: #666; margin-top: 2px;">
                    <i class="fas fa-calendar-alt me-1"></i> Billing Period: {{ $billingPeriod }}
                </div>
            @endif
            <span class="badge {{ $statusBadge }}" style="margin-top:6px;">{{ $feeInvoice->status }}</span>
        </div>
    </div>

    <div class="info-section">
        <div class="info-block">
            <div class="label">Student Details</div>
            <div class="value">
                {{ $feeInvoice->student?->first_name ?? 'N/A' }} {{ $feeInvoice->student?->last_name ?? '' }}<br>
                <span style="color:#888; font-weight:400;">{{ $feeInvoice->student?->admission_no ?? '-' }}</span>
            </div>
            <div class="value" style="margin-top:6px;">
                Course: {{ $feeInvoice->student?->course?->name ?? '-' }}
                @if($tenureLabel)
                    <br><span style="color:#ff5532; font-weight:600;"><i class="fas fa-calendar-days"></i> {{ $tenureLabel }}</span>
                @endif
                @if($isMonthlyFee)
                    <br><span style="color:#0d9488; font-weight:600;"><i class="fas fa-calendar-alt"></i> Billing: {{ $billingPeriod }}</span>
                @endif
            </div>
        </div>
        <div class="info-block">
            <div class="label">Payment Info</div>
            <div class="value">
                Date: {{ $feeInvoice->payment_date ? \Carbon\Carbon::parse($feeInvoice->payment_date)->format('M d, Y') : ($feeInvoice->created_at?->format('M d, Y') ?? '-') }}<br>
                Method: {{ $feeInvoice->payment_method ?? 'N/A' }}
                @if($feeInvoice->transaction_id)
                    <br><span style="color:#888; font-weight:400;">Txn: {{ $feeInvoice->transaction_id }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th>Particulars</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($oneTime->count() > 0)
                    <tr class="section-header"><td colspan="2">Admission Fees (One Time)</td></tr>
                    @foreach($oneTime as $item)
                        <tr>
                            <td>{{ $item['category'] }}</td>
                            <td>₹{{ number_format($item['amount'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                @endif

                @if($regular->count() > 0)
                    <tr class="section-header"><td colspan="2">{{ $isMonthlyFee ? 'Monthly Course Fee' : 'Course Fee (EMI / Installment)' }}</td></tr>
                    @foreach($regular as $item)
                        @php
                            $cat = $item['category'] ?? '';
                            $itemAmt = $item['amount'] ?? 0;
                            $baseFee = $feeInvoice->student?->course?->fee ?? 0;
                            $monthlyEquiv = '';
                            $isFine = in_array($cat, ['Late Fine', 'Attendance Fine', 'Custom Fee', 'Custom / Extra Fee']);
                            if (preg_match('/\((.+?)\)/', $cat, $m)) {
                                $tenure = $m[1];
                                $mCount = match($tenure) { '1 Month' => 1, '3 Months' => 3, '6 Months' => 6, '1 Year' => 12, default => 1 };
                                $monthlyEquiv = $baseFee / max(1, $mCount);
                            }
                        @endphp
                        @if(!$isFine)
                        <tr>
                            <td>
                                <strong>{{ $cat }}</strong>
                                @if($monthlyEquiv)
                                    <br><small style="color:#666;">
                                        ₹{{ number_format($monthlyEquiv, 2) }}/month × {{ $tenure }}
                                        <br>Total Course Fee: ₹{{ number_format($baseFee, 2) }}
                                    </small>
                                @endif
                            </td>
                            <td>₹{{ number_format($itemAmt, 2) }}</td>
                        </tr>
                        @endif
                    @endforeach
                    
                    @php
                        $fineItems = $regular->filter(fn($i) => in_array($i['category'] ?? '', ['Late Fine', 'Attendance Fine', 'Custom Fee', 'Custom / Extra Fee']))->values();
                    @endphp
                    @if($fineItems->count() > 0)
                        <tr class="section-header"><td colspan="2">Fines & Extra Charges</td></tr>
                        @foreach($fineItems as $item)
                            <tr>
                                <td>
                                    <strong style="color: {{ in_array($item['category'], ['Late Fine', 'Attendance Fine']) ? '#dc2626' : '#1a1a1a' }};">{{ $item['category'] }}</strong>
                                </td>
                                <td style="color: {{ in_array($item['category'], ['Late Fine', 'Attendance Fine']) ? '#dc2626' : '#1a1a1a' }};">₹{{ number_format($item['amount'] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                @elseif($oneTime->count() === 0)
                    <tr>
                        <td><strong>{{ $feeInvoice->fee_category ?? 'Course Fee' }}</strong></td>
                        <td>₹{{ number_format($feeInvoice->total_amount, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @php
        // Calculate sub-total (non-fine items)
        $subTotal = collect($items)->filter(function($i) {
            $cat = strtolower($i['category'] ?? '');
            return !str_contains($cat, 'fine');
        })->sum('amount');

        // Fine items from fee_items
        $fineItemsFromList = collect($items)->filter(function($i) {
            $cat = strtolower($i['category'] ?? '');
            return str_contains($cat, 'fine');
        })->values();
        $lateFineTotal = $fineItemsFromList->filter(fn($i) => str_contains(strtolower($i['category'] ?? ''), 'late'))->sum('amount');
        $attendanceFineTotal = $fineItemsFromList->filter(fn($i) => str_contains(strtolower($i['category'] ?? ''), 'attendance') || str_contains(strtolower($i['category'] ?? ''), 'absent'))->sum('amount');
        $otherFines = $fineItemsFromList->sum('amount') - $lateFineTotal - $attendanceFineTotal;

        // Use fine column if no fine items in the list
        $totalFineAmount = $fineItemsFromList->sum('amount');
        if ($totalFineAmount == 0 && $feeInvoice->fine > 0) {
            $totalFineAmount = $feeInvoice->fine;
        }

        $totalPayable = $subTotal + $totalFineAmount - $feeInvoice->discount;
    @endphp

    <div class="summary-section">
        <div class="summary-card">
            <div class="summary-row"><span>Sub-total:</span><strong>₹{{ number_format($subTotal, 2) }}</strong></div>

            @if($feeInvoice->discount > 0)
            <div class="summary-row"><span>Discount:</span><strong style="color:#10b981;">- ₹{{ number_format($feeInvoice->discount, 2) }}</strong></div>
            @endif

            @if($lateFineTotal > 0)
            <div class="summary-row"><span>Late Fine:</span><strong style="color:#dc2626;">+ ₹{{ number_format($lateFineTotal, 2) }}</strong></div>
            @endif
            @if($attendanceFineTotal > 0)
            <div class="summary-row"><span>Attendance Fine:</span><strong style="color:#f59e0b;">+ ₹{{ number_format($attendanceFineTotal, 2) }}</strong></div>
            @endif
            @if($otherFines > 0)
            <div class="summary-row"><span>Other Fine:</span><strong style="color:#dc2626;">+ ₹{{ number_format($otherFines, 2) }}</strong></div>
            @endif
            @if($feeInvoice->fine > 0 && $totalFineAmount == $feeInvoice->fine && $fineItemsFromList->count() == 0)
            <div class="summary-row"><span>Fine:</span><strong style="color:#dc2626;">+ ₹{{ number_format($feeInvoice->fine, 2) }}</strong></div>
            @endif

            <div class="summary-row" style="border-top: 1px solid #ddd; margin-top: 4px; padding-top: 6px;">
                <span><strong>Total Payable:</strong></span>
                <strong>₹{{ number_format($totalPayable, 2) }}</strong>
            </div>

            <div class="summary-row total">
                <span>Amount Paid</span>
                <span>₹{{ number_format($feeInvoice->paid_amount, 2) }}</span>
            </div>
            @if($feeInvoice->due_amount > 0)
            <div class="summary-row due"><span>Balance Due:</span><strong>₹{{ number_format($feeInvoice->due_amount, 2) }}</strong></div>
            @endif
        </div>
    </div>

    {{-- Overall Student Account Summary (Office copy only) --}}
    @if($index === 1)
    <div style="padding: 10px 24px; border-top: 2px solid #000; background: #fafafa;">
        <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #666; margin-bottom: 6px;">Overall Student Account</div>
        <div style="display: flex; gap: 20px; font-size: 0.78rem;">
            <div><strong>Course Fee:</strong> ₹{{ number_format($overallTotal, 2) }}</div>
            <div style="color: #10b981;"><strong>Paid:</strong> ₹{{ number_format($overallPaid, 2) }}</div>
            <div style="color: #dc2626;"><strong>Remaining:</strong> ₹{{ number_format($overallDue, 2) }}</div>
            @if($totalFinesDue > 0)
            <div style="color: #f59e0b;"><strong>Unpaid Fines:</strong> ₹{{ number_format($totalFinesDue, 2) }}</div>
            @endif
        </div>
    </div>
    @endif

    <div class="footer">
        <div class="notes" style="font-size: 0.72rem; color: #555; line-height: 1.4;">
            * Fees once paid are non-refundable and non-transferable.<br>
            * Prospectus & Registration fees are separate from course fee.<br>
            * Please retain this receipt for future reference.
            @if($isMonthlyFee)
                <br>* Billing Period: {{ $billingPeriod }}
            @endif
            @if($feeInvoice->remarks)
                <br>* Remarks: {{ $feeInvoice->remarks }}
            @endif
        </div>
        <div class="sign-block" style="text-align: center;">
            <h4 style="font-size: 0.85rem; font-weight: 800; color: #1a1a1a;">NETCODER TECHNOLOGY</h4>
            <div style="margin-top: 15px;">
                <div class="sign-line" style="margin: 0 auto 4px;"></div>
                <p style="font-size: 0.8rem; font-weight: 700; color: #1a1a1a;">Seal &amp; Signature</p>
            </div>
        </div>
    </div>
</div>

@if($index === 0)
<div class="cut-line">CUT HERE</div>
@endif
@endforeach

<script>
document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const theme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', theme);
});
</script>
</body>
</html>