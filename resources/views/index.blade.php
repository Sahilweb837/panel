<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Netcoder ERP | Premium Institute Management Space</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --bg-base: #ffffff;
            --bg-surface: #ffffff;
            --bg-accent: #ff5532;
            --bg-accent-hover: #e04423;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --card-border: #e2e8f0;
            
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
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(255, 85, 50, 0.08) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(255, 138, 0, 0.08) 0%, transparent 25%);
        }

        /* Glass Header */
        header {
            padding: 1.5rem 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 85, 50, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand-logo {
            max-height: 40px;
            object-fit: contain;
        }

        .brand-text {
            font-weight: 900;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
            color: var(--bg-accent);
            text-decoration: none;
            margin-left: 0.75rem;
        }

        /* Main Container */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem 1rem;
            position: relative;
            z-index: 10;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 4rem;
            max-width: 700px;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            margin-bottom: 1rem;
            line-height: 1.1;
            background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #ff5532 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .hero-actions {
            margin-top: 2rem;
        }

        .btn-outline-orange {
            color: var(--bg-accent);
            border: 2px solid var(--bg-accent);
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-orange:hover {
            background: var(--bg-accent);
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(255, 85, 50, 0.2);
            transform: translateY(-2px);
        }

        /* 3 Hubs Layout */
        .hubs-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1100px;
            width: 100%;
        }

        .hub-card {
            background: var(--bg-surface);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            text-align: center;
            text-decoration: none;
            color: inherit;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            box-shadow: 0 10px 30px rgba(255, 85, 50, 0.05);
        }

        .hub-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--hub-color);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .hub-card:hover {
            transform: translateY(-8px);
            border-color: var(--hub-color);
            box-shadow: 0 20px 40px rgba(255, 85, 50, 0.15);
        }

        .hub-card:hover::before {
            transform: scaleX(1);
        }

        .hub-icon-wrapper {
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 85, 50, 0.05);
            border: 1px solid rgba(255, 85, 50, 0.1);
            transition: all 0.4s ease;
            overflow: hidden;
            padding: 10px;
        }

        .hub-vector-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .hub-card:hover .hub-icon-wrapper {
            background: rgba(255, 85, 50, 0.1);
            transform: scale(1.1);
            box-shadow: 0 10px 20px var(--hub-shadow);
        }

        .hub-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .hub-desc {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .hub-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--bg-accent);
            background: transparent;
            border: 1px solid var(--bg-accent);
            transition: all 0.3s ease;
            width: 100%;
        }

        .hub-card:hover .hub-btn {
            background: var(--hub-color);
            border-color: var(--hub-color);
            color: #ffffff;
        }

        /* Specific Hub Colors */
        .hub-student { --hub-color: var(--hub-student); --hub-shadow: rgba(255, 118, 87, 0.3); }
        .hub-staff { --hub-color: var(--hub-staff); --hub-shadow: rgba(255, 85, 50, 0.3); }
        .hub-admin { --hub-color: var(--hub-admin); --hub-shadow: rgba(224, 68, 35, 0.3); }

        footer {
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            border-top: 1px solid var(--card-border);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hubs-container {
                grid-template-columns: 1fr;
            }
            .hero-title {
                font-size: 2.5rem;
            }
            .main-content {
                padding: 2rem 1rem;
            }
            .hero-glow {
                width: 300px;
                height: 300px;
            }
            .hub-card {
                max-width: 450px;
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex align-items-center justify-content-center">
            <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
                <img src="{{ asset('image.png') }}" alt="Netcoder ERP" class="brand-logo" onerror="this.src='https://ui-avatars.com/api/?name=NC&background=ff5532&color=ffffff'">
                <span class="brand-text">NETCODER ERP</span>
            </a>
        </div>
    </header>

    <main class="container main-content">
        <div class="hero-section">
            <h1 class="hero-title">Netcoder ERP</h1>
            <p class="hero-subtitle">The all-in-one premium institute management space. Streamline your operations, manage students effectively, and empower your staff with cutting-edge tools.</p>
            <div class="hero-actions">
                <a href="#portals" class="btn-outline-orange">Access Portals <i class="fas fa-arrow-down ms-2"></i></a>
            </div>
        </div>

        <div id="portals" class="hubs-container">
            
            <!-- Student Hub -->
            <a href="{{ route('login') }}?type=student" class="hub-card hub-student">
                <div class="hub-icon-wrapper">
                    <img src="{{ asset('images/student_photo_professional_1783756975009.png') }}" alt="Student Professional" class="hub-vector-img">
                </div>
                <h3 class="hub-title">Student Portal</h3>
                <p class="hub-desc">Access course syllabi, verify biometric attendance logs, review monthly fee installments, and download payment receipts securely.</p>
                <div class="hub-btn">
                    Enter Portal <i class="fas fa-arrow-right ms-1"></i>
                </div>
            </a>

            <!-- Staff Hub -->
            <a href="{{ route('login') }}?type=staff" class="hub-card hub-staff">
                <div class="hub-icon-wrapper">
                    <img src="{{ asset('images/staff_photo_professional_1783756986398.png') }}" alt="Staff Professional" class="hub-vector-img">
                </div>
                <h3 class="hub-title">Staff Hub</h3>
                <p class="hub-desc">Manage lectures, record face attendance, verify daily tasks, and review your salary slips and payroll history with ease.</p>
                <div class="hub-btn">
                    Enter Hub <i class="fas fa-arrow-right ms-1"></i>
                </div>
            </a>

            <!-- Admin Hub -->
            <a href="{{ route('login') }}?type=institute" class="hub-card hub-admin">
                <div class="hub-icon-wrapper">
                    <img src="{{ asset('images/admin_photo_professional_1783756997320.png') }}" alt="Admin Professional" class="hub-vector-img">
                </div>
                <h3 class="hub-title">Management Panel</h3>
                <p class="hub-desc">Configure academic courses, monitor live institutional attendance feeds, manage staff payroll, and oversee financial operations.</p>
                <div class="hub-btn">
                    Enter Panel <i class="fas fa-arrow-right ms-1"></i>
                </div>
            </a>

        </div>
    </main>

    <footer>
        <div class="container" style="position: relative; z-index: 10;">
            &copy; 2026 Netcoder ERP. Designed for modern institutional workflows.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
