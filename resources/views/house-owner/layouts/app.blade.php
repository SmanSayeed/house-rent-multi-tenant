<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'House Owner Dashboard') - Multi-Tenant System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --house-owner-primary: #059669;
            --house-owner-secondary: #10b981;
            --house-owner-accent: #06b6d4;
            --house-owner-success: #10b981;
            --house-owner-warning: #f59e0b;
            --house-owner-danger: #ef4444;
        }

        .house-owner-sidebar {
            background: linear-gradient(135deg, var(--house-owner-primary), var(--house-owner-secondary));
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .house-owner-main {
            margin-left: 250px;
            min-height: 100vh;
            background-color: #f8fafc;
        }

        .house-owner-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .house-owner-content {
            padding: 2rem;
        }

        .sidebar-brand {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            text-decoration: none;
            padding: 1.5rem;
            display: block;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .stats-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--house-owner-primary);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--house-owner-primary);
        }

        .stats-label {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .house-owner-sidebar {
                transform: translateX(-100%);
            }

            .house-owner-sidebar.show {
                transform: translateX(0);
            }

            .house-owner-main {
                margin-left: 0;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Sidebar -->
    <nav class="house-owner-sidebar" id="houseOwnerSidebar">
        <a href="{{ url('/house-owner/dashboard') }}" class="sidebar-brand">
            <i class="bi bi-house"></i> Property Manager
        </a>

        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('house-owner/dashboard') ? 'active' : '' }}"
                        href="{{ url('/house-owner/dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('house-owner/buildings*') ? 'active' : '' }}"
                        href="{{ url('/house-owner/buildings') }}">
                        <i class="bi bi-building"></i> Buildings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('house-owner/flats*') ? 'active' : '' }}"
                        href="{{ url('/house-owner/flats') }}">
                        <i class="bi bi-house-door"></i> Flats
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('house-owner/tenants*') ? 'active' : '' }}"
                        href="{{ url('/house-owner/tenants') }}">
                        <i class="bi bi-people"></i> Tenants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('house-owner/bills*') ? 'active' : '' }}"
                        href="{{ url('/house-owner/bills') }}">
                        <i class="bi bi-receipt"></i> Bills
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('house-owner/payments*') ? 'active' : '' }}"
                        href="{{ url('/house-owner/payments') }}">
                        <i class="bi bi-credit-card"></i> Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('house-owner/profile*') ? 'active' : '' }}"
                        href="{{ url('/house-owner/profile') }}">
                        <i class="bi bi-person"></i> Profile
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="house-owner-main">
        <!-- Header -->
        <header class="house-owner-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-md-none me-3" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-2"></i>{{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ url('/house-owner/profile') }}">
                            <i class="bi bi-person me-2"></i>Profile
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ url('/house-owner/logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <!-- Content -->
        <main class="house-owner-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('houseOwnerSidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('houseOwnerSidebar');
            const toggle = document.getElementById('sidebarToggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !toggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    </script>

    @yield('scripts')
</body>

</html>
