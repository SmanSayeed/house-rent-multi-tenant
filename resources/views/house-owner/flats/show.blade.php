@extends('house-owner.layouts.app')

@section('title', 'Flat ' . $flat->flat_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-house-door me-2"></i>Flat {{ $flat->flat_number }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('house-owner.flats.edit', $flat) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit Flat
            </a>
            <a href="{{ route('house-owner.flats.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Flats
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
        <!-- Flat Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Flat Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Flat Number</h6>
                            <p class="mb-0">{{ $flat->flat_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Building</h6>
                            <p class="mb-0">{{ $flat->building->name }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted mb-2">Floor</h6>
                            <p class="mb-0">{{ $flat->floor }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted mb-2">Rent Amount</h6>
                            <p class="mb-0 text-success fw-bold">৳{{ number_format($flat->rent_amount, 2) }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted mb-2">Status</h6>
                            @if ($flat->status === 'occupied')
                                <span class="badge bg-success">Occupied</span>
                            @elseif($flat->status === 'available')
                                <span class="badge bg-info">Available</span>
                            @else
                                <span class="badge bg-warning">Maintenance</span>
                            @endif
                        </div>
                    </div>

                    @if ($flat->description)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $flat->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flat Statistics -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h3 class="text-primary mb-0">{{ $flat->bills->count() }}</h3>
                            <small class="text-muted">Total Bills</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-success mb-0">{{ $flat->bills->where('status', 'paid')->count() }}</h3>
                            <small class="text-muted">Paid Bills</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-warning mb-0">{{ $flat->bills->where('status', 'pending')->count() }}</h3>
                            <small class="text-muted">Pending Bills</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-danger mb-0">{{ $flat->bills->where('status', 'overdue')->count() }}</h3>
                            <small class="text-muted">Overdue Bills</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Tenant Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Current Tenant
                    </h5>
                </div>
                <div class="card-body">
                    @if ($flat->tenantAssignments->where('status', 'active')->first())
                        @php
                            $activeAssignment = $flat->tenantAssignments->where('status', 'active')->first();
                            $tenant = $activeAssignment->tenant;
                        @endphp
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Tenant Name</h6>
                                <p class="mb-0">{{ $tenant->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Contact</h6>
                                <p class="mb-0">{{ $tenant->contact ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Email</h6>
                                <p class="mb-0">{{ $tenant->email }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Monthly Rent</h6>
                                <p class="mb-0 text-success fw-bold">
                                    ৳{{ number_format($activeAssignment->monthly_rent, 2) }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Start Date</h6>
                                <p class="mb-0">{{ $activeAssignment->start_date->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">End Date</h6>
                                <p class="mb-0">
                                    {{ $activeAssignment->end_date ? $activeAssignment->end_date->format('M d, Y') : 'Ongoing' }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-x" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="text-muted mt-3">No Tenant Assigned</h5>
                            <p class="text-muted">This flat doesn't have an active tenant.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bills Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Recent Bills
                    </h5>
                    <a href="#" class="btn btn-light btn-sm"
                        onclick="alert('Bill management will be available in the next step')">
                        <i class="bi bi-plus-circle me-2"></i>Add Bill
                    </a>
                </div>
                <div class="card-body">
                    @if ($flat->bills->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($flat->bills->take(5) as $bill)
                                        <tr>
                                            <td>{{ $bill->title }}</td>
                                            <td class="text-success fw-bold">৳{{ number_format($bill->amount, 2) }}</td>
                                            <td>{{ $bill->due_date->format('M d, Y') }}</td>
                                            <td>
                                                @if ($bill->status === 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($bill->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Overdue</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-outline-primary btn-sm"
                                                    onclick="alert('Bill management will be available in the next step')">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="text-muted mt-3">No Bills Found</h5>
                            <p class="text-muted">This flat doesn't have any bills yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
