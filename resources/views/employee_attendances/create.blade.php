@extends('layouts.app')

@section('title', 'Record Staff Attendance')
@section('page-title', 'Record Daily Staff Attendance')

@section('content')
    <div class="attendance-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="card premium-stat-card mb-4" style="height: 120px;"></div>
            <div class="card premium-form-card" style="max-width: 100%;">
                <div class="sk-text heading"></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card premium-stat-card p-4">
                        <form method="GET" action="{{ route('employee-attendances.create') }}" id="date-select-form" class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                            <div style="flex: 1; min-width: 250px;">
                                <label for="date-picker" class="form-label text-muted uppercase-bold mb-2">Select Attendance Date</label>
                                <div class="input-group" style="position: relative; max-width: 320px;">
                                    <span class="input-group-text bg-transparent border-end-0" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); z-index: 10;"><i class="fas fa-calendar-alt text-first"></i></span>
                                    <input type="date" id="date-picker" name="date" class="form-input border-start-0" value="{{ $date }}" max="{{ date('Y-m-d') }}" onchange="document.getElementById('date-select-form').submit();" style="padding-left: 44px;" />
                                </div>
                                <small class="text-muted d-block mt-2">Changing the date will automatically load any previously recorded attendance for that day.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" onclick="markAll('Present')" class="button button-secondary px-3"><i class="fas fa-check-double me-2 text-success"></i>Mark All Present</button>
                                <button type="button" onclick="markAll('Absent')" class="button button-secondary px-3" style="border-color: rgba(220, 53, 69, 0.3) !important; color: #dc3545 !important;"><i class="fas fa-times-circle me-2"></i>Mark All Absent</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if($employees->isEmpty())
                <div class="card premium-stat-card text-center py-5">
                    <i class="fas fa-users-slash fa-3x mb-3 text-muted"></i>
                    <h4 class="fw-bold mb-2">No Active Staff Members Found</h4>
                    <p class="text-muted mb-3">Please add active staff members first under the Staff Management section.</p>
                    <a href="{{ route('employees.create') }}" class="button button-primary">Add Staff Member</a>
                </div>
            @else
                <div class="card premium-stat-card p-0 table-card overflow-hidden">
                    <form action="{{ route('employee-attendances.store') }}" method="POST" id="attendance-log-form">
                        @csrf
                        <input type="hidden" name="attendance_date" value="{{ $date }}" />

                        <div class="premium-card-header bg-transparent border-bottom p-4">
                            <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-clipboard-check text-first"></i> Daily Staff Attendance Registry
                            </h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table premium-table align-middle mb-0">
                                <thead>
                                    <tr class="table-light-head">
                                        <th class="ps-4" style="width: 25%;">Staff Member</th>
                                        <th style="width: 12%;">Employee Code</th>
                                        <th style="width: 25%;" class="text-center">Attendance Status</th>
                                        <th style="width: 15%;" class="text-center">Check-in Time</th>
                                        <th style="width: 15%;" class="text-center">Check-out Time</th>
                                        <th style="width: 8%;" class="text-center">Photo</th>
                                        <th class="pe-4" style="width: 15%;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                         @php
                                             $existing = $existingAttendances->get($employee->id);
                                             $status = $existing ? $existing->status : 'Present';
                                             $remarks = $existing ? $existing->remarks : '';
                                             $checkInTime = $existing ? $existing->check_in_time : '';
                                             $checkOutTime = $existing ? $existing->check_out_time : '';
                                         @endphp
                                         <tr>
                                             <td class="ps-4">
                                                 <div class="d-flex align-items-center gap-3">
                                                     <div class="avatar-circle-sm d-grid place-items-center" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255, 85, 50, 0.1); color: var(--first-color); font-weight: bold;">
                                                         {{ strtoupper(substr($employee->user->name ?? 'S', 0, 1)) }}
                                                     </div>
                                                     <div>
                                                         <h6 class="mb-1 text-dark-title fw-bold" style="font-size: 0.95rem;">{{ $employee->user->name ?? 'N/A' }}</h6>
                                                         <small class="text-muted d-block" style="font-size: 0.8rem;">{{ $employee->department ?? 'General' }} &bull; {{ $employee->designation ?? 'Staff' }}</small>
                                                     </div>
                                                 </div>
                                             </td>
                                             <td>
                                                 <span class="badge bg-light text-dark border p-2" style="font-size: 0.8rem; font-weight: 600;">{{ $employee->employee_code }}</span>
                                             </td>
                                             <td class="text-center">
                                                 <div class="d-inline-flex gap-1" role="group" aria-label="Attendance Status Toggle">
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
                                                            class="form-input text-center py-1" 
                                                            placeholder="--:-- --" 
                                                            value="{{ $checkInTime }}" 
                                                            style="width: 100px; font-size: 0.8rem; font-weight: bold; background-color: var(--surface);" />
                                                     <button type="button" 
                                                             onclick="setCurrentTime({{ $employee->id }})" 
                                                             class="btn btn-outline-secondary btn-sm p-1 d-flex align-items-center justify-content-center" 
                                                             title="Set Check-in Time" 
                                                             style="width: 30px; height: 30px; border-radius: 6px;">
                                                         <i class="fas fa-clock text-success"></i>
                                                     </button>
                                                 </div>
                                             </td>
                                             <td class="text-center">
                                                 <div class="d-flex align-items-center justify-content-center gap-1">
                                                     <input type="text" 
                                                            name="attendance[{{ $employee->id }}][check_out_time]" 
                                                            id="timeout_{{ $employee->id }}" 
                                                            class="form-input text-center py-1" 
                                                            placeholder="--:-- --" 
                                                            value="{{ $checkOutTime }}" 
                                                            style="width: 100px; font-size: 0.8rem; font-weight: bold; background-color: var(--surface);" />
                                                     <button type="button" 
                                                             onclick="setCurrentTimeout({{ $employee->id }})" 
                                                             class="btn btn-outline-secondary btn-sm p-1 d-flex align-items-center justify-content-center" 
                                                             title="Set Check-out Time" 
                                                             style="width: 30px; height: 30px; border-radius: 6px;">
                                                         <i class="fas fa-clock text-danger"></i>
                                                     </button>
                                                 </div>
                                             </td>
                                             <td class="text-center">
                                                 <input type="hidden" name="attendance[{{ $employee->id }}][photo]" id="photo_input_{{ $employee->id }}" value="" />
                                                 <img id="photo_preview_{{ $employee->id }}" src="" style="display:none; width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--first-color); margin: 0 auto 5px;" />
                                                 <button type="button" onclick="openCamera({{ $employee->id }})" class="btn btn-outline-first btn-sm p-1" title="Take Photo" style="width: 30px; height: 30px; border-radius: 6px;">
                                                     <i class="fas fa-camera"></i>
                                                 </button>
                                             </td>
                                             <td class="pe-4">
                                                 <input type="text" name="attendance[{{ $employee->id }}][remarks]" class="form-input py-1" placeholder="Add remarks..." value="{{ $remarks }}" style="font-size: 0.8rem; padding: 6px 10px;" />
                                             </td>
                                         </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer p-4 border-top bg-light-orange bg-opacity-10 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="text-muted">
                                <i class="fas fa-info-circle me-1 text-first"></i>
                                <span>Clicking save will log records for <strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong>.</span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('employee-attendances.index') }}" class="button button-secondary px-4">Cancel</a>
                                <button type="submit" class="button button-primary px-5"><i class="fas fa-save me-2"></i>Save Daily Attendance</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- Camera Modal -->
    <div id="cameraModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1050; align-items: center; justify-content: center;">
        <div style="background: #fff; padding: 20px; border-radius: 12px; width: 400px; max-width: 90%; text-align: center;">
            <h5 class="mb-3 fw-bold">Capture Photo</h5>
            <video id="webcam-feed" autoplay playsinline style="width: 100%; border-radius: 8px; background: #000;"></video>
            <canvas id="webcam-canvas" style="display: none;"></canvas>
            <div class="mt-4 d-flex justify-content-between gap-2">
                <button type="button" class="button button-secondary" onclick="closeCamera()">Cancel</button>
                <button type="button" class="button button-primary flex-grow-1" onclick="capturePhoto()"><i class="fas fa-camera me-2"></i>Capture</button>
            </div>
        </div>
    </div>

    <!-- Clock & Camera scripts -->
    <script>
        let currentCaptureId = null;
        let videoStream = null;

        function openCamera(id) {
            currentCaptureId = id;
            const modal = document.getElementById('cameraModal');
            const video = document.getElementById('webcam-feed');
            modal.style.display = 'flex';

            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                .then(stream => {
                    videoStream = stream;
                    video.srcObject = stream;
                })
                .catch(err => {
                    alert('Error accessing camera: ' + err.message);
                    closeCamera();
                });
        }

        function closeCamera() {
            const modal = document.getElementById('cameraModal');
            modal.style.display = 'none';
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
            currentCaptureId = null;
        }

        function capturePhoto() {
            if (!currentCaptureId) return;
            const video = document.getElementById('webcam-feed');
            const canvas = document.getElementById('webcam-canvas');
            const context = canvas.getContext('2d');

            // Set canvas dimensions to a reasonable size (e.g., 320x240) to keep KB size small
            canvas.width = 320;
            canvas.height = 240;
            
            // Draw video frame on canvas
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Compress to JPEG with 0.5 quality (~10-30KB)
            const base64Data = canvas.toDataURL('image/jpeg', 0.5);
            
            // Set data to hidden input and update preview
            document.getElementById('photo_input_' + currentCaptureId).value = base64Data;
            const preview = document.getElementById('photo_preview_' + currentCaptureId);
            preview.src = base64Data;
            preview.style.display = 'block';

            closeCamera();
        }

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
            if(timeInput) timeInput.value = getFormattedTime();
        }

        function setCurrentTimeout(id) {
            const timeoutInput = document.getElementById('timeout_' + id);
            if(timeoutInput) timeoutInput.value = getFormattedTime();
        }

        function handleStatusChange(id, status) {
            const timeInput = document.getElementById('time_' + id);
            const timeoutInput = document.getElementById('timeout_' + id);
            if (status === 'Present' || status === 'Late') {
                if (timeInput && !timeInput.value) {
                    timeInput.value = getFormattedTime();
                }
            } else {
                if(timeInput) timeInput.value = '';
                if(timeoutInput) timeoutInput.value = '';
            }
        }

        function markAll(status) {
            const checks = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
            checks.forEach(radio => {
                radio.checked = true;
                const matches = radio.id.match(/status_(\d+)_/);
                if (matches && matches[1]) {
                    handleStatusChange(matches[1], status);
                }
            });
        }

        // Lazy loading transition script
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });
    </script>
@endsection
