<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | Prospect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            max-width: 480px; width: 100%;
            text-align: center;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1.2rem;
            animation: pop 0.6s ease;
        }
        @keyframes pop {
            0%   { transform: scale(0); opacity: 0; }
            70%  { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        h1 { color: #34d399; font-size: 1.8rem; font-weight: 800; margin-bottom: 0.6rem; }
        p  { color: #94a3b8; font-size: 0.95rem; line-height: 1.6; margin-bottom: 0.5rem; }
        .detail {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        .detail-row {
            display: flex; justify-content: space-between;
            color: #cbd5e1; font-size: 0.92rem;
            padding: 0.4rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-row strong { color: #fff; }
        .btn-home {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #818cf8, #6366f1);
            color: #fff;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.95rem;
            transition: transform 0.2s;
        }
        .btn-home:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">&#x2705;</div>
    <h1>Payment Successful!</h1>
    <p>Thank you, <strong style="color:#fff">{{ $prospect->name }}</strong>.</p>
    <p>Your payment has been recorded.</p>

    <div class="detail">
        <div class="detail-row">
            <span>Registration Fee</span>
            <strong>Rs. {{ number_format($prospect->registration_fee, 2) }}</strong>
        </div>
        <div class="detail-row">
            <span>Monthly Fee</span>
            <strong>Rs. {{ number_format($prospect->monthly_fee, 2) }}</strong>
        </div>
        @if($prospect->fine_total > 0)
        <div class="detail-row">
            <span>Fines</span>
            <strong style="color:#f87171">Rs. {{ number_format($prospect->fine_total, 2) }}</strong>
        </div>
        @endif
        <div class="detail-row">
            <span>Total Paid</span>
            <strong style="color:#34d399">Rs. {{ number_format($prospect->paid_amount, 2) }}</strong>
        </div>
        <div class="detail-row">
            <span>Remaining Balance</span>
            <strong style="color:{{ $prospect->remaining_balance > 0 ? '#f87171' : '#34d399' }}">
                Rs. {{ number_format($prospect->remaining_balance, 2) }}
            </strong>
        </div>
        @if($prospect->payment_date)
        <div class="detail-row">
            <span>Payment Date</span>
            <strong>{{ $prospect->payment_date->format('d M Y, h:i A') }}</strong>
        </div>
        @endif
    </div>

    @if($prospect->remaining_balance > 0)
        <p style="color:#fbbf24">&#x26A0; Balance still pending. Please make additional payments.</p>
        <a href="{{ route('prospects.invoice', $prospect->id) }}" class="btn-home">Pay Remaining Balance</a>
    @else
        <p style="color:#34d399">&#x1F389; All dues cleared! The prospect has been marked as fully paid.</p>
        <a href="{{ route('prospects.create') }}" class="btn-home">Add New Prospect</a>
    @endif
</div>
</body>
</html>
