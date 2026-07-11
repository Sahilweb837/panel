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
            --bg-base: #0f172a;
            --bg-surface: #1e293b;
            --bg-accent: #3b82f6;
            --bg-accent-hover: #2563eb;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --card-border: #334155;
            
            --hub-student: #10b981;
            --hub-staff: #8b5cf6;
            --hub-admin: #ef4444;
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
                radial-gradient(circle at 15% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(139, 92, 246, 0.15) 0%, transparent 25%);
        }

        /* Glass Header */
        header {
            padding: 1.5rem 0;
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand-logo {
            max-height: 40px;
            object-fit: contain;
        }

        .brand-text {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
            color: var(--text-primary);
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
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 1rem;
            line-height: 1.1;
            background: linear-gradient(135deg, #f8fafc 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-secondary);
            line-height: 1.6;
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
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        .hub-card:hover::before {
            transform: scaleX(1);
        }

        .hub-icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: rgba(255,255,255,0.03);
            color: var(--hub-color);
            border: 1px solid rgba(255,255,255,0.05);
            transition: all 0.4s ease;
        }

        .hub-card:hover .hub-icon-wrapper {
            background: var(--hub-color);
            color: #ffffff;
            transform: scale(1.1) rotate(5deg);
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
            color: var(--text-primary);
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            width: 100%;
        }

        .hub-card:hover .hub-btn {
            background: var(--hub-color);
            border-color: var(--hub-color);
            color: #ffffff;
        }

        /* Specific Hub Colors */
        .hub-student { --hub-color: var(--hub-student); --hub-shadow: rgba(16, 185, 129, 0.3); }
        .hub-staff { --hub-color: var(--hub-staff); --hub-shadow: rgba(139, 92, 246, 0.3); }
        .hub-admin { --hub-color: var(--hub-admin); --hub-shadow: rgba(239, 68, 68, 0.3); }

        footer {
            padding: 2rem;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.9rem;
            border-top: 1px solid var(--card-border);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hubs-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; }
            .hubs-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
                max-width: 450px;
            }
        }
    </style>
</head>
<body>

    <header>
        <div class="container d-flex align-items-center justify-content-center">
            <img src="{{ asset('image.png') }}" alt="Netcoder ERP" class="brand-logo" onerror="this.src='https://ui-avatars.com/api/?name=NC&background=1e293b&color=f8fafc'">
            <span class="brand-text">NETCODER ERP</span>
        </div>
    </header>

    <main class="main-content">
        <div class="hero-section">
            <h1 class="hero-title">Select Your Gateway</h1>
            <p class="hero-subtitle">Welcome to your ultimate institutional control center. Choose your portal below to access your personalized dashboard space securely.</p>
        </div>

        <div class="hubs-container">
            
            <!-- Student Hub -->
            <a href="{{ route('login') }}?type=student" class="hub-card hub-student">
                <div class="hub-icon-wrapper">
                    <i class="fas fa-user-graduate"></i>
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
                    <i class="fas fa-chalkboard-teacher"></i>
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
                    <i class="fas fa-shield-alt"></i>
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
        <div class="container">
            &copy; 2026 Netcoder ERP. Designed for modern institutional workflows.
        </div>
    </footer>

</body>
</html>
