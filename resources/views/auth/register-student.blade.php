@extends('layouts.app')

@section('title', 'Student Registration')
@section('page-title', 'Create Student Account')

@section('content')
<style>
    .registration-wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    .registration-card {
        background: var(--surface-card);
        border: 1px solid var(--border-sutil);
        border-radius: 16px;
        padding: 32px;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-sutil);
    }
    .required-field::after {
        content: " *";
        color: var(--accent-alert);
    }
</style>

<div class="registration-wrapper">
    <div class="registration-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-2">Student Registration</h2>
            <p class="text-muted">Create a student account to access the portal</p>
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

        <form method="POST" action="{{ route('register.student') }}" id="studentRegisterForm">
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
                        <div class="input-group">
                            <input type="tel" name="phone_number" id="regPhoneNumber" class="form-control" value="{{ old('phone_number') }}" required placeholder="+91 98765 43210" />
                            <button class="btn btn-outline-success" type="button" onclick="sendOtp()">Verify</button>
                        </div>
                        <input type="hidden" name="phone_verified" id="phoneVerified" value="0" />
                        <input type="hidden" id="regSessionInfo" />
                        <div id="regOtpSection" style="display: none;" class="mt-2">
                            <div class="input-group">
                                <input type="text" id="regOtpInput" class="form-control" placeholder="Enter 6-digit OTP" maxlength="6" />
                                <button class="btn btn-primary" type="button" onclick="verifyOtp()">Verify OTP</button>
                            </div>
                            <small class="text-muted">OTP expires in 10 minutes. <a href="javascript:void(0)" onclick="sendOtp()">Resend</a></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-section-title"><i class="fas fa-id-card me-2"></i>Student Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Admission Number</label>
                        <input type="text" name="admission_no" class="form-control" value="{{ old('admission_no') }}" required placeholder="Enter admission number" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required placeholder="Enter first name" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" placeholder="Enter last name" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold required-field">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Enter address">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">
                    <i class="fas fa-user-plus me-2"></i>Register Student
                </button>
                <a href="{{ route('login') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">Already have an account? Login</a>
            </div>
        </form>
    </div>
</div>

<script>
    async function sendOtp() {
        const phone = document.getElementById('regPhoneNumber').value.trim();
        if (!phone) { alert('Please enter a phone number.'); return; }
        
        try {
            const res = await fetch('/firebase/send-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ phone_number: phone })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('regSessionInfo').value = data.sessionInfo || '';
                document.getElementById('regOtpSection').style.display = 'block';
                alert('OTP sent! Enter the 6-digit code.');
            } else {
                alert(data.message || 'Failed to send OTP.');
            }
        } catch (e) { alert('Network error.'); console.error(e); }
    }

    async function verifyOtp() {
        const phone = document.getElementById('regPhoneNumber').value.trim();
        const otp = document.getElementById('regOtpInput').value.trim();
        const sessionInfo = document.getElementById('regSessionInfo').value;
        if (!phone || !otp || otp.length !== 6) { alert('Enter valid OTP.'); return; }
        
        try {
            const res = await fetch('/firebase/verify-otp', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ phone_number: phone, otp: otp, session_info: sessionInfo })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('phoneVerified').value = '1';
                document.getElementById('regOtpSection').innerHTML = '<div class="alert alert-success py-2 mb-0"><i class="fas fa-check-circle me-1"></i>Phone verified successfully!</div>';
            } else {
                alert(data.message || 'Invalid OTP.');
            }
        } catch (e) { alert('Network error.'); console.error(e); }
    }
</script>
@endsection
