@extends('layouts.app')

@section('title', 'Netcoder Student Dashboard')
@section('page-title', 'Student Console')

@section('content')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    .progress-ring-circle {
        transition: stroke-dashoffset 0.85s ease-in-out;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 24px;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid #E2E8F0;
        transition: all 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .msg-alert {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(99, 102, 241, 0.15));
        border: 1px solid rgba(99,102,241,0.2);
        border-left: 4px solid #6366f1 !important;
    }
    .btn-gradient {
        background: linear-gradient(135deg, var(--first-color, #ff5532), #e04423);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 20px;
        box-shadow: 0 4px 14px rgba(255,85,50,0.25);
        transition: all 0.2s ease;
    }
    .btn-gradient:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(255,85,50,0.35);
        color: #fff;
    }
</style>

<div class="max-w-[1440px] mx-auto p-4 md:p-6 w-full">

    {{-- Unread Messages Notification Banner --}}
    @if(isset($unreadMessageCount) && $unreadMessageCount > 0)
        <div class="msg-alert flex items-center justify-between p-4 rounded-xl mb-6 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-3xl text-indigo-600">mark_email_unread</span>
                <div>
                    <strong class="text-base font-bold text-indigo-950">You have {{ $unreadMessageCount }} unread message(s)!</strong>
                    <p class="text-xs text-indigo-800 mb-0">Please check your message center to stay updated on institute notices.</p>
                </div>
            </div>
            <a href="{{ route('messages.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-full font-button text-xs font-semibold hover:bg-indigo-700 transition-all text-decoration-none shadow">
                View Inbox
            </a>
        </div>
    @endif

    <!-- Hero Header -->
    <section class="mb-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-primary-container/10 text-primary-container font-label-sm text-xs font-bold uppercase tracking-wider">
                        Student Portal
                    </span>
                    <span class="text-xs text-secondary font-label-sm">ID: {{ $student->admission_no }}</span>
                </div>
                <h1 class="font-headline-lg text-2xl md:text-3xl text-on-surface font-black mb-1">
                    Welcome back, {{ $student->first_name }}! 👋
                </h1>
                <p class="font-body-lg text-base text-secondary">
                    You're making great progress in <span class="text-primary-container font-bold">{{ $student->course?->name ?? 'Enrolled Academy Program' }}</span>. Keep it up!
                </p>
            </div>
            <div class="flex gap-3">
                <button type="button" id="openPayNowModalBtn" class="bg-primary-container text-white px-5 py-2.5 rounded-xl font-button text-sm shadow-lg hover:brightness-110 active:scale-95 transition-all flex items-center gap-2 border-0">
                    <span class="material-symbols-outlined text-lg">account_balance_wallet</span>
                    Pay Fee Online
                </button>
            </div>
        </div>
    </section>

    <!-- Bento Grid Layout -->
    <div class="bento-grid">
        <!-- Course Progress & Fee Column (Spans 4) -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <!-- Attendance & Course Progress Card -->
            <div class="glass-card p-6 rounded-xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-title-md text-lg font-bold text-on-surface">Attendance & Progress</h2>
                    <span class="material-symbols-outlined text-primary-container">analytics</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Circular Indicator 1: Attendance Percentage -->
                    <div class="flex flex-col items-center bg-surface-container-low p-4 rounded-xl">
                        <div class="relative w-20 h-20 mb-2">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle class="text-surface-container-highest" cx="50" cy="50" fill="transparent" r="40" stroke="currentColor" stroke-width="8"></circle>
                                @php
                                    $dashOffset = 251.2 - (251.2 * ($attendancePercentage / 100));
                                @endphp
                                <circle class="text-primary-container progress-ring-circle" cx="50" cy="50" fill="transparent" r="40" stroke="currentColor" stroke-dasharray="251.2" stroke-dashoffset="{{ $dashOffset }}" stroke-linecap="round" stroke-width="8"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center font-bold text-lg text-on-surface">{{ $attendancePercentage }}%</div>
                        </div>
                        <p class="font-label-sm text-center text-xs font-semibold text-secondary">Attendance Rate</p>
                    </div>

                    <!-- Circular Indicator 2: Course Completion Estimate -->
                    <div class="flex flex-col items-center bg-surface-container-low p-4 rounded-xl">
                        <div class="relative w-20 h-20 mb-2">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle class="text-surface-container-highest" cx="50" cy="50" fill="transparent" r="40" stroke="currentColor" stroke-width="8"></circle>
                                <circle class="text-info-blue progress-ring-circle" cx="50" cy="50" fill="transparent" r="40" stroke="currentColor" stroke-dasharray="251.2" stroke-dashoffset="75.36" stroke-linecap="round" stroke-width="8"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center font-bold text-lg text-on-surface">70%</div>
                        </div>
                        <p class="font-label-sm text-center text-xs font-semibold text-secondary">Syllabus Covered</p>
                    </div>
                </div>

                <div class="mt-5 space-y-3 pt-3 border-t border-border-subtle">
                    <div class="flex items-center justify-between text-xs font-label-sm">
                        <span class="text-secondary">Present: <strong class="text-success-green">{{ $presentDays }} days</strong></span>
                        <span class="text-secondary">Absent: <strong class="text-error">{{ $absentDays }} days</strong></span>
                    </div>
                    <div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
                        <div class="h-full bg-primary-container" style="width: {{ min(100, $attendancePercentage) }}%;"></div>
                    </div>
                    @if($biometricFine > 0)
                        <div class="p-3 bg-error-container/20 border border-error-container rounded-lg text-xs text-error mt-2">
                            <span class="material-symbols-outlined text-base align-middle me-1">warning</span>
                            <strong>Biometric Fine: ₹{{ number_format($biometricFine, 0) }}</strong>
                            <p class="mb-0 text-[11px] opacity-90 mt-0.5">{{ $fineDetails }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fee Status Widget -->
            <div class="bg-inverse-surface text-white p-6 rounded-xl border border-white/10 relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-primary-container/20 rounded-full blur-2xl"></div>
                <h3 class="font-title-md text-lg font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-container">account_balance_wallet</span>
                    Fee Status Summary
                </h3>
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest font-label-sm mb-1">Outstanding Course Fee</p>
                        <p class="text-3xl font-bold font-headline-lg text-white">₹{{ number_format($remainingCourseFee, 2) }}</p>
                    </div>
                    <button type="button" class="bg-white text-inverse-surface px-4 py-2 rounded-lg font-button text-xs font-bold hover:bg-slate-200 transition-colors border-0 cursor-pointer" onclick="document.getElementById('openPayNowModalBtn').click();">
                        Pay Now
                    </button>
                </div>

                @php
                    $totalBase = max(1, $netCourseFee ?? $totalFees);
                    $paidPercent = min(100, round(($coursePaid / $totalBase) * 100));
                @endphp

                <div class="space-y-3 pt-3 border-t border-slate-700/60 text-xs">
                    <div class="flex justify-between text-slate-300">
                        <span>Paid: <strong>₹{{ number_format($coursePaid, 0) }}</strong></span>
                        <span>Total: <strong>₹{{ number_format($netCourseFee ?? $totalFees, 0) }}</strong></span>
                    </div>
                    <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full bg-success-green" style="width: {{ $paidPercent }}%;"></div>
                    </div>
                    
                    <!-- Overall Total Fees Summary -->
                    <div class="pt-2 border-t border-slate-800 space-y-1 text-slate-400 text-[11px]">
                        <div class="flex justify-between">
                            <span>Admission Fees:</span>
                            <span class="text-slate-300">₹{{ number_format(($student->registration_fee ?? 0) + ($student->prospectus_fee ?? 0), 0) }}</span>
                        </div>
                        @if($biometricFine > 0)
                        <div class="flex justify-between">
                            <span>Fines Charged:</span>
                            <span class="text-error">₹{{ number_format($biometricFine, 0) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-bold text-white pt-1 border-t border-slate-800/50">
                            <span>Overall Total Paid:</span>
                            <span class="text-success-green">₹{{ number_format($paidFees, 0) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-white">
                            <span>Overall Total Due:</span>
                            <span class="text-error">₹{{ number_format($dueFees, 0) }}</span>
                        </div>
                    </div>

                    @if($netMonthlyFee > 0)
                        <div class="flex items-center justify-between text-success-green text-[11px] font-label-sm pt-1">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Monthly Installment: ₹{{ number_format($netMonthlyFee, 0) }}
                            </span>
                            <span>({{ $student->fee_tenure ?? 'per tenure' }})</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Feed (Spans 5) -->
        <div class="col-span-12 lg:col-span-5 space-y-6">
            <!-- Upcoming Assignments & Modules -->
            <div class="bg-white p-6 rounded-xl border border-border-subtle shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-title-md text-lg font-bold text-on-surface">Upcoming Assignments</h2>
                    <span class="text-xs text-primary-container font-button font-bold">3 Active Tasks</span>
                </div>
                <div class="space-y-4">
                    <!-- Assignment 1 -->
                    <div class="flex gap-4 p-4 rounded-lg bg-surface-container-low hover:border-primary-container border border-transparent transition-all group">
                        <div class="flex-shrink-0 w-11 h-11 bg-primary-container/10 text-primary-container rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">description</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-on-surface text-sm mb-0">Hash Table Optimization</h4>
                                <span class="font-label-sm text-[11px] text-primary-container bg-primary-container/10 px-2 py-0.5 rounded font-bold">2 Days Left</span>
                            </div>
                            <p class="text-xs text-secondary line-clamp-1 mb-0">Data Structures & Algorithms - Lab 4</p>
                        </div>
                    </div>

                    <!-- Assignment 2 -->
                    <div class="flex gap-4 p-4 rounded-lg bg-surface-container-low hover:border-primary-container border border-transparent transition-all group">
                        <div class="flex-shrink-0 w-11 h-11 bg-info-blue/10 text-info-blue rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">terminal</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-on-surface text-sm mb-0">React Hooks Refactoring</h4>
                                <span class="font-label-sm text-[11px] text-secondary bg-surface-container-highest px-2 py-0.5 rounded">Upcoming</span>
                            </div>
                            <p class="text-xs text-secondary line-clamp-1 mb-0">Full Stack Web Dev - Project 2</p>
                        </div>
                    </div>

                    <!-- Assignment 3 -->
                    <div class="flex gap-4 p-4 rounded-lg bg-surface-container-low hover:border-primary-container border border-transparent transition-all group">
                        <div class="flex-shrink-0 w-11 h-11 bg-secondary-container text-secondary rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">database</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-on-surface text-sm mb-0">SQL Indexing Strategy</h4>
                                <span class="font-label-sm text-[11px] text-secondary bg-surface-container-highest px-2 py-0.5 rounded">Scheduled</span>
                            </div>
                            <p class="text-xs text-secondary line-clamp-1 mb-0">Database Management - Quiz</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Performance -->
            <div class="bg-white p-6 rounded-xl border border-border-subtle shadow-sm">
                <h2 class="font-title-md text-lg font-bold text-on-surface mb-4">Recent Performance</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low font-label-sm text-secondary uppercase text-[10px]">
                            <tr>
                                <th class="px-4 py-3">Module</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3 text-right">Grade / Score</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-sm divide-y divide-border-subtle">
                            <tr class="hover:bg-surface-slate transition-colors">
                                <td class="px-4 py-3.5 font-bold text-on-surface">Cloud Computing</td>
                                <td class="px-4 py-3.5 text-secondary text-xs">Midterm Evaluation</td>
                                <td class="px-4 py-3.5 text-right text-success-green font-bold">92%</td>
                            </tr>
                            <tr class="hover:bg-surface-slate transition-colors">
                                <td class="px-4 py-3.5 font-bold text-on-surface">API Security</td>
                                <td class="px-4 py-3.5 text-secondary text-xs">Lab 3 Practical</td>
                                <td class="px-4 py-3.5 text-right text-success-green font-bold">88%</td>
                            </tr>
                            <tr class="hover:bg-surface-slate transition-colors">
                                <td class="px-4 py-3.5 font-bold text-on-surface">Binary Trees</td>
                                <td class="px-4 py-3.5 text-secondary text-xs">Quiz 2</td>
                                <td class="px-4 py-3.5 text-right text-primary-container font-bold">74%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Access & Timeline Sidebar (Spans 3) -->
        <div class="col-span-12 lg:col-span-3 space-y-6">
            <!-- Course Materials -->
            <div class="bg-white p-6 rounded-xl border border-border-subtle shadow-sm">
                <h2 class="font-title-md text-lg font-bold text-on-surface mb-4">Course Materials</h2>
                <div class="grid grid-cols-1 gap-2.5">
                    @if($student->course && $student->course->syllabus_path)
                        <a href="{{ Storage::url($student->course->syllabus_path) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg border border-border-subtle hover:bg-primary-container hover:text-white group transition-all text-decoration-none text-on-surface">
                            <span class="material-symbols-outlined text-primary-container group-hover:text-white">menu_book</span>
                            <span class="font-button text-xs font-semibold">Curriculum Syllabus</span>
                        </a>
                    @else
                        <a class="flex items-center gap-3 p-3 rounded-lg border border-border-subtle hover:bg-primary-container hover:text-white group transition-all text-decoration-none text-on-surface" href="#">
                            <span class="material-symbols-outlined text-primary-container group-hover:text-white">menu_book</span>
                            <span class="font-button text-xs font-semibold">Curriculum Syllabus</span>
                        </a>
                    @endif
                    <a class="flex items-center gap-3 p-3 rounded-lg border border-border-subtle hover:bg-primary-container hover:text-white group transition-all text-decoration-none text-on-surface" href="#">
                        <span class="material-symbols-outlined text-info-blue group-hover:text-white">videocam</span>
                        <span class="font-button text-xs font-semibold">Recorded Lectures</span>
                    </a>
                    <a class="flex items-center gap-3 p-3 rounded-lg border border-border-subtle hover:bg-primary-container hover:text-white group transition-all text-decoration-none text-on-surface" href="#">
                        <span class="material-symbols-outlined text-success-green group-hover:text-white">folder_open</span>
                        <span class="font-button text-xs font-semibold">Resource Library</span>
                    </a>
                    <a class="flex items-center gap-3 p-3 rounded-lg border border-border-subtle hover:bg-primary-container hover:text-white group transition-all text-decoration-none text-on-surface" href="{{ route('messages.index') }}">
                        <span class="material-symbols-outlined text-tertiary group-hover:text-white">chat</span>
                        <span class="font-button text-xs font-semibold">Message & Forum</span>
                    </a>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="bg-surface-container-low p-6 rounded-xl border border-border-subtle">
                <h2 class="font-title-md text-lg font-bold text-on-surface mb-4">Today's Schedule</h2>
                <div class="space-y-5 relative before:absolute before:left-2 before:top-2 before:bottom-2 before:w-[2px] before:bg-border-subtle">
                    <div class="relative pl-7">
                        <div class="absolute left-0 top-1 w-3.5 h-3.5 bg-primary-container rounded-full ring-4 ring-white"></div>
                        <p class="font-label-sm text-xs text-secondary mb-0.5">09:00 - 11:30</p>
                        <p class="font-bold text-on-surface text-sm mb-0">Data Science Seminar</p>
                        <p class="text-[11px] text-secondary mb-0">Hall 4 / Zoom Room B</p>
                    </div>
                    <div class="relative pl-7">
                        <div class="absolute left-0 top-1 w-3.5 h-3.5 bg-info-blue rounded-full ring-4 ring-white"></div>
                        <p class="font-label-sm text-xs text-secondary mb-0.5">13:00 - 14:30</p>
                        <p class="font-bold text-on-surface text-sm mb-0">Mentor Session</p>
                        <p class="text-[11px] text-secondary mb-0">Office Hour with Instructor</p>
                    </div>
                    <div class="relative pl-7">
                        <div class="absolute left-0 top-1 w-3.5 h-3.5 bg-success-green rounded-full ring-4 ring-white"></div>
                        <p class="font-label-sm text-xs text-secondary mb-0.5">15:00 - 17:00</p>
                        <p class="font-bold text-on-surface text-sm mb-0">Group Lab Practice</p>
                        <p class="text-[11px] text-secondary mb-0">Library Pod #3</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Invoices Section -->
    <div class="w-full mt-8">
        <div class="bg-white p-6 rounded-xl border border-border-subtle shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h5 class="font-title-md text-lg font-bold text-on-surface">Fee Invoices & Payment Receipts</h5>
                    <p class="text-xs text-secondary mb-0">View all generated invoices and download payment records.</p>
                </div>
                <button type="button" class="btn-gradient" onclick="document.getElementById('openPayNowModalBtn').click();">
                    <span class="material-symbols-outlined text-base align-middle me-1">qr_code_2</span>
                    Pay Fee Online
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-slate border-b border-border-subtle font-label-sm text-xs text-secondary uppercase">
                            <th class="px-4 py-3">Invoice No.</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Amount</th>
                            <th class="px-4 py-3">Paid</th>
                            <th class="px-4 py-3">Due</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle text-sm">
                        @forelse($feeInvoices as $inv)
                            <tr class="hover:bg-surface-slate transition-colors">
                                <td class="px-4 py-3.5 font-bold text-primary-container">#{{ $inv->invoice_no }}</td>
                                <td class="px-4 py-3.5 text-xs text-secondary">{{ $inv->fee_category }}</td>
                                <td class="px-4 py-3.5 font-semibold text-on-surface">₹{{ number_format($inv->total_amount, 0) }}</td>
                                <td class="px-4 py-3.5 font-semibold text-success-green">₹{{ number_format($inv->paid_amount, 0) }}</td>
                                <td class="px-4 py-3.5 font-semibold {{ $inv->due_amount > 0 ? 'text-error' : 'text-secondary' }}">₹{{ number_format($inv->due_amount, 0) }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-{{ $inv->status === 'Paid' ? 'success-green/10 text-success-green' : ($inv->status === 'Partial' ? 'info-blue/10 text-info-blue' : 'error-container/20 text-error') }}">
                                        {{ $inv->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('fee_invoices.show', $inv) }}" class="px-3 py-1 bg-surface-slate border border-border-subtle rounded-lg text-xs font-button hover:bg-surface-container-high text-on-surface text-decoration-none inline-block" target="_blank">
                                        <span class="material-symbols-outlined text-sm align-middle me-1">print</span> Print
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-secondary">No fee invoices recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Attendance History Section -->
    <div class="w-full mt-8">
        <div class="bg-white p-6 rounded-xl border border-border-subtle shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h5 class="font-title-md text-lg font-bold text-on-surface">Attendance History</h5>
                    <p class="text-xs text-secondary mb-0">View your daily check-in records for the past 30 days.</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-success-green/10 text-success-green">Present: {{ $presentDays }}</span>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-error-container/20 text-error">Absent: {{ $absentDays }}</span>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-info-blue/10 text-info-blue">Late: {{ $lateDays }}</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-slate border-b border-border-subtle font-label-sm text-xs text-secondary uppercase">
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Check-in Time</th>
                            <th class="px-4 py-3">Check-out Time</th>
                            <th class="px-4 py-3">Fine Charged</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle text-sm">
                        @forelse($attendances as $att)
                            <tr class="hover:bg-surface-slate transition-colors">
                                <td class="px-4 py-3.5 font-semibold text-on-surface">{{ \Carbon\Carbon::parse($att->attendance_date)->format('d M, Y') }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-{{ $att->status === 'Present' ? 'success-green/10 text-success-green' : ($att->status === 'Late' ? 'info-blue/10 text-info-blue' : 'error-container/20 text-error') }}">
                                        {{ $att->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-secondary">{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '--' }}</td>
                                <td class="px-4 py-3.5 text-secondary">{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '--' }}</td>
                                <td class="px-4 py-3.5 font-semibold {{ $att->fine > 0 ? 'text-error' : 'text-secondary' }}">
                                    {{ $att->fine > 0 ? '₹'.number_format($att->fine, 0) : 'None' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-secondary">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Embedded Message Suite Widget -->
    <div class="w-full mt-6">
        @include('messages.widget')
    </div>
</div>

<!-- UPI Payment Modal -->
<div class="modal fade" id="payNowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-xl rounded-2xl overflow-hidden">
            <div class="modal-header text-white p-4" style="background: linear-gradient(135deg, #ff5532 0%, #e04423 100%);">
                <h6 class="modal-title font-bold text-base flex items-center gap-2 mb-0 text-white">
                    <span class="material-symbols-outlined">qr_code_2</span>
                    Scan & Pay Fee via UPI
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6 text-center">
                <div id="payModalLoading" class="py-6">
                    <div class="spinner-border text-danger" role="status"></div>
                    <p class="text-secondary text-xs mt-3">Generating Monthly Invoice & UPI QR Code...</p>
                </div>

                <div id="payModalContent" style="display: none;">
                    <div class="inline-block px-3 py-1 bg-surface-slate border border-border-subtle rounded-full text-xs font-bold mb-3">
                        Invoice No: <span id="modalInvoiceNo" class="text-primary-container font-mono"></span>
                    </div>
                    
                    <h3 class="text-3xl font-black text-primary-container mb-1">₹<span id="modalAmount"></span></h3>
                    <p class="text-xs text-secondary mb-4" id="modalCategory"></p>

                    <!-- QR Code Box -->
                    <div class="p-4 bg-white border border-border-subtle rounded-2xl inline-block shadow-md mb-4">
                        <img id="modalQrCodeImg" src="" alt="UPI QR Code" class="w-48 h-48 rounded-xl mx-auto block" />
                        <div class="mt-3 flex justify-center gap-1.5 flex-wrap">
                            <span class="px-2 py-0.5 bg-blue-600 text-white text-[10px] rounded font-bold">GPay</span>
                            <span class="px-2 py-0.5 bg-purple-700 text-white text-[10px] rounded font-bold">PhonePe</span>
                            <span class="px-2 py-0.5 bg-sky-500 text-white text-[10px] rounded font-bold">Paytm</span>
                            <span class="px-2 py-0.5 bg-emerald-600 text-white text-[10px] rounded font-bold">BHIM UPI</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-border-subtle bg-surface-slate text-left mb-4 text-xs space-y-1">
                        <div class="flex justify-between items-center">
                            <span class="text-secondary font-bold">Official UPI VPA ID:</span>
                            <strong id="modalUpiId" class="font-mono text-primary-container">netcoder@upi</strong>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-secondary font-bold">Student Name:</span>
                            <strong id="modalStudentName" class="text-on-surface"></strong>
                        </div>
                    </div>

                    <!-- Payment Confirmation Section -->
                    <div class="border-t border-border-subtle pt-4 text-left">
                        <label class="block font-bold text-xs uppercase text-secondary mb-2">Already Paid? Enter UPI Txn Ref ID</label>
                        <input type="hidden" id="modalInvoiceIdHidden" value="" />
                        <div class="flex gap-2">
                            <input type="text" id="modalTxnIdInput" class="form-control rounded-lg text-sm" placeholder="e.g. 340918239012" />
                            <button class="px-4 py-2 bg-success-green text-white rounded-lg font-button text-xs font-bold hover:bg-emerald-600 transition-all border-0 flex-shrink-0" id="confirmTxnBtn" type="button">
                                Submit Txn ID
                            </button>
                        </div>
                        <div id="confirmFeedback" style="display:none;" class="p-2.5 bg-success-green/10 text-success-green rounded-lg text-xs font-bold mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payBtn = document.getElementById('openPayNowModalBtn');
        const modalEl = document.getElementById('payNowModal');
        const loadingEl = document.getElementById('payModalLoading');
        const contentEl = document.getElementById('payModalContent');
        const confirmBtn = document.getElementById('confirmTxnBtn');
        const feedbackEl = document.getElementById('confirmFeedback');

        if (!payBtn || !modalEl) return;

        const payModal = new bootstrap.Modal(modalEl);

        payBtn.addEventListener('click', function() {
            loadingEl.style.display = 'block';
            contentEl.style.display = 'none';
            feedbackEl.style.display = 'none';
            payModal.show();

            fetch("{{ route('student.pay-now') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                loadingEl.style.display = 'none';
                if (data.success) {
                    document.getElementById('modalInvoiceNo').innerText = data.invoice_no;
                    document.getElementById('modalAmount').innerText = data.amount;
                    document.getElementById('modalCategory').innerText = data.fee_category;
                    document.getElementById('modalUpiId').innerText = data.upi_id;
                    document.getElementById('modalStudentName').innerText = data.student_name;
                    document.getElementById('modalQrCodeImg').src = data.qr_image_url;
                    document.getElementById('modalInvoiceIdHidden').value = data.invoice_id;
                    contentEl.style.display = 'block';
                } else {
                    alert(data.error || 'Failed to generate invoice.');
                    payModal.hide();
                }
            })
            .catch(err => {
                console.error(err);
                loadingEl.style.display = 'none';
                alert('Connection error while loading payment details.');
            });
        });

        confirmBtn.addEventListener('click', function() {
            const invoiceId = document.getElementById('modalInvoiceIdHidden').value;
            const txnId = document.getElementById('modalTxnIdInput').value.trim();

            if (!txnId) {
                alert('Please enter your UPI transaction reference ID.');
                return;
            }

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">sync</span> Submitting...';

            fetch("{{ route('student.confirm-payment') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    invoice_id: invoiceId,
                    txn_id: txnId
                })
            })
            .then(res => res.json())
            .then(data => {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Submit Txn ID';

                if (data.success) {
                    feedbackEl.innerText = data.message;
                    feedbackEl.style.display = 'block';
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    alert(data.message || 'Error confirming transaction.');
                }
            })
            .catch(err => {
                console.error(err);
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Submit Txn ID';
                alert('Server error confirming transaction.');
            });
        });
    });
</script>
@endsection
