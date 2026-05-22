@extends('layouts.app')

@section('title', 'Create Sub-Admin / Staff')
@section('page-title', 'Create Sub-Admin / Staff Member')

@section('content')
    <div class="card" style="max-width: 600px;">
        <form method="POST" action="{{ route('sub-admins.store') }}" class="form-card">
            @csrf

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}" 
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
                    value="{{ old('email') }}" 
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
                    value="{{ old('username') }}" 
                    required 
                    placeholder="Choose username"
                    class="form-input {{ $errors->has('username') ? 'is-invalid' : '' }}"
                />
                @error('username')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="role">
                    <i class="fas fa-user-tag"></i> Role
                </label>
                <select 
                    id="role" 
                    name="role" 
                    required 
                    class="form-input {{ $errors->has('role') ? 'is-invalid' : '' }}"
                >
                    <option value="">-- Select Role --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                        <i class="fas fa-user-shield"></i> Admin
                    </option>
                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>
                        <i class="fas fa-user-check"></i> Staff
                    </option>
                </select>
                @error('role')
                    <small style="color: var(--danger-text);">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Enter password (min. 6 characters)"
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                />
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
                    required 
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
                        {{ old('status', true) ? 'checked' : '' }} 
                        class="checkbox-input"
                    />
                    <span>
                        <i class="fas fa-check-circle"></i> Active
                    </span>
                </label>
                <small style="color: var(--muted); margin-left: 28px;">Check this to activate the account immediately</small>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="button button-primary" style="flex: 1;">
                    <i class="fas fa-plus"></i> Create Account
                </button>
                <a href="{{ route('sub-admins.index') }}" class="button button-secondary" style="flex: 1; text-align: center;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
