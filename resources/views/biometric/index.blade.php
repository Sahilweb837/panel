@extends('layouts.app')

@section('title', 'Biometric Device Settings')
@section('page-title', 'ZKTeco Hardware Integration')

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-network-wired text-primary me-2"></i> Device Connection Settings</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('biometric.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Device Name</label>
                        <input type="text" class="form-control" value="{{ $device->name }}" disabled>
                        <div class="form-text">Name is for display purposes only.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">IP Address</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address', $device->ip_address) }}" required>
                        <div class="form-text">The local IP address of your ZKTeco device (e.g., 192.168.1.201).</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Port</label>
                        <input type="number" name="port" class="form-control" value="{{ old('port', $device->port) }}" required>
                        <div class="form-text">Default is usually 4370.</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold"><i class="fas fa-sync-alt text-success me-2"></i> Synchronize Logs</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">
                
                <i class="fas fa-fingerprint fa-5x text-muted mb-4 opacity-50"></i>
                
                <h4 class="fw-bold">Pull Attendance Logs</h4>
                <p class="text-muted">Connect to the ZKTeco hardware over the local network and download all pending attendance logs into the web application.</p>
                
                <p class="small text-muted mb-4">Last Synced: <strong>{{ $device->last_sync ? \Carbon\Carbon::parse($device->last_sync)->diffForHumans() : 'Never' }}</strong></p>

                <div class="d-flex gap-2 w-100 justify-content-center">
                    <form action="{{ route('biometric.test') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-wifi me-2"></i> Test Connection
                        </button>
                    </form>
                    
                    <form action="{{ route('biometric.sync') }}" method="POST" onsubmit="return confirm('This will pull all logs from the biometric device. Ensure the device is powered on. Continue?');">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-download me-2"></i> Sync Now
                        </button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm rounded-4">
            <h5 class="fw-bold"><i class="fas fa-info-circle me-2"></i> How to map users to the biometric device?</h5>
            <p class="mb-0">
                To correctly link a fingerprint punch to a student or staff member, you must add their <strong>Biometric ID</strong> (the ID assigned to them on the physical machine) to their profile in this web application. Go to Edit Student or Edit Staff to set their Biometric ID.
            </p>
        </div>
    </div>
</div>
@endsection
