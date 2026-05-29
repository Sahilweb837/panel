@extends('layouts.app')

@section('title', 'Add Expense')
@section('page-title', 'Add Expense')

@section('content')
    <div class="expense-container">
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

        <!-- Real content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="card premium-form-card">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-wallet me-2"></i>Record Corporate Expense
                    </h3>
                </div>

                <form action="{{ route('expenses.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    <div class="form-group-grid">
                        <div class="form-group">
                            <label for="category" class="fw-semibold mb-2">
                                <i class="fas fa-tags text-first me-2"></i>Expense Category
                            </label>
                            <select id="category" name="category" required class="form-input {{ $errors->has('category') ? 'is-invalid' : '' }}">
                                <option value="">-- Select Category --</option>
                                <option value="Daily Office Expense" {{ old('category') === 'Daily Office Expense' ? 'selected' : '' }}>Daily Office Expense</option>
                                <option value="Office Supplies" {{ old('category') === 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                                <option value="Utilities & Internet" {{ old('category') === 'Utilities & Internet' ? 'selected' : '' }}>Utilities & Internet</option>
                                <option value="Office Rent / Leasing" {{ old('category') === 'Office Rent / Leasing' ? 'selected' : '' }}>Office Rent / Leasing</option>
                                <option value="Salaries & Wages" {{ old('category') === 'Salaries & Wages' ? 'selected' : '' }}>Salaries & Wages</option>
                                <option value="Marketing & Advertising" {{ old('category') === 'Marketing & Advertising' ? 'selected' : '' }}>Marketing & Advertising</option>
                                <option value="Repairs & Maintenance" {{ old('category') === 'Repairs & Maintenance' ? 'selected' : '' }}>Repairs & Maintenance</option>
                                <option value="Office Events & Catering" {{ old('category') === 'Office Events & Catering' ? 'selected' : '' }}>Office Events & Catering</option>
                                <option value="Travel Expenses" {{ old('category') === 'Travel Expenses' ? 'selected' : '' }}>Travel Expenses</option>
                                <option value="Miscellaneous" {{ old('category') === 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                            </select>
                            @error('category')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount" class="fw-semibold mb-2">
                                <i class="fas fa-indian-rupee-sign text-first me-2"></i>Amount (INR)
                            </label>
                            <input 
                                type="number" 
                                id="amount" 
                                name="amount" 
                                step="0.01" 
                                value="{{ old('amount', '') }}" 
                                required 
                                placeholder="0.00" 
                                class="form-input {{ $errors->has('amount') ? 'is-invalid' : '' }}" 
                            />
                            @error('amount')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mt-3">
                        <div class="form-group">
                            <label for="expense_date" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-days text-first me-2"></i>Expense Date
                            </label>
                            <input 
                                type="date" 
                                id="expense_date" 
                                name="expense_date" 
                                value="{{ old('expense_date', date('Y-m-d')) }}" 
                                required 
                                class="form-input {{ $errors->has('expense_date') ? 'is-invalid' : '' }}" 
                            />
                            @error('expense_date')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="description" class="fw-semibold mb-2">
                            <i class="fas fa-align-left text-first me-2"></i>Description / Remarks
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            placeholder="Enter transaction details, receipt references or office items bought..." 
                            class="form-input" 
                            style="min-height: 120px; resize: vertical;"
                        >{{ old('description') }}</textarea>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('expenses.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-save me-2"></i>Record Expense
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
