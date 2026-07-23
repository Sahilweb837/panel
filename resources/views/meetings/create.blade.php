@extends('layouts.app')

@section('title', 'Schedule Meeting')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="m-0"><i class="fas fa-calendar-plus text-primary me-2"></i> Schedule New Meeting</h4>
        </div>
    </div>

    @include('layouts.alerts')

    <form action="{{ route('meetings.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Meeting Details</h5>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Meeting Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Monthly Department Review" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Department <span class="text-muted fw-normal">(Optional)</span></label>
                                <select name="department_id" class="form-select">
                                    <option value="">General (No specific department)</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Meeting Mode <span class="text-danger">*</span></label>
                                <select name="meeting_mode" class="form-select" id="meetingModeSelect" required>
                                    <option value="Online">Online (Video Call)</option>
                                    <option value="Offline">Offline (In Person)</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                                <input type="date" name="meeting_date" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Start Time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">End Time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3" id="locationContainer" style="display:none;">
                            <label class="form-label fw-bold">Physical Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Conference Room A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description / Agenda</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Enter meeting agenda or details..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Invite Participants</h5>
                        <p class="text-muted small mb-3">Select the staff members you want to invite to this meeting.</p>
                        <div style="max-height: 400px; overflow-y: auto;" class="pe-2">
                            @foreach($staff as $user)
                            <div class="form-check mb-3 custom-checkbox-card">
                                <input class="form-check-input mt-2" type="checkbox" name="participants[]" value="{{ $user->id }}" id="user_{{ $user->id }}">
                                <label class="form-check-label w-100 p-2 border rounded cursor-pointer d-flex align-items-center" for="user_{{ $user->id }}" style="cursor: pointer;">
                                    <div class="avatar-sm me-3" style="width:35px;height:35px;border-radius:50%;background:var(--first-color);color:#fff;display:flex;align-items:center;justify-content:center;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                    <i class="fas fa-paper-plane me-2"></i> Schedule Meeting
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeSelect = document.getElementById('meetingModeSelect');
    const locationDiv = document.getElementById('locationContainer');
    
    modeSelect.addEventListener('change', function() {
        if(this.value === 'Offline' || this.value === 'Hybrid') {
            locationDiv.style.display = 'block';
        } else {
            locationDiv.style.display = 'none';
        }
    });
});
</script>
<style>
.custom-checkbox-card input:checked + label {
    border-color: var(--first-color) !important;
    background-color: rgba(16, 185, 129, 0.05);
}
</style>
@endsection
