<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Invoice - {{ $clientInvoice->invoice_no }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for Premium Icons -->
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
            --card-header-bg: #fdfcfb;
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
            --card-header-bg: #271f1b;
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
            padding: 40px 20px;
            transition: background 0.4s ease, color 0.4s ease;
            position: relative;
            overflow-x: hidden;
        }

        /* Glowing background decorations */
        .decor-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.25;
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

        .page-wrapper {
            width: 100%;
            max-width: 900px;
            z-index: 10;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
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

        .theme-toggle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .theme-toggle:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: rotate(15deg);
        }

        /* Invoice Card Layout */
        .invoice-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 48px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Elegant watermark indicator */
        .status-watermark {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-paid {
            background: var(--success-glow);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .status-partial {
            background: var(--warning-glow);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .status-unpaid {
            background: var(--danger-glow);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Header Grid */
        .invoice-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 32px;
            border-bottom: 2px dashed var(--border);
            padding-bottom: 36px;
            margin-bottom: 36px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .logo-box {
            width: 60px;
            height: 60px;
            background: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 6px;
        }
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .brand-details h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }
        .brand-details p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .meta-section {
            text-align: right;
        }
        .invoice-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }
        .invoice-no {
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .info-block h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary);
            margin-bottom: 12px;
            border-left: 3px solid var(--primary);
            padding-left: 8px;
        }
        .info-details {
            background: var(--bg-grad);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: calc(100% - 24px);
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        .info-label {
            color: var(--text-muted);
            font-weight: 500;
        }
        .info-value {
            font-weight: 600;
            text-align: right;
        }

        /* Table */
        .items-table-container {
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 36px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.92rem;
        }
        .items-table th {
            background: var(--card-header-bg);
            color: var(--text-main);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 16px 20px;
            text-align: left;
            border-bottom: 2px solid var(--border);
        }
        .items-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .items-table th.num-col, .items-table td.num-col {
            text-align: right;
        }

        /* Summary Panel */
        .summary-section {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
            margin-bottom: 24px;
        }
        .notes-card {
            background: var(--bg-grad);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            height: fit-content;
        }
        .notes-card h5 {
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .notes-card p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .calculation-card {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.92rem;
        }
        .calc-total {
            border-top: 2px solid var(--border);
            padding-top: 12px;
            font-weight: 800;
            font-size: 1.15rem;
            color: var(--primary);
        }
        .calc-due {
            border-top: 1px solid var(--border);
            padding-top: 8px;
            font-weight: 700;
            font-size: 1rem;
            color: var(--danger);
        }

        /* Client Summary Statement */
        .client-statement-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .statement-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .statement-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .statement-box {
            background: var(--bg-grad);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .statement-box .lbl {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .statement-box .val {
            font-size: 1.25rem;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
        }

        /* History Card */
        .history-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 28px;
        }
        .history-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .history-table-container {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }
        .history-table th {
            background: var(--card-header-bg);
            padding: 12px 16px;
            font-weight: 700;
            color: var(--text-main);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .history-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }
        .history-table tr:last-child td {
            border-bottom: none;
        }
        .history-status {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Footer */
        .page-footer {
            text-align: center;
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 24px;
            padding-bottom: 20px;
        }

        /* Print Override styles */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .decor-orb, .action-bar, .client-statement-card, .history-card, .page-footer {
                display: none !important;
            }
            .page-wrapper {
                max-width: 100% !important;
                gap: 0 !important;
            }
            .invoice-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                background: transparent !important;
            }
            .info-details, .notes-card {
                background: #ffffff !important;
                border: 1px solid #000000 !important;
            }
            .items-table-container {
                border: 1px solid #000000 !important;
            }
            .items-table th {
                background: #f5f5f5 !important;
                color: #000000 !important;
                border-bottom: 2px solid #000000 !important;
            }
            .items-table td {
                border-bottom: 1px solid #e0e0e0 !important;
            }
        }
    </style>
</head>
<body class="invoice-shell">
    <!-- Orbs -->
    <div class="decor-orb orb-1"></div>
    <div class="decor-orb orb-2"></div>

    <div class="page-wrapper">
        <!-- Action bar -->
        <div class="action-bar">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('client_invoices.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Invoices
                </a>
            </div>
            <div style="display: flex; align-items: center; gap: 14px;">
                <button type="button" class="theme-toggle" id="theme-toggle-btn" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </button>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="invoice-card">
            <!-- Status Badge -->
            <div class="status-watermark status-{{ strtolower($clientInvoice->status) }}">
                <span class="status-dot" style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                {{ $clientInvoice->status }}
            </div>

            <!-- Invoice Header -->
            <header class="invoice-header">
                <div class="brand-section">
                    <div class="logo-box">
                        <img src="{{ asset('image.png') }}" alt="Netcoder Logo">
                    </div>
                    <div class="brand-details">
                        <h2>Netcoder Fees</h2>
                        <p>Web App Solutions & Corporate ERP</p>
                    </div>
                </div>
                <div class="meta-section">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-no">#{{ $clientInvoice->invoice_no }}</div>
                </div>
            </header>

            <!-- Info Grid -->
            <div class="info-grid">
                <div class="info-block">
                    <h4>Client Details</h4>
                    <div class="info-details">
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $clientInvoice->client?->name ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Company:</span>
                            <span class="info-value">{{ $clientInvoice->client?->company ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value" style="font-size: 0.85rem;">{{ $clientInvoice->client?->email ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $clientInvoice->client?->phone ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="info-block">
                    <h4>Invoice Details</h4>
                    <div class="info-details">
                        <div class="info-row">
                            <span class="info-label">Issue Date:</span>
                            <span class="info-value">{{ $clientInvoice->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Due Date:</span>
                            <span class="info-value">{{ $clientInvoice->due_date ? \Carbon\Carbon::parse($clientInvoice->due_date)->format('M d, Y') : 'Immediate' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Date:</span>
                            <span class="info-value">{{ $clientInvoice->payment_date ? \Carbon\Carbon::parse($clientInvoice->payment_date)->format('M d, Y') : '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Method:</span>
                            <span class="info-value">{{ $clientInvoice->payment_method ?? 'Not Paid' }}</span>
                        </div>
                        @if($clientInvoice->transaction_id)
                        <div class="info-row">
                            <span class="info-label">Txn ID:</span>
                            <span class="info-value" style="font-family: monospace; font-size: 0.82rem;">{{ $clientInvoice->transaction_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="items-table-container">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="num-col" style="width: 100px;">Qty</th>
                            <th class="num-col" style="width: 150px;">Unit Price</th>
                            <th class="num-col" style="width: 150px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($clientInvoice->invoice_items && is_array($clientInvoice->invoice_items))
                            @foreach($clientInvoice->invoice_items as $item)
                                <tr>
                                    <td>{{ $item['description'] ?? 'Service Item' }}</td>
                                    <td class="num-col">{{ number_format($item['qty'] ?? 1, 1) }}</td>
                                    <td class="num-col">₹{{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                                    <td class="num-col">₹{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted);">No items specified in this invoice.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Calculation Section -->
            <div class="summary-section">
                <div class="notes-card">
                    <h5>Notes / Terms</h5>
                    <p>{{ $clientInvoice->notes ?: 'Thank you for your business. Payment is requested within the due date specified on the invoice.' }}</p>
                </div>
                <div class="calculation-card">
                    <div class="calc-row">
                        <span class="info-label">Subtotal:</span>
                        <span class="info-value">₹{{ number_format($clientInvoice->subtotal, 2) }}</span>
                    </div>
                    @if($clientInvoice->tax_percent > 0)
                    <div class="calc-row">
                        <span class="info-label">Tax ({{ $clientInvoice->tax_percent }}%):</span>
                        <span class="info-value">₹{{ number_format($clientInvoice->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($clientInvoice->discount > 0)
                    <div class="calc-row">
                        <span class="info-label">Discount:</span>
                        <span class="info-value" style="color: var(--success);">- ₹{{ number_format($clientInvoice->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="calc-row calc-total">
                        <span>Total:</span>
                        <span>₹{{ number_format($clientInvoice->total_amount, 2) }}</span>
                    </div>
                    <div class="calc-row">
                        <span class="info-label">Amount Paid:</span>
                        <span class="info-value" style="color: var(--success);">₹{{ number_format($clientInvoice->paid_amount, 2) }}</span>
                    </div>
                    <div class="calc-row calc-due">
                        <span>Amount Due:</span>
                        <span>₹{{ number_format($clientInvoice->due_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Statement Summary -->
        <div class="client-statement-card">
            <div class="statement-title">
                <i class="fas fa-file-invoice-dollar text-primary"></i> Client Account Statement Summary
            </div>
            <div class="statement-grid">
                <div class="statement-box">
                    <span class="lbl">Total Invoiced</span>
                    <span class="val" style="color: var(--text-main);">₹{{ number_format($overallTotal, 2) }}</span>
                </div>
                <div class="statement-box">
                    <span class="lbl">Total Paid</span>
                    <span class="val" style="color: var(--success);">₹{{ number_format($overallPaid, 2) }}</span>
                </div>
                <div class="statement-box">
                    <span class="lbl">Outstanding Balance</span>
                    <span class="val" style="color: var(--danger);">₹{{ number_format($overallDue, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Client Billing History -->
        @if($clientHistory->count() > 0)
        <div class="history-card">
            <div class="history-title">
                <i class="fas fa-history text-primary"></i> Invoice History for this Client
            </div>
            <div class="history-table-container">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientHistory as $hist)
                            <tr>
                                <td style="font-family: monospace; font-weight: 700;">{{ $hist->invoice_no }}</td>
                                <td>{{ $hist->created_at->format('M d, Y') }}</td>
                                <td>₹{{ number_format($hist->total_amount, 2) }}</td>
                                <td style="color: var(--success);">₹{{ number_format($hist->paid_amount, 2) }}</td>
                                <td style="color: var(--danger);">₹{{ number_format($hist->due_amount, 2) }}</td>
                                <td>
                                    <span class="history-status status-{{ strtolower($hist->status) }}">
                                        {{ $hist->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('client_invoices.show', $hist->id) }}" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.78rem; border-radius: 6px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <footer class="page-footer">
            <p>Generated by Netcoder Fees Manager ERP. All rights reserved.</p>
        </footer>
    </div>

    <!-- Theme management script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const getSavedTheme = () => {
                const savedTheme = localStorage.getItem('fees-theme');
                if (savedTheme) return savedTheme;
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };

            const applyTheme = (theme) => {
                document.documentElement.setAttribute('data-theme', theme);
                const icon = themeBtn.querySelector('i');
                if (theme === 'dark') {
                    icon.className = 'fas fa-sun';
                } else {
                    icon.className = 'fas fa-moon';
                }
            };

            const themeBtn = document.getElementById('theme-toggle-btn');
            let currentTheme = getSavedTheme();
            applyTheme(currentTheme);

            themeBtn.addEventListener('click', () => {
                currentTheme = currentTheme === 'light' ? 'dark' : 'light';
                localStorage.setItem('fees-theme', currentTheme);
                applyTheme(currentTheme);
            });
        });
    </script>
</body>
</html>
