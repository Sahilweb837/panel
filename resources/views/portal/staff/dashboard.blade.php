@extends('layouts.app')

@section('title', 'Staff Portal Dashboard')
@section('page-title', 'Staff Portal')

@section('content')
<style>
    :root {
        --bg-primary: #121212;
        --surface-card: #1E1E1E;
        --accent-primary: #00E5FF;
        --accent-alert: #FF453A;
        --accent-status: #32D74B;
        --text-primary: #FFFFFF;
        --text-secondary: #98989D;
        --border-sutil: #2C2C2E;
    }

    body {
        background-color: var(--bg-primary) !important;
        color: var(--text-primary) !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .card {
        background-color: var(--surface-card) !important;
        border: 1px solid var(--border-sutil) !important;
        border-radius: 16px !important;
        color: var(--text-primary) !important;
    }

    .card-header, .card-footer {
        border-color: var(--border-sutil) !important;
        background-color: var(--surface-card) !important;
        color: var(--text-primary) !important;
    }

    .text-muted {
        color: var(--text-secondary) !important;
    }

    .text-dark {
        color: var(--text-primary) !important;
    }

    .fw-bold, .fw-black, .fw-semibold, .badge, td, .fs-5, h3, h4 {
        font-family: 'JetBrains Mono', 'Fira Code', monospace;
    }

    .bg-light {
        background-color: var(--surface-card) !important;
        border-color: var(--border-sutil) !important;
    }

    .table {
        color: var(--text-primary) !important;
    }

    .table-light, .table-light-head, .table th, .table-responsive thead th {
        background-color: var(--surface-card) !important;
        color: var(--text-secondary) !important;
        border-bottom: 1px solid var(--border-sutil) !important;
    }

    .table tbody tr {
        border-bottom: 1px solid var(--border-sutil) !important;
    }

    .table tbody tr:hover {
        background-color: #252525 !important;
    }

    .alert-info {
        background-color: rgba(0, 229, 255, 0.08) !important;
        color: var(--accent-primary) !important;
        border: 1px solid rgba(0, 229, 255, 0.2) !important;
    }

    .btn-primary {
        background-color: var(--accent-primary) !important;
        color: #121212 !important;
        border: none !important;
    }

    .btn-primary:hover {
        background-color: #00b8cc !important;
    }

    .btn-outline-warning {
        color: #f59e0b !important;
        border-color: #f59e0b !important;
    }

    .btn-outline-warning:hover {
        background-color: #f59e0b !important;
        color: #121212 !important;
    }

    .btn-outline-success {
        color: var(--accent-status) !important;
        border-color: var(--accent-status) !important;
    }

    .btn-outline-success:hover {
        background-color: var(--accent-status) !important;
        color: #121212 !important;
    }

    .badge.bg-success {
        background-color: rgba(50, 215, 75, 0.15) !important;
        color: var(--accent-status) !important;
    }

    .badge.bg-danger {
        background-color: rgba(255, 69, 58, 0.15) !important;
        color: var(--accent-alert) !important;
    }

    .badge.bg-warning {
        background-color: rgba(255, 159, 11, 0.15) !important;
        color: #f59e0b !important;
    }

    .badge.bg-info {
        background-color: rgba(0, 229, 255, 0.15) !important;
        color: var(--accent-primary) !important;
    }

    .badge.bg-primary {
        background-color: rgba(0, 229, 255, 0.15) !important;
        color: var(--accent-primary) !important;
    }
</style>

<div class="container-fluid px-0">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 p-3 d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-check-circle fa-lg"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 p-3 d-flex align-items-center gap-2" role="alert">
            <i class="fas fa-exclamation-circle fa-lg"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 h-100 rounded-4">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        @if($employee->user && $employee->user->profile_pic && $employee->user->profile_pic !== 'default.png')
                            <img src="{{ Storage::url('profiles/' . $employee->user->profile_pic) }}" class="rounded-circle shadow" width="120" height="120" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto shadow" style="width: 120px; height: 120px;">
                                <i class="fas fa-user-tie text-muted fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1">{{ $employee->user->name ?? $employee->employee_code }}</h4>
                    <p class="text-muted mb-3">{{ $employee->designation ?? 'Staff Member' }}</p>
                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary px-3 py-2 rounded-pill"><i class="fas fa-id-badge me-1"></i> {{ $employee->employee_code }}</span>
                        <span class="badge bg-info px-3 py-2 rounded-pill"><i class="fas fa-building me-1"></i> {{ $employee->department ?? 'General' }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 p-4 pt-0">
                    <a href="{{ route('staff.attendance.capture') }}" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-camera fa-lg"></i> Mark Attendance
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                    <div class="card bg-primary text-white border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-calendar-check fa-2x mb-2 opacity-75"></i>
                            <h3 class="fw-bold mb-0">{{ $presentDays }}</h3>
                            <span class="small">Present</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-danger text-white border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-calendar-times fa-2x mb-2 opacity-75"></i>
                            <h3 class="fw-bold mb-0">{{ $absentDays }}</h3>
                            <span class="small">Absent</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card bg-warning text-white border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-clock fa-2x mb-2 opacity-75"></i>
                            <h3 class="fw-bold mb-0">{{ $lateDays }}</h3>
                            <span class="small">Late</span>
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

    <!-- Tasks & Daily Work Updates Grid -->
    <div class="row g-4 mt-2">
        <!-- My Assigned Tasks -->
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-tasks text-primary me-2"></i>My Assigned Tasks</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Task</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedTasks as $task)
                                    <tr id="task-row-{{ $task->id }}">
                                        <td>
                                            <div class="fw-bold text-dark">{{ $task->title }}</div>
                                            <div class="text-muted small">{{ $task->description ?? 'No details' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->priority === 'High' ? 'danger' : ($task->priority === 'Medium' ? 'warning' : 'info') }} px-2 py-1">
                                                {{ $task->priority }}
                                            </span>
                                        </td>
                                        <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                                        <td>
                                            <span id="task-status-{{ $task->id }}" class="status-badge status-{{ str_replace(' ', '-', strtolower($task->status)) }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                                {{ $task->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <button type="button" onclick="updateTaskStatus({{ $task->id }}, 'In Progress')" class="btn btn-outline-warning btn-sm py-1 px-2 btn-start-task" style="font-size: 0.75rem;" {{ $task->status === 'In Progress' || $task->status === 'Completed' ? 'disabled' : '' }}>
                                                    <i class="fas fa-play me-1"></i>Start
                                                </button>
                                                <button type="button" onclick="updateTaskStatus({{ $task->id }}, 'Completed')" class="btn btn-outline-success btn-sm py-1 px-2 btn-complete-task" style="font-size: 0.75rem;" {{ $task->status === 'Completed' ? 'disabled' : '' }}>
                                                    <i class="fas fa-check me-1"></i>Complete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-check-circle fa-2x mb-3 d-block text-success"></i>
                                            No pending tasks assigned to you.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Daily Update -->
        <div class="col-12 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-edit text-primary me-2"></i>Log Daily Work Update</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('daily-updates.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="update_text" class="fw-semibold mb-2 text-muted small">Describe the work you completed today:</label>
                            <textarea name="update_text" id="update_text" required minlength="10" placeholder="Describe the tasks done, issues resolved, or status of your work today..." class="form-control" style="min-height: 120px; resize: vertical; padding: 12px;">{{ old('update_text', $todayUpdate?->update_text) }}</textarea>
                        </div>
                        @if($todayUpdate)
                            <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-info-circle me-1"></i> You already logged a work update today. Submitting again will update today's log.
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold">
                            <i class="fas fa-save me-2"></i> {{ $todayUpdate ? 'Update Daily Log' : 'Submit Daily Log' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Slips Grid -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>My Salary Slips</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Month/Year</th>
                                    <th>Basic Salary</th>
                                    <th>Allowances</th>
                                    <th>Deductions</th>
                                    <th>Net Pay</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salarySlips ?? [] as $slip)
                                    <tr>
                                        <td class="fw-medium">{{ $slip->month }} {{ $slip->year }}</td>
                                        <td>₹{{ number_format($slip->basic_salary, 2) }}</td>
                                        <td class="text-success">+₹{{ number_format($slip->allowances, 2) }}</td>
                                        <td class="text-danger">-₹{{ number_format($slip->deductions, 2) }}</td>
                                        <td class="fw-bold text-primary">₹{{ number_format($slip->net_pay, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $slip->status === 'Paid' ? 'success' : 'warning' }} rounded-pill">
                                                {{ $slip->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($slip->status === 'Paid')
                                                <a href="{{ route('salary_slips.show', $slip) }}" class="btn btn-sm btn-outline-primary py-1 px-2" target="_blank">
                                                    <i class="fas fa-print me-1"></i>View Slip
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No salary slips found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function updateTaskStatus(taskId, status) {
        fetch(`/tasks/${taskId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const badge = document.getElementById(`task-status-${taskId}`);
                badge.innerText = status;
                badge.className = `status-badge status-${status.toLowerCase().replace(' ', '-')}`;

                const row = document.getElementById(`task-row-${taskId}`);
                const startBtn = row.querySelector('.btn-start-task');
                const completeBtn = row.querySelector('.btn-complete-task');

                if(status === 'In Progress') {
                    startBtn.disabled = true;
                    completeBtn.disabled = false;
                } else if(status === 'Completed') {
                    startBtn.disabled = true;
                    completeBtn.disabled = true;
                }

                Swal.fire({
                    title: 'Task Updated!',
                    text: `Task status set to "${status}".`,
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    background: document.documentElement.dataset.theme === 'dark' ? '#1e1714' : '#ffffff',
                    color: document.documentElement.dataset.theme === 'dark' ? '#f5eae4' : '#1c1816'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.error || 'Failed to update status',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({
                title: 'Error',
                text: 'An error occurred while updating the task status.',
                icon: 'error'
            });
        });
    }
</script>
@endsection