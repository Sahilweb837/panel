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
             background-color: #ffffff !important;
         }
         .login-left.type-staff {
             background-color: #ffffff !important;
         }
         .login-left.type-institute {
             background-color: #ffffff !important;
         }
         .hero-image {
             width: 100%;
             max-width: 400px;
             height: auto;
             max-height: 40vh;
             object-fit: contain;
             display: block;
             margin: 2rem auto 0;
             z-index: 2;
             position: relative;
             transition: all 0.4s ease;
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
         @keyframes fadeUp {
             from { opacity: 0; transform: translateY(10px); }
             to { opacity: 1; transform: translateY(0); }
         }

         /* DARK MODE OVERRIDES */
         html[data-theme="dark"] body.login-page {
             background-color: #111827 !important;
             color: #f3f4f6 !important;
         }
         html[data-theme="dark"] .login-left,
         html[data-theme="dark"] .login-right {
             background-color: #1f2937 !important;
         }
         html[data-theme="dark"] .glass-card {
             border-color: #374151 !important;
             color: #f3f4f6 !important;
         }
         html[data-theme="dark"] .feature-icon,
         html[data-theme="dark"] .login-type-icon {
             background: #374151 !important;
             border-color: #4b5563 !important;
             color: #ff5532 !important;
         }
         html[data-theme="dark"] .form-input {
             background-color: #374151 !important;
             color: #f3f4f6 !important;
             border-color: #4b5563 !important;
         }
         html[data-theme="dark"] .form-input:focus {
             background-color: #1f2937 !important;
         }
         html[data-theme="dark"] .form-label,
         html[data-theme="dark"] .checkbox-label span,
         html[data-theme="dark"] p {
             color: #9ca3af !important;
         }
         html[data-theme="dark"] .role-btn {
             background-color: #374151 !important;
             border-color: #4b5563 !important;
             color: #d1d5db !important;
         }
         html[data-theme="dark"] .role-btn.active {
             background-color: #ff5532 !important;
             border-color: #ff5532 !important;
             color: #fff !important;
         }
         html[data-theme="dark"] .login-type-badge {
             background: #374151 !important;
             border-color: #4b5563 !important;
         }
         html[data-theme="dark"] .login-title,
         html[data-theme="dark"] h1,
         html[data-theme="dark"] h2,
         html[data-theme="dark"] h3 {
             color: #f9fafb !important;
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
                    <a href="{{ url('/') }}"><img src="{{ asset('image.png') }}" alt="Netcoder ERP" class="login-logo" style="cursor: pointer;"></a>
                </div>
                <h1 class="animate-float" style="margin-bottom: 0;">Netcoder ERP</h1>
                <p class="brand-tagline" id="brandTagline">Premium Institute Management Space</p>
            </div>

            <!-- Dynamic Hero Icon based on Login Type -->
            <div id="login-hero-icon" class="animate-float" style="font-size: 8rem; margin: 3rem auto; text-align: center; transition: all 0.4s ease; z-index: 2; position: relative;">
                <i id="login-hero-icon-i" class="fas fa-building"></i>
            </div>

            <div class="features-list" id="featuresListContainer" style="position: relative; z-index: 2; margin-top: 2rem;">
                <!-- Dynamically populated by JS -->
            </div>

            <div class="login-footer-left" style="position: relative; z-index: 2;">
                <p>&copy; 2026 Netcoder. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-form-wrapper">
                <div class="login-form-header text-center mb-4">
                    <div class="login-type-icon mx-auto" id="loginTypeIcon">
                        <i class="fas fa-user-tie" id="loginTypeIconI"></i>
                    </div>
                    <h2 id="loginTitle">Welcome Back</h2>
                    <p id="loginSubtitle">Sign in to your account to continue</p>
                </div>

                @if(!request()->has('type'))
                <!-- Role Selector Tabs -->
                <div class="role-selector mb-4 d-flex justify-content-between" style="background: #f1f5f9; padding: 6px; border-radius: 12px;">
                    <button type="button" class="btn role-tab active flex-fill" data-type="institute" style="border-radius: 8px; font-weight: 600; padding: 10px; border: none; background: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); color: #1e293b; transition: all 0.3s ease;">
                        <i class="fas fa-building me-1"></i> Admin
                    </button>
                    <button type="button" class="btn role-tab flex-fill mx-1" data-type="staff" style="border-radius: 8px; font-weight: 600; padding: 10px; border: none; background: transparent; color: #64748b; transition: all 0.3s ease;">
                        <i class="fas fa-chalkboard-teacher me-1"></i> Staff
                    </button>
                    <button type="button" class="btn role-tab flex-fill" data-type="student" style="border-radius: 8px; font-weight: 600; padding: 10px; border: none; background: transparent; color: #64748b; transition: all 0.3s ease;">
                        <i class="fas fa-user-graduate me-1"></i> Student
                    </button>
                </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
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
                    tagline: 'Empowering Your Educational Journey',
                    bg: '#fff5f2',
                    color: '#ff5532',
                    border: '#ffebe6',
                    label: 'Student Portal',
                    emailPlaceholder: 'Enrollment ID or Email',
                    containerClass: 'type-student',
                    heroIcon: "fas fa-user-graduate",
                    features: [
                        { icon: 'fas fa-book', title: 'My Courses', desc: 'Access course syllabi and track your assignments' },
                        { icon: 'fas fa-fingerprint', title: 'Attendance', desc: 'Verify your daily biometric logs securely' },
                        { icon: 'fas fa-file-invoice-dollar', title: 'Fee Status', desc: 'View monthly dues and download payment receipts' }
                    ]
                },
                staff: {
                    iconClass: 'fas fa-chalkboard-teacher',
                    title: 'Welcome Back, Staff',
                    subtitle: 'Sign in to your staff hub to continue',
                    tagline: 'Streamlined Faculty Management System',
                    bg: '#f0fdf4',
                    color: '#10b981',
                    border: '#d1fae5',
                    label: 'Staff Hub',
                    emailPlaceholder: 'Staff Email or Employee Code',
                    containerClass: 'type-staff',
                    heroIcon: "fas fa-chalkboard-teacher",
                    features: [
                        { icon: 'fas fa-chalkboard', title: 'Manage Classes', desc: 'Organize your daily lectures and subjects' },
                        { icon: 'fas fa-check-circle', title: 'Attendance', desc: 'View your biometric and manual time records' },
                        { icon: 'fas fa-wallet', title: 'Payroll History', desc: 'Access and download your digital salary slips' }
                    ]
                },
                institute: {
                    iconClass: 'fas fa-user-tie',
                    title: 'Welcome Back, Admin',
                    subtitle: 'Sign in to the management panel to continue',
                    tagline: 'Premium Institute Management Space',
                    bg: '#fff5f2',
                    color: '#ff5532',
                    border: '#ffebe6',
                    label: 'Admin Panel',
                    emailPlaceholder: 'Admin Email Address',
                    containerClass: 'type-institute',
                    heroIcon: "fas fa-building",
                    features: [
                        { icon: 'fas fa-chart-line', title: 'Smart Analytics', desc: 'Track real-time performance & institutional insights' },
                        { icon: 'fas fa-users', title: 'Manage Students', desc: 'End-to-end student lifecycle & active tracking' },
                        { icon: 'fas fa-file-invoice-dollar', title: 'Fee Management', desc: 'Automated invoices, receipts, and smart payments' },
                        { icon: 'fas fa-briefcase', title: 'Staff Management', desc: 'Effortless payroll, attendance, and record keeping' }
                    ]
                }
            };

