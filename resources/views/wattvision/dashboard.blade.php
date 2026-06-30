<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>WattVision | Control de Monitoreo Eléctrico</title>
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
            --accent-gradient-fill: #30D158;
            --text-primary: #FFFFFF;
            --text-secondary: #98989D;
            --border-sutil: #2C2C2E;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding: 24px;
            overflow-x: hidden;
        }

        /* Títulos */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .dashboard-header {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-title {
            font-size: 24px;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-title i {
            color: var(--accent-primary);
            text-shadow: 0 0 10px rgba(0, 229, 255, 0.4);
        }

        .live-badge {
            background-color: rgba(50, 215, 75, 0.1);
            border: 1px solid rgba(50, 215, 75, 0.3);
            color: var(--accent-status);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-status);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px var(--accent-status);
            animation: pulse-dot 1.5s infinite;
        }

        /* Tarjetas */
        .premium-card {
            background-color: var(--surface-card);
            border: 1px solid var(--border-sutil);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            transition: border-color 0.3s ease;
            height: 100%;
        }

        .premium-card:hover {
            border-color: rgba(0, 229, 255, 0.2);
        }

        .kpi-title {
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .kpi-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .kpi-unit {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .kpi-footer {
            margin-top: 12px;
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kpi-footer.positive {
            color: var(--accent-status);
        }

        .kpi-footer.negative {
            color: var(--accent-alert);
        }

        /* Alertas */
        .alert-box {
            background-color: #3A1C1C;
            border-left: 4px solid var(--accent-alert);
            border-radius: 0 8px 8px 0;
            padding: 12px 16px;
            margin-bottom: 16px;
            color: var(--accent-alert);
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-box i {
            font-size: 16px;
            margin-top: 2px;
        }

        .alert-box-title {
            font-weight: 600;
            margin-bottom: 2px;
            color: #FFFFFF;
        }

        /* Tablas */
        .premium-table {
            color: var(--text-primary);
        }

        .premium-table tbody tr {
            border-bottom: 1px solid var(--border-sutil);
            transition: background-color 0.2s ease;
        }

        .premium-table tbody tr:hover {
            background-color: #252525;
        }

        .premium-table th {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border-sutil);
            padding: 12px 8px;
        }

        .premium-table td {
            padding: 14px 8px;
            font-size: 14px;
        }

        /* Switch Controles */
        .appliance-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-sutil);
            border-radius: 12px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }

        .appliance-control:hover {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .appliance-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .appliance-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .appliance-control.active .appliance-icon {
            background-color: var(--accent-primary);
            color: #121212;
            box-shadow: 0 0 10px rgba(0, 229, 255, 0.3);
        }

        .appliance-name {
            font-size: 14px;
            font-weight: 600;
        }

        .appliance-load {
            font-size: 11px;
            color: var(--text-secondary);
            font-family: 'JetBrains Mono', monospace;
        }

        /* Custom Switch */
        .form-switch .form-check-input {
            width: 44px;
            height: 22px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-sutil);
            cursor: pointer;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        @keyframes pulse-dot {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        /* Utility classes */
        .text-neon { color: var(--accent-primary); }
        .text-alert { color: var(--accent-alert); }
        .text-status { color: var(--accent-status); }
    </style>
</head>
<body>

    <header class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="fas fa-bolt"></i> WattVision <span style="font-weight: 300; font-size: 20px; color: var(--text-secondary);">/ Monitoreo Eléctrico</span>
        </h1>
        <div class="d-flex align-items-center gap-3">
            <div class="live-badge">
                <span class="live-dot"></span> EN VIVO
            </div>
            <a href="{{ route('wattvision.login') }}" class="btn btn-outline-secondary btn-sm rounded-pill text-white border-secondary px-3">
                <i class="fas fa-sign-out-alt me-1"></i> Salir
            </a>
        </div>
    </header>

    <!-- Row 1: KPI Panels (3 columns, top section) -->
    <div class="row g-4 mb-4">
        <!-- KPI 1: Active Load (Watts) -->
        <div class="col-12 col-md-4">
            <div class="premium-card">
                <div class="kpi-title">Carga Activa Actual</div>
                <div class="kpi-value" id="kpiActiveLoad">
                    850 <span class="kpi-unit">W</span>
                </div>
                <div class="kpi-footer text-neon" id="kpiLoadFooter">
                    <i class="fas fa-plug"></i> Simulación en consumo doméstico
                </div>
            </div>
        </div>

        <!-- KPI 2: Total Consumption (kWh) -->
        <div class="col-12 col-md-4">
            <div class="premium-card">
                <div class="kpi-title">Consumo Mensual Acumulado</div>
                <div class="kpi-value" id="kpiTotalConsumption">
                    124.50 <span class="kpi-unit">kWh</span>
                </div>
                <div class="kpi-footer positive">
                    <i class="fas fa-arrow-down"></i> -4.2% respecto al mes anterior
                </div>
            </div>
        </div>

        <!-- KPI 3: Total Cost (Bs) -->
        <div class="col-12 col-md-4">
            <div class="premium-card">
                <div class="kpi-title">Costo Estimado Factura</div>
                <div class="kpi-value" id="kpiTotalCost">
                    118.27 <span class="kpi-unit">Bs.</span>
                </div>
                <div class="kpi-footer" style="color: var(--text-secondary);">
                    <i class="fas fa-calculator"></i> Tasa promedio: 0.95 Bs. / kWh
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Main Chart (8 cols) and Sidebar (4 cols) -->
    <div class="row g-4">
        
        <!-- Main Area Graph (8 columns) -->
        <div class="col-12 col-lg-8">
            <div class="premium-card d-flex flex-column justify-content-between" style="min-height: 480px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0"><i class="fas fa-chart-area text-neon me-2"></i>Curva de Consumo Reciente (Segundos)</h5>
                    <span class="small text-muted" style="font-family: 'JetBrains Mono', monospace;">Escala: Watts / Tiempo</span>
                </div>
                <div class="flex-grow-1" style="position: relative; height: 350px;">
                    <canvas id="consumptionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Alerts & Live Simulator Panel (4 columns) -->
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-4">
                
                <!-- Simulation Appliance Control Panel -->
                <div class="premium-card">
                    <h5 class="fw-bold mb-3"><i class="fas fa-sliders-h text-neon me-2"></i>Simulador de Equipos</h5>
                    <p class="text-muted small mb-4">Enciende o apaga electrodomésticos para observar cómo impactan en la carga activa, los costos estimativos en Bs. y los picos en el gráfico.</p>
                    
                    <div class="appliance-list">
                        <!-- Appliance 1: Ducha Eléctrica (High power!) -->
                        <div class="appliance-control" id="control_ducha">
                            <div class="appliance-info">
                                <div class="appliance-icon"><i class="fas fa-shower"></i></div>
                                <div>
                                    <div class="appliance-name">Ducha Eléctrica</div>
                                    <div class="appliance-load">Consumo: +3500W</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" onchange="toggleAppliance('ducha', 3500, this)">
                            </div>
                        </div>

                        <!-- Appliance 2: Microondas -->
                        <div class="appliance-control" id="control_micro">
                            <div class="appliance-info">
                                <div class="appliance-icon"><i class="fas fa-temperature-high"></i></div>
                                <div>
                                    <div class="appliance-name">Horno Microondas</div>
                                    <div class="appliance-load">Consumo: +1200W</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" onchange="toggleAppliance('micro', 1200, this)">
                            </div>
                        </div>

                        <!-- Appliance 3: Refrigerador (Base load) -->
                        <div class="appliance-control active" id="control_refri">
                            <div class="appliance-info">
                                <div class="appliance-icon"><i class="fas fa-snowflake"></i></div>
                                <div>
                                    <div class="appliance-name">Refrigerador</div>
                                    <div class="appliance-load">Consumo: +250W</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" checked onchange="toggleAppliance('refri', 250, this)">
                            </div>
                        </div>

                        <!-- Appliance 4: Focos LED (Minimal load) -->
                        <div class="appliance-control active" id="control_luces">
                            <div class="appliance-info">
                                <div class="appliance-icon"><i class="fas fa-lightbulb"></i></div>
                                <div>
                                    <div class="appliance-name">Focos LED (Habitación)</div>
                                    <div class="appliance-load">Consumo: +60W</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" checked onchange="toggleAppliance('luces', 60, this)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Alert Panel -->
                <div class="premium-card">
                    <h5 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle text-alert me-2"></i>Alertas & Vampiros</h5>
                    <div id="alertsContainer">
                        <!-- Dynamic alerts will appear here -->
                        <div class="alert-box" id="vampireAlert">
                            <i class="fas fa-skull-crossbones"></i>
                            <div>
                                <div class="alert-box-title">Carga Vampiro Detectada</div>
                                <span>Carga pasiva persistente mayor a 100W durante la madrugada. Apague cargadores y TVs en standby.</span>
                            </div>
                        </div>
                        
                        <div class="alert-box" id="highUsageAlert" style="display: none;">
                            <i class="fas fa-fire"></i>
                            <div>
                                <div class="alert-box-title">Alerta: Consumo Crítico</div>
                                <span>La carga activa supera los 3000W. Alta probabilidad de sobrecalentamiento de disyuntores.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Row 3: Detail Consumption Table -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="premium-card">
                <h5 class="fw-bold mb-4"><i class="fas fa-history text-neon me-2"></i>Historial de Lecturas Recientes</h5>
                <div class="table-responsive">
                    <table class="table premium-table mb-0">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Carga Activa (W)</th>
                                <th>Consumo Acumulado (kWh)</th>
                                <th>Costo Estimado (Bs.)</th>
                                <th>Estado del Sistema</th>
                            </tr>
                        </thead>
                        <tbody id="readingsTableBody">
                            <!-- Rows populated dynamically by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Default Active appliances configuration
        let activeLoad = 310; // Fridge (250) + Lights (60)
        let accumulatedKWh = 124.50;
        const ratePerKWh = 0.95; // 0.95 Bolivianos

        // Active appliances track
        const applianceStates = {
            ducha: false,
            micro: false,
            refri: true,
            luces: true
        };

        // UI Element bindings
        const kpiActiveLoad = document.getElementById('kpiActiveLoad');
        const kpiTotalConsumption = document.getElementById('kpiTotalConsumption');
        const kpiTotalCost = document.getElementById('kpiTotalCost');
        const highUsageAlert = document.getElementById('highUsageAlert');
        const readingsTableBody = document.getElementById('readingsTableBody');

        // Toggle appliances
        function toggleAppliance(applianceKey, wattage, checkbox) {
            const controlDiv = document.getElementById(`control_${applianceKey}`);
            applianceStates[applianceKey] = checkbox.checked;

            if (checkbox.checked) {
                activeLoad += wattage;
                controlDiv.classList.add('active');
            } else {
                activeLoad -= wattage;
                controlDiv.classList.remove('active');
            }

            // Check high load alerts
            if (activeLoad > 3000) {
                highUsageAlert.style.display = 'flex';
            } else {
                highUsageAlert.style.display = 'none';
            }

            updateKPIs();
        }

        function updateKPIs() {
            kpiActiveLoad.innerHTML = `${activeLoad} <span class="kpi-unit">W</span>`;
            kpiTotalConsumption.innerHTML = `${accumulatedKWh.toFixed(2)} <span class="kpi-unit">kWh</span>`;
            kpiTotalCost.innerHTML = `${(accumulatedKWh * ratePerKWh).toFixed(2)} <span class="kpi-unit">Bs.</span>`;
        }

        // Initialize Chart.js
        const ctx = document.getElementById('consumptionChart').getContext('2d');
        
        // Custom Area Gradient
        const cianGradient = ctx.createLinearGradient(0, 0, 0, 300);
        cianGradient.addColorStop(0, 'rgba(0, 229, 255, 0.3)');
        cianGradient.addColorStop(1, 'rgba(48, 209, 88, 0.0)'); // transition to green/transparent

        const dataPoints = Array(20).fill(activeLoad);
        const labels = Array(20).fill('');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Watts',
                    data: dataPoints,
                    borderColor: '#00E5FF',
                    borderWidth: 2,
                    backgroundColor: cianGradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        min: 0,
                        max: 6000,
                        grid: {
                            color: '#2C2C2E'
                        },
                        ticks: {
                            color: '#98989D'
                        }
                    }
                }
            }
        });

        // Real-time loop
        setInterval(() => {
            // Add slight randomness to load based on active appliances
            let fluctuation = Math.floor(Math.random() * 15) - 7;
            let currentInstantLoad = Math.max(0, activeLoad + fluctuation);

            // Increment kWh slightly
            let increment = (currentInstantLoad / 3600) * 0.05; // 0.05 seconds simulate faster time for display
            accumulatedKWh += increment;

            updateKPIs();

            // Push to chart
            chart.data.datasets[0].data.shift();
            chart.data.datasets[0].data.push(currentInstantLoad);
            chart.update();

            // Add reading to table every 4 loops
            addTableReading(currentInstantLoad);
        }, 1000);

        let tableCounter = 0;
        function addTableReading(loadValue) {
            tableCounter++;
            if (tableCounter % 4 !== 0) return;

            const timeNow = new Date().toLocaleTimeString('es-BO', { hour12: false });
            const costNow = (accumulatedKWh * ratePerKWh).toFixed(2);
            const statusLabel = loadValue > 3000 ? '<span class="text-alert fw-bold"><i class="fas fa-exclamation-circle"></i> Crítico</span>' : '<span class="text-status fw-bold"><i class="fas fa-check-circle"></i> Normal</span>';

            const row = `
                <tr>
                    <td style="font-family: 'JetBrains Mono', monospace;">${timeNow}</td>
                    <td style="font-family: 'JetBrains Mono', monospace;" class="fw-bold">${loadValue} W</td>
                    <td style="font-family: 'JetBrains Mono', monospace;">${accumulatedKWh.toFixed(3)} kWh</td>
                    <td style="font-family: 'JetBrains Mono', monospace;" class="text-neon fw-bold">${costNow} Bs.</td>
                    <td>${statusLabel}</td>
                </tr>
            `;

            readingsTableBody.insertAdjacentHTML('afterbegin', row);

            // Keep only last 10 table rows
            if (readingsTableBody.children.length > 10) {
                readingsTableBody.removeChild(readingsTableBody.lastChild);
            }
        }

        // Add initial table rows
        for (let i = 0; i < 5; i++) {
            addTableReading(activeLoad);
        }
    </script>
</body>
</html>
