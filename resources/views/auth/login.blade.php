<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Fees Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --brand-primary: #ff5532;
            --brand-secondary: #10b981;
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --input-border: #e2e8f0;
            --input-bg: #ffffff;
            --card-shadow: 0 20px 40px rgba(0,0,0,0.06);
            --font-main: 'Outfit', sans-serif;
        }

        html[data-theme="dark"] {
            --bg-light: #0f172a;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --input-border: #334155;
            --input-bg: #1e293b;
            --card-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        body.login-page {
            font-family: var(--font-main);
            background-color: var(--bg-light);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            margin: 0;
        }

        /* Layout */
        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* Left Side */
        .login-left {
            flex: 1;
            background: var(--bg-light);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--text-dark);
            padding: 3rem;
        }

        .login-vector-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120%;
            height: 120%;
            opacity: 1;
            pointer-events: none;
            transition: opacity 0.5s ease;
        }

        .left-content-wrapper {
            position: relative;
            z-index: 10;
            max-width: 500px;
            text-align: center;
        }

        .brand-logo-html {
            font-size: 2.5rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 2rem;
            letter-spacing: -0.5px;
        }

        .brand-logo-html i {
            color: var(--brand-primary);
            background: rgba(255, 85, 50, 0.15);
            padding: 12px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(255, 85, 50, 0.2);
        }

        .left-title {
            font-size: 3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .left-subtitle {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 3rem;
        }

        .feature-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            text-align: left;
        }

        .glass-feature {
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            padding: 1.5rem;
            border-radius: 16px;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .glass-feature:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .glass-feature i {
            font-size: 1.5rem;
            color: var(--brand-primary);
            margin-top: 4px;
        }

        .glass-feature h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .glass-feature p {
            font-size: 0.9rem;
            color: #94a3b8;
            margin: 0;
        }

        /* Right Side */
        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-light);
            padding: 2rem;
            position: relative;
        }

        .login-form-container {
            width: 100%;
            max-width: 480px;
            background: var(--input-bg);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--input-border);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        /* Role Selector */
        .role-selector {
            display: flex;
            background: var(--bg-light);
            padding: 6px;
            border-radius: 16px;
            margin-bottom: 2rem;
            border: 1px solid var(--input-border);
        }

        .role-btn {
            flex: 1;
            padding: 12px;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .role-btn.active {
            background: var(--input-bg);
            color: var(--brand-primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        html[data-theme="dark"] .role-btn.active {
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Form Inputs */
        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            height: 60px;
            border-radius: 16px;
            border: 2px solid var(--input-border);
            background-color: var(--input-bg);
            color: var(--text-dark);
            padding: 1rem 1.25rem;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(255, 85, 50, 0.1);
            background-color: var(--input-bg);
        }

        .form-floating label {
            padding: 1rem 1.25rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .form-control:focus ~ label,
        .form-control:not(:placeholder-shown) ~ label {
            color: var(--brand-primary);
            transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem);
        }

        /* Secure Password Toggle */
        .password-toggle {
            position: absolute;
            right: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            z-index: 5;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--brand-primary);
        }

        .password-strength {
            height: 4px;
            background: var(--input-border);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
            display: none; /* Can be enabled via JS if needed */
        }

        /* Action Buttons */
        .btn-primary-custom {
            width: 100%;
            height: 60px;
            border-radius: 16px;
            background: var(--brand-primary);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(255, 85, 50, 0.25);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 85, 50, 0.35);
            background: #e04423;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2rem;
            color: var(--text-muted);
            font-weight: 500;
            cursor: pointer;
        }

        .checkbox-container input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            border: 2px solid var(--input-border);
            accent-color: var(--brand-primary);
            cursor: pointer;
        }

        /* Theme Toggle */
        .theme-toggle {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--text-dark);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--card-shadow);
            z-index: 100;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: rotate(15deg);
        }

        /* Back Link */
        .back-link {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: color 0.3s ease;
            z-index: 100;
        }

        .back-link:hover {
            color: var(--text-dark);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .login-left {
                display: none;
            }
        }
    </style>
