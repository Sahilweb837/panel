<svg viewBox="0 0 800 600" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 100%; height: auto; drop-shadow: 0 20px 30px rgba(59,130,246,0.1);">
    
    <!-- Decorative background blob -->
    <path d="M 500 100 Q 750 -50 800 300 Q 850 650 600 550 Q 350 450 400 200 Z" fill="#3b82f6" opacity="0.05" />
    <path d="M 100 400 Q 0 600 250 550 Q 500 500 400 350 Q 300 200 150 250 Z" fill="#3b82f6" opacity="0.05" />
    
    <!-- Main Window Background -->
    <rect x="100" y="100" width="600" height="400" rx="16" fill="#ffffff" filter="url(#drop-shadow-student)" />
    
    <!-- Left Sidebar -->
    <rect x="100" y="100" width="140" height="400" rx="16" fill="#f8fafc" />
    
    <!-- Sidebar Items -->
    <rect x="120" y="130" width="30" height="30" rx="8" fill="#3b82f6" />
    <rect x="160" y="140" width="60" height="10" rx="4" fill="#e2e8f0" />
    
    <rect x="120" y="190" width="100" height="12" rx="4" fill="#3b82f6" opacity="0.1" />
    <rect x="120" y="190" width="4" height="12" rx="2" fill="#3b82f6" />
    
    <rect x="120" y="220" width="80" height="10" rx="4" fill="#e2e8f0" />
    <rect x="120" y="250" width="90" height="10" rx="4" fill="#e2e8f0" />
    <rect x="120" y="280" width="70" height="10" rx="4" fill="#e2e8f0" />
    <rect x="120" y="310" width="85" height="10" rx="4" fill="#e2e8f0" />
    
    <!-- Header Area -->
    <rect x="270" y="130" width="250" height="20" rx="6" fill="#f1f5f9" />
    <rect x="620" y="130" width="50" height="20" rx="10" fill="#3b82f6" opacity="0.1" />
    <circle cx="645" cy="140" r="4" fill="#3b82f6" />
    
    <!-- Top Cards (Student Books/Grades) -->
    <g transform="translate(270, 180)">
        <rect width="120" height="80" rx="12" fill="#ffffff" filter="url(#drop-shadow-sm-student)" />
        <circle cx="30" cy="30" r="12" fill="#3b82f6" opacity="0.1" />
        <rect x="26" y="26" width="8" height="8" fill="#3b82f6" />
        <rect x="20" y="55" width="40" height="8" rx="4" fill="#e2e8f0" />
        <rect x="20" y="70" width="70" height="12" rx="4" fill="#1e293b" opacity="0.8" />
    </g>

    <g transform="translate(410, 180)">
        <rect width="120" height="80" rx="12" fill="#3b82f6" filter="url(#drop-shadow-sm-student)" />
        <circle cx="30" cy="30" r="12" fill="#ffffff" opacity="0.2" />
        <path d="M26 34 L30 26 L34 34" stroke="#ffffff" stroke-width="2" stroke-linecap="round" fill="none"/>
        <rect x="20" y="55" width="40" height="8" rx="4" fill="#ffffff" opacity="0.6" />
        <rect x="20" y="70" width="70" height="12" rx="4" fill="#ffffff" />
    </g>

    <g transform="translate(550, 180)">
        <rect width="120" height="80" rx="12" fill="#ffffff" filter="url(#drop-shadow-sm-student)" />
        <circle cx="30" cy="30" r="12" fill="#3b82f6" opacity="0.1" />
        <circle cx="30" cy="30" r="4" fill="#3b82f6" />
        <rect x="20" y="55" width="40" height="8" rx="4" fill="#e2e8f0" />
        <rect x="20" y="70" width="70" height="12" rx="4" fill="#1e293b" opacity="0.8" />
    </g>
    
    <!-- Student Specific Section (Course progress bars) -->
    <rect x="270" y="290" width="400" height="180" rx="12" fill="#ffffff" filter="url(#drop-shadow-sm-student)" />
    
    <g transform="translate(300, 310)">
        <rect width="200" height="8" rx="4" fill="#e2e8f0" />
        <rect width="140" height="8" rx="4" fill="#3b82f6" />
        <rect x="0" y="15" width="80" height="8" rx="4" fill="#94a3b8" />
        <rect x="290" y="0" width="40" height="10" rx="4" fill="#3b82f6" opacity="0.2" />
    </g>

    <g transform="translate(300, 360)">
        <rect width="200" height="8" rx="4" fill="#e2e8f0" />
        <rect width="180" height="8" rx="4" fill="#10b981" />
        <rect x="0" y="15" width="60" height="8" rx="4" fill="#94a3b8" />
        <rect x="290" y="0" width="40" height="10" rx="4" fill="#10b981" opacity="0.2" />
    </g>

    <g transform="translate(300, 410)">
        <rect width="200" height="8" rx="4" fill="#e2e8f0" />
        <rect width="80" height="8" rx="4" fill="#f59e0b" />
        <rect x="0" y="15" width="100" height="8" rx="4" fill="#94a3b8" />
        <rect x="290" y="0" width="40" height="10" rx="4" fill="#f59e0b" opacity="0.2" />
    </g>
    
    <!-- Floating Element -->
    <g transform="translate(620, 200)">
        <rect width="100" height="120" rx="12" fill="#ffffff" filter="url(#drop-shadow-student)" />
        <rect x="15" y="20" width="70" height="6" rx="3" fill="#e2e8f0" />
        <rect x="15" y="35" width="40" height="6" rx="3" fill="#e2e8f0" />
        
        <rect x="15" y="55" width="70" height="40" rx="6" fill="#3b82f6" opacity="0.1" />
        <circle cx="50" cy="75" r="10" fill="#3b82f6" />
    </g>

    <defs>
        <filter id="drop-shadow-student" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="15" stdDeviation="25" flood-opacity="0.08" flood-color="#3b82f6" />
        </filter>
        <filter id="drop-shadow-sm-student" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0" dy="4" stdDeviation="10" flood-opacity="0.04" flood-color="#1e293b" />
        </filter>
    </defs>
</svg>
