@extends('layouts.app')

@section('title', 'My Meetings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="m-0"><i class="fas fa-handshake text-primary me-2"></i> My Assigned Meetings</h4>
            <a href="{{ route('meetings.create') }}" class="button button-primary py-2 px-3 shadow-sm"><i class="fas fa-plus me-1"></i> Create Meeting</a>
        </div>
    </div>

    @include('layouts.alerts')

    <div class="row">
        @forelse($meetings as $meeting)
        @php
            $myStatus = $meeting->participants->where('user_id', session('user_id'))->first()->invitation_status ?? 'Pending';
        @endphp
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; overflow:hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="fw-bold mb-1">{{ $meeting->title }}</h5>
                        @if($myStatus == 'Pending')
                            <span class="badge bg-warning text-dark rounded-pill px-3">Pending Invitation</span>
                        @elseif($myStatus == 'Accepted')
                            <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check"></i> Accepted</span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3"><i class="fas fa-times"></i> Declined</span>
                        @endif
                    </div>
                    <div class="text-muted small">
                        <i class="fas fa-building me-1"></i> {{ $meeting->department ? $meeting->department->department_name : 'General' }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center mb-2">
                            <i class="far fa-calendar-alt text-primary me-3 fs-5"></i>
                            <div>
                                <div class="small text-muted mb-0" style="line-height: 1;">Date</div>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="far fa-clock text-primary me-3 fs-5"></i>
                            <div>
                                <div class="small text-muted mb-0" style="line-height: 1;">Time</div>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="small text-muted fw-bold mb-1">Mode</div>
                        @if($meeting->meeting_mode == 'Online')
                            <div class="text-success fw-bold"><i class="fas fa-video me-1"></i> Online Meeting</div>
                        @elseif($meeting->meeting_mode == 'Offline')
                            <div class="text-secondary fw-bold"><i class="fas fa-map-marker-alt me-1"></i> Offline: {{ $meeting->location }}</div>
                        @else
                            <div class="text-info fw-bold"><i class="fas fa-random me-1"></i> Hybrid (Online/Offline)</div>
                        @endif
                    </div>

                    @if($myStatus == 'Pending')
                        <div class="d-flex gap-2 mt-4">
                            @php $pId = $meeting->participants->where('user_id', session('user_id'))->first()->id; @endphp
                            <form action="{{ route('meetings.updateStatus', $pId) }}" method="POST" class="w-50">
                                @csrf
                                <input type="hidden" name="status" value="Accepted">
                                <button type="submit" class="btn btn-success w-100"><i class="fas fa-check"></i> Accept</button>
                            </form>
                            <form action="{{ route('meetings.updateStatus', $pId) }}" method="POST" class="w-50">
                                @csrf
                                <input type="hidden" name="status" value="Declined">
                                <button type="submit" class="btn btn-outline-danger w-100"><i class="fas fa-times"></i> Decline</button>
                            </form>
                        </div>
                    @elseif($myStatus == 'Accepted')
                        <div class="d-flex gap-2 mt-2">
                            <a href="{{ route('meetings.show', $meeting->id) }}" class="btn btn-primary flex-fill">
                                <i class="fas fa-info-circle"></i> Details
                            </a>
                            @if(in_array($meeting->meeting_mode, ['Online', 'Hybrid']) && $meeting->meeting_link)
                            <a href="{{ $meeting->meeting_link }}" target="_blank" class="btn btn-success flex-fill">
                                <i class="fas fa-video"></i> Join
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-mug-hot fa-3x text-muted mb-3"></i>
                    <h5>No Meetings Assigned</h5>
                    <p class="text-muted">You have no upcoming meetings or pending invitations.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
