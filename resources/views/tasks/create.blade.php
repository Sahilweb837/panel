@extends('layouts.app')

@section('title', 'Assign Task')
@section('page-title', 'Assign Staff Work Task')

@section('content')
    <div class="tasks-container">
        <div class="card premium-form-card" style="max-width: 700px;">
            <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                <h3 class="mb-0 fw-bold text-first">
                    <i class="fas fa-tasks me-2"></i>Assign New Task
                </h3>
            </div>

            <form action="{{ route('tasks.store') }}" method="POST" class="form-card p-0">
                @csrf

                <div class="form-group mb-4">
                    <label for="title" class="fw-semibold mb-2">
                        <i class="fas fa-heading text-first me-2"></i>Task Title / Summary
                    </label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}" placeholder="e.g. Audit monthly student outstanding receipts" class="form-input {{ $errors->has('title') ? 'is-invalid' : '' }}" />
                    @error('title')
                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="description" class="fw-semibold mb-2">
                        <i class="fas fa-align-left text-first me-2"></i>Detailed Description
                    </label>
                    <textarea id="description" name="description" placeholder="Provide instructions for the employee..." class="form-input" style="min-height: 120px; resize: vertical; padding: 12px;">{{ old('description') }}</textarea>
                </div>

                <div class="form-group-grid mb-4">
                    <div class="form-group">
                        <label for="assigned_to" class="fw-semibold mb-2">
                            <i class="fas fa-user text-first me-2"></i>Assign To Employee
                        </label>
                        <select id="assigned_to" name="assigned_to" required class="form-input">
                            <option value="">-- Choose Staff Assignee --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('assigned_to') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->user?->name ?? 'Unknown' }} ({{ $emp->employee_code }} - {{ $emp->designation }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="priority" class="fw-semibold mb-2">
                            <i class="fas fa-circle-exclamation text-first me-2"></i>Task Priority
                        </label>
                        <select id="priority" name="priority" required class="form-input">
                            <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                            <option value="Medium" {{ old('priority', 'Medium') == 'Medium' ? 'selected' : '' }}>Medium</option>
                            <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-4" style="max-width: 50%;">
                    <label for="due_date" class="fw-semibold mb-2">
                        <i class="fas fa-calendar-alt text-first me-2"></i>Due Date (Optional)
                    </label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" class="form-input" />
                </div>

                <div class="form-actions-row">
                    <a href="{{ route('tasks.index') }}" class="button button-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="button button-primary">
                        <i class="fas fa-check me-2"></i>Assign Task
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
