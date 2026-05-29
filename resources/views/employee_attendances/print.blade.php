@extends('layouts.print')

@section('title', 'Staff Attendance Logs')
@section('report-title', 'Staff Attendance Logs Report')

@section('content')
    <div class="filter-summary">
        @if(request()->filled('employee'))
            <div class="filter-item"><strong>Employee:</strong> "{{ request('employee') }}"</div>
        @endif
        @if(request()->filled('date'))
            <div class="filter-item"><strong>Date:</strong> {{ \Carbon\Carbon::parse(request('date'))->format('M d, Y') }}</div>
        @else
            <div class="filter-item"><strong>Date:</strong> All Dates</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Employee Code</th>
                <th>Staff Name</th>
                <th style="width: 100px;">Attendance Date</th>
                <th style="width: 80px; text-align: center;">Status</th>
                <th style="width: 100px; text-align: center;">Check-in Time</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->employee?->employee_code ?? '-' }}</td>
                    <td><strong>{{ $attendance->employee?->user?->name ?? '-' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $attendance->status === 'Present' ? 'badge-success' : ($attendance->status === 'Absent' ? 'badge-danger' : 'badge-warning') }}">
                            {{ $attendance->status }}
                        </span>
                    </td>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">
                        {{ $attendance->check_in_time ?? '-' }}
                    </td>
                    <td>{{ $attendance->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No attendance records found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
