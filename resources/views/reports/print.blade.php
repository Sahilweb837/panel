<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Collection Report - {{ date('Y-m-d') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #ffffff;
            color: #1c1816;
            padding: 30px;
            font-size: 0.9rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px dashed #e9e4e0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-img {
            max-height: 48px;
            width: auto;
        }
        .company-name {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .company-sub {
            font-size: 0.75rem;
            color: #6e645e;
        }
        .report-title {
            text-align: right;
        }
        .report-title h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ff5532;
        }
        .report-title p {
            font-size: 0.8rem;
            color: #6e645e;
            margin-top: 4px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .summary-card {
            border: 1px solid #e9e4e0;
            border-radius: 12px;
            padding: 16px;
            background: #fbf9f8;
        }
        .summary-card p {
            font-size: 0.75rem;
            font-weight: 700;
            color: #6e645e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .summary-card h3 {
            font-size: 1.3rem;
            font-weight: 800;
        }
        .summary-card.success h3 { color: #10b981; }
        .summary-card.danger h3 { color: #ef4444; }
        .table-section {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #1c1816;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e9e4e0;
            font-size: 0.82rem;
        }
        tr:nth-child(even) td {
            background: #fdfcfc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-unpaid { background: #fee2e2; color: #991b1b; }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 40px;
            border-top: 1px solid #e9e4e0;
            padding-top: 15px;
            font-size: 0.75rem;
            color: #6e645e;
        }
        .action-bar {
            max-width: 100%;
            background: #fbf9f8;
            border: 1px solid #e9e4e0;
            padding: 12px 24px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-back { background: #ffffff; color: #1c1816; border: 1px solid #e9e4e0; }
        .btn-print { background: #ff5532; color: #ffffff; }
        
        @media print {
            body { padding: 0; }
            .action-bar { display: none !important; }
            th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .summary-card { background: #ffffff !important; }
            @page { size: A4 landscape; margin: 1cm; }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <a href="{{ route('reports.index', request()->all()) }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>

    <div class="header">
        <div class="logo-block">
            <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="{{ \App\Models\Setting::get('institute_name', 'Netcoder') }} Logo" class="logo-img" />
            <div>
                <div class="company-name">Netcoder Institute</div>
                <div class="company-sub">Complete ERP & Fees Management</div>
            </div>
        </div>
        <div class="report-title">
            <h2>COLLECTION REPORT</h2>
            <p>
                @if($fromDate && $toDate)
                    Period: {{ $fromDate->format('M d, Y') }} - {{ $toDate->format('M d, Y') }}
                @elseif($fromDate)
                    From: {{ $fromDate->format('M d, Y') }}
                @elseif($toDate)
                    Up to: {{ $toDate->format('M d, Y') }}
                @else
                    All-Time Report
                @endif
                | Generated: {{ date('M d, Y h:i A') }}
            </p>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card success">
            <p>Total Collected</p>
            <h3>₹{{ number_format($totalCollected, 2) }}</h3>
        </div>
        <div class="summary-card">
            <p>Total Discounts</p>
            <h3>₹{{ number_format($totalDiscount, 2) }}</h3>
        </div>
        <div class="summary-card">
            <p>Total Fines</p>
            <h3>₹{{ number_format($totalFine, 2) }}</h3>
        </div>
        <div class="summary-card danger">
            <p>Total Outstanding</p>
            <h3>₹{{ number_format($totalDue, 2) }}</h3>
        </div>
    </div>

    <div class="table-section">
        <table>
            <thead>
                <tr>
                    <th>Receipt No</th>
                    <th>Student Name</th>
                    <th>Admission No</th>
                    <th>Course</th>
                    <th>Category</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Date</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td><strong>{{ $invoice->invoice_no }}</strong></td>
                        <td>{{ $invoice->student?->first_name }} {{ $invoice->student?->last_name }}</td>
                        <td>{{ $invoice->student?->admission_no ?? '-' }}</td>
                        <td>{{ $invoice->student?->course?->name ?? 'N/A' }}</td>
                        <td>{{ $invoice->fee_category ?? 'Fees' }}</td>
                        <td>₹{{ number_format($invoice->total_amount, 2) }}</td>
                        <td style="color: #10b981; font-weight: 600;">₹{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td style="font-weight: 600; color: {{ $invoice->due_amount > 0 ? '#ef4444' : '#10b981' }}">₹{{ number_format($invoice->due_amount, 2) }}</td>
                        <td>{{ $invoice->payment_date ? $invoice->payment_date->format('Y-m-d') : '-' }}</td>
                        <td>{{ $invoice->payment_method ?? '-' }}</td>
                        <td>
                            @php
                                $badgeClass = match($invoice->status) {
                                    'Paid' => 'badge-paid',
                                    'Partial' => 'badge-partial',
                                    default => 'badge-unpaid',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $invoice->status }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 30px; color: #6e645e;">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div>* Computer generated collection report.</div>
        <div>Authorized Signatory: ________________________</div>
    </div>

</body>
</html>
