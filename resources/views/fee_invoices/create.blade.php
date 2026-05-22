@extends('layouts.app')

@section('title', 'Create Fee Invoice')
@section('page-title', 'Generate Fee Invoice')

@section('content')
    <div class="card" style="max-width: 700px;">
        <form action="{{ route('fee_invoices.store') }}" method="POST" class="form-card">
            @csrf

            <div class="form-group">
                <label for="student_id">
                    <i class="fas fa-user-graduate"></i> Student
                </label>
                <select id="student_id" name="student_id" required class="form-input {{ $errors->has('student_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Select a student --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->admission_no }} - {{ $student->first_name }} {{ $student->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="grid grid-2 gap-4">
                <div class="form-group">
                    <label for="invoice_no">
                        <i class="fas fa-hashtag"></i> Invoice No (Optional)
                    </label>
                    <input type="text" id="invoice_no" name="invoice_no" value="{{ old('invoice_no') }}" placeholder="Auto-generated if empty" class="form-input" />
                </div>
                <div class="form-group">
                    <label for="fee_category">
                        <i class="fas fa-tags"></i> Category
                    </label>
                    <input type="text" id="fee_category" name="fee_category" value="{{ old('fee_category') }}" placeholder="Tuition, Library, Lab, etc." class="form-input" />
                </div>
            </div>

            <div class="grid grid-2 gap-4">
                <div class="form-group">
                    <label for="total_amount">
                        <i class="fas fa-money-bill-wave"></i> Total Amount
                    </label>
                    <input type="number" id="total_amount" name="total_amount" step="0.01" value="{{ old('total_amount', 0) }}" required placeholder="0.00" class="form-input {{ $errors->has('total_amount') ? 'is-invalid' : '' }}" />
                    @error('total_amount')
                        <small style="color: var(--danger-text);">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="paid_amount">
                        <i class="fas fa-check-circle"></i> Paid Amount
                    </label>
                    <input type="number" id="paid_amount" name="paid_amount" step="0.01" value="{{ old('paid_amount', 0) }}" required placeholder="0.00" class="form-input {{ $errors->has('paid_amount') ? 'is-invalid' : '' }}" />
                    @error('paid_amount')
                        <small style="color: var(--danger-text);">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="grid grid-2 gap-4">
                <div class="form-group">
                    <label for="discount">
                        <i class="fas fa-percent"></i> Discount
                    </label>
                    <input type="number" id="discount" name="discount" step="0.01" value="{{ old('discount', 0) }}" placeholder="0.00" class="form-input" />
                </div>
                <div class="form-group">
                    <label for="fine">
                        <i class="fas fa-exclamation-triangle"></i> Fine/Penalty
                    </label>
                    <input type="number" id="fine" name="fine" step="0.01" value="{{ old('fine', 0) }}" placeholder="0.00" class="form-input" />
                </div>
            </div>

            <div class="grid grid-2 gap-4">
                <div class="form-group">
                    <label for="payment_date">
                        <i class="fas fa-calendar"></i> Payment Date
                    </label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="form-input {{ $errors->has('payment_date') ? 'is-invalid' : '' }}" />
                    @error('payment_date')
                        <small style="color: var(--danger-text);">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="payment_method">
                        <i class="fas fa-credit-card"></i> Payment Method
                    </label>
                    <input type="text" id="payment_method" name="payment_method" value="{{ old('payment_method') }}" placeholder="Cash, Card, Bank, Online" class="form-input" />
                </div>
            </div>

            <div class="form-group">
                <label for="status">
                    <i class="fas fa-info-circle"></i> Invoice Status
                </label>
                <select id="status" name="status" required class="form-input {{ $errors->has('status') ? 'is-invalid' : '' }}">
                    <option value="">-- Select Status --</option>
                    <option value="Paid" {{ old('status') === 'Paid' ? 'selected' : '' }}>✓ Paid</option>
                    <option value="Partial" {{ old('status') === 'Partial' ? 'selected' : '' }}>◐ Partially Paid</option>
                    <option value="Unpaid" {{ old('status') === 'Unpaid' ? 'selected' : '' }}>✗ Unpaid</option>
                </select>
                @error('status')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="remarks">
                    <i class="fas fa-sticky-note"></i> Remarks (Optional)
                </label>
                <textarea id="remarks" name="remarks" placeholder="Add any notes about this invoice..." class="form-input" style="min-height: 100px; resize: vertical;">{{ old('remarks') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="button button-primary" style="flex: 1;">
                    <i class="fas fa-file-invoice"></i> Generate Invoice
                </button>
                <a href="{{ route('fee_invoices.index') }}" class="button button-secondary" style="flex: 1; text-align: center;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
