@extends('layouts.app')
@section('title', 'Clients')
@section('page-title', 'Client Management')

@section('content')
<style>
    .client-avatar {
        width: 42px; height: 42px; border-radius: 8px;
        background: rgba(255,85,50,0.12); color: var(--first-color);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.1rem; flex-shrink: 0;
    }
    .user-info { display: flex; align-items: center; gap: 12px; }
    .table-card { border-radius: 12px; overflow: hidden; }
    .sk-card {
        background: linear-gradient(90deg, var(--surface-soft) 25%, var(--border) 50%, var(--surface-soft) 75%);
        background-size: 200% 100%; animation: loadingSkeleton 1.5s infinite linear;
        border-radius: 10px; border: 1px solid var(--border);
    }
    @keyframes loadingSkeleton { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
</style>

<div>
    <!-- Toolbar -->
    <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <form method="GET" action="{{ route('clients.index') }}" class="d-flex align-items-center gap-2 flex-grow-1">
            <div style="position:relative; flex:1; max-width:340px;">
                <input type="text" name="search" placeholder="Search by name, company, email..." value="{{ request('search') }}" class="form-input" style="padding-left:36px;" />
                <i class="fas fa-search text-muted position-absolute" style="left:14px;top:50%;transform:translateY(-50%);"></i>
            </div>
            <select name="status" class="form-input" style="width:150px;">
                <option value="">All Status</option>
                <option value="active" {{ request('status')==='active'?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
            </select>
            <button type="submit" class="button button-secondary px-4 py-2">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('clients.index') }}" class="button button-secondary px-3 py-2"><i class="fas fa-undo"></i></a>
            @endif
        </form>
        <div class="d-flex gap-2 align-items-center">
            <div class="form-check form-switch me-2 d-flex align-items-center gap-2">
                <input class="form-check-input mt-0" type="checkbox" role="switch" id="toggleTrash"
                    {{ request('trashed') ? 'checked' : '' }}
                    onchange="window.location.href='{{ request()->fullUrlWithQuery(['trashed' => request('trashed') ? null : '1']) }}'">
                <label class="form-check-label fw-bold" for="toggleTrash" style="cursor:pointer;">Recycle Bin</label>
            </div>
            <a href="{{ route('clients.create') }}" class="button button-primary py-2 px-4">
                <i class="fas fa-plus me-2"></i>Add Client
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="card p-0 table-card">
        <div class="p-4 border-bottom d-flex align-items-center gap-2">
            <i class="fas fa-handshake text-first"></i>
            <h5 class="mb-0 fw-bold">All Clients</h5>
            <span class="badge bg-light border text-dark ms-auto">{{ $clients->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width:700px;">
                <thead style="background:var(--surface-soft);">
                    <tr>
                        <th class="ps-4" style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Client</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Contact</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">GST / PAN</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Invoices</th>
                        <th style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Status</th>
                        <th class="text-end pe-4" style="font-size:.75rem;text-transform:uppercase;color:var(--muted);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td class="ps-4">
                            <div class="user-info">
                                <div class="client-avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $client->name }}</strong>
                                    @if($client->company)
                                        <p class="text-muted small mb-0">{{ $client->company }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="small">
                                @if($client->email)<div><i class="fas fa-envelope me-1 text-muted"></i>{{ $client->email }}</div>@endif
                                @if($client->phone)<div><i class="fas fa-phone me-1 text-muted"></i>{{ $client->phone }}</div>@endif
                            </div>
                        </td>
                        <td class="text-muted small">
                            @if($client->gst_no)<div>GST: <strong>{{ $client->gst_no }}</strong></div>@endif
                            @if($client->pan_no)<div>PAN: <strong>{{ $client->pan_no }}</strong></div>@endif
                            @if(!$client->gst_no && !$client->pan_no) — @endif
                        </td>
                        <td>
                            <span class="badge bg-light border text-dark">{{ $client->invoices_count }} invoice(s)</span>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $client->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($client->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            @if($client->trashed())
                                <form action="{{ route('clients.restore', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirmAction(event, 'Restore this client?');">
                                    @csrf
                                    <button class="button button-secondary small py-1 px-3"><i class="fas fa-trash-restore me-1"></i>Restore</button>
                                </form>
                            @else
                                <a href="{{ route('clients.show', $client) }}" class="button button-secondary small py-1 px-3"><i class="fas fa-eye me-1"></i>View</a>
                                <a href="{{ route('client_invoices.create', ['client_id' => $client->id]) }}" class="button button-secondary small py-1 px-3"><i class="fas fa-file-invoice me-1"></i>Invoice</a>
                                <a href="{{ route('clients.edit', $client) }}" class="button button-secondary small py-1 px-3"><i class="fas fa-edit me-1"></i>Edit</a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirmAction(event, 'Delete this client?');">
                                    @csrf @method('DELETE')
                                    <button class="button button-danger small py-1 px-3"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-handshake fa-2x d-block mb-2"></i>No clients found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $clients->links() }}</div>
</div>
@endsection
