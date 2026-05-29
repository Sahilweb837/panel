<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Payslip - {{ $salarySlip->month }} {{ $salarySlip->year }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
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
            justify-content: center;
            padding: 40px 20px;
            transition: background 0.4s ease, color 0.4s ease;
            position: relative;
            overflow-x: hidden;
        }

        /* Abstract glowing backdrop decorations */
        .decor-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--primary) 0%, rgba(255,85,50,0) 70%);
            top: -100px;
            left: -100px;
        }
        .orb-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--success) 0%, rgba(16,185,129,0) 70%);
            bottom: -150px;
            right: -100px;
        }

        /* Container & Navigation */
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
            -webkit-backdrop-filter: blur(16px);
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
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
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
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

        /* Invoice Container */
        .invoice-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 50px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        /* Diagonally Rotated Watermark Badge */
        .watermark-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 8rem;
            font-weight: 900;
            letter-spacing: 0.5rem;
            text-transform: uppercase;
            pointer-events: none;
            user-select: none;
            z-index: 1;
            opacity: 0.05;
            transition: all 0.4s ease;
        }
        .watermark-paid { color: var(--success); }
        .watermark-pending { color: var(--warning); }

        /* Invoice Header Grid */
        .invoice-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            border-bottom: 2px dashed var(--border);
            padding-bottom: 35px;
            margin-bottom: 35px;
            position: relative;
            z-index: 2;
        }

        .logo-block {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .logo-img {
            max-height: 52px;
            max-width: 260px;
            object-fit: contain;
            align-self: flex-start;
        }
        .company-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
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
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -1px;
            margin-bottom: 4px;
        }
        .invoice-no-label {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
        }
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-paid {
            background: var(--success-glow);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .status-pending {
            background: var(--warning-glow);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Billing / Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .info-card {
            background: rgba(255, 85, 50, 0.02);
            border: 1px dashed var(--border);
            border-radius: 16px;
            padding: 24px;
        }
        .info-card-title {
            font-size: 0.78rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
        }
        .info-details {
            display: grid;
            grid-template-columns: auto 1fr;
            row-gap: 12px;
            column-gap: 16px;
            font-size: 0.9rem;
        }
        .info-label {
            color: var(--text-muted);
            font-weight: 500;
        }
        .info-value {
            color: var(--text-main);
            font-weight: 700;
        }

        /* Earnings & Deductions columns layout */
        .salary-breakdown-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .salary-column {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .salary-column-title {
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #ffffff;
            background: var(--dark-surface);
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
        }
        .salary-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 0.92rem;
        }
        .salary-item-row strong {
            font-weight: 700;
        }
        .salary-item-row.total-column-row {
            border-bottom: 2px solid var(--text-main);
            font-weight: 800;
            color: var(--text-main);
            background: rgba(28, 24, 22, 0.02);
            border-radius: 6px;
        }

        /* Summary Panel */
        .summary-block {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 45px;
            position: relative;
            z-index: 2;
        }
        .summary-card {
            width: 100%;
            max-width: 380px;
            background: rgba(28, 24, 22, 0.02);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: grid;
            gap: 12px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .summary-row strong {
            color: var(--text-main);
        }
        .summary-row.total-row {
            border-top: 1px solid var(--border);
            padding-top: 14px;
            margin-top: 4px;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary);
        }
        .summary-row.total-row span:last-child {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
        }

        /* Notes & Terms Section */
        .invoice-footer {
            border-top: 1px solid var(--border);
            padding-top: 30px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            font-size: 0.82rem;
            color: var(--text-muted);
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }
        .notes-block h4, .sign-block h4 {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .sign-block {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
            min-height: 110px;
        }
        .sign-line {
            width: 180px;
            height: 1px;
            background: var(--text-main);
            margin-bottom: 6px;
        }

        /* Print styling logic */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
            }
            .decor-orb, .action-bar {
                display: none !important;
            }
            .invoice-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                background: transparent !important;
            }
            .watermark-badge {
                opacity: 0.07 !important;
            }
            .salary-column-title {
                background: #111111 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .info-card {
                background: #fbfbfb !important;
                border: 1px dashed #cccccc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4;
                margin: 1.6cm;
            }
        }
    </style>
