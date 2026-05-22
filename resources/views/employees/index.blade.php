@extends('layouts.app')

@section('title', 'Staff')

@section('page-title', 'Staff Management')

@section('content')
    <div class="toolbar">
        <form class="filter-form" method="GET" action="{{ route('employees.index') }}">
            <input type="text" name="search" placeholder="Search staff" value="{{ request('search') }}" />
            <button type="submit" class="button button-secondary">Filter</button>
        </form>
        <a href="{{ route('employees.create') }}" class="button button-primary">Add Staff</a>
    </div>

    <div class="card table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Phone</th>
                    <th>Salary</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->employee_code }}</td>
                        <td>{{ $employee->user?->name ?? 'No login' }}</td>
                        <td>{{ $employee->department }}</td>
                        <td>{{ $employee->designation }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ number_format($employee->salary, 2) }}</td>
                        <td>{{ $employee->status ? 'Active' : 'Inactive' }}</td>
                        <td class="action-cell">
                            <a href="{{ route('employees.edit', $employee) }}" class="button button-secondary small">Edit</a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete staff record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger small">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">No staff records found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">{{ $employees->links() }}</div>
    </div>
@endsection
