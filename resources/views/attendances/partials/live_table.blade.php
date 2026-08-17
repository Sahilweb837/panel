@forelse($allAttendances as $attendance)
    <tr class="attendance-row align-middle" data-role="{{ strtolower($attendance->role) }}" data-id="{{ $attendance->id }}">
        <td class="ps-4" style="width: 70px;">
            <div class="position-relative d-inline-block">
                @if($attendance->photo)
                    <img src="{{ asset($attendance->photo) }}" 
                         width="48" height="48" 
                         class="rounded-circle shadow-sm" 
                         style="object-fit: cover; cursor: pointer; border: 2px solid {{ $attendance->role === 'Staff' ? '#6366f1' : '#ff5532' }};" 
                         onclick="showImageModal('{{ asset($attendance->photo) }}', '{{ addslashes($attendance->name) }}')"
                         onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                         style="width: 48px; height: 48px; background: {{ $attendance->role === 'Staff' ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'linear-gradient(135deg, #ff5532, #ff8a65)' }}; font-size: 1.15rem;">
                        {{ strtoupper(substr($attendance->name, 0, 1)) }}
                    </div>
                @endif
                <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $attendance->status === 'Present' ? 'success' : ($attendance->status === 'Late' ? 'warning' : 'danger') }} border border-white rounded-circle" style="width: 12px; height: 12px;" title="{{ $attendance->status }}"></span>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark fs-6">{{ $attendance->name }}</span>
                    @if($attendance->code && $attendance->code !== '-')
                        <span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.72rem; font-family: monospace;">{{ $attendance->code }}</span>
                    @endif
                </div>
                <small class="text-muted" style="font-size: 0.78rem;">{{ $attendance->sub_title }}</small>
            </div>
        </td>
        <td>
            @if($attendance->role === 'Staff')
                <span class="badge rounded-pill px-3 py-1.5 shadow-sm" style="background-color: #6366f1; color: #ffffff; font-size: 0.78rem; font-weight: 600;">
                    <i class="fas fa-chalkboard-teacher me-1"></i> Faculty Staff
                </span>
            @else
                <span class="badge rounded-pill px-3 py-1.5 shadow-sm" style="background-color: #0ea5e9; color: #ffffff; font-size: 0.78rem; font-weight: 600;">
                    <i class="fas fa-user-graduate me-1"></i> Student
                </span>
            @endif
        </td>
        <td>
            @if($attendance->status === 'Present')
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5" style="font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-check-circle me-1"></i> Present
                </span>
            @elseif($attendance->status === 'Late')
                <span class="badge bg-warning bg-opacity-15 text-warning-emphasis border border-warning border-opacity-50 rounded-pill px-3 py-1.5" style="font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-clock me-1"></i> Late
                </span>
            @elseif($attendance->status === 'Leave')
                <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-25 rounded-pill px-3 py-1.5" style="font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-plane-departure me-1"></i> Leave
                </span>
            @else
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1.5" style="font-size: 0.8rem; font-weight: 600;">
                    <i class="fas fa-times-circle me-1"></i> Absent
                </span>
            @endif
        </td>
        <td class="text-center">
            <div class="d-inline-flex flex-column align-items-center gap-1">
                <div class="d-flex align-items-center gap-2">
                    @if($attendance->check_in)
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2.5 py-1" style="font-size: 0.82rem; font-weight: 600;" title="Check-in Time">
                            <i class="fas fa-sign-in-alt me-1 text-success"></i> IN: {{ $attendance->check_in }}
                        </span>
                    @else
                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.78rem;">No Check In</span>
                    @endif

                    @if($attendance->check_out)
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-2.5 py-1" style="font-size: 0.82rem; font-weight: 600;" title="Check-out Time">
                            <i class="fas fa-sign-out-alt me-1 text-danger"></i> OUT: {{ $attendance->check_out }}
                        </span>
                    @else
                        <span class="badge bg-light text-warning-emphasis border px-2 py-1" style="font-size: 0.75rem;">
                            <i class="fas fa-spinner fa-spin text-warning me-1"></i> Still In
                        </span>
                    @endif
                </div>

                @if($attendance->duration)
                    <div class="mt-0.5">
                        <span class="text-muted small" style="font-size: 0.72rem; font-weight: 600;">
                            <i class="fas fa-stopwatch me-1 text-primary"></i>{{ $attendance->duration }}
                        </span>
                    </div>
                @endif
            </div>
        </td>
        <td>
            <div class="d-flex align-items-center text-muted small" style="font-size: 0.8rem;">
                @php
                    $dev = strtolower($attendance->device ?? '');
                    $icon = 'fa-laptop';
                    if(str_contains($dev, 'camera') || str_contains($dev, 'face') || str_contains($dev, 'ai')) {
                        $icon = 'fa-camera text-primary';
                    } elseif(str_contains($dev, 'biometric') || str_contains($dev, 'zkteco') || str_contains($dev, 'fingerprint')) {
                        $icon = 'fa-fingerprint text-success';
                    } elseif(str_contains($dev, 'dashboard') || str_contains($dev, 'console')) {
                        $icon = 'fa-desktop text-info';
                    }
                @endphp
                <i class="fas {{ $icon }} me-1.5"></i>
                <span class="text-truncate" style="max-width: 170px;" title="{{ $attendance->device ?? 'System' }}">
                    {{ $attendance->device ?? 'System' }}
                </span>
            </div>
        </td>
        <td class="text-end pe-4">
            <span class="text-muted small" style="font-size: 0.78rem;">
                <i class="fas fa-history me-1 text-secondary opacity-75"></i>{{ $attendance->time }}
            </span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <div class="py-4">
                <i class="fas fa-clock fa-3x mb-3 text-muted opacity-50"></i>
                <h5 class="fw-bold text-dark">No Attendance Records Yet Today</h5>
                <p class="small text-muted mb-0">Records will automatically stream in here as soon as staff or students check in.</p>
            </div>
        </td>
    </tr>
@endforelse

