@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
* { box-sizing: border-box; }
body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); min-height: 100vh; }

.inv-wrapper {
    max-width: 700px;
    margin: 2.5rem auto;
    padding: 0 1rem 3rem;
}

/* Header */
.inv-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem;
}
.inv-header h1 { color: #fff; font-size: 1.6rem; font-weight: 800; margin: 0; }
.inv-header a {
    color: #a78bfa; text-decoration: none; font-size: 0.9rem;
    display: flex; align-items: center; gap: 6px;
}
.inv-header a:hover { color: #c4b5fd; }

/* Invoice Card */
.inv-card {
    background: rgba(255,255,255,0.07);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
}

.inv-card-title {
    font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.08em; color: #a78bfa; margin-bottom: 1rem;
}

/* Prospect identity */
.inv-identity { margin-bottom: 1.5rem; padding-bottom: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
.inv-name { font-size: 1.4rem; font-weight: 700; color: #fff; margin: 0 0 0.3rem; }
.inv-email { color: #94a3b8; font-size: 0.9rem; }

/* Fee rows */
.fee-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.65rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    color: #cbd5e1;
    font-size: 0.95rem;
}
.fee-row:last-child { border-bottom: none; }
.fee-row.highlight {
    color: #fff;
    font-weight: 700;
    font-size: 1.05rem;
    padding-top: 0.8rem;
}
.fee-row .label { display: flex; align-items: center; gap: 8px; }
.fee-row .label .dot {
    width: 8px; height: 8px; border-radius: 50%;
    display: inline-block;
}
.dot-reg   { background: #818cf8; }
.dot-mon   { background: #34d399; }
.dot-fine  { background: #f87171; }
.dot-paid  { background: #fbbf24; }
.dot-due   { background: #a78bfa; }
.dot-bal   { background: #60a5fa; }

.amount { font-weight: 600; }
.amount-positive { color: #34d399; }
.amount-red      { color: #f87171; }
.amount-blue     { color: #60a5fa; }
.amount-purple   { color: #a78bfa; }

/* Status badges */
.badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
    margin-left: 8px;
}
.badge-paid    { background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid #34d399; }
.badge-pending { background: rgba(248,113,113,0.15); color: #f87171; border: 1px solid #f87171; }

/* Payment date */
.inv-date {
    margin-top: 0.8rem;
    color: #94a3b8; font-size: 0.85rem;
    display: flex; align-items: center; gap: 6px;
}

/* Forms Section */
.form-section {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.2rem;
}
.form-section-title {
    font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.07em; color: #a78bfa; margin-bottom: 1rem;
}

.form-row { display: flex; gap: 0.8rem; align-items: flex-end; flex-wrap: wrap; }
.form-group { flex: 1 1 180px; }
.form-group label {
    display: block; font-size: 0.82rem; font-weight: 600; color: #94a3b8;
    margin-bottom: 0.4rem;
}
.form-control {
    width: 100%; padding: 0.6rem 0.9rem;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 10px;
    color: #fff;
    font-size: 0.95rem;
    transition: border-color 0.2s;
}
.form-control::placeholder { color: #64748b; }
.form-control:focus { outline: none; border-color: #a78bfa; }

.btn {
    padding: 0.65rem 1.5rem;
    border: none; border-radius: 10px;
    font-weight: 700; font-size: 0.9rem;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    white-space: nowrap;
}
.btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); }
.btn-pay  { background: linear-gradient(135deg, #818cf8, #6366f1); color: #fff; }
.btn-fine { background: linear-gradient(135deg, #f87171, #dc2626); color: #fff; }

/* Alert */
.alert-success {
    background: rgba(52,211,153,0.12);
    border: 1px solid rgba(52,211,153,0.3);
    color: #34d399;
    border-radius: 10px;
    padding: 0.8rem 1rem;
    font-size: 0.9rem;
    margin-bottom: 1.2rem;
    display: flex; align-items: center; gap: 8px;
}
</style>

<div class="inv-wrapper">
    <!-- Header -->
    <div class="inv-header">
        <h1>&#x1F4CB; Prospect Invoice</h1>
        <a href="{{ route('prospects.create') }}">&#x2B; New Prospect</a>
    </div>

    @if(session('status'))
        <div class="alert-success">
            <span>&#x2714;</span> {{ session('status') }}
        </div>
    @endif

    <!-- Main Invoice Card -->
    <div class="inv-card">
        <div class="inv-card-title">Invoice Details</div>

        <!-- Identity -->
        <div class="inv-identity">
            <p class="inv-name">{{ $prospect->name }}</p>
            <span class="inv-email">{{ $prospect->email }}</span>
            @if($prospect->is_paid)
                <span class="badge badge-paid">&#x2714; Fully Paid</span>
            @else
                <span class="badge badge-pending">&#x23F3; Pending</span>
            @endif
        </div>

        <!-- Fee Breakdown -->
        <div class="fee-row">
            <span class="label"><span class="dot dot-reg"></span>Registration Fee</span>
            <span class="amount amount-purple">Rs. {{ number_format($prospect->registration_fee, 2) }}</span>
        </div>
        <div class="fee-row">
            <span class="label"><span class="dot dot-mon"></span>Monthly Fee</span>
            <span class="amount amount-positive">Rs. {{ number_format($prospect->monthly_fee, 2) }}</span>
        </div>
        @if($prospect->fine_total > 0)
        <div class="fee-row">
            <span class="label"><span class="dot dot-fine"></span>Fine / Penalty</span>
            <span class="amount amount-red">+ Rs. {{ number_format($prospect->fine_total, 2) }}</span>
        </div>
        @endif

        <div class="fee-row highlight">
            <span class="label"><span class="dot dot-due"></span>Total Due</span>
            <span class="amount amount-purple">Rs. {{ number_format($prospect->total_due, 2) }}</span>
        </div>
        <div class="fee-row">
            <span class="label"><span class="dot dot-paid"></span>Amount Paid</span>
            <span class="amount amount-positive">Rs. {{ number_format($prospect->paid_amount, 2) }}</span>
        </div>
        <div class="fee-row highlight">
            <span class="label"><span class="dot dot-bal"></span>Remaining Balance</span>
            <span class="amount {{ $prospect->remaining_balance > 0 ? 'amount-red' : 'amount-positive' }}">
                Rs. {{ number_format($prospect->remaining_balance, 2) }}
            </span>
        </div>

        @if($prospect->payment_date)
        <div class="inv-date">
            &#x1F5D3; Last Payment Date: <strong>{{ $prospect->payment_date->format('d M Y, h:i A') }}</strong>
        </div>
        @endif
    </div>

    @if(!$prospect->is_paid)
    <!-- Pay Now -->
    <div class="form-section">
        <div class="form-section-title">&#x1F4B3; Make a Payment</div>
        <form method="POST" action="{{ route('prospects.pay', $prospect->id) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="payment_amount">Amount to Pay (Rs.)</label>
                    <input type="number" step="0.01" min="0.01"
                           max="{{ $prospect->remaining_balance }}"
                           name="payment_amount" id="payment_amount"
                           class="form-control"
                           value="{{ number_format($prospect->remaining_balance, 2, '.', '') }}"
                           placeholder="Enter amount" required>
                </div>
                <button type="submit" class="btn btn-pay">Pay Now</button>
            </div>
        </form>
    </div>

    <!-- Add Fine -->
    <div class="form-section">
        <div class="form-section-title">&#x26A0; Add Fine / Penalty</div>
        <form method="POST" action="{{ route('prospects.fine', $prospect->id) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="fine_amount">Fine Amount (Rs.)</label>
                    <input type="number" step="0.01" min="0.01"
                           name="fine_amount" id="fine_amount"
                           class="form-control"
                           placeholder="e.g. 500" required>
                </div>
                <div class="form-group">
                    <label for="fine_reason">Reason (optional)</label>
                    <input type="text" name="fine_reason" id="fine_reason"
                           class="form-control"
                           placeholder="e.g. Seminar fine, late fee…">
                </div>
                <button type="submit" class="btn btn-fine">Add Fine</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
