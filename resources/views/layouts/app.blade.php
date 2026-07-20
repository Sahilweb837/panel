<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Fees Manager')</title>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('fees-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.dataset.theme = savedTheme || (prefersDark ? 'dark' : 'light');
            
            const savedColor = localStorage.getItem('fees-primary-color');
            if (savedColor) {
                try {
                    const colors = JSON.parse(savedColor);
                    document.documentElement.style.setProperty('--first-color', colors.primary);
                    document.documentElement.style.setProperty('--first-color-dark', colors.dark);
                    document.documentElement.style.setProperty('--first-color-light', colors.light);
                    document.documentElement.style.setProperty('--input-focus', colors.focus);
                } catch(e) {
                    console.error('Error parsing theme colors', e);
                }
            }
        })();

        window.applyPrimaryColor = function(primary, dark, light, focus) {
            document.documentElement.style.setProperty('--first-color', primary);
            document.documentElement.style.setProperty('--first-color-dark', dark);
            document.documentElement.style.setProperty('--first-color-light', light);
            document.documentElement.style.setProperty('--input-focus', focus);
            localStorage.setItem('fees-primary-color', JSON.stringify({ primary, dark, light, focus }));
            window.dispatchEvent(new Event('theme-color-changed'));
        };

        window.resetTheme = function() {
            localStorage.removeItem('fees-theme');
            localStorage.removeItem('fees-primary-color');
            
            document.documentElement.style.removeProperty('--first-color');
            document.documentElement.style.removeProperty('--first-color-dark');
            document.documentElement.style.removeProperty('--first-color-light');
            document.documentElement.style.removeProperty('--input-focus');
            
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const targetTheme = prefersDark ? 'dark' : 'light';
            document.documentElement.dataset.theme = targetTheme;
            
            document.querySelectorAll('[data-theme-toggle]').forEach(t => {
                const n = t.querySelector('i');
                if(n) n.className = targetTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            });
            
            window.dispatchEvent(new Event('theme-color-changed'));
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Theme Reset!',
                    text: 'The dashboard theme has been reset to defaults.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    background: document.documentElement.dataset.theme === 'dark' ? '#1e1714' : '#ffffff',
                    color: document.documentElement.dataset.theme === 'dark' ? '#f5eae4' : '#1c1816'
                });
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css'])
    <style>
        /* Premium Dashboard Sidebar & Logo Enhancements */
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, rgba(0,0,0,0.01) 100%);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
            font-family: 'Poppins', sans-serif;
        }
        
        html[data-theme="dark"] .sidebar {
            background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
            border-right: 1px solid #1e293b;
            box-shadow: 4px 0 24px rgba(0,0,0,0.2);
        }

        .brand-mark.html-logo, .brand-mark-sm.html-logo {
            background: linear-gradient(135deg, rgba(255, 85, 50, 0.1), rgba(255, 85, 50, 0.2));
            color: var(--first-color, #ff5532);
            border: 1px solid rgba(255, 85, 50, 0.3);
            box-shadow: 0 4px 12px rgba(255, 85, 50, 0.15);
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .brand-mark.html-logo:hover, .brand-mark-sm.html-logo:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(255, 85, 50, 0.25);
        }
        
        .brand h1, .brand-title-sm {
            font-weight: 700 !important;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, var(--text), var(--muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: 'Poppins', sans-serif;
        }
        
        .nav-link {
            border-radius: 10px;
            margin-bottom: 4px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
        }
        
        .nav-link:hover {
            transform: translateX(4px);
        }

        .nav-link.active {
            background: linear-gradient(90deg, rgba(255, 85, 50, 0.12), transparent);
            border-left: 3px solid var(--first-color);
        }
    </style>
</head>
<body class="app-shell">
    <!-- Page Loader -->
    <div id="global-page-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--main-bg, #ffffff); z-index: 9999; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.4s ease;">
        <div class="spinner-border" style="color: var(--first-color, #ff5532); width: 3rem; height: 3rem; margin-bottom: 1rem;" role="status"></div>
        <div style="color: var(--text-color, #333); font-weight: 600; font-family: 'Poppins', sans-serif;">Loading Dashboard...</div>
    </div>
    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 400);
            }
        });
    </script>

    <!-- Sidebar Overlay Backdrop for Mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Mobile Top Navigation Bar -->
    <header class="mobile-navbar d-lg-none d-flex align-items-center justify-content-between p-3 border-bottom" style="background: var(--sidebar-bg); z-index: 1000;">
        <div class="d-flex align-items-center gap-3">
            <button type="button" class="btn btn-link p-0" id="sidebar-toggle-btn" style="color: var(--text);">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none" style="color: inherit;">
                <img src="https://www.netcoder.in/images/logo.png" alt="Netcoder Logo" style="height: 32px; width: auto; object-fit: contain; max-width: 200px;">
            </a>
        </div>
        <button type="button" class="theme-toggle me-2 px-3 py-1" style="height: auto; border-radius: 20px; font-size: 0.8rem;" data-theme-toggle title="Toggle Dark/Light Mode">
            <span class="theme-icon-wrapper" style="width: 14px; height: 14px; font-size: 0.8rem;"><i class="fas fa-moon"></i></span>
            <span class="btn-text d-none d-sm-inline">Theme</span>
        </button>
    </header>

    <aside class="sidebar">
        <!-- Close button for Mobile sidebar -->
        <button type="button" class="btn-close-custom d-lg-none" id="sidebar-close-btn" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="brand px-4 py-4" style="margin-bottom: 1rem; display: flex; align-items: center;">
            <a href="{{ url('/') }}" class="text-decoration-none w-100 text-center" style="color: inherit;">
                <img src="https://www.netcoder.in/images/logo.png" alt="Netcoder Logo" style="height: 48px; width: auto; object-fit: contain; max-width: 100%;">
            </a>
        </div>

        <nav class="nav-menu accordion" id="sidebarAccordion">
            <a href="{{ route('dashboard') }}" class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            
            @php
                $roleSlug = $currentUser->role?->slug ?? null;
                $isSuperOrRoot = in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin']);
                $access = $currentUser->access ?? [];
            @endphp

            {{-- STUDENT MANAGEMENT --}}
            @if($isSuperOrRoot || in_array('students', $access) || in_array('attendances', $access))
            <div class="nav-item">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#studentMenu" role="button" aria-expanded="false" aria-controls="studentMenu">
                    <div>
                        <i class="fas fa-user-graduate me-2"></i>
                        <span>Student Management</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('students.*', 'attendances.*', 'biometric.*') ? 'show' : '' }}" id="studentMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav flex-column ms-3 py-1" style="border-left: 2px solid var(--border-sutil);">
                        @if($isSuperOrRoot || in_array('students', $access))
                        <li><a href="{{ route('students.index') }}" class="nav-link py-2 {{ request()->routeIs('students.*') ? 'active' : '' }}"><span>Students</span></a></li>
                        @endif
                        @if($isSuperOrRoot || in_array('attendances', $access))
                        <li><a href="{{ route('attendances.index') }}" class="nav-link py-2 {{ request()->routeIs('attendances.index') ? 'active' : '' }}"><span>Student Attendance</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{-- LIVE FEED & BIOMETRIC --}}
            @if($isSuperOrRoot || in_array('attendances', $access))
            <div class="nav-item">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#attendanceMenu" role="button" aria-expanded="false" aria-controls="attendanceMenu">
                    <div>
                        <i class="fas fa-fingerprint me-2"></i>
                        <span>Live Feed & Biometric</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('attendances.live', 'biometric.*') ? 'show' : '' }}" id="attendanceMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav flex-column ms-3 py-1" style="border-left: 2px solid var(--border-sutil);">
                        <li><a href="{{ route('attendances.live') }}" class="nav-link py-2 {{ request()->routeIs('attendances.live') ? 'active' : '' }}"><span>Live Feed Viewer</span></a></li>
                        <li><a href="{{ route('biometric.index') }}" class="nav-link py-2 {{ request()->routeIs('biometric.*') ? 'active' : '' }}"><span>ZKTeco Device Sync</span></a></li>
                    </ul>
                </div>
            </div>
            @endif

            {{-- ADMIN & STAFF --}}
            @if($isSuperOrRoot || in_array('employees', $access) || in_array('employee-attendances', $access) || in_array('salary-slips', $access))
            <div class="nav-item">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#adminStaffMenu" role="button" aria-expanded="false" aria-controls="adminStaffMenu">
                    <div>
                        <i class="fas fa-users-cog me-2"></i>
                        <span>Admin & Staff</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('sub-admins.*', 'employees.*', 'employee-attendances.*', 'salary_slips.*') ? 'show' : '' }}" id="adminStaffMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav flex-column ms-3 py-1" style="border-left: 2px solid var(--border-sutil);">
                        @if($isSuperOrRoot)
                        <li><a href="{{ route('sub-admins.index') }}" class="nav-link py-2 {{ request()->routeIs('sub-admins.*') ? 'active' : '' }}"><span>Sub-Admins</span></a></li>
                        @endif
                        @if($isSuperOrRoot || in_array('employees', $access))
                        <li><a href="{{ route('employees.index') }}" class="nav-link py-2 {{ request()->routeIs('employees.*') ? 'active' : '' }}"><span>Staff Members</span></a></li>
                        @endif
                        @if($isSuperOrRoot || in_array('employee-attendances', $access))
                        <li><a href="{{ route('employee-attendances.index') }}" class="nav-link py-2 {{ request()->routeIs('employee-attendances.*') ? 'active' : '' }}"><span>Staff Attendance</span></a></li>
                        @endif
                        @if($isSuperOrRoot || in_array('salary-slips', $access))
                        <li><a href="{{ route('salary_slips.index') }}" class="nav-link py-2 {{ request()->routeIs('salary_slips.*') ? 'active' : '' }}"><span>Salary Slips</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{-- COURSES & TRAINING --}}
            @if($isSuperOrRoot || in_array('courses', $access) || in_array('trainings', $access))
            <div class="nav-item">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#courseMenu" role="button" aria-expanded="false" aria-controls="courseMenu">
                    <div>
                        <i class="fas fa-book-open me-2"></i>
                        <span>Courses & Training</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('courses.*', 'training_courses.*', 'trainings.*') ? 'show' : '' }}" id="courseMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav flex-column ms-3 py-1" style="border-left: 2px solid var(--border-sutil);">
                        @if($isSuperOrRoot || in_array('courses', $access))
                        <li><a href="{{ route('courses.index') }}" class="nav-link py-2 {{ request()->routeIs('courses.*') ? 'active' : '' }}"><span>Master Courses</span></a></li>
                        <li><a href="{{ route('training_courses.index') }}" class="nav-link py-2 {{ request()->routeIs('training_courses.*') ? 'active' : '' }}"><span>Training Courses</span></a></li>
                        @endif
                        @if($isSuperOrRoot || in_array('trainings', $access))
                        <li><a href="{{ route('trainings.index') }}" class="nav-link py-2 {{ request()->routeIs('trainings.*') ? 'active' : '' }}"><span>Training & Internship</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{-- FEES & ACCOUNTS --}}
            @if($isSuperOrRoot || in_array('fee-invoices', $access) || in_array('expenses', $access))
            <div class="nav-item">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#feesMenu" role="button" aria-expanded="false" aria-controls="feesMenu">
                    <div>
                        <i class="fas fa-wallet me-2"></i>
                        <span>Fees & Accounts</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('fee_invoices.*', 'expenses.*') ? 'show' : '' }}" id="feesMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav flex-column ms-3 py-1" style="border-left: 2px solid var(--border-sutil);">
                        @if($isSuperOrRoot || in_array('fee-invoices', $access))
                        <li><a href="{{ route('fee_invoices.index') }}" class="nav-link py-2 {{ request()->routeIs('fee_invoices.*') ? 'active' : '' }}"><span>Fee Receipts</span></a></li>
                        @endif
                        @if($isSuperOrRoot || in_array('expenses', $access))
                        <li><a href="{{ route('expenses.index') }}" class="nav-link py-2 {{ request()->routeIs('expenses.*') ? 'active' : '' }}"><span>Expenses</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{-- CLIENTS & TASKS --}}
            @if($isSuperOrRoot || in_array('clients', $access) || in_array('client-invoices', $access))
            <div class="nav-item">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#clientMenu" role="button" aria-expanded="false" aria-controls="clientMenu">
                    <div>
                        <i class="fas fa-briefcase me-2"></i>
                        <span>Clients & Tasks</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('clients.*', 'client_invoices.*', 'reports.*', 'tasks.*', 'daily-updates.*') ? 'show' : '' }}" id="clientMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav flex-column ms-3 py-1" style="border-left: 2px solid var(--border-sutil);">
                        @if($isSuperOrRoot || in_array('clients', $access))
                        <li><a href="{{ route('clients.index') }}" class="nav-link py-2 {{ request()->routeIs('clients.*') ? 'active' : '' }}"><span>Clients</span></a></li>
                        @endif
                        @if($isSuperOrRoot || in_array('client-invoices', $access))
                        <li><a href="{{ route('client_invoices.index') }}" class="nav-link py-2 {{ request()->routeIs('client_invoices.*') ? 'active' : '' }}"><span>Client Invoices</span></a></li>
                        @endif
                        @if($isSuperOrRoot)
                        <li><a href="{{ route('reports.index') }}" class="nav-link py-2 {{ request()->routeIs('reports.*') ? 'active' : '' }}"><span>Reports</span></a></li>
                        <li><a href="{{ route('tasks.index') }}" class="nav-link py-2 {{ request()->routeIs('tasks.*') ? 'active' : '' }}"><span>Tasks Assigned</span></a></li>
                        <li><a href="{{ route('daily-updates.index') }}" class="nav-link py-2 {{ request()->routeIs('daily-updates.*') ? 'active' : '' }}"><span>Daily Work Logs</span></a></li>
                        @endif
                    </ul>
                </div>
            </div>
            @endif

            {{-- SETTINGS & TOOLS --}}
            <div class="nav-item mt-auto pt-4">
                <a class="nav-link collapsed d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#settingsMenu" role="button" aria-expanded="false" aria-controls="settingsMenu">
                    <div>
                        <i class="fas fa-cog me-2"></i>
                        <span>Settings & Tools</span>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
                </a>
                <div class="collapse {{ request()->routeIs('settings.*', 'backups.*', 'credentials.*') ? 'show' : '' }}" id="settingsMenu" data-bs-parent="#sidebarAccordion">
                    <ul class="nav flex-column ms-3 py-1" style="border-left: 2px solid var(--border-sutil);">
                        @if($isSuperOrRoot)
                        <li><a href="{{ route('settings.index') }}" class="nav-link py-2 {{ request()->routeIs('settings.*') ? 'active' : '' }}"><i class="fas fa-sliders-h me-2" style="width:16px;"></i><span>System Settings</span></a></li>
                        <li><a href="{{ route('messages.index') }}" class="nav-link py-2 {{ request()->routeIs('messages.*') ? 'active' : '' }}"><i class="fas fa-paper-plane me-2" style="width:16px;"></i><span>Message Suite</span></a></li>
                        <li><a href="{{ route('credentials.index') }}" class="nav-link py-2 {{ request()->routeIs('credentials.*') ? 'active' : '' }}"><i class="fas fa-key me-2" style="width:16px;"></i><span>Credentials</span></a></li>
                        <li><a href="{{ route('backups.index') }}" class="nav-link py-2 {{ request()->routeIs('backups.*') ? 'active' : '' }}"><i class="fas fa-database me-2" style="width:16px;"></i><span>System Backups</span></a></li>
                        <li>
                            <form action="{{ route('clear-cache') }}" method="POST" class="d-inline w-100" style="display:block;">
                                @csrf
                                <a href="#" class="nav-link py-2 text-warning" onclick="this.closest('form').submit(); return false;">
                                    <i class="fas fa-broom me-2" style="width:16px;"></i><span>Clear Cache</span>
                                </a>
                            </form>
                        </li>
                        @else
                        <li><a href="{{ route('messages.index') }}" class="nav-link py-2 {{ request()->routeIs('messages.*') ? 'active' : '' }}"><i class="fas fa-paper-plane me-2" style="width:16px;"></i><span>Message Suite</span></a></li>
                        @endif
                        <li><a href="#" class="nav-link py-2" onclick="document.querySelector('[data-theme-toggle]').click(); return false;"><i class="fas fa-moon me-2" style="width:16px;"></i><span>Toggle Theme</span></a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline w-100" style="display:block;">
                                @csrf
                                <a href="#" class="nav-link py-2 text-danger" onclick="this.closest('form').submit(); return false;">
                                    <i class="fas fa-power-off me-2" style="width:16px;"></i><span>Logout</span>
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

        <div class="sidebar-footer" style="padding: 16px; background: rgba(255, 85, 50, 0.04); border-radius: 14px; margin: 15px 12px; border: 1px solid rgba(255, 85, 50, 0.15);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge" style="background: rgba(255, 85, 50, 0.15); color: var(--first-color); font-weight: 700; font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; letter-spacing: 0.5px;">
                    <i class="fas fa-user-shield me-1"></i>{{ session('user_role', 'Super Admin') }}
                </span>
                <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>Online</span>
            </div>
            <div style="font-size: 0.72rem; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Logged in as</div>
            <strong style="font-size: 1rem; color: var(--text); font-weight: 700; display: block; margin-bottom: 8px;">{{ session('user_name', 'Super Admin') }}</strong>
            
            @php
                $currentUser = null;
                if (session('user_id')) {
                    $currentUser = \App\Models\User::find(session('user_id'));
                }
            @endphp
            @if($currentUser && $currentUser->phone_number)
                <small class="d-block mb-2 text-muted" style="font-size: 0.78rem;">
                    <i class="fas fa-phone me-1"></i>{{ $currentUser->phone_number }}
                </small>
            @endif
            <form action="{{ route('logout') }}" method="POST" class="logout-form mt-2">
                @csrf
                <button type="submit" class="button button-secondary w-100 py-2" style="font-size: 0.85rem; border-radius: 8px;">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">@yield('page-title', 'Management')</h2>
                <p class="page-subtitle mb-0 d-none d-md-block">Manage courses, students, fees, attendance, salaries and expenses.</p>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                @php
                    $navUnreadCount = 0;
                    if (session('user_id')) {
                        $navUnreadCount = \App\Models\Message::forUser(session('user_id'), session('user_role_slug'))->where('is_read', false)->count();
                    }
                @endphp
                <a href="{{ route('messages.index') }}" class="btn position-relative" title="Message & Notice Center" style="background: var(--surface-soft, #f8f9fa); border: 1px solid var(--border, #e9ecef); border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; color: var(--text-color, #333);">
                    <i class="fas fa-envelope"></i>
                    @if($navUnreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            {{ $navUnreadCount }}
                        </span>
                    @endif
                </a>
                <div class="search-bar position-relative d-none d-lg-block">
                    <i class="fas fa-search position-absolute text-muted" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                    <input type="text" class="form-control rounded-pill ps-5" placeholder="Search dashboard..." style="width: 260px; background-color: var(--surface-soft, #f8f9fa); border-color: var(--border, #e9ecef); color: var(--text-color, #333);">
                </div>
                <button type="button" class="theme-toggle" data-theme-toggle title="Toggle Dark/Light Mode">
                    <span class="theme-icon-wrapper"><i class="fas fa-moon"></i></span>
                </button>
            </div>
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

        </section>
    </main>

    <!-- Global Floating Toast Notification Container -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])

    <script>
        // Global Toast Notification Helper
        window.showToast = function(title, message, type = 'info') {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const toastId = 'toast-' + Date.now();
            const bgClass = type === 'success' ? 'bg-success text-white' : (type === 'danger' ? 'bg-danger text-white' : (type === 'warning' ? 'bg-warning text-dark' : 'bg-dark text-white'));
            const icon = type === 'success' ? 'fa-check-circle' : (type === 'danger' ? 'fa-exclamation-circle' : (type === 'warning' ? 'fa-exclamation-triangle' : 'fa-bell'));

            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; font-family: var(--font-sans);">
                    <div class="d-flex">
                        <div class="toast-body d-flex align-items-center gap-2">
                            <i class="fas ${icon} fa-lg"></i>
                            <div>
                                <strong>${title}</strong>
                                <div style="font-size: 0.82rem; opacity: 0.9;">${message}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', toastHtml);

            const toastEl = document.getElementById(toastId);
            const bsToast = new bootstrap.Toast(toastEl, { delay: 4500 });
            bsToast.show();
        };

        // Staff Online Heartbeat & Toast Notification Listener
        document.addEventListener('DOMContentLoaded', () => {
            let previousOnlineIds = new Set();
            let isFirstPing = true;

            function sendHeartbeatPing() {
                fetch("{{ route('staff.ping') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok || !res.headers.get('content-type')?.includes('application/json')) {
                        return null;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success && data.online_staff) {
                        const currentOnlineIds = new Set(data.online_staff.map(s => s.id));

                        if (!isFirstPing) {
                            // Detect newly online users
                            data.online_staff.forEach(s => {
                                if (!previousOnlineIds.has(s.id) && s.id !== data.current_user_id) {
                                    window.showToast('User Online', `⚡ ${s.name} (${s.role}) is now Online`, 'success');
                                }
                            });
                        }

                        previousOnlineIds = currentOnlineIds;
                        isFirstPing = false;
                    }
                })
                .catch(err => console.error('Ping error:', err));
            }

            // Initial ping + repeat every 40s
            sendHeartbeatPing();
            setInterval(sendHeartbeatPing, 40000);
        });
    </script>
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

        // Global function to replace native confirm() with SweetAlert2
        window.confirmAction = function(event, message) {
            event.preventDefault();
            const form = event.target.closest('form');
            if(!form) return false;

            Swal.fire({
                title: 'Are you sure?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--primary, #ff5532)',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, proceed!',
                background: document.documentElement.dataset.theme === 'dark' ? '#1e1714' : '#ffffff',
                color: document.documentElement.dataset.theme === 'dark' ? '#f5eae4' : '#1c1816'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        };
    </script>

    <!-- Chatbot Floating Button with Notification Pulse -->
    <div id="chatbot-trigger" class="chatbot-fab">
        <i class="fas fa-robot fa-lg"></i>
        <span class="chatbot-fab-pulse"></span>
    </div>

    <!-- Chatbot Glassmorphic Panel/Drawer -->
    <div id="chatbot-drawer" class="chatbot-drawer">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-left">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                    <span class="chatbot-status-dot"></span>
                </div>
                <div>
                    <h5 class="chatbot-title">ERP Assistant</h5>
                    <small class="chatbot-subtitle"><span class="chatbot-online-dot">●</span> Online — Netcoder ERP</small>
                </div>
            </div>
            <div class="chatbot-header-actions">
                <button type="button" id="clear-chat" class="chatbot-header-btn" title="Clear Chat">
                    <i class="fas fa-broom"></i>
                </button>
                <button type="button" id="close-chatbot" class="chatbot-header-btn" title="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Message Body -->
        <div id="chat-messages" class="chatbot-messages">
            <!-- Welcome message -->
            <div class="chat-msg chat-msg-bot">
                <div class="chat-msg-avatar"><i class="fas fa-robot"></i></div>
                <div class="chat-msg-bubble chat-msg-bubble-bot">
                    <p>👋 Hello, <strong>{{ session('user_name', 'there') }}</strong>! I'm your ERP assistant.</p>
                    <p>I can look up <strong>student fees</strong>, <strong>recent admissions</strong>, <strong>staff tasks</strong>, <strong>attendance</strong>, run <strong>system diagnostics</strong>, and more!</p>
                    <p>Click a quick action below or type a question:</p>
                </div>
            </div>
            
            <!-- Quick Options Grid -->
            <div class="quick-options-grid" id="quick-options">
                <button type="button" onclick="sendQuickQuery('Show pending fees')" class="quick-option-btn">
                    <i class="fas fa-money-bill-wave"></i> Pending Fees
                </button>
                <button type="button" onclick="sendQuickQuery('Show new students')" class="quick-option-btn">
                    <i class="fas fa-user-graduate"></i> New Students
                </button>
                <button type="button" onclick="sendQuickQuery('Show notifications')" class="quick-option-btn">
                    <i class="fas fa-bell"></i> Notifications
                </button>
                <button type="button" onclick="sendQuickQuery('Task summary')" class="quick-option-btn">
                    <i class="fas fa-tasks"></i> Task Summary
                </button>
                <button type="button" onclick="sendQuickQuery('Check biometric status')" class="quick-option-btn">
                    <i class="fas fa-fingerprint"></i> Device Status
                </button>
                <button type="button" onclick="sendQuickQuery('Check system health')" class="quick-option-btn">
                    <i class="fas fa-heartbeat"></i> System Health
                </button>
                <button type="button" onclick="sendQuickQuery('Revenue report')" class="quick-option-btn">
                    <i class="fas fa-chart-line"></i> Revenue
                </button>
                <button type="button" onclick="sendQuickQuery('Show help')" class="quick-option-btn">
                    <i class="fas fa-question-circle"></i> All Commands
                </button>
            </div>
        </div>

        <!-- Chat Input Footer -->
        <div class="chatbot-footer">
            <form id="chat-input-form" onsubmit="handleChatSubmit(event)" class="chatbot-input-form">
                <input type="text" id="chat-user-input" placeholder="Ask me anything about your ERP..." required autocomplete="off" class="chatbot-input">
                <button type="submit" class="chatbot-send-btn" title="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
            <div class="chatbot-footer-hint">
                <i class="fas fa-lightbulb"></i> Try: "pending fees", "new students", "staff overview"
            </div>
        </div>
    </div>

    <style>
        /* ---- Chatbot FAB (Floating Action Button) ---- */
        .chatbot-fab {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--first-color), var(--first-color-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(255, 85, 50, 0.35);
            z-index: 9999;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .chatbot-fab:hover {
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 12px 40px rgba(255, 85, 50, 0.45);
        }
        .chatbot-fab-pulse {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--first-color);
            opacity: 0.4;
            animation: fabPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            pointer-events: none;
        }
        @keyframes fabPulse {
            0%, 100% { transform: scale(1); opacity: 0.4; }
            50% { transform: scale(1.3); opacity: 0; }
        }

        /* ---- Chatbot Drawer ---- */
        .chatbot-drawer {
            position: fixed;
            top: 0;
            right: -440px;
            width: 420px;
            max-width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-left: 1px solid rgba(255, 255, 255, 0.3);
            z-index: 10000;
            box-shadow: -12px 0 48px rgba(0, 0, 0, 0.08);
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }
        .chatbot-drawer.open {
            right: 0;
        }

        /* Dark mode drawer */
        [data-theme="dark"] .chatbot-drawer {
            background: rgba(28, 22, 18, 0.92);
            border-left-color: rgba(255, 255, 255, 0.06);
            box-shadow: -12px 0 48px rgba(0, 0, 0, 0.3);
        }

        /* ---- Header ---- */
        .chatbot-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.4);
            flex-shrink: 0;
        }
        [data-theme="dark"] .chatbot-header {
            background: rgba(40, 30, 25, 0.6);
        }
        .chatbot-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .chatbot-avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--first-color-light), rgba(255, 85, 50, 0.15));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--first-color);
            font-size: 1.1rem;
            position: relative;
        }
        .chatbot-status-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid var(--surface);
        }
        .chatbot-title {
            margin: 0;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text);
        }
        .chatbot-subtitle {
            color: var(--muted);
            font-size: 0.78rem;
        }
        .chatbot-online-dot {
            color: #10b981;
            font-size: 0.7rem;
        }
        .chatbot-header-actions {
            display: flex;
            gap: 4px;
        }
        .chatbot-header-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        .chatbot-header-btn:hover {
            background: var(--surface-soft);
            color: var(--first-color);
        }

        /* ---- Messages ---- */
        .chatbot-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 14px;
            scroll-behavior: smooth;
        }
        .chatbot-messages::-webkit-scrollbar {
            width: 5px;
        }
        .chatbot-messages::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }

        /* Chat message layout */
        .chat-msg {
            display: flex;
            gap: 10px;
            animation: msgSlideIn 0.3s ease-out;
        }
        .chat-msg-user {
            flex-direction: row-reverse;
        }
        .chat-msg-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--first-color-light);
            color: var(--first-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .chat-msg-user .chat-msg-avatar {
            background: var(--first-color);
            color: #fff;
        }
        .chat-msg-bubble {
            max-width: 82%;
            padding: 12px 16px;
            font-size: 0.88rem;
            line-height: 1.55;
            word-break: break-word;
        }
        .chat-msg-bubble p {
            margin: 0 0 6px 0;
        }
        .chat-msg-bubble p:last-child {
            margin-bottom: 0;
        }
        .chat-msg-bubble-bot {
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 4px 16px 16px 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .chat-msg-bubble-user {
            background: linear-gradient(135deg, var(--first-color), var(--first-color-dark));
            color: #fff;
            border-radius: 16px 4px 16px 16px;
            box-shadow: 0 4px 14px rgba(255, 85, 50, 0.2);
        }

        @keyframes msgSlideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ---- Quick Options ---- */
        .quick-options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 4px 0;
        }
        .quick-option-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: left;
        }
        .quick-option-btn i {
            color: var(--first-color);
            font-size: 0.85rem;
            width: 16px;
            text-align: center;
        }
        .quick-option-btn:hover {
            border-color: var(--first-color);
            background: var(--first-color-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 85, 50, 0.1);
        }

        /* ---- Typing Indicator ---- */
        .typing-indicator {
            display: flex;
            gap: 10px;
            animation: msgSlideIn 0.3s ease-out;
        }
        .typing-dots {
            display: flex;
            gap: 4px;
            align-items: center;
            padding: 14px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 4px 16px 16px 16px;
        }
        .typing-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--muted);
            animation: typingBounce 1.4s ease-in-out infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-6px); }
        }

        /* ---- Footer / Input ---- */
        .chatbot-footer {
            padding: 14px 20px 12px;
            border-top: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.4);
            flex-shrink: 0;
        }
        [data-theme="dark"] .chatbot-footer {
            background: rgba(40, 30, 25, 0.6);
        }
        .chatbot-input-form {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .chatbot-input {
            flex: 1;
            padding: 11px 16px;
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            background: var(--surface);
            font-size: 0.88rem;
            color: var(--text);
            outline: none;
            transition: all 0.2s;
        }
        .chatbot-input::placeholder {
            color: var(--muted);
        }
        .chatbot-input:focus {
            border-color: var(--first-color);
            box-shadow: 0 0 0 3px var(--input-focus);
        }
        .chatbot-send-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--first-color), var(--first-color-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .chatbot-send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 14px rgba(255, 85, 50, 0.3);
        }
        .chatbot-footer-hint {
            text-align: center;
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 8px;
            opacity: 0.7;
        }
        .chatbot-footer-hint i {
            color: #f59e0b;
        }

        /* Mobile responsive */
        @media (max-width: 480px) {
            .chatbot-drawer {
                width: 100vw;
                right: -100vw;
            }
            .chatbot-fab {
                bottom: 18px;
                right: 18px;
                width: 52px;
                height: 52px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const trigger = document.getElementById('chatbot-trigger');
            const drawer = document.getElementById('chatbot-drawer');
            const closeBtn = document.getElementById('close-chatbot');
            const clearBtn = document.getElementById('clear-chat');
            
            if (trigger && drawer && closeBtn) {
                trigger.addEventListener('click', () => {
                    drawer.classList.toggle('open');
                    if (drawer.classList.contains('open')) {
                        trigger.style.transform = 'scale(0)';
                        trigger.style.opacity = '0';
                        document.getElementById('chat-user-input')?.focus();
                    }
                });
                closeBtn.addEventListener('click', () => {
                    drawer.classList.remove('open');
                    setTimeout(() => {
                        trigger.style.transform = 'scale(1)';
                        trigger.style.opacity = '1';
                    }, 200);
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    resetChat();
                });
            }

            // Close on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && drawer?.classList.contains('open')) {
                    closeBtn?.click();
                }
            });
        });

        // The correct chatbot API URL from Laravel
        const CHATBOT_API_URL = "{{ route('api.chatbot.query') }}";

        function sendQuickQuery(text) {
            appendMessage(text, 'user');
            fetchQuery(text);
        }

        function handleChatSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('chat-user-input');
            const text = input.value.trim();
            if (!text) return;
            
            appendMessage(text, 'user');
            input.value = '';
            fetchQuery(text);
        }

        function appendMessage(text, type) {
            const chatMessages = document.getElementById('chat-messages');
            
            // Remove quick options after first interaction
            const options = document.getElementById('quick-options');
            if (options) options.remove();

            const msgWrapper = document.createElement('div');
            msgWrapper.className = `chat-msg chat-msg-${type}`;

            const avatar = document.createElement('div');
            avatar.className = 'chat-msg-avatar';
            avatar.innerHTML = type === 'user' ? '<i class="fas fa-user"></i>' : '<i class="fas fa-robot"></i>';

            const bubble = document.createElement('div');
            bubble.className = `chat-msg-bubble chat-msg-bubble-${type}`;

            // Format text: markdown bold, line breaks, bullet points
            let formatted = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/_(.*?)_/g, '<em>$1</em>')
                .replace(/\n/g, '<br>');

            bubble.innerHTML = `<p>${formatted}</p>`;

            msgWrapper.appendChild(avatar);
            msgWrapper.appendChild(bubble);
            chatMessages.appendChild(msgWrapper);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTypingIndicator() {
            const chatMessages = document.getElementById('chat-messages');
            const typing = document.createElement('div');
            typing.id = 'typing-indicator';
            typing.className = 'typing-indicator';
            typing.innerHTML = `
                <div class="chat-msg-avatar"><i class="fas fa-robot"></i></div>
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
            `;
            chatMessages.appendChild(typing);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function removeTypingIndicator() {
            const ind = document.getElementById('typing-indicator');
            if (ind) ind.remove();
        }

        function fetchQuery(query) {
            showTypingIndicator();

            fetch(`${CHATBOT_API_URL}?query=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    removeTypingIndicator();
                    appendMessage(data.response || 'No response received.', 'bot');
                })
                .catch(err => {
                    removeTypingIndicator();
                    console.error('Chatbot error:', err);
                    appendMessage(
                        "⚠️ **Connection Error**\n\nCouldn't reach the assistant service.\n\n**Possible fixes:**\n• Make sure your web server (Apache/XAMPP) is running\n• Check that the database is online\n• Try refreshing the page\n\n_Error: " + err.message + "_",
                        'bot'
                    );
                });
        }

        function resetChat() {
            const chatMessages = document.getElementById('chat-messages');
            chatMessages.innerHTML = '';

            // Re-add welcome message
            const welcome = document.createElement('div');
            welcome.className = 'chat-msg chat-msg-bot';
            welcome.innerHTML = `
                <div class="chat-msg-avatar"><i class="fas fa-robot"></i></div>
                <div class="chat-msg-bubble chat-msg-bubble-bot">
                    <p>👋 Chat cleared! How can I help you?</p>
                </div>
            `;
            chatMessages.appendChild(welcome);

            // Re-add quick options
            const optionsHtml = `
                <div class="quick-options-grid" id="quick-options">
                    <button type="button" onclick="sendQuickQuery('Show pending fees')" class="quick-option-btn">
                        <i class="fas fa-money-bill-wave"></i> Pending Fees
                    </button>
                    <button type="button" onclick="sendQuickQuery('Show new students')" class="quick-option-btn">
                        <i class="fas fa-user-graduate"></i> New Students
                    </button>
                    <button type="button" onclick="sendQuickQuery('Show notifications')" class="quick-option-btn">
                        <i class="fas fa-bell"></i> Notifications
                    </button>
                    <button type="button" onclick="sendQuickQuery('Task summary')" class="quick-option-btn">
                        <i class="fas fa-tasks"></i> Task Summary
                    </button>
                    <button type="button" onclick="sendQuickQuery('Check biometric status')" class="quick-option-btn">
                        <i class="fas fa-fingerprint"></i> Device Status
                    </button>
                    <button type="button" onclick="sendQuickQuery('Check system health')" class="quick-option-btn">
                        <i class="fas fa-heartbeat"></i> System Health
                    </button>
                    <button type="button" onclick="sendQuickQuery('Revenue report')" class="quick-option-btn">
                        <i class="fas fa-chart-line"></i> Revenue
                    </button>
                    <button type="button" onclick="sendQuickQuery('Show help')" class="quick-option-btn">
                        <i class="fas fa-question-circle"></i> All Commands
                    </button>
                </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', optionsHtml);
        }
    </script>
    @if(session('new_user_credentials'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const creds = @json(session('new_user_credentials'));
            
            Swal.fire({
                title: 'User Created Successfully!',
                html: `
                    <div class="text-start mt-3">
                        <p class="mb-2">A new <strong>${creds.type}</strong> account has been automatically generated.</p>
                        <div class="bg-light p-3 rounded border">
                            <p class="mb-1"><strong>Email:</strong> <span class="user-select-all">${creds.email}</span></p>
                            <p class="mb-1"><strong>Username:</strong> <span class="user-select-all">${creds.username}</span></p>
                            <p class="mb-0"><strong>Password:</strong> <span class="user-select-all">${creds.password}</span></p>
                        </div>
                        <p class="mt-3 text-muted small"><i class="fas fa-info-circle me-1"></i>Please share these credentials securely with the user.</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Got it!',
                confirmButtonColor: '#32D74B',
                allowOutsideClick: false
            });
        });
    </script>
    @endif
</body>
</html>

