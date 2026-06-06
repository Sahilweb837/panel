@extends('layouts.app')

@section('title', 'Staff Directory')
@section('page-title', 'Staff Directory Management')

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
                <form method="GET" action="{{ route('employees.index') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1">
                    <div style="position: relative; flex: 1;">
                        <input type="text" name="search" placeholder="Search by name, code, email, or department..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search'))
                        <a href="{{ route('employees.index') }}" class="button button-secondary px-3 py-2">
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
                    <a href="{{ route('employees.create') }}" class="button button-primary py-2 px-4">
                        <i class="fas fa-user-plus me-2"></i>Add Staff Member
                    </a>
                </div>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-person-chalkboard text-first"></i> Staff Registry Profile List
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-id-badge me-1"></i> Code</th>
                                <th><i class="fas fa-user me-1"></i> Name</th>
                                <th><i class="fas fa-building me-1"></i> Department</th>
                                <th><i class="fas fa-user-tie me-1"></i> Designation</th>
                                <th><i class="fas fa-phone me-1"></i> Phone</th>
                                <th><i class="fas fa-wallet me-1"></i> Salary (INR)</th>
                                <th><i class="fas fa-toggle-on me-1"></i> Status</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border p-2 fw-bold" style="font-size: 0.8rem;">
                                            {{ $employee->employee_code }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar" style="font-weight: 700; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">
                                                {{ strtoupper(substr($employee->user?->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark-title" style="{{ request('trashed') ? 'text-decoration: line-through; color: #dc3545;' : '' }}">{{ $employee->user?->name ?? 'No Login Account' }}</strong>
                                                <p class="text-muted small">{{ $employee->user?->username ?? 'unlinked' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-semibold">{{ $employee->department ?? 'General' }}</td>
                                    <td class="text-muted fw-semibold">{{ $employee->designation ?? 'Staff' }}</td>
                                    <td class="text-muted">{{ $employee->phone ?? 'N/A' }}</td>
                                    <td class="text-muted fw-bold">{{ number_format($employee->salary, 2) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $employee->status ? 'active' : 'inactive' }}" style="padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">
                                            <i class="fas fa-{{ $employee->status ? 'check-circle' : 'times-circle' }} me-1"></i>
                                            {{ $employee->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 action-cell">
                                        @if($employee->trashed())
                                            <form action="{{ route('employees.restore', $employee->id) }}" method="POST" class="inline-form d-inline" onsubmit="return confirm('Are you sure you want to restore this employee?');">
                                                @csrf
                                                <button type="submit" class="button button-success small py-1.5 px-3">
                                                    <i class="fas fa-trash-restore me-1"></i>Restore
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('employees.edit', $employee) }}" class="button button-secondary small py-1.5 px-3">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline-form d-inline" onsubmit="return confirm('Are you sure you want to delete this staff record?');">
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
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-2x mb-3 d-block text-muted"></i>
                                        No staff directory profiles registered.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $employees->links() }}
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
