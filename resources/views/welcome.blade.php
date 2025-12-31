<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel 12 Starter Kit - AdminLTE 4 & Bootstrap 5</title>
    <meta name="description" content="Professional Laravel 12 starter kit with AdminLTE 4, Bootstrap 5, User Management, Permissions, Roles, Datatables, CSV Import, and Audit Logging">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: #333;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 500;
            color: #333 !important;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: var(--gradient-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            overflow: hidden;
            padding-top: 80px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            animation: float 20s infinite linear;
            opacity: 0.3;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            animation: fadeInUp 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 2rem;
            opacity: 0.95;
            animation: fadeInUp 1s ease;
        }

        .hero-description {
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            animation: fadeInUp 1.2s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 1.4s ease;
        }

        .btn-hero-primary {
            background: white;
            color: #667eea;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            background: #f8f9fa;
        }

        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-hero-outline:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: #f8f9fa;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            color: #212529;
        }

        .section-subtitle {
            font-size: 1.2rem;
            text-align: center;
            color: #6c757d;
            margin-bottom: 4rem;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: var(--gradient-primary);
            color: white;
        }

        .feature-icon.user { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .feature-icon.permission { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .feature-icon.role { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .feature-icon.datatable { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .feature-icon.csv { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .feature-icon.audit { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #212529;
        }

        .feature-description {
            color: #6c757d;
            line-height: 1.7;
        }

        /* Tech Stack Section */
        .tech-section {
            padding: 100px 0;
            background: white;
        }

        .tech-badge {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .tech-badge:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: var(--gradient-primary);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-description {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
        }

        /* Footer */
        .footer {
            background: #212529;
            color: white;
            padding: 3rem 0;
            text-align: center;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .footer-links li {
            display: inline-block;
            margin: 0 1rem;
        }

        .footer-links a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .feature-card {
                margin-bottom: 2rem;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .stats-section {
            padding: 80px 0;
            background: white;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1.1rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-code-slash me-2"></i>Laravel Starter
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tech">Tech Stack</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/home') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <button class="btn btn-primary btn-sm">Get Started</button>
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">Laravel 12 Starter Kit</h1>
                    <h2 class="hero-subtitle">Professional Admin Panel with AdminLTE 4</h2>
                    <p class="hero-description">
                        A complete, production-ready starter kit featuring User Management, Role-Based Access Control,
                        Advanced DataTables, CSV Import, and Comprehensive Audit Logging. Built with Laravel 12,
                        AdminLTE 4, and Bootstrap 5.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        @auth
                            <a href="{{ url('/home') }}" class="btn btn-hero btn-hero-primary">
                                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-hero btn-hero-primary">
                                <i class="bi bi-rocket-takeoff me-2"></i>Get Started Free
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-hero btn-hero-outline">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-5 mt-lg-0">
                    <div class="fade-in">
                        <div class="bg-white rounded-4 p-4 shadow-lg" style="transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);">
                            <div class="bg-gradient p-3 rounded-3 mb-3" style="background: var(--gradient-primary); height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-layout-text-window-reverse text-white" style="font-size: 5rem;"></i>
                            </div>
                            <h5 class="fw-bold">Modern Admin Interface</h5>
                            <p class="text-muted">Beautiful, responsive, and intuitive</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">6+</div>
                        <div class="stat-label">Core Features</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Responsive</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">Laravel 12</div>
                        <div class="stat-label">Latest Version</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card">
                        <div class="stat-number">Bootstrap 5</div>
                        <div class="stat-label">Modern UI</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">Powerful Features</h2>
            <p class="section-subtitle">Everything you need to build modern web applications</p>

            <div class="row g-4">
                <!-- User Management -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon user">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="feature-title">User Management</h3>
                        <p class="feature-description">
                            Complete user CRUD operations with advanced filtering, search, and bulk actions.
                            Manage user profiles, permissions, and roles seamlessly.
                        </p>
                    </div>
                </div>

                <!-- Permission System -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon permission">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Permission System</h3>
                        <p class="feature-description">
                            Granular permission management with intuitive interface. Create, edit, and assign
                            permissions with ease. Perfect for complex access control requirements.
                        </p>
                    </div>
                </div>

                <!-- Role Management -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon role">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h3 class="feature-title">Role Management</h3>
                        <p class="feature-description">
                            Flexible role-based access control (RBAC). Assign multiple permissions to roles
                            and manage user-role relationships efficiently.
                        </p>
                    </div>
                </div>

                <!-- DataTables -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon datatable">
                            <i class="bi bi-table"></i>
                        </div>
                        <h3 class="feature-title">Advanced DataTables</h3>
                        <p class="feature-description">
                            Server-side processing, pagination, sorting, filtering, and export capabilities.
                            Export data to Excel, PDF, CSV, and more with a single click.
                        </p>
                    </div>
                </div>

                <!-- CSV Import -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon csv">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                        </div>
                        <h3 class="feature-title">CSV Import</h3>
                        <p class="feature-description">
                            Bulk import data from CSV files with validation and error handling. Preview data
                            before importing and handle duplicates intelligently.
                        </p>
                    </div>
                </div>

                <!-- Audit Log -->
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon audit">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <h3 class="feature-title">Audit Logging</h3>
                        <p class="feature-description">
                            Comprehensive audit trail tracking all changes. Monitor user activities, data
                            modifications, and system events with detailed timestamps and user information.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack Section -->
    <section id="tech" class="tech-section">
        <div class="container">
            <h2 class="section-title">Built With Modern Technology</h2>
            <p class="section-subtitle">Leveraging the best tools and frameworks</p>

            <div class="text-center">
                <span class="tech-badge">
                    <i class="bi bi-laravel me-2"></i>Laravel 12
                </span>
                <span class="tech-badge">
                    <i class="bi bi-bootstrap me-2"></i>Bootstrap 5
                </span>
                <span class="tech-badge">
                    <i class="bi bi-layout-sidebar me-2"></i>AdminLTE 4
                </span>
                <span class="tech-badge">
                    <i class="bi bi-database me-2"></i>MySQL/SQLite
                </span>
                <span class="tech-badge">
                    <i class="bi bi-table me-2"></i>DataTables
                </span>
                <span class="tech-badge">
                    <i class="bi bi-shield-lock me-2"></i>Laravel Sanctum
                </span>
                <span class="tech-badge">
                    <i class="bi bi-key me-2"></i>2FA Support
                </span>
                <span class="tech-badge">
                    <i class="bi bi-filetype-csv me-2"></i>CSV Processing
                </span>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="about" class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Get Started?</h2>
            <p class="cta-description">
                Start building your next great application with our comprehensive starter kit.
                Save weeks of development time and focus on what matters most - your business logic.
            </p>
            @auth
                <a href="{{ url('/home') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill">
                    <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill me-3">
                    <i class="bi bi-rocket-takeoff me-2"></i>Get Started Free
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
                    <h5 class="mb-3">
                        <i class="bi bi-code-slash me-2"></i>Laravel Starter Kit
                    </h5>
                    <p class="text-muted mb-0">
                        Professional Laravel 12 starter kit with AdminLTE 4 & Bootstrap 5
                    </p>
                </div>
                <div class="col-md-6 text-md-end text-center">
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#tech">Tech Stack</a></li>
                        <li><a href="#about">About</a></li>
                    </ul>
                    <p class="text-muted mt-3 mb-0">
                        &copy; {{ date('Y') }} Laravel Starter Kit. Built with <i class="bi bi-heart-fill text-danger"></i>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>
