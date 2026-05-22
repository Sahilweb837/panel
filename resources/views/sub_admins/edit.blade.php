@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit ' . $subAdmin->name)

@section('content')
    <div class="card" style="max-width: 600px;">
        <form method="POST" action="{{ route('sub-admins.update', $subAdmin) }}" class="form-card">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $subAdmin->name) }}" 
                    required 
                    placeholder="Enter full name"
                    class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                />
                @error('name')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $subAdmin->email) }}" 
                    required 
                    placeholder="Enter email address"
                    class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                />
                @error('email')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="username">
                    <i class="fas fa-at"></i> Username
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', $subAdmin->username) }}" 
                    required 
                    placeholder="Enter username"
                    class="form-input {{ $errors->has('username') ? 'is-invalid' : '' }}"
                />
                @error('username')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-user-tag"></i> Role
                </label>
                <div class="form-input" style="background: var(--surface-soft); padding: 12px 14px; border: none; cursor: not-allowed;">
                    <strong>{{ $subAdmin->role?->role_name }}</strong>
                    <small style="display: block; color: var(--muted); margin-top: 4px;">Role cannot be changed</small>
                </div>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Leave blank to keep current password"
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                />
                <small style="color: var(--muted);">Only fill if you want to change the password</small>
                @error('password')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">
                    <i class="fas fa-lock"></i> Confirm Password
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    placeholder="Confirm password"
                    class="form-input"
                />
            </div>

            <div class="form-group checkbox-group">
                <label class="checkbox-label">
                    <input 
                        type="checkbox" 
                        name="status" 
                        value="1" 
                        {{ old('status', $subAdmin->status) ? 'checked' : '' }} 
                        class="checkbox-input"
                    />
                    <span>
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                </label>
                <small style="color: var(--muted); margin-left: 28px;">Uncheck to deactivate this account</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="button button-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('sub-admins.index') }}" class="button button-secondary" style="flex: 1; text-align: center;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
