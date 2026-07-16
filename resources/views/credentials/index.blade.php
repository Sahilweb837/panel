@extends('layouts.app')

@section('title', 'User Credentials')
@section('page-title', 'User Credentials')

@section('content')
    <div class="credentials-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 320px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 130px; height: 42px;"></div>
            </div>
            <div class="row g-4">
                <div class="col-12"><div class="sk-card" style="height: 400px;"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Security Notice:</strong> Plain text passwords are shown for administrative purposes. Passwords are securely hashed in the database.
            </div>

            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1 flex-wrap" method="GET" action="{{ route('credentials.index') }}">
                    <div style="position: relative; flex: 1; min-width: 200px;">
                        <input type="text" name="search" placeholder="Search by name, email, or username..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 160px;">
                        <select name="role" class="form-input" style="padding-left: 36px;">
                            <option value="">All Roles</option>
                            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        <i class="fas fa-user-tag text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('credentials.index') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('credentials.create') }}" class="button button-primary px-4 py-2">
                    <i class="fas fa-plus me-2"></i>Create Credential
                </a>
            </div>

            <div class="card premium-stat-card border-0 p-0 overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Name</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Role</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Login Email</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Register ID / Username</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Password</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="py-3 px-4 fw-bold text-dark-title">{{ $user->name }}</td>
                                    <td class="py-3 px-4">
                                        <span class="badge" style="background-color: {{ $user->role->slug === 'staff' ? 'rgba(111, 66, 193, 0.1)' : 'rgba(255, 85, 50, 0.1)' }}; color: {{ $user->role->slug === 'staff' ? '#6f42c1' : 'var(--first-color)' }}; font-weight: 600; padding: 6px 12px;">
                                            {{ ucfirst($user->role->slug) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-muted">{{ $user->email ?? '-' }}</td>
                                    <td class="py-3 px-4 fw-bold" style="color: #2c3e50;">{{ $user->username }}</td>
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="password-field" style="font-family: monospace; background: #f8f9fa; padding: 4px 8px; border-radius: 4px; border: 1px solid #e9ecef; color: #333;">
                                                {{ $user->raw_password ?? 'Encrypted' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('credentials.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('credentials.destroy', $user->id) }}" method="POST" onsubmit="return confirmAction(event, 'Are you sure you want to delete this credential? This will also remove the user\'s login access.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-users fa-3x mb-3 text-muted opacity-50"></i>
                                        <p class="mb-0">No records found matching your criteria.</p>
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
