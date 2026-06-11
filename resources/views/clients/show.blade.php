@extends('layouts.app')
@section('title', 'Client — ' . $client->name)
@section('page-title', 'Client Profile')

@section('content')
<div class="row g-4">
    <!-- Left: Client Info -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="fas fa-user-tie"></i> Client Details</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('clients.edit', $client) }}" class="button button-secondary small"><i class="fas fa-edit"></i></a>
                </div>
            </div>
            <div class="p-4">
                <div class="text-center mb-4">
                    <div style="width:80px;height:80px;border-radius:16px;background:rgba(255,85,50,0.12);color:var(--first-color);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:2rem;margin:0 auto 12px;">
                        {{ strtoupper(substr($client->name, 0, 1)) }}
                    </div>
                    <h4 class="fw-bold mb-0">{{ $client->name }}</h4>
                    @if($client->company)<p class="text-muted">{{ $client->company }}</p>@endif
                    <span class="badge rounded-pill {{ $client->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </div>
                <dl class="profile-list">
                    @if($client->email)
                    <div>
                        <dt><i class="fas fa-envelope me-1"></i> Email</dt>
                        <dd>{{ $client->email }}</dd>
                    </div>
                    @endif
                    @if($client->phone)
                    <div>
                        <dt><i class="fas fa-phone me-1"></i> Phone</dt>
                        <dd>{{ $client->phone }}</dd>
                    </div>
                    @endif
                    @if($client->address)
                    <div>
                        <dt><i class="fas fa-map-marker-alt me-1"></i> Address</dt>
                        <dd>{{ $client->address }}</dd>
                    </div>
                    @endif
                    @if($client->gst_no)
                    <div>
                        <dt>GST No.</dt>
                        <dd class="font-monospace">{{ $client->gst_no }}</dd>
                    </div>
                    @endif
                    @if($client->pan_no)
                    <div>
                        <dt>PAN No.</dt>
                        <dd class="font-monospace">{{ $client->pan_no }}</dd>
                    </div>
                    @endif
                </dl>
                @if($client->notes)
                <div class="mt-3 p-3 rounded" style="background:var(--surface-soft);font-size:0.85rem;color:var(--muted);">
                    <strong>Notes:</strong> {{ $client->notes }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right: Financial Summary + Invoices -->
    <div class="col-12 col-lg-8">
        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card p-3 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Total Billed</div>
                    <div class="fw-bold fs-5">₹{{ number_format($totalBilled, 2) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card p-3 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Total Paid</div>
                    <div class="fw-bold fs-5 text-success">₹{{ number_format($totalPaid, 2) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="card p-3 text-center">
                    <div class="text-muted small fw-bold text-uppercase mb-1">Outstanding Due</div>
                    <div class="fw-bold fs-5 {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($totalDue, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card p-0" style="border-radius:12px;overflow:hidden;">
            <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2 text-first"></i>Invoices</h5>
                <a href="{{ route('client_invoices.create', ['client_id' => $client->id]) }}" class="button button-primary small py-1 px-3">
                    <i class="fas fa-plus me-1"></i>New Invoice
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:var(--surface-soft);">
                        <tr>
                            <th class="ps-4" style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Invoice No</th>
                            <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Total</th>
                            <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Paid</th>
                            <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Due</th>
                            <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Status</th>
                            <th class="text-end pe-4" style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($client->invoices as $inv)
                        <tr>
                            <td class="ps-4 fw-bold text-first">{{ $inv->invoice_no }}</td>
                            <td>₹{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="text-success fw-bold">₹{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="{{ $inv->due_amount > 0 ? 'text-danger' : 'text-success' }} fw-bold">₹{{ number_format($inv->due_amount, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ strtolower($inv->status) === 'paid' ? 'success' : (strtolower($inv->status) === 'unpaid' ? 'danger' : 'warning') }}">
                                    {{ $inv->status }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('client_invoices.show', $inv) }}" class="button button-secondary small py-1 px-3" target="_blank">
                                    <i class="fas fa-print me-1"></i>Print
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No invoices for this client yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
