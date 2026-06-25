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
            max-width: 780px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .receipt-header {
            background: #1a1a1a;
            color: #fff;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .receipt-header .logo { font-weight: 800; font-size: 1.1rem; }
        .receipt-header .invoice-meta { text-align: right; }
        .receipt-header .invoice-title { font-size: 1.3rem; font-weight: 800; letter-spacing: 1px; }
        .receipt-header .invoice-no { font-size: 0.85rem; opacity: 0.8; margin-top: 2px; }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-unpaid { background: #fee2e2; color: #991b1b; }
        .badge-partial { background: #fef3c7; color: #92400e; }

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
            background: #ff553212;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #ff5532;
            padding: 5px 24px;
        }
        table tr.tenure-row td {
            background: #f8f8f8;
            font-size: 0.75rem;
            color: #666;
            font-style: italic;
            padding: 4px 24px;
        }

        .summary-section {
            background: #fafafa;
            padding: 16px 24px;
            display: flex;
            justify-content: flex-end;
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
        <div class="logo">
            <div style="font-weight:800; font-size:1rem;">Netcoder</div>
            <div style="font-size:0.7rem; opacity:0.7; font-weight:400;">Technology Solutions</div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">FEE RECEIPT</div>
            <div class="invoice-no">#{{ $feeInvoice->invoice_no }}</div>
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
                    <tr class="section-header"><td colspan="2">Course Fee</td></tr>
                    @foreach($regular as $item)
                        <tr>
                            <td>{{ $item['category'] }}</td>
                            <td>₹{{ number_format($item['amount'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                @elseif($oneTime->count() === 0)
                    <tr>
                        <td><strong>{{ $feeInvoice->fee_category ?? 'Course Fee' }}</strong></td>
                        <td>₹{{ number_format($feeInvoice->total_amount, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="summary-section">
        <div class="summary-card">
            @if($feeInvoice->discount > 0)
            <div class="summary-row"><span>Discount:</span><strong style="color:#10b981;">- ₹{{ number_format($feeInvoice->discount, 2) }}</strong></div>
            @endif
            @if($feeInvoice->fine > 0)
            <div class="summary-row"><span>Fine:</span><strong>+ ₹{{ number_format($feeInvoice->fine, 2) }}</strong></div>
            @endif
            <div class="summary-row total">
                <span>Paid</span>
                <span>₹{{ number_format($feeInvoice->paid_amount, 2) }}</span>
            </div>
            @if($feeInvoice->due_amount > 0)
            <div class="summary-row due"><span>Balance Due:</span><strong>₹{{ number_format($feeInvoice->due_amount, 2) }}</strong></div>
            @endif
        </div>
    </div>

    <div class="footer">
        <div class="notes">
            * Computer-generated receipt. Fees once paid are not refundable.
            @if($feeInvoice->remarks)
                <br>* {{ $feeInvoice->remarks }}
            @endif
        </div>
        <div class="sign-block">
            <div class="sign-line"></div>
            <div class="sign-text">Authorized Signatory</div>
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