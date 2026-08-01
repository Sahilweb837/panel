@extends('layouts.app')

@section('title', 'Staff Registration')
@section('page-title', 'Create Staff Account')

@section('content')
<style>
    .registration-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    .registration-card {
        background: var(--surface-card);
        border: 1px solid var(--border, #e7e2df);
        border-radius: 16px;
        padding: 32px;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border, #e7e2df);
    }
    .required-field::after {
        content: " *";
        color: var(--accent-alert);
    }
</style>

<div class="registration-wrapper">
    <div class="registration-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-2">Staff Registration</h2>
            <p class="text-muted">Create a staff account to access the staff portal</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.staff') }}" id="staffRegisterForm">
            @csrf

            <div class="mb-4">
                <div class="form-section-title"><i class="fas fa-user me-2"></i>Account Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Enter full name" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Enter email address" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Enter username" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Enter password" minlength="6" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Confirm password" minlength="6" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Phone Number</label>
                        <input type="tel" name="phone_number" class="form-control" value="{{ old('phone_number') }}" required placeholder="+91 98765 43210" />
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-section-title"><i class="fas fa-briefcase me-2"></i>Staff Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Employee Code</label>
                        <input type="text" name="employee_code" class="form-control" value="{{ old('employee_code') }}" required placeholder="Enter employee code" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Designation</label>
                        <input type="text" name="designation" class="form-control" value="{{ old('designation') }}" required placeholder="Enter designation" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Department</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department') }}" placeholder="Enter department" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date') }}" />
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">
                    <i class="fas fa-user-plus me-2"></i>Register Staff
                </button>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Already have an account? Login</a>
            </div>
        </form>
    </div>
</div>
@endsection
