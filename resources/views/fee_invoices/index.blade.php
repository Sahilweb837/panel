@extends('layouts.app')

@section('title', 'Fee Invoices')

@section('page-title', 'Fee Invoice Management')

@section('content')
    <div class="toolbar">
        <form class="filter-form" method="GET" action="{{ route('fee_invoices.index') }}">
            <input type="text" name="search" placeholder="Invoice or student" value="{{ request('search') }}" />
            <select name="status">
                <option value="">All status</option>
                <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Partial" {{ request('status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
            </select>
            <button type="submit" class="button button-secondary">Filter</button>
        </form>
        <a href="{{ route('fee_invoices.create') }}" class="button button-primary">Create Invoice</a>
    </div>

    <div class="card table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Student</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Due</th>
                    <th>Payment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_no }}</td>
                        <td>{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</td>
                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                        <td>{{ $invoice->status }}</td>
                        <td>{{ number_format($invoice->due_amount, 2) }}</td>
                        <td>{{ $invoice->payment_date }}</td>
                        <td class="action-cell">
                            <form action="{{ route('fee_invoices.destroy', $invoice) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete invoice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger small">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No fee invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">{{ $invoices->links() }}</div>
    </div>
@endsection
