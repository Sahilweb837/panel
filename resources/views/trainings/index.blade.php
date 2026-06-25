@extends('layouts.app')

@section('title', 'Training & Internship')
@section('page-title', 'Training & Internship Registrations')

@section('content')
    <div class="invoice-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 280px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 180px; height: 42px;"></div>
            </div>
            <div class="card premium-form-card" style="max-width: 100%;">
                <div class="sk-text heading"></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form method="GET" action="{{ route('trainings.index') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by slip no, name, email or mobile..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 180px;">
                        <select name="course_id" class="form-input" style="padding-left: 36px;">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-book text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 160px;">
                        <select name="duration" class="form-input" style="padding-left: 36px;">
                            <option value="">All Durations</option>
                            @foreach($durations as $duration)
                                <option value="{{ $duration }}" {{ request('duration') == $duration ? 'selected' : '' }}>{{ $duration }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-clock text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('course_id') || request('duration'))
                        <a href="{{ route('trainings.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <div class="d-flex gap-2 align-items-center">
                    <div class="form-check form-switch me-3 d-flex align-items-center gap-2">
                        <input class="form-check-input mt-0" type="checkbox" role="switch" id="toggleTrash"
                               {{ request('trashed') ? 'checked' : '' }}
                               onchange="window.location.href='{{ request()->fullUrlWithQuery(['trashed' => request('trashed') ? null : '1']) }}'">
                        <label class="form-check-label fw-bold text-dark-title" for="toggleTrash" style="cursor: pointer; margin-top: 2px;">
                            Show Recycle Bin
                        </label>
                    </div>
                    <a href="{{ route('trainings.export.csv') }}" class="button button-secondary py-2 px-4">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </a>
                    <a href="{{ route('trainings.analytics') }}" class="button button-secondary py-2 px-4">
                        <i class="fas fa-chart-bar me-2"></i>Analytics
                    </a>
                    <a href="{{ route('trainings.create') }}" class="button button-primary py-2 px-4">
                        <i class="fas fa-plus me-2"></i>New Training Slip
                    </a>
                </div>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-graduation-cap text-first"></i> Training & Internship Registrations
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-hashtag me-1"></i> Slip No</th>
                                <th><i class="fas fa-user me-1"></i> Candidate</th>
                                <th><i class="fas fa-book me-1"></i> Course</th>
                                <th><i class="fas fa-clock me-1"></i> Duration</th>
                                <th><i class="fas fa-coins me-1"></i> Fees (INR)</th>
                                <th><i class="fas fa-calendar me-1"></i> Payment Date</th>
                                <th><i class="fas fa-toggle-on me-1"></i> Status</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainings as $training)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border p-2 fw-bold" style="font-size: 0.8rem; {{ $training->trashed() ? 'text-decoration: line-through; color: #dc3545 !important;' : '' }}">
                                            {{ $training->slip_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar" style="font-weight: 700; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">
                                                {{ strtoupper(substr($training->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark-title">{{ $training->name }}</strong>
                                                <p class="text-muted small">{{ $training->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $training->course->name ?? $training->course_name ?? 'N/A' }}</td>
                                    <td class="text-muted">{{ $training->duration }}</td>
                                    <td class="text-muted fw-bold">{{ number_format($training->fees, 2) }}</td>
                                    <td class="text-muted">{{ $training->payment_date ? \Carbon\Carbon::parse($training->payment_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @php
                                            $statusClass = strtolower($training->status);
                                        @endphp
                                        <span class="status-badge status-{{ $statusClass }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            <i class="fas fa-{{ $statusClass === 'paid' ? 'check-circle' : 'clock' }} me-1"></i>
                                            {{ $training->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 action-cell">
                                        @if($training->trashed())
                                            <form action="{{ route('trainings.restore', $training->id) }}" method="POST" class="inline-form d-inline" onsubmit="return confirmAction(event, 'Restore this training slip?');">
                                                @csrf
                                                <button type="submit" class="button button-success small py-1.5 px-3">
                                                    <i class="fas fa-trash-restore me-1"></i>Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('trainings.show', $training) }}" class="button button-secondary small py-1.5 px-3" target="_blank">
                                                <i class="fas fa-print me-1"></i>Print
                                            </a>
                                            <form action="{{ route('trainings.destroy', $training) }}" method="POST" class="inline-form d-inline" onsubmit="return confirmAction(event, 'Are you sure you want to delete this training slip?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="button button-danger small py-1.5 px-3">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-graduation-cap fa-2x mb-3 d-block text-muted"></i>
                                        No training registrations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $trainings->links() }}
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
