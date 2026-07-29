<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Portal Login | Fees Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #6366f1;
            --brand-primary-dark: #4f46e5;
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

        .login-wrapper { display: flex; width: 100%; height: 100vh; }

        .login-left {
            flex: 1; background: var(--bg-light); position: relative; overflow: hidden;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            color: var(--text-dark); padding: 3rem;
        }

        .left-content-wrapper { position: relative; z-index: 10; max-width: 500px; text-align: center; }

        .brand-logo-html { font-size: 2.5rem; font-weight: 700; display: inline-flex; align-items: center; gap: 12px; margin-bottom: 2rem; }
        
        .left-title { font-size: 3.5rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.1; color: var(--text-dark); }
        .left-title span { color: var(--brand-primary); }
        .left-subtitle { font-size: 1.15rem; color: var(--text-muted); margin-bottom: 3rem; }

        .feature-cards { display: grid; grid-template-columns: 1fr; gap: 1rem; text-align: left; }
        .glass-feature {
            background: var(--input-bg); border: 1px solid var(--input-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02); padding: 1.5rem; border-radius: 16px;
            display: flex; align-items: flex-start; gap: 1rem; transition: transform 0.3s ease;
        }
        .glass-feature:hover { transform: translateY(-5px); }
        .glass-feature i { font-size: 1.5rem; color: var(--brand-primary); margin-top: 4px; }
        .glass-feature h4 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.25rem; }
        .glass-feature p { font-size: 0.9rem; color: #94a3b8; margin: 0; }

        .login-right { flex: 1; display: flex; align-items: center; justify-content: center; background: var(--bg-light); padding: 2rem; position: relative; }
        .login-form-container { width: 100%; max-width: 460px; background: var(--input-bg); padding: 3rem; border-radius: 24px; box-shadow: var(--card-shadow); border: 1px solid var(--input-border); }

        .form-header { text-align: center; margin-bottom: 2.5rem; }
        .form-header h2 { font-size: 2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.5rem; }
        .form-header p { color: var(--text-muted); font-size: 1rem; }

        .role-badge { display: inline-block; background: rgba(99, 102, 241, 0.1); color: var(--brand-primary); padding: 6px 16px; border-radius: 50px; font-weight: 600; font-size: 0.85rem; margin-bottom: 1rem; border: 1px solid rgba(99, 102, 241, 0.2); }

        .form-floating { margin-bottom: 1.5rem; position: relative; }
        .form-control { height: 60px; border-radius: 16px; border: 2px solid var(--input-border); background-color: var(--input-bg); color: var(--text-dark); padding: 1rem 1.25rem; font-size: 1rem; font-weight: 500; transition: all 0.3s ease; }
        .form-control:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); background-color: var(--input-bg); }
        .form-floating label { padding: 1rem 1.25rem; color: var(--text-muted); font-weight: 500; }
        .form-control:focus ~ label, .form-control:not(:placeholder-shown) ~ label { color: var(--brand-primary); transform: scale(0.85) translateY(-0.75rem) translateX(0.15rem); }

        .password-toggle { position: absolute; right: 1.25rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); cursor: pointer; z-index: 5; transition: color 0.3s ease; }
        .password-toggle:hover { color: var(--brand-primary); }

        .btn-primary-custom { width: 100%; height: 60px; border-radius: 16px; background: var(--brand-primary); color: white; font-size: 1.1rem; font-weight: 600; border: none; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25); }
        .btn-primary-custom:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(99, 102, 241, 0.35); background: var(--brand-primary-dark); }

        .checkbox-container { display: flex; align-items: center; gap: 8px; margin-bottom: 2rem; color: var(--text-muted); font-weight: 500; cursor: pointer; }
        .checkbox-container input[type="checkbox"] { width: 18px; height: 18px; border-radius: 6px; border: 2px solid var(--input-border); accent-color: var(--brand-primary); cursor: pointer; }

        .theme-toggle { position: absolute; top: 2rem; right: 2rem; background: var(--input-bg); border: 1px solid var(--input-border); color: var(--text-dark); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--card-shadow); z-index: 100; transition: all 0.3s ease; }
        .theme-toggle:hover { transform: rotate(15deg); }
        .back-link { position: absolute; top: 2rem; left: 2rem; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500; transition: color 0.3s ease; z-index: 100; }
        .back-link:hover { color: var(--text-dark); }

        @media (max-width: 992px) { .login-left { display: none; } }
    </style>
</head>
<body class="login-page">
    <button type="button" class="theme-toggle" data-theme-toggle title="Toggle Dark/Light Mode">
        <i class="fas fa-moon"></i>
    </button>

    <div class="login-wrapper">
        <div class="login-left">
            <a href="{{ url('/') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to Main Site</a>
            <div class="left-content-wrapper">
                <div class="brand-logo-html" style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: center;">
                    <img src="https://www.netcoder.in/images/logo.png" alt="Netcoder Logo" style="height: 64px; width: auto; object-fit: contain; max-width: 100%;">
                </div>
                <h1 class="left-title">Student <br><span>Portal</span></h1>
                <p class="left-subtitle">Your personal academic dashboard.</p>
                <div class="feature-cards">
                    <div class="glass-feature">
                        <i class="fas fa-book-reader"></i>
                        <div><h4>Course Hub</h4><p>Access materials anywhere, anytime.</p></div>
                    </div>
                    <div class="glass-feature">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <div><h4>Fee Management</h4><p>Transparent due tracking and online payments.</p></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-form-container">
                <div class="form-header">
                    <div class="role-badge"><i class="fas fa-user-graduate me-1"></i> Student Access</div>
                    <h2>Welcome Back</h2>
                    <p>Enter your details to access your account</p>
                </div>

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
                    <input type="hidden" name="account_type" value="student">

                    <div class="form-floating">
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="login_id" name="login_id" value="{{ old('login_id', old('email')) }}" placeholder="Email, Username, or Student ID" required>
                        <label for="login_id"><i class="fas fa-id-card me-2"></i> Student ID / Email</label>
                    </div>

                    <div class="form-floating position-relative">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i> Secure Password</label>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
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
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

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
