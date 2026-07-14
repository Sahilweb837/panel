@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #e0f7fa, #e1bee7);}
    .card {background: rgba(255,255,255,0.85); backdrop-filter: blur(8px); border-radius: 12px; padding: 2rem; max-width: 600px; margin: 3rem auto; box-shadow: 0 4px 30px rgba(0,0,0,0.1);}
    .card h2 {color: #37474f; margin-bottom: 1rem;}
    .form-group {margin-bottom: 1.2rem;}
    label {display:block; font-weight:600; color:#455a64; margin-bottom:0.4rem;}
    input[type="text"], input[type="email"], input[type="number"] {width:100%; padding:0.6rem; border:1px solid #cfd8dc; border-radius:6px;}
    .btn-primary {background:#009688; color:#fff; padding:0.8rem 1.5rem; border:none; border-radius:6px; cursor:pointer; transition:background 0.3s;}
    .btn-primary:hover {background:#00796b;}
</style>
<div class="card">
    <h2>Create Prospect</h2>
    <form method="POST" action="{{ route('prospects.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="registration_fee">Registration Fee</label>
            <input type="number" step="0.01" name="registration_fee" id="registration_fee" required>
        </div>
        <div class="form-group">
            <label for="monthly_fee">Monthly Fee</label>
            <input type="number" step="0.01" name="monthly_fee" id="monthly_fee" required>
        </div>
        <button type="submit" class="btn-primary">Create Prospect</button>
    </form>
</div>
@endsection
