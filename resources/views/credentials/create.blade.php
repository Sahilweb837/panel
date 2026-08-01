@extends('layouts.app')

@section('title', 'Create / Link Credential')
@section('page-title', 'Create or Link Login Credential')

@section('content')
<div class="card premium-stat-card border-0 p-4">
    <div class="alert alert-info border-0 rounded-3 mb-4" style="background: rgba(255, 85, 50, 0.08); color: var(--text);">
        <i class="fas fa-info-circle text-first me-2"></i>
        Select the account type tab below, pick a registered person to auto-populate, and generate their login account.
    </div>

    <!-- Segment / Tab Buttons to Separate Student and Staff -->
    <div class="d-flex gap-2 mb-4 p-1 rounded bg-light" style="width: fit-content; border: 1px solid #e2e8f0;">
        <button type="button" id="tab_student" class="btn btn-sm px-4 py-2 fw-bold text-dark border-0 rounded" style="background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.06); transition: all 0.2s;" onclick="switchCredentialType('student')">
            🎓 Student Credential
        </button>
        <button type="button" id="tab_employee" class="btn btn-sm px-4 py-2 fw-bold text-secondary border-0 rounded" style="background: transparent; transition: all 0.2s;" onclick="switchCredentialType('employee')">
            👔 Staff Credential
        </button>
    </div>

    <form action="{{ route('credentials.store') }}" method="POST">
        @csrf

        <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id') }}">
        <input type="hidden" name="employee_id" id="employee_id" value="{{ old('employee_id') }}">

        <!-- Student Dropdown Selector Wrapper -->
        <div id="student_select_wrapper" class="mb-4 p-3 rounded-3 border" style="background: var(--surface-soft);">
            <label for="student_select" class="form-label fw-bold text-dark-title">
                <i class="fas fa-user-graduate text-first me-2"></i>Select Registered Student
            </label>
            <select id="student_select" class="form-input form-select-lg" style="font-size: 1rem; font-weight: 600;">
                <option value="">-- Choose Registered Student (or fill details manually) --</option>
                @foreach($students as $st)
                    @php
                        $stName = trim($st->first_name . ' ' . ($st->last_name ?? ''));
                        $stUsername = $st->user?->username ?? $st->admission_no;
                        $stEmail = $st->user?->email ?? ($st->email ?? '');
                        $stPw = $st->user?->raw_password ?? $st->admission_no;
                        $hasAcc = $st->user_id ? ' (Account Active)' : ' (New Account)';
                    @endphp
                    <option value="{{ $st->id }}"
                            data-id="{{ $st->id }}"
                            data-name="{{ $stName }}"
                            data-username="{{ $stUsername }}"
                            data-email="{{ $stEmail }}"
                            data-password="{{ $stPw }}"
                            data-role-slug="student">
                        {{ $stName }} - ID: {{ $st->admission_no }}{{ $hasAcc }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Staff Dropdown Selector Wrapper -->
        <div id="employee_select_wrapper" class="mb-4 p-3 rounded-3 border d-none" style="background: var(--surface-soft);">
            <label for="employee_select" class="form-label fw-bold text-dark-title">
                <i class="fas fa-user-tie text-first me-2"></i>Select Registered Staff Member
            </label>
            <select id="employee_select" class="form-input form-select-lg" style="font-size: 1rem; font-weight: 600;">
                <option value="">-- Choose Registered Staff Member (or fill details manually) --</option>
                @foreach($employees as $emp)
                    @php
                        $empName = $emp->user?->name ?? ('Staff Member #'.$emp->id);
                        $empUsername = $emp->user?->username ?? ($emp->employee_code ?? 'EMP-'.$emp->id);
                        $empEmail = $emp->user?->email ?? '';
                        $empPw = $emp->user?->raw_password ?? '123456';
                        $hasEmpAcc = $emp->user_id ? ' (Account Active)' : ' (New Account)';
                    @endphp
                    <option value="{{ $emp->id }}"
                            data-id="{{ $emp->id }}"
                            data-name="{{ $empName }}"
                            data-username="{{ $empUsername }}"
                            data-email="{{ $empEmail }}"
                            data-password="{{ $empPw }}"
                            data-role-slug="staff">
                        {{ $empName }} - Code: {{ $emp->employee_code }}{{ $hasEmpAcc }}
                    </option>
                @endforeach
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
    let activeType = 'student';

    function switchCredentialType(type) {
        activeType = type;
        const studentWrapper = document.getElementById('student_select_wrapper');
        const employeeWrapper = document.getElementById('employee_select_wrapper');
        const tabStudent = document.getElementById('tab_student');
        const tabEmployee = document.getElementById('tab_employee');
        const studentIdInput = document.getElementById('student_id');
        const employeeIdInput = document.getElementById('employee_id');
        
        // Reset selectors and inputs
        document.getElementById('student_select').value = "";
        document.getElementById('employee_select').value = "";
        studentIdInput.value = '';
        employeeIdInput.value = '';

        // Dynamically show/hide options in the Role selection box
        const roleSelect = document.getElementById('role_id');
        if (roleSelect) {
            for (let i = 0; i < roleSelect.options.length; i++) {
                const opt = roleSelect.options[i];
                if (!opt.value) continue; // Skip placeholder
                const slug = opt.dataset.slug;
                if (type === 'student') {
                    if (slug === 'student') {
                        opt.style.display = '';
                        opt.disabled = false;
                    } else {
                        opt.style.display = 'none';
                        opt.disabled = true;
                    }
                } else {
                    if (slug === 'student') {
                        opt.style.display = 'none';
                        opt.disabled = true;
                    } else {
                        opt.style.display = '';
                        opt.disabled = false;
                    }
                }
            }
        }

        if (type === 'student') {
            studentWrapper.classList.remove('d-none');
            employeeWrapper.classList.add('d-none');
            
            tabStudent.style.background = '#ffffff';
            tabStudent.style.boxShadow = '0 2px 4px rgba(0,0,0,0.06)';
            tabStudent.classList.remove('text-secondary');
            tabStudent.classList.add('text-dark');
            
            tabEmployee.style.background = 'transparent';
            tabEmployee.style.boxShadow = 'none';
            tabEmployee.classList.remove('text-dark');
            tabEmployee.classList.add('text-secondary');
            
            // Set Role select to 'student' by default
            selectRoleBySlug('student');
        } else {
            employeeWrapper.classList.remove('d-none');
            studentWrapper.classList.add('d-none');
            
            tabEmployee.style.background = '#ffffff';
            tabEmployee.style.boxShadow = '0 2px 4px rgba(0,0,0,0.06)';
            tabEmployee.classList.remove('text-secondary');
            tabEmployee.classList.add('text-dark');
            
            tabStudent.style.background = 'transparent';
            tabStudent.style.boxShadow = 'none';
            tabStudent.classList.remove('text-dark');
            tabStudent.classList.add('text-secondary');
            
            // Set Role select to 'staff' by default
            selectRoleBySlug('staff');
        }
    }

    function selectRoleBySlug(roleSlug) {
        const roleSelect = document.getElementById('role_id');
        if (roleSelect) {
            for (let i = 0; i < roleSelect.options.length; i++) {
                const opt = roleSelect.options[i];
                if (opt.dataset && opt.dataset.slug === roleSlug) {
                    roleSelect.selectedIndex = i;
                    break;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const studentSelect = document.getElementById('student_select');
        const employeeSelect = document.getElementById('employee_select');
        const nameInput = document.getElementById('name');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const studentIdInput = document.getElementById('student_id');
        const employeeIdInput = document.getElementById('employee_id');

        function handleSelection(selectEl, idInput, type) {
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            if (!selectedOpt || !selectedOpt.value) {
                idInput.value = '';
                return;
            }

            const id = selectedOpt.dataset.id;
            const name = selectedOpt.dataset.name;
            const username = selectedOpt.dataset.username;
            const email = selectedOpt.dataset.email;
            const password = selectedOpt.dataset.password;
            const roleSlug = selectedOpt.dataset.roleSlug;

            idInput.value = id;
            if (type === 'student') {
                document.getElementById('employee_id').value = '';
            } else {
                document.getElementById('student_id').value = '';
            }

            if (name) nameInput.value = name;
            if (username) usernameInput.value = username;
            if (email !== undefined) emailInput.value = email;
            if (password) passwordInput.value = password;

            selectRoleBySlug(roleSlug);
        }

        studentSelect.addEventListener('change', function() {
            handleSelection(this, studentIdInput, 'student');
        });

        employeeSelect.addEventListener('change', function() {
            handleSelection(this, employeeIdInput, 'employee');
        });

        // Initialize active state and role selection
        switchCredentialType('student');
    });
</script>
@endsection
