@extends('layouts.print')

@section('title', 'Staff Directory')
@section('report-title', 'Staff Directory Report')

@section('content')
    @if(request()->filled('search'))
        <div class="filter-summary">
            <div class="filter-item"><strong>Search Query:</strong> "{{ request('search') }}"</div>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Employee Code</th>
                <th>Full Name</th>
                <th>Email</th>
                <th style="width: 100px;">Phone</th>
                <th>Department</th>
                <th>Designation</th>
                <th style="width: 100px; text-align: right;">Base Salary</th>
                <th style="width: 90px;">Joining Date</th>
                <th style="width: 60px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr>
                    <td>{{ $employee->employee_code }}</td>
                    <td><strong>{{ $employee->user?->name ?? '-' }}</strong></td>
                    <td>{{ $employee->user?->email ?? '-' }}</td>
                    <td>{{ $employee->phone ?? '-' }}</td>
                    <td>{{ $employee->department ?? '-' }}</td>
                    <td>{{ $employee->designation ?? '-' }}</td>
                    <td style="text-align: right;">₹{{ number_format($employee->salary, 2) }}</td>
                    <td>{{ $employee->joining_date ?? '-' }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $employee->status ? 'badge-success' : 'badge-danger' }}">
                            {{ $employee->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No employees found matching your search.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
