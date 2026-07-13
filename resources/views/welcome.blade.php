<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <!-- Custom Premium Styles -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        *, ::after, ::before { box-sizing: border-box; }
        .min-h-screen { min-height: 100vh; }
        
        .hub-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            z-index: 10;
            position: relative;
        }

        .hub-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .hub-header h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--color-text);
            margin-bottom: 1rem;
        }

        .hub-header p {
            font-size: 1.25rem;
            color: var(--color-text);
            opacity: 0.8;
            max-width: 600px;
            margin: 0 auto;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            width: 100%;
        }

        @media (min-width: 768px) {
            .cards-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .hub-card {
            background: var(--color-card-bg);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border-radius: 1.5rem;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: var(--color-text);
            overflow: hidden; /* For image corners */
        }

        .hub-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: rgba(255, 85, 50, 0.3);
        }

        .card-img-wrapper {
            width: 100%;
            height: 200px;
            position: relative;
            background: #f1f5f9;
            overflow: hidden;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .hub-card:hover .card-img-wrapper img {
            transform: scale(1.05);
        }

        /* Floating Icon inside image */
        .card-icon-overlay {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 2;
        }

        .card-body {
            padding: 3rem 2rem 2rem 2rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .hub-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .hub-card p {
            font-size: 1rem;
            opacity: 0.7;
            margin-bottom: 2rem;
            line-height: 1.5;
            flex-grow: 1;
        }

        .card-btn {
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
            width: 100%;
        }

        /* Specific Card Styles */
        .card-admin .card-icon-overlay { background: #ff5532; color: #fff; }
        .card-admin .card-btn { background: #fff5f2; color: #ff5532; border: 1px solid #ffebe6; }
        .card-admin:hover .card-btn { background: #ff5532; color: #fff; }

        .card-staff .card-icon-overlay { background: #10b981; color: #fff; }
        .card-staff .card-btn { background: #f0fdf4; color: #10b981; border: 1px solid #d1fae5; }
        .card-staff:hover .card-btn { background: #10b981; color: #fff; }

        .card-student .card-icon-overlay { background: #6366f1; color: #fff; }
        .card-student .card-btn { background: #eef2ff; color: #6366f1; border: 1px solid #e0e7ff; }
        .card-student:hover .card-btn { background: #6366f1; color: #fff; }

    </style>
</head>
<body>
    <div class="relative min-h-screen">
        <!-- Abstract Vector Background -->
        <div class="background-vectors">
            @include('partials.background_vectors')
        </div>

        <div class="hub-container">
            <div class="hub-header">
                 <p>Select your portal to securely log in and access your personalized dashboard.</p>
            </div>

            <div class="cards-grid">
                <!-- Admin Card -->
                <a href="{{ route('login') }}?type=institute" class="hub-card card-admin">
                    <div class="card-img-wrapper">
                        <img src="{{ asset('images/admin_photo_professional_1783756997320.png') }}" alt="Admin Portal">
                        <div class="card-icon-overlay"><i class="fas fa-building"></i></div>
                    </div>
                    <div class="card-body">
                        <h2>Admin Portal</h2>
                        <p>Manage institute operations, view analytics, and control staff and student records.</p>
                        <div class="card-btn">Login as Admin</div>
                    </div>
                </a>

                <!-- Staff Card -->
                <a href="{{ route('login') }}?type=staff" class="hub-card card-staff">
                    <div class="card-img-wrapper">
                        <img src="{{ asset('images/staff_photo_professional_1783756986398.png') }}" alt="Staff Portal">
                        <div class="card-icon-overlay"><i class="fas fa-chalkboard-teacher"></i></div>
                    </div>
                    <div class="card-body">
                        <h2>Staff Portal</h2>
                        <p>Access your classes, mark attendance, and manage your payroll and digital records.</p>
                        <div class="card-btn">Login as Staff</div>
                    </div>
                </a>

                <!-- Student Card -->
                <a href="{{ route('login') }}?type=student" class="hub-card card-student">
                    <div class="card-img-wrapper">
                        <img src="{{ asset('images/student_photo_professional_1783756975009.png') }}" alt="Student Portal">
                        <div class="card-icon-overlay"><i class="fas fa-user-graduate"></i></div>
                    </div>
                    <div class="card-body">
                        <h2>Student Portal</h2>
                        <p>Track your assignments, view attendance logs, and download fee receipts.</p>
                        <div class="card-btn">Login as Student</div>
                    </div>
                </a>
            </div>
            
            @auth
            <div style="margin-top: 3rem; text-align: center;">
                <a href="{{ url('/home') }}" style="display: inline-block; padding: 1rem 3rem; background: var(--color-primary); color: #fff; text-decoration: none; border-radius: 1rem; font-weight: 700; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">Go to Active Dashboard</a>
            </div>
            @endauth
        </div>
    </div>
</body>
</html>
