@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('page-title', 'Staff Dashboard')

@section('content')
    <!-- Dashboard Styling and Animations -->
    <style>
        .dashboard-container {
            position: relative;
            z-index: 1;
        }

        /* High-performance lazy-loading skeleton overlay */
        .skeleton-loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--main-bg);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 20px;
            pointer-events: none;
            opacity: 1;
            transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .skeleton-loader-overlay.fade-out {
            opacity: 0;
            display: none !important;
        }

        .sk-card {
            background: linear-gradient(90deg, var(--surface-soft) 25%, var(--border) 50%, var(--surface-soft) 75%);
            background-size: 200% 100%;
            animation: loadingSkeleton 1.5s infinite linear;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        @keyframes loadingSkeleton {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Circular progress ring widgets */
        .stat-circle-box {
            position: relative;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-circle-svg {
            transform: rotate(-90deg);
            width: 64px;
            height: 64px;
        }

        .stat-circle-bg {
            fill: none;
            stroke: var(--border);
            stroke-width: 6;
        }

        .stat-circle-bar {
            fill: none;
            stroke: var(--first-color);
            stroke-width: 6;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.6s ease;
        }

        /* Interactive stat cards */
        .premium-stat-card {
            border: 1px solid var(--border) !important;
            border-radius: 16px !important;
            background: var(--surface) !important;
            box-shadow: var(--shadow) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .premium-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--first-color);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .premium-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(255, 85, 50, 0.15) !important;
            border-color: var(--first-color) !important;
        }

        .premium-stat-card:hover::before {
            opacity: 1;
        }

        .premium-card-header {
            border-bottom: 1px solid var(--border) !important;
            padding: 20px 24px !important;
        }

        .progress-bar-glow {
            height: 6px;
            background-color: var(--border);
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill-first {
            height: 100%;
            border-radius: 9999px;
            background: linear-gradient(90deg, var(--first-color) 0%, #ff7c60 100%);
            box-shadow: 0 0 10px rgba(255, 85, 50, 0.3);
        }

        .premium-table th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.04em;
            color: var(--muted);
            background-color: var(--surface-soft);
            border-bottom: 2px solid var(--border) !important;
            padding: 14px 16px;
        }

        .premium-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
        }
    </style>

    <div class="dashboard-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="dashboard-skeleton">
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
                <div class="col-12 col-sm-6 col-xl-3"><div class="sk-card" style="height: 160px;"></div></div>
            </div>
            <div class="row g-4 mt-1">
                <div class="col-12 col-lg-6"><div class="sk-card" style="height: 380px;"></div></div>
                <div class="col-12 col-lg-6"><div class="sk-card" style="height: 380px;"></div></div>
            </div>
        </div>

        <!-- Staff Dashboard Real Content -->
        <div id="dashboard-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <!-- Stats Deck -->
            <div class="row g-4 mb-4">
                <!-- Employee ID Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Employee Code</span>
                                <h4 class="fw-bold mb-2 text-dark-title" style="font-size: 1.4rem;">{{ $employee?->employee_code ?? 'Not linked' }}</h4>
                                <span class="text-success small fw-semibold"><i class="fas fa-id-badge me-1"></i>Official ID</span>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-id-card fa-2x text-first"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Designation Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Designation</span>
                                <h4 class="fw-bold mb-2 text-dark-title" style="font-size: 1.4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">{{ $employee?->designation ?? 'Not set' }}</h4>
                                <span class="text-success small fw-semibold"><i class="fas fa-briefcase me-1"></i>Active Role</span>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-user-tie fa-2x text-success" style="color: #10b981 !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Total Students</span>
                                <h3 class="display-6 fw-bold mb-2 text-dark-title">{{ $studentCount }}</h3>
                                <span class="text-warning small fw-semibold"><i class="fas fa-graduation-cap me-1"></i>Enrolled</span>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-user-graduate fa-2x text-warning" style="color: #f59e0b !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Entries Stat -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small-text uppercase-bold d-block mb-1">Attendance Logged</span>
                                <h3 class="display-6 fw-bold mb-2 text-dark-title">{{ $attendanceCount }}</h3>
                                <span class="text-danger small fw-semibold"><i class="fas fa-check-double me-1"></i>Total recorded</span>
                            </div>
                            <div class="stat-circle-box">
                                <i class="fas fa-clipboard-check fa-2x text-danger" style="color: #ef4444 !important;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile & Salary Slips Grid -->
            <div class="row g-4 mb-4">
                <!-- My Profile -->
                <div class="col-12 col-lg-5">
                    <div class="card premium-stat-card h-100">
                        <div class="premium-card-header bg-transparent">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-user-circle text-first"></i> My Staff Profile
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <dl class="profile-list">
                                <div><dt>Department</dt><dd>{{ $employee?->department ?? 'Not set' }}</dd></div>
                                <div><dt>Phone</dt><dd>{{ $employee?->phone ?? 'Not added' }}</dd></div>
                                <div><dt>Joining Date</dt><dd>{{ $employee?->joining_date ?? 'Not added' }}</dd></div>
                                <div><dt>Status</dt><dd>{{ $employee?->status ? 'Active' : 'Inactive' }}</dd></div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- My Salary Slips -->
                <div class="col-12 col-lg-7">
                    <div class="card premium-stat-card h-100">
                        <div class="premium-card-header bg-transparent">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-wallet text-first"></i> My Recent Salary Slips
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover align-middle mb-0" style="min-width: auto;">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Month</th>
                                            <th>Year</th>
                                            <th>Net Pay</th>
                                            <th class="pe-4 text-end">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salarySlips as $slip)
                                            <tr>
                                                <td class="fw-bold ps-4 text-first">{{ $slip->month }}</td>
                                                <td>{{ $slip->year }}</td>
                                                <td class="fw-semibold">{{ number_format($slip->net_pay, 2) }}</td>
                                                <td class="pe-4 text-end">
                                                    <span class="status-badge status-{{ strtolower($slip->status) }}">
                                                        {{ $slip->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4 ps-4">No salary slips assigned yet.</td>
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
                    <div class="card premium-stat-card h-100">
                        <div class="premium-card-header bg-transparent">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-tasks text-first"></i> My Assigned Tasks
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table premium-table table-hover align-middle mb-0" style="min-width: auto;">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Task</th>
                                            <th>Priority</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th class="pe-4 text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($assignedTasks as $task)
                                            <tr id="task-row-{{ $task->id }}">
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark-title">{{ $task->title }}</div>
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
                                                <td class="pe-4 text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <button type="button" onclick="updateTaskStatus({{ $task->id }}, 'In Progress')" class="button button-secondary btn-sm py-1 px-2 text-warning border-warning btn-start-task" style="font-size: 0.75rem;" {{ $task->status === 'In Progress' || $task->status === 'Completed' ? 'disabled' : '' }}>
                                                            <i class="fas fa-play me-1"></i>Start
                                                        </button>
                                                        <button type="button" onclick="updateTaskStatus({{ $task->id }}, 'Completed')" class="button button-secondary btn-sm py-1 px-2 text-success border-success btn-complete-task" style="font-size: 0.75rem;" {{ $task->status === 'Completed' ? 'disabled' : '' }}>
                                                            <i class="fas fa-check me-1"></i>Complete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-5 ps-4">
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
                    <div class="card premium-stat-card h-100">
                        <div class="premium-card-header bg-transparent">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-edit text-first"></i> Log Daily Work Update
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('daily-updates.store') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="update_text" class="fw-semibold mb-2 text-muted small">Describe the work you completed today:</label>
                                    <textarea name="update_text" id="update_text" required minlength="10" placeholder="Describe the tasks done, issues resolved, or status of your work today..." class="form-input" style="min-height: 120px; resize: vertical; padding: 12px;">{{ old('update_text', $todayUpdate?->update_text) }}</textarea>
                                </div>
                                @if($todayUpdate)
                                    <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.85rem;">
                                        <i class="fas fa-info-circle me-1"></i> You already logged a work update today. Submitting again will update today's log.
                                    </div>
                                @endif
                                <button type="submit" class="button button-primary w-100 py-2.5">
                                    <i class="fas fa-save me-2"></i> {{ $todayUpdate ? 'Update Daily Log' : 'Submit Daily Log' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script to simulate lazy loading & handle AJAX status updates -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('dashboard-skeleton');
            const content = document.getElementById('dashboard-content');
            
            setTimeout(() => {
                skeleton.classList.add('fade-out');
                content.style.opacity = '1';
            }, 600);
        });

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
