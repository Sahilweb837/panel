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
        <div class="login-left" style="position: relative; overflow: hidden;">
            <!-- Glowing Space Orbs and Grid Overlay -->
            <div class="space-glow-orb space-glow-orb-1"></div>
            <div class="space-glow-orb space-glow-orb-2"></div>
            <div class="space-grid-overlay"></div>
            <canvas id="login-particles" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; opacity: 0.7;"></canvas>

            <div class="login-header-left" style="position: relative; z-index: 2;">
                <div class="login-logo-wrapper animate-float">
                    <img src="{{ asset('image.png') }}" alt="Netcoder ERP" class="login-logo">
                </div>
                <h1 class="animate-float">Netcoder ERP</h1>
                <p class="brand-tagline">Premium Institute Management Space</p>
            </div>

            <div class="features-list" style="position: relative; z-index: 2;">
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

            <div class="login-footer-left" style="position: relative; z-index: 2;">
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

        // Floating Canvas Particles Space Animation
        (function() {
            const canvas = document.getElementById('login-particles');
            if(!canvas) return;
            const ctx = canvas.getContext('2d');
            let particles = [];
            
            function resizeCanvas() {
                canvas.width = canvas.parentElement.offsetWidth;
                canvas.height = canvas.parentElement.offsetHeight;
            }
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2.5 + 1;
                    this.speedX = Math.random() * 0.3 - 0.15;
                    this.speedY = Math.random() * 0.3 - 0.15;
                    this.alpha = Math.random() * 0.6 + 0.2;
                }
                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    if (this.x < 0 || this.x > canvas.width) this.speedX = -this.speedX;
                    if (this.y < 0 || this.y > canvas.height) this.speedY = -this.speedY;
                }
                draw() {
                    ctx.save();
                    ctx.globalAlpha = this.alpha;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fillStyle = '#ff5532';
                    ctx.shadowBlur = 6;
                    ctx.shadowColor = '#ff5532';
                    ctx.fill();
                    ctx.restore();
                }
            }

            function init() {
                particles = [];
                const particleCount = Math.floor((canvas.width * canvas.height) / 9500);
                for(let i = 0; i < particleCount; i++) {
                    particles.push(new Particle());
                }
            }
            init();
            window.addEventListener('resize', init);

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                particles.forEach(p => {
                    p.update();
                    p.draw();
                });
                
                // Draw connecting threads between adjacent particles
                for(let i = 0; i < particles.length; i++) {
                    for(let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if(dist < 90) {
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = `rgba(255, 85, 50, ${0.15 - dist/90})`;
                            ctx.lineWidth = 0.5;
                            ctx.stroke();
                        }
                    }
                }
                
                requestAnimationFrame(animate);
            }
            animate();
        })();
    </script>
</body>
</html>
