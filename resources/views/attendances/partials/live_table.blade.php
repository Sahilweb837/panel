@forelse($allAttendances as $attendance)
    <tr>
        <td class="ps-4 fw-medium text-muted">
            <i class="far fa-clock me-1"></i> {{ $attendance->time }}
        </td>
        <td>
            @if($attendance->photo)
                <img src="{{ asset($attendance->photo) }}" width="45" height="45" class="rounded-circle shadow-sm" style="object-fit: cover; cursor: pointer;" onclick="showImageModal('{{ asset($attendance->photo) }}', '{{ $attendance->name }}')">
            @else
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 45px; height: 45px;">
                    <i class="fas fa-user"></i>
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
        <td class="text-muted small">
            <i class="fas {{ str_contains(strtolower($attendance->device), 'web') ? 'fa-laptop' : 'fa-mobile-alt' }} me-1"></i> 
            {{ $attendance->device ?? 'System' }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
            <h5>No attendance records today</h5>
            <p>Records will appear here automatically when students/staff check in.</p>
        </td>
    </tr>
@endforelse
