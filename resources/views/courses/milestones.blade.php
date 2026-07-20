@extends('layouts.app')

@section('title', 'Course Syllabus & Daily Milestones')
@section('page-title', 'Course Syllabus Coverage - ' . $course->name)

@section('content')
<style>
    .milestone-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    .progress-bar-custom {
        height: 12px;
        border-radius: 10px;
        background: var(--border-sutil, #e2e8f0);
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 10px;
        transition: width 0.4s ease;
    }
</style>

<div class="container-fluid px-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Top Header Summary Card --}}
    <div class="milestone-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <span class="badge bg-light text-primary border mb-1 px-3 py-1 fw-bold">{{ $course->code ?? 'COURSE' }}</span>
                <h3 class="fw-bold text-dark-title mb-1">{{ $course->name }}</h3>
                <p class="text-muted small mb-0"><i class="fas fa-clock me-1"></i>Duration: {{ $course->duration ?? 'Standard' }} | Total Fee: ₹{{ number_format($course->fee, 2) }}</p>
            </div>
            <div class="text-end">
                <a href="{{ route('courses.index') }}" class="button button-secondary py-2 px-3">
                    <i class="fas fa-arrow-left me-1"></i>Back to Courses
                </a>
            </div>
        </div>

        <div class="row align-items-center g-3 pt-3 border-top">
            <div class="col-12 col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-dark-title small">Syllabus Completion Rate</span>
                    <strong class="text-success">{{ $progressPercentage }}% Completed</strong>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: {{ $progressPercentage }}%;"></div>
                </div>
            </div>
            <div class="col-12 col-md-4 text-md-end">
                <span class="badge bg-success-subtle text-success border border-success p-2 px-3 rounded-3" style="font-size: 0.9rem;">
                    <i class="fas fa-check-double me-1"></i>{{ $completedCount }} / {{ $totalCount }} Topics Covered
                </span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Add New Topic Form --}}
        <div class="col-12 col-lg-4">
            <div class="milestone-card p-4">
                <h5 class="fw-bold text-dark-title mb-3"><i class="fas fa-plus-circle text-primary me-2"></i>Add Syllabus Milestone Topic</h5>
                <form action="{{ route('courses.milestones.store', $course->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Topic / Milestone Title</label>
                        <input type="text" name="milestone_title" class="form-input" placeholder="e.g. Module 1: HTML5 Forms & Validations" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Day / Order Index</label>
                        <input type="number" name="order_index" class="form-input" placeholder="e.g. 1, 2, 3..." value="{{ $totalCount + 1 }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Topic Description / Sub-topics (Optional)</label>
                        <textarea name="description" rows="4" class="form-input" placeholder="List key sub-topics covered in this session..."></textarea>
                    </div>

                    <button type="submit" class="button button-primary w-100 py-2">
                        <i class="fas fa-save me-2"></i>Add Milestone Topic
                    </button>
                </form>
            </div>
        </div>

        {{-- Syllabus Checklist & Daily Coverage Table --}}
        <div class="col-12 col-lg-8">
            <div class="milestone-card p-0 overflow-hidden">
                <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark-title mb-0"><i class="fas fa-list-check me-2 text-primary"></i>Daily Syllabus Coverage Checklist</h6>
                    <small class="text-muted">Click checkbox to mark daily covered topic</small>
                </div>

                <div class="table-responsive">
                    <table class="table premium-table align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4" style="width: 60px;">Covered</th>
                                <th style="width: 70px;"># Order</th>
                                <th>Milestone Topic</th>
                                <th>Covered By / Timestamp</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($milestones as $m)
                                <tr id="milestone-row-{{ $m->id }}" class="{{ $m->is_completed ? 'table-success-subtle' : '' }}">
                                    <td class="ps-4">
                                        <input type="checkbox" class="form-check-input toggle-milestone-check"
                                               data-id="{{ $m->id }}"
                                               style="width: 22px; height: 22px; cursor: pointer;"
                                               {{ $m->is_completed ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $m->order_index }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-dark-title d-block {{ $m->is_completed ? 'text-decoration-line-through text-muted' : '' }}">
                                            {{ $m->milestone_title }}
                                        </strong>
                                        @if($m->description)
                                            <small class="text-muted d-block mt-1">{{ $m->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($m->is_completed)
                                            <span class="badge bg-success-subtle text-success border border-success p-1 px-2 mb-1 d-inline-block" style="font-size: 0.72rem;">
                                                <i class="fas fa-user-check me-1"></i>{{ $m->covered_by ?? 'Trainer' }}
                                            </span>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                                {{ $m->completed_at ? $m->completed_at->format('M d, Y h:i A') : '' }}
                                            </small>
                                        @else
                                            <span class="badge bg-secondary-subtle text-muted border p-1 px-2" style="font-size: 0.72rem;">Pending Coverage</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <form action="{{ route('courses.milestones.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this milestone topic?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger btn-sm py-1 px-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-book-open fa-2x mb-2 d-block"></i>No syllabus milestone topics added for this course yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-milestone-check').forEach(chk => {
            chk.addEventListener('change', function() {
                const id = this.dataset.id;
                fetch(`/courses/milestones/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(err => console.error(err));
            });
        });
    });
</script>
@endsection
