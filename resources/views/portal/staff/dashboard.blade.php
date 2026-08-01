@extends('layouts.app')

@section('title', 'Faculty Dashboard - Netcoder')
@section('page-title', 'Instructor Console')

@section('content')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #E2E8F0;
        border-radius: 10px;
    }
    .bento-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 0.75rem;
        transition: all 150ms ease-in-out;
    }
    .bento-card:hover {
        box-shadow: 0px 4px 20px rgba(0,0,0,0.05);
    }
    
    /* Premium Nav Pills styling */
    #staffTab .nav-link {
        color: #5f5e5c !important;
        border-radius: 0.5rem;
        background: transparent !important;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        transform: none !important;
        margin-bottom: 0;
    }
    #staffTab .nav-link:hover {
        background-color: #f4f3f3 !important;
        color: #1a1c1c !important;
    }
    #staffTab .nav-link.active {
        background-color: var(--first-color, #b02e00) !important;
        color: #ffffff !important;
        border-left: none !important;
    }
</style>

<div class="max-w-[1440px] mx-auto p-4 md:p-6 w-full">

    {{-- Unread Messages Alert Banner --}}
    @if(isset($unreadMessageCount) && $unreadMessageCount > 0)
        <div class="flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 border-l-4 border-l-emerald-500 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-3xl text-emerald-600">mark_email_unread</span>
                <div>
                    <strong class="text-base font-bold text-emerald-950">You have {{ $unreadMessageCount }} unread message(s)!</strong>
                    <p class="text-xs text-emerald-800 mb-0">Check your inbox to read faculty updates and announcements.</p>
                </div>
            </div>
            <a href="{{ route('messages.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-full font-button text-xs font-semibold hover:bg-emerald-700 transition-all text-decoration-none shadow">
                View Inbox
            </a>
        </div>
    @endif

    <!-- Header & Greeting Row -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-primary-container/10 text-primary-container font-label-sm text-xs font-bold uppercase tracking-wider">
                    Staff & Faculty Portal
                </span>
                <span class="text-xs text-secondary font-label-sm">Code: {{ $employee->employee_code }}</span>
            </div>
            <h2 class="font-headline-lg text-2xl md:text-3xl font-black text-on-surface mb-0.5">
                Instructor Dashboard
            </h2>
            <p class="text-secondary font-body-md text-sm md:text-base mb-0">
                <span id="dashboard-greeting-prefix">Good day</span>, <strong>{{ $employee->user?->name ?? 'Faculty Instructor' }}</strong> ({{ $employee->designation ?? 'Senior Instructor' }}). Here's your daily schedule & academic summary.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('staff.attendance.capture') }}" class="flex items-center gap-2 px-4 py-2.5 bg-primary-container text-white rounded-lg hover:brightness-110 transition-all font-button text-xs font-bold text-decoration-none shadow-md shadow-primary/20">
                <span class="material-symbols-outlined text-lg">photo_camera</span>
                Mark Attendance
            </a>
            <a href="{{ route('messages.index') }}" class="flex items-center gap-2 px-4 py-2.5 border border-border-subtle bg-white text-on-surface rounded-lg hover:bg-surface-slate transition-all font-button text-xs font-bold text-decoration-none shadow-sm">
                <span class="material-symbols-outlined text-lg">campaign</span>
                Post Announcement
            </a>
        </div>
    </div>

    <!-- Bento Grid Dashboard -->
    <div class="grid grid-cols-12 gap-6 mb-8">
        
        <!-- Quick Stats Row (Spans 12) -->
        <div class="col-span-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat 1: Attendance Rate -->
            <div class="bento-card p-6 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="material-symbols-outlined text-primary-container p-2 bg-primary-container/10 rounded-lg text-xl">person_check</span>
                        <span class="text-success-green font-label-sm text-xs font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">trending_up</span> {{ $presentDays }} Days Present
                        </span>
                    </div>
                    <h3 class="text-secondary font-label-sm text-xs uppercase tracking-wider mb-1">Monthly Attendance Rate</h3>
                    <p class="font-display-lg text-3xl font-black text-on-surface mb-0">{{ $attendancePercentage }}%</p>
                </div>
                <div class="w-full bg-surface-container h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-primary-container h-full" style="width: {{ min(100, $attendancePercentage) }}%;"></div>
                </div>
            </div>

            <!-- Stat 2: Pending Tasks & Grading -->
            <div class="bento-card p-6 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="material-symbols-outlined text-info-blue p-2 bg-info-blue/10 rounded-lg text-xl">assignment_turned_in</span>
                        <span class="text-secondary font-label-sm text-xs">Assigned Tasks</span>
                    </div>
                    <h3 class="text-secondary font-label-sm text-xs uppercase tracking-wider mb-1">Pending Tasks & Review</h3>
                    <p class="font-display-lg text-3xl font-black text-on-surface mb-0">
                        {{ $assignedTasks->whereIn('status', ['Pending', 'In Progress'])->count() }} 
                        <span class="text-sm font-normal text-secondary">action items</span>
                    </p>
                </div>
                <div class="flex gap-2 mt-4 flex-wrap">
                    <span class="px-2 py-0.5 bg-surface-container-high text-secondary text-[10px] font-bold rounded">Python Dev</span>
                    <span class="px-2 py-0.5 bg-surface-container-high text-secondary text-[10px] font-bold rounded">Cloud Arch</span>
                    <span class="px-2 py-0.5 bg-surface-container-high text-secondary text-[10px] font-bold rounded">React Lab</span>
                </div>
            </div>

            <!-- Stat 3: Salary & Work Schedule -->
            <div class="bento-card p-6 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="material-symbols-outlined text-success-green p-2 bg-success-green/10 rounded-lg text-xl">payments</span>
                        <span class="text-success-green font-label-sm text-xs font-bold">Estimated Net</span>
                    </div>
                    <h3 class="text-secondary font-label-sm text-xs uppercase tracking-wider mb-1">Monthly Payable Salary</h3>
                    <p class="font-display-lg text-3xl font-black text-on-surface mb-0">₹{{ number_format($netMonthlySalary, 0) }}</p>
                </div>
                <p class="text-secondary font-label-sm text-xs mt-3 mb-0">
                    Basic: ₹{{ number_format($monthlySalary, 0) }} | Effective Late Days: <strong>{{ $effectiveLateDays }}</strong>
                </p>
            </div>
        </div>

        <!-- Main Column: Schedule & Attendance (Spans 8) -->
        <div class="col-span-12 lg:col-span-8 space-y-6">
            <!-- Today's Schedule -->
            <section class="bento-card p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-title-md text-lg font-bold text-on-surface">Today's Academic Schedule</h3>
                    <span class="font-label-sm text-xs text-secondary bg-surface-slate px-2.5 py-1 rounded border border-border-subtle">
                        {{ now()->format('l, M d, Y') }}
                    </span>
                </div>
                <div class="space-y-4">
                    <!-- Schedule Item 1 -->
                    <div class="flex items-center gap-4 md:gap-6 p-4 border border-border-subtle rounded-xl hover:border-primary-container/30 transition-colors group">
                        <div class="w-16 text-center flex-shrink-0">
                            <p class="font-bold text-on-surface text-base mb-0">09:00</p>
                            <p class="text-[11px] font-label-sm text-secondary mb-0">AM</p>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-primary-container/10 text-primary-container text-[10px] font-bold rounded uppercase">Lecture</span>
                                <span class="text-xs text-secondary font-label-sm">Room 402</span>
                            </div>
                            <h4 class="font-button text-sm font-bold group-hover:text-primary-container transition-colors mb-0">Advanced Python Architectures</h4>
                            <p class="text-xs text-secondary mb-0">CS204 - Master Class Batch</p>
                        </div>
                        <a href="{{ route('staff.attendance.capture') }}" class="px-3.5 py-1.5 border border-border-subtle rounded-lg font-button text-xs hover:bg-surface-container-low text-on-surface text-decoration-none">
                            Take Attendance
                        </a>
                    </div>

                    <!-- Schedule Item 2 (Happening Now) -->
                    <div class="flex items-center gap-4 md:gap-6 p-4 border-l-4 border-l-primary-container bg-primary-container/5 rounded-r-xl">
                        <div class="w-16 text-center flex-shrink-0">
                            <p class="font-bold text-primary-container text-base mb-0">11:30</p>
                            <p class="text-[11px] font-label-sm text-secondary mb-0">AM</p>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-success-green/10 text-success-green text-[10px] font-bold rounded uppercase">Lab Session</span>
                                <span class="text-xs text-secondary font-label-sm">Lab C</span>
                            </div>
                            <h4 class="font-button text-sm font-bold text-on-surface mb-0">Full-stack React Deployment</h4>
                            <p class="text-xs text-secondary mb-0">WEB102 - Practical Hands-on</p>
                        </div>
                        <div class="flex items-center gap-2 text-primary-container font-bold text-xs">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-container opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-primary-container"></span>
                            </span>
                            <span class="font-label-sm">Happening Now</span>
                        </div>
                    </div>

                    <!-- Schedule Item 3 -->
                    <div class="flex items-center gap-4 md:gap-6 p-4 border border-border-subtle rounded-xl hover:border-primary-container/30 transition-colors group">
                        <div class="w-16 text-center flex-shrink-0">
                            <p class="font-bold text-on-surface text-base mb-0">02:30</p>
                            <p class="text-[11px] font-label-sm text-secondary mb-0">PM</p>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 bg-info-blue/10 text-info-blue text-[10px] font-bold rounded uppercase">Workshop</span>
                                <span class="text-xs text-secondary font-label-sm">Online Seminar</span>
                            </div>
                            <h4 class="font-button text-sm font-bold group-hover:text-primary-container transition-colors mb-0">AWS Solutions Architect Deep Dive</h4>
                            <p class="text-xs text-secondary mb-0">CLOUD301 - Interactive Session</p>
                        </div>
                        <a href="{{ route('meetings.index') }}" class="px-3.5 py-1.5 border border-border-subtle rounded-lg font-button text-xs hover:bg-surface-container-low text-on-surface text-decoration-none">
                            Join Session
                        </a>
                    </div>
                </div>
            </section>

            <!-- Student Attendance Quick-Stats Table -->
            <section class="bento-card p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-title-md text-lg font-bold text-on-surface">Staff Monthly Attendance Logs</h3>
                    <a href="{{ route('staff.attendance.capture') }}" class="text-primary-container font-button text-xs font-bold hover:underline flex items-center gap-1 text-decoration-none">
                        Self Log Attendance <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low font-label-sm text-xs text-secondary uppercase">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Check In</th>
                                <th class="px-4 py-3">Check Out</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Remark</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle text-sm">
                            @forelse($monthAttendances->take(5) as $att)
                                @php
                                    $isLateCheckIn = false;
                                    if ($att->check_in_time) {
                                        try {
                                            $ci = \Carbon\Carbon::parse($att->attendance_date . ' ' . $att->check_in_time);
                                            $co = \Carbon\Carbon::parse($att->attendance_date . ' 10:00:00');
                                            $isLateCheckIn = $ci->gt($co);
                                        } catch (\Exception $e) {}
                                    }
                                @endphp
                                <tr class="hover:bg-surface-slate transition-colors">
                                    <td class="px-4 py-3.5 font-semibold text-on-surface">
                                        {{ \Carbon\Carbon::parse($att->attendance_date)->format('D, M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-mono">
                                        {{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('h:i A') : '--' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-mono">
                                        {{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('h:i A') : '--' }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-{{ $att->status === 'Present' ? 'success-green/10 text-success-green' : ($att->status === 'Absent' ? 'error-container/20 text-error' : 'info-blue/10 text-info-blue') }}">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right text-xs">
                                        @if($isLateCheckIn)
                                            <span class="text-amber-600 font-bold flex items-center justify-end gap-1">
                                                <span class="material-symbols-outlined text-sm">warning</span> Late Check-in
                                            </span>
                                        @else
                                            <span class="text-secondary">On Time</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-secondary">No attendance logs found for this month.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Salary & HR Documents Tabs -->
            <section class="bento-card p-6 shadow-sm">
                <h3 class="font-title-md text-lg font-bold text-on-surface mb-4">Faculty Financial & HR Records</h3>
                <ul class="nav nav-pills gap-2 border-b border-border-subtle pb-3 mb-4" id="staffTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active font-button text-xs font-bold px-4 py-2" data-bs-toggle="tab" data-bs-target="#salary-panel" type="button">
                            Salary Slips
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link font-button text-xs font-bold px-4 py-2" data-bs-toggle="tab" data-bs-target="#offer-panel" type="button">
                            Offer Letters
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link font-button text-xs font-bold px-4 py-2" data-bs-toggle="tab" data-bs-target="#leave-panel" type="button">
                            Leave Requests
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="salary-panel">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-slate border-b border-border-subtle text-xs text-secondary uppercase font-label-sm">
                                        <th class="px-4 py-2.5">Month/Year</th>
                                        <th class="px-4 py-2.5">Basic</th>
                                        <th class="px-4 py-2.5">Deductions</th>
                                        <th class="px-4 py-2.5">Net Pay</th>
                                        <th class="px-4 py-2.5">Status</th>
                                        <th class="px-4 py-2.5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border-subtle text-sm">
                                    @forelse($salarySlips ?? [] as $slip)
                                        <tr class="hover:bg-surface-slate transition-colors">
                                            <td class="px-4 py-3 font-semibold">{{ $slip->month }} {{ $slip->year }}</td>
                                            <td class="px-4 py-3 text-xs">₹{{ number_format($slip->basic_salary, 0) }}</td>
                                            <td class="px-4 py-3 text-xs text-error">-₹{{ number_format($slip->deductions, 0) }}</td>
                                            <td class="px-4 py-3 font-bold text-primary-container">₹{{ number_format($slip->net_pay, 0) }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-{{ $slip->status === 'Paid' ? 'success-green/10 text-success-green' : 'amber-500/10 text-amber-600' }}">
                                                    {{ $slip->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                @if($slip->status === 'Paid')
                                                    <a href="{{ route('salary_slips.show', $slip) }}" target="_blank" class="px-2.5 py-1 bg-surface-slate border border-border-subtle rounded text-xs hover:bg-surface-container-high text-decoration-none inline-block">
                                                        <span class="material-symbols-outlined text-sm align-middle">print</span> Print Slip
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-4 text-secondary text-xs">No salary slips generated yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="offer-panel">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-slate border-b border-border-subtle text-xs text-secondary uppercase font-label-sm">
                                        <th class="px-4 py-2.5">Offer No</th>
                                        <th class="px-4 py-2.5">Designation</th>
                                        <th class="px-4 py-2.5">Salary</th>
                                        <th class="px-4 py-2.5">Joining Date</th>
                                        <th class="px-4 py-2.5 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border-subtle text-sm">
                                    @forelse($offerLetters ?? [] as $letter)
                                        <tr class="hover:bg-surface-slate transition-colors">
                                            <td class="px-4 py-3 font-semibold">{{ $letter->offer_letter_no }}</td>
                                            <td class="px-4 py-3 text-xs">{{ $letter->designation }}</td>
                                            <td class="px-4 py-3 text-xs text-success-green font-bold">₹{{ number_format($letter->offered_salary, 0) }}</td>
                                            <td class="px-4 py-3 text-xs">{{ \Carbon\Carbon::parse($letter->joining_date)->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-{{ $letter->status === 'Accepted' ? 'success-green/10 text-success-green' : 'amber-500/10 text-amber-600' }}">
                                                    {{ $letter->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4 text-secondary text-xs">No offer letters available.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="leave-panel">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-slate border-b border-border-subtle text-xs text-secondary uppercase font-label-sm">
                                        <th class="px-4 py-2.5">Type</th>
                                        <th class="px-4 py-2.5">From</th>
                                        <th class="px-4 py-2.5">To</th>
                                        <th class="px-4 py-2.5">Days</th>
                                        <th class="px-4 py-2.5 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border-subtle text-sm">
                                    @forelse($leaveApplications ?? [] as $leave)
                                        <tr class="hover:bg-surface-slate transition-colors">
                                            <td class="px-4 py-3 font-semibold">{{ $leave->leave_type }}</td>
                                            <td class="px-4 py-3 text-xs">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }}</td>
                                            <td class="px-4 py-3 text-xs">{{ \Carbon\Carbon::parse($leave->end_date)->format('M d') }}</td>
                                            <td class="px-4 py-3 text-xs">{{ $leave->total_days ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-{{ $leave->status === 'Approved' ? 'success-green/10 text-success-green' : 'amber-500/10 text-amber-600' }}">
                                                    {{ $leave->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4 text-secondary text-xs">No leave applications recorded.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Side Column: Tasks, Announcements & Department Banner (Spans 4) -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <!-- Pending Tasks -->
            <section class="bento-card p-6 shadow-sm">
                <h3 class="font-title-md text-lg font-bold text-on-surface mb-4">Pending Tasks</h3>
                <div class="space-y-3">
                    @forelse($assignedTasks->take(4) as $task)
                        <div class="flex gap-3 p-3 bg-surface-slate border border-border-subtle rounded-lg">
                            <span class="material-symbols-outlined text-primary-container mt-0.5">priority_high</span>
                            <div>
                                <p class="font-button text-xs font-bold text-on-surface leading-tight mb-1">{{ $task->title }}</p>
                                <p class="text-[11px] text-secondary mb-1">{{ Str::limit($task->description ?? 'No details provided', 60) }}</p>
                                <span class="px-2 py-0.5 bg-amber-500/10 text-amber-700 text-[10px] font-bold rounded">
                                    Status: {{ $task->status ?? 'Pending' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex gap-3 p-3 bg-error-container/10 border border-error-container/20 rounded-lg">
                            <span class="material-symbols-outlined text-error mt-0.5">priority_high</span>
                            <div>
                                <p class="font-button text-xs font-bold text-error leading-tight mb-1">Grade Midterms: Cloud Arch</p>
                                <p class="text-[11px] text-secondary mb-1">12 submissions overdue by 1 day</p>
                                <a href="#" class="text-error font-bold text-[11px] underline">Start Grading Now</a>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 bg-surface-slate border border-border-subtle rounded-lg">
                            <span class="material-symbols-outlined text-info-blue mt-0.5">description</span>
                            <div>
                                <p class="font-button text-xs font-bold text-on-surface leading-tight mb-1">Update Syllabus: Q4 WEB102</p>
                                <p class="text-[11px] text-secondary mb-0">Content review required by Friday</p>
                            </div>
                        </div>
                        <div class="flex gap-3 p-3 bg-surface-slate border border-border-subtle rounded-lg">
                            <span class="material-symbols-outlined text-success-green mt-0.5">check_circle</span>
                            <div>
                                <p class="font-button text-xs font-bold text-on-surface leading-tight mb-1">Exam Prep Finalized</p>
                                <p class="text-[11px] text-secondary mb-0">CS204 materials uploaded to LMS</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Faculty Updates & Department Announcements -->
            <section class="bento-card overflow-hidden shadow-sm">
                <div class="p-4 border-b border-border-subtle flex justify-between items-center bg-surface-slate">
                    <h3 class="font-title-md text-base font-bold text-on-surface mb-0">Faculty Updates</h3>
                    <span class="px-2 py-0.5 bg-primary-container text-white text-[10px] font-bold rounded">3 NEW</span>
                </div>
                <div class="p-5 space-y-4 text-xs">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-primary-container"></span>
                            <span class="font-label-sm text-secondary text-[11px]">2 Hours Ago</span>
                        </div>
                        <h4 class="font-button font-bold text-on-surface text-xs mb-1">New Policy: Generative AI in Code Submission</h4>
                        <p class="text-secondary text-xs line-clamp-2 mb-1">The department has finalized guidelines for students using LLMs during programming assignments...</p>
                        <a class="text-primary-container font-bold text-[11px] hover:underline block" href="{{ route('messages.index') }}">Read Full Update</a>
                    </div>
                    <div class="border-t border-border-subtle pt-3">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-label-sm text-secondary text-[11px]">Yesterday</span>
                        </div>
                        <h4 class="font-button font-bold text-on-surface text-xs mb-1">Faculty Meeting: Quarterly Review</h4>
                        <p class="text-secondary text-xs line-clamp-2 mb-1">All staff members are required to attend the Q4 review meeting in Conference Room A or via Zoom...</p>
                        <a class="text-primary-container font-bold text-[11px] hover:underline block" href="{{ route('meetings.index') }}">Add to Calendar</a>
                    </div>
                </div>
                <a href="{{ route('messages.index') }}" class="block w-full py-3 text-center bg-surface-slate text-secondary font-button text-xs font-bold hover:bg-surface-container-high transition-colors text-decoration-none border-t border-border-subtle">
                    View All Announcements
                </a>
            </section>

            <!-- Department Banner -->
            <div class="rounded-xl overflow-hidden relative h-44 group cursor-pointer shadow-md bg-slate-900" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div class="absolute inset-0 bg-cover bg-center opacity-30 group-hover:scale-105 transition-transform duration-500" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDCgCfj22r5mMY-LZ8hyeM-RSVADZpGC3hj5Atr4_cMuBaDf_qROB7jYEpKHnel3CJQol-nL5tF5iI8tUWp02JbajoiG9UvUaNLMEyAH98IjAaH-twz6XQ-k0acbh4cAF1wWbj5kZ88SqXPXBl8DX0uEVzyNo9bwa9WxUmPBWiU_fEFRhk7uOjD0B0FTGrXLyiB2xkmai6ZFgUEW7xfqhmu9B1COizNeh5vVY0MOCEJ5w2vwPQGsLDljA')"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-5 z-10">
                    <span class="px-2.5 py-0.5 bg-primary-container text-white text-[10px] font-bold rounded-full w-fit mb-2">CS & IT Dept</span>
                    <h4 class="text-white font-bold text-base mb-1">Computer Science Department</h4>
                    <p class="text-slate-300 text-xs mb-0">Annual Research Grant & Course Syllabus Review applications are open.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Embedded Message Suite Widget -->
    <div class="w-full mt-6">
        @include('messages.widget')
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const greetingPrefix = document.getElementById('dashboard-greeting-prefix');
        if (greetingPrefix) {
            const hour = new Date().getHours();
            if (hour >= 5 && hour < 12) {
                greetingPrefix.textContent = "Good morning";
            } else if (hour >= 12 && hour < 17) {
                greetingPrefix.textContent = "Good afternoon";
            } else if (hour >= 17 && hour < 22) {
                greetingPrefix.textContent = "Good evening";
            } else {
                greetingPrefix.textContent = "Welcome";
            }
        }
    });
</script>
@endsection
