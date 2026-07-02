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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            
            @php
                $roleSlug = $currentUser->role?->slug ?? null;
                $isSuperOrRoot = in_array($roleSlug, ['super-admin', 'superadmin', 'root-admin']);
                $access = $currentUser->access ?? [];
            @endphp

            @if($isSuperOrRoot)
                <a href="{{ route('sub-admins.index') }}" class="nav-link{{ request()->routeIs('sub-admins.*') ? ' active' : '' }}">
                    <i class="fas fa-user-shield"></i>
                    <span>Sub-Admins & Staff</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('courses', $access))
                <a href="{{ route('courses.index') }}" class="nav-link{{ request()->routeIs('courses.*') ? ' active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>
                <a href="{{ route('training_courses.index') }}" class="nav-link{{ request()->routeIs('training_courses.*') ? ' active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Training Courses</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('students', $access))
                <a href="{{ route('students.index') }}" class="nav-link{{ request()->routeIs('students.*') ? ' active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Students</span>
                </a>
                <a href="{{ route('credentials.index') }}" class="nav-link{{ request()->routeIs('credentials.*') ? ' active' : '' }}">
                    <i class="fas fa-id-badge"></i>
                    <span>Credentials</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('employees', $access))
                <a href="{{ route('employees.index') }}" class="nav-link{{ request()->routeIs('employees.*') ? ' active' : '' }}">
                    <i class="fas fa-person-chalkboard"></i>
                    <span>Staff</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('employee-attendances', $access))
                <a href="{{ route('employee-attendances.index') }}" class="nav-link{{ request()->routeIs('employee-attendances.*') ? ' active' : '' }}">
                    <i class="fas fa-clipboard-user"></i>
                    <span>Staff Attendance</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('attendances', $access))
                <a href="{{ route('attendances.live') }}" class="nav-link{{ request()->routeIs('attendances.live') ? ' active' : '' }}">
                    <i class="fas fa-broadcast-tower"></i>
                    <span>Live Feed</span>
                </a>
                <a href="{{ route('attendances.index') }}" class="nav-link{{ request()->routeIs('attendances.index') ? ' active' : '' }}">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Student Attendance</span>
                </a>
                <a href="{{ route('biometric.index') }}" class="nav-link{{ request()->routeIs('biometric.*') ? ' active' : '' }}">
                    <i class="fas fa-fingerprint"></i>
                    <span>Biometric Sync</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('fee-invoices', $access))
                <a href="{{ route('fee_invoices.index') }}" class="nav-link{{ request()->routeIs('fee_invoices.*') ? ' active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Fee Receipts</span>
                </a>
            @endif

            @if($isSuperOrRoot)
                <a href="{{ route('reports.index') }}" class="nav-link{{ request()->routeIs('reports.*') ? ' active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Reports</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('trainings', $access))
                <a href="{{ route('trainings.index') }}" class="nav-link{{ request()->routeIs('trainings.*') ? ' active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Training & Internship</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('expenses', $access))
                <a href="{{ route('expenses.index') }}" class="nav-link{{ request()->routeIs('expenses.*') ? ' active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Expenses</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('salary-slips', $access))
                <a href="{{ route('salary_slips.index') }}" class="nav-link{{ request()->routeIs('salary_slips.*') ? ' active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Salary Slips</span>
                </a>
            @endif

            @if($isSuperOrRoot)
                <a href="{{ route('backups.index') }}" class="nav-link{{ request()->routeIs('backups.*') ? ' active' : '' }}">
                    <i class="fas fa-database"></i>
                    <span>Backups</span>
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-link{{ request()->routeIs('tasks.*') ? ' active' : '' }}">
                    <i class="fas fa-tasks"></i>
                    <span>Tasks Assigned</span>
                </a>
                <a href="{{ route('daily-updates.index') }}" class="nav-link{{ request()->routeIs('daily-updates.*') ? ' active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Daily Work Logs</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('clients', $access))
                <a href="{{ route('clients.index') }}" class="nav-link{{ request()->routeIs('clients.*') ? ' active' : '' }}">
                    <i class="fas fa-handshake"></i>
                    <span>Clients</span>
                </a>
            @endif

            @if($isSuperOrRoot || in_array('client-invoices', $access))
                <a href="{{ route('client_invoices.index') }}" class="nav-link{{ request()->routeIs('client_invoices.*') ? ' active' : '' }}">
                    <i class="fas fa-file-invoice"></i>
                    <span>Client Invoices</span>
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
            <button type="button" class="theme-toggle" data-theme-toggle title="Toggle Dark/Light Mode">
                <span class="theme-icon-wrapper"><i class="fas fa-moon"></i></span>
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
</body>
</html>

