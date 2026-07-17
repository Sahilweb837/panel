<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Receipt - {{ $feeInvoice->invoice_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary: #ff5532;
            --secondary: #0f172a;
            --accent: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            padding: 40px 20px;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        .receipt-container {
            max-width: 850px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .receipt {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0,0,0,0.02);
            overflow: hidden;
            position: relative;
            page-break-inside: avoid;
        }

        /* Top Color Bar */
        .receipt::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), #ffa032);
        }

        .receipt-header {
            padding: 40px 40px 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--border-light);
        }

        .brand-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .brand-logo-html {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--secondary);
            letter-spacing: -0.5px;
        }

        .brand-logo-html i {
            color: var(--primary);
            background: rgba(255, 85, 50, 0.1);
            padding: 10px;
            border-radius: 12px;
            font-size: 1.5rem;
        }

        .brand-details {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--border-light);
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1;
            margin-bottom: 15px;
        }

        .invoice-no {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .invoice-no span {
            color: var(--text-muted);
            font-weight: 500;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 12px;
        }
        .badge-paid { background: #dcfce7; color: var(--success); }
        .badge-unpaid { background: #fee2e2; color: var(--danger); }
        .badge-partial { background: #fef3c7; color: var(--warning); }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 30px 40px;
            background: var(--accent);
            border-bottom: 1px solid var(--border-light);
        }

        .info-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-main);
            line-height: 1.5;
        }

        .info-sub {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .table-container {
            padding: 30px 40px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        th {
            background: var(--accent);
            padding: 12px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 2px solid var(--border-light);
        }

        th:last-child { text-align: right; }

        td {
            padding: 16px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-main);
            vertical-align: middle;
        }

        td:last-child { 
            text-align: right; 
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
        }

        .item-name {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 4px;
        }

        .item-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        tr.section-header td {
            background: var(--accent);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            padding: 8px 16px;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
            padding: 0 40px 30px;
        }

        .totals-box {
            width: 320px;
            background: var(--accent);
            border-radius: 12px;
            padding: 24px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            padding: 8px 0;
            color: var(--text-muted);
        }

        .total-row.discount { color: var(--success); }
        .total-row.fine { color: var(--danger); }

        .total-row.grand-total {
            border-top: 2px solid var(--border-light);
            margin-top: 12px;
            padding-top: 16px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--secondary);
        }

        .total-row.paid {
            font-weight: 600;
            color: var(--text-main);
        }

        .total-row.due {
            background: #fee2e2;
            color: var(--danger);
            padding: 10px 12px;
            border-radius: 8px;
            margin-top: 8px;
            font-weight: 700;
        }

        .receipt-footer {
            padding: 30px 40px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .terms {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 60%;
        }

        .terms ul {
            padding-left: 16px;
            margin-top: 6px;
        }

        .signature {
            text-align: center;
        }

        .sign-line {
            width: 140px;
            height: 1px;
            background: var(--border-light);
            margin-bottom: 8px;
        }

        .sign-text {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
        }

        .copy-badge {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--border-light);
            color: var(--text-muted);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .cut-line {
            text-align: center;
            margin: 0;
            color: var(--text-muted);
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .cut-line::before,
        .cut-line::after {
            content: '';
            flex: 1;
            border-top: 2px dashed var(--border-light);
        }

        .action-bar {
            max-width: 850px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-back { 
            background: #ffffff; 
            color: var(--text-main); 
            border: 1px solid var(--border-light); 
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .btn-back:hover { background: var(--accent); }

        .btn-print { 
            background: var(--secondary); 
            color: #ffffff; 
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(15, 23, 42, 0.3); }

        @media print {
            body { background: #fff; padding: 0; }
            .receipt-container { gap: 20px; }
            .action-bar { display: none !important; }
            .receipt { box-shadow: none; border: 1px solid var(--border-light); page-break-inside: avoid; }
            @page { size: A4; margin: 1cm; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="{{ route('fee_invoices.index') }}" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back to Invoices</a>
    <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print Receipt</button>
</div>

<div class="receipt-container">
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
        <div class="copy-badge">{{ $copyName }}</div>
        
        <div class="receipt-header">
            <div class="brand-section">
                <div class="brand-logo-html">
                    <i class="fas fa-layer-group"></i> Netcoder ERP
                </div>
                <div class="brand-details">
                    1st Floor, above Gramin Bank, (Near ITI Bridge)<br>
                    Dari, Dharamshala, Himachal Pradesh, 176057<br>
                    <i class="fas fa-phone-alt me-1 mt-2"></i> 098167 32055 | 7590832055<br>
                    <i class="fas fa-globe me-1"></i> www.netcoder.in | <i class="fas fa-envelope me-1"></i> support@netcoder.in
                </div>
            </div>
            <div class="invoice-meta">
                <div class="invoice-title">RECEIPT</div>
                <div class="invoice-no"><span>Invoice:</span> #{{ $feeInvoice->invoice_no }}</div>
                @if($isMonthlyFee)
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                        <i class="fas fa-calendar-alt me-1"></i> {{ $billingPeriod }}
                    </div>
                @endif
                <div class="badge {{ $statusBadge }}"><i class="fas fa-circle me-2" style="font-size:6px;"></i> {{ $feeInvoice->status }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-group">
                <div class="info-label">Billed To</div>
                <div class="info-value">{{ $feeInvoice->student?->first_name ?? 'N/A' }} {{ $feeInvoice->student?->last_name ?? '' }}</div>
                <div class="info-sub">Admission No: {{ $feeInvoice->student?->admission_no ?? '-' }}</div>
                <div class="info-sub mt-1">Course: <strong>{{ $feeInvoice->student?->course?->name ?? '-' }}</strong></div>
                @if($tenureLabel)
                    <div class="info-sub"><i class="fas fa-clock text-primary"></i> Tenure: {{ $tenureLabel }}</div>
                @endif
            </div>
            <div class="info-group" style="text-align: right;">
                <div class="info-label">Payment Details</div>
                <div class="info-value">Date: {{ $feeInvoice->payment_date ? \Carbon\Carbon::parse($feeInvoice->payment_date)->format('M d, Y') : ($feeInvoice->created_at?->format('M d, Y') ?? '-') }}</div>
                <div class="info-sub">Method: <strong>{{ $feeInvoice->payment_method ?? 'N/A' }}</strong></div>
                @if($feeInvoice->transaction_id)
                    <div class="info-sub">Txn ID: {{ $feeInvoice->transaction_id }}</div>
                @endif
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if($oneTime->count() > 0)
                        <tr class="section-header"><td colspan="2">Admission & One-Time Fees</td></tr>
                        @foreach($oneTime as $item)
                            <tr>
                                <td><div class="item-name">{{ $item['category'] }}</div></td>
                                <td>₹{{ number_format($item['amount'] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    @if($regular->count() > 0)
                        <tr class="section-header"><td colspan="2">{{ $isMonthlyFee ? 'Monthly Course Fee' : 'Course Fee Installments' }}</td></tr>
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
                                    <div class="item-name">{{ $cat }}</div>
                                    @if($monthlyEquiv)
                                        <div class="item-desc">
                                            ₹{{ number_format($monthlyEquiv, 2) }}/month × {{ $tenure }} (Base: ₹{{ number_format($baseFee, 2) }})
                                        </div>
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
                            <tr class="section-header"><td colspan="2">Fines & Penalties</td></tr>
                            @foreach($fineItems as $item)
                                <tr>
                                    <td>
                                        <div class="item-name" style="color: var(--danger);">
                                            <i class="fas fa-exclamation-circle me-1"></i> {{ $item['category'] }}
                                        </div>
                                    </td>
                                    <td style="color: var(--danger);">₹{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                    @elseif($oneTime->count() === 0)
                        <tr>
                            <td><div class="item-name">{{ $feeInvoice->fee_category ?? 'Course Fee' }}</div></td>
                            <td>₹{{ number_format($feeInvoice->total_amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @php
            $subTotal = collect($items)->filter(function($i) {
                return !str_contains(strtolower($i['category'] ?? ''), 'fine');
            })->sum('amount');

            $fineItemsFromList = collect($items)->filter(function($i) {
                return str_contains(strtolower($i['category'] ?? ''), 'fine');
            })->values();
            $lateFineTotal = $fineItemsFromList->filter(fn($i) => str_contains(strtolower($i['category'] ?? ''), 'late'))->sum('amount');
            $attendanceFineTotal = $fineItemsFromList->filter(fn($i) => str_contains(strtolower($i['category'] ?? ''), 'attendance') || str_contains(strtolower($i['category'] ?? ''), 'absent'))->sum('amount');
            $otherFines = $fineItemsFromList->sum('amount') - $lateFineTotal - $attendanceFineTotal;

            $totalFineAmount = $fineItemsFromList->sum('amount');
            if ($totalFineAmount == 0 && $feeInvoice->fine > 0) {
                $totalFineAmount = $feeInvoice->fine;
            }

            $totalPayable = $subTotal + $totalFineAmount - $feeInvoice->discount;
        @endphp

        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row"><span>Sub-total</span> <span>₹{{ number_format($subTotal, 2) }}</span></div>
                
                @if($feeInvoice->discount > 0)
                <div class="total-row discount"><span>Discount applied</span> <span>- ₹{{ number_format($feeInvoice->discount, 2) }}</span></div>
                @endif
                
                @if($lateFineTotal > 0)
                <div class="total-row fine"><span>Late Fine</span> <span>+ ₹{{ number_format($lateFineTotal, 2) }}</span></div>
                @endif
                @if($attendanceFineTotal > 0)
                <div class="total-row fine" style="color: var(--warning);"><span>Attendance Fine</span> <span>+ ₹{{ number_format($attendanceFineTotal, 2) }}</span></div>
                @endif
                @if($otherFines > 0)
                <div class="total-row fine"><span>Other Fines</span> <span>+ ₹{{ number_format($otherFines, 2) }}</span></div>
                @endif
                @if($feeInvoice->fine > 0 && $totalFineAmount == $feeInvoice->fine && $fineItemsFromList->count() == 0)
                <div class="total-row fine"><span>Fines</span> <span>+ ₹{{ number_format($feeInvoice->fine, 2) }}</span></div>
                @endif

                <div class="total-row grand-total">
                    <span>Total Amount</span>
                    <span>₹{{ number_format($totalPayable, 2) }}</span>
                </div>

                <div class="total-row paid">
                    <span>Amount Paid</span>
                    <span>₹{{ number_format($feeInvoice->paid_amount, 2) }}</span>
                </div>

                @if($feeInvoice->due_amount > 0)
                <div class="total-row due">
                    <span>Balance Due</span>
                    <span>₹{{ number_format($feeInvoice->due_amount, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        @if($index === 1)
        <div style="padding: 20px 40px; background: #fffbeb; border-top: 1px solid #fef3c7;">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #b45309; margin-bottom: 8px;"><i class="fas fa-chart-pie me-1"></i> Office Use - Overall Account Summary</div>
            <div style="display: flex; gap: 30px; font-size: 0.85rem; color: #78350f;">
                <div>Total Course Fee: <strong>₹{{ number_format($overallTotal, 2) }}</strong></div>
                <div>Total Paid: <strong style="color: var(--success);">₹{{ number_format($overallPaid, 2) }}</strong></div>
                <div>Remaining Balance: <strong style="color: var(--danger);">₹{{ number_format($overallDue, 2) }}</strong></div>
                @if($totalFinesDue > 0)
                <div>Unpaid Fines: <strong style="color: var(--warning);">₹{{ number_format($totalFinesDue, 2) }}</strong></div>
                @endif
            </div>
        </div>
        @endif

        <div class="receipt-footer">
            <div class="terms">
                <strong>Terms & Conditions:</strong>
                <ul>
                    <li>Fees once paid are non-refundable and non-transferable.</li>
                    <li>Please retain this receipt for future reference.</li>
                    @if($feeInvoice->remarks)
                        <li style="color: var(--secondary); font-weight: 600;">Remarks: {{ $feeInvoice->remarks }}</li>
                    @endif
                </ul>
            </div>
            <div class="signature">
                <div class="sign-line"></div>
                <div class="sign-text">Authorized Signature</div>
            </div>
        </div>
    </div>

    @if($index === 0)
    <div class="cut-line">
        <i class="fas fa-scissors"></i>
    </div>
    @endif
@endforeach
</div>

</body>
</html>