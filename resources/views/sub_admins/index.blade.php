@extends('layouts.app')

@section('title', 'Sub-Admins & Staff')
@section('page-title', 'Sub-Admins & Staff')

@section('content')
    <div class="toolbar">
        <form method="GET" action="{{ route('sub-admins.index') }}" class="filter-form">
            <input type="text" name="search" placeholder="Search by name, email, or username..." value="{{ request('search') }}">
            <select name="role">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
            <button type="submit" class="button button-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <a href="{{ route('sub-admins.create') }}" class="button button-primary">
            <i class="fas fa-plus"></i> Create New
        </a>
    </div>

    <div class="card table-card">
        <table class="table">
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Name</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-user-tag"></i> Role</th>
                    <th><i class="fas fa-toggle-on"></i> Status</th>
                    <th><i class="fas fa-calendar"></i> Created</th>
                    <th><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="avatar">{{ substr($user->name, 0, 1) }}</div>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    <p>@{{ $user->username }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-badge role-{{ strtolower($user->role?->slug) }}">
                                <i class="fas fa-{{ $user->role?->slug === 'admin' ? 'user-shield' : 'user-check' }}"></i>
                                {{ $user->role?->role_name }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $user->status ? 'active' : 'inactive' }}">
                                <i class="fas fa-{{ $user->status ? 'check-circle' : 'times-circle' }}"></i>
                                {{ $user->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="action-cell">
                            <a href="{{ route('sub-admins.edit', $user) }}" class="button button-secondary small">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('sub-admins.destroy', $user) }}" method="POST" class="inline-form" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="button button-danger small" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <p style="color: var(--muted); padding: 20px 0;">No sub-admins or staff members found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $users->links() }}
    </div>
@endsection
