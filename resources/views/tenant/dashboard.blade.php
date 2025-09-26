@extends('tenant.layouts.app')

@section('title', 'Tenant Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="bi bi-house-door me-2"></i>Welcome back, {{ auth()->user()->name }}!
            </h2>
            <p class="text-muted">Here's what's happening with your rental</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total_bills'] ?? 0 }}</h4>
                            <p class="card-text">Total Bills</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-receipt" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['pending_bills'] ?? 0 }}</h4>
                            <p class="card-text">Pending Bills</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['paid_bills'] ?? 0 }}</h4>
                            <p class="card-text">Paid Bills</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">৳{{ number_format($stats['total_amount'] ?? 0, 2) }}</h4>
                            <p class="card-text">Total Amount</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Bills -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Recent Bills
                    </h5>
                </div>
                <div class="card-body">
                    @if (isset($recentBills) && $recentBills->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Bill</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentBills as $bill)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0">{{ $bill->title }}</h6>
                                                <small class="text-muted">{{ $bill->category->name }}</small>
                                            </td>
                                            <td class="fw-bold">৳{{ number_format($bill->amount, 2) }}</td>
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
                                                <a href="{{ route('tenant.bills.show', $bill) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('tenant.bills') }}" class="btn btn-primary">View All Bills</a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="text-muted mt-3">No Bills Yet</h5>
                            <p class="text-muted">You don't have any bills assigned to you yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flat Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-house-door me-2"></i>My Flat
                    </h5>
                </div>
                <div class="card-body">
                    @if (isset($currentAssignment) && $currentAssignment)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Flat Number</h6>
                            <p class="mb-0 fw-bold">{{ $currentAssignment->flat->flat_number }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Floor</h6>
                            <p class="mb-0">{{ $currentAssignment->flat->floor }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Building</h6>
                            <p class="mb-0">{{ $currentAssignment->building->name }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Monthly Rent</h6>
                            <p class="mb-0 fw-bold text-success">৳{{ number_format($currentAssignment->monthly_rent, 2) }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Assignment Start</h6>
                            <p class="mb-0">{{ $currentAssignment->start_date->format('M d, Y') }}</p>
                        </div>

                        @if ($currentAssignment->end_date)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Assignment End</h6>
                                <p class="mb-0">{{ $currentAssignment->end_date->format('M d, Y') }}</p>
                            </div>
                        @endif

                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Status</h6>
                            @if ($currentAssignment->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($currentAssignment->status === 'inactive')
                                <span class="badge bg-warning">Inactive</span>
                            @else
                                <span class="badge bg-danger">Terminated</span>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-house-door" style="font-size: 3rem; color: #6c757d;"></i>
                            <h5 class="text-muted mt-3">No Flat Assigned</h5>
                            <p class="text-muted">You don't have any flat assigned to you yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('tenant.bills') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-receipt me-2"></i>View All Bills
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('tenant.payments') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-credit-card me-2"></i>Payment History
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('tenant.profile') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-person me-2"></i>Update Profile
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('tenant.support') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-headset me-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
