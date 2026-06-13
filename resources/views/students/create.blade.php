@extends('layouts.app')

@section('title', 'Add Student')
@section('page-title', 'Add Student')

@section('content')
    <div class="students-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card" style="max-width: 900px;">
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
            <div class="card premium-form-card" style="max-width: 900px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-user-graduate me-2"></i>Enroll New Student Profile
                    </h3>
                </div>

                <form action="{{ route('students.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    <!-- Section 1: Official Identifiers -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-id-card me-1"></i> Official Identifiers</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="admission_no" class="fw-semibold mb-2">
                                <i class="fas fa-hashtag text-first me-2"></i>Admission Number
                            </label>
                            <input type="text" id="admission_no" name="admission_no" value="{{ old('admission_no') }}" required placeholder="e.g. ADM-2026-001" class="form-input {{ $errors->has('admission_no') ? 'is-invalid' : '' }}" />
                            @error('admission_no')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="roll_no" class="fw-semibold mb-2">
                                <i class="fas fa-list-ol text-first me-2"></i>Roll Number
                            </label>
                            <input type="text" id="roll_no" name="roll_no" value="{{ old('roll_no') }}" placeholder="e.g. 101" class="form-input {{ $errors->has('roll_no') ? 'is-invalid' : '' }}" />
                            @error('roll_no')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="biometric_id" class="fw-semibold mb-2">
                                <i class="fas fa-fingerprint text-first me-2"></i>Biometric Machine ID
                            </label>
                            <input type="text" id="biometric_id" name="biometric_id" value="{{ old('biometric_id') }}" placeholder="e.g. 101" class="form-input {{ $errors->has('biometric_id') ? 'is-invalid' : '' }}" />
                            @error('biometric_id')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="aadhar_number" class="fw-semibold mb-2">
                                <i class="fas fa-fingerprint text-first me-2"></i>Aadhar No (12 Digits)
                            </label>
                            <input type="text" id="aadhar_number" name="aadhar_number" maxlength="12" placeholder="e.g. 123456789012" value="{{ old('aadhar_number') }}" class="form-input {{ $errors->has('aadhar_number') ? 'is-invalid' : '' }}" />
                            @error('aadhar_number')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 2: Personal Details -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-user-circle me-1"></i> Personal Credentials</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="first_name" class="fw-semibold mb-2">
                                <i class="fas fa-font text-first me-2"></i>First Name
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. Sahil" class="form-input {{ $errors->has('first_name') ? 'is-invalid' : '' }}" />
                            @error('first_name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="fw-semibold mb-2">
                                <i class="fas fa-font text-first me-2"></i>Last Name
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="e.g. Sharma" class="form-input {{ $errors->has('last_name') ? 'is-invalid' : '' }}" />
                            @error('last_name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="guardian_name" class="fw-semibold mb-2">
                                <i class="fas fa-person-breastfeeding text-first me-2"></i>Guardian / Father Name
                            </label>
                            <input type="text" id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="e.g. R. K. Sharma" class="form-input {{ $errors->has('guardian_name') ? 'is-invalid' : '' }}" />
                            @error('guardian_name')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email" class="fw-semibold mb-2">
                                <i class="fas fa-envelope text-first me-2"></i>Email Address
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="e.g. student@domain.com" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}" />
                            @error('email')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="phone" class="fw-semibold mb-2">
                                <i class="fas fa-phone text-first me-2"></i>Phone Number
                            </label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. 9876543210" class="form-input {{ $errors->has('phone') ? 'is-invalid' : '' }}" />
                            @error('phone')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="dob" class="fw-semibold mb-2">
                                <i class="fas fa-calendar text-first me-2"></i>Date of Birth
                            </label>
                            <input type="date" id="dob" name="dob" value="{{ old('dob') }}" class="form-input {{ $errors->has('dob') ? 'is-invalid' : '' }}" />
                            @error('dob')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gender" class="fw-semibold mb-2">
                                <i class="fas fa-venus-mars text-first me-2"></i>Gender
                            </label>
                            <select id="gender" name="gender" class="form-input">
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 3: Academic Mapping -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-graduation-cap me-1"></i> Academic Configurations</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="course_id" class="fw-semibold mb-2">
                                <i class="fas fa-book text-first me-2"></i>Select Course
                            </label>
                            <select id="course_id" name="course_id" class="form-input">
                                <option value="">Choose Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-fee="{{ $course->fee ?? 0 }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}{{ $course->code ? ' ('.$course->code.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="course_duration" class="fw-semibold mb-2">
                                <i class="fas fa-clock text-first me-2"></i>Course Duration
                            </label>
                            <select id="course_duration" name="course_duration" class="form-input">
                                <option value="">Choose Duration</option>
                                @foreach($durations as $duration)
                                    <option value="{{ $duration }}" {{ old('course_duration') === $duration ? 'selected' : '' }}>{{ $duration }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="student_type" class="fw-semibold mb-2">
                                <i class="fas fa-user-check text-first me-2"></i>Student Type
                            </label>
                            <select id="student_type" name="student_type" required class="form-input">
                                <option value="Regular (On Campus)" {{ old('student_type', 'Regular (On Campus)') === 'Regular (On Campus)' ? 'selected' : '' }}>Regular (On Campus)</option>
                                <option value="Regular (Internship)" {{ old('student_type') === 'Regular (Internship)' ? 'selected' : '' }}>Regular (Internship)</option>
                                <option value="Online" {{ old('student_type') === 'Online' ? 'selected' : '' }}>Online Course</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-grid mb-4" style="grid-template-columns: 1fr;">
                        <div class="form-group">
                            <label for="admission_date" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-check text-first me-2"></i>Admission Date
                            </label>
                            <input type="date" id="admission_date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" class="form-input" />
                        </div>
                    </div>

                    <!-- Section 4: Financial Configurations -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3 mt-4" style="font-size: 0.75rem;"><i class="fas fa-file-invoice-dollar me-1"></i> Financial Configurations</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group checkbox-group" style="display: flex; align-items: center;">
                            <label class="checkbox-label" style="cursor: pointer;">
                                <input type="hidden" name="prospectus_fee" value="0">
                                <input type="checkbox" id="prospectus_fee" name="prospectus_fee" value="500" {{ old('prospectus_fee', '500') > 0 ? 'checked' : '' }} class="checkbox-input" />
                                <span class="fw-semibold">
                                    <i class="fas fa-file-alt text-first me-1"></i>Apply Prospectus Fee (₹500)
                                </span>
                            </label>
                            @error('prospectus_fee')
                                <small style="color: var(--danger-text);" class="ms-2">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group checkbox-group" style="display: flex; align-items: center;">
                            <label class="checkbox-label" style="cursor: pointer;">
                                <input type="hidden" name="registration_fee" value="0">
                                <input type="checkbox" id="registration_fee" name="registration_fee" value="5000" {{ old('registration_fee', '5000') > 0 ? 'checked' : '' }} class="checkbox-input" />
                                <span class="fw-semibold">
                                    <i class="fas fa-user-plus text-first me-1"></i>Apply Registration Fee (₹5000)
                                </span>
                            </label>
                            @error('registration_fee')
                                <small style="color: var(--danger-text);" class="ms-2">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="discount" class="fw-semibold mb-2">
                                <i class="fas fa-tags text-first me-2"></i>Course Fee Discount
                            </label>
                            <input type="number" step="0.01" id="discount" name="discount" value="{{ old('discount', '0') }}" placeholder="e.g. 1000" class="form-input {{ $errors->has('discount') ? 'is-invalid' : '' }}" />
                            @error('discount')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fee_tenure" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-alt text-first me-2"></i>Payment Tenure
                            </label>
                            <select id="fee_tenure" name="fee_tenure" class="form-input">
                                <option value="">Full Course Fee</option>
                                <option value="1 Month" {{ old('fee_tenure') === '1 Month' ? 'selected' : '' }}>1 Month (Monthly)</option>
                                <option value="3 Months" {{ old('fee_tenure') === '3 Months' ? 'selected' : '' }}>3 Months (Quarterly)</option>
                                <option value="6 Months" {{ old('fee_tenure') === '6 Months' ? 'selected' : '' }}>6 Months (Half Yearly)</option>
                                <option value="1 Year" {{ old('fee_tenure') === '1 Year' ? 'selected' : '' }}>1 Year (Yearly)</option>
                            </select>
                            <small class="text-muted d-block mt-1">If selected, the Course Fee on the first invoice will be split as a partial installment.</small>
                        </div>
                    </div>

                    <div class="form-group-grid mb-4" style="grid-template-columns: 1fr;">
                        <div class="form-group p-4 bg-light rounded border d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark-title"><i class="fas fa-calculator text-first me-2"></i>Invoice Estimates</h6>
                                <p class="mb-0 text-muted small">Generated Invoices Preview</p>
                            </div>
                            <div class="text-end" style="min-width: 300px;">
                                <h4 class="mb-2 fw-bold text-first" id="total_amount_display">₹0.00</h4>
                                <div class="text-muted text-end" id="course_fee_display" style="font-size: 0.85rem;">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-grid mb-4" style="grid-template-columns: 1fr 1fr;">
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label" style="cursor: pointer;">
                                <input 
                                    type="checkbox" 
                                    name="include_registration_invoice" 
                                    id="include_registration_invoice"
                                    value="1" 
                                    checked 
                                    class="checkbox-input"
                                />
                                <span class="fw-semibold">
                                    <i class="fas fa-file-invoice text-first me-1"></i>Generate Registration Fee Invoice
                                </span>
                            </label>
                        </div>
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label" style="cursor: pointer;">
                                <input 
                                    type="checkbox" 
                                    name="include_prospectus_invoice" 
                                    id="include_prospectus_invoice"
                                    value="1" 
                                    checked 
                                    class="checkbox-input"
                                />
                                <span class="fw-semibold">
                                    <i class="fas fa-file-invoice text-first me-1"></i>Generate Prospectus Fee Invoice
                                </span>
                            </label>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <small style="color: var(--muted); margin-left: 28px;" class="d-block mt-1">If checked, a separate admission invoice containing these fees will be generated alongside the regular Course Fee invoice.</small>
                        </div>
                    </div>

                    <!-- Section 5: Address Details -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-map-location-dot me-1"></i> Address Configurations</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="current_address" class="fw-semibold mb-2 d-block">Current Address</label>
                                <textarea id="current_address" name="current_address" placeholder="Enter current residential address..." class="form-input" style="height: 120px; resize: vertical;">{{ old('current_address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label for="permanent_address" class="fw-semibold mb-0">Permanent Address</label>
                                    <div class="d-inline-flex align-items-center gap-2 font-size-0.8rem text-muted cursor-pointer user-select-none" style="font-size: 0.8rem;">
                                        <input type="checkbox" id="same_as_current" style="width: 15px; height: 15px; margin: 0; cursor: pointer; accent-color: var(--first-color);" />
                                        <span id="same_as_current_label" style="cursor: pointer; font-weight: 600;">Same as Current</span>
                                    </div>
                                </div>
                                <textarea id="permanent_address" name="permanent_address" placeholder="Enter permanent residential address..." class="form-input" style="height: 120px; resize: vertical;">{{ old('permanent_address') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback/compatible address input -->
                    <input type="hidden" name="address" id="hidden_address" value="{{ old('address') }}" />

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
                                <i class="fas fa-circle-check text-success me-1"></i>Active Enrollment Status
                            </span>
                        </label>
                        <small style="color: var(--muted); margin-left: 28px;" class="d-block mt-1">Activate the student's status immediately upon registration.</small>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('students.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-save me-2"></i>Enroll Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Javascript mapping address triggers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const curInput = document.getElementById('current_address');
            const permInput = document.getElementById('permanent_address');
            const sameCheck = document.getElementById('same_as_current');
            const sameCheckLabel = document.getElementById('same_as_current_label');
            const hiddenAddr = document.getElementById('hidden_address');

            const updateHidden = () => {
                if(hiddenAddr) hiddenAddr.value = curInput.value;
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

            if(sameCheckLabel) sameCheckLabel.addEventListener('click', handleSameToggle);

            // Lazy loading toggle
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);

            // Fee calculation
            const courseSelect = document.getElementById('course_id');
            const regFeeInput = document.getElementById('registration_fee');
            const prosFeeInput = document.getElementById('prospectus_fee');
            const discountInput = document.getElementById('discount');
            const tenureSelect = document.getElementById('fee_tenure');
            const durationSelect = document.getElementById('course_duration');
            const totalDisplay = document.getElementById('total_amount_display');
            const courseFeeDisplay = document.getElementById('course_fee_display');

            const calculateFees = () => {
                const selectedOption = courseSelect.options[courseSelect.selectedIndex];
                const courseFee = parseFloat(selectedOption?.getAttribute('data-fee')) || 0;
                
                const includeReg = document.getElementById('include_registration_invoice')?.checked;
                const includePros = document.getElementById('include_prospectus_invoice')?.checked;

                const baseRegFee = regFeeInput.type === 'checkbox' ? (regFeeInput.checked ? parseFloat(regFeeInput.value) : 0) : (parseFloat(regFeeInput.value) || 0);
                const baseProsFee = prosFeeInput.type === 'checkbox' ? (prosFeeInput.checked ? parseFloat(prosFeeInput.value) : 0) : (parseFloat(prosFeeInput.value) || 0);

                const regFee = includeReg ? baseRegFee : 0;
                const prosFee = includePros ? baseProsFee : 0;
                const discount = parseFloat(discountInput.value) || 0;
                
                const tenure = tenureSelect ? tenureSelect.value : '';
                const duration = durationSelect ? durationSelect.value.toLowerCase() : '';
                
                let divisor = 1;
                
                if (duration.includes('1 year') || duration.includes('12 month')) {
                    if (tenure === '1 Month') divisor = 12;
                    else if (tenure === '3 Months') divisor = 4;
                    else if (tenure === '6 Months') divisor = 2;
                } else if (duration.includes('6 month')) {
                    if (tenure === '1 Month') divisor = 6;
                    else if (tenure === '3 Months') divisor = 2;
                } else if (duration.includes('3 month')) {
                    if (tenure === '1 Month') divisor = 3;
                }
                
                const installmentCourseFee = (courseFee / divisor).toFixed(2);
                const installmentDiscount = (discount / divisor).toFixed(2);
                const netCourseFee = Math.max(0, installmentCourseFee - installmentDiscount).toFixed(2);
                
                const admissionTotal = (regFee + prosFee).toFixed(2);
                
                courseFeeDisplay.innerHTML = `
                    <div class="mb-1"><strong>Course Installment:</strong> ₹${netCourseFee} <br><small class="text-muted">(₹${installmentCourseFee} fee - ₹${installmentDiscount} discount)</small></div>
                    <div><strong>Admission Fees:</strong> ₹${admissionTotal} <br><small class="text-muted">(Registration + Prospectus)</small></div>
                `;
                
                const combinedTotal = (parseFloat(netCourseFee) + parseFloat(admissionTotal)).toFixed(2);
                totalDisplay.innerText = `Total Today: ₹${combinedTotal}`;
            };

            courseSelect.addEventListener('change', calculateFees);
            regFeeInput.addEventListener('change', calculateFees);
            prosFeeInput.addEventListener('change', calculateFees);
            discountInput.addEventListener('input', calculateFees);
            if(tenureSelect) tenureSelect.addEventListener('change', calculateFees);
            if(durationSelect) durationSelect.addEventListener('change', calculateFees);
            
            const includeRegCheck = document.getElementById('include_registration_invoice');
            const includeProsCheck = document.getElementById('include_prospectus_invoice');
            if (includeRegCheck) includeRegCheck.addEventListener('change', calculateFees);
            if (includeProsCheck) includeProsCheck.addEventListener('change', calculateFees);
            
            // Initial calculation on load
            calculateFees();
        });
    </script>
@endsection
