@extends('layouts.app')

@section('title', 'Expenses')
@section('page-title', 'Office Expenses')

@section('content')
    <div class="expense-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 250px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 130px; height: 42px;"></div>
            </div>
            <div class="card premium-form-card" style="max-width: 100%;">
                <div class="sk-text heading"></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1" method="GET" action="{{ route('expenses.index') }}">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by category or description..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 200px;">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-calendar text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('date'))
                        <a href="{{ route('expenses.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('expenses.create') }}" class="button button-primary py-2 px-4">
                    <i class="fas fa-plus me-2"></i>Add Expense
                </a>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-money-bill-wave text-first"></i> Office Expenditure Registry
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-tags me-1"></i> Category</th>
                                <th><i class="fas fa-indian-rupee-sign me-1"></i> Amount</th>
                                <th><i class="fas fa-calendar-day me-1"></i> Date</th>
                                <th><i class="fas fa-file-lines me-1"></i> Description</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td class="fw-bold ps-4">
                                        <span class="badge" style="background: rgba(255, 85, 50, 0.1); color: var(--first-color); padding: 6px 12px; border-radius: 6px;">
                                            {{ $expense->category }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark-title">₹{{ number_format($expense->amount, 2) }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}</td>
                                    <td class="text-muted" style="max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $expense->description ?: '-' }}
                                    </td>
                                    <td class="text-end pe-4 action-cell">
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-form d-inline" onsubmit="return confirm('Are you sure you want to delete this expense record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger small py-1.5 px-3">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-face-meh fa-2x mb-3 d-block text-muted"></i>
                                        No expenses recorded matching the criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $expenses->links() }}
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
