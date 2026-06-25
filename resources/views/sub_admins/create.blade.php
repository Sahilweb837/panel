@extends('layouts.app')

@section('title', 'Create Sub-Admin / Staff')
@section('page-title', 'Create Sub-Admin / Staff Member')

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
                        <i class="fas fa-user-plus me-2"></i>Register New Account
                    </h3>
                </div>

                <form method="POST" action="{{ route('sub-admins.store') }}" class="form-card p-0">
                    @csrf

                    <div class="form-group-grid">
                        <div class="form-group">
                            <label for="name" class="fw-semibold mb-2">
                                <i class="fas fa-user text-first me-2"></i>Full Name
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
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
                                value="{{ old('email') }}" 
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
                                value="{{ old('username') }}" 
                                required 
                                placeholder="Choose a unique username"
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
                            <select 
                                id="role" 
                                name="role" 
                                required 
                                class="form-input {{ $errors->has('role') ? 'is-invalid' : '' }}"
                            >
                                <option value="">-- Select Role --</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                            @error('role')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mt-3">
                        <div class="form-group">
                            <label for="password" class="fw-semibold mb-2">
                                <i class="fas fa-lock text-first me-2"></i>Password
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                placeholder="Enter strong password (min. 6 characters)"
                                class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            />
                            @error('password')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="fw-semibold mb-2">
                                <i class="fas fa-lock text-first me-2"></i>Confirm Password
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required 
                                placeholder="Confirm security password"
                                class="form-input"
                            />
                        </div>
                    </div>

                    <div class="form-group border rounded p-4 mb-4 mt-4" style="background: var(--surface-soft); border: 1px solid var(--border) !important; border-radius: 12px !important;">
                        <label style="font-weight: 700; color: var(--first-color); text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em; display: block; margin-bottom: 16px;">
                            <i class="fas fa-shield-halved me-2"></i>Module Access Permissions
                        </label>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="courses" {{ is_array(old('access')) && in_array('courses', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-book text-first me-1"></i>Courses</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="students" {{ is_array(old('access')) && in_array('students', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-graduation-cap text-first me-1"></i>Students</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="employees" {{ is_array(old('access')) && in_array('employees', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-person-chalkboard text-first me-1"></i>Staff Directory</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="employee-attendances" {{ is_array(old('access')) && in_array('employee-attendances', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-clipboard-user text-first me-1"></i>Staff Attendance</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="attendances" {{ is_array(old('access')) && in_array('attendances', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-clipboard-check text-first me-1"></i>Student Attendance</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="fee-invoices" {{ is_array(old('access')) && in_array('fee-invoices', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-file-invoice-dollar text-first me-1"></i>Fee Invoices</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="expenses" {{ is_array(old('access')) && in_array('expenses', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-money-bill-wave text-first me-1"></i>Expenses</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="salary-slips" {{ is_array(old('access')) && in_array('salary-slips', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
                                    <span style="display: inline-flex !important; align-items: center !important; gap: 6px !important;"><i class="fas fa-wallet text-first me-1"></i>Salary Slips</span>
                                </label>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="checkbox-label" style="cursor: pointer; display: flex !important; flex-direction: row !important; align-items: center !important; gap: 8px !important; width: auto !important; margin: 0 !important;">
                                    <input type="checkbox" name="access[]" value="trainings" {{ is_array(old('access')) && in_array('trainings', old('access')) ? 'checked' : '' }} class="checkbox-input" style="width: 18px !important; height: 18px !important; margin: 0 !important; padding: 0 !important; flex-shrink: 0 !important; accent-color: var(--first-color) !important;">
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
                                {{ old('status', true) ? 'checked' : '' }} 
                                class="checkbox-input"
                            />
                            <span class="fw-semibold">
                                <i class="fas fa-circle-check text-success me-1"></i>Active Status
                            </span>
                        </label>
                        <small style="color: var(--muted); margin-left: 28px;" class="d-block mt-1">Activate user's system credentials immediately on creation.</small>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('sub-admins.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-save me-2"></i>Create Account
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
