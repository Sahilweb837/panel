@extends('layouts.app')

@section('title', 'Student Attendance')
@section('page-title', 'Student Attendance')

@section('content')
    <div class="attendance-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 250px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 130px; height: 42px;"></div>
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
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1" method="GET" action="{{ route('attendances.index') }}">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="student" placeholder="Search by student name or admission number..." value="{{ request('student') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 200px;">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-calendar text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('student') || request('date'))
                        <a href="{{ route('attendances.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <div class="d-flex gap-2">
                    <a href="{{ route('attendances.export.csv', request()->all()) }}" class="button button-secondary py-2 px-3" title="Export as CSV">
                        <i class="fas fa-file-csv text-first"></i>
                    </a>
                    <a href="{{ route('attendances.create') }}" class="button button-primary py-2 px-4">
                        <i class="fas fa-clipboard-user me-2"></i>Record Attendance
                    </a>
                </div>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-check text-first"></i> Student Attendance Log
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-user me-1"></i> Student</th>
                                <th><i class="fas fa-calendar-day me-1"></i> Date</th>
                                <th class="text-center"><i class="fas fa-toggle-on me-1"></i> Status</th>
                                <th class="text-center"><i class="fas fa-arrow-right-to-bracket me-1"></i> Check-in</th>
                                <th class="text-center"><i class="fas fa-arrow-right-from-bracket me-1"></i> Check-out</th>
                                <th class="text-end"><i class="fas fa-sack-dollar me-1"></i> Fine</th>
                                <th><i class="fas fa-comment-dots me-1"></i> Remarks</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td class="ps-4">
                                        <div class="user-info">
                                            <div class="avatar" style="font-weight: 700; background: rgba(255, 85, 50, 0.1); color: var(--first-color); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                @if($attendance->photo_path)
                                                    <img src="{{ Storage::url($attendance->photo_path) }}" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;" />
                                                @else
                                                    {{ strtoupper(substr($attendance->student?->first_name ?? 'S', 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <strong class="text-dark-title">{{ $attendance->student?->first_name }} {{ $attendance->student?->last_name }}</strong>
                                                <p class="text-muted small">Adm No: {{ $attendance->student?->admission_no }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                                    <td class="text-center">
                                        <span class="status-badge status-{{ strtolower($attendance->status) }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->check_in_time)
                                            <span class="badge bg-light text-dark border p-2" style="font-size: 0.8rem; font-weight: 600;">
                                                <i class="far fa-clock text-success me-1"></i>{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($attendance->check_out_time)
                                            <span class="badge bg-light text-dark border p-2" style="font-size: 0.8rem; font-weight: 600;">
                                                <i class="far fa-clock text-danger me-1"></i>{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-dark-title">₹{{ number_format($attendance->fine, 2) }}</td>
                                    <td class="text-muted" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $attendance->remarks ?: '-' }}
                                    </td>
                                    <td class="text-end pe-4 action-cell">
                                        <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" class="inline-form d-inline" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger small py-1.5 px-3">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-3 d-block text-muted"></i>
                                        No student attendance records logged matching criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $attendances->links() }}
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
