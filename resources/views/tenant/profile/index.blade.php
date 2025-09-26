@extends('tenant.layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Profile Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $user->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $user->email }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact" name="contact"
                                value="{{ $user->contact }}">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-house-door me-2"></i>Current Assignment
                    </h5>
                </div>
                <div class="card-body">
                    @if ($currentAssignment)
                        <p><strong>Flat:</strong> {{ $currentAssignment->flat->flat_number }}</p>
                        <p><strong>Building:</strong> {{ $currentAssignment->building->name }}</p>
                        <p><strong>Monthly Rent:</strong> ৳{{ number_format($currentAssignment->monthly_rent, 2) }}</p>
                    @else
                        <p class="text-muted">No active assignment</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
