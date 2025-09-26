<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Multi-Tenant System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --admin-primary: #1e3a8a;
            --admin-secondary: #3b82f6;
            --admin-accent: #8b5cf6;
            --admin-success: #10b981;
            --admin-warning: #f59e0b;
            --admin-danger: #ef4444;
        }

        .admin-sidebar {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-secondary));
            min-height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .admin-main {
            margin-left: 250px;
            min-height: 100vh;
            background-color: #f8fafc;
        }

        .admin-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .admin-content {
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
            border-left: 4px solid var(--admin-primary);
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--admin-primary);
        }

        .stats-label {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
    <!-- Sidebar -->
    <nav class="admin-sidebar" id="adminSidebar">
        <a href="{{ url('/admin/dashboard') }}" class="sidebar-brand">
            <i class="bi bi-shield-check"></i> Admin Panel
        </a>

        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"
                        href="{{ url('/admin/dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}"
                        href="{{ url('/admin/users') }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/buildings*') ? 'active' : '' }}"
                        href="{{ url('/admin/buildings') }}">
                        <i class="bi bi-building"></i> Buildings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}"
                        href="{{ url('/admin/settings') }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/profile*') ? 'active' : '' }}"
                        href="{{ url('/admin/profile') }}">
                        <i class="bi bi-person"></i> Profile
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="admin-main">
        <!-- Header -->
        <header class="admin-header d-flex justify-content-between align-items-center">
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
                    <li><a class="dropdown-item" href="{{ url('/admin/profile') }}">
                            <i class="bi bi-person me-2"></i>Profile
                        </a></li>
                    <li><a class="dropdown-item" href="{{ url('/admin/settings') }}">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ url('/admin/logout') }}">
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
        <main class="admin-content">
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
            document.getElementById('adminSidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
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
