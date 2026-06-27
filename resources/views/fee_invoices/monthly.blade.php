@extends('layouts.app')

@section('title', 'Monthly Fee Collection')
@section('page-title', 'Monthly Fee Collection')

@section('content')
    <div class="invoice-container">
        <div id="page-content">
            <div class="card premium-form-card" style="max-width: 950px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-calendar-alt me-2"></i>Monthly Fee Collection
                    </h3>
                    <p class="text-muted mb-0 small mt-1">Select a student and month to collect monthly course fee with applicable fines</p>
                </div>

                <form action="{{ route('fee_invoices.store') }}" method="POST" class="form-card p-0" id="monthlyFeeForm">
                    @csrf

                    <!-- Student Selection & Month/Year -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded border">
                        <div class="col-md-5">
                            <label for="student_id" class="fw-semibold mb-2 d-block">
                                <i class="fas fa-user-graduate text-first me-2"></i>Select Student
                            </label>
                            <select id="student_id" name="student_id" required class="form-input" onchange="loadMonthlyStatus()">
                                <option value="">-- Choose Student --</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                                        {{ $student->admission_no }} - {{ $student->first_name }} {{ $student->last_name }} ({{ $student->course?->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="billing_month" class="fw-semibold mb-2 d-block">
                                <i class="fas fa-calendar text-first me-2"></i>Month
                            </label>
                            <select id="billing_month" name="billing_month" required class="form-input" onchange="loadMonthlyStatus()">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="billing_year" class="fw-semibold mb-2 d-block">
                                <i class="fas fa-calendar-alt text-first me-2"></i>Year
                            </label>
                            <select id="billing_year" name="billing_year" required class="form-input" onchange="loadMonthlyStatus()">
                                @for($y = 2024; $y <= 2030; $y++)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" onclick="loadMonthlyStatus()" class="button button-secondary w-100">
                                <i class="fas fa-sync-alt me-1"></i>Load Status
                            </button>
                        </div>
                    </div>

                    <!-- Monthly Fee Status Panel -->
                    <div id="monthly-status-panel" style="display: none;">
                        <div class="card mb-4 border" style="background: var(--surface); border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-file-invoice-dollar me-2 text-first"></i>Monthly Fee for <span id="display-month-year"></span>
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1" id="student-name-display">Student Name</h5>
                                        <p class="text-muted mb-0 small" id="student-course-display">Course - Tenure</p>
                                    </div>
                                    <span class="badge bg-{{ $monthlyStatus['existing_invoice'] ? ($monthlyStatus['existing_invoice']['status'] === 'Paid' ? 'success' : ($monthlyStatus['existing_invoice']['status'] === 'Partial' ? 'warning' : 'danger')) : 'info' }} border" id="status-badge">
                                        {{ $monthlyStatus['existing_invoice'] ? $monthlyStatus['existing_invoice']['status'] : 'Not Generated' }}
                                    </span>
                                </div>

                                @if($monthlyStatus['existing_invoice'])
                                    <div class="alert alert-light border mb-3">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <strong>Receipt No:</strong><br>
                                                <span class="fw-bold" id="existing-invoice-no">{{ $monthlyStatus['existing_invoice']['invoice_no'] }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Status:</strong><br>
                                                <span class="fw-bold" id="existing-status">{{ $monthlyStatus['existing_invoice']['status'] }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Paid:</strong><br>
                                                <span class="text-success fw-bold" id="existing-paid">₹{{ number_format($monthlyStatus['existing_invoice']['paid_amount'], 2) }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Due:</strong><br>
                                                <span class="text-danger fw-bold" id="existing-due">₹{{ number_format($monthlyStatus['existing_invoice']['due_amount'], 2) }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Date:</strong><br>
                                                <span class="fw-bold" id="existing-date">{{ $monthlyStatus['existing_invoice']['payment_date'] }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Fine Paid:</strong><br>
                                                <span class="fw-bold" id="existing-fine-paid">₹0</span>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between">
                                            <span><strong>Action:</strong></span>
                                            <span class="text-muted small">This month's fee already exists. You can create a new payment entry below.</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info border mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>New Monthly Fee:</strong> No fee record exists for this month. Create the first payment below.
                                    </div>
                                @endif

                                <!-- Fee Breakdown -->
                                <div class="table-responsive mb-3">
                                    <table class="table table-sm align-middle mb-0" style="font-size: 0.9rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 45%;">Fee Component</th>
                                                <th style="width: 20%;">Amount (₹)</th>
                                                <th style="width: 35%;">Pay Now (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="monthly-fee-rows">
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Total Summary -->
                                <div class="mb-3 p-3 bg-light rounded border">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-6">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold text-first">Monthly Course Fee:</span>
                                                <span class="fw-bold" id="display-monthly-fee">₹0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-success">Discount:</span>
                                                <span class="text-success fw-bold" id="display-discount">- ₹0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-danger">Late Fine:</span>
                                                <span class="text-danger fw-bold" id="display-late-fine">+ ₹0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-warning">Attendance Fine:</span>
                                                <span class="text-warning fw-bold" id="display-attendance-fine">+ ₹0</span>
                                            </div>
                                            <hr class="my-2">
                                            <div class="d-flex justify-content-between">
                                                <strong class="text-first">Total Amount:</strong>
                                                <strong class="text-first" id="display-total-amount">₹0</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-white rounded border">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong>Amount to Pay Now</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <input type="number" step="0.01" class="form-input py-2" id="pay_now_amount" value="0" style="max-width: 150px; text-align: right; font-size: 1.1rem; font-weight: 700;" oninput="updatePayNow()" />
                                                </div>
                                                <small class="text-muted">Enter amount received from student</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fine Details (if any) -->
                                <div id="fine-details-box" class="mb-3" style="display: none;">
                                    <div class="alert alert-warning border">
                                        <strong><i class="fas fa-exclamation-triangle me-1"></i> Fine Details:</strong>
                                        <div class="mt-2" id="fine-details-content"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="card mb-4 border" style="background: var(--surface); border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-money-bill-wave me-2 text-first"></i>Payment Details
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label for="payment_date" class="fw-semibold mb-2 d-block">
                                            <i class="fas fa-calendar text-first me-2"></i>Payment Date
                                        </label>
                                        <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required class="form-input" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="payment_method" class="fw-semibold mb-2 d-block">
                                            <i class="fas fa-credit-card text-first me-2"></i>Payment Mode
                                        </label>
                                        <select id="payment_method" name="payment_method" class="form-input" onchange="toggleTxnFields()">
                                            <option value="Cash">Cash</option>
                                            <option value="Online">Online</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="status" class="fw-semibold mb-2 d-block">
                                            <i class="fas fa-toggle-on text-first me-2"></i>Payment Status
                                        </label>
                                        <select id="status" name="status" class="form-input">
                                            <option value="Paid">Paid</option>
                                            <option value="Partial">Partial</option>
                                            <option value="Unpaid">Unpaid</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3" id="txn-fields" style="display: none;">
                                    <div class="col-md-6">
                                        <label for="transaction_id" class="fw-semibold mb-2">Transaction / Ref No</label>
                                        <input type="text" id="transaction_id" name="transaction_id" class="form-input" placeholder="Enter transaction ID" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="utr_no" class="fw-semibold mb-2">UTR Number</label>
                                        <input type="text" id="utr_no" name="utr_no" class="form-input" placeholder="UTR No (if applicable)" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="remarks" class="fw-semibold mb-2">
                                        <i class="fas fa-sticky-note text-first me-2"></i>Remarks (Optional)
                                    </label>
                                    <textarea id="remarks" name="remarks" class="form-input" style="height: 70px; resize: vertical;" placeholder="Any note about this payment..."></textarea>
                                </div>

                                <!-- Hidden fields for form submission -->
                                <input type="hidden" name="fee_category" id="fee_category_hidden" value="Monthly Fee" />
                                <input type="hidden" name="total_amount" id="total_amount_hidden" value="0" />
                                <input type="hidden" name="paid_amount" id="paid_amount_hidden" value="0" />
                                <input type="hidden" name="discount" id="discount_hidden" value="0" />
                                <input type="hidden" name="fine" id="fine_hidden" value="0" />
                                <input type="hidden" name="fee_items_json" id="fee_items_json" value="" />

                                <div class="form-actions-row">
                                    <a href="{{ route('fee_invoices.index') }}" class="button button-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Receipts
                                    </a>
                                    <button type="button" onclick="submitMonthlyFee()" class="button button-primary">
                                        <i class="fas fa-check-circle me-2"></i>Save & Print Receipt
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="empty-state" class="text-center py-5" style="display: {{ $studentId ? 'none' : 'block' }};">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Select a student and month to begin</h4>
                        <p class="text-muted">Choose a student, select the billing month/year, and click "Load Status" to see the fee breakdown and collect payment.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentMonthlyStatus = null;

        function toggleTxnFields() {
            const method = document.getElementById('payment_method')?.value;
            const txnDiv = document.getElementById('txn-fields');
            if (txnDiv) {
                txnDiv.style.display = (method === 'Online') ? 'flex' : 'none';
            }
        }

        function loadMonthlyStatus() {
            const studentId = document.getElementById('student_id').value;
            const month = document.getElementById('billing_month').value;
            const year = document.getElementById('billing_year').value;
            const panel = document.getElementById('monthly-status-panel');
            const emptyState = document.getElementById('empty-state');

            if (!studentId) {
                panel.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            panel.style.display = 'none';
            emptyState.style.display = 'none';

            // Show loading
            document.getElementById('monthly-fee-rows').innerHTML = '<tr><td colspan="3" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i>Loading fee details...</td></tr>';

            fetch(`/api/students/${studentId}/monthly-status?month=${month}&year=${year}`)
                .then(res => res.json())
                .then(json => {
                    if (!json.success) {
                        throw new Error('Failed to load status');
                    }
                    
                    currentMonthlyStatus = json.status;
                    renderMonthlyStatus(json.status);
                    panel.style.display = 'block';
                })
                .catch(err => {
                    console.error('Error loading monthly status:', err);
                    document.getElementById('monthly-fee-rows').innerHTML = '<tr><td colspan="3" class="text-center py-4 text-danger">Error loading fee details. Please try again.</td></tr>';
                    panel.style.display = 'block';
                });
        }

        function renderMonthlyStatus(status) {
            const monthName = status.billing_period.month_name;
            const year = status.billing_period.year;
            const breakdown = status.fee_breakdown;
            const existing = status.existing_invoice;
            const student = status.student;

            document.getElementById('display-month-year').textContent = `${monthName} ${year}`;
            document.getElementById('student-name-display').textContent = student.name;
            document.getElementById('student-course-display').textContent = `${student.course} • ${student.fee_tenure}`;

            // Update status badge
            const statusBadge = document.getElementById('status-badge');
            if (existing) {
                const statusClass = existing.status === 'Paid' ? 'success' : (existing.status === 'Partial' ? 'warning' : 'danger');
                statusBadge.className = `badge bg-${statusClass} border`;
                statusBadge.textContent = existing.status;
            } else {
                statusBadge.className = 'badge bg-info border';
                statusBadge.textContent = 'Not Generated';
            }

            // Render fee rows
            const tbody = document.getElementById('monthly-fee-rows');
            tbody.innerHTML = '';

            function addRow(label, amount, editable = false, feeType = '', showInBreakdown = true) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-semibold">${label}</td>
                    <td>₹${parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                    <td>
                        <input type="number" step="0.01" class="form-input py-1 pay-amount-input" 
                               data-fee-type="${feeType}" 
                               value="${editable ? amount : '0'}" 
                               ${editable ? '' : 'readonly'} 
                               style="padding: 6px 10px; font-size: 0.85rem; height: auto; max-width: 140px;" 
                               oninput="recalcMonthlyTotal()" />
                    </td>
                `;
                tbody.appendChild(tr);
            }

            // Monthly course fee (editable for partial payments)
            addRow('Monthly Course Fee', breakdown.net_monthly_fee, true, 'Monthly Course Fee');

            // Discount (display only, negative)
            if (breakdown.monthly_discount > 0) {
                const tr = document.createElement('tr');
                tr.className = 'table-success';
                tr.innerHTML = `
                    <td class="fw-semibold text-success"><i class="fas fa-tag me-1"></i>Discount</td>
                    <td class="text-success">- ₹${parseFloat(breakdown.monthly_discount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                    <td>-</td>
                `;
                tbody.appendChild(tr);
            }

            // Late fine
            if (breakdown.late_fine > 0) {
                addRow(`Late Fine (${breakdown.months_late} month${breakdown.months_late > 1 ? 's' : ''} late)`, breakdown.late_fine, true, 'Late Fine');
            }

            // Attendance fine
            if (breakdown.attendance_fine > 0) {
                addRow('Attendance Fine', breakdown.attendance_fine, true, 'Attendance Fine');
            }

            // Custom/Extra fee row (always editable)
            addRow('Custom / Extra Fee', '0', true, 'Custom Fee');

            // Update breakdown display
            document.getElementById('display-monthly-fee').textContent = `₹${breakdown.monthly_course_fee.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
            document.getElementById('display-discount').textContent = `- ₹${breakdown.monthly_discount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
            document.getElementById('display-late-fine').textContent = `+ ₹${breakdown.late_fine.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
            document.getElementById('display-attendance-fine').textContent = `+ ₹${breakdown.attendance_fine.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
            document.getElementById('display-total-amount').textContent = `₹${breakdown.total_amount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

            // Set default pay now amount
            const payNowInput = document.getElementById('pay_now_amount');
            if (existing && existing.due_amount > 0) {
                payNowInput.value = existing.due_amount.toFixed(2);
            } else {
                payNowInput.value = breakdown.total_amount.toFixed(2);
            }

            // Show fine details if any
            const fineDetailsBox = document.getElementById('fine-details-box');
            const fineDetailsContent = document.getElementById('fine-details-content');
            if (breakdown.late_fine > 0 || breakdown.attendance_fine > 0) {
                fineDetailsBox.style.display = 'block';
                let detailsHtml = '';
                if (breakdown.late_fine > 0) {
                    detailsHtml += `<div><strong>Late Fine:</strong> ${breakdown.months_late} month(s) overdue × ₹50 = ₹${breakdown.late_fine}</div>`;
                }
                if (breakdown.attendance_fine > 0) {
                    detailsHtml += `<div><strong>Attendance Fine:</strong> ${status.fee_breakdown.attendance_fine_details?.join(', ') || 'Absent days'}</div>`;
                }
                fineDetailsContent.innerHTML = detailsHtml;
            } else {
                fineDetailsBox.style.display = 'none';
            }

            // Set hidden fields
            document.getElementById('discount_hidden').value = breakdown.monthly_discount;
            document.getElementById('fine_hidden').value = breakdown.total_fine;
            
            recalcMonthlyTotal();
        }

        function recalcMonthlyTotal() {
            const inputs = document.querySelectorAll('.pay-amount-input');
            let total = 0;
            let feeItems = [];

            inputs.forEach(inp => {
                const val = parseFloat(inp.value) || 0;
                if (val > 0) {
                    feeItems.push({
                        category: inp.dataset.feeType,
                        amount: val
                    });
                    total += val;
                }
            });

            // Set pay now amount to match total if user hasn't manually changed it
            const payNowInput = document.getElementById('pay_now_amount');
            const currentPayNow = parseFloat(payNowInput.value) || 0;
            // Only auto-update if payNow is 0 or matches previous total
            if (currentPayNow === 0 || Math.abs(currentPayNow - total) < 0.01) {
                payNowInput.value = total.toFixed(2);
            }

            document.getElementById('paid_amount_hidden').value = payNowInput.value;
            document.getElementById('total_amount_hidden').value = total.toFixed(2);
            document.getElementById('fee_items_json').value = JSON.stringify(feeItems);
        }

        function updatePayNow() {
            const payNow = parseFloat(document.getElementById('pay_now_amount').value) || 0;
            document.getElementById('paid_amount_hidden').value = payNow.toFixed(2);
        }

        function submitMonthlyFee() {
            const studentId = document.getElementById('student_id').value;
            if (!studentId) {
                alert('Please select a student first.');
                return;
            }

            const payNow = parseFloat(document.getElementById('pay_now_amount').value) || 0;
            const totalAmount = parseFloat(document.getElementById('total_amount_hidden').value) || 0;
            
            if (totalAmount <= 0 && payNow <= 0) {
                alert('Please enter at least one fee amount.');
                return;
            }

            // Set status based on payment
            const statusSelect = document.getElementById('status');
            if (payNow >= totalAmount && totalAmount > 0) {
                statusSelect.value = 'Paid';
            } else if (payNow > 0) {
                statusSelect.value = 'Partial';
            }

            // Set fee category with month/year
            const month = document.getElementById('billing_month').value;
            const year = document.getElementById('billing_year').value;
            const monthName = new Date(year, month - 1).toLocaleString('default', { month: 'long' });
            document.getElementById('fee_category_hidden').value = `Monthly Fee - ${monthName} ${year}`;

            const form = document.getElementById('monthlyFeeForm');
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleTxnFields();
            
            // Auto-load if studentId is pre-selected
            @if($studentId)
                loadMonthlyStatus();
            @endif
        });
    </script>
@endsection