@extends('layouts.app')

@section('title', 'Add Staff')

@section('page-title', 'Add Staff')

@section('content')
    <div class="card form-card">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf

            <div class="grid grid-2 gap-4">
                <label>Employee Code<input type="text" name="employee_code" value="{{ old('employee_code') }}" required /></label>
                <label>Staff Name<input type="text" name="staff_name" value="{{ old('staff_name') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Login Email<input type="email" name="login_email" value="{{ old('login_email') }}" /></label>
                <label>Login Username<input type="text" name="login_username" value="{{ old('login_username') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Login Password<input type="password" name="login_password" /></label>
                <label>Phone<input type="text" name="phone" value="{{ old('phone') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Department<input type="text" name="department" value="{{ old('department') }}" /></label>
                <label>Designation<input type="text" name="designation" value="{{ old('designation') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Salary<input type="number" name="salary" step="0.01" value="{{ old('salary', 0) }}" required /></label>
                <label>Joining Date<input type="date" name="joining_date" value="{{ old('joining_date') }}" /></label>
            </div>

            <label>Address<textarea name="address">{{ old('address') }}</textarea></label>

            <label class="checkbox-input">
                <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }} /> Active
            </label>

            <button type="submit" class="button button-primary">Create Staff</button>
        </form>
    </div>
@endsection
