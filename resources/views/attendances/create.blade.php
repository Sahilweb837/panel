@extends('layouts.app')

@section('title', 'Record Attendance')

@section('page-title', 'Record Attendance')

@section('content')
    <div class="card form-card">
        <form action="{{ route('attendances.store') }}" method="POST">
            @csrf

            <div class="grid grid-2 gap-4">
                <label>Student
                    <select name="student_id" required>
                        <option value="">Choose a student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->admission_no }} - {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>Date<input type="date" name="attendance_date" value="{{ old('attendance_date', date('Y-m-d')) }}" required /></label>
            </div>

            <div class="grid grid-2 gap-4">
                <label>Status
                    <select name="status" required>
                        <option value="Present" {{ old('status') === 'Present' ? 'selected' : '' }}>Present</option>
                        <option value="Absent" {{ old('status') === 'Absent' ? 'selected' : '' }}>Absent</option>
                        <option value="Late" {{ old('status') === 'Late' ? 'selected' : '' }}>Late</option>
                        <option value="Leave" {{ old('status') === 'Leave' ? 'selected' : '' }}>Leave</option>
                    </select>
                </label>
                <label>Fine Amount<input type="number" name="fine" step="0.01" value="{{ old('fine', 0) }}" /></label>
            </div>

            <label>Remarks<textarea name="remarks">{{ old('remarks') }}</textarea></label>

            <button type="submit" class="button button-primary">Save Attendance</button>
        </form>
    </div>
@endsection
