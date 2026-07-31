@php
    $primaryColor = \App\Models\Setting::get('primary_color', '#ff5532');
    
    // Parse hex to RGB
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
    
    // Calculate dark/light variants
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
    
    $brandDark = $adjustBrightness($primaryColor, -25);
    $brandLight = 'rgba(' . implode(',', [$r, $g, $b]) . ', 0.06)';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('institute_name', 'Netcoder') }} - Premium Institute Management</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --brand-primary: {{ $primaryColor }};
            --brand-dark: {{ $brandDark }};
            --brand-light: {{ $brandLight }};
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --font-main: 'Outfit', sans-serif;
        }

        body {
            font-family: var(--font-main);
            color: var(--text-dark);
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .navbar-brand img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .nav-link {
            font-weight: 600;
            color: var(--text-dark) !important;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--brand-primary) !important;
        }

        .btn-brand {
            background: var(--brand-primary);
            color: white;
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 85, 50, 0.2);
        }

        /* Hero Section */
        .hero-section {
            padding: 180px 0 100px 0;
            background: linear-gradient(135deg, var(--brand-light) 0%, var(--surface) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-badge {
            background: rgba(255, 85, 50, 0.1);
            color: var(--brand-primary);
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -1.5px;
        }

        .hero-title span {
            color: var(--brand-primary);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 600px;
            line-height: 1.6;
        }

        /* Portals Section */
        .portals-section {
            padding: 100px 0;
            background: var(--surface);
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .section-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .portal-card {
            background: var(--surface);
            border-radius: 24px;
            border: 1px solid var(--border);
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: inherit;
        }

        .portal-card:hover {
            transform: translateY(-10px);
            border-color: var(--brand-primary);
            box-shadow: 0 20px 40px rgba(255, 85, 50, 0.08);
        }

        .portal-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 2rem auto;
            transition: all 0.3s ease;
        }

        .portal-admin .portal-icon { background: rgba(255, 85, 50, 0.1); color: #ff5532; }
        .portal-staff .portal-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .portal-student .portal-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

        .portal-card:hover .portal-icon {
            transform: scale(1.1);
        }

        .portal-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .portal-card p {
            color: var(--text-muted);
            flex-grow: 1;
            margin-bottom: 2rem;
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: var(--bg-light);
        }

        .feature-item {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--brand-light);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .feature-content h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .feature-content p {
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: var(--brand-primary);
            color: white;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }
        
        .cta-btn {
            background: white;
            color: var(--brand-primary);
            font-weight: 800;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .cta-btn:hover {
            transform: scale(1.05);
            color: var(--brand-primary);
        }

        /* Footer */
        .footer {
            background: var(--surface);
            padding: 4rem 0 2rem 0;
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/" class="navbar-brand m-0 p-0">
                <img src="{{ \App\Models\Setting::get('logo_url', 'https://www.netcoder.in/images/logo.png') }}" alt="{{ \App\Models\Setting::get('institute_name', 'Netcoder') }} Logo" style="height: 48px; max-width: 220px; object-fit: contain;">
            </a>
            <div class="d-none d-md-flex align-items-center">
                <a href="#features" class="nav-link">Features</a>
                <a href="#portals" class="nav-link">Portals</a>
                <a href="#contact" class="nav-link">Contact</a>
            </div>
            <div>
                <a href="{{ route('login') }}" class="btn-brand">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-badge"><i class="fas fa-star me-2"></i>#1 Premium Management Software</div>
                    <h1 class="hero-title">Transform Your <br><span>Institution</span> Today</h1>
                    <p class="hero-subtitle">Netcoder provides an all-in-one ecosystem for managing students, staff, attendance, payroll, and fees effortlessly.</p>
                    <div class="d-flex gap-3">
                        <a href="#portals" class="btn-brand" style="padding: 14px 32px; font-size: 1.1rem;">
                            Get Started <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#features" class="btn btn-light" style="padding: 14px 32px; font-size: 1.1rem; font-weight: 700; border-radius: 50px; color: var(--text-dark);">
                            Explore Features
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="{{ asset('images/admin_photo_professional_1783756997320.png') }}" alt="Dashboard Preview" class="img-fluid rounded-4 shadow-lg" style="border: 1px solid var(--border); max-height: 500px; object-fit: cover;">
                </div>
            </div>
        </div>
    </section>

    <!-- Portals Section -->
    <section id="portals" class="portals-section">
        <div class="container">
            <div class="section-header">
                <h2>Access Your Workspace</h2>
                <p>Select your dedicated portal to securely log in and access your personalized dashboard.</p>
            </div>
            
            <div class="row g-4">
                <!-- Admin -->
                <div class="col-md-4">
                    <a href="{{ route('login') }}?type=institute" class="portal-card portal-admin">
                        <div class="portal-icon"><i class="fas fa-building"></i></div>
                        <h3>Admin Portal</h3>
                        <p>Manage entire institute operations, view deep analytics, and control all staff and student records globally.</p>
                        <span class="btn-brand mt-auto w-100 justify-content-center">Login as Admin</span>
                    </a>
                </div>
                <!-- Staff -->
                <div class="col-md-4">
                    <a href="{{ route('login') }}?type=staff" class="portal-card portal-staff">
                        <div class="portal-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                        <h3>Staff Portal</h3>
                        <p>Access your classes, mark student attendance, view assigned tasks, and manage your monthly payroll.</p>
                        <span class="btn-brand mt-auto w-100 justify-content-center" style="background: #10b981;">Login as Staff</span>
                    </a>
                </div>
                <!-- Student -->
                <div class="col-md-4">
                    <a href="{{ route('login') }}?type=student" class="portal-card portal-student">
                        <div class="portal-icon"><i class="fas fa-user-graduate"></i></div>
                        <h3>Student Portal</h3>
                        <p>Track your academic progress, download syllabus, view attendance fines, and pay fee installments securely.</p>
                        <span class="btn-brand mt-auto w-100 justify-content-center" style="background: #3b82f6;">Login as Student</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1.5rem;">Powerful Features Built for Modern Needs</h2>
                    <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 2rem;">Everything you need to run your institution efficiently. From biometric syncing to automated payroll calculations.</p>
                    <a href="{{ route('login') }}" class="btn-brand">Try It Now</a>
                </div>
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-fingerprint"></i></div>
                                <div class="feature-content">
                                    <h4>Biometric Sync</h4>
                                    <p>Direct integration with ZKTeco devices for automated real-time staff and student attendance.</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                <div class="feature-content">
                                    <h4>Fee Management</h4>
                                    <p>Automated invoice generation, partial payment tracking, and due amount notifications.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-wallet"></i></div>
                                <div class="feature-content">
                                    <h4>Smart Payroll</h4>
                                    <p>Automatically deduct salaries based on late arrivals or absent days synced from biometrics.</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                                <div class="feature-content">
                                    <h4>Role Security</h4>
                                    <p>Advanced permission control for Sub-Admins, ensuring data privacy and secure access.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Digitize Your Institute?</h2>
            <p class="mb-5" style="font-size: 1.25rem; opacity: 0.9;">Join modern institutes using Netcoder to streamline their operations.</p>
            <a href="{{ route('login') }}" class="cta-btn">Access Dashboard <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-4 mb-4">
                    <img src="{{ \App\Models\Setting::get('logo_url', 'https://www.netcoder.in/images/logo.png') }}" alt="{{ \App\Models\Setting::get('institute_name', 'Netcoder') }} Logo" style="height: 48px; max-width: 100%; object-fit: contain; margin-bottom: 1.5rem;">
                    <p style="color: var(--text-muted);">Premium institution management software designed for speed, security, and scalability.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 style="font-weight: 700; margin-bottom: 1.25rem;">Quick Links</h5>
                    <ul class="list-unstyled" style="line-height: 2;">
                        <li><a href="#features" style="color: var(--text-muted); text-decoration: none;">Features</a></li>
                        <li><a href="#portals" style="color: var(--text-muted); text-decoration: none;">Portals</a></li>
                        <li><a href="{{ route('login') }}" style="color: var(--text-muted); text-decoration: none;">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 style="font-weight: 700; margin-bottom: 1.25rem;">Contact</h5>
                    <ul class="list-unstyled" style="line-height: 2; color: var(--text-muted);">
                        <li><i class="fas fa-envelope me-2"></i> info@netcoder.in</li>
                        <li><i class="fas fa-phone me-2"></i> Support Team</li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 style="font-weight: 700; margin-bottom: 1.25rem;">Socials</h5>
                    <div class="d-flex gap-3">
                        <a href="#" style="color: var(--brand-primary); font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: var(--brand-primary); font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: var(--brand-primary); font-size: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center pt-4" style="border-top: 1px solid var(--border); color: var(--text-muted); font-size: 0.9rem;">
                &copy; {{ date('Y') }} Netcoder. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar blur effect on scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.navbar-custom');
            if (window.scrollY > 50) {
                nav.style.boxShadow = '0 4px 20px rgba(0,0,0,0.05)';
            } else {
                nav.style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>
