@extends('layouts.app')

@section('title', 'Add Expense')

@section('page-title', 'Add Expense')

@section('content')
    <div class="card form-card">
        <form action="{{ route('expenses.store') }}" method="POST">
            @csrf

            <div class="grid grid-2 gap-4">
                <label>Category<input type="text" name="category" value="{{ old('category') }}" /></label>
                <label>Amount<input type="number" name="amount" step="0.01" value="{{ old('amount', 0) }}" required /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Expense Date<input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required /></label>
            </div>

            <label>Description<textarea name="description">{{ old('description') }}</textarea></label>

            <button type="submit" class="button button-primary">Record Expense</button>
        </form>
    </div>
@endsection
