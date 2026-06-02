@extends('layouts.app')

@section('title', 'Student Expenses')
@section('page-title', 'Student Expense Summary')

@section('content')
<div class="expense-container">
    <div class="toolbar mb-4 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Monthly Expense Totals</h3>
        <button id="theme-toggle" class="theme-toggle button button-secondary">
            <i class="fas fa-adjust"></i> Toggle Theme
        </button>
    </div>
    <div class="card premium-form-card table-card">
        <table class="table premium-table table-hover align-middle mb-0">
            <thead>
                <tr class="table-light-head">
                    <th class="ps-4">Month</th>
                    <th class="text-end pe-4">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyExpenses as $expense)
                <tr>
                    <td class="ps-4">{{ $expense->month }}</td>
                    <td class="text-end pe-4">₹{{ number_format($expense->total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center py-5 text-muted">No expense records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('theme-toggle').addEventListener('click', function() {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = current === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });
    // Persist theme on page load
    const saved = localStorage.getItem('theme');
    if (saved) {
        document.documentElement.setAttribute('data-theme', saved);
    }
</script>
@endsection
