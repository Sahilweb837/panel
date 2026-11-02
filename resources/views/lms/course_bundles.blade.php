<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Course Bundle - Edaca LMS</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        edaca: {
                            blue: '#2563eb',
                            'blue-hover': '#1d4ed8',
                            'blue-light': '#eff6ff',
                            bg: '#f8fafc',
                            card: '#ffffff',
                            border: '#eef2f6',
                            text: '#0f172a',
                            muted: '#64748b'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }

        /* Edaca Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Edaca Sidebar Active Pill */
        .sidebar-item-active {
            background-color: #2563eb !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        /* Status Pills */
        .badge-active {
            background-color: #ecfdf5;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .badge-upcoming {
            background-color: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }
        .badge-paused {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        /* Table styling */
        .edaca-table th {
            font-size: 0.8rem;
            font-weight: 600;
            color: #475569;
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
            white-space: nowrap;
        }
        .edaca-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f8fafc;
            font-size: 0.875rem;
            vertical-align: middle;
        }
        .edaca-table tr:hover td {
            background-color: #fafbfd;
        }
        .edaca-table tr:last-child td {
            border-bottom: none;
        }

        /* Bullet lists in Courses column */
        .course-bullet-list {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }
        .course-bullet-list li {
            position: relative;
            padding-left: 14px;
            font-size: 0.815rem;
            color: #475569;
            line-height: 1.55;
            margin-bottom: 2px;
        }
        .course-bullet-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #64748b;
            font-weight: bold;
        }

        /* Popover dropdown for 3-dots actions */
        .action-popover {
            position: absolute;
            right: 20px;
            margin-top: 6px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 14px 34px -4px rgba(15, 23, 42, 0.12), 0 6px 14px -2px rgba(15, 23, 42, 0.06);
            width: 140px;
            padding: 6px;
            z-index: 50;
            animation: fadeIn 0.15s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .action-popover-item {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 8px 12px;
            font-size: 0.835rem;
            font-weight: 500;
            color: #334155;
            border-radius: 8px;
            transition: all 0.15s ease;
            text-align: left;
            background: transparent;
            border: none;
            cursor: pointer;
        }
        .action-popover-item:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .action-popover-item.active-item {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 600;
        }
        .action-popover-item.active-item:hover {
            background-color: #1d4ed8;
        }
        .action-popover-item.delete-item:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }

        /* Global Toast Notifications */
        #edacaToastContainer {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .edaca-toast {
            pointer-events: auto;
            min-width: 320px;
            max-width: 400px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: toastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.25s ease;
            cursor: pointer;
        }
        .edaca-toast:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
        }
        @keyframes toastSlideIn {
            from { opacity: 0; transform: translateX(40px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased min-h-screen flex">

    <!-- Global Floating Toast Container -->
    <div id="edacaToastContainer"></div>

    <!-- ========================================================
         LEFT SIDEBAR (MATCHING SCREENSHOT PIXEL-PERFECT)
         ======================================================== -->
    <aside id="edacaSidebar" class="w-64 bg-white border-r border-[#eef2f6] flex flex-col shrink-0 min-h-screen transition-all duration-300 z-30">
        <!-- Brand Header -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-[#f1f5f9]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <!-- Edaca Logo Icon -->
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm shadow-blue-500/30 group-hover:bg-blue-700 transition">
                    <i class="fas fa-layer-group text-sm"></i>
                </div>
                <span class="text-xl font-bold font-display tracking-tight text-[#0f172a] flex items-center">
                    <span class="text-blue-600">Edaca</span>
                </span>
            </a>
            <!-- Collapse Button |<- -->
            <button type="button" onclick="toggleSidebar()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 flex items-center justify-center transition border border-slate-200/60" title="Toggle Sidebar">
                <i class="fas fa-arrow-left-to-line text-xs"></i>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
            <!-- Dashboard Item -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition group">
                <i class="fas fa-table-cells-large text-slate-400 group-hover:text-slate-700 text-sm w-4"></i>
                <span>Dashboard</span>
            </a>

            <!-- Courses (Dropdown Group - Open by default matching image) -->
            <div class="pt-2">
                <div class="flex items-center justify-between px-3.5 py-2 text-slate-800 text-sm font-semibold rounded-xl cursor-pointer hover:bg-slate-50 transition" onclick="toggleCoursesMenu()">
                    <div class="flex items-center gap-3">
                        <i class="far fa-folder-open text-slate-700 text-sm w-4"></i>
                        <span>Courses</span>
                    </div>
                    <i class="fas fa-chevron-down text-[11px] text-slate-400 transition-transform duration-200" id="coursesMenuChevron"></i>
                </div>

                <!-- Submenu items -->
                <div id="coursesSubmenu" class="pl-5 pr-1 py-1 space-y-1 border-l-2 border-slate-100 ml-5 my-1">
                    <a href="{{ route('courses.index') }}" class="block px-3 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition">
                        Manage Course
                    </a>
                    <a href="{{ route('courses.create') }}" class="block px-3 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition">
                        Add New Course
                    </a>
                    <a href="{{ route('courses.index') }}" class="block px-3 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition">
                        Course Category
                    </a>
                    <a href="{{ route('fee_invoices.index') }}" class="block px-3 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition">
                        Coupons
                    </a>
                    <!-- Active Course Bundle (Screenshot Highlight) -->
                    <a href="{{ route('lms.course_bundles.index') }}" class="block px-3.5 py-2 text-xs sidebar-item-active transition">
                        Course Bundle
                    </a>
                    <a href="{{ route('reports.index') }}" class="block px-3 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-lg transition">
                        Subscription Reports
                    </a>
                </div>
            </div>

            <!-- Other Edaca Nav Items with Chevron > -->
            <a href="javascript:void(0)" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-person-chalkboard text-slate-400 text-sm w-4"></i>
                    <span>Bootcamp</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="javascript:void(0)" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-users-viewfinder text-slate-400 text-sm w-4"></i>
                    <span>Team Training</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="javascript:void(0)" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="far fa-calendar-check text-slate-400 text-sm w-4"></i>
                    <span>Tutor Booking</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="javascript:void(0)" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-book text-slate-400 text-sm w-4"></i>
                    <span>EBook</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="{{ route('students.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-user-plus text-slate-400 text-sm w-4"></i>
                    <span>Enrollments</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="{{ route('reports.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="far fa-file-lines text-slate-400 text-sm w-4"></i>
                    <span>Reports</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="javascript:void(0)" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="fas fa-network-wired text-slate-400 text-sm w-4"></i>
                    <span>Affiliate</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="{{ route('sub-admins.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="far fa-user text-slate-400 text-sm w-4"></i>
                    <span>Users</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="{{ route('fee_invoices.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="far fa-credit-card text-slate-400 text-sm w-4"></i>
                    <span>Offline Payment</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <!-- Message (NetChat link) -->
            <a href="{{ route('netchat.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="far fa-comment-dots text-slate-400 text-sm w-4"></i>
                    <span>Message</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </a>

            <a href="javascript:void(0)" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="far fa-newspaper text-slate-400 text-sm w-4"></i>
                    <span>Newsletter</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="javascript:void(0)" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-sm font-medium transition">
                <div class="flex items-center gap-3">
                    <i class="far fa-address-book text-slate-400 text-sm w-4"></i>
                    <span>Contact</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>
        </nav>

        <!-- Return to Main ERP Footer -->
        <div class="p-4 border-t border-[#f1f5f9] bg-slate-50/60">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full py-2 px-3 text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition shadow-2xs">
                <i class="fas fa-arrow-left text-[11px] text-slate-400"></i>
                <span>Return to ERP Dashboard</span>
            </a>
        </div>
    </aside>

    <!-- ========================================================
         MAIN CONTENT AREA
         ======================================================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Top Navigation Bar (Matching Screenshot Breadcrumb & Actions) -->
        <header class="h-16 bg-white border-b border-[#eef2f6] px-6 lg:px-8 flex items-center justify-between sticky top-0 z-20">
            <!-- Left: < Manage Course Bundle -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="history.back()" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition" title="Go back">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>
                <h1 class="text-base sm:text-lg font-semibold text-[#0f172a] tracking-tight">
                    Manage Course Bundle
                </h1>
            </div>

            <!-- Right: Action Icons & Profile -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <!-- Layout icon -->
                <button type="button" class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm transition" title="Layout Options">
                    <i class="fas fa-table-cells-large"></i>
                </button>

                <!-- Sparkle / AI ✨ -->
                <button type="button" onclick="Swal.fire({title:'Edaca AI Assistant', text:'Course Bundle optimizer & curriculum recommender is active.', icon:'info', confirmButtonColor:'#2563eb'})" class="w-9 h-9 rounded-xl hover:bg-blue-50 text-blue-600 flex items-center justify-center text-sm transition" title="AI Recommendations">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </button>

                <!-- 9-dot launcher grid -->
                <a href="{{ route('lms.index') }}" class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm transition" title="LMS Suite Hub">
                    <i class="fas fa-braille"></i>
                </a>

                <!-- Help (?) -->
                <button type="button" onclick="Swal.fire({title:'Edaca Course Bundle Help', html:'Bundles group related mastercourses into cost-saving multi-month access subscriptions.<br><br>Manage subscription limits, active status, and promotional pricing from this screen.', icon:'question', confirmButtonColor:'#2563eb'})" class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm transition" title="Help & Documentation">
                    <i class="far fa-circle-question"></i>
                </button>

                <!-- Notification Bell with Dot -->
                <a href="{{ route('netchat.index') }}" class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center text-sm transition relative" title="Notifications & Messages">
                    <i class="far fa-bell"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-blue-600"></span>
                </a>

                <!-- User Avatar -->
                <div class="pl-2 border-l border-slate-200 ml-1">
                    <a href="{{ route('settings.index') }}" class="block">
                        <img src="{{ session('user_avatar') ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop' }}" 
                             alt="User" 
                             class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs hover:ring-2 hover:ring-blue-500 transition"
                             onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Workspace Container -->
        <main class="p-6 lg:p-8 flex-1">
            <!-- White Edaca Card (Exact Image Layout) -->
            <div class="bg-white rounded-2xl border border-[#eef2f6] shadow-sm p-6 lg:p-7">
                
                <!-- Toolbar Row: Title + Search + Filter + Sort + Add New Bundle -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <!-- Title -->
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-bold text-[#0f172a] tracking-tight">Course Bundle</h2>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 border border-blue-200">
                            {{ $totalBundles }} Bundles
                        </span>
                    </div>

                    <!-- Right Controls Group -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Search Box with ⌘K Badge -->
                        <div class="relative flex items-center min-w-[240px]">
                            <i class="fas fa-search absolute left-3.5 text-slate-400 text-xs pointer-events-none"></i>
                            <input type="text" 
                                   id="bundleSearchInput" 
                                   placeholder="Search" 
                                   value="{{ request('search') }}"
                                   onkeyup="handleClientSearch(this.value)"
                                   class="w-full pl-9 pr-14 py-2 bg-white border border-[#e2e8f0] rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                            <span class="absolute right-2.5 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-slate-100 border border-slate-200 rounded pointer-events-none">
                                ⌘K
                            </span>
                        </div>

                        <!-- Filter Button -->
                        <div class="relative">
                            <button type="button" 
                                    onclick="toggleDropdown('filterDropdown')" 
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 border border-[#e2e8f0] rounded-xl text-xs sm:text-sm font-semibold text-slate-600 transition shadow-2xs">
                                <i class="fas fa-filter text-slate-400 text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <!-- Filter Dropdown Menu -->
                            <div id="filterDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-lg p-2 z-40 text-xs">
                                <a href="{{ route('lms.course_bundles.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-700 font-medium">All Statuses</a>
                                <a href="{{ route('lms.course_bundles.index', ['status' => 'Active']) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-emerald-600 font-medium">Active Only</a>
                                <a href="{{ route('lms.course_bundles.index', ['status' => 'Upcoming']) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-sky-600 font-medium">Upcoming Only</a>
                                <a href="{{ route('lms.course_bundles.index', ['status' => 'Paused']) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-amber-600 font-medium">Paused Only</a>
                            </div>
                        </div>

                        <!-- Sort By Dropdown -->
                        <div class="relative">
                            <button type="button" 
                                    onclick="toggleDropdown('sortDropdown')" 
                                    class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 border border-[#e2e8f0] rounded-xl text-xs sm:text-sm font-semibold text-slate-600 transition shadow-2xs">
                                <i class="fas fa-arrow-down-short-wide text-slate-400 text-xs"></i>
                                <span>Sort by</span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
                            </button>
                            <!-- Sort Dropdown Menu -->
                            <div id="sortDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-lg p-2 z-40 text-xs">
                                <a href="{{ route('lms.course_bundles.index', ['sort' => 'price', 'order' => 'asc']) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-700 font-medium">Price (Low to High)</a>
                                <a href="{{ route('lms.course_bundles.index', ['sort' => 'price', 'order' => 'desc']) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-700 font-medium">Price (High to Low)</a>
                                <a href="{{ route('lms.course_bundles.index', ['sort' => 'title', 'order' => 'asc']) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-700 font-medium">Bundle Name (A-Z)</a>
                                <a href="{{ route('lms.course_bundles.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-700 font-medium">Default Order</a>
                            </div>
                        </div>

                        <!-- + Add New Bundle Blue Button -->
                        <button type="button" 
                                onclick="openAddBundleModal()" 
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs sm:text-sm transition shadow-sm shadow-blue-500/25">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Add New Bundle</span>
                        </button>
                    </div>
                </div>

                <!-- Table Container with Horizontal Scroll -->
                <div class="overflow-x-auto border border-[#f1f5f9] rounded-xl">
                    <table class="w-full text-left border-collapse edaca-table" id="courseBundlesTable">
                        <thead>
                            <tr class="bg-[#fafbfc]">
                                <th class="w-72">
                                    <div class="flex items-center gap-1.5 cursor-pointer hover:text-slate-800" onclick="sortTableByColumn(0)">
                                        <span>Bundle</span>
                                        <i class="fas fa-sort text-[10px] text-slate-400"></i>
                                    </div>
                                </th>
                                <th>
                                    <div class="flex items-center gap-1.5 cursor-pointer hover:text-slate-800" onclick="sortTableByColumn(1)">
                                        <span>Courses</span>
                                        <i class="fas fa-sort text-[10px] text-slate-400"></i>
                                    </div>
                                </th>
                                <th class="w-48">
                                    <div class="flex items-center gap-1.5 cursor-pointer hover:text-slate-800" onclick="sortTableByColumn(2)">
                                        <span>Subscription Limit</span>
                                        <i class="fas fa-sort text-[10px] text-slate-400"></i>
                                    </div>
                                </th>
                                <th class="w-28">
                                    <div class="flex items-center gap-1.5 cursor-pointer hover:text-slate-800" onclick="sortTableByColumn(3)">
                                        <span>Price</span>
                                        <i class="fas fa-sort text-[10px] text-slate-400"></i>
                                    </div>
                                </th>
                                <th class="w-32">
                                    <div class="flex items-center gap-1.5 cursor-pointer hover:text-slate-800" onclick="sortTableByColumn(4)">
                                        <span>Status</span>
                                        <i class="fas fa-sort text-[10px] text-slate-400"></i>
                                    </div>
                                </th>
                                <th class="w-20 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bundleTableBody">
                            @forelse($bundles as $bundle)
                                <tr class="bundle-row transition-colors" data-id="{{ $bundle->id }}" data-title="{{ strtolower($bundle->title) }}" data-status="{{ strtolower($bundle->status) }}">
                                    <!-- Bundle Column: Thumbnail + Title -->
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $bundle->thumbnail ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop' }}" 
                                                 alt="{{ $bundle->title }}" 
                                                 class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-200/80 shadow-2xs"
                                                 loading="lazy"
                                                 onerror="this.onerror=null; this.src='{{ asset('image.png') }}';">
                                            <div>
                                                <div class="font-semibold text-slate-900 text-sm leading-snug">
                                                    {{ $bundle->title }}
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                    {{ $bundle->slug ?: Str::slug($bundle->title) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Courses Column (Bulleted List) -->
                                    <td>
                                        <ul class="course-bullet-list">
                                            @php
                                                $coursesArr = is_array($bundle->courses) ? $bundle->courses : json_decode($bundle->courses ?? '[]', true);
                                                if (empty($coursesArr)) {
                                                    $coursesArr = ['Curriculum Module: ' . $bundle->title, 'Full Stack Hands-on Labs & Tests', 'Verified Industry Certification'];
                                                }
                                            @endphp
                                            @foreach($coursesArr as $cName)
                                                <li>{{ $cName }}</li>
                                            @endforeach
                                        </ul>
                                    </td>

                                    <!-- Subscription Limit Column -->
                                    <td>
                                        <span class="text-slate-600 font-medium text-xs sm:text-sm">
                                            {{ $bundle->subscription_limit }}
                                        </span>
                                    </td>

                                    <!-- Price Column -->
                                    <td>
                                        <span class="font-semibold text-slate-900 text-sm">
                                            ${{ number_format($bundle->price, 0) }}
                                        </span>
                                    </td>

                                    <!-- Status Column -->
                                    <td>
                                        @if($bundle->status === 'Active')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold badge-active">
                                                Active
                                            </span>
                                        @elseif($bundle->status === 'Upcoming')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold badge-upcoming">
                                                Upcoming
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold badge-paused">
                                                Paused
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions Column (3-dots button with popover) -->
                                    <td class="text-center relative">
                                        <button type="button" 
                                                onclick="toggleActionPopover(event, {{ $bundle->id }})" 
                                                class="w-8 h-8 rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 inline-flex items-center justify-center transition">
                                            <i class="fas fa-ellipsis text-sm"></i>
                                        </button>

                                        <!-- Popover Menu matching Screenshot (Edit Course blue highlight, Delete, Pause) -->
                                        <div id="popover-{{ $bundle->id }}" class="action-popover hidden text-left">
                                            <button type="button" 
                                                    onclick="openEditBundleModal({{ $bundle->id }})" 
                                                    class="action-popover-item active-item">
                                                <i class="fas fa-pen text-xs w-4 mr-2"></i>
                                                <span>Edit Course</span>
                                            </button>
                                            <button type="button" 
                                                    onclick="deleteBundle({{ $bundle->id }}, '{{ addslashes($bundle->title) }}')" 
                                                    class="action-popover-item delete-item">
                                                <i class="far fa-trash-can text-xs w-4 mr-2"></i>
                                                <span>Delete</span>
                                            </button>
                                            <button type="button" 
                                                    onclick="toggleBundleStatus({{ $bundle->id }}, '{{ $bundle->status === 'Paused' ? 'Active' : 'Paused' }}')" 
                                                    class="action-popover-item">
                                                <i class="fas {{ $bundle->status === 'Paused' ? 'fa-play' : 'fa-pause' }} text-xs w-4 mr-2"></i>
                                                <span>{{ $bundle->status === 'Paused' ? 'Activate' : 'Pause' }}</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                                        <i class="fas fa-box-open text-3xl mb-3 text-slate-300 block"></i>
                                        No course bundles found matching your filters.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination (Matching Screenshot Layout) -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-slate-100">
                    <div class="text-xs sm:text-sm text-slate-500 font-medium">
                        Showing result {{ $bundles->firstItem() ?? 1 }}-{{ $bundles->lastItem() ?? $bundles->count() }} of {{ $bundles->total() ?? $totalBundles }} Entries
                    </div>

                    <!-- Previous & Next rounded buttons -->
                    <div class="flex items-center gap-2">
                        @if ($bundles->onFirstPage())
                            <button disabled class="px-5 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">
                                Previous
                            </button>
                        @else
                            <a href="{{ $bundles->previousPageUrl() }}" class="px-5 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-2xs">
                                Previous
                            </a>
                        @endif

                        @if ($bundles->hasMorePages())
                            <a href="{{ $bundles->nextPageUrl() }}" class="px-6 py-2 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-2xs">
                                Next
                            </a>
                        @else
                            <button disabled class="px-6 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 rounded-xl cursor-not-allowed">
                                Next
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ========================================================
         MODAL: ADD NEW BUNDLE
         ======================================================== -->
    <div id="addBundleModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fas fa-plus text-xs"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Add New Course Bundle</h3>
                </div>
                <button type="button" onclick="closeAddBundleModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form action="{{ route('lms.course_bundles.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bundle Title</label>
                    <input type="text" name="title" required placeholder="e.g. Full Stack Pioneer Kit" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Included Courses (One course per line)</label>
                    <textarea name="courses" rows="3" placeholder="• Building Single Page Applications&#10;• Advanced Web Security&#10;• Production Deployments" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">Courses will be formatted as neat bullet points in the table.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Subscription Limit</label>
                        <input type="text" name="subscription_limit" required placeholder="e.g. 180 Days" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Price ($ USD)</label>
                        <input type="number" step="0.01" name="price" required placeholder="29" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            <option value="Active">Active</option>
                            <option value="Upcoming">Upcoming</option>
                            <option value="Paused">Paused</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Thumbnail Image URL</label>
                        <input type="url" name="thumbnail" placeholder="https://..." class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeAddBundleModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-sm shadow-blue-500/25">
                        Save Bundle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================
         MODAL: EDIT BUNDLE
         ======================================================== -->
    <div id="editBundleModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <i class="fas fa-pen text-xs"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Edit Course Bundle</h3>
                </div>
                <button type="button" onclick="closeEditBundleModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form id="editBundleForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="editBundleId" name="bundle_id">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Bundle Title</label>
                    <input type="text" id="editBundleTitle" name="title" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Included Courses (One per line)</label>
                    <textarea id="editBundleCourses" name="courses" rows="3" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Subscription Limit</label>
                        <input type="text" id="editBundleLimit" name="subscription_limit" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Price ($ USD)</label>
                        <input type="number" step="0.01" id="editBundlePrice" name="price" required class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status</label>
                    <select id="editBundleStatus" name="status" class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <option value="Active">Active</option>
                        <option value="Upcoming">Upcoming</option>
                        <option value="Paused">Paused</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeEditBundleModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-sm shadow-blue-500/25">
                        Update Bundle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Form for Status Toggle and Delete -->
    <form id="actionForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="_method" id="actionFormMethod" value="POST">
        <input type="hidden" name="status" id="actionFormStatus" value="">
    </form>

    <!-- JavaScript Interactions & Real-time Toasts -->
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Sidebar Collapse Toggle
        function toggleSidebar() {
            const sb = document.getElementById('edacaSidebar');
            if (sb) {
                sb.classList.toggle('-ml-64');
            }
        }

        // Toggle Courses Submenu
        function toggleCoursesMenu() {
            const menu = document.getElementById('coursesSubmenu');
            const chev = document.getElementById('coursesMenuChevron');
            if (menu) {
                menu.classList.toggle('hidden');
                if (chev) {
                    chev.classList.toggle('rotate-180');
                }
            }
        }

        // Toggle generic dropdowns (Filter, Sort by)
        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if (el) {
                const isHidden = el.classList.contains('hidden');
                document.querySelectorAll('#filterDropdown, #sortDropdown').forEach(d => d.classList.add('hidden'));
                if (isHidden) el.classList.remove('hidden');
            }
        }

        // Close dropdowns on outside click
        window.addEventListener('click', function(e) {
            if (!e.target.closest('#filterDropdown') && !e.target.closest('button[onclick*="filterDropdown"]')) {
                document.getElementById('filterDropdown')?.classList.add('hidden');
            }
            if (!e.target.closest('#sortDropdown') && !e.target.closest('button[onclick*="sortDropdown"]')) {
                document.getElementById('sortDropdown')?.classList.add('hidden');
            }
            if (!e.target.closest('.action-popover') && !e.target.closest('button[onclick*="toggleActionPopover"]')) {
                document.querySelectorAll('.action-popover').forEach(p => p.classList.add('hidden'));
            }
        });

        // 3-Dots Action Popover Toggle
        function toggleActionPopover(e, id) {
            e.stopPropagation();
            const target = document.getElementById('popover-' + id);
            const isHidden = target.classList.contains('hidden');
            document.querySelectorAll('.action-popover').forEach(p => p.classList.add('hidden'));
            if (isHidden) target.classList.remove('hidden');
        }

        // Keyboard Shortcut ⌘K / Ctrl+K
        window.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('bundleSearchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Client-side instant filter on typing
        function handleClientSearch(val) {
            const term = (val || '').toLowerCase().trim();
            const rows = document.querySelectorAll('.bundle-row');
            rows.forEach(r => {
                const text = r.innerText.toLowerCase();
                if (!term || text.includes(term)) {
                    r.style.display = '';
                } else {
                    r.style.display = 'none';
                }
            });
        }

        // Sort table columns
        let sortDirections = {};
        function sortTableByColumn(colIndex) {
            const table = document.getElementById('courseBundlesTable');
            const tbody = document.getElementById('bundleTableBody');
            const rows = Array.from(tbody.querySelectorAll('tr.bundle-row'));
            
            sortDirections[colIndex] = !sortDirections[colIndex];
            const asc = sortDirections[colIndex];

            rows.sort((a, b) => {
                const cellA = a.children[colIndex]?.innerText.trim() || '';
                const cellB = b.children[colIndex]?.innerText.trim() || '';

                if (colIndex === 3) {
                    const priceA = parseFloat(cellA.replace(/[^0-9.]/g, '')) || 0;
                    const priceB = parseFloat(cellB.replace(/[^0-9.]/g, '')) || 0;
                    return asc ? priceA - priceB : priceB - priceA;
                }

                return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            });

            rows.forEach(r => tbody.appendChild(r));
        }

        // Add Modal
        function openAddBundleModal() {
            document.getElementById('addBundleModal')?.classList.remove('hidden');
        }
        function closeAddBundleModal() {
            document.getElementById('addBundleModal')?.classList.add('hidden');
        }

        // Edit Modal
        async function openEditBundleModal(id) {
            document.querySelectorAll('.action-popover').forEach(p => p.classList.add('hidden'));
            try {
                const res = await fetch(`/lms/course-bundles/${id}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.success && data.bundle) {
                    const b = data.bundle;
                    document.getElementById('editBundleId').value = b.id;
                    document.getElementById('editBundleTitle').value = b.title;
                    
                    const courses = Array.isArray(b.courses) ? b.courses.join("\n") : (b.courses || '');
                    document.getElementById('editBundleCourses').value = courses;
                    document.getElementById('editBundleLimit').value = b.subscription_limit;
                    document.getElementById('editBundlePrice').value = b.price;
                    document.getElementById('editBundleStatus').value = b.status;

                    const form = document.getElementById('editBundleForm');
                    form.action = `/lms/course-bundles/${b.id}`;

                    document.getElementById('editBundleModal')?.classList.remove('hidden');
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Could not load bundle details.' });
            }
        }
        function closeEditBundleModal() {
            document.getElementById('editBundleModal')?.classList.add('hidden');
        }

        // Delete Bundle
        function deleteBundle(id, title) {
            document.querySelectorAll('.action-popover').forEach(p => p.classList.add('hidden'));
            Swal.fire({
                title: 'Delete Course Bundle?',
                text: `Are you sure you want to remove "${title}"? This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete bundle'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('actionForm');
                    form.action = `/lms/course-bundles/${id}`;
                    document.getElementById('actionFormMethod').value = 'DELETE';
                    form.submit();
                }
            });
        }

        // Toggle Status (Pause / Activate)
        function toggleBundleStatus(id, newStatus) {
            document.querySelectorAll('.action-popover').forEach(p => p.classList.add('hidden'));
            const form = document.getElementById('actionForm');
            form.action = `/lms/course-bundles/${id}/status`;
            document.getElementById('actionFormMethod').value = 'POST';
            document.getElementById('actionFormStatus').value = newStatus;
            form.submit();
        }

        // ========================================================
        // REAL-TIME NOTIFICATION TOAST POPUPS (CHAT & HUDDLE)
        // ========================================================
        function showNotificationToast(title, message, iconType = 'chat', clickUrl = '{{ route("netchat.index") }}') {
            const container = document.getElementById('edacaToastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'edaca-toast';

            let iconHtml = '<div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="far fa-comment-dots"></i></div>';
            if (iconType === 'huddle') {
                iconHtml = '<div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 animate-pulse"><i class="fas fa-headphones"></i></div>';
            }

            toast.innerHTML = `
                ${iconHtml}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-semibold text-slate-900 text-xs">${title}</div>
                        <span class="text-[10px] text-slate-400">Just now</span>
                    </div>
                    <div class="text-xs text-slate-500 truncate mt-0.5">${message}</div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 ml-1 text-xs" onclick="event.stopPropagation(); this.closest('.edaca-toast').remove();">
                    <i class="fas fa-times"></i>
                </button>
            `;

            toast.addEventListener('click', () => {
                if (clickUrl) window.location.href = clickUrl;
            });

            container.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(40px)';
                setTimeout(() => toast.remove(), 250);
            }, 5000);
        }

        // Polling loop for background message and huddle notifications
        let lastBackgroundMsgId = 0;
        setInterval(async () => {
            try {
                const res = await fetch('/api/messages/inbox-poll?last_id=' + lastBackgroundMsgId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            if (msg.id > lastBackgroundMsgId) {
                                lastBackgroundMsgId = msg.id;
                                showNotificationToast(msg.sender || 'NetChat Message', msg.body || 'New message received', 'chat');
                            }
                        });
                    }
                }
            } catch (e) {
                // background poll
            }
        }, 5000);
    </script>
</body>
</html>
