@extends('layouts.app')

@section('title', 'Create Fee Receipt')
@section('page-title', 'Generate Student Fee Receipt')

@section('content')
    <div class="invoice-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="premium-form-card" style="max-width: 850px;">
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
            <div class="card premium-form-card" style="max-width: 850px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Generate Student Fee Receipt
                    </h3>
                </div>

                <form action="{{ route('fee_invoices.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    <!-- Section 1: Academic Identifier -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-user-graduate me-1"></i> Student Selection</h5>
                    <div class="form-group mb-4" style="position: relative;">
                        <label for="student_id" class="fw-semibold mb-2">
                            <i class="fas fa-user text-first me-2"></i>Select Student Record
                        </label>
                        <div style="position: relative;">
                            <span class="position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="fas fa-graduation-cap text-muted"></i></span>
                            <select id="student_id" name="student_id" required class="form-input {{ $errors->has('student_id') ? 'is-invalid' : '' }}" style="padding-left: 38px;">
                                <option value="">-- Choose student to invoice --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->admission_no }} - {{ $student->first_name }} {{ $student->last_name }} ({{ $student->course?->name ?? 'No Course' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('student_id')
                            <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Section 1.5: Dynamic Payment History -->
                    <div id="payment-history-container" class="mb-4">
                        <!-- Populated by JS -->
                    </div>

                    <!-- Section 2: Receipt Setup -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-sliders me-1"></i> Receipt Details</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="invoice_no" class="fw-semibold mb-2">
                                <i class="fas fa-hashtag text-first me-2"></i>Receipt No (Optional)
                            </label>
                            <input type="text" id="invoice_no" name="invoice_no" value="{{ old('invoice_no') }}" placeholder="Auto-generated if left empty" class="form-input" />
                        </div>
                        <div class="form-group">
                            <label for="tenure_mode" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-alt text-first me-2"></i>Tenure Mode
                            </label>
                            <select id="tenure_mode" name="tenure_mode" class="form-input" onchange="populateFeeItems()">
                                <option value="Full" {{ old('tenure_mode') == 'Full' ? 'selected' : '' }}>Full Payment</option>
                                <option value="Monthly" {{ old('tenure_mode') == 'Monthly' ? 'selected' : '' }}>Monthly Installment</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fee_category_select" class="fw-semibold mb-2">
                                <i class="fas fa-tags text-first me-2"></i>Fee Category
                            </label>
                            <select id="fee_category_select" name="fee_category" class="form-input" onchange="toggleOtherFeeCategory()">
                                <option value="" {{ old('fee_category') === '' ? 'selected' : '' }}>-- Auto-generate from items --</option>
                                <option value="Regular Fees" {{ old('fee_category') == 'Regular Fees' ? 'selected' : '' }}>Regular Fees</option>
                                <option value="Monthly Fees" {{ old('fee_category') == 'Monthly Fees' ? 'selected' : '' }}>Monthly Fees</option>
                                <option value="Fine" {{ old('fee_category') == 'Fine' ? 'selected' : '' }}>Fine</option>
                                <option value="Seminar" {{ old('fee_category') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                                <option value="Other" {{ (!in_array(old('fee_category'), ['Regular Fees', 'Monthly Fees', 'Fine', 'Seminar', null, ''])) ? 'selected' : '' }}>Other</option>
                            </select>
                            <input type="text" id="fee_category_other" name="fee_category_other" value="{{ (!in_array(old('fee_category'), ['Regular Fees', 'Monthly Fees', 'Fine', 'Seminar', null, ''])) ? old('fee_category') : '' }}" class="form-input mt-2" style="{{ (!in_array(old('fee_category'), ['Regular Fees', 'Monthly Fees', 'Fine', 'Seminar', null, ''])) ? 'display: block;' : 'display: none;' }}" placeholder="Enter custom fee category" />
                        </div>
                    </div>

                    <!-- Section: Fee Itemization -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-list-ol me-1"></i> Fee Items Breakdown</h5>
                    <div class="card p-4 mb-4 border" style="background: var(--surface); border-radius: 12px;">
                        <p class="text-muted mb-3" style="font-size: 0.85rem;">Select which fee types to include in this receipt slip. You can adjust the amounts or add custom fee rows.</p>
                        
                        <div class="table-responsive">
                            <table class="table align-middle table-sm" id="fee-breakdown-table" style="font-size: 0.9rem; width: 100%;">
                                <thead>
                                    <tr class="table-light">
                                        <th style="width: 10%;" class="text-center">Select</th>
                                        <th style="width: 55%;">Fee Category / Name</th>
                                        <th style="width: 25%;">Amount (INR)</th>
                                        <th style="width: 10%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="fee-items-tbody">
                                    <!-- Dynamic rows will be generated here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-start mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm px-3" onclick="addCustomFeeRow()" style="border-radius: 8px; font-weight: 600; border: 1px solid var(--primary); color: var(--primary); background: transparent;">
                                <i class="fas fa-plus me-1"></i> Add Custom Fee Row
                            </button>
                        </div>
                    </div>

                    <!-- Section 3: Amounts -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-coins me-1"></i> Billing Summary (INR)</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="total_amount" class="fw-semibold mb-2">
                                <i class="fas fa-money-bill-wave text-first me-2"></i>Total Amount
                            </label>
                            <input type="number" id="total_amount" name="total_amount" step="0.01" value="{{ old('total_amount', 0) }}" required readonly placeholder="0.00" class="form-input {{ $errors->has('total_amount') ? 'is-invalid' : '' }}" style="background-color: var(--border);" />
                            @error('total_amount')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="paid_amount" class="fw-semibold mb-2">
                                <i class="fas fa-check-circle text-first me-2"></i>Amount Paid Already
                            </label>
                            <input type="number" id="paid_amount" name="paid_amount" step="0.01" value="{{ old('paid_amount', 0) }}" required placeholder="0.00" class="form-input {{ $errors->has('paid_amount') ? 'is-invalid' : '' }}" />
                            @error('paid_amount')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="discount" class="fw-semibold mb-2">
                                <i class="fas fa-percent text-first me-2"></i>Discount Applied
                            </label>
                            <input type="number" id="discount" name="discount" step="0.01" value="{{ old('discount', 0) }}" placeholder="0.00" class="form-input" />
                        </div>
                        <div class="form-group">
                            <label for="fine" class="fw-semibold mb-2">
                                <i class="fas fa-circle-exclamation text-first me-2"></i>Late Fine / Penalty
                            </label>
                            <input type="number" id="fine" name="fine" step="0.01" value="{{ old('fine', 0) }}" placeholder="0.00" class="form-input" />
                        </div>
                    </div>

                    <!-- Section 4: Payments and Status -->
                    <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-wallet me-1"></i> Payment Verification</h5>
                    <div class="form-group-grid mb-4">
                        <div class="form-group">
                            <label for="payment_date" class="fw-semibold mb-2">
                                <i class="fas fa-calendar text-first me-2"></i>Transaction Date
                            </label>
                            <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="form-input {{ $errors->has('payment_date') ? 'is-invalid' : '' }}" />
                            @error('payment_date')
                                <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="payment_method" class="fw-semibold mb-2">
                                <i class="fas fa-credit-card text-first me-2"></i>Payment Method
                            </label>
                            <select id="payment_method" name="payment_method" class="form-input" onchange="toggleOnlineFields()">
                                <option value="Cash" {{ old('payment_method', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Online" {{ old('payment_method') === 'Online' ? 'selected' : '' }}>Online</option>
                            </select>
                        </div>
                    </div>

                    <!-- Online Payment Fields (Transaction ID and UTR No) -->
                    <div class="form-group-grid mb-4" id="online-details-grid" style="display: none;">
                        <div class="form-group">
                            <label for="transaction_id" class="fw-semibold mb-2">
                                <i class="fas fa-hashtag text-first me-2"></i>Transaction ID
                            </label>
                            <input type="text" id="transaction_id" name="transaction_id" value="{{ old('transaction_id') }}" placeholder="Enter transaction reference ID" class="form-input" />
                        </div>
                        <div class="form-group">
                            <label for="utr_no" class="fw-semibold mb-2">
                                <i class="fas fa-receipt text-first me-2"></i>UTR Number
                            </label>
                            <input type="text" id="utr_no" name="utr_no" value="{{ old('utr_no') }}" placeholder="Enter 12-digit UTR number" class="form-input" />
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="status" class="fw-semibold mb-2">
                            <i class="fas fa-circle-info text-first me-2"></i>Receipt Settlement Status
                        </label>
                        <select id="status" name="status" required class="form-input {{ $errors->has('status') ? 'is-invalid' : '' }}">
                            <option value="">-- Choose Status --</option>
                            <option value="Paid" {{ old('status') === 'Paid' ? 'selected' : '' }}>✓ Paid</option>
                            <option value="Partial" {{ old('status') === 'Partial' ? 'selected' : '' }}>◐ Partially Paid</option>
                            <option value="Unpaid" {{ old('status', 'Unpaid') === 'Unpaid' ? 'selected' : '' }}>✗ Unpaid / Outstanding</option>
                        </select>
                        @error('status')
                            <small style="color: var(--danger-text);" class="mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="remarks" class="fw-semibold mb-2">
                            <i class="fas fa-sticky-note text-first me-2"></i>Remarks / Notes
                        </label>
                        <textarea id="remarks" name="remarks" placeholder="Add optional transaction references, memo, etc..." class="form-input" style="min-height: 80px; resize: vertical; padding: 12px;">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="form-actions-row">
                        <a href="{{ route('fee_invoices.index') }}" class="button button-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="button button-primary">
                            <i class="fas fa-file-invoice me-2"></i>Generate Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and async fetches -->
    <script>
        const oldFeeItems = {!! old('fee_items') ? json_encode(old('fee_items')) : 'null' !!};

        function getMonthsFromDuration(duration) {
            if (!duration) return 1;
            let text = duration.toLowerCase();
            if (text.includes('45 days')) return 1.5;
            if (text.includes('1 month')) return 1;
            if (text.includes('6 months')) return 6;
            if (text.includes('1 year')) return 12;
            return 1;
        }

        async function populateFeeItems() {
            const studentId = document.getElementById('student_id').value;
            const tenureMode = document.getElementById('tenure_mode').value;
            const tbody = document.getElementById('fee-items-tbody');
            const historyContainer = document.getElementById('payment-history-container');
            
            tbody.innerHTML = '';
            if (historyContainer) historyContainer.innerHTML = '';
            
            if (!studentId) {
                updateFeeItemsNamesAndTotal();
                return;
            }
            
            try {
                if (historyContainer) {
                    historyContainer.innerHTML = '<div class="text-center p-3 text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading student fee profile...</div>';
                }

                const response = await fetch(`/api/students/${studentId}/fee-info`);
                const json = await response.json();
                
                if (!json.success) return;
                
                const data = json.student_data;
                const pastPayments = json.past_payments || [];
                const attendanceFine = parseFloat(json.attendance_fine) || 0;
                const fineDetails = json.fine_details || '';

                // Render Payment History
                if (historyContainer) {
                    if (pastPayments.length > 0) {
                        let historyHtml = `
                            <h5 class="fw-bold text-muted uppercase-bold mb-3" style="font-size: 0.75rem;"><i class="fas fa-clock-rotate-left me-1"></i> Recent Payment History</h5>
                            <div class="table-responsive border rounded" style="background: var(--surface);">
                                <table class="table table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Category</th>
                                            <th class="text-end">Paid (INR)</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;
                        pastPayments.forEach(payment => {
                            let badgeClass = payment.status === 'Paid' ? 'bg-success' : (payment.status === 'Partial' ? 'bg-warning' : 'bg-danger');
                            historyHtml += `
                                        <tr>
                                            <td class="text-muted">${payment.date}</td>
                                            <td class="fw-semibold">${payment.invoice_no}</td>
                                            <td>${payment.category || '-'}</td>
                                            <td class="text-end fw-semibold text-dark-title">₹${payment.paid}</td>
                                            <td class="text-center"><span class="badge ${badgeClass}">${payment.status}</span></td>
                                        </tr>
                            `;
                        });
                        historyHtml += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                        historyContainer.innerHTML = historyHtml;
                    } else {
                        historyContainer.innerHTML = `
                            <div class="p-3 border rounded text-center text-muted" style="background: var(--surface); font-size: 0.85rem;">
                                <i class="fas fa-info-circle me-1"></i> No past payment records found for this student.
                            </div>
                        `;
                    }
                }

                // Calculate adjusted course fee based on tenure mode
                let adjustedCourseFee = parseFloat(data.course_fee) || 0;
                if (tenureMode === 'Monthly' && adjustedCourseFee > 0) {
                    const months = getMonthsFromDuration(data.course_duration);
                    adjustedCourseFee = adjustedCourseFee / months;
                }
                
                if (oldFeeItems && oldFeeItems.length > 0) {
                    oldFeeItems.forEach((item, index) => {
                        const isDefault = ['Course Fee', 'Registration Fee', 'Prospectus Fee', 'Monthly Course Fee', 'Attendance Fine'].includes(item.category);
                        if (isDefault) {
                            tbody.appendChild(createDefaultRow(item.category, item.amount, true));
                        } else {
                            tbody.appendChild(createCustomRow(item.category, item.amount, true));
                        }
                    });
                    const categoriesInOld = oldFeeItems.map(item => item.category);
                    const feeLabel = tenureMode === 'Monthly' ? 'Monthly Course Fee' : 'Course Fee';
                    if (!categoriesInOld.includes(feeLabel) && adjustedCourseFee > 0) {
                        tbody.appendChild(createDefaultRow(feeLabel, adjustedCourseFee, false));
                    }
                } else {
                    if (adjustedCourseFee > 0) {
                        const feeLabel = tenureMode === 'Monthly' ? 'Monthly Course Fee' : 'Course Fee';
                        tbody.appendChild(createDefaultRow(feeLabel, adjustedCourseFee, true));
                    }
                    
                    if (attendanceFine > 0) {
                        tbody.appendChild(createDefaultRow('Attendance Fine', attendanceFine, true));
                        const remarksInput = document.getElementById('remarks');
                        if (remarksInput && !remarksInput.value) {
                            remarksInput.value = `Auto-calculated attendance fine: ${fineDetails}`;
                        }
                    }
                    
                    if (parseFloat(data.discount) > 0) {
                        document.getElementById('discount').value = parseFloat(data.discount).toFixed(2);
                    }
                }
                
                updateFeeItemsNamesAndTotal();
            } catch (err) {
                console.error("Failed to fetch student fee info:", err);
                if (historyContainer) historyContainer.innerHTML = '<div class="text-danger p-3"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load history.</div>';
                updateFeeItemsNamesAndTotal();
            }
        }

        function createDefaultRow(category, amount, checked) {
            const tr = document.createElement('tr');
            tr.className = 'fee-row default-row';
            tr.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="fee-include-chk form-check-input" ${checked ? 'checked' : ''} onchange="updateFeeItemsNamesAndTotal()">
                </td>
                <td>
                    <span class="fw-semibold text-dark-title">${category}</span>
                    <input type="hidden" class="fee-category-input" value="${category}">
                </td>
                <td>
                    <input type="number" step="0.01" class="form-input py-1 fee-amount-input" value="${parseFloat(amount).toFixed(2)}" oninput="updateFeeItemsNamesAndTotal()" style="padding: 6px 10px; font-size: 0.85rem; height: auto;">
                </td>
                <td class="text-center">
                    <span class="text-muted" style="font-size: 0.8rem;">-</span>
                </td>
            `;
            return tr;
        }

        function createCustomRow(category = '', amount = '', checked = true) {
            const tr = document.createElement('tr');
            tr.className = 'fee-row custom-row';
            tr.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="fee-include-chk form-check-input" ${checked ? 'checked' : ''} onchange="updateFeeItemsNamesAndTotal()">
                </td>
                <td>
                    <input type="text" class="form-input py-1 fee-category-input" placeholder="e.g. Exam Fee" value="${category}" required oninput="updateFeeItemsNamesAndTotal()" style="padding: 6px 10px; font-size: 0.85rem; height: auto;">
                </td>
                <td>
                    <input type="number" step="0.01" class="form-input py-1 fee-amount-input" placeholder="0.00" value="${amount}" required oninput="updateFeeItemsNamesAndTotal()" style="padding: 6px 10px; font-size: 0.85rem; height: auto;">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="removeCustomFeeRow(this)" style="border-radius: 6px; width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-color: rgba(220, 53, 69, 0.3) !important; color: #dc3545 !important;">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            return tr;
        }

        function addCustomFeeRow() {
            const tbody = document.getElementById('fee-items-tbody');
            tbody.appendChild(createCustomRow('', '', true));
            updateFeeItemsNamesAndTotal();
        }

        function removeCustomFeeRow(btn) {
            const row = btn.closest('tr');
            row.remove();
            updateFeeItemsNamesAndTotal();
        }

        function updateFeeItemsNamesAndTotal() {
            const rows = document.querySelectorAll('#fee-items-tbody .fee-row');
            let total = 0;
            let index = 0;
            
            rows.forEach(row => {
                const chk = row.querySelector('.fee-include-chk');
                const catInput = row.querySelector('.fee-category-input');
                const amtInput = row.querySelector('.fee-amount-input');
                
                if (chk.checked) {
                    // Set names for submission
                    catInput.name = `fee_items[${index}][category]`;
                    amtInput.name = `fee_items[${index}][amount]`;
                    
                    const amount = parseFloat(amtInput.value) || 0;
                    total += amount;
                    index++;
                    
                    // Style active row slightly differently
                    row.style.opacity = '1';
                } else {
                    // Remove names so they aren't submitted
                    catInput.removeAttribute('name');
                    amtInput.removeAttribute('name');
                    
                    row.style.opacity = '0.5';
                }
            });
            
            document.getElementById('total_amount').value = total.toFixed(2);
        }

        document.getElementById('student_id').addEventListener('change', populateFeeItems);

        function toggleOnlineFields() {
            const method = document.getElementById('payment_method').value;
            const onlineGrid = document.getElementById('online-details-grid');
            if(onlineGrid) {
                if(method === 'Online') {
                    onlineGrid.style.display = 'grid';
                } else {
                    onlineGrid.style.display = 'none';
                    document.getElementById('transaction_id').value = '';
                    document.getElementById('utr_no').value = '';
                }
            }
        }

        function toggleOtherFeeCategory() {
            const select = document.getElementById('fee_category_select');
            const otherInput = document.getElementById('fee_category_other');
            if (select.value === 'Other') {
                select.removeAttribute('name');
                otherInput.setAttribute('name', 'fee_category');
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                select.setAttribute('name', 'fee_category');
                otherInput.removeAttribute('name');
                otherInput.style.display = 'none';
                otherInput.required = false;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            // Populate fee items if a student is already selected (e.g. old inputs)
            populateFeeItems();
            
            // Run toggle once to sync old value on error reload
            toggleOnlineFields();
            toggleOtherFeeCategory();

            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection
