@extends('layouts.app')
@section('title', 'Create Client Invoice')
@section('page-title', 'Generate Client Invoice')

@section('content')
<style>
    .line-item-row td { vertical-align: middle; padding: 8px 6px; }
    .line-item-row input { padding: 8px 10px; font-size: .9rem; }
    #items-table tfoot td { padding: 8px 6px; }
    .total-box { background: var(--surface-soft); border-radius: 10px; padding: 20px; }
    .total-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid var(--border); font-size: .95rem; }
    .total-row:last-child { border-bottom: none; font-size: 1.15rem; font-weight: 700; color: var(--first-color); }
</style>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-file-invoice-dollar"></i> New Client Invoice</h3>
                <a href="{{ route('client_invoices.index') }}" class="button button-secondary small"><i class="fas fa-arrow-left me-1"></i> Back</a>
            </div>
            <form action="{{ route('client_invoices.store') }}" method="POST" class="form-card" id="invoice-form">
                @csrf
                <div class="row g-4">
                    <!-- Client & Invoice Info -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Client <span class="text-danger">*</span></label>
                        <select name="client_id" class="form-input" required>
                            <option value="">— Select Client —</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ (old('client_id', $selectedClient?->id) == $client->id) ? 'selected' : '' }}>
                                    {{ $client->name }}{{ $client->company ? ' (' . $client->company . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Invoice No. <small class="text-muted">(auto if blank)</small></label>
                        <input type="text" name="invoice_no" class="form-input" value="{{ old('invoice_no') }}" placeholder="CL-INV-..." />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Due Date</label>
                        <input type="date" name="due_date" class="form-input" value="{{ old('due_date') }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-input" value="{{ old('payment_date') }}" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <select name="payment_method" class="form-input">
                            <option value="">— Select —</option>
                            <option value="Cash" {{ old('payment_method')==='Cash'?'selected':'' }}>Cash</option>
                            <option value="Online" {{ old('payment_method')==='Online'?'selected':'' }}>Online / UPI</option>
                            <option value="Cheque" {{ old('payment_method')==='Cheque'?'selected':'' }}>Cheque</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Transaction ID <small class="text-muted">(online)</small></label>
                        <input type="text" name="transaction_id" class="form-input" value="{{ old('transaction_id') }}" placeholder="UTR / Ref. No." />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-input" required>
                            <option value="Unpaid" {{ old('status','Unpaid')==='Unpaid'?'selected':'' }}>Unpaid</option>
                            <option value="Partial" {{ old('status')==='Partial'?'selected':'' }}>Partial</option>
                            <option value="Paid" {{ old('status')==='Paid'?'selected':'' }}>Paid</option>
                        </select>
                    </div>

                    <!-- Line Items -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Invoice Items / Services <span class="text-danger">*</span></label>
                        <div style="overflow-x:auto;">
                            <table class="table table-bordered mb-2" id="items-table" style="min-width:680px;">
                                <thead style="background:var(--surface-soft);">
                                    <tr>
                                        <th style="width:40%">Description</th>
                                        <th style="width:12%">Qty</th>
                                        <th style="width:18%">Unit Price (₹)</th>
                                        <th style="width:18%">Amount (₹)</th>
                                        <th style="width:12%"></th>
                                    </tr>
                                </thead>
                                <tbody id="items-body">
                                    <tr class="line-item-row">
                                        <td><input type="text" name="invoice_items[0][description]" class="form-input" placeholder="Service / product description" required /></td>
                                        <td><input type="number" name="invoice_items[0][qty]" class="form-input qty-input" value="1" min="0" step="any" required /></td>
                                        <td><input type="number" name="invoice_items[0][unit_price]" class="form-input price-input" value="0" min="0" step="any" required /></td>
                                        <td><input type="number" name="invoice_items[0][amount]" class="form-input amount-input" value="0" min="0" step="any" readonly style="background:var(--surface-soft);" /></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove"><i class="fas fa-times"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-row" class="button button-secondary small py-1 px-3">
                            <i class="fas fa-plus me-1"></i>Add Line Item
                        </button>
                    </div>

                    <!-- Totals -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Notes / Remarks</label>
                        <textarea name="notes" class="form-input" rows="4" placeholder="Payment terms, special conditions...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="total-box">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span id="subtotal-display">₹0.00</span>
                            </div>
                            <div class="total-row align-items-center">
                                <span>Tax %</span>
                                <input type="number" name="tax_percent" id="tax-percent" class="form-input" style="width:90px;padding:4px 8px;" value="{{ old('tax_percent', 0) }}" min="0" max="100" step="any" />
                            </div>
                            <div class="total-row">
                                <span>Tax Amount</span>
                                <span id="tax-display">₹0.00</span>
                            </div>
                            <div class="total-row align-items-center">
                                <span>Discount (₹)</span>
                                <input type="number" name="discount" id="discount-input" class="form-input" style="width:120px;padding:4px 8px;" value="{{ old('discount', 0) }}" min="0" step="any" />
                            </div>
                            <div class="total-row">
                                <span>Grand Total</span>
                                <span id="total-display">₹0.00</span>
                            </div>
                            <div class="total-row align-items-center mt-2">
                                <span>Paid Amount (₹) <span class="text-danger">*</span></span>
                                <input type="number" name="paid_amount" id="paid-input" class="form-input" style="width:140px;padding:6px 10px;" value="{{ old('paid_amount', 0) }}" min="0" step="any" required />
                            </div>
                            <div class="total-row">
                                <span>Due Amount</span>
                                <span id="due-display" class="text-danger fw-bold">₹0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="button button-primary px-5 py-2">
                            <i class="fas fa-file-invoice me-2"></i>Generate Invoice
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let rowIndex = 1;

function calcRow(row) {
    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const amount = qty * price;
    row.querySelector('.amount-input').value = amount.toFixed(2);
    calcTotals();
}

function calcTotals() {
    const amounts = Array.from(document.querySelectorAll('.amount-input')).map(i => parseFloat(i.value) || 0);
    const subtotal = amounts.reduce((a, b) => a + b, 0);
    const taxPct = parseFloat(document.getElementById('tax-percent').value) || 0;
    const taxAmount = subtotal * taxPct / 100;
    const discount = parseFloat(document.getElementById('discount-input').value) || 0;
    const total = Math.max(0, subtotal + taxAmount - discount);
    const paid = parseFloat(document.getElementById('paid-input').value) || 0;
    const due = Math.max(0, total - paid);

    document.getElementById('subtotal-display').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('tax-display').textContent = '₹' + taxAmount.toFixed(2);
    document.getElementById('total-display').textContent = '₹' + total.toFixed(2);
    document.getElementById('due-display').textContent = '₹' + due.toFixed(2);
}

document.getElementById('add-row').addEventListener('click', function () {
    const body = document.getElementById('items-body');
    const row = document.createElement('tr');
    row.className = 'line-item-row';
    row.innerHTML = `
        <td><input type="text" name="invoice_items[${rowIndex}][description]" class="form-input" placeholder="Description" required /></td>
        <td><input type="number" name="invoice_items[${rowIndex}][qty]" class="form-input qty-input" value="1" min="0" step="any" required /></td>
        <td><input type="number" name="invoice_items[${rowIndex}][unit_price]" class="form-input price-input" value="0" min="0" step="any" required /></td>
        <td><input type="number" name="invoice_items[${rowIndex}][amount]" class="form-input amount-input" value="0" min="0" step="any" readonly style="background:var(--surface-soft);" /></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button></td>
    `;
    body.appendChild(row);
    attachRowListeners(row);
    rowIndex++;
});

function attachRowListeners(row) {
    row.querySelector('.qty-input').addEventListener('input', () => calcRow(row));
    row.querySelector('.price-input').addEventListener('input', () => calcRow(row));
    row.querySelector('.remove-row').addEventListener('click', function () {
        if (document.querySelectorAll('.line-item-row').length > 1) {
            row.remove();
            calcTotals();
        }
    });
}

// Attach to initial row
document.querySelectorAll('.line-item-row').forEach(attachRowListeners);
document.getElementById('tax-percent').addEventListener('input', calcTotals);
document.getElementById('discount-input').addEventListener('input', calcTotals);
document.getElementById('paid-input').addEventListener('input', calcTotals);
</script>
@endsection
