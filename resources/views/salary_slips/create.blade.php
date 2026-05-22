@extends('layouts.app')

@section('title', 'Generate Salary Slip')
@section('page-title', 'Generate Salary Slip')

@section('content')
    <div class="card" style="max-width: 700px;">
        <form action="{{ route('salary_slips.store') }}" method="POST" class="form-card">
            @csrf

            <div class="form-group">
                <label for="employee_id">
                    <i class="fas fa-person-chalkboard"></i> Employee
                </label>
                <select id="employee_id" name="employee_id" required class="form-input {{ $errors->has('employee_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Select an employee --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_code }} - {{ $employee->user?->name }} ({{ $employee->designation }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="grid grid-2 gap-4">
                <div class="form-group">
                    <label for="month">
                        <i class="fas fa-calendar-alt"></i> Month
                    </label>
                    <input type="text" id="month" name="month" value="{{ old('month') }}" placeholder="e.g., January, March" required class="form-input {{ $errors->has('month') ? 'is-invalid' : '' }}" />
                    @error('month')
                        <small style="color: var(--danger-text);">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="year">
                        <i class="fas fa-calendar-check"></i> Year
                    </label>
                    <input type="text" id="year" name="year" value="{{ old('year', now()->year) }}" required class="form-input {{ $errors->has('year') ? 'is-invalid' : '' }}" />
                    @error('year')
                        <small style="color: var(--danger-text);">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="grid grid-2 gap-4">
                <div class="form-group">
                    <label for="basic_salary">
                        <i class="fas fa-money-bill"></i> Basic Salary
                    </label>
                    <input type="number" id="basic_salary" name="basic_salary" step="0.01" value="{{ old('basic_salary', 0) }}" required placeholder="0.00" class="form-input {{ $errors->has('basic_salary') ? 'is-invalid' : '' }}" />
                    @error('basic_salary')
                        <small style="color: var(--danger-text);">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="allowances">
                        <i class="fas fa-plus-circle"></i> Allowances
                    </label>
                    <input type="number" id="allowances" name="allowances" step="0.01" value="{{ old('allowances', 0) }}" placeholder="0.00" class="form-input" />
                </div>
            </div>

            <div class="grid grid-2 gap-4">
                <div class="form-group">
                    <label for="deductions">
                        <i class="fas fa-minus-circle"></i> Deductions
                    </label>
                    <input type="number" id="deductions" name="deductions" step="0.01" value="{{ old('deductions', 0) }}" placeholder="0.00" class="form-input" />
                </div>
                <div class="form-group">
                    <label for="payment_date">
                        <i class="fas fa-calendar"></i> Payment Date (Optional)
                    </label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="form-input" />
                </div>
            </div>

            <div class="form-group">
                <label for="status">
                    <i class="fas fa-info-circle"></i> Status
                </label>
                <select id="status" name="status" required class="form-input {{ $errors->has('status') ? 'is-invalid' : '' }}">
                    <option value="">-- Select Status --</option>
                    <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="Paid" {{ old('status') === 'Paid' ? 'selected' : '' }}>✓ Paid</option>
                </select>
                @error('status')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div style="background: var(--surface-soft); padding: 16px; border-radius: 8px; margin-top: 20px; margin-bottom: 20px;">
                <h4 style="margin-top: 0;">Net Salary Calculation</h4>
                <div class="grid grid-2" style="gap: 16px;">
                    <div>
                        <p style="margin: 0 0 4px; color: var(--muted); font-size: 0.9rem;">Basic Salary</p>
                        <p style="margin: 0; font-weight: bold; font-size: 1.1rem;" id="calc-basic">0.00</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; color: var(--muted); font-size: 0.9rem;">+ Allowances</p>
                        <p style="margin: 0; font-weight: bold; font-size: 1.1rem;" id="calc-allowance">0.00</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; color: var(--muted); font-size: 0.9rem;">- Deductions</p>
                        <p style="margin: 0; font-weight: bold; font-size: 1.1rem;" id="calc-deduction">0.00</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 4px; color: var(--first-color); font-size: 0.9rem; font-weight: 600;">Net Salary</p>
                        <p style="margin: 0; font-weight: bold; font-size: 1.3rem; color: var(--first-color);" id="calc-net">0.00</p>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" class="button button-primary" style="flex: 1;">
                    <i class="fas fa-file-alt"></i> Generate Salary Slip
                </button>
                <a href="{{ route('salary_slips.index') }}" class="button button-secondary" style="flex: 1; text-align: center;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        function updateCalculation() {
            const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
            const allowance = parseFloat(document.getElementById('allowances').value) || 0;
            const deduction = parseFloat(document.getElementById('deductions').value) || 0;
            const net = basic + allowance - deduction;

            document.getElementById('calc-basic').textContent = basic.toFixed(2);
            document.getElementById('calc-allowance').textContent = allowance.toFixed(2);
            document.getElementById('calc-deduction').textContent = deduction.toFixed(2);
            document.getElementById('calc-net').textContent = net.toFixed(2);
        }

        document.getElementById('basic_salary').addEventListener('input', updateCalculation);
        document.getElementById('allowances').addEventListener('input', updateCalculation);
        document.getElementById('deductions').addEventListener('input', updateCalculation);

        updateCalculation();
    </script>
@endsection
