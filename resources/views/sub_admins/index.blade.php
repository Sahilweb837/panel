@extends('layouts.app')

@section('title', 'Sub-Admins & Staff')
@section('page-title', 'Sub-Admins & Staff')

@section('content')
    <div class="staff-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 280px; height: 42px;"></div>
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
                <form method="GET" action="{{ route('sub-admins.index') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by name, email, or username..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 180px;">
                        <select name="role" class="form-input" style="padding-left: 36px;">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        <i class="fas fa-user-shield text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('sub-admins.index') }}" class="button button-secondary px-3 py-2">
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
                    <a href="{{ route('sub-admins.create') }}" class="button button-primary py-2 px-4">
                        <i class="fas fa-plus me-2"></i>Create New
                    </a>
                </div>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-user-shield text-first"></i> System Users & Permission Slates
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-user me-1"></i> Name</th>
                                <th><i class="fas fa-envelope me-1"></i> Email</th>
                                <th><i class="fas fa-user-tag me-1"></i> Role</th>
                                <th><i class="fas fa-key me-1"></i> Password</th>
                                <th><i class="fas fa-toggle-on me-1"></i> Status</th>
                                <th><i class="fas fa-{{ request('trashed') ? 'calendar-times' : 'calendar' }} me-1"></i> {{ request('trashed') ? 'Deleted' : 'Created' }}</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="user-info">
                                            <div class="avatar" style="font-weight: 700; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">{{ substr($user->name, 0, 1) }}</div>
                                            <div>
                                                <strong class="text-dark-title">{{ $user->name }}</strong>
                                                <p class="text-muted small">{{ $user->username }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $user->email }}</td>
                                    <td>
                                        <span class="role-badge role-{{ strtolower($user->role?->slug) }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            <i class="fas fa-{{ $user->role?->slug === 'admin' ? 'user-shield' : ($user->role?->slug === 'student' ? 'user-graduate' : 'user-check') }} me-1"></i>
                                            {{ $user->role?->role_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="button button-secondary small py-1 px-2 view-password-btn"
                                            data-url="{{ route('sub-admins.password', $user) }}"
                                            title="View Password">
                                            <i class="fas fa-eye me-1"></i> View
                                        </button>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $user->status ? 'active' : 'inactive' }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            <i class="fas fa-{{ $user->status ? 'check-circle' : 'times-circle' }} me-1"></i>
                                            {{ $user->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="{{ request('trashed') ? 'text-danger' : 'text-muted' }}">
                                        {{ request('trashed') ? $user->deleted_at->format('M d, Y') : $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="text-end pe-4 action-cell">
                                        @if($user->trashed())
                                            <form action="{{ route('sub-admins.restore', $user->id) }}" method="POST" class="inline-form d-inline" onsubmit="return confirmAction(event, 'Are you sure you want to restore this user?');">
                                                @csrf
                                                <button type="submit" class="button button-success small py-1.5 px-3">
                                                    <i class="fas fa-trash-restore me-1"></i>Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('sub-admins.edit', $user) }}" class="button button-secondary small py-1.5 px-3">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('sub-admins.destroy', $user) }}" method="POST" class="inline-form d-inline" onsubmit="return confirmAction(event, 'Are you sure you want to delete this user?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="button button-danger small py-1.5 px-3">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-user-slash fa-2x mb-3 d-block text-muted"></i>
                                        No sub-admins or staff members registered.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- Password Viewer Modal --}}
    <div id="passwordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div class="card" style="max-width:440px; width:90%; padding:2rem; position:relative; border-radius:16px;">
            <button onclick="closePasswordModal()" style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.2rem; color:var(--muted); cursor:pointer;"><i class="fas fa-times"></i></button>
            <div style="text-align:center; margin-bottom:1.5rem;">
                <div style="width:56px; height:56px; border-radius:14px; background:rgba(255,85,50,0.1); color:var(--first-color); font-size:1.5rem; display:inline-flex; align-items:center; justify-content:center; margin-bottom:0.75rem;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5 style="font-weight:700; margin:0;">Account Credentials</h5>
                <p style="color:var(--muted); font-size:0.85rem; margin-top:4px;">Super Admin Only — Confidential</p>
            </div>
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
                    <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Password (Plain Text)</div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div id="pw-password" style="font-weight:700; font-size:1.1rem; font-family:monospace; color:var(--first-color); flex:1;">—</div>
                        <button onclick="copyPassword()" style="background:var(--first-color); color:#fff; border:none; border-radius:8px; padding:6px 12px; font-size:0.8rem; cursor:pointer;"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(data => {
                            document.getElementById('pw-name').textContent = data.name || '—';
                            document.getElementById('pw-email').textContent = data.email || '—';
                            document.getElementById('pw-password').textContent = data.password || '—';
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

        // Close modal on backdrop click
        document.getElementById('passwordModal').addEventListener('click', function(e) {
            if (e.target === this) closePasswordModal();
        });
    </script>
@endsection
