<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fee Report - {{ $student->first_name }} {{ $student->last_name }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <style>
        :root {
            --primary: #ff5532;
            --primary-glow: rgba(255, 85, 50, 0.15);
            --primary-dark: #e04422;
            --success: #10b981;
            --success-glow: rgba(16, 185, 129, 0.12);
            --warning: #f59e0b;
            --warning-glow: rgba(245, 158, 11, 0.12);
            --danger: #ef4444;
            --danger-glow: rgba(239, 68, 68, 0.12);
            --dark-surface: #120e0c;
            
            --bg-grad: linear-gradient(135deg, #fbf9f8 0%, #f3edea 100%);
            --card-bg: #ffffff;
            --text-main: #1c1816;
            --text-muted: #6e645e;
            --border: #e9e4e0;
            --shadow: 0 20px 40px rgba(28, 24, 22, 0.06);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        [data-theme="dark"] {
            --bg-grad: linear-gradient(135deg, #15100e 0%, #0d0908 100%);
            --card-bg: #1e1714;
            --text-main: #f5eae4;
            --text-muted: #aca099;
            --border: #332722;
            --shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            --glass-bg: rgba(30, 23, 20, 0.6);
            --glass-border: rgba(255, 255, 255, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-grad);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            transition: background 0.4s ease, color 0.4s ease;
            position: relative;
        }

        .page-wrapper {
            width: 100%;
            max-width: 850px;
            z-index: 10;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(16px);
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s;
            border: none;
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-main);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background: rgba(255, 85, 50, 0.05);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 6px 20px rgba(255, 85, 50, 0.25);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 85, 50, 0.35);
        }

        .invoice-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 30px 40px;
            position: relative;
            overflow: hidden;
            margin-bottom: 0;
        }

        .report-badge {
            display: inline-block;
            background: var(--text-main);
            color: var(--card-bg);
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .invoice-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            border-bottom: 2px dashed var(--border);
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .logo-block {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .logo-img {
            max-height: 45px;
            max-width: 220px;
            object-fit: contain;
            align-self: flex-start;
        }
        .company-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .invoice-meta-right {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
        }
        .invoice-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -1px;
            margin-bottom: 4px;
        }
        .invoice-no-label {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-card {
            background: rgba(255, 85, 50, 0.02);
            border: 1px dashed var(--border);
            border-radius: 12px;
            padding: 15px;
        }
        .info-card-title {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 6px;
        }
        .info-details {
            display: grid;
            grid-template-columns: auto 1fr;
            row-gap: 8px;
            column-gap: 12px;
            font-size: 0.8rem;
        }
        .info-label { color: var(--text-muted); font-weight: 500; }
        .info-value { color: var(--text-main); font-weight: 700; }

        .table-wrapper {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-table th {
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            color: #ffffff;
            background: var(--dark-surface);
            padding: 8px 12px;
            border: none;
            text-align: left;
        }
        .invoice-table th:first-child { border-top-left-radius: 6px; border-bottom-left-radius: 6px; }
        .invoice-table th:last-child { border-top-right-radius: 6px; border-bottom-right-radius: 6px; text-align: right; }
        .invoice-table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            font-size: 0.85rem;
            vertical-align: middle;
        }
        .invoice-table td:last-child { text-align: right; font-weight: 700; }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .status-paid { background: var(--success-glow); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-partial { background: var(--warning-glow); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.2); }
        .status-unpaid { background: var(--danger-glow); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }

        .summary-block {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .summary-card {
            width: 100%;
            max-width: 320px;
            background: rgba(28, 24, 22, 0.02);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 15px;
            display: grid;
            gap: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .summary-row strong { color: var(--text-main); }
        .summary-row.total-row {
            border-top: 1px solid var(--border);
            padding-top: 10px;
            margin-top: 2px;
            font-size: 1rem;
            font-weight: 800;
            color: var(--primary);
        }
        .summary-row.total-row span:last-child { font-family: 'Outfit', sans-serif; font-size: 1.15rem; }
        .due-warning { color: var(--danger); font-weight: 800; }

        .invoice-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
        }
        .sign-block {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .sign-line {
            width: 140px;
            height: 1px;
            background: var(--text-main);
            margin-bottom: 6px;
        }
        .sign-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
        }

        @media print {
            body { background: #ffffff !important; color: #000000 !important; padding: 0 !important; }
            .action-bar { display: none !important; }
            .invoice-card { border: none !important; box-shadow: none !important; padding: 0 !important; background: transparent !important; }
            .invoice-table th { background: #e0e0e0 !important; color: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .info-card, .summary-card { background: transparent !important; border: 1px solid #ccc !important; }
            @page { size: A4 portrait; margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="action-bar">
            <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>

        <div class="invoice-card">
            <div class="report-badge">STATEMENT OF ACCOUNT</div>

            <header class="invoice-header">
                <div class="logo-block">
                    <img src="https://www.netcoder.in/images/logo.png" alt="Netcoder Technology" class="logo-img">
                    <div class="company-meta">
                        <strong>Netcoder Technology Solutions</strong><br>
                        Dari, Dharamshala, HP 176215<br>
                        support@netcoder.tech | 098167 32055
                    </div>
                </div>
                <div class="invoice-meta-right">
                    <div>
                        <div class="invoice-title">STUDENT FEE REPORT</div>
                        <div class="invoice-no-label">Date: {{ now()->format('M d, Y') }}</div>
                    </div>
                </div>
            </header>

            <section class="info-grid">
                <div class="info-card">
                    <div class="info-card-title"><i class="fas fa-user-graduate"></i> Student Details</div>
                    <div class="info-details">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $student->first_name }} {{ $student->last_name }}</span>
                        <span class="info-label">Admission No:</span>
                        <span class="info-value">{{ $student->admission_no }}</span>
                        <span class="info-label">Course:</span>
                        <span class="info-value">{{ $student->course?->name ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-title"><i class="fas fa-chart-pie"></i> Account Summary</div>
                    <div class="info-details">
                        <span class="info-label">Course Fees Due:</span>
                        <span class="info-value {{ $dueFees > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($dueFees, 2) }}</span>
                        
                        <span class="info-label">Seminar Dues:</span>
                        <span class="info-value {{ $dueSeminarFees > 0 ? 'text-warning' : 'text-success' }}">₹{{ number_format($dueSeminarFees, 2) }}</span>
                        
                        <span class="info-label">Fines Due:</span>
                        <span class="info-value {{ $dueFines > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($dueFines, 2) }}</span>
                    </div>
                </div>
            </section>

            <div class="table-wrapper">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Status</th>
                            <th>Amount (INR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feeInvoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->invoice_no }}</strong></td>
                                <td>{{ $invoice->created_at->format('d M Y') }}</td>
                                <td>{{ $invoice->fee_category ?: 'Fees' }}</td>
                                <td>
                                    @if($invoice->status === 'Paid')
                                        <span class="status-pill status-paid">Paid</span>
                                    @elseif($invoice->status === 'Partial')
                                        <span class="status-pill status-partial">Partial</span>
                                    @else
                                        <span class="status-pill status-unpaid">Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 20px;">No fee transactions found for this student.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="summary-block">
                <div class="summary-card" style="max-width: 380px;">
                    <!-- Course Fees -->
                    <div class="summary-row">
                        <span>Course Fees:</span>
                        <strong>{{ number_format($totalFees, 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Course Paid (-):</span>
                        <strong style="color: var(--success);">{{ number_format($paidFees, 2) }}</strong>
                    </div>
                    <div class="summary-row total-row mb-3 pb-3 border-bottom">
                        <span class="{{ $dueFees > 0 ? 'due-warning' : '' }}">Course Balance:</span>
                        <span class="{{ $dueFees > 0 ? 'due-warning' : '' }}">₹{{ number_format($dueFees, 2) }}</span>
                    </div>

                    <!-- Seminar Fees -->
                    <div class="summary-row">
                        <span>Seminar Fees:</span>
                        <strong>{{ number_format($totalSeminarFees, 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Seminar Paid (-):</span>
                        <strong style="color: var(--success);">{{ number_format($paidSeminarFees, 2) }}</strong>
                    </div>
                    <div class="summary-row total-row mb-3 pb-3 border-bottom">
                        <span class="{{ $dueSeminarFees > 0 ? 'text-warning' : '' }}">Seminar Balance:</span>
                        <span class="{{ $dueSeminarFees > 0 ? 'text-warning' : '' }}">₹{{ number_format($dueSeminarFees, 2) }}</span>
                    </div>

                    <!-- Fines -->
                    <div class="summary-row">
                        <span>Fines & Penalties:</span>
                        <strong>{{ number_format($totalFines, 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Fines Paid (-):</span>
                        <strong style="color: var(--success);">{{ number_format($paidFines, 2) }}</strong>
                    </div>
                    <div class="summary-row total-row">
                        <span class="{{ $dueFines > 0 ? 'due-warning' : '' }}">Fines Balance:</span>
                        <span class="{{ $dueFines > 0 ? 'due-warning' : '' }}">₹{{ number_format($dueFines, 2) }}</span>
                    </div>
                </div>
            </div>

            <footer class="invoice-footer">
                <div style="font-size: 0.75rem; color: var(--text-muted); max-width: 60%;">
                    <em>* This is a computer-generated report.</em><br>
                    <em>* For any discrepancies, please contact the administration office.</em>
                </div>
                <div class="sign-block">
                    <div class="sign-line"></div>
                    <div class="sign-text">Authorized Signatory</div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
