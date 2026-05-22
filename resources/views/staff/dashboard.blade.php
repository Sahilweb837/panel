@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('page-title', 'Staff Dashboard')

@section('content')
    <div class="grid grid-4 gap-4 stats-grid">
        <div class="card stat-card">
            <p>Employee ID</p>
            <strong>{{ $employee?->employee_code ?? 'Not linked' }}</strong>
        </div>
        <div class="card stat-card">
            <p>Designation</p>
            <strong>{{ $employee?->designation ?? 'Not set' }}</strong>
        </div>
        <div class="card stat-card">
            <p>Students</p>
            <strong>{{ $studentCount }}</strong>
        </div>
        <div class="card stat-card">
            <p>Attendance Entries</p>
            <strong>{{ $attendanceCount }}</strong>
        </div>
    </div>

    <div class="grid grid-2 gap-4 mt-4">
        <div class="card">
            <h3>My Staff Profile</h3>
            <dl class="profile-list">
                <div><dt>Department</dt><dd>{{ $employee?->department ?? 'Not set' }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $employee?->phone ?? 'Not added' }}</dd></div>
                <div><dt>Joining Date</dt><dd>{{ $employee?->joining_date ?? 'Not added' }}</dd></div>
                <div><dt>Status</dt><dd>{{ $employee?->status ? 'Active' : 'Inactive' }}</dd></div>
            </dl>
        </div>

        <div class="card table-card">
            <h3>My Salary Slips</h3>
            <table class="table">
                <thead>
                    <tr><th>Month</th><th>Year</th><th>Net Pay</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($salarySlips as $slip)
                        <tr>
                            <td>{{ $slip->month }}</td>
                            <td>{{ $slip->year }}</td>
                            <td>{{ number_format($slip->net_pay, 2) }}</td>
                            <td>{{ $slip->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No salary slips assigned yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
