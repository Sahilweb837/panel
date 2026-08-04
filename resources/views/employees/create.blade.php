@extends('layouts.app')

@section('title', 'Add Staff Member')
@section('page-title', 'Register New Staff Profile')

@section('content')
    <div class="staff-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card" style="max-width: 900px;">
                <div class="sk-text heading"></div>
                <div class="form-group-grid">
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                </div>
                <div class="form-group-grid mt-4">
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                </div>
                <div class="form-actions-row">
                    <div class="sk-button"></div>
                    <div class="sk-button"></div>
                </div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="card premium-form-card" style="max-width: 900px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-user-plus me-2"></i>Register New Staff Profile
                    </h3>
                </div>

                <form action="{{ route('employees.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    <!-- Section 1: Official Identifiers -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-id-card me-1"></i> Staff System Identifiers</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="employee_code" class="fw-semibold mb-2">
                                <i class="fas fa-hashtag text-first me-2"></i>Employee Code
                            </label>
                            <input 
                                type="text" 
                                id="employee_code" 
                                name="employee_code" 
                                value="{{ old('employee_code') }}" 
                                required 
                                placeholder="e.g. EMP-2026-001" 
                                class="form-input {{ $errors->has('employee_code') ? 'is-invalid' : '' }}" 
                            />
                            @error('employee_code')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="staff_name" class="fw-semibold mb-2">
                                <i class="fas fa-user text-first me-2"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="staff_name" 
                                name="staff_name" 
                                value="{{ old('staff_name') }}" 
                                required 
                                placeholder="Enter staff member name" 
                                class="form-input {{ $errors->has('staff_name') ? 'is-invalid' : '' }}" 
                            />
                            @error('staff_name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="biometric_id" class="fw-semibold mb-2">
                                <i class="fas fa-fingerprint text-first me-2"></i>Biometric Machine ID
                            </label>
                            <input 
                                type="text" 
                                id="biometric_id" 
                                name="biometric_id" 
                                value="{{ old('biometric_id') }}" 
                                placeholder="Hardware Fingerprint ID" 
                                class="form-input {{ $errors->has('biometric_id') ? 'is-invalid' : '' }}" 
                            />
                            @error('biometric_id')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 2: Account Login Credentials -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-shield-halved me-1"></i> Portal Access Credentials</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="login_email" class="fw-semibold mb-2">
                                <i class="fas fa-envelope text-first me-2"></i>Login Email Address
                            </label>
                            <input 
                                type="email" 
                                id="login_email" 
                                name="login_email" 
                                value="{{ old('login_email') }}" 
                                placeholder="e.g. staff@domain.com" 
                                class="form-input {{ $errors->has('login_email') ? 'is-invalid' : '' }}" 
                            />
                            @error('login_email')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="login_username" class="fw-semibold mb-2">
                                <i class="fas fa-at text-first me-2"></i>Login Username
                            </label>
                            <input 
                                type="text" 
                                id="login_username" 
                                name="login_username" 
                                value="{{ old('login_username') }}" 
                                placeholder="Choose username for portal" 
                                class="form-input {{ $errors->has('login_username') ? 'is-invalid' : '' }}" 
                            />
                            @error('login_username')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="login_password" class="fw-semibold mb-2">
                                <i class="fas fa-lock text-first me-2"></i>Login Password
                            </label>
                            <div class="d-flex gap-2">
                                <div class="position-relative flex-grow-1">
                                    <input 
                                        type="password" 
                                        id="login_password" 
                                        name="login_password" 
                                        required 
                                        placeholder="Set secure password" 
                                        class="form-input {{ $errors->has('login_password') ? 'is-invalid' : '' }}" 
                                        style="padding-right: 40px;"
                                    />
                                    <button type="button" onclick="togglePasswordVisibility('login_password')" class="position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent pe-3 text-muted" style="z-index: 10; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-eye" id="login_password_eye"></i>
                                    </button>
                                </div>
                                <button type="button" onclick="autoGeneratePassword('login_password')" class="button button-secondary flex-shrink-0" style="padding: 10px 15px; margin: 0; display: flex; align-items: center; gap: 6px; height: 48px;">
                                    <i class="fas fa-magic"></i> Generate
                                </button>
                            </div>
                            @error('login_password')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 3: Job Profile & Details -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-briefcase me-1"></i> Employment & Contact Information</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="phone" class="fw-semibold mb-2">
                                <i class="fas fa-phone text-first me-2"></i>Phone Number
                            </label>
                            <input 
                                type="text" 
                                id="phone" 
                                name="phone" 
                                value="{{ old('phone') }}" 
                                placeholder="e.g. 9876543210" 
                                class="form-input {{ $errors->has('phone') ? 'is-invalid' : '' }}" 
                            />
                            @error('phone')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="department" class="fw-semibold mb-2">
                                <i class="fas fa-building text-first me-2"></i>Department
                            </label>
                            <input 
                                type="text" 
                                id="department" 
                                name="department" 
                                value="{{ old('department') }}" 
                                placeholder="e.g. Administration, Academic" 
                                class="form-input {{ $errors->has('department') ? 'is-invalid' : '' }}" 
                            />
                            @error('department')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="designation" class="fw-semibold mb-2">
                                <i class="fas fa-user-tie text-first me-2"></i>Designation
                            </label>
                            <input 
                                type="text" 
                                id="designation" 
                                name="designation" 
                                value="{{ old('designation') }}" 
                                placeholder="e.g. Senior Lecturer, Clerk" 
                                class="form-input {{ $errors->has('designation') ? 'is-invalid' : '' }}" 
                            />
                            @error('designation')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="salary" class="fw-semibold mb-2">
                                <i class="fas fa-wallet text-first me-2"></i>Base Monthly Salary (INR)
                            </label>
                            <input 
                                type="number" 
                                id="salary" 
                                name="salary" 
                                step="0.01" 
                                value="{{ old('salary', 0) }}" 
                                required 
                                placeholder="0.00" 
                                class="form-input {{ $errors->has('salary') ? 'is-invalid' : '' }}" 
                            />
                            @error('salary')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="joining_date" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-days text-first me-2"></i>Joining Date
                            </label>
                            <input 
                                type="date" 
                                id="joining_date" 
                                name="joining_date" 
                                value="{{ old('joining_date', date('Y-m-d')) }}" 
                                class="form-input {{ $errors->has('joining_date') ? 'is-invalid' : '' }}" 
                            />
                            @error('joining_date')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 4: Address Details -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-map-location-dot me-1"></i> Residential Address</h5>
                    <div class="form-group mb-4">
                        <textarea 
                            id="address" 
                            name="address" 
                            placeholder="Enter complete residential home address..." 
                            class="form-input" 
                            style="height: 100px; resize: vertical; padding: 12px;"
                        >{{ old('address') }}</textarea>
                    </div>

                    <div class="form-group checkbox-group mt-4">
                        <label class="checkbox-label" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="status" 
                                value="1" 
                                {{ old('status', true) ? 'checked' : '' }} 
                                class="checkbox-input"
                            />
                            <span class="fw-semibold">
                                <i class="fas fa-circle-check text-success me-1"></i>Active Status
                            </span>
                        </label>
                        <small style="color: var(--muted); margin-left: 28px;" class="d-block mt-1">Activate the staff member's credentials and records immediately on registration.</small>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('employees.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-save me-2"></i>Create Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generateSecurePassword(length = 10) {
            const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%";
            let password = "";
            for (let i = 0; i < length; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            return password;
        }

        function autoGeneratePassword(passwordInputId, confirmInputId = null) {
            const pwd = generateSecurePassword(10);
            const pwdInput = document.getElementById(passwordInputId);
            if (pwdInput) {
                pwdInput.value = pwd;
                pwdInput.type = 'text'; // Make it visible initially
            }
            if (confirmInputId) {
                const confirmInput = document.getElementById(confirmInputId);
                if (confirmInput) {
                    confirmInput.value = pwd;
                    confirmInput.type = 'text';
                }
            }
            // Update toggle eye icon to show it is visible
            const eyeIcon = document.getElementById(passwordInputId + '_eye');
            if (eyeIcon) {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
            const confirmEyeIcon = document.getElementById(confirmInputId + '_eye');
            if (confirmEyeIcon) {
                confirmEyeIcon.classList.remove('fa-eye');
                confirmEyeIcon.classList.add('fa-eye-slash');
            }
        }

        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(inputId + '_eye');
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    if (eyeIcon) {
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    }
                } else {
                    input.type = 'password';
                    if (eyeIcon) {
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);

            // Auto-generate password on load
            autoGeneratePassword('login_password');
        });
    </script>
@endsection
