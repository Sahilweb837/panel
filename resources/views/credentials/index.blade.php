@extends('layouts.app')

@section('title', 'User Credentials & Portal Access')
@section('page-title', 'User Credentials & Portal Access')

@section('content')
    <div class="credentials-container">
        <!-- Toast Notification for Copying -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
            <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body py-2 px-3">
                        <i class="fas fa-check-circle text-success me-2"></i><span id="toastMessage">Copied to clipboard!</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Lazy Loading Skeleton Overlay -->
        <div class="skeleton-loader-overlay" id="page-skeleton">
            <div class="toolbar mb-4">
                <div class="sk-card" style="width: 320px; height: 42px;"></div>
                <div class="sk-card" style="width: 150px; height: 42px;"></div>
                <div class="sk-card" style="width: 130px; height: 42px;"></div>
            </div>
            <div class="row g-4">
                <div class="col-12"><div class="sk-card" style="height: 400px;"></div></div>
            </div>
        </div>

        <!-- Real Content -->
        <div id="page-content" style="opacity: 0; transition: opacity 0.5s ease;">
            
            <div class="alert alert-info mb-4 d-flex align-items-center gap-3 border-0 shadow-sm" style="background: rgba(13, 202, 240, 0.08); color: #055160; border-left: 4px solid #0dcaf2 !important;">
                <i class="fas fa-shield-alt fa-2x text-info"></i>
                <div>
                    <h6 class="fw-bold mb-1">Student & Staff Portal Credentials Center</h6>
                    <p class="mb-0 small">Admin can view plain text passwords, check active portal access status, copy student IDs (e.g. <code>NT-ENR-011</code>), and directly click <strong>Portal Login</strong> to access student dashboards.</p>
                </div>
            </div>

            <div class="toolbar mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <form class="filter-form d-flex align-items-center gap-2 flex-grow-1 flex-wrap" method="GET" action="{{ route('credentials.index') }}">
                    <div style="position: relative; flex: 1; min-width: 240px;">
                        <input type="text" name="search" placeholder="Search by name, email, username or student ID (e.g. NT-ENR-011)..." value="{{ request('search') }}" class="form-input" style="padding-left: 36px;" />
                        <i class="fas fa-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <div style="position: relative; width: 170px;">
                        <select name="role" class="form-input" style="padding-left: 36px;">
                            <option value="">All Roles</option>
                            <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                        <i class="fas fa-user-tag text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                    </div>
                    <button type="submit" class="button button-secondary px-4 py-2">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('credentials.index') }}" class="button button-secondary px-3 py-2" title="Reset Filters">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
                <a href="{{ route('credentials.create') }}" class="button button-primary px-4 py-2">
                    <i class="fas fa-plus me-2"></i>Create Credential
                </a>
            </div>

            <div class="card premium-stat-card border-0 p-0 overflow-hidden mb-4 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="min-width: 900px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Name / Student Details</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Role</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Login Email</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Login Username / Student ID</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4">Plain Text Password</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4 text-center">Portal Access</th>
                                <th class="text-uppercase text-secondary small fw-bold py-3 px-4 text-end">Actions / Dashboard Access</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $isStudent = $user->role?->slug === 'student';
                                    $student = $user->student;
                                    $portalActive = $isStudent ? ($student ? $student->portal_active : $user->status) : $user->status;
                                    $loginIdentifier = $user->username ?: ($student?->admission_no ?? '-');
                                @endphp
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="fw-bold text-dark-title" style="font-size: 0.95rem;">{{ $user->name }}</div>
                                        @if($student)
                                            <small class="text-muted d-block">
                                                <i class="fas fa-graduation-cap me-1 text-primary"></i>Adm: <strong>{{ $student->admission_no }}</strong> {{ $student->course ? '('.$student->course->name.')' : '' }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="badge" style="background-color: {{ $user->role?->slug === 'staff' ? 'rgba(111, 66, 193, 0.12)' : 'rgba(255, 85, 50, 0.12)' }}; color: {{ $user->role?->slug === 'staff' ? '#6f42c1' : 'var(--first-color)' }}; font-weight: 700; padding: 6px 14px; border-radius: 6px;">
                                            {{ ucfirst($user->role?->slug ?? 'User') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-muted small">
                                        {{ $user->email ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold" style="font-family: monospace; font-size: 0.95rem; color: #2c3e50; background: #f8f9fa; padding: 4px 8px; border-radius: 5px; border: 1px solid #dee2e6;">
                                                {{ $loginIdentifier }}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-light border px-2 py-1" onclick="copyToClipboard('{{ $loginIdentifier }}', 'Username / ID')" title="Copy Username / ID">
                                                <i class="fas fa-copy text-secondary" style="font-size: 0.8rem;"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="password-field fw-bold" style="font-family: monospace; background: #fff8f0; padding: 4px 10px; border-radius: 5px; border: 1px solid #ffe0b2; color: #d97706;">
                                                {{ $user->raw_password ?? 'Encrypted' }}
                                            </span>
                                            @if($user->raw_password)
                                                <button type="button" class="btn btn-sm btn-light border px-2 py-1" onclick="copyToClipboard('{{ $user->raw_password }}', 'Password')" title="Copy Password">
                                                    <i class="fas fa-copy text-secondary" style="font-size: 0.8rem;"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('credentials.toggle-portal', $user->id) }}" method="POST">
                                            @csrf
                                            @if($portalActive)
                                                <button type="submit" class="btn btn-sm btn-success-subtle text-success border border-success fw-bold px-3 py-1" title="Click to Deactivate Portal Access">
                                                    <i class="fas fa-check-circle me-1"></i>Active Access
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-danger-subtle text-danger border border-danger fw-bold px-3 py-1" title="Click to Activate Portal Access">
                                                    <i class="fas fa-times-circle me-1"></i>Disabled
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                    <td class="py-3 px-4 text-end">
                                        <div class="d-flex justify-content-end align-items-center gap-2">
                                            <a href="{{ route('credentials.impersonate', $user->id) }}" class="btn btn-sm button-primary px-3 py-1 text-white fw-bold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.82rem;" title="1-Click Login to Student/User Dashboard">
                                                <i class="fas fa-sign-in-alt"></i> Portal Login
                                            </a>
                                            
                                            @php
                                                $waText = "";
                                                $waPhone = "";
                                                if ($isStudent && $student) {
                                                    $name = trim($student->first_name . ' ' . ($student->last_name ?? ''));
                                                    $courseName = $student->course?->name ?? 'N/A';
                                                    $waText = "🎓 *Student Portal Credentials*\n\n"
                                                            . "Hello *" . $name . "*,\n\n"
                                                            . "Your student portal account has been activated:\n"
                                                            . "🌐 *Portal URL:* " . route('login.student') . "\n"
                                                            . "🆔 *Username / ID:* " . $loginIdentifier . "\n"
                                                            . "🔑 *Password:* " . ($user->raw_password ?? '') . "\n\n"
                                                            . "📚 *Course:* " . $courseName . "\n\n"
                                                            . "Log in to track your attendance, fees, and assignments.";
                                                    $waPhone = $student->phone;
                                                } else {
                                                    $waText = "💻 *Portal Login Credentials*\n\n"
                                                            . "Hello " . $user->name . ",\n\n"
                                                            . "Your portal account has been generated:\n"
                                                            . "🌐 *Login URL:* " . route('login') . "\n"
                                                            . "🆔 *Username:* " . $loginIdentifier . "\n"
                                                            . "🔑 *Password:* " . ($user->raw_password ?? '') . "\n\n"
                                                            . "Please keep these credentials secure.";
                                                    $waPhone = $user->employee?->phone ?? '';
                                                }
                                                $waPhoneClean = preg_replace('/[^0-9]/', '', $waPhone);
                                                if (strlen($waPhoneClean) === 10) {
                                                    $waPhoneClean = '91' . $waPhoneClean;
                                                }
                                                $waUrl = "https://wa.me/" . $waPhoneClean . "?text=" . urlencode($waText);
                                            @endphp

                                            <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-success text-white px-2 py-1 d-inline-flex align-items-center" title="Share via WhatsApp">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>

                                            <button type="button" class="btn btn-sm btn-light border px-2 py-1" onclick="copyToClipboard('{{ addslashes($waText) }}', 'Full Credentials')" title="Copy Full Credentials">
                                                <i class="fas fa-copy text-primary"></i>
                                            </button>

                                            <a href="{{ route('credentials.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Credential">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('credentials.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this credential? This will also remove login access.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Credential">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3 text-muted opacity-50"></i>
                                        <p class="mb-0 fw-semibold">No credentials found matching your criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-wrapper mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Script to simulate dynamic lazy loading and copy to clipboard functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const skeleton = document.getElementById('page-skeleton');
            const content = document.getElementById('page-content');
            
            setTimeout(() => {
                if (skeleton) skeleton.classList.add('fade-out');
                if (content) content.style.opacity = '1';
            }, 600);
        });

        function copyToClipboard(text, label) {
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const toastEl = document.getElementById('copyToast');
                const toastMsg = document.getElementById('toastMessage');
                if (toastMsg) toastMsg.innerText = label + ' copied to clipboard!';
                if (toastEl && typeof bootstrap !== 'undefined') {
                    const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
                    toast.show();
                } else {
                    alert(label + ' copied to clipboard!');
                }
            }).catch(err => {
                console.error('Copy failed', err);
            });
        }
    </script>
@endsection

