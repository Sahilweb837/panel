@extends('layouts.app')

@section('title', 'Add Student')

@section('page-title', 'Add Student')

@section('content')
    <div class="card form-card">
        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div class="grid grid-3 gap-4">
                <label>Admission No<input type="text" name="admission_no" value="{{ old('admission_no') }}" required /></label>
                <label>Roll No<input type="text" name="roll_no" value="{{ old('roll_no') }}" /></label>
                <label>Aadhar Number (12 Digits)<input type="text" name="aadhar_number" maxlength="12" placeholder="e.g. 123456789012" value="{{ old('aadhar_number') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>First Name<input type="text" name="first_name" value="{{ old('first_name') }}" required /></label>
                <label>Last Name<input type="text" name="last_name" value="{{ old('last_name') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Guardian Name<input type="text" name="guardian_name" value="{{ old('guardian_name') }}" /></label>
                <label>Email<input type="email" name="email" value="{{ old('email') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Phone<input type="text" name="phone" value="{{ old('phone') }}" /></label>
                <label>Date of Birth<input type="date" name="dob" value="{{ old('dob') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Gender
                    <select name="gender">
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </label>
                <label>Course
                    <select name="course_id">
                        <option value="">Choose course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}{{ $course->code ? ' ('.$course->code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="grid grid-3 gap-4">
                <label>Duration
                    <select name="course_duration">
                        <option value="">Choose duration</option>
                        @foreach($durations as $duration)
                            <option value="{{ $duration }}" {{ old('course_duration') === $duration ? 'selected' : '' }}>{{ $duration }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Student Type
                    <select name="student_type" required>
                        <option value="Regular (On Campus)" {{ old('student_type', 'Regular (On Campus)') === 'Regular (On Campus)' ? 'selected' : '' }}>Regular Course (On Campus)</option>
                        <option value="Regular (Internship)" {{ old('student_type') === 'Regular (Internship)' ? 'selected' : '' }}>Regular Internship</option>
                        <option value="Online" {{ old('student_type') === 'Online' ? 'selected' : '' }}>Online Course</option>
                    </select>
                </label>
                <label>Admission Date<input type="date" name="admission_date" value="{{ old('admission_date') }}" /></label>
            </div>

            <div class="grid grid-2 gap-4" style="margin-bottom: 1.5rem;">
                <div>
                    <label class="mb-2 d-block">Current Address</label>
                    <textarea id="current_address" name="current_address" placeholder="Enter current address..." style="height: 100px; padding: 10px 14px; border-radius: 8px;">{{ old('current_address') }}</textarea>
                </div>
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                        <label style="margin-bottom: 0;">Permanent Address</label>
                        <div style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; color: var(--muted); cursor: pointer; user-select: none;">
                            <input type="checkbox" id="same_as_current" style="width: 14px; height: 14px; margin: 0; cursor: pointer;" />
                            <span id="same_as_current_label" style="cursor: pointer;">Same as current</span>
                        </div>
                    </div>
                    <textarea id="permanent_address" name="permanent_address" placeholder="Enter permanent address..." style="height: 100px; padding: 10px 14px; border-radius: 8px;">{{ old('permanent_address') }}</textarea>
                </div>
            </div>

            <!-- Fallback/compatible address input -->
            <input type="hidden" name="address" id="hidden_address" value="{{ old('address') }}" />

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const curInput = document.getElementById('current_address');
                    const permInput = document.getElementById('permanent_address');
                    const sameCheck = document.getElementById('same_as_current');
                    const sameCheckLabel = document.getElementById('same_as_current_label');
                    const hiddenAddr = document.getElementById('hidden_address');

                    const updateHidden = () => {
                        hiddenAddr.value = curInput.value;
                    };

                    curInput.addEventListener('input', () => {
                        updateHidden();
                        if (sameCheck.checked) {
                            permInput.value = curInput.value;
                        }
                    });

                    permInput.addEventListener('input', () => {
                        if (sameCheck.checked) {
                            sameCheck.checked = false;
                        }
                    });

                    const handleSameToggle = () => {
                        sameCheck.checked = !sameCheck.checked;
                        if (sameCheck.checked) {
                            permInput.value = curInput.value;
                        }
                    };

                    sameCheck.addEventListener('change', () => {
                        if (sameCheck.checked) {
                            permInput.value = curInput.value;
                        }
                    });

                    sameCheckLabel.addEventListener('click', handleSameToggle);
                });
            </script>

            <label class="checkbox-input">
                <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }} /> Active
            </label>

            <button type="submit" class="button button-primary">Create Student Card</button>
        </form>
    </div>
@endsection
