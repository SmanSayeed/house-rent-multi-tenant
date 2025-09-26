@extends('house-owner.layouts.app')

@section('title', $building->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-building me-2"></i>{{ $building->name }}
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('house-owner.buildings.edit', $building) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit Building
            </a>
            <a href="{{ route('house-owner.buildings.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Buildings
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
        <!-- Building Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Building Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Building Name</h6>
                            <p class="mb-0">{{ $building->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">City</h6>
                            <p class="mb-0">{{ $building->city }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Address</h6>
                        <p class="mb-0">{{ $building->address }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted mb-2">State</h6>
                            <p class="mb-0">{{ $building->state }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted mb-2">Postal Code</h6>
                            <p class="mb-0">{{ $building->postal_code }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted mb-2">Country</h6>
                            <p class="mb-0">{{ $building->country }}</p>
                        </div>
                    </div>

                    @if ($building->description)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $building->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Building Statistics -->
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
                            <h3 class="text-success mb-0">{{ $building->flats->count() }}</h3>
                            <small class="text-muted">Total Flats</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-info mb-0">{{ $building->flats->where('status', 'occupied')->count() }}</h3>
                            <small class="text-muted">Occupied</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-warning mb-0">{{ $building->flats->where('status', 'available')->count() }}
                            </h3>
                            <small class="text-muted">Available</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-danger mb-0">{{ $building->flats->where('status', 'maintenance')->count() }}
                            </h3>
                            <small class="text-muted">Maintenance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flats Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-house-door me-2"></i>Flats in this Building
                    </h5>
                    <a href="{{ route('house-owner.flats.create', ['building' => $building->id]) }}"
                        class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle me-2"></i>Add Flat
                    </a>
                </div>
                <div class="card-body">
                    @if ($building->flats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Flat Number</th>
                                        <th>Floor</th>
                                        <th>Rent Amount</th>
                                        <th>Status</th>
                                        <th>Tenant</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($building->flats as $flat)
                                        <tr>
                                            <td>
                                                <strong>{{ $flat->flat_number }}</strong>
                                            </td>
                                            <td>{{ $flat->floor }}</td>
                                            <td>
                                                <span
                                                    class="text-success fw-bold">৳{{ number_format($flat->rent_amount, 2) }}</span>
                                            </td>
                                            <td>
                                                @if ($flat->status === 'occupied')
                                                    <span class="badge bg-success">Occupied</span>
                                                @elseif($flat->status === 'available')
                                                    <span class="badge bg-info">Available</span>
                                                @else
                                                    <span class="badge bg-warning">Maintenance</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($flat->tenantAssignments->where('status', 'active')->first())
                                                    <span class="text-primary">
                                                        {{ $flat->tenantAssignments->where('status', 'active')->first()->tenant->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No tenant</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('house-owner.flats.show', $flat) }}"
                                                        class="btn btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('house-owner.flats.edit', $flat) }}"
                                                        class="btn btn-outline-success">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-house-door" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="text-muted mt-3">No Flats Found</h5>
                            <p class="text-muted">This building doesn't have any flats yet.</p>
                            <a href="{{ route('house-owner.flats.create', ['building' => $building->id]) }}"
                                class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add First Flat
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
