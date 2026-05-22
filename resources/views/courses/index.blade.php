@extends('layouts.app')

@section('title', 'Courses')

@section('page-title', 'Course Management')

@section('content')
    <div class="toolbar">
        <form class="filter-form" method="GET" action="{{ route('courses.index') }}">
            <input type="text" name="search" placeholder="Search courses" value="{{ request('search') }}" />
            <button type="submit" class="button button-secondary">Filter</button>
        </form>
        <a href="{{ route('courses.create') }}" class="button button-primary">Add Course</a>
    </div>

    <div class="course-card-grid">
        @forelse($courses as $course)
            <article class="course-card">
                <div>
                    <p class="course-code">{{ $course->code ?? 'COURSE' }}</p>
                    <h3>{{ $course->name }}</h3>
                    <p>{{ $course->duration ?? 'Flexible duration' }}</p>
                </div>
                <dl>
                    <div>
                        <dt>Fee</dt>
                        <dd>Rs. {{ number_format($course->fee, 2) }}</dd>
                    </div>
                    <div>
                        <dt>Students</dt>
                        <dd>{{ $course->students_count }}</dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd>{{ $course->status ? 'Active' : 'Inactive' }}</dd>
                    </div>
                </dl>
                <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete course? Existing students will keep their records.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button-danger small">Delete</button>
                </form>
            </article>
        @empty
            <div class="card">No courses found. Create a course first, then assign it to student cards.</div>
        @endforelse
    </div>

    <div class="pagination-wrapper">{{ $courses->links() }}</div>
@endsection
