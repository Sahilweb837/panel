@extends('layouts.app')

@section('title', 'Daily Work Logs')
@section('page-title', 'Daily Work Logs')

@section('content')
    <div class="daily-updates-container">
        <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <form method="GET" action="{{ route('daily-updates.index') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                <select name="employee_id" class="form-input" style="max-width: 220px;">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->user?->name ?? 'Unknown' }} ({{ $emp->employee_code }})
                        </option>
                    @endforeach
                </select>
                <input type="date" name="date" class="form-input" style="max-width: 180px;" value="{{ request('date') }}" />
                <button type="submit" class="button button-secondary py-2 px-4">
                    <i class="fas fa-filter me-2"></i>Filter
                </button>
                @if(request('employee_id') || request('date'))
                    <a href="{{ route('daily-updates.index') }}" class="button button-secondary px-3 py-2">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="card premium-stat-card p-0 table-card overflow-hidden">
            <div class="card-body p-0">
                <table class="table premium-table table-hover align-middle mb-0">
                    <thead>
                        <tr class="table-light-head">
                            <th class="ps-4" style="width: 180px;">Date</th>
                            <th style="width: 220px;">Employee</th>
                            <th>Work Update / Log Details</th>
                            <th class="pe-4 text-end" style="width: 150px;">Logged Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($updates as $update)
                            <tr>
                                <td class="ps-4 fw-bold text-dark-title">
                                    <i class="fas fa-calendar-day text-first me-2"></i>
                                    {{ \Carbon\Carbon::parse($update->date)->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-dark-title">{{ $update->employee?->user?->name ?? 'Unknown Staff' }}</strong>
                                        <span class="text-muted small">{{ $update->employee?->employee_code }} - {{ $update->employee?->designation }}</span>
                                    </div>
                                </td>
                                <td class="text-dark-title" style="word-break: break-word; line-height: 1.5; font-size: 0.92rem;">
                                    @if($update->work_title)
                                        <div class="fw-bold mb-1" style="font-size: 0.95rem; color: var(--first-color, #ff5532);">
                                            <i class="fas fa-tasks me-1"></i>{{ $update->work_title }}
                                        </div>
                                    @endif
                                    <div style="white-space: pre-line;">{{ $update->update_text }}</div>
                                    @if($update->file_path)
                                        <div class="mt-2">
                                            <a href="{{ asset($update->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-3 py-1" style="border-radius: 6px; font-size: 0.75rem;">
                                                <i class="fas fa-paperclip me-1"></i>View Attachment
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td class="pe-4 text-end text-muted small">
                                    {{ $update->created_at->format('h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-edit fa-2x mb-3 d-block"></i>
                                    No work updates logged yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pagination-wrapper mt-4">
            {{ $updates->links() }}
        </div>
    </div>
@endsection
