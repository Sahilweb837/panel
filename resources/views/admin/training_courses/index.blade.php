@extends('layouts.app')

@section('title', 'Training Courses')
@section('page-title', 'Training Course Management')

@section('content')
    <div class="courses-container">
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

        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1" method="GET" action="{{ route('training_courses.index') }}">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by course name or code..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; flex: 1;">
                        <select name="duration" class="form-input" style="padding-left: 36px;">
                            <option value="">All Durations</option>
                            @foreach($durations as $duration)
                                <option value="{{ $duration }}" {{ request('duration') == $duration ? 'selected' : '' }}>{{ $duration }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-clock text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <select name="status" class="form-input" style="width: 140px;">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('duration') || request('status'))
                        <a href="{{ route('training_courses.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                @if(in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin', 'admin']))
                    <div class="d-flex gap-2 align-items-center">
                        <div class="form-check form-switch me-3 d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0" type="checkbox" role="switch" id="toggleTrash"
                                   {{ request('trashed') ? 'checked' : '' }}
                                   onchange="window.location.href='{{ request()->fullUrlWithQuery(['trashed' => request('trashed') ? null : '1']) }}'">
                            <label class="form-check-label fw-bold text-dark-title" for="toggleTrash" style="cursor: pointer; margin-top: 2px;">
                                Show Recycle Bin
                            </label>
                        </div>
                        <a href="{{ route('training_courses.create') }}" class="button button-primary py-2 px-4">
                            <i class="fas fa-plus me-2"></i>Add Training Course
                        </a>
                    </div>
                @endif
            </div>

            <div class="row g-4">
                @forelse($trainingCourses as $course)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card premium-stat-card h-100 d-flex flex-column justify-content-between p-4">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="course-code text-uppercase-bold" style="letter-spacing: 0.05em; font-weight: 800; font-size: 0.8rem; color: var(--first-color);">
                                        {{ $course->short_code ?? 'TC' }}
                                    </span>
                                    <span class="status-pill {{ $course->status ? 'active' : 'inactive' }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                                        {{ $course->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <h3 class="fw-bold mb-1 text-dark-title" style="font-size: 1.25rem; {{ request('trashed') ? 'text-decoration: line-through; color: #dc3545;' : '' }}">{{ $course->name }}</h3>
                                <p class="text-muted small mb-2"><i class="fas fa-clock me-1"></i>Duration: {{ $course->duration }}</p>
                                <p class="text-muted small mb-3" style="font-size: 0.72rem; line-height: 1.4;">{{ $course->description ?? 'No description provided.' }}</p>
                            </div>

                            <div>
                                <div class="border-top pt-3 mt-2">
                                    <span class="text-muted d-block small" style="font-size: 0.75rem;">COURSE FEE</span>
                                    <strong class="text-dark-title" style="font-size: 1.1rem;">₹{{ number_format($course->fee, 2) }}</strong>

                                    <div class="row g-2 mt-2 text-center" style="font-size: 0.7rem;">
                                        <div class="col-3">
                                            <span class="text-muted d-block">1 M</span>
                                            <strong>₹{{ number_format($course->tenure_1_month, 2) }}</strong>
                                        </div>
                                        <div class="col-3">
                                            <span class="text-muted d-block">3 M</span>
                                            <strong>₹{{ number_format($course->tenure_3_months, 2) }}</strong>
                                        </div>
                                        <div class="col-3">
                                            <span class="text-muted d-block">6 M</span>
                                            <strong>₹{{ number_format($course->tenure_6_months, 2) }}</strong>
                                        </div>
                                        <div class="col-3">
                                            <span class="text-muted d-block">12 M</span>
                                            <strong>₹{{ number_format($course->tenure_12_months, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                @if(in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin', 'admin']))
                                    <div class="d-flex align-items-center gap-2 w-100 mt-3">
                                        @if($course->trashed())
                                            <form action="{{ route('training_courses.restore', $course->id) }}" method="POST" onsubmit="return confirmAction(event, 'Restore this training course?');" class="flex-grow-1">
                                                @csrf
                                                <button type="submit" class="button button-success small py-2 w-100" style="font-size: 0.8rem; font-weight: 700;">
                                                    <i class="fas fa-trash-restore me-1"></i>Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('training_courses.edit', $course) }}" class="button button-secondary small py-2 text-center flex-grow-1" style="font-size: 0.8rem; font-weight: 700;">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('training_courses.destroy', $course) }}" method="POST" onsubmit="return confirmAction(event, 'Delete this training course?');" class="flex-grow-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button button-danger small py-2 w-100" style="font-size: 0.8rem; font-weight: 700;">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card premium-stat-card p-5 text-center text-muted">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3 d-block"></i>
                            No training courses defined yet. Click "Add Training Course" above to create one.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $trainingCourses->links() }}
            </div>
        </div>
    </div>

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