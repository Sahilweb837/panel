@extends('layouts.app')

@section('title', 'Generate Salary Slip')
@section('page-title', 'Generate Staff Salary Slip')

@section('content')
    <div class="salary-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card" style="max-width: 850px;">
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
            <div class="card premium-form-card" style="max-width: 850px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-wallet me-2"></i>Generate Staff Salary Slip
                    </h3>
                </div>

                <form action="{{ route('salary_slips.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    <!-- Section 1: Staff Selection -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-person-chalkboard me-1"></i> Staff Member</h5>
                    <div class="form-group mb-4" style="position: relative;">
                        <label for="employee_id" class="fw-semibold mb-2">
                            <i class="fas fa-user text-first me-2"></i>Select Employee Recipient
                        </label>
                        <div style="position: relative;">
                            <span class="position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="fas fa-id-card text-muted"></i></span>
                            <select id="employee_id" name="employee_id" required class="form-input {{ $errors->has('employee_id') ? 'is-invalid' : '' }}" style="padding-left: 38px;">
                                <option value="">-- Choose employee to pay --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" data-salary="{{ $employee->salary }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_code }} - {{ $employee->user?->name }} ({{ $employee->designation ?? 'Staff' }} &bull; {{ $employee->department ?? 'General' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('employee_id')
                            <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Section 2: Period -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-calendar-days me-1"></i> Payroll Period</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="month" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-check text-first me-2"></i>Month Name
                            </label>
                            <input type="text" id="month" name="month" value="{{ old('month') }}" placeholder="e.g. January, February..." required class="form-input {{ $errors->has('month') ? 'is-invalid' : '' }}" />
                            @error('month')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="year" class="fw-semibold mb-2">
                                <i class="fas fa-calendar text-first me-2"></i>Year
                            </label>
                            <input type="text" id="year" name="year" value="{{ old('year', now()->year) }}" required class="form-input {{ $errors->has('year') ? 'is-invalid' : '' }}" />
                            @error('year')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 3: Salary Components -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-coins me-1"></i> Salary Breakdown (INR)</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="basic_salary" class="fw-semibold mb-2">
                                <i class="fas fa-money-bill text-first me-2"></i>Basic Base Salary
                            </label>
                            <input type="number" id="basic_salary" name="basic_salary" step="0.01" value="{{ old('basic_salary', 0) }}" required placeholder="0.00" class="form-input {{ $errors->has('basic_salary') ? 'is-invalid' : '' }}" />
                            @error('basic_salary')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="allowances" class="fw-semibold mb-2">
                                <i class="fas fa-circle-plus text-first me-2"></i>Allowances (Bonus, HRA)
                            </label>
                            <input type="number" id="allowances" name="allowances" step="0.01" value="{{ old('allowances', 0) }}" placeholder="0.00" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="deductions" class="fw-semibold mb-2">
                                <i class="fas fa-circle-minus text-first me-2"></i>Deductions (Taxes, PF)
                            </label>
                            <input type="number" id="deductions" name="deductions" step="0.01" value="{{ old('deductions', 0) }}" placeholder="0.00" class="form-input" />
                        </div>
                        <div class="form-group">
                            <label for="payment_date" class="fw-semibold mb-2">
                                <i class="fas fa-calendar text-first me-2"></i>Payment Date (Optional)
                            </label>
                            <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="status" class="fw-semibold mb-2">
                            <i class="fas fa-circle-info text-first me-2"></i>Payroll Settlement Status
                        </label>
                        <select id="status" name="status" required class="form-input {{ $errors->has('status') ? 'is-invalid' : '' }}">
                            <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>⏳ Pending / Processed</option>
                            <option value="Paid" {{ old('status', 'Paid') === 'Paid' ? 'selected' : '' }}>✓ Paid In Full</option>
                        </select>
                        @error('status')
                            <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Realtime Math Slate Card -->
                    <div style="background: var(--surface-soft); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-top: 24px; margin-bottom: 24px;">
                        <h5 class="fw-bold uppercase-bold mb-3 text-first" style="font-size: 0.8rem;"><i class="fas fa-calculator me-1"></i> Live Payroll Sheet Summary</h5>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block small mb-1">Basic Base</span>
                                <strong id="calc-basic" style="font-size: 1.15rem; color: var(--text);">0.00</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-success d-block small mb-1">+ Allowances</span>
                                <strong id="calc-allowance" style="font-size: 1.15rem; color: var(--success-text);">0.00</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-danger d-block small mb-1">- Deductions</span>
                                <strong id="calc-deduction" style="font-size: 1.15rem; color: var(--danger-text);">0.00</strong>
                            </div>
                            <div class="col-6 col-md-3 border-start ps-3">
                                <span class="text-first d-block small mb-1" style="font-weight: 700;">Net Payroll (INR)</span>
                                <strong id="calc-net" style="font-size: 1.35rem; color: var(--first-color); font-weight: 800;">0.00</strong>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('salary_slips.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Generate Salary Slip
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const employeeSelect = document.getElementById('employee_id');
            const basicInput = document.getElementById('basic_salary');
            const allowanceInput = document.getElementById('allowances');
            const deductionInput = document.getElementById('deductions');

            function updateCalculation() {
                const basic = parseFloat(basicInput.value) || 0;
                const allowance = parseFloat(allowanceInput.value) || 0;
                const deduction = parseFloat(deductionInput.value) || 0;
                const net = basic + allowance - deduction;

                document.getElementById('calc-basic').textContent = basic.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('calc-allowance').textContent = allowance.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('calc-deduction').textContent = deduction.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('calc-net').textContent = net.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }

            employeeSelect.addEventListener('change', function() {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                if(selectedOption && selectedOption.value) {
                    const salary = parseFloat(selectedOption.getAttribute('data-salary')) || 0;
                    basicInput.value = salary;
                } else {
                    basicInput.value = 0;
                }
                updateCalculation();
            });

            basicInput.addEventListener('input', updateCalculation);
            allowanceInput.addEventListener('input', updateCalculation);
            deductionInput.addEventListener('input', updateCalculation);

            // Initial calculation
            updateCalculation();

            // Lazy loading transition script
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection
