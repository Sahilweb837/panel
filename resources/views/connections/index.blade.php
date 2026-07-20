@extends('layouts.app')

@section('title', 'My Connections')
@section('page-title', 'My Connections')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card premium-card">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-plus text-primary me-2"></i> Pending Requests</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($pendingConnections as $connection)
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h6 class="mb-0">{{ $connection->requester->name }}</h6>
                                    <small class="text-muted">{{ $connection->requester->email }}</small>
                                </div>
                                <div>
                                    <form action="{{ route('connections.update', $connection) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Accept</button>
                                    </form>
                                    <form action="{{ route('connections.update', $connection) }}" method="POST" class="d-inline">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">
                                No pending connection requests.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card premium-card">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-users text-success me-2"></i> My Connections</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($connectedUsers as $connection)
                            @php
                                $connectedUser = $connection->requester_id === Auth::id() ? $connection->recipient : $connection->requester;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <h6 class="mb-0">{{ $connectedUser->name }}</h6>
                                    <small class="text-muted">{{ $connectedUser->email }}</small>
                                </div>
                                <span class="badge bg-success rounded-pill">Connected</span>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">
                                You don't have any connections yet.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
