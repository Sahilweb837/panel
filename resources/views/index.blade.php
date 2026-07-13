<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Netcoder ERP | Premium Institute Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --bg-base: #ffffff;
            --bg-accent: #ff5532;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --card-bg: rgba(255, 255, 255, 0.85);
            --border-color: rgba(0,0,0,0.05);
            
            --hub-student: #ff7657;
            --hub-staff: #ff5532;
            --hub-admin: #e04423;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-primary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 85, 50, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 138, 0, 0.05) 0%, transparent 40%);
        }

        /* Animated Bubbles Background */
        .bubbles-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            overflow: hidden;
        }
        .bubble {
            position: absolute;
            background: linear-gradient(135deg, rgba(255, 85, 50, 0.1), rgba(255, 138, 0, 0.05));
            border-radius: 50%;
            animation: floatBubble infinite ease-in-out;
        }
        @keyframes floatBubble {
            0% { transform: translateY(100vh) scale(0.5); opacity: 0; }
            20% { opacity: 1; }
            80% { opacity: 1; }
            100% { transform: translateY(-20vh) scale(1.5); opacity: 0; }
        }

        /* Header */
        header {
            padding: 1.5rem 0;
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 100;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .brand-logo { max-height: 45px; object-fit: contain; }
        .brand-text {
            font-weight: 900;
            font-size: 1.75rem;
            color: var(--bg-accent);
            text-decoration: none;
            margin-left: 1rem;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        /* Split Layout */
        .split-container {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
            padding-top: 5rem; /* Space for header */
            max-width: 1400px;
            margin: 0 auto;
        }

        @media (min-width: 992px) {
            .split-container {
                grid-template-columns: 1.2fr 1fr;
                gap: 4rem;
                padding: 6rem 2rem 2rem 2rem;
                align-items: center;
            }
        }

        /* Left Side - Vector Graphic & Text */
        .left-content {
            padding: 2rem;
            text-align: center;
        }
        @media (min-width: 992px) {
            .left-content { text-align: left; }
        }
        
        .hero-subtitle {
            font-size: 1.35rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .vector-wrapper {
            max-width: 100%;
            margin-top: 2rem;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Right Side - Login Cards Toggle Hub */
        .right-content {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-hub-container {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 2rem;
            padding: 2.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.04);
        }

        .hub-title {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        .hub-desc {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        /* Stacked Toggle Cards */
        .portal-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-radius: 1.25rem;
            border: 1px solid var(--border-color);
            background: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            color: var(--text-primary);
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }

        .portal-card:hover {
            transform: translateY(-3px);
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            border-color: rgba(0,0,0,0.08);
        }

        .portal-vector {
            width: 60px;
            height: 60px;
            margin-right: 1.25rem;
            z-index: 2;
            flex-shrink: 0;
        }

        .portal-info {
            flex: 1;
            z-index: 2;
        }
        .portal-info h4 {
            margin: 0 0 0.4rem 0;
            font-size: 1.15rem;
            font-weight: 700;
            transition: color 0.3s ease;
        }
        .portal-info p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .portal-arrow {
            font-size: 1.2rem;
            color: #cbd5e1;
            transition: color 0.3s ease;
            z-index: 2;
        }

        /* Specific Portal Themes */
        .portal-admin:hover .portal-info h4 { color: #e04423; }
        .portal-admin:hover .portal-arrow { color: #e04423; }

        .portal-staff:hover .portal-info h4 { color: #10b981; }
        .portal-staff:hover .portal-arrow { color: #10b981; }

        .portal-student:hover .portal-info h4 { color: #6366f1; }
        .portal-student:hover .portal-arrow { color: #6366f1; }

    </style>
</head>
<body>

    <!-- Animated Bubbles -->
    <div class="bubbles-bg">
        <div class="bubble" style="left: 10%; width: 80px; height: 80px; animation-duration: 8s; animation-delay: 0s;"></div>
        <div class="bubble" style="left: 30%; width: 120px; height: 120px; animation-duration: 12s; animation-delay: 2s;"></div>
        <div class="bubble" style="left: 60%; width: 60px; height: 60px; animation-duration: 7s; animation-delay: 4s;"></div>
        <div class="bubble" style="left: 85%; width: 150px; height: 150px; animation-duration: 15s; animation-delay: 1s;"></div>
        <div class="bubble" style="left: 45%; width: 40px; height: 40px; animation-duration: 6s; animation-delay: 5s;"></div>
    </div>

    <!-- Header (Logo on one side) -->
    <header>
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
                <img src="{{ asset('image.png') }}" alt="Netcoder ERP Logo" class="brand-logo" onerror="this.src='https://ui-avatars.com/api/?name=NC&background=ff5532&color=ffffff'">
             </a>
            
            <div class="d-none d-md-flex align-items-center gap-3">
                <a href="#portals" class="text-secondary text-decoration-none fw-semibold">Portals</a>
                <a href="{{ route('login') }}?type=institute" class="btn btn-outline-dark" style="border-radius: 50px; font-weight: 600; padding: 0.4rem 1.2rem;">Admin Login</a>
            </div>
            
            <div class="d-md-none text-secondary fs-3">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Split Layout Main Area -->
    <main class="split-container">
        
        <!-- Left Side: Subtitle + Vector Graphic -->
        <div class="left-content">
            <p class="hero-subtitle">The all-in-one premium institute management space. Streamline operations, manage students effectively, and empower your staff with cutting-edge tools.</p>
            
            <div class="vector-wrapper">
                @include('partials.hero_illustration')
            </div>
        </div>

        <!-- Right Side: Toggle Login Cards Hub -->
        <div class="right-content">
            <div class="login-hub-container">
                <h2 class="hub-title">Access Portals</h2>
                <p class="hub-desc">Select your designated portal to securely log in and access your dashboard.</p>

                <!-- Admin Card -->
                <a href="{{ route('login') }}?type=institute" class="portal-card portal-admin">
                    <div class="portal-vector">
                        <svg viewBox="0 0 100 100" class="w-100 h-100">
                            <rect x="10" y="10" width="80" height="80" rx="12" fill="#e04423" opacity="0.1"/>
                            <rect x="22" y="25" width="25" height="25" rx="6" fill="#e04423"/>
                            <rect x="53" y="25" width="25" height="25" rx="6" fill="#e04423" opacity="0.7"/>
                            <rect x="22" y="58" width="56" height="18" rx="6" fill="#e04423" opacity="0.5"/>
                            <circle cx="34.5" cy="37.5" r="4" fill="#fff"/>
                        </svg>
                    </div>
                    <div class="portal-info">
                        <h4>Management Panel</h4>
                        <p>Configure courses & financial operations</p>
                    </div>
                    <div class="portal-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Staff Card -->
                <a href="{{ route('login') }}?type=staff" class="portal-card portal-staff">
                    <div class="portal-vector">
                        <svg viewBox="0 0 100 100" class="w-100 h-100">
                            <rect x="10" y="10" width="80" height="80" rx="12" fill="#10b981" opacity="0.1"/>
                            <circle cx="50" cy="35" r="16" fill="#10b981"/>
                            <path d="M22 80 C22 55 78 55 78 80" fill="#10b981" opacity="0.8"/>
                        </svg>
                    </div>
                    <div class="portal-info">
                        <h4>Staff Hub</h4>
                        <p>Manage lectures & record attendance</p>
                    </div>
                    <div class="portal-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Student Card -->
                <a href="{{ route('login') }}?type=student" class="portal-card portal-student">
                    <div class="portal-vector">
                        <svg viewBox="0 0 100 100" class="w-100 h-100">
                            <rect x="10" y="10" width="80" height="80" rx="12" fill="#6366f1" opacity="0.1"/>
                            <path d="M18 45 L50 28 L82 45 L50 62 Z" fill="#6366f1"/>
                            <path d="M26 51 L50 63 L74 51 L74 68 C74 78 26 78 26 68 Z" fill="#6366f1" opacity="0.8"/>
                            <rect x="78" y="45" width="4" height="20" fill="#6366f1" rx="2"/>
                            <circle cx="80" cy="68" r="5" fill="#4f46e5"/>
                        </svg>
                    </div>
                    <div class="portal-info">
                        <h4>Student Portal</h4>
                        <p>View attendance logs & fee receipts</p>
                    </div>
                    <div class="portal-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                @auth
                <div class="mt-4 text-center">
                    <a href="{{ url('/home') }}" class="btn btn-dark px-4 py-2" style="border-radius: 50px; font-weight: 600;">Go to Active Dashboard</a>
                </div>
                @endauth
            </div>
        </div>

    </main>

</body>
</html>
