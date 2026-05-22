<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Fees Manager')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="app-shell">
    <!-- Sidebar Overlay Backdrop for Mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Mobile Top Navigation Bar -->
    <header class="mobile-navbar d-lg-none d-flex align-items-center justify-content-between p-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-link p-0 me-2" id="sidebar-toggle-btn">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <div class="brand-mark-sm">
                <img src="{{ asset('image.png') }}" alt="Netcoder Fees" class="brand-logo-sm">
            </div>
            <div>
                <h1 class="brand-title-sm mb-0">Netcoder Fees</h1>
            </div>
        </div>
        <button type="button" class="eye-protection-btn theme-toggle me-2 px-3 py-1" style="height: auto; border-radius: 20px; font-size: 0.8rem;" data-theme-toggle title="Toggle Eye Protection Theme">
            <span class="eye-icon-wrapper" style="width: 14px; height: 14px; font-size: 0.8rem;"><i class="fas fa-eye-slash"></i></span>
            <span class="btn-text d-none d-sm-inline">Eye Protection</span>
        </button>
    </header>

    <aside class="sidebar">
        <!-- Close button for Mobile sidebar -->
        <button type="button" class="btn-close-custom d-lg-none" id="sidebar-close-btn" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="brand">
            <div class="brand-mark">
                <img src="{{ asset('image.png') }}" alt="Netcoder Fees" class="brand-logo">
            </div>
            <div>
                <h1>Netcoder Fees</h1>
                <p>Institute ERP</p>
            </div>
        </div>

        <nav class="nav-menu">
            <a href="{{ route('dashboard') }}" class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            @if(($currentUser->role?->slug ?? null) !== 'staff')
                @if(($currentUser->role?->slug ?? null) === 'superadmin')
                    <a href="{{ route('sub-admins.index') }}" class="nav-link{{ request()->routeIs('sub-admins.*') ? ' active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        <span>Sub-Admins & Staff</span>
                    </a>
                @endif
                <a href="{{ route('courses.index') }}" class="nav-link{{ request()->routeIs('courses.*') ? ' active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>
                <a href="{{ route('students.index') }}" class="nav-link{{ request()->routeIs('students.*') ? ' active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Students</span>
                </a>
                <a href="{{ route('employees.index') }}" class="nav-link{{ request()->routeIs('employees.*') ? ' active' : '' }}">
                    <i class="fas fa-person-chalkboard"></i>
                    <span>Staff</span>
                </a>
                <a href="{{ route('employee-attendances.index') }}" class="nav-link{{ request()->routeIs('employee-attendances.*') ? ' active' : '' }}">
                    <i class="fas fa-clipboard-user"></i>
                    <span>Staff Attendance</span>
                </a>
                <a href="{{ route('attendances.index') }}" class="nav-link{{ request()->routeIs('attendances.*') ? ' active' : '' }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Student Attendance</span>
                </a>
                <a href="{{ route('fee_invoices.index') }}" class="nav-link{{ request()->routeIs('fee_invoices.*') ? ' active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Fee Invoices</span>
                </a>
                <a href="{{ route('expenses.index') }}" class="nav-link{{ request()->routeIs('expenses.*') ? ' active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Expenses</span>
                </a>
                <a href="{{ route('salary_slips.index') }}" class="nav-link{{ request()->routeIs('salary_slips.*') ? ' active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Salary Slips</span>
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <p>Logged in as</p>
            <strong>{{ session('user_name', 'Guest') }}</strong>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="button button-secondary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <h2>@yield('page-title', 'Management')</h2>
                <p class="page-subtitle">Manage courses, students, fees, attendance, salaries and expenses.</p>
            </div>
            <button type="button" class="eye-protection-btn" data-theme-toggle title="Toggle Eye Protection Theme">
                <span class="eye-icon-wrapper"><i class="fas fa-eye-slash"></i></span>
                <span class="btn-text">Eye Protection</span>
            </button>
        </header>

        <section class="page-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle-btn');
            const closeBtn = document.getElementById('sidebar-close-btn');
            const overlay = document.getElementById('sidebar-overlay');

            const toggleSidebar = () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');
            };

            if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);
        });
    </script>
</body>
</html>
