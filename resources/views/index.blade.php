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
            --bg-primary: #f8fafc;
            --bg-accent: #ff5532;
            --bg-accent-hover: #e04423;
            --text-dark: #1e293b;
            --text-light: #ffffff;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-dark);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            overflow-x: hidden;
        }

        /* Split-screen layout container */
        .split-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Left side - Branding & Showcase (Pure White) */
        .brand-panel {
            flex: 1;
            background: #ffffff;
            color: #1e293b;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }

        /* Decorative orange SVG vectors background */
        .brand-panel::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(255, 85, 50, 0.04);
            border-radius: 50%;
            top: -100px;
            left: -100px;
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 85, 50, 0.02);
            border-radius: 80px;
            bottom: -200px;
            right: -100px;
            transform: rotate(45deg);
        }

        .brand-header {
            z-index: 1;
        }

        .brand-logo-wrapper {
            padding: 1.25rem;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 2rem;
        }

        .brand-logo {
            max-height: 70px;
            object-fit: contain;
        }

        .brand-showcase {
            z-index: 1;
            margin: auto 0;
            max-width: 640px;
            position: relative;
        }

        .brand-title {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .brand-description {
            font-size: 1.2rem;
            color: #1e293b;
            line-height: 1.6;
        }

        .brand-footer {
            z-index: 2;
            position: relative;
            font-size: 0.9rem;
            color: #64748b;
        }

        /* Right side - Gateways/Card selectors (Clean White) */
        .gateways-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 5rem;
            background-color: var(--bg-primary);
        }

        .gateways-header {
            margin-bottom: 3rem;
            max-width: 480px;
        }

        .gateways-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-dark);
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
        }

        .gateways-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.5;
        }

        .cards-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            max-width: 520px;
        }

        .portal-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 1.75rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .portal-card:hover {
            transform: translateY(-4px) scale(1.01);
            border-color: var(--bg-accent);
            box-shadow: 0 20px 30px rgba(255, 85, 50, 0.08);
        }

        .portal-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background-color: #fff5f2;
            color: var(--bg-accent);
            border: 1px solid #ffebe6;
            transition: all 0.3s ease;
        }

        .portal-card:hover .portal-icon-wrapper {
            background-color: var(--bg-accent);
            color: var(--text-light);
            border-color: var(--bg-accent);
            box-shadow: 0 0 15px rgba(255, 85, 50, 0.3);
        }

        .portal-details {
            flex-grow: 1;
        }

        .portal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
            transition: color 0.2s ease;
        }

        .portal-card:hover .portal-title {
            color: var(--bg-accent);
        }

        .portal-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.4;
            margin: 0;
        }

        .portal-arrow {
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .portal-card:hover .portal-arrow {
            color: var(--bg-accent);
            transform: translateX(4px);
        }

        /* Responsive styling */
        @media (max-width: 1024px) {
            .split-container {
                flex-direction: column;
            }
            .brand-panel {
                padding: 3rem;
                min-height: 350px;
            }
            .brand-title {
                font-size: 2.5rem;
            }
            .gateways-panel {
                padding: 3rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Side: Orange Brand Showcase -->
        <section class="brand-panel">
            <!-- Custom Orange SVG Vectors -->
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

            <div class="brand-header" style="position: relative; z-index: 2;">
                <div class="brand-logo-wrapper">
                    <img src="{{ asset('image.png') }}" alt="Netcoder ERP" class="brand-logo">
                </div>
            </div>

            <div class="brand-showcase">
                <h1 class="brand-title">Netcoder ERP</h1>
                <p class="brand-description">Welcome to your ultimate institutional control center. Choose your gateway to access your dashboard and manage files, classes, and balances.</p>
            </div>

            <div class="brand-footer">
                <p>&copy; 2026 Netcoder ERP. Designed for modern institutional workflows.</p>
            </div>
        </section>

        <!-- Right Side: White Gateways Cards -->
        <main class="gateways-panel">
            <div class="gateways-header">
                <h2 class="gateways-title">Institutional Portals</h2>
                <p class="gateways-subtitle">Select your entry role gateway below to access your personalized dashboard space.</p>
            </div>

            <div class="cards-list">
                <!-- Student Card -->
                <a href="{{ route('login') }}?type=student" class="portal-card">
                    <div class="portal-icon-wrapper">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="portal-details">
                        <h3 class="portal-title">Student Space</h3>
                        <p class="portal-desc">Access course syllabi, verify biometric attendance logs, review monthly fees, and download receipts.</p>
                    </div>
                    <div class="portal-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Staff Card -->
                <a href="{{ route('login') }}?type=staff" class="portal-card">
                    <div class="portal-icon-wrapper">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="portal-details">
                        <h3 class="portal-title">Staff Hub</h3>
                        <p class="portal-desc">Manage lectures, record face attendance, and review salaries and payroll history.</p>
                    </div>
                    <div class="portal-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <!-- Management Card -->
                <a href="{{ route('login') }}?type=institute" class="portal-card">
                    <div class="portal-icon-wrapper">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="portal-details">
                        <h3 class="portal-title">Management Panel</h3>
                        <p class="portal-desc">Configure academic courses, upload syllabi, review live attendance logs, and record fee payments.</p>
                    </div>
                    <div class="portal-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
