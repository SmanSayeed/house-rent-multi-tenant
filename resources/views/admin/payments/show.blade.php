@extends('admin.layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>Payment #{{ $payment->id }}
                    </h5>
                    <a href="{{ route('admin.payments') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Payments
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Payment ID:</strong></td>
                                    <td>#{{ $payment->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>৳{{ number_format($payment->paid_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if ($payment->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Date:</strong></td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Month:</strong></td>
                                    <td>{{ $payment->payment_month }}/{{ $payment->payment_year }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Bill Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Bill ID:</strong></td>
                                    <td>
                                        <a href="{{ route('admin.bills.show', $payment->bill_id) }}"
                                            class="text-decoration-none">
                                            #{{ $payment->bill_id }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ $payment->bill->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bill Amount:</strong></td>
                                    <td>৳{{ number_format($payment->bill->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Due Date:</strong></td>
                                    <td>{{ $payment->bill->due_date ? $payment->bill->due_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>
                    <h6>Property Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Flat:</strong></td>
                                    <td>{{ $payment->bill->flat->flat_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Building:</strong></td>
                                    <td>{{ $payment->bill->flat->building->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Owner:</strong></td>
                                    <td>{{ $payment->bill->flat->building->owner->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td>{{ $payment->bill->flat->building->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
