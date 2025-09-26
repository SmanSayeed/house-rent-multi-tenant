<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Multi-Tenant Flat & Bill Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(45deg, #5a6fd8, #6a4190);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-custom {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }

        .btn-outline-custom:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="{{ route('home') }}">
                <i class="fas fa-building me-2"></i>FlatManager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-custom btn-custom me-2">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-primary-custom btn-custom">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="text-white">
                        <h1 class="display-4 fw-bold mb-4">
                            Manage Your <span class="text-warning">Multi-Tenant</span><br>
                            Properties with Ease
                        </h1>
                        <p class="lead mb-4 fs-5">
                            Streamline your flat and bill management with our comprehensive multi-tenant system.
                            Perfect for house owners managing multiple properties and tenants.
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="btn btn-warning btn-custom btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Get Started Free
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-custom btn-lg">
                                <i class="fas fa-play me-2"></i>Learn More
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <div class="stats-card rounded-4 p-4 mb-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="fw-bold text-white">1000+</h3>
                                    <p class="text-white-50 mb-0">Properties</p>
                                </div>
                                <div class="col-4">
                                    <h3 class="fw-bold text-white">5000+</h3>
                                    <p class="text-white-50 mb-0">Tenants</p>
                                </div>
                                <div class="col-4">
                                    <h3 class="fw-bold text-white">99%</h3>
                                    <p class="text-white-50 mb-0">Satisfaction</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-4 p-4 shadow-lg">
                            <h5 class="text-dark fw-semibold mb-3">Quick Demo</h5>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-custom btn-sm">
                                    <i class="fas fa-building me-2"></i>Manage Buildings
                                </button>
                                <button class="btn btn-outline-custom btn-sm">
                                    <i class="fas fa-home me-2"></i>Track Flats
                                </button>
                                <button class="btn btn-outline-custom btn-sm">
                                    <i class="fas fa-file-invoice me-2"></i>Generate Bills
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold text-dark mb-3">Why Choose FlatManager?</h2>
                    <p class="lead text-muted">
                        Our multi-tenant system is designed specifically for house owners who manage multiple properties
                        and need efficient bill management and tenant tracking.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-building text-white fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Multi-Tenant Architecture</h5>
                            <p class="text-muted">
                                Complete data isolation for each house owner. Your property data is secure and separate
                                from others.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-file-invoice-dollar text-white fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Smart Bill Management</h5>
                            <p class="text-muted">
                                Automated bill generation, dues tracking, and payment management with email
                                notifications.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-users text-white fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Tenant Management</h5>
                            <p class="text-muted">
                                Easy tenant assignment, contact management, and role-based access control.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-info bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-chart-line text-white fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Analytics & Reports</h5>
                            <p class="text-muted">
                                Comprehensive reports on rent collection, dues, and property performance.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-danger bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-mobile-alt text-white fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Mobile Responsive</h5>
                            <p class="text-muted">
                                Access your property management system from any device, anywhere, anytime.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-secondary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 60px; height: 60px;">
                                <i class="fas fa-shield-alt text-white fs-4"></i>
                            </div>
                            <h5 class="fw-bold mb-3">Secure & Reliable</h5>
                            <p class="text-muted">
                                Enterprise-grade security with data encryption and regular backups.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-primary">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="text-white fw-bold mb-3">Ready to Streamline Your Property Management?</h3>
                    <p class="text-white-50 mb-0">
                        Join hundreds of house owners who trust FlatManager for their multi-tenant property management
                        needs.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('register') }}" class="btn btn-warning btn-custom btn-lg">
                        <i class="fas fa-rocket me-2"></i>Start Free Trial
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-building me-2"></i>FlatManager
                    </h5>
                    <p class="text-white-50">
                        The ultimate multi-tenant flat and bill management system for house owners.
                    </p>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-white-50 text-decoration-none">Features</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Pricing</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">API</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Documentation</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">About</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Careers</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Connect</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="text-white-50"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-white-50 mb-0">&copy; 2024 FlatManager. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white-50 text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-white-50 text-decoration-none">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>

</html>
