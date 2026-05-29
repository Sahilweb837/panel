@extends('layouts.app')

@section('title', 'Add Course')
@section('page-title', 'Add Course')

@section('content')
    <div class="courses-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card">
                <div class="sk-text heading"></div>
                <div class="form-group-grid">
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                </div>
                <div class="form-group-grid mt-4">
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                    <div>
                        <div class="sk-text short"></div>
                        <div class="sk-card" style="height: 48px;"></div>
                    </div>
                </div>
                <div class="form-actions-row">
                    <div class="sk-button"></div>
                    <div class="sk-button"></div>
                </div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="card premium-form-card">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-book-open me-2"></i>Configure New Academic Course
                    </h3>
                </div>

                <form action="{{ route('courses.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    <div class="form-group-grid">
                        <div class="form-group">
                            <label for="name" class="fw-semibold mb-2">
                                <i class="fas fa-heading text-first me-2"></i>Course Name
                            </label>
                            <input 
                                type="text" 
                                id="name"
                                name="name" 
                                value="{{ old('name') }}" 
                                placeholder="e.g. Master of Business Administration"
                                required 
                                class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            />
                            @error('name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code" class="fw-semibold mb-2">
                                <i class="fas fa-barcode text-first me-2"></i>Course Code
                            </label>
                            <input 
                                type="text" 
                                id="code"
                                name="code" 
                                value="{{ old('code') }}" 
                                placeholder="e.g. MBA-01"
                                class="form-input {{ $errors->has('code') ? 'is-invalid' : '' }}"
                            />
                            @error('code')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mt-3">
                        <div class="form-group">
                            <label for="duration" class="fw-semibold mb-2">
                                <i class="fas fa-clock text-first me-2"></i>Course Duration
                            </label>
                            <select id="duration" name="duration" class="form-input">
                                <option value="">Flexible / Not Specified</option>
                                <option value="45 Days" {{ old('duration') === '45 Days' ? 'selected' : '' }}>45 Days</option>
                                <option value="1 Month" {{ old('duration') === '1 Month' ? 'selected' : '' }}>1 Month</option>
                                <option value="3 Months" {{ old('duration') === '3 Months' ? 'selected' : '' }}>3 Months</option>
                                <option value="6 Months" {{ old('duration') === '6 Months' ? 'selected' : '' }}>6 Months</option>
                                <option value="1 Year" {{ old('duration') === '1 Year' ? 'selected' : '' }}>1 Year</option>
                                <option value="2 Years" {{ old('duration') === '2 Years' ? 'selected' : '' }}>2 Years</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fee" class="fw-semibold mb-2">
                                <i class="fas fa-indian-rupee-sign text-first me-2"></i>Course Fees (INR)
                            </label>
                            <input 
                                type="number" 
                                id="fee"
                                name="fee" 
                                min="0" 
                                step="0.01" 
                                value="{{ old('fee', 0) }}" 
                                class="form-input {{ $errors->has('fee') ? 'is-invalid' : '' }}"
                            />
                            @error('fee')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group checkbox-group mt-4">
                        <label class="checkbox-label" style="cursor: pointer;">
                            <input 
                                type="checkbox" 
                                name="status" 
                                value="1" 
                                {{ old('status', true) ? 'checked' : '' }} 
                                class="checkbox-input"
                            />
                            <span class="fw-semibold">
                                <i class="fas fa-circle-check text-success me-1"></i>Active / Assignable Status
                            </span>
                        </label>
                        <small style="color: var(--muted); margin-left: 28px;" class="d-block mt-1">Allow this course to be assigned to new student registries.</small>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('courses.index') }}" class="button button-secondary">
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

    <!-- Script to simulate dynamic lazy loading and skeleton fading -->
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
