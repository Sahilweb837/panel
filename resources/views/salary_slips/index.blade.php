@extends('layouts.app')

@section('title', 'Salary Slips')

@section('page-title', 'Salary Slip Management')

@section('content')
    <div class="toolbar">
        <a href="{{ route('salary_slips.create') }}" class="button button-primary">Generate Salary Slip</a>
    </div>

    <div class="card table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Month</th>
                    <th>Net Pay</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salarySlips as $slip)
                    <tr>
                        <td>{{ $slip->employee->employee_code }}</td>
                        <td>{{ $slip->month }} {{ $slip->year }}</td>
                        <td>{{ number_format($slip->net_pay, 2) }}</td>
                        <td>{{ $slip->status }}</td>
                        <td>{{ $slip->payment_date ?? 'Not paid' }}</td>
                        <td class="action-cell">
                            <form action="{{ route('salary_slips.destroy', $slip) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete salary slip?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger small">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No salary slips generated.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">{{ $salarySlips->links() }}</div>
    </div>
@endsection
