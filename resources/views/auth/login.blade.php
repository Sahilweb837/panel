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
                                autocomplete="new-password"
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

                    <div class="demo-credentials-card mt-4 p-3 rounded-3 border text-start" style="background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 85, 50, 0.15) !important; cursor: pointer; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);" id="demo-credentials-trigger" title="Click to instantly auto-fill credentials">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--first-color); letter-spacing: 0.05em;">
                                <i class="fas fa-wand-magic-sparkles me-1 animate-pulse" style="animation: pulse 2s infinite;"></i> Demo Quick-Fill
                            </span>
                            <!-- <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem; font-weight: 700; padding: 3px 8px; border-radius: 4px;">Click to Fill</span> -->
                        </div>
                        <!-- <p class="mb-1 text-muted small" style="font-size: 0.8rem;"><strong class="text-white-50">Email:</strong> superadmin@example.com</p>
                        <p class="mb-0 text-muted small" style="font-size: 0.8rem;"><strong class="text-white-50">Password:</strong> admin123</p> -->
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

        // Interactive Holographic Constellation Wave Particle Simulation
        (function() {
            const canvas = document.getElementById('login-particles');
            if(!canvas) return;
            const ctx = canvas.getContext('2d');
            let particles = [];
            let mouse = { x: null, y: null, radius: 160 };
            
            function resizeCanvas() {
                const parent = canvas.parentElement;
                canvas.width = parent.offsetWidth;
                canvas.height = parent.offsetHeight;
            }
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // Track mouse cursor relative to the canvas inside the login-left container
            canvas.parentElement.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                mouse.x = e.clientX - rect.left;
                mouse.y = e.clientY - rect.top;
            });

            canvas.parentElement.addEventListener('mouseleave', () => {
                mouse.x = null;
                mouse.y = null;
            });

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2.2 + 0.8;
                    this.speedX = Math.random() * 0.4 - 0.2;
                    this.speedY = Math.random() * 0.4 - 0.2;
                    this.alpha = Math.random() * 0.5 + 0.3;
                    
                    // High-end warm-colored spectrum
                    const colors = ['#ff5532', '#ffa032', '#ffd532', '#ff7850'];
                    this.color = colors[Math.floor(Math.random() * colors.length)];
                }
                update() {
                    // Drift smoothly
                    this.x += this.speedX;
                    this.y += this.speedY;

                    // Bounce on canvas boundaries
                    if (this.x < 0 || this.x > canvas.width) this.speedX = -this.speedX;
                    if (this.y < 0 || this.y > canvas.height) this.speedY = -this.speedY;

                    // Smooth magnetic gravity attraction to cursor
                    if (mouse.x !== null && mouse.y !== null) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < mouse.radius) {
                            const force = (mouse.radius - dist) / mouse.radius;
                            this.x += (dx / dist) * force * 1.3;
                            this.y += (dy / dist) * force * 1.3;
                        }
                    }
                }
                draw() {
                    ctx.save();
                    ctx.globalAlpha = this.alpha;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fillStyle = this.color;
                    ctx.shadowBlur = 6;
                    ctx.shadowColor = this.color;
                    ctx.fill();
                    ctx.restore();
                }
            }

            function init() {
                particles = [];
                const count = Math.floor((canvas.width * canvas.height) / 8000);
                for(let i = 0; i < count; i++) {
                    particles.push(new Particle());
                }
            }
            init();
            window.addEventListener('resize', init);

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Update and draw each particle
                particles.forEach(p => {
                    p.update();
                    p.draw();
                });
                
                // Draw delicate glowing bonds between adjacent particles
                for(let i = 0; i < particles.length; i++) {
                    for(let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if(dist < 100) {
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            
                            // Blend dynamic color link
                            ctx.strokeStyle = `rgba(255, 120, 50, ${(0.16 - dist/100).toFixed(3)})`;
                            ctx.lineWidth = 0.6;
                            ctx.stroke();
                        }
                    }
                }
                
                requestAnimationFrame(animate);
            }
            animate();
        })();

        // Click to auto-fill demo credentials helper
        const demoTrigger = document.getElementById('demo-credentials-trigger');
        if (demoTrigger) {
            demoTrigger.addEventListener('click', () => {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const typeSelect = document.getElementById('account_type');

                if (emailInput && passwordInput && typeSelect) {
                    emailInput.value = 'superadmin@example.com';
                    passwordInput.value = 'admin123';
                    typeSelect.value = 'institute';

                    // Add subtle click scale transition
                    demoTrigger.style.transform = 'scale(0.97)';
                    demoTrigger.style.backgroundColor = 'rgba(255, 85, 50, 0.08)';
                    setTimeout(() => {
                        demoTrigger.style.transform = 'none';
                        demoTrigger.style.backgroundColor = 'rgba(255, 255, 255, 0.03)';
                    }, 180);
                }
            });
        }
    </script>
</body>
</html>
