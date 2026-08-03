@extends('layouts.app')

@section('title', 'Meeting Schedule - Netcoder')
@section('page-title', 'Academic Scheduling')

@section('content')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e5e2de;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #d1cfcc;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
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
    <!-- Notifications/Alerts -->
    @include('layouts.alerts')

    <!-- PAGE HEADER -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <p class="text-primary font-label-sm uppercase tracking-wider mb-2 font-bold">Workspace</p>
            <h3 class="font-headline-lg text-2xl md:text-3xl font-black text-on-surface mb-0">Collaboration & Scheduling</h3>
        </div>
    </div>

    <!-- BENTO GRID LAYOUT -->
    <div class="grid grid-cols-12 gap-6">
        <!-- LEFT COLUMN: CALENDAR & UPCOMING (8 COLS) -->
        <div class="col-span-12 lg:col-span-8 space-y-6">
            
            @php
                $currentDate = now();
                $year = $currentDate->year;
                $month = $currentDate->month;
                $monthName = $currentDate->format('F Y');
                $today = $currentDate->day;

                $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon\Carbon::SUNDAY);
                $endOfCalendar = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon\Carbon::SATURDAY);

                $days = [];
                $day = $startOfCalendar->copy();
                while ($day->lte($endOfCalendar)) {
                    $days[] = [
                        'date' => $day->copy(),
                        'isCurrentMonth' => $day->month === $month,
                        'isToday' => $day->isToday(),
                        'dayNum' => $day->day,
                    ];
                    $day->addDay();
                }
            @endphp

            <!-- MAIN CALENDAR CARD -->
            <div class="bg-white border border-border-subtle rounded-xl p-6 shadow-sm lift-card">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-4">
                        <h4 class="font-title-md text-lg font-bold text-on-surface mb-0">{{ $monthName }}</h4>
                    </div>
                    <span class="text-xs font-bold text-secondary bg-surface px-2.5 py-1 rounded">Today is {{ now()->format('M d, Y') }}</span>
                </div>
                <!-- Calendar Header -->
                <div class="calendar-grid text-center text-xs font-label-sm font-bold text-tertiary mb-3 border-b border-border-subtle pb-2">
                    <div>SUN</div><div>MON</div><div>TUE</div><div>WED</div><div>THU</div><div>FRI</div><div>SAT</div>
                </div>
                <!-- Calendar Days -->
                <div class="calendar-grid gap-px bg-border-subtle border border-border-subtle rounded-lg overflow-hidden">
                    @foreach($days as $dayData)
                        @php
                            $dayMeetings = $meetings->filter(function($m) use ($dayData) {
                                return \Carbon\Carbon::parse($m->meeting_date)->isSameDay($dayData['date']);
                            });
                        @endphp
                        <div class="h-24 p-2 text-sm flex flex-col justify-between overflow-hidden {{ $dayData['isToday'] ? 'bg-primary/5 ring-2 ring-primary ring-inset' : ($dayData['isCurrentMonth'] ? 'bg-white' : 'bg-surface-container-low text-secondary/40') }}">
                            <span class="{{ $dayData['isToday'] ? 'text-primary font-bold' : 'text-on-surface' }}">{{ $dayData['dayNum'] }}</span>
                            
                            <div class="space-y-1 overflow-y-auto custom-scrollbar flex-1 mt-1">
                                @foreach($dayMeetings as $m)
                                    <a href="{{ route('meetings.show', $m->id) }}" class="block px-1.5 py-0.5 text-[9px] font-bold rounded truncate text-decoration-none {{ $m->meeting_mode === 'Online' ? 'bg-success-green/10 border-l-2 border-success-green text-success-green' : ($m->meeting_mode === 'Offline' ? 'bg-info-blue/10 border-l-2 border-info-blue text-info-blue' : 'bg-primary/10 border-l-2 border-primary text-primary') }}" title="{{ $m->title }}">
                                        {{ $m->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- UPCOMING MEETINGS LIST -->
            <div class="bg-white border border-border-subtle rounded-xl p-6 shadow-sm lift-card">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="font-title-md text-lg font-bold text-on-surface mb-0">Upcoming Academic Meetings</h4>
                    <span class="badge bg-primary px-2.5 py-1">{{ $meetings->count() }} Total</span>
                </div>
                <div class="space-y-4">
                    @forelse($meetings->take(5) as $meeting)
                        @php
                            $mDate = \Carbon\Carbon::parse($meeting->meeting_date);
                        @endphp
                        <div class="flex flex-col md:flex-row md:items-center gap-4 p-4 rounded-xl border border-border-subtle hover:border-primary/20 transition-all bg-surface-slate/20">
                            <div class="flex flex-col items-center justify-center bg-surface-container rounded-lg w-14 h-14 shrink-0">
                                <span class="text-[10px] font-bold text-secondary uppercase">{{ $mDate->format('M') }}</span>
                                <span class="text-lg font-black text-primary leading-none">{{ $mDate->format('d') }}</span>
                            </div>
                            <div class="flex-1">
                                <h5 class="font-bold text-on-surface text-sm mb-1">
                                    <a href="{{ route('meetings.show', $meeting->id) }}" class="text-decoration-none text-on-surface hover:text-primary">
                                        {{ $meeting->title }}
                                    </a>
                                </h5>
                                <div class="flex flex-wrap items-center gap-3 text-xs text-secondary">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">meeting_room</span> {{ $meeting->location ?? ($meeting->meeting_mode === 'Online' ? 'Virtual Call Room' : 'Department Hall') }}</span>
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-full {{ $meeting->meeting_mode === 'Online' ? 'bg-success-green/10 text-success-green' : 'bg-info-blue/10 text-info-blue' }}">{{ $meeting->meeting_mode }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if(in_array($meeting->meeting_mode, ['Online', 'Hybrid']))
                                    <a href="{{ $meeting->meeting_link ?? route('meetings.join', ['id' => uniqid('meet-')]) }}" class="px-3.5 py-1.5 bg-primary-container text-white rounded-lg hover:brightness-110 transition-all font-button text-xs font-bold text-decoration-none shadow">
                                        Join Video Room
                                    </a>
                                @endif
                                <form action="{{ route('meetings.destroy', $meeting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this meeting?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 border border-border-subtle bg-white text-secondary hover:text-error rounded-lg hover:bg-red-50 transition-colors">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-secondary text-sm">No upcoming meetings scheduled.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SETUP & STATS (4 COLS) -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <!-- QUICK SETUP FORM -->
            <div class="bg-white border border-border-subtle rounded-xl p-6 shadow-sm lift-card">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-primary/10 rounded-lg">
                        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">add_circle</span>
                    </div>
                    <h4 class="font-title-md text-base font-bold text-on-surface mb-0">Quick Meeting Setup</h4>
                </div>
                <form action="{{ route('meetings.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1.5">Meeting Title</label>
                        <input name="title" required class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all placeholder:text-secondary/50" placeholder="e.g. Weekly Faculty Sync" type="text"/>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-on-surface mb-1.5">Date</label>
                            <input name="meeting_date" required class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all" type="date" value="{{ date('Y-m-d') }}"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface mb-1.5">Start Time</label>
                            <input name="start_time" required class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all" type="time" value="10:00"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-on-surface mb-1.5">End Time</label>
                            <input name="end_time" required class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all" type="time" value="11:00"/>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface mb-1.5">Meeting Mode</label>
                            <select name="meeting_mode" required class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all">
                                <option value="Online">Online (Video Room)</option>
                                <option value="Offline">Offline (On-Premise)</option>
                                <option value="Hybrid">Hybrid (Both)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-on-surface mb-1.5">Department</label>
                            <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all">
                                <option value="">General / None</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface mb-1.5">Location/Room</label>
                            <input name="location" class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all placeholder:text-secondary/50" placeholder="e.g. Hall B" type="text"/>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1.5">Invite Participants</label>
                        <select name="participants[]" multiple class="w-full px-3 py-2 rounded-lg border border-border-subtle bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary text-xs transition-all" style="min-height: 80px;">
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role?->role_name }})</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-secondary">Hold Ctrl/Cmd to select multiple.</span>
                    </div>
                    <div class="pt-2">
                        <button class="w-full py-2.5 bg-primary text-white font-button rounded-lg hover:brightness-110 active:scale-95 transition-all shadow border-0 cursor-pointer text-xs" type="submit">Create Meeting</button>
                    </div>
                </form>
            </div>

            <!-- STATS CARD -->
            <div class="bg-inverse-surface text-white rounded-xl p-6 relative overflow-hidden shadow-md" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(255,255,255,0.05);">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <span class="material-symbols-outlined text-[80px]">auto_graph</span>
                </div>
                <h4 class="text-slate-400 font-label-sm text-xs uppercase tracking-widest mb-6">Institute Momentum</h4>
                <div class="space-y-4 relative z-10 text-xs">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="font-medium text-slate-300">Meeting Productivity</span>
                            <span class="font-bold">92%</span>
                        </div>
                        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-primary-container w-[92%] h-full rounded-full"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="font-medium text-slate-300">Course Alignment</span>
                            <span class="font-bold">84%</span>
                        </div>
                        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-success-green w-[84%] h-full rounded-full"></div>
                        </div>
                    </div>
                </div>
                <p class="mt-8 text-xs text-slate-300 leading-relaxed mb-0">
                    You have <span class="text-white font-bold">{{ $meetings->count() }} active meetings</span> scheduled for the institute.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