</head>
<body>

    <!-- Glowing backdrop orbs -->
    <div class="decor-orb orb-1"></div>
    <div class="decor-orb orb-2"></div>

    <div class="page-wrapper">
        <!-- Interactive screen controls -->
        <div class="action-bar">
            <a href="{{ route('salary_slips.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Salary Slips
            </a>
            
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Payslip
            </button>
        </div>

        <!-- Printable Slip Card -->
        <div class="invoice-card">
            <!-- Semi-transparent status watermark -->
            @if($salarySlip->status === 'Paid')
                <div class="watermark-badge watermark-paid">Paid</div>
            @else
                <div class="watermark-badge watermark-pending">Pending</div>
            @endif

            <!-- Invoice Header -->
            <header class="invoice-header">
                <div class="logo-block">
                    <!-- Netcoder Technology brand logo -->
                    <img src="{{ asset('image.png') }}" alt="Netcoder Technology" class="logo-img">
                    <div class="company-meta">
                        <strong>Netcoder Technology Solutions</strong><br>
                        Industrial Area Phase-7, ERP Division<br>
                        support@netcoder.tech | +91 98765-43210
                    </div>
                </div>
                <div class="invoice-meta-right">
                    <div>
                        <div class="invoice-title">PAYSLIP</div>
                        <div class="invoice-no-label">{{ $salarySlip->month }} {{ $salarySlip->year }}</div>
                    </div>
                    <div>
                        @if($salarySlip->status === 'Paid')
                            <span class="status-pill status-paid">
                                <i class="fas fa-check-circle"></i> Paid
                            </span>
                        @else
                            <span class="status-pill status-pending">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Metadata Info Grid -->
            <section class="info-grid">
                <!-- Employee details -->
                <div class="info-card">
                    <div class="info-card-title">
                        <i class="fas fa-user-tie"></i> Employee Information
                    </div>
                    <div class="info-details">
                        <span class="info-label">Employee Code:</span>
                        <span class="info-value">{{ $salarySlip->employee->employee_code }}</span>

                        <span class="info-label">Full Name:</span>
                        <span class="info-value">{{ $salarySlip->employee->user?->name ?? 'Staff Member' }}</span>

                        <span class="info-label">Department:</span>
                        <span class="info-value">{{ $salarySlip->employee->department ?? '-' }}</span>

                        <span class="info-label">Designation:</span>
                        <span class="info-value">{{ $salarySlip->employee->designation ?? '-' }}</span>
                    </div>
                </div>

                <!-- Slip Metadata -->
                <div class="info-card">
                    <div class="info-card-title">
                        <i class="fas fa-calendar-check"></i> Statement Details
                    </div>
                    <div class="info-details">
                        <span class="info-label">Pay Period:</span>
                        <span class="info-value">{{ $salarySlip->month }} {{ $salarySlip->year }}</span>

                        <span class="info-label">Payment Date:</span>
                        <span class="info-value">{{ $salarySlip->payment_date ? \Carbon\Carbon::parse($salarySlip->payment_date)->format('M d, Y') : 'Pending Release' }}</span>

                        <span class="info-label">Generated By:</span>
                        <span class="info-value">{{ $salarySlip->creator?->name ?? 'System Admin' }}</span>

                        <span class="info-label">Issue Date:</span>
                        <span class="info-value">{{ $salarySlip->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </section>

            <!-- Earnings vs Deductions Breakdown -->
            <section class="salary-breakdown-grid">
                <!-- Left column: Earnings -->
                <div class="salary-column">
                    <div class="salary-column-title">
                        <span>Earnings</span>
                        <span>Amount (INR)</span>
                    </div>
                    <div class="salary-item-row">
                        <span>Basic Salary</span>
                        <strong>{{ number_format($salarySlip->basic_salary, 2) }}</strong>
                    </div>
                    <div class="salary-item-row">
                        <span>Special Allowance</span>
                        <strong>{{ number_format($salarySlip->allowances, 2) }}</strong>
                    </div>
                    <div class="salary-item-row total-column-row">
                        <span>Gross Earnings</span>
                        <span>{{ number_format($salarySlip->basic_salary + $salarySlip->allowances, 2) }}</span>
                    </div>
                </div>

                <!-- Right column: Deductions -->
                <div class="salary-column">
                    <div class="salary-column-title">
                        <span>Deductions</span>
                        <span>Amount (INR)</span>
                    </div>
                    <div class="salary-item-row">
                        <span>Statutory Deductions (TDS/PF)</span>
                        <strong>{{ number_format($salarySlip->deductions, 2) }}</strong>
                    </div>
                    <div class="salary-item-row">
                        <span>Other Deductions</span>
                        <strong>0.00</strong>
                    </div>
                    <div class="salary-item-row total-column-row">
                        <span>Total Deductions</span>
                        <span>{{ number_format($salarySlip->deductions, 2) }}</span>
                    </div>
                </div>
            </section>

            <!-- Net Pay Summary Panel -->
            <div class="summary-block">
                <div class="summary-card">
                    <div class="summary-row">
                        <span>Gross Earnings:</span>
                        <strong>{{ number_format($salarySlip->basic_salary + $salarySlip->allowances, 2) }}</strong>
                    </div>
                    <div class="summary-row">
                        <span>Total Deductions (-):</span>
                        <strong style="color: var(--danger);">{{ number_format($salarySlip->deductions, 2) }}</strong>
                    </div>
                    <div class="summary-row total-row">
                        <span>Net Take-home Pay:</span>
                        <span>{{ number_format($salarySlip->net_pay, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes & Signatures footer -->
            <footer class="invoice-footer">
                <div class="notes-block">
                    <h4>Confidentiality & Inquiries</h4>
                    <p style="margin-bottom: 6px;">1. This payslip is a private record between Netcoder Technology and the employee. Please do not share or distribute this information.</p>
                    <p style="margin-bottom: 6px;">2. For discrepancies in attendance calculations, basic components, or allowances, please raise a ticket to HR ERP Portal.</p>
                    <p>3. Tax deductions are calculated based on current statutory declarations under prevailing financial acts.</p>
                </div>
                <div class="sign-block">
                    <h4>Signing Authority</h4>
                    <div>
                        <div class="sign-line"></div>
                        <p style="font-size: 0.8rem; font-weight: 700; color: var(--text-main);">Human Resources Division</p>
                        <p style="font-size: 0.72rem;">System Verification Sealed</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Script to support theme updates -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const html = document.documentElement;
            const currentTheme = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-theme', currentTheme);
        });
    </script>
</body>
</html>
