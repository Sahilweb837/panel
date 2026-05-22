@extends('layouts.app')

@section('title', 'Add Course')

@section('page-title', 'Add Course')

@section('content')
    <div class="card form-card">
        <form action="{{ route('courses.store') }}" method="POST">
            @csrf

            <div class="grid grid-2 gap-4">
                <label>Course Name<input type="text" name="name" value="{{ old('name') }}" required /></label>
                <label>Course Code<input type="text" name="code" value="{{ old('code') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Duration
                    <select name="duration">
                        <option value="">Flexible</option>
                        <option value="45 Days" {{ old('duration') === '45 Days' ? 'selected' : '' }}>45 Days</option>
                        <option value="1 Month" {{ old('duration') === '1 Month' ? 'selected' : '' }}>1 Month</option>
                        <option value="6 Months" {{ old('duration') === '6 Months' ? 'selected' : '' }}>6 Months</option>
                        <option value="1 Year" {{ old('duration') === '1 Year' ? 'selected' : '' }}>1 Year</option>
                    </select>
                </label>
                <label>Course Fee<input type="number" name="fee" min="0" step="0.01" value="{{ old('fee', 0) }}" /></label>
            </div>

            <label class="checkbox-input">
                <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }} /> Active
            </label>

            <button type="submit" class="button button-primary">Create Course</button>
        </form>
    </div>
@endsection
