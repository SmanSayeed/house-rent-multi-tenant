@extends('admin.layouts.app')

@section('title', 'Tenant Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-person me-2"></i>Tenant Details
    </h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-2"></i>Edit Tenant
        </a>
        <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary">
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>Personal Information
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center">
                        {{ substr($tenant->name, 0, 1) }}
                    </div>
                    <h5 class="mt-2 mb-0">{{ $tenant->name }}</h5>
                    <p class="text-muted">{{ $tenant->email }}</p>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Email Address</label>
                        <p class="mb-0">{{ $tenant->email }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Contact Number</label>
                        <p class="mb-0">{{ $tenant->contact ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Role</label>
                        <p class="mb-0">
                            <span class="badge bg-info">Tenant</span>
                        </p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Account Created</label>
                        <p class="mb-0">{{ $tenant->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Last Updated</label>
                        <p class="mb-0">{{ $tenant->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Assignment -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-house me-2"></i>Current Assignment
                </h5>
            </div>
            <div class="card-body">
                @if($tenant->tenantAssignments->where('status', 'active')->count() > 0)
                    @php
                        $activeAssignment = $tenant->tenantAssignments->where('status', 'active')->first();
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Building</label>
                            <p class="mb-0">{{ $activeAssignment->flat->building->name }}</p>
                            <small class="text-muted">{{ $activeAssignment->flat->building->address }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Flat Number</label>
                            <p class="mb-0">{{ $activeAssignment->flat->flat_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Monthly Rent</label>
                            <p class="mb-0">Tk {{ number_format($activeAssignment->monthly_rent, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Assignment Date</label>
                            <p class="mb-0">{{ $activeAssignment->start_date->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#removeAssignmentModal">
                            <i class="bi bi-person-dash me-1"></i>Remove from Flat
                        </button>
                        <a href="{{ route('admin.buildings.show', $activeAssignment->flat->building) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-building me-1"></i>View Building
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-house display-4 text-muted"></i>
                        <h5 class="mt-3 text-muted">No Active Assignment</h5>
                        <p class="text-muted">This tenant is not currently assigned to any flat.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignFlatModal">
                            <i class="bi bi-person-plus me-2"></i>Assign to Flat
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Assignment History -->
        @if($tenant->tenantAssignments->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Assignment History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Flat</th>
                                    <th>Building</th>
                                    <th>Rent</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenant->tenantAssignments->sortByDesc('created_at') as $assignment)
                                    <tr>
                                        <td>{{ $assignment->flat->flat_number }}</td>
                                        <td>{{ $assignment->flat->building->name }}</td>
                                        <td>Tk {{ number_format($assignment->monthly_rent, 2) }}</td>
                                        <td>{{ $assignment->start_date->format('M d, Y') }}</td>
                                        <td>{{ $assignment->end_date ? $assignment->end_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            @if($assignment->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Terminated</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Assign Flat Modal -->
<div class="modal fade" id="assignFlatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Tenant to Flat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.tenants.assign', $tenant) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_flat_id" class="form-label">Select Flat *</label>
                        <select class="form-select" id="modal_flat_id" name="flat_id" required>
                            <option value="">Choose a flat</option>
                            @foreach($buildings as $building)
                                <optgroup label="{{ $building->name }}">
                                    @foreach($building->flats as $flat)
                                        <option value="{{ $flat->id }}" 
                                                data-rent="{{ $flat->rent_amount }}">
                                            {{ $flat->flat_number }} - {{ $building->name }}
                                            @if($flat->rent_amount)
                                                (Tk {{ number_format($flat->rent_amount) }})
                                            @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_monthly_rent" class="form-label">Monthly Rent *</label>
                        <input type="number" class="form-control" id="modal_monthly_rent" 
                               name="monthly_rent" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_start_date" class="form-label">Start Date *</label>
                        <input type="date" class="form-control" id="modal_start_date" 
                               name="start_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign to Flat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Remove Assignment Modal -->
@if($tenant->tenantAssignments->where('status', 'active')->count() > 0)
    <div class="modal fade" id="removeAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Tenant from Flat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.tenants.remove', $tenant) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="assignment_id" value="{{ $activeAssignment->id }}">
                        <p>Are you sure you want to remove this tenant from their current flat?</p>
                        <div class="alert alert-warning">
                            <strong>Current Assignment:</strong><br>
                            {{ $activeAssignment->flat->flat_number }} - {{ $activeAssignment->flat->building->name }}
                        </div>
                        <div class="mb-3">
                            <label for="modal_end_date" class="form-label">End Date *</label>
                            <input type="date" class="form-control" id="modal_end_date" 
                                   name="end_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Remove from Flat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<style>
    .avatar-lg {
        width: 80px;
        height: 80px;
        font-size: 32px;
        font-weight: 600;
    }
</style>

<script>
// Auto-fill monthly rent when flat is selected in modal
document.getElementById('modal_flat_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const rentAmount = selectedOption.getAttribute('data-rent');
    const monthlyRentInput = document.getElementById('modal_monthly_rent');
    
    if (rentAmount && rentAmount !== 'null') {
        monthlyRentInput.value = rentAmount;
    } else {
        monthlyRentInput.value = '';
    }
});
</script>
@endsection
