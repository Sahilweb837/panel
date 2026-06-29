@extends('layouts.app')

@section('title', 'Create Fee Receipt')
@section('page-title', 'Generate Student Fee Receipt')

@section('content')
    <div class="invoice-container">
        <div id="page-content">
            <div class="card premium-form-card" style="max-width: 850px;">
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

                    <div id="student-fee-panel" style="display: none;">
                        <div class="card mb-4 border" style="background: var(--surface); border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-1" id="student-name-display">Student Name</h5>
                                        <p class="text-muted mb-0 small" id="student-course-display">Course -</p>
                                    </div>
                                    <span class="badge bg-light text-dark border" id="tenure-badge">Tenure: --</span>
                                </div>

                                <div class="alert alert-light border mb-3" id="pending-due-box">
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Total Course Fee:</strong></span>
                                        <span class="fw-bold" id="total-course-fee">₹0</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Already Paid:</strong></span>
                                        <span class="text-success fw-bold" id="already-paid">₹0</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span><strong>Pending Dues:</strong></span>
                                        <span class="text-danger fw-bold" id="pending-dues">₹0</span>
                                    </div>
                                </div>

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
                            <input type="hidden" name="billing_month" id="billing_month" value="{{ date('n') }}" />
                            <input type="hidden" name="billing_year" id="billing_year" value="{{ date('Y') }}" />
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
            const tbody = document.getElementById('fee-rows-body');

            if (!studentId) {
                panel.style.display = 'none';
                return;
            }

            try {
                const res = await fetch(`/api/students/${studentId}/fee-info`);
                const json = await res.json();
                if (!json.success) return;

                currentStudentData = json;
                const data = json.student_data;
                const pastPayments = json.past_payments || [];
                const attendanceFine = parseFloat(json.attendance_fine) || 0;
                const fineDetails = json.fine_details || '';

                const totalCourseFee = parseFloat(data.course_fee) || 0;
                const regFee = parseFloat(data.registration_fee) || 0;
                const prosFee = parseFloat(data.prospectus_fee) || 0;
                const discount = parseFloat(data.discount) || 0;

                const totalPaidSoFar = pastPayments.reduce((sum, p) => sum + parseFloat(p.paid), 0);
                const pendingDues = Math.max(0, totalCourseFee - totalPaidSoFar);

                document.getElementById('student-name-display').textContent = (json.student_data?.student_name || 'Student');
                document.getElementById('student-course-display').textContent = `${data.course_duration || ''} Course • Fee: ₹${totalCourseFee.toLocaleString()}`;
                document.getElementById('total-course-fee').textContent = `₹${totalCourseFee.toLocaleString()}`;
                document.getElementById('already-paid').textContent = `₹${totalPaidSoFar.toLocaleString()}`;
                document.getElementById('pending-dues').textContent = `₹${pendingDues.toLocaleString()}`;

                const tenureLabel = data.fee_tenure || '1 Year';
                document.getElementById('tenure-badge').textContent = `Tenure: ${tenureLabel}`;

                tbody.innerHTML = '';

                function addRow(label, amount, editable = false, placeholder = '0') {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="fw-semibold">${label}</td>
                        <td>₹${parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                        <td>
                            <input type="number" step="0.01" class="form-input py-1 pay-amount-input" data-fee-type="${label}" value="${editable ? placeholder : '0'}" ${editable ? '' : 'readonly'} style="padding: 6px 10px; font-size: 0.85rem; height: auto; max-width: 140px;" oninput="recalcTotal()" />
                        </td>
                    `;
                    tbody.appendChild(tr);
                }

                if (regFee > 0 && !json.registration_paid) addRow('Registration Fee', regFee, true, '0');
                if (prosFee > 0 && !json.prospectus_paid) addRow('Prospectus Fee', prosFee, true, '0');

                let monthlyAmt = 0;
                if (totalCourseFee > 0 && tenureLabel) {
                    const months = tenureLabel === '1 Month' ? 1 : tenureLabel === '3 Months' ? 3 : tenureLabel === '6 Months' ? 6 : 12;
                    monthlyAmt = totalCourseFee / months;
                    addRow(`Course Fee (${tenureLabel} EMI)`, monthlyAmt, true, '0');
                }

                if (attendanceFine > 0) {
                    addRow(`Attendance Fine`, attendanceFine, true, attendanceFine.toFixed(2));
                    document.getElementById('remarks').value = `Fine: ${fineDetails}`;
                }

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

            // Add billing month/year to fee_category if selected
            const billingMonth = document.getElementById('billing_month').value;
            const billingYear = document.getElementById('billing_year').value;
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

            const form = document.querySelector('form');
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleTxnFields();
        });
    </script>
@endsection