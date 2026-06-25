@extends('layouts.app')

@section('title', 'Training Analytics')
@section('page-title', 'Training & Internship Analytics')

@section('content')
    <div class="invoice-container">
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 280px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
            </div>
            <div class="row g-4">
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
                <div class="col-md-3"><div class="sk-card" style="height: 120px;"></div></div>
            </div>
        </div>

        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form method="GET" action="{{ route('trainings.analytics') }}" class="filter-form d-flex align-items-center gap-2 flex-grow-1">
                    <div style="position: relative; width: 160px;">
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-input" style="padding-left: 36px;" placeholder="From Date" />
                        <i class="fas fa-calendar text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 160px;">
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-input" style="padding-left: 36px;" placeholder="To Date" />
                        <i class="fas fa-calendar text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 200px;">
                        <select name="course_name" class="form-input" style="padding-left: 36px;">
                            <option value="">All Courses</option>
                            @foreach($courseStats as $stat)
                                <option value="{{ $stat->course_name }}" {{ request('course_name') == $stat->course_name ? 'selected' : '' }}>{{ $stat->course_name ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-book text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Apply Filter
                    </button>
                    @if(request('from_date') || request('to_date') || request('course_name'))
                        <a href="{{ route('trainings.analytics') }}" class="button button-secondary px-3 py-2">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon" style="width: 48px; height: 48px; border-radius: 12px; background: var(--first-color-light); display: flex; align-items: center; justify-content: center; color: var(--first-color); font-size: 1.2rem;">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1" style="font-size: 0.8rem;">Total Registrations</p>
                                    <h3 class="mb-0 fw-bold text-dark-title">{{ $totalRegistrations }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: #10b981; font-size: 1.2rem;">
                                    <i class="fas fa-rupee-sign"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1" style="font-size: 0.8rem;">Total Revenue</p>
                                    <h3 class="mb-0 fw-bold text-dark-title">{{ number_format($totalRevenue, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); display: flex; align-items: center; justify-content: center; color: #f59e0b; font-size: 1.2rem;">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1" style="font-size: 0.8rem;">Active Courses</p>
                                    <h3 class="mb-0 fw-bold text-dark-title">{{ $courseStats->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card premium-stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="stat-icon" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #3b82f6; font-size: 1.2rem;">
                                    <i class="fas fa-filter"></i>
                                </div>
                                <div>
                                    <p class="text-muted mb-1" style="font-size: 0.8rem;">Filtered Revenue</p>
                                    <h3 class="mb-0 fw-bold text-dark-title">{{ number_format($filteredRevenue, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card premium-stat-card h-100">
                        <div class="card-header bg-transparent border-bottom p-4">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-book text-first me-2"></i>Course-wise Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <table class="table premium-table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="table-light-head">
                                        <th>Course</th>
                                        <th class="text-center">Registrations</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($courseStats as $stat)
                                        <tr>
                                            <td>{{ $stat->course_name ?? 'N/A' }}</td>
                                            <td class="text-center fw-bold">{{ $stat->count }}</td>
                                            <td class="text-end fw-bold text-success">{{ number_format($stat->revenue, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-4 text-muted">No data available</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card premium-stat-card h-100">
                        <div class="card-header bg-transparent border-bottom p-4">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-clock text-first me-2"></i>Duration-wise Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <table class="table premium-table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="table-light-head">
                                        <th>Duration</th>
                                        <th class="text-center">Registrations</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalCount = $durationStats->sum('count');
                                    @endphp
                                    @forelse($durationStats as $stat)
                                        <tr>
                                            <td>{{ $stat->duration }}</td>
                                            <td class="text-center fw-bold">{{ $stat->count }}</td>
                                            <td class="text-end fw-bold">{{ $totalCount > 0 ? round(($stat->count / $totalCount) * 100, 1) : 0 }}%</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-4 text-muted">No data available</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card premium-stat-card">
                <div class="card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-history text-first me-2"></i>Recent Registrations</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4">Slip No</th>
                                <th>Candidate</th>
                                <th>Course</th>
                                <th class="text-end">Fees</th>
                                <th class="text-end pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRegistrations as $training)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border p-2 fw-bold" style="font-size: 0.8rem;">
                                            {{ $training->slip_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="avatar" style="font-weight: 700; background: rgba(255, 85, 50, 0.1); color: var(--first-color);">
                                                {{ strtoupper(substr($training->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong class="text-dark-title">{{ $training->name }}</strong>
                                                <p class="text-muted small">{{ $training->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $training->course_name ?? 'N/A' }}</td>
                                    <td class="text-end fw-bold">{{ number_format($training->fees, 2) }}</td>
                                    <td class="text-end pe-4 text-muted">{{ $training->payment_date ? \Carbon\Carbon::parse($training->payment_date)->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No registrations found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
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
