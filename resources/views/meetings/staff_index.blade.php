@extends('layouts.app')

@section('title', 'My Meetings - Netcoder')
@section('page-title', 'My Scheduled Meetings')

@section('content')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    .lift-card {
        transition: all 0.3s ease;
    }
    .lift-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.06) !important;
    }
</style>

<div class="max-w-[1440px] mx-auto p-4 md:p-6 w-full">
    <!-- Alerts/Notifications -->
    @include('layouts.alerts')

    <!-- PAGE HEADER -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <p class="text-primary font-label-sm uppercase tracking-wider mb-2 font-bold font-mono">Faculty Suite</p>
            <h3 class="font-headline-lg text-2xl md:text-3xl font-black text-on-surface mb-0">Assigned Meetings</h3>
        </div>
        <div>
            <span class="badge bg-primary px-3 py-2 fs-6">Total: {{ $meetings->count() }}</span>
        </div>
    </div>

    <!-- CARDS GRID -->
    <div class="row">
        @forelse($meetings as $meeting)
            @php
                $myStatus = $meeting->participants->where('user_id', session('user_id'))->first()->invitation_status ?? 'Pending';
                $mDate = \Carbon\Carbon::parse($meeting->meeting_date);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border border-border-subtle bg-white shadow-sm lift-card" style="border-radius: 16px; overflow:hidden;">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <span class="px-2 py-0.5 text-[9px] font-bold rounded-full {{ $meeting->meeting_mode === 'Online' ? 'bg-success-green/10 text-success-green' : 'bg-info-blue/10 text-info-blue' }}">{{ $meeting->meeting_mode }}</span>
                            @if($myStatus == 'Pending')
                                <span class="badge bg-warning text-dark px-2.5 py-1 text-[10px] rounded-full">Pending Invite</span>
                            @elseif($myStatus == 'Accepted')
                                <span class="badge bg-success px-2.5 py-1 text-[10px] rounded-full"><i class="fas fa-check"></i> Accepted</span>
                            @else
                                <span class="badge bg-danger px-2.5 py-1 text-[10px] rounded-full"><i class="fas fa-times"></i> Declined</span>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1 text-on-surface text-base">
                            <a href="{{ route('meetings.show', $meeting->id) }}" class="text-decoration-none text-on-surface hover:text-primary">
                                {{ $meeting->title }}
                            </a>
                        </h5>
                        <div class="text-muted small">
                            <i class="fas fa-building me-1"></i> {{ $meeting->department ? $meeting->department->department_name : 'General' }}
                        </div>
                    </div>
                    <div class="card-body d-flex flex-col justify-between">
                        <div class="mb-4 p-3 bg-surface-slate rounded-xl border border-border-subtle mt-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="far fa-calendar-alt text-primary me-3 fs-5"></i>
                                <div>
                                    <div class="small text-muted mb-0" style="line-height: 1; font-size: 10px;">Date</div>
                                    <div class="fw-bold text-sm text-on-surface">{{ $mDate->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="far fa-clock text-primary me-3 fs-5"></i>
                                <div>
                                    <div class="small text-muted mb-0" style="line-height: 1; font-size: 10px;">Time</div>
                                    <div class="fw-bold text-sm text-on-surface">{{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            @if($myStatus == 'Pending')
                                <div class="d-flex gap-2">
                                    @php $pId = $meeting->participants->where('user_id', session('user_id'))->first()->id; @endphp
                                    <form action="{{ route('meetings.updateStatus', $pId) }}" method="POST" class="w-50">
                                        @csrf
                                        <input type="hidden" name="status" value="Accepted">
                                        <button type="submit" class="btn btn-success btn-sm w-100 rounded-lg fw-bold"><i class="fas fa-check"></i> Accept</button>
                                    </form>
                                    <form action="{{ route('meetings.updateStatus', $pId) }}" method="POST" class="w-50">
                                        @csrf
                                        <input type="hidden" name="status" value="Declined">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100 rounded-lg fw-bold"><i class="fas fa-times"></i> Decline</button>
                                    </form>
                                </div>
                            @elseif($myStatus == 'Accepted')
                                <div class="d-flex gap-2">
                                    <a href="{{ route('meetings.show', $meeting->id) }}" class="btn btn-light border btn-sm flex-fill rounded-lg fw-bold text-decoration-none text-center">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    @if(in_array($meeting->meeting_mode, ['Online', 'Hybrid']))
                                        <a href="{{ route('meetings.join', ['id' => str_replace('meet-', '', $meeting->room_id ?? uniqid('meet-'))]) }}" class="btn btn-success btn-sm flex-fill rounded-lg fw-bold text-white text-decoration-none text-center">
                                            <i class="fas fa-video"></i> Join Room
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5 rounded-4 bg-white border border-border-subtle">
                    <div class="card-body">
                        <i class="fas fa-mug-hot fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="fw-bold">No Meetings Assigned</h5>
                        <p class="text-muted">You have no upcoming meetings or pending invitations.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
