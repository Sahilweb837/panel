@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f3e5f5, #e1f5fe);}
    .card {background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-radius: 12px; padding: 2rem; max-width: 500px; margin: 3rem auto; box-shadow: 0 8px 24px rgba(0,0,0,0.1);}
    .card h2 {color: #424242; margin-bottom: 1rem;}
    .detail {margin-bottom: 0.8rem; color: #616161;}
    .amount {font-size: 1.4rem; font-weight: 600; color: #0288d1;}
    .btn-pay {background:#0288d1; color:#fff; padding:0.8rem 1.5rem; border:none; border-radius:6px; cursor:pointer; transition:transform 0.2s;}
    .btn-pay:hover {background:#0277bd; transform:scale(1.03);}
</style>
<div class="card">
    <h2>Prospect Invoice</h2>
    <div class="detail"><strong>Name:</strong> {{ $prospect->name }}</div>
    <div class="detail"><strong>Email:</strong> {{ $prospect->email }}</div>
    <div class="detail"><strong>Registration Fee:</strong> <span class="amount">${{ number_format($prospect->registration_fee, 2) }}</span></div>
    <div class="detail"><strong>Monthly Fee:</strong> ${{ number_format($prospect->monthly_fee, 2) }}</div>
    <hr>
    <div class="detail"><strong>Total Due (First Payment):</strong> <span class="amount">${{ number_format($prospect->registration_fee, 2) }}</span></div>
    <form method="POST" action="{{ route('prospects.pay', ['id' => $prospect->id]) }}">
        @csrf
        <button type="submit" class="btn-pay">Pay Now</button>
    </form>
</div>
@endsection