</head>
<body class="login-page">
    <button type="button" class="theme-toggle" data-theme-toggle title="Toggle Dark/Light Mode">
        <i class="fas fa-moon"></i>
    </button>

    <div class="login-wrapper">
        <!-- Left Visual Side -->
        <div class="login-left">
            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Main Site
            </a>

            <!-- Clean text logo -->
            <div class="left-content-wrapper">
                <div class="brand-logo-html" style="font-size: 2rem; font-weight: 800; display: inline-flex; align-items: center; gap: 12px; margin-bottom: 2rem; letter-spacing: -0.5px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--brand-primary), #ffa032); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; box-shadow: 0 4px 15px rgba(255, 85, 50, 0.3);">
                        N
                    </div>
                    Netcoder
                </div>
                <h1 class="left-title" id="leftTitle">Transform Your <br>Institution</h1>
                <p class="left-subtitle" id="leftSubtitle">The all-in-one premium management ecosystem.</p>

                <div class="feature-cards" id="featureCards">
                    <!-- Dynamic Features injected via JS -->
                </div>
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="login-right">
            <div class="login-form-container">
                <div class="form-header">
                    <h2 id="loginTitle">Welcome Back</h2>
                    <p id="loginSubtitle">Enter your details to access your account</p>
                </div>

                @if(!request()->has('type'))
                <div class="role-selector">
                    <button type="button" class="role-btn active" data-type="institute">
                        <i class="fas fa-building"></i> Admin
                    </button>
                    <button type="button" class="role-btn" data-type="staff">
                        <i class="fas fa-chalkboard-teacher"></i> Staff
                    </button>
                    <button type="button" class="role-btn" data-type="student">
                        <i class="fas fa-user-graduate"></i> Student
                    </button>
                </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" style="border-radius: 12px; font-size: 0.9rem;">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="login-form">
                    @csrf
                    <input type="hidden" name="account_type" id="account_type" value="{{ request('type', old('account_type', 'institute')) }}">

                    <div class="form-floating">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required>
                        <label for="email"><i class="fas fa-envelope me-2"></i> Email Address</label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i> Secure Password</label>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <label class="checkbox-container">
                        <input type="checkbox" name="remember">
                        Remember me on this device
                    </label>

                    <button type="submit" class="btn-primary-custom">
                        Sign In <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        // Password Toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        // Theme Toggle logic
        const themeBtn = document.querySelector('.theme-toggle');
        const html = document.documentElement;
        
        const savedTheme = localStorage.getItem('fees-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        html.dataset.theme = savedTheme;
        updateThemeIcon(savedTheme);

        themeBtn.addEventListener('click', () => {
            const currentTheme = html.dataset.theme;
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.dataset.theme = newTheme;
            localStorage.setItem('fees-theme', newTheme);
            updateThemeIcon(newTheme);
        });

        function updateThemeIcon(theme) {
            const icon = themeBtn.querySelector('i');
            if(theme === 'dark') {
                icon.className = 'fas fa-sun';
            } else {
                icon.className = 'fas fa-moon';
            }
        }

        // Role switching logic
        const configs = {
            institute: {
                title: 'Transform Your<br>Institution',
                subtitle: 'The all-in-one premium management ecosystem.',
                features: [
                    { icon: 'fa-chart-pie', title: 'Smart Analytics', desc: 'Real-time performance insights' },
                    { icon: 'fa-shield-alt', title: 'Enterprise Security', desc: 'Bank-grade data encryption' }
                ],
                color: '#ff5532'
            },
            staff: {
                title: 'Empower Your<br>Teaching',
                subtitle: 'Streamlined tools for modern educators.',
                features: [
                    { icon: 'fa-clipboard-check', title: 'Automated Attendance', desc: 'Seamless biometric integration' },
                    { icon: 'fa-wallet', title: 'Payroll History', desc: 'Instant access to digital slips' }
                ],
                color: '#ff5532'
            },
            student: {
                title: 'Accelerate Your<br>Learning',
                subtitle: 'Your personal academic portal.',
                features: [
                    { icon: 'fa-book-reader', title: 'Course Hub', desc: 'Access materials anywhere' },
                    { icon: 'fa-file-invoice-dollar', title: 'Fee Management', desc: 'Transparent due tracking' }
                ],
                color: '#ff5532'
            }
        };

        function updateRole(role) {
            // Update hidden input
            document.getElementById('account_type').value = role;

            // Update UI buttons
            document.querySelectorAll('.role-btn').forEach(btn => {
                if(btn.dataset.type === role) {
                    btn.classList.add('active');
                    btn.style.color = configs[role].color;
                } else {
                    btn.classList.remove('active');
                    btn.style.color = ''; // reset
                }
            });

            // Update Text Content
            document.getElementById('leftTitle').innerHTML = configs[role].title;
            document.getElementById('leftSubtitle').innerHTML = configs[role].subtitle;
            document.documentElement.style.setProperty('--brand-primary', configs[role].color);

            // Update Features
            const fc = document.getElementById('featureCards');
            fc.innerHTML = configs[role].features.map(f => `
                <div class="glass-feature">
                    <i class="fas ${f.icon}"></i>
                    <div>
                        <h4>${f.title}</h4>
                        <p>${f.desc}</p>
                    </div>
                </div>
            `).join('');
        }

        // Initialize
        const initialType = document.getElementById('account_type').value;
        updateRole(initialType);

        // Click listeners
        document.querySelectorAll('.role-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                updateRole(e.currentTarget.dataset.type);
            });
        });

        // Clean URL
        if (window.location.search) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
</body>
</html>
