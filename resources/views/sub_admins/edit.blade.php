@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit ' . $subAdmin->name)

@section('content')
    <div class="staff-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card">
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
            <div class="card premium-form-card">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-user-pen me-2"></i>Update User Credentials
                    </h3>
                </div>

                <form method="POST" action="{{ route('sub-admins.update', $subAdmin) }}" class="form-card p-0">
                    @csrf @method('PUT')

                    <div class="form-group-grid">
                        <div class="form-group">
                            <label for="name" class="fw-semibold mb-2">
                                <i class="fas fa-user text-first me-2"></i>Full Name
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $subAdmin->name) }}" 
                                required 
                                placeholder="Enter full name"
                                class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            />
                            @error('name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="fw-semibold mb-2">
                                <i class="fas fa-envelope text-first me-2"></i>Email Address
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email', $subAdmin->email) }}" 
                                required 
                                placeholder="Enter email address"
                                class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            />
                            @error('email')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mt-3">
                        <div class="form-group">
                            <label for="username" class="fw-semibold mb-2">
                                <i class="fas fa-at text-first me-2"></i>Username
                            </label>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                value="{{ old('username', $subAdmin->username) }}" 
                                required 
                                placeholder="Enter username"
                                class="form-input {{ $errors->has('username') ? 'is-invalid' : '' }}"
                            />
                            @error('username')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="role" class="fw-semibold mb-2">
                                <i class="fas fa-user-tag text-first me-2"></i>Role
                            </label>
                            <select id="role" name="role" required class="form-input {{ $errors->has('role') ? 'is-invalid' : '' }}">
                                <option value="admin" {{ old('role', $subAdmin->role?->slug) === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ old('role', $subAdmin->role?->slug) === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('role')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mt-3">
                        <div class="form-group">
                            <label for="password" class="fw-semibold mb-2">
                                <i class="fas fa-lock text-first me-2"></i>New Password
                            </label>
                            <div class="d-flex gap-2">
                                <div class="position-relative flex-grow-1">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Leave blank to retain current password"
                                        class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                        style="padding-right: 40px;"
                                    />
                                    <button type="button" onclick="togglePasswordVisibility('password')" class="position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent pe-3 text-muted" style="z-index: 10; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-eye" id="password_eye"></i>
                                    </button>
                                </div>
                                <button type="button" onclick="autoGeneratePassword('password', 'password_confirmation')" class="button button-secondary flex-shrink-0" style="padding: 10px 15px; margin: 0; display: flex; align-items: center; gap: 6px; height: 48px;">
                                    <i class="fas fa-magic"></i> Generate
                                </button>
                            </div>
                            <small style="color: var(--muted);" class="mt-1 d-block">Only fill this to override security password</small>
                            @error('password')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="fw-semibold mb-2">
                                <i class="fas fa-lock text-first me-2"></i>Confirm Password
                            </label>
                            <div class="position-relative">
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Re-enter security password"
                                    class="form-input"
                                    style="padding-right: 40px;"
                                />
                                <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent pe-3 text-muted" style="z-index: 10; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-eye" id="password_confirmation_eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group border rounded p-4 mb-4 mt-4" style="background: var(--surface-soft); border: 1px solid var(--border) !important; border-radius: 12px !important;">
                        <label style="font-weight: 700; color: var(--first-color); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; display: block; margin-bottom: 16px;">
                            <i class="fas fa-shield-halved me-2"></i>Module Access Permissions
                        </label>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="courses" {{ is_array(old('access', $subAdmin->access)) && in_array('courses', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-book text-first me-1"></i>Courses</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="training-courses" {{ is_array(old('access', $subAdmin->access)) && in_array('training-courses', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-graduation-cap text-first me-1"></i>Training Courses</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="students" {{ is_array(old('access', $subAdmin->access)) && in_array('students', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-graduation-cap text-first me-1"></i>Students</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="employees" {{ is_array(old('access', $subAdmin->access)) && in_array('employees', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-person-chalkboard text-first me-1"></i>Staff Directory</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="employee-attendances" {{ is_array(old('access', $subAdmin->access)) && in_array('employee-attendances', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-clipboard-user text-first me-1"></i>Staff Attendance</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="attendances" {{ is_array(old('access', $subAdmin->access)) && in_array('attendances', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-clipboard-check text-first me-1"></i>Student Attendance</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="fee-invoices" {{ is_array(old('access', $subAdmin->access)) && in_array('fee-invoices', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-file-invoice-dollar text-first me-1"></i>Fee Invoices</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="expenses" {{ is_array(old('access', $subAdmin->access)) && in_array('expenses', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-money-bill-wave text-first me-1"></i>Expenses</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="salary-slips" {{ is_array(old('access', $subAdmin->access)) && in_array('salary-slips', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-wallet text-first me-1"></i>Salary Slips</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="trainings" {{ is_array(old('access', $subAdmin->access)) && in_array('trainings', old('access', $subAdmin->access)) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-graduation-cap text-first me-1"></i>Training & Internship</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group checkbox-group mt-3">
                        <label class="checkbox-label" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="status" 
                                value="1" 
                                {{ old('status', $subAdmin->status) ? 'checked' : '' }} 
                                class="checkbox-input"
                            />
                            <span class="fw-semibold">
                                <i class="fas fa-circle-check text-success me-1"></i>Active Status
                            </span>
                        </label>
                        <small style="color: var(--muted); margin-left: 28px;" class="d-block mt-1">Uncheck this to freeze credentials and deactivate this account.</small>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('sub-admins.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
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
        });
    </script>
@endsection
