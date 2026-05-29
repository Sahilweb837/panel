<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Report</title>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff5532;
            --dark: #1e1e2d;
            --muted: #6c757d;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            background: #fff;
            font-size: 11px;
            line-height: 1.5;
        }

        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .brand-section h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .brand-section h1 span {
            color: var(--primary);
        }

        .brand-section p {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .report-meta {
            text-align: right;
        }

        .report-meta h2 {
            margin: 0;
            font-size: 14px;
            color: var(--primary);
            font-weight: 700;
        }

        .report-meta p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 9px;
        }

        .filter-summary {
            background: var(--light-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filter-item {
            font-size: 10px;
        }

        .filter-item strong {
            color: var(--muted);
            font-weight: 600;
            margin-right: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: var(--dark) !important;
            color: #fff !important;
            text-align: left;
            padding: 8px 10px;
            font-weight: 600;
            border: 1px solid var(--dark);
            font-size: 10px;
        }

        td {
            padding: 8px 10px;
            border: 1px solid var(--border-color);
        }

        tr:nth-child(even) td {
            background-color: #fcfcfc;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-primary {
            background-color: #eff6ff;
            color: #1e40af;
        }

        .print-footer {
            margin-top: 30px;
            border-top: 1px solid var(--border-color);
            padding-top: 10px;
            text-align: center;
            color: var(--muted);
            font-size: 8px;
        }

        .no-print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(255, 85, 50, 0.3);
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            z-index: 9999;
        }

        @media print {
            .no-print-btn {
                display: none !important;
            }
            body {
                padding: 0;
            }
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body>

    <button class="no-print-btn" onclick="window.print()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Report
    </button>

    <div class="print-header">
        <div class="brand-section">
            <h1>Netcoder <span>Fees</span></h1>
            <p>Institute Management & Billing System</p>
        </div>
        <div class="report-meta">
            <h2>@yield('report-title')</h2>
            <p>Generated: {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>

    @yield('content')

    <div class="print-footer">
        <p>This is a system-generated document. Netcoder Fees ERP System © {{ date('Y') }}</p>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Give rendering a split second then trigger print dialog automatically
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
