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
            @endif

            @if($isSuperOrRoot || in_array('students', $access))
                <a href="{{ route('students.index') }}" class="nav-link{{ request()->routeIs('students.*') ? ' active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Students</span>
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

    <!-- Chatbot Floating Button -->
    <div id="chatbot-trigger" style="position: fixed; bottom: 25px; right: 25px; width: 60px; height: 60px; border-radius: 50%; background: var(--first-color); display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; box-shadow: 0 10px 30px rgba(255, 85, 50, 0.3); z-index: 9999; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
        <i class="fas fa-robot fa-lg"></i>
    </div>

    <!-- Chatbot Glassmorphic Panel/Drawer -->
    <div id="chatbot-drawer" style="position: fixed; top: 0; right: -420px; width: 420px; height: 100vh; background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-left: 1px solid rgba(255, 255, 255, 0.4); z-index: 10000; box-shadow: -10px 0 40px rgba(0,0,0,0.05); transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column;">
        <!-- Header -->
        <div style="padding: 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.5);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--first-color-light); display: flex; align-items: center; justify-content: center; color: var(--first-color);">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h5 style="margin: 0; font-weight: 700;">ERP Assistant</h5>
                    <small class="text-muted"><span style="color: #10b981;">●</span> Online</small>
                </div>
            </div>
            <button type="button" id="close-chatbot" style="background: transparent; border: none; font-size: 1.2rem; color: var(--muted); cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <!-- Message Body -->
        <div id="chat-messages" style="flex: 1; padding: 24px; overflow-y: auto; display: flex; flex-direction: column; gap: 16px;">
            <!-- Welcome message -->
            <div style="align-self: flex-start; max-width: 80%; background: var(--surface); border-radius: 16px 16px 16px 4px; padding: 12px 16px; border: 1px solid var(--border); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                <p style="margin: 0; font-size: 0.92rem; line-height: 1.5;">Hello! I am your ERP system assistant. I can run health checks, look up outstanding fees, or check device connectivity. Try clicking one of the options below:</p>
            </div>
            
            <!-- Quick Options -->
            <div class="quick-options" style="display: flex; flex-direction: column; gap: 8px;">
                <button type="button" onclick="sendQuickQuery('Show pending fees')" style="text-align: left; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--first-color)'; this.style.color='var(--first-color)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='inherit'">
                    💵 Which students have pending fees?
                </button>
                <button type="button" onclick="sendQuickQuery('Check biometric status')" style="text-align: left; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--first-color)'; this.style.color='var(--first-color)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='inherit'">
                    📡 Show ADMS Connection Status
                </button>
                <button type="button" onclick="sendQuickQuery('Check system diagnostics')" style="text-align: left; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--first-color)'; this.style.color='var(--first-color)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='inherit'">
                    🏥 Check System Health & Errors
                </button>
            </div>
        </div>

        <!-- Chat Input Footer -->
        <div style="padding: 16px 24px; border-top: 1px solid var(--border); background: rgba(255, 255, 255, 0.5);">
            <form id="chat-input-form" onsubmit="handleChatSubmit(event)" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" id="chat-user-input" placeholder="Type a message..." style="flex: 1; padding: 10px 14px; border: 1px solid var(--input-border); border-radius: 10px; background: var(--surface); font-size: 0.9rem;" required autocomplete="off">
                <button type="submit" style="width: 42px; height: 42px; border-radius: 10px; background: var(--first-color); color: #fff; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: transform 0.2s;"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const trigger = document.getElementById('chatbot-trigger');
            const drawer = document.getElementById('chatbot-drawer');
            const closeBtn = document.getElementById('close-chatbot');
            
            if (trigger && drawer && closeBtn) {
                trigger.addEventListener('click', () => {
                    drawer.style.right = drawer.style.right === '0px' ? '-420px' : '0px';
                });
                closeBtn.addEventListener('click', () => {
                    drawer.style.right = '-420px';
                });
            }
        });

        // Dark mode adaptation for chatbot drawer
        window.addEventListener('theme-color-changed', () => {
            syncChatbotTheme();
        });
        
        function syncChatbotTheme() {
            const drawer = document.getElementById('chatbot-drawer');
            if(drawer) {
                if (document.documentElement.dataset.theme === 'dark') {
                    drawer.style.background = 'rgba(30, 23, 20, 0.85)';
                    drawer.style.borderColor = 'rgba(255, 255, 255, 0.05)';
                    drawer.style.color = '#f5eae4';
                } else {
                    drawer.style.background = 'rgba(255, 255, 255, 0.75)';
                    drawer.style.borderColor = 'rgba(255, 255, 255, 0.4)';
                    drawer.style.color = '#1c1816';
                }
            }
        }
        
        document.addEventListener('DOMContentLoaded', syncChatbotTheme);

        function sendQuickQuery(text) {
            appendMessage(text, 'user');
            fetchQuery(text);
        }

        function handleChatSubmit(e) {
            e.preventDefault();
            const input = document.getElementById('chat-user-input');
            const text = input.value.trim();
            if(!text) return;
            
            appendMessage(text, 'user');
            input.value = '';
            fetchQuery(text);
        }

        function appendMessage(text, type) {
            const chatMessages = document.getElementById('chat-messages');
            const msgDiv = document.createElement('div');
            
            if (type === 'user') {
                msgDiv.style.alignSelf = 'flex-end';
                msgDiv.style.maxWidth = '80%';
                msgDiv.style.background = 'var(--first-color)';
                msgDiv.style.color = '#fff';
                msgDiv.style.borderRadius = '16px 16px 4px 16px';
                msgDiv.style.padding = '12px 16px';
                msgDiv.style.boxShadow = '0 4px 12px rgba(255, 85, 50, 0.15)';
            } else {
                msgDiv.style.alignSelf = 'flex-start';
                msgDiv.style.maxWidth = '80%';
                msgDiv.style.background = 'var(--surface)';
                msgDiv.style.color = 'var(--text)';
                msgDiv.style.borderRadius = '16px 16px 16px 4px';
                msgDiv.style.padding = '12px 16px';
                msgDiv.style.border = '1px solid var(--border)';
                msgDiv.style.boxShadow = '0 4px 12px rgba(0,0,0,0.02)';
            }
            
            const formattedText = text.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            msgDiv.innerHTML = `<p style="margin: 0; font-size: 0.92rem; line-height: 1.5;">${formattedText}</p>`;
            
            const options = chatMessages.querySelector('.quick-options');
            if (options) options.remove();

            chatMessages.appendChild(msgDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function fetchQuery(query) {
            const chatMessages = document.getElementById('chat-messages');
            const indicator = document.createElement('div');
            indicator.id = 'typing-indicator';
            indicator.style.alignSelf = 'flex-start';
            indicator.style.background = 'var(--surface)';
            indicator.style.borderRadius = '16px';
            indicator.style.padding = '12px 16px';
            indicator.style.border = '1px solid var(--border)';
            indicator.innerHTML = `<span style="font-size: 0.85rem; color: var(--muted);"><i class="fas fa-ellipsis-h fa-pulse"></i> Assistant is typing...</span>`;
            chatMessages.appendChild(indicator);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            fetch(`/api/chatbot/query?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    const ind = document.getElementById('typing-indicator');
                    if(ind) ind.remove();
                    appendMessage(data.response, 'bot');
                })
                .catch(err => {
                    const ind = document.getElementById('typing-indicator');
                    if(ind) ind.remove();
                    appendMessage("⚠️ **Error:** Unable to connect to assistant service. Check your connection.", 'bot');
                });
        }
    </script>
</body>
</html>
