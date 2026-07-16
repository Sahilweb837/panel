@extends('layouts.app')

@section('title', 'Edit Credential')
@section('page-title', 'Edit Credential')

@section('content')
<div class="card premium-stat-card border-0 p-4">
    <form action="{{ route('credentials.update', $credential->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $credential->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label">Register ID / Username</label>
                <input type="text" name="username" id="username" class="form-input @error('username') is-invalid @enderror" value="{{ old('username', $credential->username) }}" required>
                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email (Optional)</label>
                <input type="email" name="email" id="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email', $credential->email) }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password (Leave blank to keep unchanged)</label>
                <input type="text" name="password" id="password" class="form-input @error('password') is-invalid @enderror" value="" placeholder="Current: {{ $credential->raw_password }}">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="role_id" class="form-label">Role</label>
                <select name="role_id" id="role_id" class="form-input @error('role_id') is-invalid @enderror" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $credential->role_id) == $role->id ? 'selected' : '' }}>{{ ucfirst($role->slug) }}</option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="button button-primary px-4 py-2">Update Credential</button>
            <a href="{{ route('credentials.index') }}" class="button button-secondary px-4 py-2 ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
