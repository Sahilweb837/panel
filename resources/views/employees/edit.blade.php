@extends('layouts.app')

@section('title', 'Edit Staff Member')
@section('page-title', 'Edit Staff Profile')

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
                        <i class="fas fa-user-pen me-2"></i>Edit Staff Profile: {{ $employee->user?->name ?? 'Staff' }}
                    </h3>
                </div>

                <form action="{{ route('employees.update', $employee) }}" method="POST" class="form-card p-0">
                    @csrf
                    @method('PUT')

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
                                value="{{ old('employee_code', $employee->employee_code) }}" 
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
                                value="{{ old('staff_name', $employee->user?->name) }}" 
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
                                value="{{ old('biometric_id', $employee->biometric_id) }}" 
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
                                value="{{ old('login_email', $employee->user?->email) }}" 
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
                                value="{{ old('login_username', $employee->user?->username) }}" 
                                placeholder="Choose username for portal" 
                                class="form-input {{ $errors->has('login_username') ? 'is-invalid' : '' }}" 
                            />
                            @error('login_username')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="login_password" class="fw-semibold mb-2">
                                <i class="fas fa-lock text-first me-2"></i>New Password (Optional)
                            </label>
                            <input 
                                type="password" 
                                id="login_password" 
                                name="login_password" 
                                placeholder="Leave blank to keep existing password" 
                                class="form-input {{ $errors->has('login_password') ? 'is-invalid' : '' }}" 
                            />
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
                                value="{{ old('phone', $employee->phone) }}" 
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
                                value="{{ old('department', $employee->department) }}" 
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
                                value="{{ old('designation', $employee->designation) }}" 
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
                                value="{{ old('salary', $employee->salary) }}" 
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
                                value="{{ old('joining_date', $employee->joining_date) }}" 
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
                        >{{ old('address', $employee->address) }}</textarea>
                    </div>

                    <div class="form-group checkbox-group mt-4">
                        <label class="checkbox-label" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="status" 
                                value="1" 
                                {{ old('status', $employee->status) ? 'checked' : '' }} 
                                class="checkbox-input"
                            />
                            <span class="fw-semibold">
                                <i class="fas fa-circle-check text-success me-1"></i>Active Status
                            </span>
                        </label>
                        <small style="color: var(--muted); margin-left: 28px;" class="d-block mt-1">Activate the staff member's credentials and records immediately on registration.</small>
                    </div>

                    <!-- Section 5: Bank Details for Payroll -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3 mt-2" style="font-size: 0.75rem;">
                        <i class="fas fa-building-columns me-1 text-first"></i> Bank Details (for Salary Payroll)
                    </h5>
                    <div class="p-3 mb-4 rounded" style="background:rgba(255,85,50,0.05); border:1px solid rgba(255,85,50,0.15);">
                        <small class="text-muted d-block mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            These details are used by <strong>Razorpay Payouts</strong> to transfer salary directly to this employee's bank account.
                        </small>
                        <div class="form-group-grid mb-3">
                            <div class="form-group">
                                <label class="fw-semibold mb-2"><i class="fas fa-user text-first me-2"></i>Account Holder Name</label>
                                <input type="text" name="account_holder_name" class="form-input"
                                    value="{{ old('account_holder_name', $employee->account_holder_name) }}"
                                    placeholder="Name as on bank account" />
                            </div>
                            <div class="form-group">
                                <label class="fw-semibold mb-2"><i class="fas fa-hashtag text-first me-2"></i>Bank Account Number</label>
                                <input type="text" name="bank_account_no" class="form-input"
                                    value="{{ old('bank_account_no', $employee->bank_account_no) }}"
                                    placeholder="e.g. 1234567890123456" />
                            </div>
                            <div class="form-group">
                                <label class="fw-semibold mb-2"><i class="fas fa-code text-first me-2"></i>IFSC Code</label>
                                <input type="text" name="bank_ifsc" class="form-input"
                                    value="{{ old('bank_ifsc', $employee->bank_ifsc) }}"
                                    placeholder="e.g. HDFC0001234" style="text-transform:uppercase;" />
                            </div>
                            <div class="form-group">
                                <label class="fw-semibold mb-2"><i class="fas fa-landmark text-first me-2"></i>Bank Name</label>
                                <input type="text" name="bank_name" class="form-input"
                                    value="{{ old('bank_name', $employee->bank_name) }}"
                                    placeholder="e.g. HDFC Bank, SBI" />
                            </div>
                        </div>
                        @if($employee->razorpay_contact_id)
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Razorpay Contact Linked</span>
                            <small class="text-muted">{{ $employee->razorpay_contact_id }}</small>
                        </div>
                        @endif
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('employees.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-save me-2"></i>Update Staff Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and skeleton fading -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection
