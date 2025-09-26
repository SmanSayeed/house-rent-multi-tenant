@extends('admin.layouts.app')

@section('title', 'User Details')
@section('page-title', 'User Details')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>User Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Name</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Role</label>
                            <p class="form-control-plaintext">
                                <span
                                    class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'house_owner' ? 'success' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Contact</label>
                            <p class="form-control-plaintext">{{ $user->contact ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Created At</label>
                            <p class="form-control-plaintext">{{ $user->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="form-control-plaintext">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>Buildings
                    </h5>
                </div>
                <div class="card-body">
                    @if ($user->buildings->count() > 0)
                        @foreach ($user->buildings as $building)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $building->name }}</h6>
                                    <small class="text-muted">{{ $building->address }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No buildings found</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2"></i>Tenant Assignments
                    </h5>
                </div>
                <div class="card-body">
                    @if ($user->tenantAssignments->count() > 0)
                        @foreach ($user->tenantAssignments as $assignment)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-0">{{ $assignment->building->name ?? 'N/A' }}</h6>
                                    <small class="text-muted">Flat: {{ $assignment->flat->flat_number ?? 'N/A' }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No tenant assignments found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ url('/admin/users') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>
@endsection
