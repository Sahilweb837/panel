@extends('layouts.app')

@section('title', 'Students')
@section('page-title', 'Student Management')

@section('content')
    <div class="students-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 320px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 130px; height: 42px;"></div>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-xl-4"><div class="sk-card" style="height: 280px;"></div></div>
                <div class="col-12 col-md-6 col-xl-4"><div class="sk-card" style="height: 280px;"></div></div>
                <div class="col-12 col-md-6 col-xl-4"><div class="sk-card" style="height: 280px;"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <!-- Analytics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card premium-stat-card h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">TOTAL STUDENTS</h4>
                        <h2 class="fw-bold mb-0 text-dark-title" style="font-size: 1.75rem;">{{ number_format($totalStudents) }}</h2>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card premium-stat-card h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(40, 167, 69, 0.1); color: #28a745;">
                                <i class="fas fa-user-check fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">ACTIVE STUDENTS</h4>
                        <h2 class="fw-bold mb-0 text-dark-title" style="font-size: 1.75rem;">{{ number_format($activeStudents) }}</h2>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card premium-stat-card h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                                <i class="fas fa-user-times fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">INACTIVE STUDENTS</h4>
                        <h2 class="fw-bold mb-0 text-dark-title" style="font-size: 1.75rem;">{{ number_format($inactiveStudents) }}</h2>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="card premium-stat-card h-100 p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-wrapper d-grid place-items-center" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                                <i class="fas fa-laptop-house fa-lg"></i>
                            </div>
                        </div>
                        <h4 class="text-muted small fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">ONLINE STUDENTS</h4>
                        <h2 class="fw-bold mb-0 text-dark-title" style="font-size: 1.75rem;">{{ number_format($onlineStudents) }}</h2>
                    </div>
                </div>
            </div>

            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1 flex-wrap" method="GET" action="{{ route('students.index') }}">
                    <div style="position: relative; flex: 1; min-width: 200px;">
                        <input type="text" name="search" placeholder="Search by name or admission no..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 160px;">
                        <select name="course_id" class="form-input" style="padding-left: 36px;">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (string) request('course_id') === (string) $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-book text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 150px;">
                        <select name="status" class="form-input" style="padding-left: 36px;">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <i class="fas fa-circle-check text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('course_id') || request('status'))
                        <a href="{{ route('students.index') }}" class="button button-secondary px-3 py-2">
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
                            Show Recycle Bin Data
                        </label>
                    </div>
                    <a href="{{ route('students.create') }}" class="button button-primary py-2 px-4">
                        <i class="fas fa-plus me-2"></i>Add Student
                    </a>
                </div>
            </div>

            <div class="row g-4">
                @forelse($students as $student)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="card premium-stat-card h-100 p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="student-card-top mb-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="student-avatar text-uppercase-bold d-grid place-items-center" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 85, 50, 0.1); color: var(--first-color); font-size: 1.2rem; font-weight: 800;">
                                            {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="fw-bold mb-1 text-dark-title" style="font-size: 1.15rem; margin: 0; {{ request('trashed') ? 'text-decoration: line-through; color: #dc3545;' : '' }}">{{ $student->first_name }} {{ $student->last_name }}</h3>
                                            <p class="text-muted small mb-0"><i class="fas fa-id-card me-1"></i>{{ $student->admission_no }}{{ $student->roll_no ? ' / '.$student->roll_no : '' }}</p>
                                        </div>
                                    </div>
                                    <span class="status-pill {{ $student->status ? 'active' : 'inactive' }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.75rem;">
                                        {{ $student->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <dl class="student-card-details border-top border-bottom py-3 my-3">
                                    <div class="mb-2">
                                        <dt class="text-muted small mb-1" style="font-size: 0.75rem;">COURSE</dt>
                                        <dd class="fw-bold text-dark-title" style="font-size: 0.9rem; margin: 0;">{{ optional($student->course)->name ?? $student->class ?? 'Not assigned' }}</dd>
                                    </div>
                                    <div class="mb-2">
                                        <dt class="text-muted small mb-1" style="font-size: 0.75rem;">STUDENT TYPE</dt>
                                        <dd style="margin: 0;">
                                            @if(($student->student_type ?? 'Regular (On Campus)') === 'Online')
                                                <span class="badge px-2 py-1" style="background-color: rgba(23, 162, 184, 0.1); color: #17a2b8; font-size: 0.75rem; font-weight: 600; border-radius: 4px;">Online</span>
                                            @elseif(($student->student_type ?? 'Regular (On Campus)') === 'Regular (Internship)')
                                                <span class="badge px-2 py-1" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1; font-size: 0.75rem; font-weight: 600; border-radius: 4px;">Regular (Internship)</span>
                                            @else
                                                <span class="badge px-2 py-1" style="background-color: rgba(255, 85, 50, 0.1); color: var(--first-color); font-size: 0.75rem; font-weight: 600; border-radius: 4px;">Regular (On Campus)</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="mb-2">
                                        <dt class="text-muted small mb-1" style="font-size: 0.75rem;">PHONE</dt>
                                        <dd class="fw-bold text-dark-title" style="font-size: 0.9rem; margin: 0;">{{ $student->phone ?? 'Not added' }}</dd>
                                    </div>
                                    <div class="mb-0">
                                        <dt class="text-muted small mb-1" style="font-size: 0.75rem;">AADHAR NUMBER</dt>
                                        <dd class="fw-bold text-dark-title" style="font-size: 0.9rem; margin: 0;">{{ $student->aadhar_number ? implode(' ', str_split($student->aadhar_number, 4)) : 'Not added' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="d-flex gap-2">
                                @if($student->trashed())
                                    <form action="{{ route('students.restore', $student->id) }}" method="POST" class="inline-form flex-grow-1" onsubmit="return confirmAction(event, 'Restore this student?');">
                                        @csrf
                                        <button type="submit" class="button button-success small w-100 py-2">
                                            <i class="fas fa-trash-restore me-1"></i>Restore
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('students.show', $student) }}" class="button button-primary small flex-grow-1 py-2" style="background-color: var(--first-color); color: white; border: none;">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" class="button button-secondary small flex-grow-1 py-2">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    @if(in_array(session('user_role_slug'), ['super-admin', 'superadmin', 'root-admin', 'admin', 'subadmin', 'sub-admin']) && $student->user_id)
                                    <button type="button" class="button button-secondary small flex-grow-1 py-2 view-password-btn" data-user-id="{{ $student->user_id }}" data-url="{{ route('sub-admins.password.show', $student->user_id) }}">
                                        <i class="fas fa-key me-1"></i>Key
                                    </button>
                                    @endif
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline-form flex-grow-1" onsubmit="return confirmAction(event, 'Are you sure you want to delete this student record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-danger small w-100 py-2">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card premium-stat-card p-5 text-center text-muted">
                            <i class="fas fa-user-graduate fa-3x text-muted mb-3 d-block"></i>
                            No students registered matching the criteria. Click "Add Student" above to enroll.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and skeleton fading -->
    <script>
        let currentPasswordUserId = null;

        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);

            // Password modal
            document.querySelectorAll('.view-password-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const url = this.dataset.url;
                    currentPasswordUserId = this.dataset.userId;
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(data => {
                            if(data.error) {
                                alert(data.error);
                                return;
                            }
                            document.getElementById('pw-name').textContent = data.name || '—';
                            document.getElementById('pw-email').textContent = data.email || '—';
                            document.getElementById('pw-password').textContent = data.password || '—';
                            document.getElementById('new-password-input').value = '';
                            document.getElementById('pw-update-status').style.display = 'none';
                            const modal = document.getElementById('passwordModal');
                            modal.style.display = 'flex';
                        })
                        .catch(() => alert('Could not fetch password. Please try again.'));
                });
            });
        });

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        function copyPassword() {
            const pw = document.getElementById('pw-password').textContent;
            navigator.clipboard.writeText(pw).then(() => {
                const btn = document.querySelector('#passwordModal button[onclick="copyPassword()"]');
                btn.innerHTML = '<i class="fas fa-check"></i>';
                setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i>', 2000);
            });
        }

        function submitPasswordUpdate() {
            const newPw = document.getElementById('new-password-input').value.trim();
            const statusEl = document.getElementById('pw-update-status');

            if (!newPw || newPw.length < 6) {
                statusEl.style.display = 'block';
                statusEl.className = 'alert alert-danger py-2 px-3 small';
                statusEl.textContent = 'Password must be at least 6 characters.';
                return;
            }

            if (!currentPasswordUserId) {
                alert('User ID missing.');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

            fetch(`/sub-admins/${currentPasswordUserId}/password-update`, {
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
                    document.getElementById('pw-password').textContent = data.password;
                    document.getElementById('new-password-input').value = '';
                    statusEl.style.display = 'block';
                    statusEl.className = 'alert alert-success py-2 px-3 small';
                    statusEl.textContent = 'Password updated and saved successfully!';
                    setTimeout(() => { statusEl.style.display = 'none'; }, 3000);
                } else {
                    statusEl.style.display = 'block';
                    statusEl.className = 'alert alert-danger py-2 px-3 small';
                    statusEl.textContent = data.error || 'Failed to update password.';
                }
            })
            .catch(err => {
                statusEl.style.display = 'block';
                statusEl.className = 'alert alert-danger py-2 px-3 small';
                statusEl.textContent = 'An error occurred while updating password.';
            });
        }

        const passModal = document.getElementById('passwordModal');
        if(passModal) {
            passModal.addEventListener('click', function(e) {
                if (e.target === this) closePasswordModal();
            });
        }
    </script>

    {{-- Password Viewer & Editor Modal --}}
    <div id="passwordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div class="card" style="max-width:440px; width:90%; padding:2rem; position:relative; border-radius:16px;">
            <button onclick="closePasswordModal()" style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.2rem; color:var(--muted); cursor:pointer;"><i class="fas fa-times"></i></button>
            <div style="text-align:center; margin-bottom:1.5rem;">
                <div style="width:56px; height:56px; border-radius:14px; background:rgba(255,85,50,0.1); color:var(--first-color); font-size:1.5rem; display:inline-flex; align-items:center; justify-content:center; margin-bottom:0.75rem;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5 style="font-weight:700; margin:0;">Account Credentials</h5>
                <p style="color:var(--muted); font-size:0.85rem; margin-top:4px;">Manage Student Login & Password</p>
            </div>
            <div id="pw-update-status" style="display:none; margin-bottom:1rem;"></div>
            <div style="display:grid; gap:1rem;">
                <div style="background:var(--surface-soft); border-radius:10px; padding:1rem;">
                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Name</div>
                    <div id="pw-name" style="font-weight:600;">—</div>
                </div>
                <div style="background:var(--surface-soft); border-radius:10px; padding:1rem;">
                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Email / Login ID</div>
                    <div id="pw-email" style="font-weight:600;">—</div>
                </div>
                <div style="background:var(--surface-soft); border-radius:10px; padding:1rem;">
                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Current Password (Plain Text)</div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div id="pw-password" style="font-weight:700; font-size:1.1rem; font-family:monospace; color:var(--first-color); flex:1;">—</div>
                        <button onclick="copyPassword()" style="background:var(--first-color); color:#fff; border:none; border-radius:8px; padding:6px 12px; font-size:0.8rem; cursor:pointer;" title="Copy Password"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
                <div style="background:var(--surface-soft); border-radius:10px; padding:1rem;">
                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:6px;">Update Password</div>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="new-password-input" placeholder="Type new password..." style="padding:6px 10px; font-size:0.85rem; border-radius:8px; border:1px solid var(--border); flex:1;" />
                        <button onclick="submitPasswordUpdate()" style="background:var(--first-color); color:#fff; border:none; border-radius:8px; padding:6px 14px; font-size:0.85rem; font-weight:600; cursor:pointer;">
                            <i class="fas fa-save me-1"></i>Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
