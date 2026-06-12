@extends('layouts.app')
@section('title', 'Client Invoices')
@section('page-title', 'Client Invoice Management')

@section('content')
<style>
    .client-avatar-sm { width:36px;height:36px;border-radius:8px;background:rgba(255,85,50,0.1);color:var(--first-color);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.9rem;flex-shrink:0; }
    .user-info { display:flex;align-items:center;gap:10px; }
</style>

<div>
    <!-- Toolbar -->
    <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <form method="GET" action="{{ route('client_invoices.index') }}" class="d-flex align-items-center gap-2 flex-grow-1">
            <div style="position:relative;flex:1;max-width:340px;">
                <input type="text" name="search" placeholder="Search invoice no. or client..." value="{{ request('search') }}" class="form-input" style="padding-left:36px;" />
                <i class="fas fa-search text-muted position-absolute" style="left:14px;top:50%;transform:translateY(-50%);"></i>
            </div>
            <select name="status" class="form-input" style="width:160px;">
                <option value="">All Statuses</option>
                <option value="Paid" {{ request('status')==='Paid'?'selected':'' }}>Paid</option>
                <option value="Partial" {{ request('status')==='Partial'?'selected':'' }}>Partial</option>
                <option value="Unpaid" {{ request('status')==='Unpaid'?'selected':'' }}>Unpaid</option>
            </select>
            <button type="submit" class="button button-secondary px-4 py-2"><i class="fas fa-filter me-2"></i>Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('client_invoices.index') }}" class="button button-secondary px-3 py-2"><i class="fas fa-undo"></i></a>
            @endif
        </form>
        <div class="d-flex gap-2 align-items-center">
            <div class="form-check form-switch me-2 d-flex align-items-center gap-2">
                <input class="form-check-input mt-0" type="checkbox" role="switch" id="toggleTrash"
                    {{ request('trashed') ? 'checked' : '' }}
                    onchange="window.location.href='{{ request()->fullUrlWithQuery(['trashed' => request('trashed') ? null : '1']) }}'">
                <label class="form-check-label fw-bold" for="toggleTrash" style="cursor:pointer;">Recycle Bin</label>
            </div>
            <a href="{{ route('client_invoices.create') }}" class="button button-primary py-2 px-4">
                <i class="fas fa-plus me-2"></i>Create Invoice
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="card p-0" style="border-radius:12px;overflow:hidden;">
        <div class="p-4 border-bottom d-flex align-items-center gap-2">
            <i class="fas fa-file-invoice text-first"></i>
            <h5 class="mb-0 fw-bold">Client Invoices</h5>
            <span class="badge bg-light border text-dark ms-auto">{{ $invoices->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width:800px;">
                <thead style="background:var(--surface-soft);">
                    <tr>
                        <th class="ps-4" style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Invoice No</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Client</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Total</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Paid</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Due</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Status</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Due Date</th>
                        <th class="text-end pe-4" style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border p-2 fw-bold" style="font-size:.8rem; {{ request('trashed') ? 'text-decoration:line-through;color:#dc3545!important;' : '' }}">
                                {{ $invoice->invoice_no }}
                            </span>
                        </td>
                        <td>
                            <div class="user-info">
                                <div class="client-avatar-sm">{{ strtoupper(substr($invoice->client?->name ?? 'C', 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $invoice->client?->name ?? 'Unknown' }}</strong>
                                    @if($invoice->client?->company)
                                        <p class="text-muted small mb-0">{{ $invoice->client->company }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold">₹{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="text-success fw-bold">₹{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td class="{{ $invoice->due_amount > 0 ? 'text-danger' : 'text-success' }} fw-bold">₹{{ number_format($invoice->due_amount, 2) }}</td>
                        <td>
                            <span class="badge rounded-pill bg-{{ strtolower($invoice->status) === 'paid' ? 'success' : (strtolower($invoice->status) === 'unpaid' ? 'danger' : 'warning') }}">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}</td>
                        <td class="text-end pe-4">
                            @if($invoice->trashed())
                                <form action="{{ route('client_invoices.restore', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirmAction(event, 'Restore this invoice?');">
                                    @csrf
                                    <button class="button button-secondary small py-1 px-3"><i class="fas fa-trash-restore me-1"></i>Restore</button>
                                </form>
                            @else
                                <a href="{{ route('client_invoices.show', $invoice) }}" class="button button-secondary small py-1 px-3" target="_blank">
                                    <i class="fas fa-print me-1"></i>Print
                                </a>
                                <form action="{{ route('client_invoices.destroy', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirmAction(event, 'Delete this invoice?');">
                                    @csrf @method('DELETE')
                                    <button class="button button-danger small py-1 px-3"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-file-invoice fa-2x d-block mb-2"></i>No client invoices found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection
