@extends('admin.layouts.app')

@section('title', 'Building Flats - ' . $building->name)
@section('page-title', 'Building Flats - ' . $building->name)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-house-door me-2"></i>Flats in {{ $building->name }}
                    </h5>
                    <a href="{{ route('admin.buildings') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Buildings
                    </a>
                </div>
                <div class="card-body">
                    @if ($flats->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Flat Number</th>
                                        <th>Rent Amount</th>
                                        <th>Current Tenant</th>
                                        <th>Bill Categories</th>
                                        <th>Total Bills</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($flats as $flat)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="bi bi-house-door"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $flat->flat_number }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>৳{{ number_format($flat->rent_amount, 2) }}</td>
                                            <td>
                                                @if ($flat->tenantAssignments->count() > 0)
                                                    @foreach ($flat->tenantAssignments as $assignment)
                                                        <span
                                                            class="badge bg-success">{{ $assignment->tenant->name ?? 'N/A' }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="badge bg-warning">No Tenant</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($flat->bills->count() > 0)
                                                    @foreach ($flat->bills->groupBy('category_id') as $categoryBills)
                                                        @php $category = $categoryBills->first()->category; @endphp
                                                        <span
                                                            class="badge bg-info me-1">{{ $category->name ?? 'N/A' }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No bills</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $flat->bills->count() }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#assignTenantModal{{ $flat->id }}">
                                                        <i class="bi bi-person-plus"></i> Assign Tenant
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewBillsModal{{ $flat->id }}">
                                                        <i class="bi bi-receipt"></i> View Bills
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-house-door text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No flats found</h5>
                            <p class="text-muted">No flats have been created for this building yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Tenant Modal for each flat -->
    @foreach ($flats as $flat)
        <div class="modal fade" id="assignTenantModal{{ $flat->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Tenant to {{ $flat->flat_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.tenants.assign') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="flat_id" value="{{ $flat->id }}">
                            <div class="mb-3">
                                <label for="tenant_id" class="form-label">Select Tenant</label>
                                <select class="form-select" name="tenant_id" required>
                                    <option value="">Choose a tenant...</option>
                                    @foreach (\App\Models\User::where('role', 'tenant')->get() as $tenant)
                                        <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign Tenant</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- View Bills Modal for each flat -->
    @foreach ($flats as $flat)
        <div class="modal fade" id="viewBillsModal{{ $flat->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bills for {{ $flat->flat_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if ($flat->bills->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($flat->bills as $bill)
                                            <tr>
                                                <td>{{ $bill->category->name ?? 'N/A' }}</td>
                                                <td>৳{{ number_format($bill->amount, 2) }}</td>
                                                <td>
                                                    @if ($bill->status === 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @else
                                                        <span class="badge bg-warning">Unpaid</span>
                                                    @endif
                                                </td>
                                                <td>{{ $bill->due_date ? $bill->due_date->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No bills found for this flat.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
