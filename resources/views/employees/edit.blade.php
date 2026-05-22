@extends('layouts.app')

@section('title', 'Edit Staff')

@section('page-title', 'Edit Staff')

@section('content')
    <div class="card form-card">
        <form action="{{ route('employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-2 gap-4">
                <label>Employee Code<input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required /></label>
                <label>Staff Name<input type="text" name="staff_name" value="{{ old('staff_name', $employee->user?->name) }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Login Email<input type="email" name="login_email" value="{{ old('login_email', $employee->user?->email) }}" /></label>
                <label>Login Username<input type="text" name="login_username" value="{{ old('login_username', $employee->user?->username) }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>New Login Password<input type="password" name="login_password" /></label>
                <label>Phone<input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Department<input type="text" name="department" value="{{ old('department', $employee->department) }}" /></label>
                <label>Designation<input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Salary<input type="number" name="salary" step="0.01" value="{{ old('salary', $employee->salary) }}" required /></label>
                <label>Joining Date<input type="date" name="joining_date" value="{{ old('joining_date', $employee->joining_date) }}" /></label>
            </div>

            <label>Address<textarea name="address">{{ old('address', $employee->address) }}</textarea></label>

            <label class="checkbox-input">
                <input type="checkbox" name="status" value="1" {{ old('status', $employee->status) ? 'checked' : '' }} /> Active
            </label>

            <button type="submit" class="button button-primary">Update Staff</button>
        </form>
    </div>
@endsection
