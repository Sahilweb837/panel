@extends('layouts.app')

@section('title', 'New Training Course')
@section('page-title', 'Create Training Course')

@section('content')
    <div class="courses-container">
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card" style="max-width: 750px;">
                <div class="sk-text heading"></div>
                <div class="form-group-grid">
                    <div><div class="sk-text short"></div><div class="sk-card" style="height: 48px;"></div></div>
                    <div><div class="sk-text short"></div><div class="sk-card" style="height: 48px;"></div></div>
                </div>
                <div class="form-actions-row">
                    <div class="sk-button"></div>
                    <div class="sk-button"></div>
                </div>
            </div>
        </div>

        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="card premium-form-card" style="max-width: 750px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-graduation-cap me-2"></i>Create Training Course
                    </h3>
                </div>

                <form action="{{ route('training_courses.store') }}" method="POST" class="form-card p-0">
                    @csrf
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="name" class="fw-semibold mb-2">
                                <i class="fas fa-book text-first me-2"></i>Course Name
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. PHP Full Stack" class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}" />
                            @error('name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="short_code" class="fw-semibold mb-2">
                                <i class="fas fa-code text-first me-2"></i>Short Code
                            </label>
                            <input type="text" id="short_code" name="short_code" value="{{ old('short_code') }}" placeholder="e.g. PHPFS" maxlength="50" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="duration" class="fw-semibold mb-2">
                                <i class="fas fa-clock text-first me-2"></i>Duration
                            </label>
                            <select id="duration" name="duration" required class="form-input {{ $errors->has('duration') ? 'is-invalid' : '' }}">
                                @foreach($durations as $duration)
                                    <option value="{{ $duration }}" {{ old('duration', '28 Days') == $duration ? 'selected' : '' }}>{{ $duration }}</option>
                                @endforeach
                            </select>
                            @error('duration')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="fee" class="fw-semibold mb-2">
                                <i class="fas fa-coins text-first me-2"></i>Course Fee (INR)
                            </label>
                            <input type="number" id="fee" name="fee" step="0.01" value="{{ old('fee', 0) }}" required placeholder="0.00" class="form-input {{ $errors->has('fee') ? 'is-invalid' : '' }}" />
                            @error('fee')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="fw-semibold mb-3">
                            <i class="fas fa-calendar-days text-first me-2"></i>Payment Tenure Fees
                        </label>
                        <div class="form-group-grid mb-3" style="grid-template-columns: repeat(4, 1fr);">
                            <div class="form-group">
                                <label for="tenure_1_month" class="fw-semibold mb-2" style="font-size: 0.8rem;">1 Month</label>
                                <input type="number" id="tenure_1_month" name="tenure_1_month" step="0.01" value="{{ old('tenure_1_month', 0) }}" placeholder="0.00" class="form-input" />
                            </div>
                            <div class="form-group">
                                <label for="tenure_3_months" class="fw-semibold mb-2" style="font-size: 0.8rem;">3 Months</label>
                                <input type="number" id="tenure_3_months" name="tenure_3_months" step="0.01" value="{{ old('tenure_3_months', 0) }}" placeholder="0.00" class="form-input" />
                            </div>
                            <div class="form-group">
                                <label for="tenure_6_months" class="fw-semibold mb-2" style="font-size: 0.8rem;">6 Months</label>
                                <input type="number" id="tenure_6_months" name="tenure_6_months" step="0.01" value="{{ old('tenure_6_months', 0) }}" placeholder="0.00" class="form-input" />
                            </div>
                            <div class="form-group">
                                <label for="tenure_12_months" class="fw-semibold mb-2" style="font-size: 0.8rem;">12 Months</label>
                                <input type="number" id="tenure_12_months" name="tenure_12_months" step="0.01" value="{{ old('tenure_12_months', 0) }}" placeholder="0.00" class="form-input" />
                            </div>
                        </div>
                        <small class="text-muted">Set per-installment fee for each tenure. Leave as 0 if not applicable.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label for="description" class="fw-semibold mb-2">
                            <i class="fas fa-align-left text-first me-2"></i>Description
                        </label>
                        <textarea id="description" name="description" rows="3" placeholder="Brief course description..." class="form-input" style="resize: vertical;">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group checkbox-group mb-4">
                        <label class="checkbox-label" style="cursor: pointer;">
                            <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }} class="checkbox-input" />
                            <span class="fw-semibold">
                                <i class="fas fa-circle-check text-success me-1"></i>Active Status
                            </span>
                        </label>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('training_courses.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-save me-2"></i>Create Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection