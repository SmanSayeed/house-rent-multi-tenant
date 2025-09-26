@extends('house-owner.layouts.app')

@section('title', 'My Payments')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-credit-card me-2"></i>My Payments
        </h4>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($payments->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Bill</th>
                                <th>Tenant</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $payment->id }}</span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0">{{ $payment->bill->title }}</h6>
                                        <small class="text-muted">{{ $payment->bill->flat->flat_number }} -
                                            {{ $payment->bill->flat->building->name }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $payment->tenant->name }}</span>
                                        <br>
                                        <small class="text-muted">{{ $payment->tenant->email }}</small>
                                    </td>
                                    <td class="text-success fw-bold">৳{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($payment->payment_method) }}</span>
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
                                    <td>
                                        {{ $payment->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('house-owner.bills.show', $payment->bill) }}"
                                                class="btn btn-outline-primary" title="View Bill">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if ($payment->transaction_id)
                                                <span class="btn btn-outline-info"
                                                    title="Transaction ID: {{ $payment->transaction_id }}">
                                                    <i class="bi bi-receipt"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $payments->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-credit-card" style="font-size: 4rem; color: #6c757d;"></i>
            </div>
            <h5 class="text-muted mb-3">No Payments Found</h5>
            <p class="text-muted mb-4">No payments have been recorded yet.</p>
        </div>
    @endif
@endsection
