@extends('layouts.app')

@section('title', 'Create Fee Receipt')
@section('page-title', 'Generate Student Fee Receipt')

@section('content')
    <div class="invoice-container">
        <div id="page-content">
            <div class="card premium-form-card" style="max-width: 900px;">
                <div class="card-header bg-transparent border-bottom mb-4 pb-3">
                    <h3 class="mb-0 fw-bold text-first">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Generate Student Fee Receipt
                    </h3>
                </div>

                <form action="{{ route('fee_invoices.store') }}" method="POST" class="form-card p-0">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger mb-4 p-3" style="border-radius: 8px;">
                            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Student Selection --}}
                    <div class="form-group mb-4">
                        <label for="student_id" class="fw-semibold mb-2">
                            <i class="fas fa-user-graduate text-first me-2"></i>Select Student
                        </label>
                        <select id="student_id" name="student_id" required class="form-input" onchange="loadStudentFees()">
                            <option value="">-- Choose Student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->admission_no }} - {{ $student->first_name }} {{ $student->last_name }} ({{ $student->course?->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Billing Period Selector --}}
                    <div class="form-group-grid mb-4" id="billing-period-section" style="display: none;">
                        <div class="form-group">
                            <label for="billing_month_select" class="fw-semibold mb-2">
                                <i class="fas fa-calendar-alt text-first me-2"></i>Billing Month
                            </label>
                            <select id="billing_month_select" name="billing_month" class="form-input" onchange="loadStudentFees()">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="billing_year_select" class="fw-semibold mb-2">
                                <i class="fas fa-calendar text-first me-2"></i>Billing Year
                            </label>
                            <select id="billing_year_select" name="billing_year" class="form-input" onchange="loadStudentFees()">
                                @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    {{-- Fee Details Panel --}}
                    <div id="student-fee-panel" style="display: none;">
                        <div class="card mb-4 border" style="background: var(--surface); border-radius: 12px;">
                            <div class="card-body p-4">
                                {{-- Student Info Header --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1" id="student-name-display">Student Name</h5>
                                        <p class="text-muted mb-0 small" id="student-course-display">Course -</p>
                                    </div>
                                    <span class="badge bg-light text-dark border" id="tenure-badge">Tenure: --</span>
                                </div>

                                {{-- Course Fee Summary Box --}}
                                <div class="alert alert-light border mb-3" id="pending-due-box">
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Total Course Fee:</strong></span>
                                        <span class="fw-bold" id="total-course-fee">₹0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Discount:</strong></span>
                                        <span class="text-success fw-bold" id="total-discount">-₹0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Net Course Fee:</strong></span>
                                        <span class="fw-bold" id="net-course-fee">₹0</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Already Paid (Course):</strong></span>
                                        <span class="text-success fw-bold" id="already-paid">₹0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Remaining Course Fee:</strong></span>
                                        <span class="text-danger fw-bold" id="pending-dues">₹0</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between" style="background: #f0f8ff; padding: 6px 8px; border-radius: 6px;">
                                        <span><strong>Per-Installment:</strong></span>
                                        <span class="fw-bold text-first" id="per-installment-amount">₹0</span>
                                    </div>
                                </div>

                                {{-- Fee Rows Table --}}
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0" style="font-size: 0.9rem;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40%;">Fee Type</th>
                                                <th style="width: 30%;">Amount (₹)</th>
                                                <th style="width: 30%;">Pay Now (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="fee-rows-body">
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-start mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" style="font-size: 0.8rem; padding: 6px 12px;" onclick="addCustomRow()">
                                        <i class="fas fa-plus me-1"></i> Add Custom Fee Item
                                    </button>
                                </div>

                                {{-- Total to Pay Now --}}
                                <div class="mt-3 p-3 bg-light rounded border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-first">Total to Pay Now:</strong>
                                        </div>
                                        <div class="text-end">
                                            <h4 class="mb-0 fw-bold text-first" id="pay-now-total">₹0.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Details --}}
                    <div class="form-group-grid mb-4">
                            <div class="form-group">
                                <label for="payment_date" class="fw-semibold mb-2">
                                    <i class="fas fa-calendar text-first me-2"></i>Payment Date
                                </label>
                                <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required class="form-input" />
                            </div>
                            <div class="form-group">
                                <label for="payment_method" class="fw-semibold mb-2">
                                    <i class="fas fa-credit-card text-first me-2"></i>Payment Mode
                                </label>
                                <select id="payment_method" name="payment_method" class="form-input" onchange="toggleTxnFields()">
                                    <option value="Cash">Cash</option>
                                    <option value="Online">Online</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group-grid mb-4" id="txn-fields" style="display: none;">
                            <div class="form-group">
                                <label for="transaction_id" class="fw-semibold mb-2">Transaction / Ref No</label>
                                <input type="text" id="transaction_id" name="transaction_id" class="form-input" placeholder="Enter transaction ID" />
                            </div>
                            <div class="form-group">
                                <label for="utr_no" class="fw-semibold mb-2">UTR Number</label>
                                <input type="text" id="utr_no" name="utr_no" class="form-input" placeholder="UTR No (if applicable)" />
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="remarks" class="fw-semibold mb-2">
                                <i class="fas fa-sticky-note text-first me-2"></i>Remarks (Optional)
                            </label>
                            <textarea id="remarks" name="remarks" class="form-input" style="height: 70px; resize: vertical;" placeholder="Any note about this payment..."></textarea>
                        </div>

                        {{-- Hidden Fields --}}
                        <input type="hidden" name="fee_items_json" id="fee_items_json" value="" />
                        <input type="hidden" name="total_amount" id="total_amount_hidden" value="0" />
                        <input type="hidden" name="paid_amount" id="paid_amount_hidden" value="0" />
                        <input type="hidden" name="discount" id="discount_hidden" value="0" />
                        <input type="hidden" name="fine" id="fine_hidden" value="0" />
                        <input type="hidden" name="status" id="status_hidden" value="Paid" />
                        <input type="hidden" name="fee_category" id="fee_category_hidden" value="Regular Fees" />

                        <div class="form-actions-row">
                            <a href="{{ route('fee_invoices.index') }}" class="button button-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="button" onclick="submitInvoice()" class="button button-primary">
                                <i class="fas fa-check-circle me-2"></i>Save & Print Receipt
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentStudentData = null;

        function toggleTxnFields() {
            const method = document.getElementById('payment_method')?.value;
            const txnDiv = document.getElementById('txn-fields');
            if (txnDiv) {
                txnDiv.style.display = (method === 'Online') ? 'grid' : 'none';
            }
        }

        async function loadStudentFees() {
            const studentId = document.getElementById('student_id').value;
            const panel = document.getElementById('student-fee-panel');
            const billingSection = document.getElementById('billing-period-section');
            const tbody = document.getElementById('fee-rows-body');

            if (!studentId) {
                panel.style.display = 'none';
                billingSection.style.display = 'none';
                return;
            }

            billingSection.style.display = 'grid';

            const month = document.getElementById('billing_month_select').value;
            const year = document.getElementById('billing_year_select').value;

            try {
                const res = await fetch(`/api/students/${studentId}/fee-info?month=${month}&year=${year}`);
                const json = await res.json();
                if (!json.success) return;

                currentStudentData = json;
                const data = json.student_data;
                const courseAccount = json.course_account;
                const attendanceFine = parseFloat(json.attendance_fine) || 0;
                const lateFine = parseFloat(json.late_fine) || 0;
                const fineDetails = json.fine_details || '';

                const totalCourseFee = parseFloat(data.course_fee) || 0;
                const discount = parseFloat(data.discount) || 0;
                const netCourseFee = Math.max(0, totalCourseFee - discount);
                const regFee = parseFloat(data.registration_fee) || 0;
                const prosFee = parseFloat(data.prospectus_fee) || 0;

                const totalPaidSoFar = parseFloat(courseAccount.total_paid) || 0;
                const pendingDues = parseFloat(courseAccount.pending_dues) || 0;

                const tenureLabel = data.fee_tenure || '1 Year';
                const netInstallment = parseFloat(data.net_installment) || 0;
                const numInstallments = parseInt(data.num_installments) || 1;

                // Update summary box
                document.getElementById('student-name-display').textContent = data.student_name || 'Student';
                document.getElementById('student-course-display').textContent = `${data.course_duration || ''} Course • ${numInstallments} installments of ₹${netInstallment.toLocaleString('en-IN')}`;
                document.getElementById('total-course-fee').textContent = `₹${totalCourseFee.toLocaleString('en-IN')}`;
                document.getElementById('total-discount').textContent = `-₹${discount.toLocaleString('en-IN')}`;
                document.getElementById('net-course-fee').textContent = `₹${netCourseFee.toLocaleString('en-IN')}`;
                document.getElementById('already-paid').textContent = `₹${totalPaidSoFar.toLocaleString('en-IN')}`;
                document.getElementById('pending-dues').textContent = `₹${pendingDues.toLocaleString('en-IN')}`;
                document.getElementById('per-installment-amount').textContent = `₹${netInstallment.toLocaleString('en-IN')} / ${tenureLabel}`;
                document.getElementById('tenure-badge').textContent = `Tenure: ${tenureLabel}`;

                // Build fee rows
                tbody.innerHTML = '';

                function addSectionHeader(title) {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td colspan="3" style="background: #f0f0f0; font-weight: 700; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; color: #555; padding: 6px 10px; border-bottom: 2px solid #ddd;">${title}</td>`;
                    tbody.appendChild(tr);
                }

                function addRow(label, amount, editable = false, defaultPay = '0', colorClass = '') {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="fw-semibold ${colorClass}">${label}</td>
                        <td class="${colorClass}">₹${parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                        <td>
                            <input type="number" step="0.01" class="form-input py-1 pay-amount-input" data-fee-type="${label}" value="${defaultPay}" ${editable ? '' : 'readonly'} style="padding: 6px 10px; font-size: 0.85rem; height: auto; max-width: 140px;" oninput="recalcTotal()" />
                        </td>
                    `;
                    tbody.appendChild(tr);
                }

                // ── One-Time Fees Section ──
                if ((regFee > 0 && !json.registration_paid) || (prosFee > 0 && !json.prospectus_paid)) {
                    addSectionHeader('One-Time Fees (Admission)');
                    if (regFee > 0 && !json.registration_paid) {
                        addRow('Registration Fee', regFee, true, '0');
                    }
                    if (prosFee > 0 && !json.prospectus_paid) {
                        addRow('Prospectus Fee', prosFee, true, '0');
                    }
                }

                // Show badges for already-paid one-time fees
                if (json.registration_paid || json.prospectus_paid) {
                    let badges = [];
                    if (json.registration_paid) badges.push('<span class="badge bg-success me-1" style="font-size: 0.72rem;">✓ Registration Paid</span>');
                    if (json.prospectus_paid) badges.push('<span class="badge bg-success me-1" style="font-size: 0.72rem;">✓ Prospectus Paid</span>');
                    const tr = document.createElement('tr');
                    tr.innerHTML = `<td colspan="3" style="padding: 4px 10px;">${badges.join('')}</td>`;
                    tbody.appendChild(tr);
                }

                // ── Course Fee Section ──
                addSectionHeader(`Course Fee — ${tenureLabel} Installment`);
                if (netInstallment > 0) {
                    addRow(`Course Fee (${tenureLabel} EMI)`, netInstallment, true, netInstallment.toFixed(2));
                }

                // ── Fines Section ──
                if (attendanceFine > 0 || lateFine > 0) {
                    addSectionHeader('Fines & Penalties');
                    if (attendanceFine > 0) {
                        addRow('Attendance Fine (₹50/day absent)', attendanceFine, true, attendanceFine.toFixed(2), 'text-danger');
                        document.getElementById('remarks').value = `Attendance Fine: ${fineDetails}`;
                    }
                    if (lateFine > 0) {
                        addRow('Late Payment Fine', lateFine, true, lateFine.toFixed(2), 'text-warning');
                    }
                }

                // ── Custom / Extra Fees Section ──
                addSectionHeader('Additional / Custom');
                addRow('Seminar Fine', '0', true, '0');
                addRow('Custom / Extra Fee', '0', true, '0');

                panel.style.display = 'block';
                recalcTotal();
            } catch (err) {
                console.error('Error loading fees:', err);
                panel.style.display = 'none';
            }
        }

        function recalcTotal() {
            const inputs = document.querySelectorAll('.pay-amount-input');
            let total = 0;
            inputs.forEach(inp => {
                total += parseFloat(inp.value) || 0;
            });
            document.getElementById('pay-now-total').textContent = `₹${total.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
        }

        function addCustomRow() {
            const tbody = document.getElementById('fee-rows-body');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" class="form-input py-1 custom-fee-label" placeholder="Enter Fee Name (e.g. Exam Fee)" style="padding: 6px 10px; font-size: 0.85rem; height: auto; max-width: 250px;" oninput="updateCustomFeeType(this)" />
                </td>
                <td>₹0.00</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" step="0.01" class="form-input py-1 pay-amount-input" data-fee-type="Custom Fee" value="0" style="padding: 6px 10px; font-size: 0.85rem; height: auto; max-width: 140px;" oninput="recalcTotal()" />
                        <button type="button" class="btn btn-sm text-danger p-1" onclick="this.closest('tr').remove(); recalcTotal();">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        }

        function updateCustomFeeType(inputElement) {
            const amountInput = inputElement.closest('tr').querySelector('.pay-amount-input');
            if (amountInput) {
                amountInput.dataset.feeType = inputElement.value.trim() || 'Custom Fee';
            }
        }

        function submitInvoice() {
            const studentId = document.getElementById('student_id').value;
            if (!studentId) {
                alert('Please select a student first.');
                return;
            }

            const inputs = document.querySelectorAll('.pay-amount-input');
            const feeItems = [];
            let baseTotal = 0;
            let fineTotal = 0;
            let discountTotal = 0;

            inputs.forEach(inp => {
                const val = parseFloat(inp.value) || 0;
                if (val > 0) {
                    feeItems.push({
                        category: inp.dataset.feeType,
                        amount: val
                    });

                    const typeLower = inp.dataset.feeType.toLowerCase();
                    if (typeLower.includes('fine')) {
                        fineTotal += val;
                    } else if (typeLower.includes('discount')) {
                        discountTotal += val;
                    } else {
                        baseTotal += val;
                    }
                }
            });

            if (feeItems.length === 0) {
                alert('Please enter atleast one fee amount to pay.');
                return;
            }

            // Set billing period
            const billingMonth = document.getElementById('billing_month_select').value;
            const billingYear = document.getElementById('billing_year_select').value;
            if (billingMonth && billingYear) {
                const monthName = new Date(billingYear, billingMonth - 1).toLocaleString('default', { month: 'long' });
                document.getElementById('fee_category_hidden').value = `Monthly Fee - ${monthName} ${billingYear}`;
            }

            document.getElementById('fee_items_json').value = JSON.stringify(feeItems);
            document.getElementById('total_amount_hidden').value = baseTotal.toFixed(2);
            document.getElementById('fine_hidden').value = fineTotal.toFixed(2);
            document.getElementById('discount_hidden').value = discountTotal.toFixed(2);
            document.getElementById('paid_amount_hidden').value = (baseTotal + fineTotal - discountTotal).toFixed(2);
            document.getElementById('status_hidden').value = 'Paid';

            const form = document.getElementById('create-invoice-form');
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleTxnFields();
        });
    </script>
@endsection