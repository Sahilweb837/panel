@extends('layouts.app')

@section('title', 'Expenses')

@section('page-title', 'Office Expenses')

@section('content')
    <div class="toolbar">
        <form class="filter-form" method="GET" action="{{ route('expenses.index') }}">
            <input type="text" name="search" placeholder="Category or description" value="{{ request('search') }}" />
            <input type="date" name="date" value="{{ request('date') }}" />
            <button type="submit" class="button button-secondary">Filter</button>
        </form>
        <a href="{{ route('expenses.create') }}" class="button button-primary">Add Expense</a>
    </div>

    <div class="card table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->category }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->expense_date }}</td>
                        <td>{{ $expense->description }}</td>
                        <td class="action-cell">
                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete expense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger small">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No expenses recorded.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">{{ $expenses->links() }}</div>
    </div>
@endsection
