@extends('layouts.app')

@section('title', 'Staff Portal Dashboard')
@section('page-title', 'Staff Portal')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100 rounded-4">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    @if($employee->user->profile_pic && $employee->user->profile_pic !== 'default.png')
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
