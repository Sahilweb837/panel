@extends('layouts.app')

@section('title', 'New Training Slip')
@section('page-title', 'Generate Training Slip')

@section('content')
    <div class="invoice-container">
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

        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="card premium-form-card" style="max-width: 850px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-graduation-cap me-2"></i>Generate Training Slip
                    </h3>
                </div>

                <form action="{{ route('trainings.store') }}" method="POST" class="form-card p-0">
                    @csrf

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
                    </div>

                    <h5 class="fw-bold text-muted uppercase-bold mb-3 mt-4" style="font-size: 0.75rem;"><i class="fas fa-sliders me-1"></i> Training Details</h5>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="course_select" class="fw-semibold mb-2">
                                <i class="fas fa-book text-first me-2"></i>Course
                            </label>
                            <select id="course_select" name="course_select" onchange="handleCourseSelect()" required class="form-input {{ $errors->has('course_select') ? 'is-invalid' : '' }}">
                                <option value="">-- Select Course --</option>
                                @foreach($trainingCourses as $tc)
                                    <option value="{{ $tc->name }}" data-fee="{{ $tc->fee }}" {{ old('course_select') === $tc->name ? 'selected' : '' }}>{{ $tc->name }} ({{ $tc->duration }}) - ₹{{ number_format($tc->fee, 2) }}</option>
                                @endforeach
                                <option value="Other" {{ old('course_select') === 'Other' ? 'selected' : '' }}>Drop Other Training Course</option>
                            </select>
                            @error('course_select')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div id="manual-course-wrapper" class="form-group-grid mb-4" style="display: none;">
                        <div class="form-group">
                            <label for="manual_course_name" class="fw-semibold mb-2">
                                <i class="fas fa-book text-first me-2"></i>Enter Course Name
                            </label>
                            <input type="text" id="manual_course_name" name="manual_course_name" value="{{ old('manual_course_name') }}" placeholder="Enter course name" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="duration" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-alt text-first me-2"></i>Duration
                            </label>
                            <select id="duration" name="duration" required class="form-input {{ $errors->has('duration') ? 'is-invalid' : '' }}">
                                <option value="">-- Select Duration --</option>
                                <option value="28 Days" {{ old('duration') == '28 Days' ? 'selected' : '' }}>28 Days</option>
                                <option value="45 Days" {{ old('duration') == '45 Days' ? 'selected' : '' }}>45 Days</option>
                                <option value="1 Month" {{ old('duration') == '1 Month' ? 'selected' : '' }}>1 Month</option>
                                <option value="3 Months" {{ old('duration') == '3 Months' ? 'selected' : '' }}>3 Months</option>
                                <option value="6 Months" {{ old('duration') == '6 Months' ? 'selected' : '' }}>6 Months</option>
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

                    <h5 class="fw-bold text-muted uppercase-bold mb-3 mt-4" style="font-size: 0.75rem;"><i class="fas fa-credit-card me-1"></i> Payment Details</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="payment_method" class="fw-semibold mb-2">
                                <i class="fas fa-wallet text-first me-2"></i>Payment Method
                            </label>
                            <select id="payment_method" name="payment_method" required class="form-input {{ $errors->has('payment_method') ? 'is-invalid' : '' }}">
                                <option value="Cash" {{ old('payment_method', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Online" {{ old('payment_method') === 'Online' ? 'selected' : '' }}>Online</option>
                                <option value="Cheque" {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="UPI" {{ old('payment_method') === 'UPI' ? 'selected' : '' }}>UPI</option>
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

                    <div id="upi-details-grid" class="form-group-grid mb-4" style="display: none;">
                        <div class="form-group">
                            <label for="upi_transaction_id" class="fw-semibold mb-2">
                                <i class="fas fa-mobile-screen text-first me-2"></i>UPI Transaction ID
                            </label>
                            <input type="text" id="upi_transaction_id" name="upi_transaction_id" value="{{ old('upi_transaction_id') }}" placeholder="Enter UPI transaction reference ID" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="status" class="fw-semibold mb-2">
                            <i class="fas fa-circle-info text-first me-2"></i>Payment Status
                        </label>
                        <select id="status" name="status" required class="form-input {{ $errors->has('status') ? 'is-invalid' : '' }}">
                            <option value="">-- Select Status --</option>
                            <option value="Paid" {{ old('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Unpaid" {{ old('status', 'Unpaid') === 'Unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                        @error('status')
                            <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                        @enderror
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
        function handleCourseSelect() {
            const courseSelect = document.getElementById('course_select');
            const manualWrapper = document.getElementById('manual-course-wrapper');

            if (courseSelect.value === 'Other') {
                manualWrapper.style.display = 'grid';
                document.getElementById('fees').value = '0';
            } else {
                manualWrapper.style.display = 'none';
                const manualInput = document.getElementById('manual_course_name');
                if (manualInput) {
                    manualInput.value = '';
                }
                
                // Populate fee based on selected course
                const selectedOption = courseSelect.options[courseSelect.selectedIndex];
                const fee = selectedOption ? selectedOption.getAttribute('data-fee') : 0;
                document.getElementById('fees').value = fee || 0;
            }
        }


        function toggleUpiFields() {
            const method = document.getElementById('payment_method').value;
            const upiGrid = document.getElementById('upi-details-grid');
            if (upiGrid) {
                upiGrid.style.display = method === 'UPI' ? 'grid' : 'none';
                if (method !== 'UPI') {
                    document.getElementById('upi_transaction_id').value = '';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');

            handleCourseSelect();
            toggleUpiFields();
            document.getElementById('payment_method').addEventListener('change', toggleUpiFields);

            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection