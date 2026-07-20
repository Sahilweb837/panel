@extends('layouts.app')

@section('title', 'Tasks Management')
@section('page-title', 'Task Board')

@section('content')
    <div class="tasks-container">
        @if(session('user_role_slug') !== 'staff')
            {{-- Task Analytics Stat Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card premium-stat-card p-3 d-flex flex-row align-items-center gap-3">
                        <div style="width:48px; height:48px; border-radius:12px; background:rgba(59,130,246,0.1); color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted);">Total Tasks</div>
                            <div style="font-size:1.4rem; font-weight:800;" class="text-dark-title">{{ $totalTasksCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card premium-stat-card p-3 d-flex flex-row align-items-center gap-3">
                        <div style="width:48px; height:48px; border-radius:12px; background:rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#10b981;">Completed</div>
                            <div style="font-size:1.4rem; font-weight:800; color:#10b981;">{{ $completedTasksCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card premium-stat-card p-3 d-flex flex-row align-items-center gap-3">
                        <div style="width:48px; height:48px; border-radius:12px; background:rgba(245,158,11,0.1); color:#f59e0b; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                            <i class="fas fa-spinner"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:#f59e0b;">In Progress</div>
                            <div style="font-size:1.4rem; font-weight:800; color:#f59e0b;">{{ $inProgressTasksCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card premium-stat-card p-3 d-flex flex-row align-items-center gap-3">
                        <div style="width:48px; height:48px; border-radius:12px; background:rgba(239,68,68,0.1); color:#ef4444; display:flex; align-items:center; justify-content:center; font-size:1.25rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted);">Completion Rate</div>
                            <div style="font-size:1.4rem; font-weight:800; color:var(--first-color);">{{ $completionRate ?? 0 }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Staff Task Performance Report --}}
            @if(isset($employeeTaskReports) && count($employeeTaskReports) > 0)
            <div class="card premium-stat-card p-0 mb-4 overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark-title"><i class="fas fa-users-cog text-first me-2"></i>Staff Task Performance Report</h6>
                    <small class="text-muted">Analytics by Staff Member</small>
                </div>
                <div class="table-responsive">
                    <table class="table premium-table align-middle mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4">Staff Member</th>
                                <th>Total Assigned</th>
                                <th>Completed</th>
                                <th>In Progress</th>
                                <th>Pending</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employeeTaskReports as $rep)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark-title">
                                        {{ $rep['employee']->user?->name ?? 'Staff #'.$rep['employee']->id }}
                                        <small class="text-muted ms-1">({{ $rep['employee']->employee_code }})</small>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $rep['assigned'] }}</span></td>
                                    <td><span class="badge bg-success">{{ $rep['done'] }}</span></td>
                                    <td><span class="badge bg-warning text-dark">{{ $rep['in_progress'] }}</span></td>
                                    <td><span class="badge bg-danger">{{ $rep['pending'] }}</span></td>
                                    <td style="width: 220px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="fee-bar-bg flex-grow-1" style="height: 6px; background: var(--border);">
                                                <div class="fee-bar-fill" style="width: {{ $rep['rate'] }}%; background: var(--first-color); height: 100%; border-radius: 4px;"></div>
                                            </div>
                                            <strong style="font-size: 0.8rem;">{{ $rep['rate'] }}%</strong>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @endif
        <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            @if(session('user_role_slug') !== 'staff')
                <form method="GET" action="{{ route('tasks.index') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1">
                    <select name="status" class="form-input" style="max-width: 180px;">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    <select name="priority" class="form-input" style="max-width: 180px;">
                        <option value="">All Priorities</option>
                        <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                        <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                        <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High</option>
                    </select>
                    <button type="submit" class="button button-secondary py-2 px-4">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('status') || request('priority'))
                        <a href="{{ route('tasks.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('tasks.create') }}" class="button button-primary py-2 px-4">
                    <i class="fas fa-plus me-2"></i>Assign Task
                </a>
            @else
                <div class="flex-grow-1">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list text-first me-2"></i>My Assigned Work Tasks</h5>
                </div>
            @endif
        </div>

        <div class="card premium-stat-card p-0 table-card overflow-hidden">
            <div class="card-body p-0">
                <table class="table premium-table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-light-head">
                            <th class="ps-4">Task Title</th>
                            <th>Description</th>
                            @if(session('user_role_slug') !== 'staff')
                                <th>Assigned To</th>
                            @endif
                            <th>Priority</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td class="ps-4 fw-bold text-dark-title">{{ $task->title }}</td>
                                <td class="text-muted small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $task->description ?? '-' }}
                                </td>
                                @if(session('user_role_slug') !== 'staff')
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="text-dark-title">{{ $task->employee?->user?->name ?? 'Unknown Staff' }}</strong>
                                            <span class="badge bg-light border text-dark">{{ $task->employee?->employee_code }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td>
                                    <span class="badge bg-{{ $task->priority === 'High' ? 'danger' : ($task->priority === 'Medium' ? 'warning' : 'info') }} px-2 py-1">
                                        {{ $task->priority }}
                                    </span>
                                </td>
                                <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                                <td>
                                    <span id="status-badge-{{ $task->id }}" class="status-badge status-{{ str_replace(' ', '-', strtolower($task->status)) }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                        {{ $task->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    @if(session('user_role_slug') === 'staff')
                                        <div class="d-flex gap-1 justify-content-end">
                                            <form action="{{ route('tasks.update_status', $task->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="In Progress">
                                                <button type="submit" class="button button-secondary btn-sm py-1 px-2 text-warning border-warning" style="font-size: 0.75rem;" {{ $task->status === 'In Progress' ? 'disabled' : '' }}>
                                                    <i class="fas fa-play me-1"></i>Start
                                                </button>
                                            </form>
                                            <form action="{{ route('tasks.update_status', $task->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="Completed">
                                                <button type="submit" class="button button-secondary btn-sm py-1 px-2 text-success border-success" style="font-size: 0.75rem;" {{ $task->status === 'Completed' ? 'disabled' : '' }}>
                                                    <i class="fas fa-check me-1"></i>Complete
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirmAction(event, 'Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger small py-1.5 px-3">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-tasks fa-2x mb-3 d-block"></i>
                                    No tasks registered.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(session('user_role_slug') !== 'staff')
            <div class="pagination-wrapper mt-4">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
@endsection
