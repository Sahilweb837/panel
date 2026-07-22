<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Netcoder ERP | Premium Institute Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #ff5532;
            --primary-dark: #e04423;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.5);
            --glass-shadow: rgba(0, 0, 0, 0.05);
            
            --admin-color: #e04423;
            --staff-color: #10b981;
            --student-color: #6366f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            background-color: #f8fafc;
            position: relative;
        }

        h1, h2, h3, h4, h5, h6, .brand-text {
            font-family: 'Outfit', sans-serif;
        }

        /* Dynamic Mesh Gradient Background */
        .mesh-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -2;
            background: #ffffff;
            overflow: hidden;
        }
        
        .mesh-blob {
            position: absolute;
            filter: blur(90px);
            opacity: 0.6;
            border-radius: 50%;
            animation: moveBlob 20s infinite alternate ease-in-out;
        }

        .blob-1 {
            top: -10%; left: -10%;
            width: 50vw; height: 50vw;
            background: rgba(255, 85, 50, 0.2);
            animation-delay: 0s;
        }
        
        .blob-2 {
            bottom: -20%; right: -10%;
            width: 60vw; height: 60vw;
            background: rgba(99, 102, 241, 0.15);
            animation-delay: -5s;
        }
        
        .blob-3 {
            top: 40%; left: 60%;
            width: 40vw; height: 40vw;
            background: rgba(16, 185, 129, 0.15);
            animation-delay: -10s;
        }

        @keyframes moveBlob {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(10vw, -15vh) scale(1.1); }
            66% { transform: translate(-10vw, 10vh) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }

        /* Dot Pattern Overlay */
        .pattern-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            background-image: radial-gradient(rgba(0,0,0,0.05) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        /* Header Layout */
        header {
            position: absolute;
            top: 0;
            width: 100%;
            padding: 1.5rem 0;
            z-index: 100;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .brand-logo {
            height: 48px;
            object-fit: contain;
            filter: drop-shadow(0 4px 6px rgba(255, 85, 50, 0.2));
            transition: transform 0.3s ease;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        .brand-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.5px;
        }

        .btn-premium {
            background: var(--text-main);
            color: #fff;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .btn-premium:hover {
            background: var(--primary);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 85, 50, 0.25);
        }

        /* Main Split View */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 7rem 0 3rem;
            position: relative;
            z-index: 10;
        }

        .hero-content {
            padding-right: 3rem;
        }
        
        @media (max-width: 991px) {
            .hero-content {
                padding-right: 0;
                text-align: center;
                margin-bottom: 4rem;
            }
        }

        .badge-premium {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(255, 85, 50, 0.1);
            color: var(--primary-dark);
            border: 1px solid rgba(255, 85, 50, 0.2);
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: clamp(2.5rem, 4vw, 4rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--text-main);
        }
        
        .hero-title span {
            background: linear-gradient(135deg, var(--primary) 0%, #ff8a00 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 2.5rem;
            max-width: 540px;
        }

        /* Glassmorphism Hub Card */
        .glass-hub {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 
                0 20px 40px var(--glass-shadow),
                inset 0 0 0 1px rgba(255, 255, 255, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .glass-hub::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--admin-color), var(--staff-color), var(--student-color));
        }

        .hub-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        
        .hub-subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
        }

        /* Portal Cards */
        .portal-card {
            display: flex;
            align-items: center;
            padding: 1.25rem;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.04);
            text-decoration: none;
            color: var(--text-main);
            margin-bottom: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            position: relative;
            z-index: 1;
        }

        .portal-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border-radius: 16px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .portal-card:hover {
            transform: translateY(-5px) scale(1.02);
            border-color: transparent;
        }

        .portal-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1.25rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .portal-info {
            flex: 1;
        }

        .portal-info h4 {
            margin: 0 0 4px 0;
            font-size: 1.1rem;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .portal-info p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .portal-arrow {
            font-size: 1.2rem;
            color: #cbd5e1;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateX(-10px);
        }

        .portal-card:hover .portal-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Color Variations */
        .card-admin .portal-icon { background: rgba(224, 68, 35, 0.1); color: var(--admin-color); }
        .card-admin:hover::after { box-shadow: 0 15px 35px rgba(224, 68, 35, 0.15); }
        .card-admin:hover .portal-info h4, .card-admin:hover .portal-arrow { color: var(--admin-color); }
        .card-admin:hover .portal-icon { background: var(--admin-color); color: #fff; }

        .card-staff .portal-icon { background: rgba(16, 185, 129, 0.1); color: var(--staff-color); }
        .card-staff:hover::after { box-shadow: 0 15px 35px rgba(16, 185, 129, 0.15); }
        .card-staff:hover .portal-info h4, .card-staff:hover .portal-arrow { color: var(--staff-color); }
        .card-staff:hover .portal-icon { background: var(--staff-color); color: #fff; }

        .card-student .portal-icon { background: rgba(99, 102, 241, 0.1); color: var(--student-color); }
        .card-student:hover::after { box-shadow: 0 15px 35px rgba(99, 102, 241, 0.15); }
        .card-student:hover .portal-info h4, .card-student:hover .portal-arrow { color: var(--student-color); }
        .card-student:hover .portal-icon { background: var(--student-color); color: #fff; }

        .floating-illustration {
            animation: float 6s ease-in-out infinite;
            max-width: 100%;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
    </style>
</head>
<body>

    <!-- Background Elements -->
    <div class="mesh-bg">
        <div class="mesh-blob blob-1"></div>
        <div class="mesh-blob blob-2"></div>
        <div class="mesh-blob blob-3"></div>
    </div>
    <div class="pattern-overlay"></div>

    <!-- Header -->
    <header>
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="navbar-brand">
                <img src="{{ asset('image.png') }}" alt="Logo" class="brand-logo" onerror="this.src='https://ui-avatars.com/api/?name=NC&background=ff5532&color=ffffff'">
             </a>
            
            <div class="d-none d-md-flex align-items-center gap-4">
                @auth
                    <a href="{{ url('/home') }}" class="btn-premium">Active Dashboard</a>
                @else
                    <a href="{{ route('login') }}?type=institute" class="btn-premium"><i class="fas fa-shield-alt me-2"></i>Admin Login</a>
                @endauth
            </div>
            
            <div class="d-md-none fs-3 text-dark">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                
                <!-- Left Side: Copy & Illustration -->
                <div class="col-lg-6 hero-content">
                    <div class="badge-premium">
                        <i class="fas fa-star"></i> Premium Portal Experience
                    </div>
                    
                    <h1 class="hero-title">
                        Empower your <br>
                        <span>Educational</span> <br>
                        Ecosystem.
                    </h1>
                    
                    <p class="hero-desc">
                        A centralized, state-of-the-art platform connecting administrators, faculty, and students. Experience seamless operations, attendance tracking, and dynamic learning management.
                    </p>
                    
                    <!-- Include custom illustration or fallback vector -->
                    <div class="floating-illustration mt-4 text-center text-lg-start">
                        @include('partials.hero_illustration')
                    </div>
                </div>

                <!-- Right Side: Login Portals -->
                <div class="col-lg-5 offset-lg-1 mt-5 mt-lg-0">
                    <div class="glass-hub">
                        <h2 class="hub-title">Secure Access</h2>
                        <p class="hub-subtitle">Select your designated portal to continue</p>

                        <!-- Admin Portal -->
                        <a href="{{ route('login') }}?type=institute" class="portal-card card-admin">
                            <div class="portal-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="portal-info">
                                <h4>Management Panel</h4>
                                <p>Oversee courses, staff, and finances</p>
                            </div>
                            <div class="portal-arrow"><i class="fas fa-chevron-right"></i></div>
                        </a>

                        <!-- Staff Portal -->
                        <a href="{{ route('login') }}?type=staff" class="portal-card card-staff">
                            <div class="portal-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="portal-info">
                                <h4>Staff Hub</h4>
                                <p>Manage lectures & log daily updates</p>
                            </div>
                            <div class="portal-arrow"><i class="fas fa-chevron-right"></i></div>
                        </a>

                        <!-- Student Portal -->
                        <a href="{{ route('login') }}?type=student" class="portal-card card-student">
                            <div class="portal-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="portal-info">
                                <h4>Student Portal</h4>
                                <p>Access syllabus, fees & attendance</p>
                            </div>
                            <div class="portal-arrow"><i class="fas fa-chevron-right"></i></div>
                        </a>
                        
                    </div>
                </div>

            </div>
        </div>
    </section>

</body>
</html>
