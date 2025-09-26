@extends('tenant.layouts.app')

@section('title', 'My Payments')
@section('page-title', 'My Payments')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-credit-card me-2"></i>My Payments
        </h4>
    </div>

    @if ($payments->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Bill</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td><span class="badge bg-secondary">#{{ $payment->id }}</span></td>
                                    <td>{{ $payment->bill->title }}</td>
                                    <td class="text-success fw-bold">৳{{ number_format($payment->amount, 2) }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst($payment->payment_method) }}</span></td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-credit-card" style="font-size: 4rem; color: #6c757d;"></i>
            <h5 class="text-muted mt-3">No Payments Found</h5>
            <p class="text-muted">You haven't made any payments yet.</p>
        </div>
    @endif
@endsection
