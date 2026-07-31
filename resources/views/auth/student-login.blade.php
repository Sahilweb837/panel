@php
    $primaryColor = \App\Models\Setting::get('primary_color', '#ff5532');
    $hex = str_replace('#', '', $primaryColor);
    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $primaryRgb = "$r, $g, $b";

    $adjustBrightness = function($hex, $steps) {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    };

    $gradientStart = $adjustBrightness($primaryColor, 150);
    $gradientEnd = $adjustBrightness($primaryColor, 120);
    $primaryColorDark = $adjustBrightness($primaryColor, -25);
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Portal Login | Fees Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: {{ $primaryColor }};
            --brand-primary-dark: {{ $primaryColorDark }};
            --brand-primary-rgb: {{ $primaryRgb }};
            --bg-light: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --input-border: #e2e8f0;
            --input-bg: #ffffff;
            --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            --font-main: 'Outfit', sans-serif;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.6);
            --gradient-left: linear-gradient(135deg, {{ $gradientStart }} 0%, {{ $gradientEnd }} 100%);
        }

        html[data-theme="dark"] {
            --bg-light: #0b0f19;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --input-border: #1e293b;
            --input-bg: #0f172a;
            --card-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            --glass-bg: rgba(15, 23, 42, 0.65);
            --glass-border: rgba(255, 255, 255, 0.05);
            --gradient-left: linear-gradient(135deg, #0f172a 0%, #070a13 100%);
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

        .login-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            position: relative;
        }

        /* Ambient Background Blobs */
        .ambient-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            z-index: 1;
            pointer-events: none;
            animation: pulseBlobs 10s infinite alternate;
        }
        
        .blob-1 {
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(var(--brand-primary-rgb), 0.8) 0%, rgba(255,255,255,0) 70%);
        }
        
        .blob-2 {
            bottom: -10%;
            right: -10%;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(244, 63, 94, 0.6) 0%, rgba(255,255,255,0) 70%);
        }

        @keyframes pulseBlobs {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.1) translate(30px, -20px); }
        }

        /* Left Side */
        .login-left {
            flex: 1.1;
            background: var(--gradient-left);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 4rem;
            z-index: 5;
            border-right: 1px solid var(--input-border);
        }

        .left-content-wrapper {
            position: relative;
            z-index: 10;
            max-width: 480px;
            text-align: center;
        }

        .brand-logo-html img {
            height: 56px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
            transition: transform 0.3s ease;
        }

        .brand-logo-html:hover img {
            transform: scale(1.05);
        }

        .left-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            line-height: 1.2;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--text-dark) 30%, rgba(var(--brand-primary-rgb), 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .left-subtitle {
            font-size: 1.05rem;
            color: var(--text-muted);
            margin-bottom: 3.5rem;
            font-weight: 500;
        }

        .feature-cards {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            text-align: left;
            width: 100%;
        }

        .glass-feature {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            padding: 1.25rem 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-feature:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 30px rgba(var(--brand-primary-rgb), 0.08);
            border-color: rgba(var(--brand-primary-rgb), 0.3);
        }

        .glass-feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(var(--brand-primary-rgb), 0.1);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .glass-feature:hover .glass-feature-icon {
            background: var(--brand-primary);
            color: #fff;
            transform: rotate(360deg);
        }

        .glass-feature h4 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
            color: var(--text-dark);
        }

        .glass-feature p {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin: 0;
            font-weight: 500;
        }

        /* Right Side */
        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            position: relative;
            z-index: 10;
        }

        .login-form-container {
            width: 100%;
            max-width: 460px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 3rem 2.5rem;
            border-radius: 28px;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--glass-border);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.25rem;
        }

        .form-header h2 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.4rem;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .role-badge {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            color: var(--brand-primary);
            padding: 6px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.82rem;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(99, 102, 241, 0.15);
        }

        /* Form Inputs */
        .form-floating {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .form-control {
            height: 58px;
            border-radius: 16px;
            border: 2px solid var(--input-border);
            background-color: var(--input-bg);
            color: var(--text-dark);
            padding: 1rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(var(--brand-primary-rgb), 0.1);
            background-color: var(--input-bg);
        }

        .form-floating label {
            padding: 1rem 1.25rem;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control:focus ~ label,
        .form-control:not(:placeholder-shown) ~ label {
            color: var(--brand-primary);
            transform: scale(0.85) translateY(-0.85rem) translateX(0.15rem);
            background: transparent;
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
            font-size: 1.05rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--brand-primary);
        }

        /* Remember Checkbox */
        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.75rem;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-container input[type="checkbox"] {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid var(--input-border);
            accent-color: var(--brand-primary);
            cursor: pointer;
            transition: all 0.2s;
        }

        /* Button Custom */
        .btn-primary-custom {
            width: 100%;
            height: 58px;
            border-radius: 16px;
            background: var(--brand-primary);
            color: white;
            font-size: 1.05rem;
            font-weight: 700;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 20px rgba(var(--brand-primary-rgb), 0.25);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(var(--brand-primary-rgb), 0.4);
            background: var(--brand-primary-dark);
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-dark);
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(0,0,0,0.03);
            z-index: 100;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: rotate(20deg) scale(1.05);
            background: var(--input-bg);
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
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            z-index: 100;
        }

        .back-link:hover {
            color: var(--brand-primary);
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .login-left {
                display: none;
            }
            .login-wrapper {
                background: var(--gradient-left);
            }
            .ambient-blob {
                opacity: 0.25;
            }
        }
    </style>
