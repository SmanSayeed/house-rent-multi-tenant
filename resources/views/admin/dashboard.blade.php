@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $stats['total_users'] }}</div>
                        <div class="stats-label">Total Users</div>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $stats['total_house_owners'] }}</div>
                        <div class="stats-label">House Owners</div>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-house" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $stats['total_buildings'] }}</div>
                        <div class="stats-label">Buildings</div>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-building" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $stats['total_payments'] }}</div>
                        <div class="stats-label">Total Payments</div>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-credit-card" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Users -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>Recent Users
                    </h5>
                    <a href="{{ url('/admin/users') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if ($recentUsers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($recentUsers as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <span
                                        class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'house_owner' ? 'success' : 'info') }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No users found</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Buildings -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>Recent Buildings
                    </h5>
                    <a href="{{ url('/admin/buildings') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if ($recentBuildings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($recentBuildings as $building)
                                <div class="list-group-item">
                                    <h6 class="mb-1">{{ $building->name }}</h6>
                                    <p class="mb-1 text-muted">{{ $building->address }}</p>
                                    <small class="text-muted">
                                        Owner: {{ $building->owner->name ?? 'N/A' }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No buildings found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ url('/admin/users') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people d-block mb-2" style="font-size: 1.5rem;"></i>
                                Manage Users
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ url('/admin/buildings') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-building d-block mb-2" style="font-size: 1.5rem;"></i>
                                Manage Buildings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ url('/admin/settings') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-gear d-block mb-2" style="font-size: 1.5rem;"></i>
                                System Settings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ url('/admin/profile') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-person d-block mb-2" style="font-size: 1.5rem;"></i>
                                My Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-person-check text-success" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">{{ $stats['total_admins'] }}</h4>
                    <p class="text-muted">Administrators</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-people text-info" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">{{ $stats['total_tenants'] }}</h4>
                    <p class="text-muted">Tenants</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-house text-warning" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">{{ $stats['total_flats'] }}</h4>
                    <p class="text-muted">Total Flats</p>
                </div>
            </div>
        </div>
    </div>
@endsection
