<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Slip - {{ $training->slip_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            justify-content: flex-start;
            padding: 30px 20px;
            transition: background 0.4s ease, color 0.4s ease;
            position: relative;
            overflow-x: hidden;
        }

        .decor-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.25;
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, var(--primary) 0%, rgba(255,85,50,0) 70%);
            top: -80px;
            left: -80px;
        }
        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, var(--success) 0%, rgba(16,185,129,0) 70%);
            bottom: -100px;
            right: -80px;
        }

        .page-wrapper {
            width: 100%;
            max-width: 210mm;
            min-height: 297mm;
            z-index: 10;
            padding: 0;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 14px 24px;
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
            font-family: 'Plus Jakarta Sans', sans-serif;
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

        .slip-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 0;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            margin-bottom: 0;
        }

        .slip-header {
            background: linear-gradient(135deg, #1c1816 0%, #2d2522 100%);
            color: #ffffff;
            padding: 24px 32px;
            position: relative;
            z-index: 2;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            flex-shrink: 0;
        }

        .company-info h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin: 0;
            line-height: 1.2;
        }

        .company-info p {
            font-size: 0.78rem;
            margin: 2px 0 0;
            opacity: 0.85;
            line-height: 1.4;
        }

        .slip-badge-area {
            text-align: right;
        }

        .slip-type-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .slip-number-display {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #ffffff;
            margin: 0;
        }

        .slip-number-label {
            font-size: 0.7rem;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .slip-divider {
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--primary-dark), transparent);
            margin: 0;
        }

        .slip-body {
            padding: 28px 32px;
        }

        .section-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border);
        }

        .section-title i {
            font-size: 0.75rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-card {
            background: rgba(255, 85, 50, 0.02);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
        }

        .info-details {
            display: grid;
            grid-template-columns: auto 1fr;
            row-gap: 10px;
            column-gap: 14px;
            font-size: 0.85rem;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 500;
            white-space: nowrap;
        }

        .info-value {
            color: var(--text-main);
            font-weight: 700;
        }

        .payment-summary {
            background: linear-gradient(135deg, rgba(255, 85, 50, 0.03), rgba(255, 85, 50, 0.06));
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 20px;
        }

        .payment-summary-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed var(--border);
            font-size: 0.9rem;
        }

        .payment-row:last-child {
            border-bottom: none;
            padding-top: 12px;
            margin-top: 4px;
            border-top: 2px solid var(--text-main);
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--primary);
        }

        .payment-row span:last-child {
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
        }

        .slip-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 20px 32px 24px;
            border-top: 1px solid var(--border);
            background: rgba(255, 85, 50, 0.01);
        }

        .footer-notes {
            font-size: 0.72rem;
            color: var(--text-muted);
            line-height: 1.6;
            max-width: 65%;
        }

        .footer-notes em {
            display: block;
            margin-bottom: 2px;
        }

        .sign-block {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sign-line {
            width: 160px;
            height: 1px;
            background: var(--text-main);
            margin-bottom: 8px;
        }

        .sign-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .watermark-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 7rem;
            font-weight: 900;
            letter-spacing: 0.5rem;
            text-transform: uppercase;
            pointer-events: none;
            user-select: none;
            z-index: 1;
            opacity: 0.03;
            transition: all 0.4s ease;
        }
        .watermark-active {
            color: var(--success);
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--success-glow);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-indicator i {
            font-size: 0.7rem;
        }

        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .decor-orb, .action-bar { display: none !important; }
            .slip-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                background: transparent !important;
                page-break-inside: avoid;
                border-radius: 0 !important;
            }
            .watermark-badge { opacity: 0.04 !important; }
            .slip-header {
                background: #1c1816 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .slip-divider {
                background: #ff5532 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .payment-summary {
                border-color: #ff5532 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-wrapper {
                max-width: 100%;
                min-height: auto;
            }
            @page {
                size: A4 portrait;
                margin: 15mm 15mm 15mm 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="decor-orb orb-1"></div>
    <div class="decor-orb orb-2"></div>

    <div class="page-wrapper">
        <div class="action-bar">
            <a href="{{ route('trainings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Slip
            </button>
        </div>

        <div class="slip-card">
            <div class="watermark-badge watermark-active">VERIFIED</div>

            <div class="slip-header">
                <div class="header-top">
                    <div class="logo-area">
                        <div class="logo-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="company-info">
                            <h1>Netcoder Technology Solutions</h1>
                            <p>Address: 1st Floor, above Gramin Bank, near ITI Bridge, Dari, Dharamshala, Gabli Dar, Himachal Pradesh 176057</p>
                            <p>Phone: 098167 32055 | support@netcoder.tech</p>
                        </div>
                    </div>
                    <div class="slip-badge-area">
                        <div class="slip-type-badge">Official Training Slip</div>
                        <div class="slip-number-label">Slip Reference Number</div>
                        <div class="slip-number-display">#{{ $training->slip_no }}</div>
                    </div>
                </div>
            </div>

            <div class="slip-divider"></div>

            <div class="slip-body">
                <div class="section-title">
                    <i class="fas fa-user-check"></i>
                    Candidate Information
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-details">
                            <span class="info-label">Full Name:</span>
                            <span class="info-value">{{ $training->name }}</span>
                            <span class="info-label">Father Name:</span>
                            <span class="info-value">{{ $training->father_name ?? 'N/A' }}</span>
                            <span class="info-label">Email ID:</span>
                            <span class="info-value">{{ $training->email }}</span>
                            <span class="info-label">Mobile No:</span>
                            <span class="info-value">{{ $training->mobile }}</span>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-details">
                            <span class="info-label">College / Institution:</span>
                            <span class="info-value">{{ $training->college ?? 'N/A' }}</span>
                            <span class="info-label">Course Enrolled:</span>
                            <span class="info-value">{{ $training->course->name ?? 'N/A' }}</span>
                            <span class="info-label">Training Duration:</span>
                            <span class="info-value">{{ $training->duration }}</span>
                            <span class="info-label">Issued On:</span>
                            <span class="info-value">{{ $training->payment_date ? \Carbon\Carbon::parse($training->payment_date)->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="section-title">
                    <i class="fas fa-receipt"></i>
                    Payment Details
                </div>

                <div class="payment-summary">
                    <div class="payment-summary-title">
                        <i class="fas fa-wallet"></i>
                        Fee Summary
                    </div>
                    <div class="payment-row">
                        <span>Training Fees</span>
                        <span>INR {{ number_format($training->fees, 2) }}</span>
                    </div>
                    <div class="payment-row">
                        <span>Payment Method</span>
                        <span>{{ $training->payment_method }}</span>
                    </div>
                    <div class="payment-row">
                        <span>Total Amount Paid</span>
                        <span>INR {{ number_format($training->fees, 2) }}</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div class="status-indicator">
                        <i class="fas fa-check-circle"></i>
                        Active Registration
                    </div>
                    <div style="font-size: 0.78rem; color: var(--text-muted);">
                        Generated By: <strong>{{ $training->creator->name ?? 'System' }}</strong>
                    </div>
                </div>
            </div>

            <div class="slip-footer">
                <div class="footer-notes">
                    <em>* This is a computer-generated training slip and does not require a physical signature.</em>
                    <em>* Please preserve this slip for your records and produce it when requested.</em>
                    <em>* All disputes are subject to Dharamshala jurisdiction.</em>
                </div>
                <div class="sign-block">
                    <div class="sign-line"></div>
                    <div class="sign-text">Authorized Signatory</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const html = document.documentElement;
            const currentTheme = localStorage.getItem('theme') || 'light';
            html.setAttribute('data-theme', currentTheme);
        });
    </script>
</body>
</html>