</head>
<body class="login-page">
    <div class="ambient-blob blob-1"></div>
    <div class="ambient-blob blob-2"></div>

    <button type="button" class="theme-toggle" data-theme-toggle title="Toggle Dark/Light Mode">
        <i class="fas fa-moon"></i>
    </button>

    <div class="login-wrapper">
        <!-- Left Side (Visual Highlights) -->
        <div class="login-left">
            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Main Site
            </a>

            <div class="left-content-wrapper">
                <div class="brand-logo-html mb-5 text-center">
                    <img src="{{ \App\Models\Setting::getLogoUrl() }}" alt="{{ \App\Models\Setting::get('institute_name', 'Netcoder') }} Logo">
                </div>
                <h1 class="left-title">Student <br><span>Portal</span></h1>
                <p class="left-subtitle">Your personal academic student dashboard.</p>

                <div class="feature-cards">
                    <div class="glass-feature">
                        <div class="glass-feature-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <div>
                            <h4>Course Hub</h4>
                            <p>Access enrollment details and download syllabus.</p>
                        </div>
                    </div>
                    <div class="glass-feature">
                        <div class="glass-feature-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h4>Fee Management</h4>
                            <p>View unpaid receipts and clear dues online.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side (Form) -->
        <div class="login-right">
            <div class="login-form-container">
                <div class="form-header">
                    <div class="role-badge"><i class="fas fa-user-graduate me-1"></i> Student Portal</div>
                    <h2>Welcome Back</h2>
                    <p>Enter your details to access your account</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger p-3 mb-4" style="border-radius: 16px; font-size: 0.85rem;">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="login-form">
                    @csrf
                    <input type="hidden" name="account_type" value="student">

                    <div class="form-floating">
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="login_id" name="login_id" value="{{ old('login_id', old('email')) }}" placeholder="Student ID / Email" required autocomplete="username">
                        <label for="login_id"><i class="fas fa-id-card"></i> Student ID / Email</label>
                    </div>

                    <div class="form-floating position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required autocomplete="current-password">
                        <label for="password"><i class="fas fa-lock"></i> Secure Password</label>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>

                    <label class="checkbox-container">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Remember me on this device</span>
                    </label>

                    <button type="submit" class="btn-primary-custom">
                        <span>Sign In</span> <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password Visibility Toggle
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function () {
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
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    </script>
</body>
</html>
