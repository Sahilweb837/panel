@extends('layouts.app')

@section('title', 'Student Portal Dashboard')
@section('page-title', 'Student Portal')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100 rounded-4">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    @if($student->photo)
                        <img src="{{ Storage::url($student->photo) }}" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow" style="width: 120px; height: 120px;">
                            <i class="fas fa-user-graduate text-muted fa-3x"></i>
                        </div>
                    @endif
                </div>
                <h4 class="fw-bold mb-1">{{ $student->first_name }} {{ $student->last_name }}</h4>
                <p class="text-muted mb-3">{{ $student->course->name ?? 'No Course Assigned' }}</p>
                <div class="d-flex justify-content-center gap-2">
                    <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="fas fa-id-card me-1"></i> {{ $student->admission_no }}</span>
                    <span class="badge bg-info px-3 py-2 rounded-pill"><i class="fas fa-layer-group me-1"></i> {{ $student->class }}</span>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                <a href="{{ route('student.attendance.capture') }}" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-camera fa-lg"></i> Mark Attendance Now
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-3">
                        <i class="fas fa-calendar-check fa-2x mb-2 opacity-75"></i>
                        <h3 class="fw-bold mb-0">{{ $presentDays }}</h3>
                        <span class="small">Present</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-danger text-white border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-3">
                        <i class="fas fa-calendar-times fa-2x mb-2 opacity-75"></i>
                        <h3 class="fw-bold mb-0">{{ $absentDays }}</h3>
                        <span class="small">Absent</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-warning text-white border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-3">
                        <i class="fas fa-clock fa-2x mb-2 opacity-75"></i>
                        <h3 class="fw-bold mb-0">{{ $lateDays }}</h3>
                        <span class="small">Late</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-success text-white border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body text-center p-3">
                        <i class="fas fa-chart-pie fa-2x mb-2 opacity-75"></i>
                        <h3 class="fw-bold mb-0">{{ $attendancePercentage }}%</h3>
                        <span class="small">Overall</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-list text-primary me-2"></i>Recent Attendance</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Device</th>
                                <th>Photo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td class="fw-medium">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $attendance->status === 'Present' ? 'success' : ($attendance->status === 'Absent' ? 'danger' : 'warning') }} rounded-pill">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted small"><i class="fas fa-laptop me-1"></i>{{ $attendance->device_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($attendance->photo_path)
                                            <a href="{{ Storage::url($attendance->photo_path) }}" target="_blank">
                                                <img src="{{ Storage::url($attendance->photo_path) }}" width="40" height="40" class="rounded shadow-sm" style="object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="text-muted small">No photo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No attendance records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
