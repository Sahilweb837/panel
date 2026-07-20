@extends('layouts.app')

@section('title', 'Staff Attendance')
@section('page-title', 'Staff Attendance Management')

@section('content')
    <div class="staff-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 100px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 100px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 100px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 100px;"></div></div>
            </div>
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
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <!-- Dashboard Stats Grid -->
            <div class="row g-4 mb-4">
                <!-- Card 1: Total Employees -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small uppercase-bold d-block mb-1">Total Staff</span>
                                <h3 class="mb-0 font-weight-bold text-dark-title display-6 fw-bold">{{ $totalEmployees }}</h3>
                                <small class="text-success small fw-semibold"><i class="fas fa-check-double me-1"></i>Active employees</small>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-users fa-2x text-first"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Present Today -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small uppercase-bold d-block mb-1">Present Today</span>
                                <h3 class="mb-0 font-weight-bold text-success display-6 fw-bold">{{ $presentToday }}</h3>
                                <small class="text-success small fw-semibold"><i class="fas fa-check-circle me-1"></i>On-duty staff</small>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-user-check fa-2x text-success" style="color: #10b981 !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Absent Today -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small uppercase-bold d-block mb-1">Absent Today</span>
                                <h3 class="mb-0 font-weight-bold text-danger display-6 fw-bold">{{ $absentToday }}</h3>
                                <small class="text-danger small fw-semibold"><i class="fas fa-circle-exclamation me-1"></i>Not logged</small>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-user-xmark fa-2x text-danger" style="color: #ef4444 !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Leave Today -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small uppercase-bold d-block mb-1">On Leave</span>
                                <h3 class="mb-0 font-weight-bold text-warning display-6 fw-bold">{{ $leaveToday }}</h3>
                                <small class="text-warning small fw-semibold"><i class="fas fa-business-time me-1"></i>Approved leave</small>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-calendar-minus fa-2x text-warning" style="color: #f59e0b !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Action Bar -->
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1" method="GET" action="{{ route('employee-attendances.index') }}">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="employee" placeholder="Search by staff name or employee code..." value="{{ request('employee') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 200px;">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-calendar text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('employee') || request('date'))
                        <a href="{{ route('employee-attendances.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <div class="d-flex gap-2">
                    <a href="{{ route('employee-attendances.export.csv', request()->all()) }}" class="button button-secondary py-2 px-3" title="Export as CSV">
                        <i class="fas fa-file-csv text-first"></i>
                    </a>
                    <a href="{{ route('employee-attendances.create') }}" class="button button-primary py-2 px-4">
                        <i class="fas fa-clipboard-user me-2"></i>Record Attendance
                    </a>
                </div>
            </div>

            <!-- Attendance Data Table -->
            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-check text-first"></i> Staff Attendance Log
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table premium-table table-hover align-middle mb-0">
                            <thead>
                                <tr class="table-light-head">
                                    <th class="ps-4"><i class="fas fa-user me-1"></i> Staff Member</th>
                                    <th><i class="fas fa-barcode me-1"></i> Code</th>
                                    <th><i class="fas fa-building me-1"></i> Designation</th>
                                    <th><i class="fas fa-calendar-day me-1"></i> Date</th>
                                    <th class="text-center"><i class="fas fa-toggle-on me-1"></i> Status</th>
                                    <th class="text-center"><i class="fas fa-clock me-1"></i> Check-In / Out</th>
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
                                                        <img src="{{ asset($attendance->photo_path) }}" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;" />
                                                    @else
                                                        {{ strtoupper(substr($attendance->employee?->user?->name ?? 'S', 0, 1)) }}
                                                    @endif
                                                </div>
                                                 <div>
                                                     <div class="d-flex align-items-center gap-2">
                                                         <strong class="text-dark-title">{{ $attendance->employee?->user?->name ?? 'N/A' }}</strong>
                                                         @php
                                                             $isOnline = $attendance->employee?->user?->last_seen_at && \Carbon\Carbon::parse($attendance->employee->user->last_seen_at)->gt(\Carbon\Carbon::now()->subMinutes(3));
                                                         @endphp
                                                         @if($isOnline)
                                                             <span class="badge bg-success rounded-pill px-2 py-0.5" style="font-size:0.65rem;"><i class="fas fa-circle me-1" style="font-size:0.45rem;"></i>Online</span>
                                                         @else
                                                             <span class="badge bg-secondary rounded-pill px-2 py-0.5" style="font-size:0.65rem;">Offline</span>
                                                         @endif
                                                     </div>
                                                     <p class="text-muted small mb-0">{{ $attendance->employee?->user?->email }}</p>
                                                 </div>
                                             </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border p-2" style="font-size: 0.8rem; font-weight: 600;">{{ $attendance->employee?->employee_code }}</span></td>
                                        <td>
                                            <div class="fw-bold text-dark-title" style="font-size: 0.9rem;">{{ $attendance->employee?->department ?? 'N/A' }}</div>
                                            <div class="text-muted small">{{ $attendance->employee?->designation ?? 'N/A' }}</div>
                                        </td>
                                        <td class="text-muted">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ strtolower($attendance->status) }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                                {{ $attendance->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                             <div class="d-flex flex-column align-items-center gap-1 justify-content-center">
                                                 @if($attendance->check_in_time)
                                                     <span class="badge bg-light text-success border px-2 py-1.5" style="font-size: 0.8rem; font-weight: 600; min-width: 95px;">
                                                         <i class="fas fa-sign-in-alt me-1 text-success"></i>{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                                                     </span>
                                                 @endif
                                                 @if($attendance->latitude && $attendance->longitude)
                                                     <a href="https://maps.google.com/?q={{ $attendance->latitude }},{{ $attendance->longitude }}" target="_blank" class="badge bg-light text-primary border text-decoration-none p-1 px-2" style="font-size:0.72rem;" title="View GPS Location on Google Maps">
                                                         <i class="fas fa-map-marker-alt text-danger me-1"></i>GPS Map
                                                     </a>
                                                 @endif
                                                 @if($attendance->check_out_time)
                                                     <span class="badge bg-light text-danger border px-2 py-1.5" style="font-size: 0.8rem; font-weight: 600; min-width: 95px;">
                                                         <i class="fas fa-sign-out-alt me-1 text-danger"></i>{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}
                                                     </span>
                                                 @else
                                                     <span class="text-muted small" style="font-size: 0.75rem;">No Out Punch</span>
                                                 @endif
                                             </div>
                                        </td>
                                        <td class="text-muted" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $attendance->remarks ?: '-' }}
                                        </td>
                                        <td class="text-end pe-4 action-cell">
                                            <form action="{{ route('employee-attendances.destroy', $attendance->id) }}" method="POST" class="inline-form d-inline" onsubmit="return confirmAction(event, 'Are you sure you want to delete this staff attendance record?');">
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
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-clock fa-2x mb-3 d-block text-muted"></i>
                                            No staff attendance records logged matching criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pagination-wrapper mt-4 p-3 border-top">
                    {{ $attendances->links() }}
                </div>
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
