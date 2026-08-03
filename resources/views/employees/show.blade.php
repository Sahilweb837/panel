@extends('layouts.app')

@section('title', 'Staff Profile')
@section('page-title', 'Staff Profile - ' . ($employee->user?->name ?? $employee->employee_code))

@section('content')
<style>
    .profile-card {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    html[data-theme="dark"] .profile-card {
        background: rgba(31, 41, 55, 0.45);
        backdrop-filter: blur(12px);
    }
    .profile-header {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.05));
        padding: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
        border-bottom: 1px solid var(--border);
    }
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #10b981;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: bold;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }
    .profile-info h3 {
        margin: 0 0 5px 0;
        font-weight: 700;
        color: var(--dark-title);
    }
    .profile-info p {
        margin: 0;
        color: var(--muted);
    }
    .metric-card {
        background: var(--surface-soft);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s;
    }
    .metric-card:hover {
        transform: translateY(-3px);
    }
    .metric-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--dark-title);
    }
    .metric-label {
        font-size: 0.85rem;
        color: var(--muted);
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .history-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: var(--muted);
        background: var(--surface-soft);
    }
</style>

<div class="row">
    <div class="col-12">
        <!-- Main Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar overflow-hidden p-0" style="width: 100px; height: 100px; border-radius: 50%;">
                    @if($employee->user && $employee->user->profile_pic && $employee->user->profile_pic !== 'default.png')
                        <img src="{{ asset('uploads/profiles/' . $employee->user->profile_pic) }}" alt="Profile" class="w-full h-full object-cover rounded-circle">
                    @else
                        {{ strtoupper(substr($employee->user?->name ?? 'S', 0, 1)) }}
                    @endif
                </div>
                <div class="profile-info flex-grow-1">
                    <h3>{{ $employee->user?->name ?? 'Unknown' }}</h3>
                    <p>Employee Code: <strong>{{ $employee->employee_code }}</strong> | Department: <strong>{{ $employee->department ?? 'N/A' }}</strong></p>
                    <div class="mt-2">
                        <span class="badge bg-{{ $employee->status ? 'success' : 'danger' }}">{{ $employee->status ? 'Active' : 'Inactive' }}</span>
                        <span class="badge bg-primary">{{ $employee->designation ?? 'Staff' }}</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-outline-success">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
            
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <!-- Key Metrics -->
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-success">₹{{ number_format($employee->salary, 0) }}</div>
                            <div class="metric-label">Base Salary</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-primary">{{ $attendancePercentage }}%</div>
                            <div class="metric-label">Attendance Rate</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-warning">{{ $tasks->count() }}</div>
                            <div class="metric-label">Active Tasks</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="metric-card">
                            <div class="metric-value text-info">{{ $salarySlips->count() }}</div>
                            <div class="metric-label">Payslips Generated</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Contact Details -->
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Contact & Access</h5>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" width="130"><i class="fas fa-envelope me-2"></i>Email:</td>
                                <td class="fw-semibold">{{ $employee->user?->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-phone me-2"></i>Phone:</td>
                                <td class="fw-semibold">{{ $employee->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address:</td>
                                <td class="fw-semibold">{{ $employee->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-user-lock me-2"></i>Access Level:</td>
                                <td class="fw-semibold">
                                    @foreach($employee->user?->access ?? [] as $access)
                                        <span class="badge bg-secondary me-1">{{ ucfirst($access) }}</span>
                                    @endforeach
                                    @if(empty($employee->user?->access))
                                        <span class="text-muted">Basic Access</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- Work Details -->
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Employment Information</h5>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" width="130"><i class="fas fa-calendar-alt me-2"></i>Joined:</td>
                                <td class="fw-semibold">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-id-card me-2"></i>Biometric ID:</td>
                                <td class="fw-semibold">{{ $employee->biometric_id ?? 'Not Assigned' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-university me-2"></i>Bank Account:</td>
                                <td class="fw-semibold">{{ $employee->bank_account_no ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted"><i class="fas fa-file-signature me-2"></i>Bank IFSC:</td>
                                <td class="fw-semibold">{{ $employee->bank_ifsc ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($employee->user || in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']))
                <!-- Staff Portal Credentials Card -->
                <div class="card border border-success border-opacity-25 rounded-3 p-3 mt-4" style="background: rgba(16, 185, 129, 0.03);">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
                                <i class="fas fa-key"></i>
                            </div>
                            <h6 class="fw-bold mb-0" style="font-size: 1rem; color: var(--dark-title);">Staff Login Credentials</h6>
                        </div>
                        <span class="badge bg-success"><i class="fas fa-shield-check me-1"></i>Active Portal Access</span>
                    </div>

                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-4">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Login Username / ID</small>
                            <div class="fw-bold text-dark-title" style="font-size: 0.95rem;">{{ $employee->user?->username ?? $employee->employee_code }}</div>
                        </div>
                        <div class="col-12 col-md-4">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Current Password (Plain Text)</small>
                            <div class="d-flex align-items-center gap-2">
                                <span id="show-raw-pw" class="fw-bold font-monospace" style="color: #10b981; font-size: 1rem;">
                                    {{ $employee->user?->raw_password ?? 'staff123' }}
                                </span>
                                <button type="button" class="btn btn-sm btn-light border p-1 px-2" onclick="navigator.clipboard.writeText('{{ $employee->user?->raw_password ?? 'staff123' }}')" title="Copy Password">
                                    <i class="fas fa-copy text-muted"></i>
                                </button>
                            </div>
                        </div>
                        @if(in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']) && $employee->user_id)
                        <div class="col-12 col-md-4">
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem;">Update Password</small>
                            <div class="input-group input-group-sm">
                                <input type="text" id="show-new-password" class="form-control" placeholder="New Password..." minlength="6">
                                <button class="btn btn-success" type="button" onclick="updateStaffPasswordDirect({{ $employee->user_id }})">
                                    <i class="fas fa-save me-1"></i>Save
                                </button>
                            </div>
                            <div id="show-pw-msg" class="small mt-1" style="display:none;"></div>
                        </div>
                        @endif
                    </div>
                </div>
                <script>
                    function updateStaffPasswordDirect(userId) {
                        const newPw = document.getElementById('show-new-password').value.trim();
                        const msgEl = document.getElementById('show-pw-msg');
                        if (!newPw || newPw.length < 6) {
                            msgEl.style.display = 'block';
                            msgEl.className = 'small text-danger fw-bold mt-1';
                            msgEl.textContent = 'Minimum 6 characters required.';
                            return;
                        }

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                        fetch(`/sub-admins/${userId}/password-update`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ password: newPw })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('show-raw-pw').textContent = data.password;
                                document.getElementById('show-new-password').value = '';
                                msgEl.style.display = 'block';
                                msgEl.className = 'small text-success fw-bold mt-1';
                                msgEl.textContent = 'Password updated successfully!';
                                setTimeout(() => { msgEl.style.display = 'none'; }, 3000);
                            } else {
                                msgEl.style.display = 'block';
                                msgEl.className = 'small text-danger fw-bold mt-1';
                                msgEl.textContent = data.error || 'Failed to update.';
                            }
                        })
                        .catch(() => {
                            msgEl.style.display = 'block';
                            msgEl.className = 'small text-danger fw-bold mt-1';
                            msgEl.textContent = 'Error updating password.';
                        });
                    }
                </script>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Active Tasks -->
    <div class="col-12 col-xl-7">
        <div class="profile-card h-100 mb-0">
            <div class="card-header bg-transparent border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-tasks text-success me-2"></i>Assigned Tasks</h5>
                <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-outline-success">Assign Task</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table history-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Task</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr>
                                    <td class="fw-bold ps-4">{{ $task->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $task->priority === 'High' ? 'danger' : ($task->priority === 'Medium' ? 'warning' : 'info') }} rounded-pill">
                                            {{ $task->priority }}
                                        </span>
                                    </td>
                                    <td>{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($task->status) === 'completed' ? 'success' : 'secondary' }} rounded-pill">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No active tasks assigned.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="col-12 col-xl-5">
        <div class="profile-card h-100 mb-0">
            <div class="card-header bg-transparent border-bottom p-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-check text-primary me-2"></i>Recent Attendance</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table history-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td class="fw-medium ps-4">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($attendance->status) === 'present' ? 'success' : (strtolower($attendance->status) === 'absent' ? 'danger' : 'warning') }} rounded-pill">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 text-muted small">
                                        {{ $attendance->remarks ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No recent attendance records.</td>
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
