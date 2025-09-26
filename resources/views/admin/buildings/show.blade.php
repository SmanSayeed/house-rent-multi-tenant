@extends('admin.layouts.app')

@section('title', 'Building Details')
@section('page-title', 'Building Details')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>Building Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Building Name</label>
                            <p class="form-control-plaintext">{{ $building->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Owner</label>
                            <p class="form-control-plaintext">{{ $building->owner->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <p class="form-control-plaintext">{{ $building->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created At</label>
                            <p class="form-control-plaintext">{{ $building->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="form-control-plaintext">{{ $building->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flats Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-house-door me-2"></i>Flats ({{ $building->flats->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if ($building->flats->count() > 0)
                        <div class="row">
                            @foreach ($building->flats as $flat)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $flat->flat_number }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Owner: {{ $flat->owner_name ?? 'N/A' }}<br>
                                                    Contact: {{ $flat->owner_contact ?? 'N/A' }}
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No flats found for this building.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Building Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Building Statistics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="text-primary">{{ $building->flats->count() }}</h4>
                            <small class="text-muted">Total Flats</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success">{{ $building->flats->where('status', 'occupied')->count() }}</h4>
                            <small class="text-muted">Occupied</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-warning">{{ $building->flats->where('status', 'available')->count() }}</h4>
                            <small class="text-muted">Available</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-info">
                                {{ $building->flats->sum('rent_amount') ? 'Tk ' . number_format($building->flats->sum('rent_amount'), 2) : 'N/A' }}
                            </h4>
                            <small class="text-muted">Total Rent</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ url('/admin/buildings') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Buildings
            </a>
        </div>
    </div>
@endsection
