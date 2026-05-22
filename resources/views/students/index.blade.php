@extends('layouts.app')

@section('title', 'Students')

@section('page-title', 'Student Management')

@section('content')
    <div class="toolbar">
        <form class="filter-form" method="GET" action="{{ route('students.index') }}">
            <input type="text" name="search" placeholder="Search students" value="{{ request('search') }}" />
            <select name="course_id">
                <option value="">All courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ (string) request('course_id') === (string) $course->id ? 'selected' : '' }}>
                        {{ $course->name }}
                    </option>
                @endforeach
            </select>
            <select name="course_duration">
                <option value="">All durations</option>
                @foreach($durations as $duration)
                    <option value="{{ $duration }}" {{ request('course_duration') === $duration ? 'selected' : '' }}>{{ $duration }}</option>
                @endforeach
            </select>
            <select name="status">
                <option value="">All status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="button button-secondary">Filter</button>
        </form>
        <a href="{{ route('students.create') }}" class="button button-primary">Add Student</a>
    </div>

    <div>
        <div class="student-card-grid">
            @forelse($students as $student)
                <article class="student-card">
                    <div class="student-card-top">
                        <div class="student-avatar">{{ strtoupper(substr($student->first_name, 0, 1)) }}</div>
                        <div>
                            <h3>{{ $student->first_name }} {{ $student->last_name }}</h3>
                            <p>{{ $student->admission_no }}{{ $student->roll_no ? ' / '.$student->roll_no : '' }}</p>
                        </div>
                        <span class="status-pill {{ $student->status ? 'active' : 'inactive' }}">{{ $student->status ? 'Active' : 'Inactive' }}</span>
                    </div>

                    <dl class="student-card-details">
                        <div>
                            <dt>Course</dt>
                            <dd>{{ optional($student->course)->name ?? $student->class ?? 'Not assigned' }}</dd>
                        </div>
                        <div>
                            <dt>Type</dt>
                            <dd>
                                @if(($student->student_type ?? 'Regular (On Campus)') === 'Online')
                                    <span class="badge bg-info-light text-info px-2 py-1" style="background-color: rgba(23, 162, 184, 0.1); font-size: 0.75rem; font-weight: 600; border-radius: 4px;">Online</span>
                                @elseif(($student->student_type ?? 'Regular (On Campus)') === 'Regular (Internship)')
                                    <span class="badge bg-purple-light text-purple px-2 py-1" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1 !important; font-size: 0.75rem; font-weight: 600; border-radius: 4px;">Regular (Internship)</span>
                                @else
                                    <span class="badge bg-warning-light text-warning px-2 py-1" style="background-color: rgba(255, 85, 50, 0.1); color: var(--first-color) !important; font-size: 0.75rem; font-weight: 600; border-radius: 4px;">Regular (On Campus)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt>Duration</dt>
                            <dd>{{ $student->course_duration ?? $student->section ?? 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt>Phone</dt>
                            <dd>{{ $student->phone ?? 'Not added' }}</dd>
                        </div>
                        <div>
                            <dt>Aadhar No</dt>
                            <dd>{{ $student->aadhar_number ? implode(' ', str_split($student->aadhar_number, 4)) : 'Not added' }}</dd>
                        </div>
                        <div>
                            <dt>Admission Date</dt>
                            <dd>{{ $student->admission_date ?? 'Not added' }}</dd>
                        </div>
                    </dl>

                    <div class="student-card-actions">
                        <a href="{{ route('students.edit', $student) }}" class="button button-secondary small">Edit</a>
                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline-form" onsubmit="return confirm('Delete student?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button button-danger small">Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="card">No students found.</div>
            @endforelse
        </div>
        <div class="pagination-wrapper">{{ $students->links() }}</div>
    </div>
@endsection