const cfg = configs[type] || configs['institute'];

             icon.className = cfg.iconClass;
             title.textContent = cfg.title;
             subtitle.textContent = cfg.subtitle;

             wrapper.style.background = cfg.bg;
             wrapper.style.color = cfg.color;
             wrapper.style.borderColor = cfg.border;

             const heroIconWrapper = document.getElementById('login-hero-icon');
             const heroIconI = document.getElementById('login-hero-icon-i');
             if (heroIconWrapper && heroIconI) {
                 heroIconWrapper.style.opacity = 0;
                 setTimeout(() => {
                     heroIconI.className = cfg.heroIcon;
                     heroIconWrapper.style.color = cfg.color;
                     heroIconWrapper.style.opacity = 1;
                 }, 200);
             }

             const taglineEl = document.getElementById('brandTagline');
             if (taglineEl) {
                 taglineEl.style.opacity = 0;
                 setTimeout(() => {
                     taglineEl.textContent = cfg.tagline;
                     taglineEl.style.opacity = 1;
                 }, 200);
             }

             const featuresContainer = document.getElementById('featuresListContainer');
             if (featuresContainer) {
                 featuresContainer.style.opacity = 0;
                 setTimeout(() => {
                     featuresContainer.innerHTML = cfg.features.map(f => `
                        <div class="feature-item glass-card animate-hover-lift" style="opacity: 0; animation: fadeUp 0.4s forwards;">
                            <div class="feature-icon">
                                <i class="${f.icon}"></i>
                            </div>
                            <div>
                                <h3>${f.title}</h3>
                                <p>${f.desc}</p>
                            </div>
                        </div>
                     `).join('');
                     featuresContainer.style.opacity = 1;
                 }, 200);
             }

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

             // Update active tab style
             document.querySelectorAll('.role-tab').forEach(tab => {
                 if (tab.getAttribute('data-type') === type) {
                     tab.classList.add('active');
                     tab.style.background = cfg.color; // Colored background based on type
                     tab.style.boxShadow = '0 4px 12px ' + cfg.color + '40'; // Soft shadow matching color
                     tab.style.color = '#ffffff'; // White text when active
                 } else {
                     tab.classList.remove('active');
                     tab.style.background = 'transparent';
                     tab.style.boxShadow = 'none';
                     tab.style.color = '#64748b';
                 }
             });
         }

         // Tab click listeners
         document.querySelectorAll('.role-tab').forEach(tab => {
             tab.addEventListener('click', function() {
                 const newType = this.getAttribute('data-type');
                 updateLoginFormStyle(newType);
             });
         });
    </script>
</body>
</html>
