@extends('layouts.app')

@section('title', 'Attendance')

@section('page-title', 'Attendance Management')

@section('content')
    <div class="toolbar">
        <form class="filter-form" method="GET" action="{{ route('attendances.index') }}">
            <input type="text" name="student" placeholder="Student search" value="{{ request('student') }}" />
            <input type="date" name="date" value="{{ request('date') }}" />
            <button type="submit" class="button button-secondary">Filter</button>
        </form>
        <a href="{{ route('attendances.create') }}" class="button button-primary">Record Attendance</a>
    </div>

    <div class="card table-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Fine</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</td>
                        <td>{{ $attendance->attendance_date }}</td>
                        <td>{{ $attendance->status }}</td>
                        <td>{{ number_format($attendance->fine, 2) }}</td>
                        <td>{{ $attendance->remarks }}</td>
                        <td class="action-cell">
                            <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete attendance record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger small">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No attendance records found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination-wrapper">{{ $attendances->links() }}</div>
    </div>
@endsection
