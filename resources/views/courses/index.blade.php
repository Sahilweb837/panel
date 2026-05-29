@extends('layouts.app')

@section('title', 'Courses')
@section('page-title', 'Course Management')

@section('content')
    <div class="courses-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 250px; height: 42px;"></div>
                <div class="sk-card" style="width: 130px; height: 42px;"></div>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-xl-4"><div class="sk-card" style="height: 220px;"></div></div>
                <div class="col-12 col-md-6 col-xl-4"><div class="sk-card" style="height: 220px;"></div></div>
                <div class="col-12 col-md-6 col-xl-4"><div class="sk-card" style="height: 220px;"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1" method="GET" action="{{ route('courses.index') }}">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by course name or code..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('courses.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('courses.create') }}" class="button button-primary py-2 px-4">
                    <i class="fas fa-plus me-2"></i>Add Course
                </a>
            </div>

            <div class="row g-4">
                @forelse($courses as $course)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card premium-stat-card h-100 d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="course-code text-uppercase-bold" style="letter-spacing: 0.05em; font-weight: 800; font-size: 0.8rem; color: var(--first-color);">
                                        {{ $course->code ?? 'COURSE' }}
                                    </span>
                                    <span class="status-pill {{ $course->status ? 'active' : 'inactive' }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                                        {{ $course->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <h3 class="fw-bold mb-2 text-dark-title" style="font-size: 1.25rem;">{{ $course->name }}</h3>
                                <p class="text-muted small mb-3"><i class="fas fa-clock me-1"></i>Duration: {{ $course->duration ?? 'Flexible duration' }}</p>
                            </div>

                            <div class="border-top pt-3 mt-3">
                                <div class="row g-2 text-center mb-3">
                                    <div class="col-6 border-end">
                                        <span class="text-muted d-block small" style="font-size: 0.75rem;">COURSE FEE</span>
                                        <strong class="text-dark-title" style="font-size: 1rem;">₹{{ number_format($course->fee, 2) }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted d-block small" style="font-size: 0.75rem;">STUDENTS</span>
                                        <strong class="text-dark-title" style="font-size: 1rem;">{{ $course->students_count }}</strong>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete this course? Registered students will preserve their current records.');" class="w-100">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-danger small w-100 py-2">
                                            <i class="fas fa-trash me-2"></i>Delete Course
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card premium-stat-card p-5 text-center text-muted">
                            <i class="fas fa-book-open fa-3x text-muted mb-3 d-block"></i>
                            No courses defined yet. Click "Add Course" above to create one.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and skeleton fading -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection
