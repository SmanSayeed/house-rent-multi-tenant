@extends('house-owner.layouts.app')

@section('title', 'Tenant Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-person me-2"></i>Tenant Details
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('house-owner.tenants.edit', $tenant) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit Assignment
            </a>
            <a href="{{ route('house-owner.tenants.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Tenants
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Tenant Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-circle me-2"></i>Tenant Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Name</h6>
                            <p class="mb-0">{{ $tenant->tenant->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Email</h6>
                            <p class="mb-0">{{ $tenant->tenant->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Contact</h6>
                            <p class="mb-0">{{ $tenant->tenant->contact ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Role</h6>
                            <span class="badge bg-info">{{ ucfirst($tenant->tenant->role) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-house-door me-2"></i>Assignment Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Flat</h6>
                        <p class="mb-0">{{ $tenant->flat->flat_number }} - Floor {{ $tenant->flat->floor }}</p>
                        <small class="text-muted">{{ $tenant->building->name }}</small>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Monthly Rent</h6>
                        <p class="mb-0 text-success fw-bold">৳{{ number_format($tenant->monthly_rent, 2) }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Start Date</h6>
                        <p class="mb-0">{{ $tenant->start_date->format('M d, Y') }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">End Date</h6>
                        <p class="mb-0">{{ $tenant->end_date ? $tenant->end_date->format('M d, Y') : 'Ongoing' }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Status</h6>
                        @if ($tenant->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif($tenant->status === 'inactive')
                            <span class="badge bg-warning">Inactive</span>
                        @else
                            <span class="badge bg-danger">Terminated</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('house-owner.tenants.edit', $tenant) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Assignment
                        </a>

                        @if ($tenant->status === 'active')
                            <form action="{{ route('house-owner.tenants.terminate', $tenant) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Are you sure you want to terminate this tenant assignment?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-x-circle me-2"></i>Terminate Assignment
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('house-owner.tenants.destroy', $tenant) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to remove this tenant assignment? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-2"></i>Remove Assignment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
