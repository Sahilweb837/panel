@extends('layouts.app')

@section('title', 'Create / Link Credential')
@section('page-title', 'Create or Link Login Credential')

@section('content')
<div class="card premium-stat-card border-0 p-4">
    <div class="alert alert-info border-0 rounded-3 mb-4" style="background: rgba(255, 85, 50, 0.08); color: var(--text);">
        <i class="fas fa-info-circle text-first me-2"></i>
        Select a registered <strong>Student</strong> or <strong>Staff Member</strong> from the dropdown below to auto-populate their details and generate or update their login account.
    </div>

    <form action="{{ route('credentials.store') }}" method="POST">
        @csrf

        <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}">
        <input type="hidden" name="employee_id" id="employee_id" value="{{ old('employee_id') }}">

        <!-- Top Dropdown Selector -->
        <div class="mb-4 p-3 rounded-3 border" style="background: var(--surface-soft);">
            <label for="registered_person_select" class="form-label fw-bold text-dark-title">
                <i class="fas fa-user-check text-first me-2"></i>Select Registered Student or Staff Member
            </label>
            <select id="registered_person_select" class="form-input form-select-lg" style="font-size: 1rem; font-weight: 600;">
                <option value="">-- Choose Registered Person (or create custom credential) --</option>
                
                @if(count($students) > 0)
                <optgroup label="🎓 Registered Students ({{ count($students) }})">
                    @foreach($students as $st)
                        @php
                            $stName = trim($st->first_name . ' ' . ($st->last_name ?? ''));
                            $stUsername = $st->user?->username ?? $st->admission_no;
                            $stEmail = $st->user?->email ?? ($st->email ?? '');
                            $stPw = $st->user?->raw_password ?? $st->admission_no;
                            $hasAcc = $st->user_id ? ' (Account Active)' : ' (New Account)';
                        @endphp
                        <option value="student_{{ $st->id }}"
                                data-type="student"
                                data-id="{{ $st->id }}"
                                data-name="{{ $stName }}"
                                data-username="{{ $stUsername }}"
                                data-email="{{ $stEmail }}"
                                data-password="{{ $stPw }}"
                                data-role-slug="student">
                            Student: {{ $stName }} - ID: {{ $st->admission_no }}{{ $hasAcc }}
                        </option>
                    @endforeach
                </optgroup>
                @endif

                @if(count($employees) > 0)
                <optgroup label="👔 Registered Staff Members ({{ count($employees) }})">
                    @foreach($employees as $emp)
                        @php
                            $empName = $emp->user?->name ?? ('Staff Member #'.$emp->id);
                            $empUsername = $emp->user?->username ?? ($emp->employee_code ?? 'EMP-'.$emp->id);
                            $empEmail = $emp->user?->email ?? '';
                            $empPw = $emp->user?->raw_password ?? '123456';
                            $hasEmpAcc = $emp->user_id ? ' (Account Active)' : ' (New Account)';
                        @endphp
                        <option value="employee_{{ $emp->id }}"
                                data-type="employee"
                                data-id="{{ $emp->id }}"
                                data-name="{{ $empName }}"
                                data-username="{{ $empUsername }}"
                                data-email="{{ $empEmail }}"
                                data-password="{{ $empPw }}"
                                data-role-slug="staff">
                            Staff: {{ $empName }} - Code: {{ $emp->employee_code }}{{ $hasEmpAcc }}
                        </option>
                    @endforeach
                </optgroup>
                @endif
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label fw-bold">Full Name</label>
                <input type="text" name="name" id="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Sahil Sharma">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label fw-bold">Register ID / Username</label>
                <input type="text" name="username" id="username" class="form-input @error('username') is-invalid @enderror" value="{{ old('username') }}" required placeholder="e.g. NT-ENR-001">
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-bold">Email Address (Optional)</label>
                <input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="student@netcoder.in">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label fw-bold">Plain-Text Password</label>
                <input type="text" name="password" id="password" class="form-input @error('password') is-invalid @enderror" required minlength="6" placeholder="e.g. NT-ENR-001">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="role_id" class="form-label fw-bold">Account Role</label>
                <select name="role_id" id="role_id" class="form-input @error('role_id') is-invalid @enderror" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" data-slug="{{ $role->slug }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->slug) }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="button button-primary px-4 py-2">
                <i class="fas fa-save me-2"></i>Save Credential
            </button>
            <a href="{{ route('credentials.index') }}" class="button button-secondary px-4 py-2">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectEl = document.getElementById('registered_person_select');
        const nameInput = document.getElementById('name');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const roleSelect = document.getElementById('role_id');
        const studentIdInput = document.getElementById('student_id');
        const employeeIdInput = document.getElementById('employee_id');

        selectEl.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) {
                studentIdInput.value = '';
                employeeIdInput.value = '';
                return;
            }

            const type = selectedOpt.dataset.type;
            const id = selectedOpt.dataset.id;
            const name = selectedOpt.dataset.name;
            const username = selectedOpt.dataset.username;
            const email = selectedOpt.dataset.email;
            const password = selectedOpt.dataset.password;
            const roleSlug = selectedOpt.dataset.roleSlug;

            if (type === 'student') {
                studentIdInput.value = id;
                employeeIdInput.value = '';
            } else if (type === 'employee') {
                employeeIdInput.value = id;
                studentIdInput.value = '';
            }

            if (name) nameInput.value = name;
            if (username) usernameInput.value = username;
            if (email !== undefined) emailInput.value = email;
            if (password) passwordInput.value = password;

            // Auto-select role matching role-slug
            if (roleSlug && roleSelect) {
                for (let i = 0; i < roleSelect.options.length; i++) {
                    const opt = roleSelect.options[i];
                    if (opt.dataset && opt.dataset.slug === roleSlug) {
                        roleSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        });
    });
</script>
@endsection
