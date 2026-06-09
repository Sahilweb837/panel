@forelse($allAttendances as $attendance)
    <tr>
        <td class="ps-4">
            @if($attendance->photo)
                <img src="{{ asset($attendance->photo) }}" width="45" height="45" class="rounded-circle shadow-sm" style="object-fit: cover; cursor: pointer; border: 2px solid var(--first-color, #ff5532);" onclick="showImageModal('{{ asset($attendance->photo) }}', '{{ $attendance->name }}')">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 45px; height: 45px; background: linear-gradient(135deg, #ff5532, #ff8a65); font-size: 1.1rem;">
                    {{ strtoupper(substr($attendance->name, 0, 1)) }}
                </div>
            @endif
        </td>
        <td class="fw-bold">{{ $attendance->name }}</td>
        <td>
            <span class="badge bg-{{ $attendance->role === 'Student' ? 'info' : 'secondary' }} rounded-pill text-white">
                {{ $attendance->role }}
            </span>
        </td>
        <td>
            <span class="badge bg-{{ $attendance->status === 'Present' ? 'success' : ($attendance->status === 'Absent' ? 'danger' : 'warning') }} rounded-pill px-3">
                {{ $attendance->status }}
            </span>
        </td>
        <td class="text-center">
            <div class="d-flex flex-column align-items-center gap-1 justify-content-center">
                @if($attendance->check_in)
                    <span class="badge bg-light text-success border px-2 py-1.5" style="font-size: 0.8rem; font-weight: 600; min-width: 95px;">
                        <i class="fas fa-sign-in-alt me-1 text-success"></i>{{ $attendance->check_in }}
                    </span>
                @endif
                @if($attendance->check_out)
                    <span class="badge bg-light text-danger border px-2 py-1.5" style="font-size: 0.8rem; font-weight: 600; min-width: 95px;">
                        <i class="fas fa-sign-out-alt me-1 text-danger"></i>{{ $attendance->check_out }}
                    </span>
                @else
                    <span class="text-muted small" style="font-size: 0.75rem;">No Out Punch</span>
                @endif
            </div>
        </td>
        <td class="text-muted small">
            <i class="fas {{ str_contains(strtolower($attendance->device ?? ''), 'web') ? 'fa-laptop' : 'fa-fingerprint' }} me-1"></i> 
            {{ $attendance->device ?? 'System' }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
            <h5>No attendance records today</h5>
            <p>Records will appear here automatically when students/staff check in.</p>
        </td>
    </tr>
@endforelse
