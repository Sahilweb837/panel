<svg viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto; drop-shadow: 0 20px 30px rgba(255,85,50,0.1);">
    
    <!-- Decorative background blob -->
    <path d="M 500 100 Q 750 -50 800 300 Q 850 650 600 550 Q 350 450 400 200 Z" fill="#ff5532" opacity="0.05" />
    <path d="M 100 400 Q 0 600 250 550 Q 500 500 400 350 Q 300 200 150 250 Z" fill="#ff5532" opacity="0.05" />
    
    <!-- Main Window Background -->
    <rect x="100" y="100" width="600" height="400" rx="16" fill="#ffffff" filter="url(#drop-shadow)" />
    
    <!-- Left Sidebar -->
    <rect x="100" y="100" width="140" height="400" rx="16" fill="#f8fafc" />
    
    <!-- Sidebar Items -->
    <rect x="120" y="130" width="30" height="30" rx="8" fill="#ff5532" />
    <rect x="160" y="140" width="60" height="10" rx="4" fill="#e2e8f0" />
    
    <rect x="120" y="190" width="100" height="12" rx="4" fill="#ff5532" opacity="0.1" />
    <rect x="120" y="190" width="4" height="12" rx="2" fill="#ff5532" />
    
    <rect x="120" y="220" width="80" height="10" rx="4" fill="#e2e8f0" />
    <rect x="120" y="250" width="90" height="10" rx="4" fill="#e2e8f0" />
    <rect x="120" y="280" width="70" height="10" rx="4" fill="#e2e8f0" />
    <rect x="120" y="310" width="85" height="10" rx="4" fill="#e2e8f0" />
    
    <!-- Header Area -->
    <rect x="270" y="130" width="250" height="20" rx="6" fill="#f1f5f9" />
    <rect x="620" y="130" width="50" height="20" rx="10" fill="#ff5532" opacity="0.1" />
    <circle cx="645" cy="140" r="4" fill="#ff5532" />
    
    <!-- Top Cards -->
    <g transform="translate(270, 180)">
        <rect width="120" height="80" rx="12" fill="#ffffff" filter="url(#drop-shadow-sm)" />
        <circle cx="30" cy="30" r="12" fill="#ff5532" opacity="0.1" />
        <circle cx="30" cy="30" r="4" fill="#ff5532" />
        <rect x="20" y="55" width="40" height="8" rx="4" fill="#e2e8f0" />
        <rect x="20" y="70" width="70" height="12" rx="4" fill="#1e293b" opacity="0.8" />
    </g>

    <g transform="translate(410, 180)">
        <rect width="120" height="80" rx="12" fill="#ff5532" filter="url(#drop-shadow-sm)" />
        <circle cx="30" cy="30" r="12" fill="#ffffff" opacity="0.2" />
        <rect x="25" y="25" width="10" height="10" rx="2" fill="#ffffff" />
        <rect x="20" y="55" width="40" height="8" rx="4" fill="#ffffff" opacity="0.6" />
        <rect x="20" y="70" width="70" height="12" rx="4" fill="#ffffff" />
    </g>

    <g transform="translate(550, 180)">
        <rect width="120" height="80" rx="12" fill="#ffffff" filter="url(#drop-shadow-sm)" />
        <circle cx="30" cy="30" r="12" fill="#ff5532" opacity="0.1" />
        <path d="M26 30 L34 30 M30 26 L30 34" stroke="#ff5532" stroke-width="2" stroke-linecap="round"/>
        <rect x="20" y="55" width="40" height="8" rx="4" fill="#e2e8f0" />
        <rect x="20" y="70" width="70" height="12" rx="4" fill="#1e293b" opacity="0.8" />
    </g>
    
    <!-- Chart Section -->
    <rect x="270" y="290" width="400" height="180" rx="12" fill="#ffffff" filter="url(#drop-shadow-sm)" />
    <!-- Chart lines -->
    <rect x="300" y="320" width="340" height="2" fill="#f1f5f9" />
    <rect x="300" y="360" width="340" height="2" fill="#f1f5f9" />
    <rect x="300" y="400" width="340" height="2" fill="#f1f5f9" />
    <rect x="300" y="440" width="340" height="2" fill="#f1f5f9" />
    
    <!-- Chart Graph -->
    <path d="M 310 440 L 350 370 L 410 390 L 480 330 L 550 360 L 620 310" stroke="#ff5532" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" fill="none" />
    <path d="M 310 440 L 310 440 L 350 370 L 410 390 L 480 330 L 550 360 L 620 310 L 620 440 Z" fill="url(#chart-gradient)" opacity="0.2" />
    
    <circle cx="350" cy="370" r="4" fill="#ffffff" stroke="#ff5532" stroke-width="2" />
    <circle cx="480" cy="330" r="4" fill="#ffffff" stroke="#ff5532" stroke-width="2" />
    <circle cx="620" cy="310" r="4" fill="#ffffff" stroke="#ff5532" stroke-width="2" />
    
    <!-- Floating Element -->
    <g transform="translate(620, 200)">
        <rect width="100" height="120" rx="12" fill="#ffffff" filter="url(#drop-shadow)" />
        <rect x="15" y="20" width="70" height="6" rx="3" fill="#e2e8f0" />
        <rect x="15" y="35" width="40" height="6" rx="3" fill="#e2e8f0" />
        
        <rect x="15" y="55" width="70" height="40" rx="6" fill="#ff5532" opacity="0.1" />
        <circle cx="50" cy="75" r="10" fill="#ff5532" />
    </g>

    <defs>
        <filter id="drop-shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="15" stdDeviation="25" flood-opacity="0.08" flood-color="#ff5532" />
        </filter>
        <filter id="drop-shadow-sm" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0" dy="4" stdDeviation="10" flood-opacity="0.04" flood-color="#1e293b" />
        </filter>
        <linearGradient id="chart-gradient" x1="0" y1="0" x2="0" y2="1">
            <stop stop-color="#ff5532" stop-opacity="1"/>
            <stop offset="1" stop-color="#ff5532" stop-opacity="0"/>
        </linearGradient>
    </defs>
</svg>
