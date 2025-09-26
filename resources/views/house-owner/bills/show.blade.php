@extends('house-owner.layouts.app')

@section('title', 'Bill Details')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-receipt me-2"></i>Bill Details
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('house-owner.bills.invoice', $bill) }}" class="btn btn-outline-info">
                <i class="bi bi-download me-2"></i>Download Invoice
            </a>
            <a href="{{ route('house-owner.bills.edit', $bill) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit Bill
            </a>
            <a href="{{ route('house-owner.bills.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Bills
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
        <!-- Bill Information -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Bill Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Bill ID</h6>
                            <p class="mb-0"><span class="badge bg-secondary">#{{ $bill->id }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Title</h6>
                            <p class="mb-0">{{ $bill->title }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Category</h6>
                            <span class="badge bg-primary">{{ $bill->category->name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Amount</h6>
                            <p class="mb-0 text-success fw-bold fs-5">৳{{ number_format($bill->amount, 2) }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Due Date</h6>
                            <p class="mb-0">{{ $bill->due_date->format('M d, Y') }}</p>
                            @if ($bill->due_date->isPast() && $bill->status !== 'paid')
                                <small class="text-danger">Overdue</small>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted mb-2">Status</h6>
                            @if ($bill->status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($bill->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Overdue</span>
                            @endif
                        </div>
                    </div>

                    @if ($bill->description)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $bill->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flat Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-house-door me-2"></i>Flat Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Flat Number</h6>
                        <p class="mb-0">{{ $bill->flat->flat_number }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Floor</h6>
                        <p class="mb-0">{{ $bill->flat->floor }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Building</h6>
                        <p class="mb-0">{{ $bill->flat->building->name }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Rent Amount</h6>
                        <p class="mb-0 text-success fw-bold">৳{{ number_format($bill->flat->rent_amount, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments -->
    @if ($bill->payments->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-credit-card me-2"></i>Payment History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Transaction ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bill->payments as $payment)
                                        <tr>
                                            <td><span class="badge bg-secondary">#{{ $payment->id }}</span></td>
                                            <td class="text-success fw-bold">৳{{ number_format($payment->amount, 2) }}</td>
                                            <td><span
                                                    class="badge bg-primary">{{ ucfirst($payment->payment_method) }}</span>
                                            </td>
                                            <td>
                                                @if ($payment->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($payment->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                            <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                        <a href="{{ route('house-owner.bills.edit', $bill) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Bill
                        </a>

                        <a href="{{ route('house-owner.bills.invoice', $bill) }}" class="btn btn-info">
                            <i class="bi bi-download me-2"></i>Download Invoice
                        </a>

                        @if ($bill->status !== 'paid')
                            <form action="{{ route('house-owner.bills.mark-as-paid', $bill) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Are you sure you want to mark this bill as paid?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Mark as Paid
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('house-owner.bills.destroy', $bill) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this bill? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-2"></i>Delete Bill
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
