<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Fees Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css'])
</head>
<body class="login-page">
    <button type="button" class="eye-protection-btn login-theme-toggle" data-theme-toggle title="Toggle Eye Protection Theme">
        <span class="eye-icon-wrapper"><i class="fas fa-eye-slash"></i></span>
        <span class="btn-text">Eye Protection</span>
    </button>

    <main class="login-container">
        <!-- Left Side - Branding & Features -->
        <div class="login-left">
            <!-- Glowing Space Orbs and Grid Overlay -->
            <div class="space-glow-orb space-glow-orb-1"></div>
            <div class="space-glow-orb space-glow-orb-2"></div>
            <div class="space-grid-overlay"></div>

            <div class="login-header-left">
                <div class="login-logo-wrapper animate-float">
                    <img src="{{ asset('image.png') }}" alt="Netcoder ERP" class="login-logo">
                </div>
                <h1 class="animate-float">Netcoder ERP</h1>
                <p class="brand-tagline">Premium Institute Management Space</p>
            </div>

            <div class="features-list">
                <div class="feature-item glass-card animate-hover-lift">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h3>Smart Analytics</h3>
                        <p>Track real-time performance & institutional insights</p>
                    </div>
                </div>

                <div class="feature-item glass-card animate-hover-lift">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h3>Manage Students</h3>
                        <p>End-to-end student lifecycle & active tracking</p>
                    </div>
                </div>

                <div class="feature-item glass-card animate-hover-lift">
                    <div class="feature-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3>Fee Management</h3>
                        <p>Automated invoices, receipts, and smart payments</p>
                    </div>
                </div>

                <div class="feature-item glass-card animate-hover-lift">
                    <div class="feature-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h3>Staff Management</h3>
                        <p>Effortless payroll, attendance, and record keeping</p>
                    </div>
                </div>
            </div>

            <div class="login-footer-left">
                <p>&copy; 2026 Netcoder. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-form-wrapper">
                <div class="login-form-header">
                    <h2>Welcome Back</h2>
                    <p>Sign in to your account to continue</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="login-form">
                    @csrf

                    <div class="form-group">
                        <label for="account_type">
                            <i class="fas fa-user-tie"></i> Login Type
                        </label>
                        <select id="account_type" name="account_type" required class="form-input">
                            <option value="institute" {{ old('account_type', 'institute') === 'institute' ? 'selected' : '' }}>Institute / Admin</option>
                            <option value="staff" {{ old('account_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                class="form-input"
                                placeholder="Enter your email"
                            />
                            <span class="input-icon"><i class="fas fa-at"></i></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                class="form-input"
                                placeholder="Enter your password"
                            />
                            <span class="input-icon toggle-password" data-toggle="password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" class="checkbox-input" />
                            <span>Remember me for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" class="button button-login">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>

                    <div class="login-help">
                        <p><strong>Demo Credentials:</strong></p>
                        <p><small>Email: superadmin@example.com</small></p>
                        <p><small>Password: admin123</small></p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const input = this.closest('.input-wrapper').querySelector('input');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
</body>
</html>
