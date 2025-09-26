@extends('house-owner.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $stats['total_buildings'] }}</div>
                        <div class="stats-label">My Buildings</div>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-building" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $stats['total_flats'] }}</div>
                        <div class="stats-label">Total Flats</div>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-house-door" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-number">{{ $stats['total_bills'] }}</div>
                        <div class="stats-label">Total Bills</div>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-receipt" style="font-size: 2rem;"></i>
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
                    <div class="text-primary">
                        <i class="bi bi-credit-card" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Buildings -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>My Buildings
                    </h5>
                    <a href="{{ url('/house-owner/buildings') }}" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body">
                    @if ($recentBuildings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($recentBuildings as $building)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $building->name }}</h6>
                                        <small class="text-muted">{{ $building->address }}</small>
                                    </div>
                                    <span class="badge bg-success">{{ $building->flats->count() }} flats</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">No buildings found</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>Payment Summary
                    </h5>
                    <a href="{{ url('/house-owner/payments') }}" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-success">{{ $stats['paid_payments'] }}</h4>
                            <small class="text-muted">Paid Payments</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $stats['pending_payments'] }}</h4>
                            <small class="text-muted">Pending Payments</small>
                        </div>
                    </div>
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
                            <a href="{{ url('/house-owner/buildings') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-building d-block mb-2" style="font-size: 1.5rem;"></i>
                                Manage Buildings
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ url('/house-owner/flats') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-house-door d-block mb-2" style="font-size: 1.5rem;"></i>
                                Manage Flats
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ url('/house-owner/bill-categories') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-tags d-block mb-2" style="font-size: 1.5rem;"></i>
                                Bill Categories
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ url('/house-owner/bills') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-receipt d-block mb-2" style="font-size: 1.5rem;"></i>
                                Manage Bills
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
