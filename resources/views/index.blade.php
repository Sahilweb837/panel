<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Netcoder ERP | Premium Institute Management</title>
    <!-- Modern Typography -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #ff5532;
            --primary-glow: rgba(255, 85, 50, 0.25);
            --bg-base: #f8fafc;
            --bg-surface: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            
            --admin-color: #ff5532;
            --staff-color: #059669;
            --student-color: #4f46e5;

            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(226, 232, 240, 0.8);
            --glass-highlight: rgba(255, 255, 255, 0.5);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Abstract Light Background */
        .bg-canvas {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: -2;
            background: radial-gradient(circle at 50% 0%, #ffffff 0%, #f1f5f9 70%);
            overflow: hidden;
        }

        /* Animated Glow Orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.5;
            animation: floatOrb 20s infinite alternate ease-in-out;
        }

        .orb-1 { width: 600px; height: 600px; background: rgba(255, 85, 50, 0.07); top: -200px; left: -100px; }
        .orb-2 { width: 500px; height: 500px; background: rgba(99, 102, 241, 0.07); bottom: -100px; right: -100px; animation-delay: -5s; }
        .orb-3 { width: 400px; height: 400px; background: rgba(16, 185, 129, 0.05); top: 40%; left: 50%; animation-delay: -10s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(50px, -50px) scale(1.1); }
            66% { transform: translate(-50px, 50px) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }

        /* Grid Overlay for Texture */
        .grid-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-image: linear-gradient(rgba(15, 23, 42, 0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(15, 23, 42, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(ellipse at center, black 0%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 0%, transparent 80%);
        }

        /* Glass Navbar */
        header {
            position: fixed; width: 100%; top: 0; z-index: 100;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1.25rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand img { height: 40px; transition: transform 0.3s ease; }
        .navbar-brand:hover img { transform: scale(1.05); }

        .btn-glow {
            background: linear-gradient(135deg, var(--primary), #d93d1a);
            color: #fff; border: none; padding: 0.6rem 1.5rem;
            border-radius: 50px; font-weight: 600; font-size: 0.95rem;
            box-shadow: 0 0 20px var(--primary-glow);
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(255, 85, 50, 0.4);
            color: #fff;
        }

        /* Hero Layout */
        .hero-section {
            min-height: 100vh; display: flex; align-items: center;
            padding-top: 80px; position: relative; z-index: 10;
        }

        .badge-premium {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 16px; background: rgba(255, 85, 50, 0.06);
            border: 1px solid rgba(255, 85, 50, 0.15); border-radius: 50px;
            font-size: 0.85rem; font-weight: 600; color: var(--primary);
            margin-bottom: 2rem; backdrop-filter: blur(4px);
            animation: slideDown 0.8s ease backwards;
        }
        .badge-premium i { color: var(--primary); }

        .hero-title {
            font-size: clamp(3rem, 5vw, 4.5rem); font-weight: 800;
            line-height: 1.1; margin-bottom: 1.5rem; letter-spacing: -1px;
            animation: slideUp 0.8s ease 0.1s backwards;
        }
        
        .text-gradient {
            background: linear-gradient(to right, #0f172a, #475569);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .text-accent {
            background: linear-gradient(135deg, var(--primary), #ffa38f);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.15rem; color: var(--text-muted); line-height: 1.7;
            max-width: 500px; margin-bottom: 3rem;
            animation: slideUp 0.8s ease 0.2s backwards;
        }

        /* Interactive Portal Cards Container */
        .portal-showcase {
            position: relative;
            animation: fadeIn 1s ease 0.3s backwards;
        }

        /* Glassmorphism Portal Cards */
        .portal-card {
            display: flex; align-items: center; padding: 1.5rem;
            background: var(--glass-bg); border: 1px solid var(--glass-border);
            border-radius: 20px; text-decoration: none; color: var(--text-main);
            margin-bottom: 1.25rem; transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            position: relative; overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .portal-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(45deg, transparent, var(--glass-highlight), transparent);
            transform: translateX(-100%); transition: transform 0.6s ease;
        }

        .portal-card:hover {
            transform: translateY(-5px) scale(1.02);
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(226, 232, 240, 1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
        }
        .portal-card:hover::before { transform: translateX(100%); }

        .portal-icon {
            width: 60px; height: 60px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; margin-right: 1.5rem; flex-shrink: 0;
            background: rgba(15, 23, 42, 0.03);
            border: 1px solid rgba(15, 23, 42, 0.05);
            color: #475569;
            transition: all 0.4s ease;
        }

        .portal-info flex { flex: 1; }
        .portal-info h4 { margin: 0 0 6px 0; font-size: 1.2rem; font-weight: 700; letter-spacing: -0.3px; color: #0f172a; }
        .portal-info p { margin: 0; font-size: 0.9rem; color: var(--text-muted); line-height: 1.4; }

        .portal-arrow {
            font-size: 1.2rem; color: #94a3b8; transition: all 0.3s ease;
            opacity: 0; transform: translateX(-10px);
        }
        .portal-card:hover .portal-arrow { opacity: 1; transform: translateX(0); color: var(--text-main); }

        /* Role-specific styling */
        .card-admin:hover { box-shadow: 0 10px 40px rgba(255, 85, 50, 0.15); }
        .card-admin:hover .portal-icon { background: rgba(255, 85, 50, 0.15); color: var(--admin-color); border-color: rgba(255, 85, 50, 0.3); box-shadow: 0 0 20px rgba(255, 85, 50, 0.2); }

        .card-staff:hover { box-shadow: 0 10px 40px rgba(16, 185, 129, 0.15); }
        .card-staff:hover .portal-icon { background: rgba(16, 185, 129, 0.15); color: var(--staff-color); border-color: rgba(16, 185, 129, 0.3); box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }

        .card-student:hover { box-shadow: 0 10px 40px rgba(99, 102, 241, 0.15); }
        .card-student:hover .portal-icon { background: rgba(99, 102, 241, 0.15); color: var(--student-color); border-color: rgba(99, 102, 241, 0.3); box-shadow: 0 0 20px rgba(99, 102, 241, 0.2); }

        /* Animations */
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        @media (max-width: 991px) {
            .hero-content { text-align: center; margin-bottom: 4rem; }
            .hero-desc { margin: 0 auto 3rem auto; }
            .badge-premium { margin: 0 auto 2rem auto; }
        }
    </style>
</head>
<body>

    <!-- Dynamic Background -->
    <div class="bg-canvas">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>
    <div class="grid-overlay"></div>

    <!-- Glass Header -->
    <header id="header">
        <div class="container d-flex align-items-center justify-content-between">
             <a href="{{ url('/') }}" class="navbar-brand">
                <!-- Fallback text if logo image is missing -->
                <span class="text-dark fw-bold fs-4 d-flex align-items-center gap-2">
                    <i class="fas fa-layer-group text-primary"></i> ERP<span style="font-weight:400; opacity:0.8; color:#475569;">Hub</span>
                </span>
             </a>
            
            <div class="d-none d-md-flex align-items-center gap-4">
                @auth
                    <a href="{{ url('/home') }}" class="btn-glow">Active Dashboard</a>
                @else
                    <a href="{{ url('/superadmin') }}" class="btn-glow"><i class="fas fa-shield-alt me-2"></i>Admin Access</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                
                <!-- Left Content -->
                <div class="col-lg-6 hero-content">
                    <div class="badge-premium">
                        <i class="fas fa-bolt"></i> Premium Management Ecosystem
                    </div>
                    
                    <h1 class="hero-title">
                        <span class="text-gradient">Orchestrate your</span><br>
                        <span class="text-accent">Institution.</span>
                    </h1>
                    
                    <p class="hero-desc">
                        A centralized, state-of-the-art platform connecting administrators, faculty, and students. Experience seamless operations, biometric attendance, and dynamic learning management in one unified workspace.
                    </p>

                    <div class="d-flex flex-wrap gap-4 mt-4 align-items-center" style="animation: slideUp 0.8s ease 0.3s backwards;">
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.9rem;">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i> Secure Access
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.9rem;">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i> Real-time Sync
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.9rem;">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i> Analytics Ready
                        </div>
                    </div>
                </div>

                <!-- Right Content: Portal Selectors -->
                <div class="col-lg-5 offset-lg-1 mt-5 mt-lg-0">
                    <div class="portal-showcase">
                        
                        <div class="text-center text-lg-start mb-4">
                            <h3 class="text-dark fw-bold mb-2">Select Portal</h3>
                            <p class="text-muted small">Choose your designated workspace to continue</p>
                        </div>

                        <!-- Admin Portal -->
                        <a href="{{ url('/superadmin') }}" class="portal-card card-admin">
                            <div class="portal-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="portal-info">
                                <h4>Management Console</h4>
                                <p>Oversee courses, staff, and finances securely.</p>
                            </div>
                            <div class="portal-arrow"><i class="fas fa-arrow-right"></i></div>
                        </a>

                        <!-- Staff Portal -->
                        <a href="{{ url('/staff') }}" class="portal-card card-staff">
                            <div class="portal-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="portal-info">
                                <h4>Staff Workspace</h4>
                                <p>Manage lectures & biometric daily updates.</p>
                            </div>
                            <div class="portal-arrow"><i class="fas fa-arrow-right"></i></div>
                        </a>

                        <!-- Student Portal -->
                        <a href="{{ route('student.dashboard') }}" class="portal-card card-student">
                            <div class="portal-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="portal-info">
                                <h4>Student Dashboard</h4>
                                <p>Access syllabus, track fees & attendance.</p>
                            </div>
                            <div class="portal-arrow"><i class="fas fa-arrow-right"></i></div>
                        </a>
                        
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        // Optional: Add subtle scroll effect to header
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 20) {
                header.style.background = 'rgba(255, 255, 255, 0.9)';
                header.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.05)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.7)';
                header.style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>
