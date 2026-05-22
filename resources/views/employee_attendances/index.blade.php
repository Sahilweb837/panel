@extends('layouts.app')

@section('title', 'Staff Attendance')

@section('page-title', 'Staff Attendance Management')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Total Employees -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-4 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted uppercase-bold">Total Staff</span>
                        <h3 class="mb-0 mt-1 font-weight-bold text-dark-title">{{ $totalEmployees }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-light-orange text-first">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Present Today -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-4 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted uppercase-bold">Present Today</span>
                        <h3 class="mb-0 mt-1 font-weight-bold text-success">{{ $presentToday }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-success-light text-success" style="width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background-color: rgba(40, 167, 69, 0.1);">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Absent Today -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-4 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted uppercase-bold">Absent Today</span>
                        <h3 class="mb-0 mt-1 font-weight-bold text-danger">{{ $absentToday }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-danger-light text-danger" style="width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background-color: rgba(220, 53, 69, 0.1);">
                        <i class="fas fa-times-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Leave Today -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card p-4 border-0 shadow-sm h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted uppercase-bold">On Leave</span>
                        <h3 class="mb-0 mt-1 font-weight-bold text-warning">{{ $leaveToday }}</h3>
                    </div>
                    <div class="stat-icon-wrapper bg-warning-light text-warning" style="width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; background-color: rgba(255, 193, 7, 0.1);">
                        <i class="fas fa-plane fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Action Bar -->
    <div class="toolbar">
        <form class="filter-form d-flex gap-2 flex-wrap flex-grow-1" method="GET" action="{{ route('employee-attendances.index') }}">
            <input type="text" name="employee" placeholder="Search Staff Name or Code" value="{{ request('employee') }}" class="form-input flex-grow-1" style="min-width: 200px;" />
            <input type="date" name="date" value="{{ request('date') }}" class="form-input" />
            <button type="submit" class="button button-secondary">Filter</button>
            @if(request()->anyFilled(['employee', 'date']))
                <a href="{{ route('employee-attendances.index') }}" class="button btn-light d-flex align-items-center justify-content-center">Reset</a>
            @endif
        </form>
        <a href="{{ route('employee-attendances.create') }}" class="button button-primary"><i class="fas fa-clipboard-user me-2"></i>Record Daily Attendance</a>
    </div>

    <!-- Attendance Data Table -->
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light-head">
                    <tr>
                        <th class="ps-4">Staff Member</th>
                        <th>Code</th>
                        <th>Department & Designation</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Arrival Time</th>
                        <th>Remarks</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle-sm bg-light-orange text-first me-3" style="width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; background-color: var(--first-color-light);">
                                        {{ strtoupper(substr($attendance->employee->user->name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark-title">{{ $attendance->employee->user->name ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ $attendance->employee->user->email ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $attendance->employee->employee_code }}</span></td>
                            <td>
                                <div style="font-weight: 600; color: var(--dark-title);">{{ $attendance->employee->department ?? 'N/A' }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;">{{ $attendance->employee->designation ?? 'N/A' }}</div>
                            </td>
                            <td><strong>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</strong></td>
                            <td>
                                @if($attendance->status === 'Present')
                                    <span class="badge bg-success-light text-success px-3 py-2" style="background-color: rgba(40, 167, 69, 0.1); border-radius: 30px;">Present</span>
                                @elseif($attendance->status === 'Absent')
                                    <span class="badge bg-danger-light text-danger px-3 py-2" style="background-color: rgba(220, 53, 69, 0.1); border-radius: 30px;">Absent</span>
                                @elseif($attendance->status === 'Late')
                                    <span class="badge bg-warning-light text-warning px-3 py-2" style="background-color: rgba(255, 193, 7, 0.1); border-radius: 30px;">Late</span>
                                @else
                                    <span class="badge bg-info-light text-info px-3 py-2" style="background-color: rgba(23, 162, 184, 0.1); border-radius: 30px;">Leave</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_in_time)
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-weight: 700; font-size: 0.8rem; letter-spacing: 0.5px;">
                                        <i class="far fa-clock me-1" style="color: var(--first-color);"></i>{{ $attendance->check_in_time }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted"><small>{{ $attendance->remarks ?? '-' }}</small></span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('employee-attendances.destroy', $attendance->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Remove this staff attendance record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button button-danger small py-1 px-3" style="border-radius: 6px;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-user fa-3x mb-3 text-light"></i>
                                <p class="mb-0">No staff attendance records found for this selection.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper p-3 border-top">{{ $attendances->links() }}</div>
    </div>
@endsection
