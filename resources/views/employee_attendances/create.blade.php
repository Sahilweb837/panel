@extends('layouts.app')

@section('title', 'Record Staff Attendance')

@section('page-title', 'Record Daily Staff Attendance')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card p-4 border-0 shadow-sm">
                <form method="GET" action="{{ route('employee-attendances.create') }}" id="date-select-form" class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                    <div style="flex: 1; min-width: 250px;">
                        <label for="date-picker" class="form-label text-muted uppercase-bold mb-2">Select Attendance Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-calendar-alt text-first"></i></span>
                            <input type="date" id="date-picker" name="date" class="form-control border-start-0" value="{{ $date }}" max="{{ date('Y-m-d') }}" onchange="document.getElementById('date-select-form').submit();" style="border-radius: 0 8px 8px 0; padding: 10px 14px;" />
                        </div>
                        <small class="text-muted d-block mt-2">Changing the date will automatically load any previously recorded attendance for that day.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" onclick="markAll('Present')" class="button btn-light px-3"><i class="fas fa-check-double me-2"></i>Mark All Present</button>
                        <button type="button" onclick="markAll('Absent')" class="button button-danger small px-3" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.2);"><i class="fas fa-times-circle me-2"></i>Mark All Absent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($employees->isEmpty())
        <div class="card text-center py-5">
            <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
            <h4>No Employees Found</h4>
            <p class="text-muted">Please add active staff members first under the Staff Management section.</p>
            <a href="{{ route('employees.create') }}" class="button button-primary mt-2">Add Staff Member</a>
        </div>
    @else
        <div class="card table-card border-0 shadow-sm">
            <form action="{{ route('employee-attendances.store') }}" method="POST" id="attendance-log-form">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date }}" />

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light-head">
                            <tr>
                                <th class="ps-4" style="width: 25%;">Staff Member</th>
                                <th style="width: 10%;">Code</th>
                                <th style="width: 25%;" class="text-center">Attendance Status</th>
                                <th style="width: 20%;" class="text-center">Check-in Time</th>
                                <th class="pe-4" style="width: 20%;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                 @php
                                     $existing = $existingAttendances->get($employee->id);
                                     $status = $existing ? $existing->status : 'Present';
                                     $remarks = $existing ? $existing->remarks : '';
                                     $checkInTime = $existing ? $existing->check_in_time : '';
                                 @endphp
                                 <tr>
                                     <td class="ps-4">
                                         <div class="d-flex align-items-center">
                                             <div class="avatar-circle-sm bg-light-orange text-first me-3" style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; background-color: var(--first-color-light);">
                                                 {{ strtoupper(substr($employee->user->name ?? 'S', 0, 2)) }}
                                             </div>
                                             <div>
                                                 <h6 class="mb-0 text-dark-title">{{ $employee->user->name ?? 'N/A' }}</h6>
                                                 <small class="text-muted">{{ $employee->department ?? 'General' }} &bull; {{ $employee->designation ?? 'Staff' }}</small>
                                             </div>
                                         </div>
                                     </td>
                                     <td>
                                         <span class="badge bg-light text-dark border">{{ $employee->employee_code }}</span>
                                     </td>
                                     <td class="text-center">
                                         <div class="d-inline-flex gap-2" role="group" aria-label="Attendance Status Toggle">
                                             <!-- Present -->
                                             <input type="radio" class="btn-check" name="attendance[{{ $employee->id }}][status]" id="status_{{ $employee->id }}_present" value="Present" {{ $status === 'Present' ? 'checked' : '' }} autocomplete="off" onchange="handleStatusChange({{ $employee->id }}, 'Present')" />
                                             <label class="btn btn-outline-success btn-sm px-3 rounded-pill" style="font-size: 0.8rem; font-weight: 600;" for="status_{{ $employee->id }}_present">Present</label>
 
                                             <!-- Late -->
                                             <input type="radio" class="btn-check" name="attendance[{{ $employee->id }}][status]" id="status_{{ $employee->id }}_late" value="Late" {{ $status === 'Late' ? 'checked' : '' }} autocomplete="off" onchange="handleStatusChange({{ $employee->id }}, 'Late')" />
                                             <label class="btn btn-outline-warning btn-sm px-3 rounded-pill" style="font-size: 0.8rem; font-weight: 600;" for="status_{{ $employee->id }}_late">Late</label>
 
                                             <!-- Absent -->
                                             <input type="radio" class="btn-check" name="attendance[{{ $employee->id }}][status]" id="status_{{ $employee->id }}_absent" value="Absent" {{ $status === 'Absent' ? 'checked' : '' }} autocomplete="off" onchange="handleStatusChange({{ $employee->id }}, 'Absent')" />
                                             <label class="btn btn-outline-danger btn-sm px-3 rounded-pill" style="font-size: 0.8rem; font-weight: 600;" for="status_{{ $employee->id }}_absent">Absent</label>
 
                                             <!-- Leave -->
                                             <input type="radio" class="btn-check" name="attendance[{{ $employee->id }}][status]" id="status_{{ $employee->id }}_leave" value="Leave" {{ $status === 'Leave' ? 'checked' : '' }} autocomplete="off" onchange="handleStatusChange({{ $employee->id }}, 'Leave')" />
                                             <label class="btn btn-outline-info btn-sm px-3 rounded-pill" style="font-size: 0.8rem; font-weight: 600;" for="status_{{ $employee->id }}_leave">Leave</label>
                                         </div>
                                     </td>
                                     <td class="text-center">
                                         <div class="d-flex align-items-center justify-content-center gap-1">
                                             <input type="text" 
                                                    name="attendance[{{ $employee->id }}][check_in_time]" 
                                                    id="time_{{ $employee->id }}" 
                                                    class="form-control text-center" 
                                                    placeholder="--:-- --" 
                                                    value="{{ $checkInTime }}" 
                                                    style="width: 110px; padding: 6px; font-size: 0.85rem; border-radius: 6px; font-weight: bold; background-color: var(--card-bg);" />
                                             <button type="button" 
                                                     onclick="setCurrentTime({{ $employee->id }})" 
                                                     class="btn btn-outline-secondary btn-sm p-1 d-flex align-items-center justify-content-center" 
                                                     title="Set Current Time" 
                                                     style="width: 32px; height: 32px; border-radius: 6px;">
                                                 <i class="fas fa-clock"></i>
                                             </button>
                                         </div>
                                     </td>
                                     <td class="pe-4">
                                         <input type="text" name="attendance[{{ $employee->id }}][remarks]" class="form-control" placeholder="Add optional remarks..." value="{{ $remarks }}" style="padding: 6px 12px; font-size: 0.85rem; border-radius: 6px;" />
                                     </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer p-4 border-top bg-light d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-1 text-first"></i>
                        <span>Clicking save will write/update records for <strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong>.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('employee-attendances.index') }}" class="button btn-light px-4">Cancel</a>
                        <button type="submit" class="button button-primary px-5"><i class="fas fa-save me-2"></i>Save Daily Attendance</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <script>
        function getFormattedTime() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            return hours + ':' + minutes + ' ' + ampm;
        }

        function setCurrentTime(id) {
            const timeInput = document.getElementById('time_' + id);
            timeInput.value = getFormattedTime();
        }

        function handleStatusChange(id, status) {
            const timeInput = document.getElementById('time_' + id);
            if (status === 'Present' || status === 'Late') {
                if (!timeInput.value) {
                    timeInput.value = getFormattedTime();
                }
            } else {
                timeInput.value = '';
            }
        }

        function markAll(status) {
            const checks = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
            checks.forEach(radio => {
                radio.checked = true;
                // Extract employee ID from radio ID: e.g. status_12_present
                const matches = radio.id.match(/status_(\d+)_/);
                if (matches && matches[1]) {
                    handleStatusChange(matches[1], status);
                }
            });
        }
    </script>
@endsection
