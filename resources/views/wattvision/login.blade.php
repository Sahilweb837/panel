<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | WattVision Electrical Monitor</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        :root {
            --bg-primary: #121212;
            --surface-card: #1E1E1E;
            --accent-primary: #00E5FF;
            --accent-alert: #FF453A;
            --accent-status: #32D74B;
            --text-primary: #FFFFFF;
            --text-secondary: #98989D;
            --border-sutil: #2C2C2E;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            position: relative;
            padding: 2rem 1rem;
        }

        /* Subtle ambient glow in background */
        .ambient-glow {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 229, 255, 0.08) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
        }

        .login-box {
            background-color: var(--surface-card);
            border: 1px solid var(--border-sutil);
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            z-index: 1;
            position: relative;
            transition: all 0.4s ease;
        }

        .header-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-icon {
            font-size: 3rem;
            color: var(--accent-primary);
            margin-bottom: 1rem;
            text-shadow: 0 0 15px rgba(0, 229, 255, 0.4);
            animation: pulse-glow 3s infinite alternate;
        }

        .brand-name {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }

        .brand-tagline {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Role Selection Cards */
        .role-cards-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .role-card {
            background-color: rgba(255, 255, 255, 0.01);
            border: 1px solid var(--border-sutil);
            border-radius: 16px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .role-card:hover {
            border-color: var(--accent-primary);
            background-color: rgba(0, 229, 255, 0.03);
            transform: translateX(4px);
        }

        .role-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-sutil);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .role-card:hover .role-icon-box {
            background-color: var(--accent-primary);
            color: #121212;
            border-color: var(--accent-primary);
            box-shadow: 0 0 10px rgba(0, 229, 255, 0.3);
        }

        .role-details {
            flex-grow: 1;
        }

        .role-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.15rem;
        }

        .role-desc {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .arrow-indicator {
            color: var(--text-secondary);
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .role-card:hover .arrow-indicator {
            color: var(--accent-primary);
            transform: translateX(3px);
        }

        /* Form Area - hidden initially */
        .credentials-form-container {
            display: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--accent-primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-sutil);
            border-radius: 10px;
            color: var(--text-primary);
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            width: 100%;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            background-color: rgba(255, 255, 255, 0.04);
            box-shadow: 0 0 8px rgba(0, 229, 255, 0.15);
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-input:focus ~ .input-icon {
            color: var(--accent-primary);
        }

        .btn-submit {
            background-color: var(--accent-primary);
            color: #121212;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 0.85rem;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: #00b8cc;
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.4);
            transform: translateY(-1px);
        }

        .selected-role-header {
            margin-bottom: 2rem;
        }

        .selected-role-badge {
            background-color: rgba(0, 229, 255, 0.08);
            border: 1px solid rgba(0, 229, 255, 0.2);
            color: var(--accent-primary);
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
        }

        @keyframes pulse-glow {
            from { text-shadow: 0 0 10px rgba(0, 229, 255, 0.3); }
            to { text-shadow: 0 0 20px rgba(0, 229, 255, 0.6); }
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>

    <main class="login-box">
        <div class="header-section">
            <div class="logo-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <h1 class="brand-name">WattVision</h1>
            <p class="brand-tagline">Sistema de Monitoreo Eléctrico</p>
        </div>

        <!-- Section 1: Role Selector -->
        <div class="role-selection-container" id="roleSelectionSection">
            <h5 class="text-center mb-4 fw-semibold" style="color: var(--text-secondary);">Selecciona tu portal de ingreso</h5>
            <div class="role-cards-container">
                <!-- Admin Card -->
                <div class="role-card" onclick="selectRole('Administrador', 'admin')">
                    <div class="role-icon-box">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="role-details">
                        <h4 class="role-title">Administrador</h4>
                        <p class="role-desc">Acceso a paneles generales, configuración y tarifas.</p>
                    </div>
                    <div class="arrow-indicator">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Staff Card -->
                <div class="role-card" onclick="selectRole('Personal Técnico', 'staff')">
                    <div class="role-icon-box">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="role-details">
                        <h4 class="role-title">Personal / Soporte</h4>
                        <p class="role-desc">Monitoreo de equipos, calibraciones y alertas técnicas.</p>
                    </div>
                    <div class="arrow-indicator">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>

                <!-- Student Card -->
                <div class="role-card" onclick="selectRole('Usuario Final / Estudiante', 'student')">
                    <div class="role-icon-box">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="role-details">
                        <h4 class="role-title">Usuario Final</h4>
                        <p class="role-desc">Consulta de consumos del hogar y costos estimativos (Bs).</p>
                    </div>
                    <div class="arrow-indicator">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Form Credentials (Initially Hidden) -->
        <div class="credentials-form-container" id="credentialsFormSection">
            <a class="back-link" onclick="goBackToRoles()">
                <i class="fas fa-arrow-left"></i> Volver a roles
            </a>
            
            <div class="selected-role-header">
                <span class="selected-role-badge" id="selectedRoleBadge">ADMINISTRADOR</span>
                <h3 class="fw-bold mt-2">Iniciar Sesión</h3>
                <p style="color: var(--text-secondary); font-size: 0.85rem;">Ingresa tus credenciales registradas para acceder al monitor.</p>
            </div>

            <form action="{{ route('wattvision.dashboard') }}" method="GET" class="mt-4">
                <input type="hidden" name="role" id="formRoleField" value="admin">
                
                <div class="form-group">
                    <label class="form-label" for="email">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" class="form-input" placeholder="usuario@wattvision.com" required>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" class="form-input" placeholder="••••••••" required>
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Ingresar al Monitor <i class="fas fa-sign-in-alt ms-1"></i>
                </button>
            </form>
        </div>
    </main>

    <script>
        function selectRole(roleName, roleValue) {
            document.getElementById('roleSelectionSection').style.display = 'none';
            document.getElementById('credentialsFormSection').style.display = 'block';
            document.getElementById('selectedRoleBadge').textContent = roleName;
            document.getElementById('formRoleField').value = roleValue;
            
            // Focus on email input
            setTimeout(() => {
                document.getElementById('email').focus();
            }, 100);
        }

        function goBackToRoles() {
            document.getElementById('credentialsFormSection').style.display = 'none';
            document.getElementById('roleSelectionSection').style.display = 'block';
        }
    </script>
</body>
</html>
