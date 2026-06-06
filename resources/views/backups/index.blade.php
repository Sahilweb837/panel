@extends('layouts.app')

@section('title', 'Database Backups')
@section('page-title', 'Backup Management')

@section('content')
    <div class="backups-container">
        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 250px; height: 42px;"></div>
            </div>
            <div class="card premium-form-card" style="max-width: 100%;">
                <div class="sk-text heading"></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
                <div class="sk-row"><div class="sk-text"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h2 class="h5 fw-bold text-dark-title mb-0">System Backups</h2>
                    <p class="text-muted small mb-0">Automated daily backups run at 23:00</p>
                </div>
                <form action="{{ route('backups.create') }}" method="POST">
                    @csrf
                    <button type="submit" class="button button-primary py-2 px-4">
                        <i class="fas fa-database me-2"></i>Generate Backup Now
                    </button>
                </form>
            </div>

            <div class="card premium-stat-card p-0 table-card overflow-hidden">
                <div class="premium-card-header bg-transparent border-bottom p-4">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-server text-first"></i> Available Backup Files
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table premium-table table-hover align-middle mb-0">
                        <thead>
                            <tr class="table-light-head">
                                <th class="ps-4"><i class="fas fa-file-archive me-1"></i> File Name</th>
                                <th><i class="fas fa-hdd me-1"></i> Size</th>
                                <th><i class="fas fa-calendar-day me-1"></i> Date Created</th>
                                <th class="text-end pe-4"><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $backup)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark-title" style="font-family: monospace; font-size: 0.9rem;">
                                            {{ $backup['name'] }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-muted">{{ $backup['size'] }}</td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($backup['date'])->format('M d, Y h:i A') }}</td>
                                    <td class="text-end pe-4 action-cell">
                                        <a href="{{ route('backups.download', base64_encode($backup['name'])) }}" class="button button-success small py-1.5 px-3 me-1">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                        
                                        <form action="{{ route('backups.restore', base64_encode($backup['name'])) }}" method="POST" class="inline-form d-inline me-1" onsubmit="return confirm('WARNING: This will overwrite the current database with this backup. Are you absolutely sure?');">
                                            @csrf
                                            <button type="submit" class="button button-secondary small py-1.5 px-3">
                                                <i class="fas fa-history me-1"></i>Restore
                                            </button>
                                        </form>

                                        <form action="{{ route('backups.destroy', base64_encode($backup['name'])) }}" method="POST" class="inline-form d-inline" onsubmit="return confirm('Are you sure you want to delete this backup file?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button button-danger small py-1.5 px-3">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 d-block text-muted"></i>
                                        No database backups available. Generate one to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and skeleton fading -->
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
