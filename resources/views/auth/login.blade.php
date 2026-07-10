<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Fees Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css'])
<style>
         body.login-page {
             background-color: #f8fafc !important;
             color: #1e293b !important;
             font-family: 'Inter', sans-serif;
         }
         .login-container {
             background-color: #f8fafc !important;
             box-shadow: none !important;
             border: none !important;
         }
         .login-left {
             background-color: #ffffff !important;
             border-right: 1px solid #e2e8f0 !important;
             color: #1e293b !important;
             background-image: none;
         }
         .login-left.type-student {
             background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><path fill="%23ff5532" opacity="0.03" d="M50 350 L100 50 L150 200 L200 100 L250 250 L300 80 L350 120 L300 350 Z"/><path fill="%23ff5532" opacity="0.02" d="M0 200 Q100 100 200 200 T400 200"/><circle cx="50" cy="50" r="30" fill="%23ff5532" opacity="0.04"/></svg>');
             background-size: cover;
             background-position: center;
         }
         .login-left.type-staff {
             background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><rect fill="%2310b981" opacity="0.03" x="50" y="50" width="100" height="100"/><rect fill="%2310b981" opacity="0.02" x="150" y="150" width="80" height="80"/><circle cx="300" cy="100" r="40" fill="%2310b981" opacity="0.04"/><path fill="%2310b981" opacity="0.02" d="M0 300 Q100 250 200 300 T400 300"/></svg>');
             background-size: cover;
             background-position: center;
         }
         .login-left.type-institute {
             background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400"><path fill="%23ff5532" opacity="0.04" d="M0 0 L200 0 L200 400 L0 400 Z"/><path fill="%23ff5532" opacity="0.03" d="M200 200 L400 0 L400 200 Z"/><path fill="%23ff5532" opacity="0.02" d="M200 200 L400 400 L200 400 Z"/></svg>');
             background-size: cover;
             background-position: center;
         }
        .login-left h1 {
            color: #1e293b !important;
        }
        .login-right {
            background-color: #f8fafc !important;
        }
        .login-form-wrapper {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 20px !important;
            padding: 40px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
        }
        .login-form-header h2 {
            font-size: 28px !important;
            font-weight: 800 !important;
            color: #1e293b !important;
            letter-spacing: -0.02em !important;
        }
        .login-form-header p {
            color: #64748b !important;
            font-size: 14px !important;
        }
        .form-group label {
            color: #64748b !important;
            font-size: 14px !important;
            margin-bottom: 8px !important;
            font-weight: 600 !important;
        }
        .form-input {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #1e293b !important;
            border-radius: 10px !important;
            padding: 12px 14px !important;
        }
        .form-input:focus {
            border-color: #ff5532 !important;
            box-shadow: 0 0 8px rgba(255, 85, 50, 0.2) !important;
            background-color: #ffffff !important;
        }
        .button-login {
            background-color: #ff5532 !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(255, 85, 50, 0.2) !important;
            padding: 12px !important;
            transition: all 0.3s ease !important;
        }
        .button-login:hover {
            background-color: #e04423 !important;
            box-shadow: 0 4px 16px rgba(255, 85, 50, 0.4) !important;
        }
        .login-type-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: #fff5f2;
            color: #ff5532;
            border: 1px solid #ffebe6;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        .login-form-wrapper {
            transition: all 0.3s ease;
        }
        .login-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 16px;
        }
        .login-type-badge.student {
            background: #fff5f2;
            color: #ff5532;
            border: 1px solid #ffebe6;
        }
        .login-type-badge.staff {
            background: #f0fdf4;
            color: #10b981;
            border: 1px solid #d1fae5;
        }
        .login-type-badge.institute {
            background: #fff5f2;
            color: #ff5532;
            border: 1px solid #ffebe6;
        }
        .type-student .form-input { border-color: #ffebe6 !important; }
        .type-student .form-input:focus { border-color: #ff5532 !important; box-shadow: 0 0 0 3px rgba(255, 85, 50, 0.08) !important; }
        .type-staff .form-input { border-color: #d1fae5 !important; }
        .type-staff .form-input:focus { border-color: #10b981 !important; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.08) !important; }
        .type-institute .form-input { border-color: #ffebe6 !important; }
        .type-institute .form-input:focus { border-color: #ff5532 !important; box-shadow: 0 0 0 3px rgba(255, 85, 50, 0.08) !important; }
        .glass-card {
            background: rgba(0, 0, 0, 0.01) !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            color: #1e293b !important;
        }
        .feature-icon {
            background: #fff5f2 !important;
            color: #ff5532 !important;
            border: 1px solid #ffebe6 !important;
        }
        .brand-tagline {
            color: #ff5532 !important;
            opacity: 1 !important;
        }
        .checkbox-label span {
            color: #64748b !important;
        }
         .login-logo {
             filter: none !important;
         }
         .is-invalid {
             border-color: #ef4444 !important;
             background-color: #fef2f2 !important;
         }
         .is-invalid:focus {
             border-color: #ef4444 !important;
             box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
         }
         .text-danger.mt-2 {
             font-size: 0.85rem;
             margin-top: 0.35rem;
         }
     </style>
</head>
<body class="login-page">
    <button type="button" class="theme-toggle login-theme-toggle" data-theme-toggle title="Toggle Dark/Light Mode">
        <span class="theme-icon-wrapper"><i class="fas fa-moon"></i></span>
    </button>

    <main class="login-container">
        <!-- Left Side - Branding & Features -->
        <div class="login-left" style="position: relative; overflow: hidden;">
            <!-- Premium Orange SVG Vectors/Grids Background -->
            <svg style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;" xmlns="http://www.w3.org/2000/svg">
                <!-- Dot Grid -->
                <pattern id="dot-grid" x="0" y="0" width="30" height="30" patternUnits="userSpaceOnUse">
                    <circle cx="2" cy="2" r="1.5" fill="#ff5532" opacity="0.12" />
                </pattern>
                <rect width="100%" height="100%" fill="url(#dot-grid)" />
                
                <!-- Geometric shapes -->
                <circle cx="80%" cy="20%" r="180" fill="none" stroke="#ff5532" stroke-width="1.5" opacity="0.08" />
                <circle cx="80%" cy="20%" r="280" fill="none" stroke="#ff5532" stroke-width="1" stroke-dasharray="8 8" opacity="0.06" />
                <circle cx="10%" cy="80%" r="120" fill="none" stroke="#ff5532" stroke-width="2" opacity="0.06" />
                
                <!-- Wave lines -->
                <path d="M-50,300 Q150,200 350,320 T750,280" fill="none" stroke="#ff5532" stroke-width="2.5" opacity="0.08" />
                <path d="M-50,320 Q150,220 350,340 T750,300" fill="none" stroke="#ff5532" stroke-width="1" opacity="0.05" />
            </svg>

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
                <div id="changeTypeContainer" style="display: none; margin-bottom: 16px;">
                    <a href="{{ route('login') }}" class="back-link" style="display: inline-flex;">
                        <i class="fas fa-arrow-left"></i> Change login type
                    </a>
                </div>
                <div class="login-form-header">
                    <div class="login-type-icon" id="loginTypeIcon">
                        <i class="fas fa-user-tie" id="loginTypeIconI"></i>
                    </div>
                    <div id="loginTypeBadge"></div>
                    <h2 id="loginTitle">Welcome Back, Admin</h2>
                    <p id="loginSubtitle">Sign in to the management panel to continue</p>
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

                    <input type="hidden" name="account_type" id="account_type" value="{{ request('type', old('account_type', 'institute')) }}">

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
                                class="form-input @error('email') is-invalid @enderror"
                                placeholder="Enter your email"
                            />
                            <span class="input-icon"><i class="fas fa-at"></i></span>
                        </div>
                        @error('email')
                            <div class="text-danger mt-2 small fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
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
                                class="form-input @error('password') is-invalid @enderror"
                                placeholder="Enter your password"
                                autocomplete="new-password"
                            />
                            <span class="input-icon toggle-password" data-toggle="password">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        @error('password')
                            <div class="text-danger mt-2 small fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    @error('account_type')
                        <div class="alert alert-warning mt-3 mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                        </div>
                    @enderror

                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" class="checkbox-input" />
                            <span>Remember me for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" class="button button-login mt-4">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>

                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted"><i class="fas fa-shield-alt me-1"></i>Phone Verification (Firebase OTP)</span>
                            <span id="phoneVerifyBadge" class="badge bg-secondary rounded-pill">Not Verified</span>
                        </div>
                        <div class="input-group mb-2">
                            <input type="tel" id="phoneNumber" class="form-input" placeholder="+91 98765 43210" />
                            <button class="btn btn-outline-success" type="button" onclick="sendOtp()" id="btnSendOtp">
                                <i class="fas fa-paper-plane me-1"></i>Verify
                            </button>
                        </div>
                        <div id="otpSection" style="display: none;">
                            <div class="input-group mb-2">
                                <input type="text" id="otpInput" class="form-input" placeholder="Enter 6-digit OTP" maxlength="6" />
                                <button class="btn btn-primary" type="button" onclick="verifyOtp()">
                                    <i class="fas fa-check me-1"></i>Submit
                                </button>
                            </div>
                            <p class="small text-muted mb-0">OTP expires in 10 minutes. <a href="javascript:void(0)" onclick="sendOtp()" class="text-decoration-underline">Resend</a></p>
                        </div>
                        <input type="hidden" id="sessionInfo" />
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

        // Firebase OTP Functions
        async function sendOtp() {
            const phone = document.getElementById('phoneNumber').value.trim();
            const btn = document.getElementById('btnSendOtp');
            const badge = document.getElementById('phoneVerifyBadge');
            
            if (!phone) {
                alert('Please enter a phone number.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';

            try {
                const response = await fetch('/firebase/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone_number: phone })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('sessionInfo').value = data.sessionInfo || '';
                    document.getElementById('otpSection').style.display = 'block';
                    badge.className = 'badge bg-warning rounded-pill';
                    badge.innerText = 'Pending';
                    alert('OTP sent successfully! Enter the 6-digit code below.');
                } else {
                    alert(data.message || 'Failed to send OTP.');
                    badge.className = 'badge bg-danger rounded-pill';
                    badge.innerText = 'Error';
                }
            } catch (error) {
                alert('Network error. Please try again.');
                console.error(error);
                badge.className = 'badge bg-danger rounded-pill';
                badge.innerText = 'Error';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Verify';
            }
        }

        async function verifyOtp() {
            const phone = document.getElementById('phoneNumber').value.trim();
            const otp = document.getElementById('otpInput').value.trim();
            const sessionInfo = document.getElementById('sessionInfo').value;
            const badge = document.getElementById('phoneVerifyBadge');
            
            if (!phone || !otp) {
                alert('Please enter both phone number and OTP.');
                return;
            }

            if (otp.length !== 6) {
                alert('Please enter a valid 6-digit OTP.');
                return;
            }

            try {
                const response = await fetch('/firebase/verify-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone_number: phone, otp: otp, session_info: sessionInfo })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    badge.className = 'badge bg-success rounded-pill';
                    badge.innerText = 'Verified';
                    document.getElementById('otpSection').style.display = 'none';
                    document.getElementById('phoneNumber').disabled = true;
                    document.getElementById('btnSendOtp').disabled = true;
                    
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                } else {
                    alert(data.message || 'Invalid OTP. Please try again.');
                    badge.className = 'badge bg-danger rounded-pill';
                    badge.innerText = 'Failed';
                }
            } catch (error) {
                alert('Network error. Please try again.');
                console.error(error);
                badge.className = 'badge bg-danger rounded-pill';
                badge.innerText = 'Error';
            }
        }

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

        // Set form style on load
        function initLoginForm() {
            const hiddenInput = document.getElementById('account_type');
            if (!hiddenInput) return;
            const type = hiddenInput.value || 'institute';
            updateLoginFormStyle(type);
            
            // Show change link if type was set via URL
            const changeLinkContainer = document.getElementById('changeTypeContainer');
            if (changeLinkContainer && type && type !== 'institute') {
                changeLinkContainer.style.display = 'block';
            }

            // Clear URL query parameters (like ?type=...) so they are not shown in the URL
            if (window.location.search) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        if (document.readyState === 'loading') {
            window.addEventListener('DOMContentLoaded', initLoginForm);
        } else {
            initLoginForm();
        }

        function updateLoginFormStyle(typeOverride) {
            const selectEl = document.getElementById('account_type_select');
            const hiddenInput = document.getElementById('account_type');
            const type = typeOverride || (selectEl ? selectEl.value : hiddenInput.value) || 'institute';
            const icon = document.getElementById('loginTypeIconI');
            const title = document.getElementById('loginTitle');
            const subtitle = document.getElementById('loginSubtitle');
            const wrapper = document.getElementById('loginTypeIcon');
            const badgeContainer = document.getElementById('loginTypeBadge');
            const formEl = document.querySelector('.login-form');

            hiddenInput.value = type;

            const configs = {
                student: {
                    iconClass: 'fas fa-user-graduate',
                    title: 'Welcome Back, Student',
                    subtitle: 'Sign in to your student portal to continue',
                    bg: '#fff5f2',
                    color: '#ff5532',
                    border: '#ffebe6',
                    label: 'Student Portal',
                    emailPlaceholder: 'Enrollment ID or Email',
                    containerClass: 'type-student'
                },
                staff: {
                    iconClass: 'fas fa-chalkboard-teacher',
                    title: 'Welcome Back, Staff',
                    subtitle: 'Sign in to your staff hub to continue',
                    bg: '#f0fdf4',
                    color: '#10b981',
                    border: '#d1fae5',
                    label: 'Staff Hub',
                    emailPlaceholder: 'Staff Email or Employee Code',
                    containerClass: 'type-staff'
                },
                institute: {
                    iconClass: 'fas fa-user-tie',
                    title: 'Welcome Back, Admin',
                    subtitle: 'Sign in to the management panel to continue',
                    bg: '#fff5f2',
                    color: '#ff5532',
                    border: '#ffebe6',
                    label: 'Admin Panel',
                    emailPlaceholder: 'Admin Email Address',
                    containerClass: 'type-institute'
                }
            };

const cfg = configs[type] || configs['institute'];

             icon.className = cfg.iconClass;
             title.textContent = cfg.title;
             subtitle.textContent = cfg.subtitle;

             wrapper.style.background = cfg.bg;
             wrapper.style.color = cfg.color;
             wrapper.style.borderColor = cfg.border;

             badgeContainer.innerHTML = `
                 <span class="login-type-badge ${type}">
                     <i class="fas fa-circle" style="font-size: 6px;"></i> ${cfg.label}
                 </span>
             `;

             const emailInput = document.getElementById('email');
             const passwordInput = document.getElementById('password');
             const loginLeft = document.querySelector('.login-left');

             if (emailInput) emailInput.placeholder = cfg.emailPlaceholder;
             if (passwordInput) passwordInput.placeholder = 'Enter your password';

             if (formEl) {
                 formEl.classList.remove('type-student', 'type-staff', 'type-institute');
                 formEl.classList.add(cfg.containerClass);
             }
             
             if (loginLeft) {
                 loginLeft.classList.remove('type-student', 'type-staff', 'type-institute');
                 loginLeft.classList.add(cfg.containerClass);
             }
         }
    </script>
</body>
</html>
