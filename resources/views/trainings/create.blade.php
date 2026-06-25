@extends('layouts.app')

@section('title', 'New Training Slip')
@section('page-title', 'Generate Training Slip')

@section('content')
    <div class="invoice-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card" style="max-width: 850px;">
                <div class="sk-text heading"></div>
                <div class="form-group-grid">
                    <div><div class="sk-text short"></div><div class="sk-card" style="height: 48px;"></div></div>
                    <div><div class="sk-text short"></div><div class="sk-card" style="height: 48px;"></div></div>
                </div>
                <div class="form-group-grid mt-4">
                    <div><div class="sk-text short"></div><div class="sk-card" style="height: 48px;"></div></div>
                    <div><div class="sk-text short"></div><div class="sk-card" style="height: 48px;"></div></div>
                </div>
                <div class="form-actions-row">
                    <div class="sk-button"></div>
                    <div class="sk-button"></div>
                </div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="card premium-form-card" style="max-width: 850px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-graduation-cap me-2"></i>Generate Training Slip
                    </h3>
                </div>

                <form action="{{ route('trainings.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    <!-- Section 1: Candidate Details -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-user me-1"></i> Candidate Details</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="name" class="fw-semibold mb-2">
                                <i class="fas fa-user text-first me-2"></i>Full Name
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter full name" class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}" />
                            @error('name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="father_name" class="fw-semibold mb-2">
                                <i class="fas fa-user-tie text-first me-2"></i>Father's Name
                            </label>
                            <input type="text" id="father_name" name="father_name" value="{{ old('father_name') }}" placeholder="Enter father's name" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="email" class="fw-semibold mb-2">
                                <i class="fas fa-envelope text-first me-2"></i>Email Address
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Enter email address" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}" />
                            @error('email')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="college" class="fw-semibold mb-2">
                                <i class="fas fa-school text-first me-2"></i>College / Institution
                            </label>
                            <input type="text" id="college" name="college" value="{{ old('college') }}" placeholder="Enter college or institution name" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="mobile" class="fw-semibold mb-2">
                                <i class="fas fa-phone text-first me-2"></i>Mobile Number
                            </label>
                            <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" required placeholder="Enter mobile number" class="form-input {{ $errors->has('mobile') ? 'is-invalid' : '' }}" />
                            @error('mobile')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="course_id" class="fw-semibold mb-2">
                                <i class="fas fa-book text-first me-2"></i>Course
                            </label>
                            <select id="course_id" name="course_id" required class="form-input {{ $errors->has('course_id') ? 'is-invalid' : '' }}">
                                <option value="">-- Select Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 2: Training Details -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-sliders me-1"></i> Training Details</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="duration" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-alt text-first me-2"></i>Duration
                            </label>
                            <select id="duration" name="duration" required class="form-input {{ $errors->has('duration') ? 'is-invalid' : '' }}">
                                <option value="">-- Select Duration --</option>
                                @foreach($durations as $duration)
                                    <option value="{{ $duration }}" {{ old('duration') == $duration ? 'selected' : '' }}>{{ $duration }}</option>
                                @endforeach
                            </select>
                            @error('duration')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="fees" class="fw-semibold mb-2">
                                <i class="fas fa-coins text-first me-2"></i>Fees (INR)
                            </label>
                            <input type="number" id="fees" name="fees" step="0.01" value="{{ old('fees', 0) }}" required placeholder="0.00" class="form-input {{ $errors->has('fees') ? 'is-invalid' : '' }}" />
                            @error('fees')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 3: Payment Details -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-credit-card me-1"></i> Payment Details</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="payment_method" class="fw-semibold mb-2">
                                <i class="fas fa-wallet text-first me-2"></i>Payment Method
                            </label>
                            <select id="payment_method" name="payment_method" required class="form-input {{ $errors->has('payment_method') ? 'is-invalid' : '' }}">
                                <option value="Cash" {{ old('payment_method', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Online" {{ old('payment_method') === 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="Cheque" {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                            @error('payment_method')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="payment_date" class="fw-semibold mb-2">
                                <i class="fas fa-calendar text-first me-2"></i>Payment Date
                            </label>
                            <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="form-input {{ $errors->has('payment_date') ? 'is-invalid' : '' }}" />
                            @error('payment_date')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('trainings.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-graduation-cap me-2"></i>Generate Slip
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
