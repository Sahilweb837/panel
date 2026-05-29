@extends('layouts.print')

@section('title', 'Student Attendance Logs')
@section('report-title', 'Student Attendance Logs Report')

@section('content')
    <div class="filter-summary">
        @if(request()->filled('student'))
            <div class="filter-item"><strong>Student:</strong> "{{ request('student') }}"</div>
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
                <th style="width: 100px;">Admission No</th>
                <th>Student Name</th>
                <th>Course</th>
                <th style="width: 100px;">Attendance Date</th>
                <th style="width: 80px; text-align: center;">Status</th>
                <th style="width: 100px; text-align: center;">Check-in Time</th>
                <th style="width: 80px; text-align: right;">Fine</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->student?->admission_no ?? '-' }}</td>
                    <td><strong>{{ ($attendance->student?->first_name ?? '') . ' ' . ($attendance->student?->last_name ?? '') }}</strong></td>
                    <td>{{ $attendance->student?->course?->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $attendance->status === 'Present' ? 'badge-success' : ($attendance->status === 'Absent' ? 'badge-danger' : 'badge-warning') }}">
                            {{ $attendance->status }}
                        </span>
                    </td>
                    <td style="text-align: center; font-weight: 600; color: var(--primary);">
                        {{ $attendance->check_in_time ?? '-' }}
                    </td>
                    <td style="text-align: right;">₹{{ number_format($attendance->fine, 2) }}</td>
                    <td>{{ $attendance->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No attendance records found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
