@extends('layouts.app')

@section('title', 'Executive Dashboard')
@section('page-title', 'Executive Overview')

@section('content')
<style>
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: auto;
        gap: 24px;
    }
    @media (max-width: 1024px) {
        .bento-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
    }
    .chart-bar {
        transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .color-dot {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid transparent;
        cursor: pointer;
        transition: transform 0.2s, border-color 0.2s;
    }
    .color-dot:hover {
        transform: scale(1.15);
    }
    .color-dot.active-accent {
        border-color: #000;
        box-shadow: 0 0 0 2px rgba(0,0,0,0.1);
    }
</style>

<div class="p-4 md:p-8 max-w-[1440px] mx-auto">
    <!-- Welcome Executive Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-primary-container/10 text-primary-container font-label-sm text-[11px] font-bold uppercase tracking-wider">
                    Executive Portal
                </span>
                <span class="text-xs text-secondary font-label-sm">System Live & Logged</span>
            </div>
            <h3 class="font-headline-lg text-2xl md:text-3xl font-black text-on-surface mb-1">Executive Overview</h3>
            <p class="font-body-md text-secondary text-sm md:text-base max-w-2xl">
                Manage your institute's operational pulse. Track financial health, enrollment velocity, and instructional capacity in real-time.
            </p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-white border border-border-subtle rounded-lg font-button text-sm text-on-surface flex items-center gap-2 hover:bg-surface-slate transition-all shadow-sm text-decoration-none">
                <span class="material-symbols-outlined text-[18px]">download</span>
                Export Report
            </a>
            <a href="{{ route('students.create') }}" class="px-4 py-2 bg-primary-container text-white rounded-lg font-button text-sm flex items-center gap-2 hover:brightness-110 transition-all shadow-lg shadow-primary/20 text-decoration-none">
                <span class="material-symbols-outlined text-[18px]">add</span>
                New Enrollment
            </a>
        </div>
    </div>

    <!-- Metrics Bento Grid -->
    <div class="bento-grid mb-6">
        <!-- Revenue Metric -->
        <div class="p-6 bg-white border border-border-subtle rounded-xl hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-primary-container/10 rounded-lg">
                    <span class="material-symbols-outlined text-primary-container">payments</span>
                </div>
                <span class="px-2 py-1 bg-success-green/10 text-success-green font-label-sm text-[10px] rounded flex items-center gap-1 font-bold">
                    <span class="material-symbols-outlined text-[12px]">trending_up</span>
                    Received Receipts
                </span>
            </div>
            <p class="font-label-sm text-secondary uppercase tracking-wider text-xs mb-1">Total Revenue</p>
            <h4 class="font-headline-lg text-2xl md:text-3xl text-on-surface font-black">₹{{ number_format($totalIncome, 2) }}</h4>
            <div class="mt-4 h-10 flex items-end gap-1">
                <div class="flex-1 bg-primary-container/20 rounded-t-sm h-1/2 group-hover:h-3/4 transition-all"></div>
                <div class="flex-1 bg-primary-container/30 rounded-t-sm h-2/3 group-hover:h-full transition-all delay-75"></div>
                <div class="flex-1 bg-primary-container/40 rounded-t-sm h-1/3 group-hover:h-1/2 transition-all delay-100"></div>
                <div class="flex-1 bg-primary-container/60 rounded-t-sm h-3/4 group-hover:h-5/6 transition-all delay-150"></div>
                <div class="flex-1 bg-primary-container/80 rounded-t-sm h-1/2 group-hover:h-2/3 transition-all delay-200"></div>
                <div class="flex-1 bg-primary-container rounded-t-sm h-full group-hover:h-full transition-all delay-300"></div>
            </div>
        </div>

        <!-- Enrollments Metric -->
        <div class="p-6 bg-white border border-border-subtle rounded-xl hover:shadow-md transition-shadow group relative">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-info-blue/10 rounded-lg">
                    <span class="material-symbols-outlined text-info-blue">person_add</span>
                </div>
                <span class="px-2 py-1 bg-success-green/10 text-success-green font-label-sm text-[10px] rounded flex items-center gap-1 font-bold">
                    <span class="material-symbols-outlined text-[12px]">trending_up</span>
                    Active
                </span>
            </div>
            <p class="font-label-sm text-secondary uppercase tracking-wider text-xs mb-1">Total Students</p>
            <h4 class="font-headline-lg text-2xl md:text-3xl text-on-surface font-black">{{ number_format($studentCount) }}</h4>
            <p class="text-[12px] text-secondary mt-2">Enrolled across active batches</p>
        </div>

        <!-- Active Courses Metric -->
        <div class="p-6 bg-white border border-border-subtle rounded-xl hover:shadow-md transition-shadow group relative">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-on-tertiary-container/10 rounded-lg">
                    <span class="material-symbols-outlined text-on-tertiary-container">library_books</span>
                </div>
                <a href="{{ route('courses.index') }}" class="text-xs text-primary-container hover:underline font-button">View All</a>
            </div>
            <p class="font-label-sm text-secondary uppercase tracking-wider text-xs mb-1">Active Master Courses</p>
            <h4 class="font-headline-lg text-2xl md:text-3xl text-on-surface font-black">{{ $coursesCount }}</h4>
            <div class="mt-4 w-full bg-surface-container-low h-2 rounded-full overflow-hidden">
                <div class="bg-primary-container h-full w-[85%] transition-all duration-1000"></div>
            </div>
            <p class="text-[12px] text-secondary mt-2">Active syllabus curriculum</p>
        </div>

        <!-- Staff Metric -->
        <div class="p-6 bg-white border border-border-subtle rounded-xl hover:shadow-md transition-shadow group relative">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-secondary-container rounded-lg">
                    <span class="material-symbols-outlined text-on-surface">engineering</span>
                </div>
                <a href="{{ route('employees.index') }}" class="text-xs text-secondary hover:underline font-button">Manage Staff</a>
            </div>
            <p class="font-label-sm text-secondary uppercase tracking-wider text-xs mb-1">Active Staff</p>
            <h4 class="font-headline-lg text-2xl md:text-3xl text-on-surface font-black">{{ number_format($employeeCount) }}</h4>
            <p class="text-[12px] text-secondary mt-2">Allocated across departments</p>
        </div>
    </div>

    <!-- Visualization & Data Row -->
    <div class="grid grid-cols-12 gap-6 mb-6">
        <!-- Course Popularity Chart (Left 5) -->
        <div class="col-span-12 lg:col-span-5 p-6 bg-white border border-border-subtle rounded-xl flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h5 class="font-title-md text-lg font-bold text-on-surface">Course Popularity</h5>
                    <span class="text-xs text-secondary bg-surface-slate px-2.5 py-1 rounded font-label-sm">Enrolled Stats</span>
                </div>
                <div class="space-y-5">
                    @forelse($coursesPopularity as $index => $course)
                        @php
                            $pct = $maxStudentsInCourse > 0 ? round(($course->students_count / $maxStudentsInCourse) * 100) : 0;
                            $barColors = ['bg-primary-container', 'bg-primary', 'bg-info-blue', 'bg-tertiary'];
                            $barColor = $barColors[$index % count($barColors)];
                        @endphp
                        <div>
                            <div class="flex justify-between mb-1 text-sm">
                                <span class="font-body-md text-on-surface font-semibold">{{ $course->name }}</span>
                                <span class="font-label-sm text-secondary text-xs">{{ $course->students_count }} Students</span>
                            </div>
                            <div class="w-full bg-surface-container-low h-7 rounded-lg overflow-hidden flex items-center px-1">
                                <div class="{{ $barColor }} h-5 rounded transition-all duration-1000 flex items-center px-2" style="width: {{ max(12, $pct) }}%;">
                                    <span class="text-[10px] text-white font-bold">{{ $pct }}%</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-secondary text-sm">No course popularity data available.</div>
                    @endforelse
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-surface-slate rounded-lg flex items-center gap-4 border border-border-subtle">
                <div class="w-10 h-10 rounded-full bg-primary-container/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary-container text-xl">tips_and_updates</span>
                </div>
                <div>
                    <p class="font-body-md text-on-surface font-bold text-xs uppercase tracking-wide mb-0.5">Enrollment Insight</p>
                    <p class="text-xs text-secondary mb-0">
                        @if($coursesPopularity->isNotEmpty())
                            <strong>{{ $coursesPopularity->first()->name }}</strong> is leading with {{ $coursesPopularity->first()->students_count }} enrolled students.
                        @else
                            Regularly review course enrollment metrics to optimize faculty allocation.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Enrollments Table (Right 7) -->
        <div class="col-span-12 lg:col-span-7 p-6 bg-white border border-border-subtle rounded-xl flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h5 class="font-title-md text-lg font-bold text-on-surface">Recent Enrollments</h5>
                        <p class="text-xs text-secondary">Latest students joined in system</p>
                    </div>
                    <a href="{{ route('students.index') }}" class="text-primary-container font-button text-sm hover:underline font-bold text-decoration-none">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-slate border-b border-border-subtle">
                                <th class="px-4 py-3 font-label-sm text-xs text-secondary uppercase tracking-wider">Student</th>
                                <th class="px-4 py-3 font-label-sm text-xs text-secondary uppercase tracking-wider">Course</th>
                                <th class="px-4 py-3 font-label-sm text-xs text-secondary uppercase tracking-wider">Admission No</th>
                                <th class="px-4 py-3 font-label-sm text-xs text-secondary uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 font-label-sm text-xs text-secondary uppercase tracking-wider text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border-subtle text-sm">
                            @forelse($recentStudents as $student)
                                <tr class="hover:bg-surface-slate transition-colors group">
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-3">
                                            @if($student->user && $student->user->profile_pic)
                                                <img class="w-8 h-8 rounded-full object-cover border border-border-subtle" src="{{ asset('uploads/profiles/' . $student->user->profile_pic) }}" alt="{{ $student->first_name }}">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-primary-container/10 flex items-center justify-center font-bold text-primary-container text-xs">
                                                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-body-md font-semibold text-on-surface mb-0">{{ $student->first_name }} {{ $student->last_name }}</p>
                                                <p class="text-[11px] text-secondary mb-0">{{ $student->email ?? ($student->user?->email ?? 'No email') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="font-body-md text-on-surface">{{ $student->course?->name ?? 'Unassigned' }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 font-label-sm text-secondary text-xs">
                                        {{ $student->admission_no ?? ('STD-'.$student->id) }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="px-2.5 py-1 bg-success-green/10 text-success-green text-[11px] font-bold rounded-full inline-block">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <a href="{{ route('students.show', $student) }}" class="p-1.5 hover:bg-surface-container-high rounded transition-all text-secondary hover:text-primary-container inline-block">
                                            <span class="material-symbols-outlined text-lg align-middle">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-secondary">No recent student registrations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Operational Activity Bento Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="p-6 bg-white border border-border-subtle rounded-xl flex items-center justify-between shadow-sm">
            <div>
                <p class="font-label-sm text-secondary text-xs mb-1 uppercase tracking-wider">Attendance Logs Today</p>
                <h4 class="font-title-md text-xl font-bold text-on-surface">{{ number_format($attendanceCount) }} Logs</h4>
                <a href="{{ route('attendances.index') }}" class="text-xs text-primary-container hover:underline font-button mt-1 inline-block">View Records &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-full bg-success-green/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-success-green text-2xl">event_available</span>
            </div>
        </div>

        <div class="p-6 bg-white border border-border-subtle rounded-xl flex items-center justify-between shadow-sm">
            <div>
                <p class="font-label-sm text-secondary text-xs mb-1 uppercase tracking-wider">Staff Work Hours (10-5)</p>
                <h4 class="font-title-md text-xl font-bold text-on-surface">{{ number_format($workingHoursEmployeesCount) }} Active Today</h4>
                <a href="{{ route('employee-attendances.index') }}" class="text-xs text-info-blue hover:underline font-button mt-1 inline-block">Staff Attendance &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-full bg-info-blue/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-info-blue text-2xl">badge</span>
            </div>
        </div>

        <div class="p-6 bg-primary text-white rounded-xl shadow-lg relative overflow-hidden flex flex-col justify-between" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <div class="relative z-10">
                <p class="font-label-sm opacity-80 text-xs mb-1 uppercase tracking-wider text-slate-300">Outstanding Fee Receipts</p>
                <h4 class="font-title-md text-xl font-bold text-white mb-1">₹{{ number_format($totalPendingFees, 2) }}</h4>
                <p class="text-xs text-slate-300 mb-4">{{ $dueInvoices }} Unpaid Invoices</p>
                <a href="{{ route('fee_invoices.index') }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-white font-button text-xs transition-all inline-block text-decoration-none">
                    Fee Management
                </a>
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-[100px] opacity-10 rotate-12 text-white">domain</span>
        </div>
    </div>

    <!-- Quick Actions & Customization Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Quick Actions Panel (8 cols) -->
        <div class="lg:col-span-8 p-6 bg-white border border-border-subtle rounded-xl shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h5 class="font-title-md text-lg font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary-container">bolt</span>
                    Quick Management Shortcuts
                </h5>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('students.create') }}" class="shortcut-btn p-4 bg-surface-slate border border-border-subtle rounded-xl flex flex-col items-center justify-center text-center gap-2 text-on-surface hover:text-primary-container hover:border-primary-container text-decoration-none">
                    <span class="material-symbols-outlined text-2xl text-primary-container">person_add</span>
                    <span class="font-button text-xs font-semibold">Add Student</span>
                </a>
                <a href="{{ route('attendances.index') }}" class="shortcut-btn p-4 bg-surface-slate border border-border-subtle rounded-xl flex flex-col items-center justify-center text-center gap-2 text-on-surface hover:text-info-blue hover:border-info-blue text-decoration-none">
                    <span class="material-symbols-outlined text-2xl text-info-blue">fact_check</span>
                    <span class="font-button text-xs font-semibold">Attendance Log</span>
                </a>
                <a href="{{ route('expenses.create') }}" class="shortcut-btn p-4 bg-surface-slate border border-border-subtle rounded-xl flex flex-col items-center justify-center text-center gap-2 text-on-surface hover:text-error hover:border-error text-decoration-none">
                    <span class="material-symbols-outlined text-2xl text-error">receipt_long</span>
                    <span class="font-button text-xs font-semibold">Record Expense</span>
                </a>
                <a href="{{ route('courses.create') }}" class="shortcut-btn p-4 bg-surface-slate border border-border-subtle rounded-xl flex flex-col items-center justify-center text-center gap-2 text-on-surface hover:text-success-green hover:border-success-green text-decoration-none">
                    <span class="material-symbols-outlined text-2xl text-success-green">post_add</span>
                    <span class="font-button text-xs font-semibold">Create Course</span>
                </a>
            </div>
            <div class="mt-4 pt-3 border-t border-border-subtle flex justify-between items-center">
                <form action="{{ route('clear-cache') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 bg-primary-container/10 text-primary-container rounded-lg font-button text-xs font-bold hover:bg-primary-container/20 transition-all flex items-center justify-center gap-2 border-0">
                        <span class="material-symbols-outlined text-base">cleaning_services</span>
                        Clear Application System Cache
                    </button>
                </form>
            </div>
        </div>

        <!-- Theme Accent Settings (4 cols) -->
        <div class="lg:col-span-4 p-6 bg-white border border-border-subtle rounded-xl shadow-sm flex flex-col justify-between">
            <div>
                <h5 class="font-title-md text-lg font-bold text-on-surface flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-primary-container">palette</span>
                    Workspace Customizer
                </h5>
                <p class="text-xs text-secondary mb-4">Personalize theme appearance & color accent.</p>
                
                <div class="mb-4">
                    <label class="font-label-sm text-xs text-secondary uppercase font-bold tracking-wider block mb-2">Accent Color</label>
                    <div class="flex flex-wrap gap-2.5" id="accent-picker">
                        <button type="button" class="color-dot" style="background: #ff5532;" onclick="selectAccentColor(this, '#ff5532', '#d63a1f', 'rgba(255, 85, 50, 0.12)', 'rgba(255, 85, 50, 0.22)')" title="Sunset Orange"></button>
                        <button type="button" class="color-dot" style="background: #3b82f6;" onclick="selectAccentColor(this, '#3b82f6', '#1d4ed8', 'rgba(59, 130, 246, 0.12)', 'rgba(59, 130, 246, 0.22)')" title="Ocean Blue"></button>
                        <button type="button" class="color-dot" style="background: #10b981;" onclick="selectAccentColor(this, '#10b981', '#059669', 'rgba(16, 185, 129, 0.12)', 'rgba(16, 185, 129, 0.22)')" title="Emerald Green"></button>
                        <button type="button" class="color-dot" style="background: #8b5cf6;" onclick="selectAccentColor(this, '#8b5cf6', '#6d28d9', 'rgba(139, 92, 246, 0.12)', 'rgba(139, 92, 246, 0.22)')" title="Royal Purple"></button>
                        <button type="button" class="color-dot" style="background: #ec4899;" onclick="selectAccentColor(this, '#ec4899', '#be185d', 'rgba(236, 72, 153, 0.12)', 'rgba(236, 72, 153, 0.22)')" title="Deep Pink"></button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="font-label-sm text-xs text-secondary uppercase font-bold tracking-wider block mb-2">Display Theme</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" class="px-3 py-1.5 border border-border-subtle rounded-lg text-xs font-button flex items-center justify-center gap-1.5 hover:bg-surface-slate" onclick="setThemeMode('light')">
                            <span class="material-symbols-outlined text-base">light_mode</span> Light
                        </button>
                        <button type="button" class="px-3 py-1.5 border border-border-subtle rounded-lg text-xs font-button flex items-center justify-center gap-1.5 hover:bg-surface-slate" onclick="setThemeMode('dark')">
                            <span class="material-symbols-outlined text-base">dark_mode</span> Dark
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-border-subtle flex justify-between items-center text-xs">
                <button type="button" class="text-error font-bold hover:underline bg-transparent border-0 p-0" onclick="window.resetTheme()">
                    Reset Theme
                </button>
                <span class="text-secondary text-[11px]">Autosaved</span>
            </div>
        </div>
    </div>

    <!-- Embedded Messages Suite Widget -->
    <div class="w-full mt-6">
        @include('messages.widget')
    </div>
</div>

<script>
    function selectAccentColor(btn, primary, dark, light, focus) {
        document.querySelectorAll('#accent-picker .color-dot').forEach(el => {
            el.classList.remove('active-accent');
        });
        btn.classList.add('active-accent');
        if (window.applyPrimaryColor) {
            window.applyPrimaryColor(primary, dark, light, focus);
        }
    }
    
    function setThemeMode(mode) {
        document.documentElement.dataset.theme = mode;
        if (mode === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        localStorage.setItem('fees-theme', mode);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedColor = localStorage.getItem('fees-primary-color');
        const buttons = document.querySelectorAll('#accent-picker .color-dot');
        if (savedColor) {
            try {
                const colors = JSON.parse(savedColor);
                buttons.forEach(btn => {
                    const styleBg = btn.style.backgroundColor;
                    if (rgb2hex(styleBg) === colors.primary.toLowerCase()) {
                        btn.classList.add('active-accent');
                    }
                });
            } catch(e) { console.error(e); }
        } else {
            const orangeBtn = document.querySelector('#accent-picker .color-dot[title="Sunset Orange"]');
            if (orangeBtn) orangeBtn.classList.add('active-accent');
        }
    });

    function rgb2hex(rgb) {
        if (!rgb) return '';
        if (rgb.search("rgb") == -1) return rgb.toLowerCase();
        rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
        function hex(x) {
            return ("0" + parseInt(x).toString(16)).slice(-2);
        }
        return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
    }
</script>
@endsection
