@extends('layouts.print')

@section('title', 'Students List')
@section('report-title', 'Students List')

@section('content')
    @if(request()->anyFilled(['search', 'status', 'course_id', 'course_duration']))
        <div class="filter-summary">
            @if(request()->filled('search'))
                <div class="filter-item"><strong>Search Query:</strong> "{{ request('search') }}"</div>
            @endif
            @if(request()->filled('status'))
                <div class="filter-item"><strong>Status:</strong> {{ ucfirst(request('status')) }}</div>
            @endif
            @if(request()->filled('course_duration'))
                <div class="filter-item"><strong>Duration:</strong> {{ request('course_duration') }}</div>
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Admission No</th>
                <th style="width: 80px;">Roll No</th>
                <th>Full Name</th>
                <th>Email</th>
                <th style="width: 100px;">Phone</th>
                <th>Course</th>
                <th style="width: 100px;">Student Type</th>
                <th style="width: 60px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->admission_no }}</td>
                    <td>{{ $student->roll_no ?? '-' }}</td>
                    <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                    <td>{{ $student->email ?? '-' }}</td>
                    <td>{{ $student->phone ?? '-' }}</td>
                    <td>{{ $student->course?->name ?? '-' }}</td>
                    <td>{{ $student->student_type }}</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $student->status ? 'badge-success' : 'badge-danger' }}">
                            {{ $student->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No students found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
