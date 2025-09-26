@extends('house-owner.layouts.app')

@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-key me-2"></i>Change Password
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ url('/house-owner/profile/change-password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Password must be at least 8 characters long</small>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url('/house-owner/profile') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Profile
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Requirements -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Password Requirements
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>At least 8 characters long</li>
                        <li>Must contain uppercase and lowercase letters</li>
                        <li>Must contain at least one number</li>
                        <li>Must contain at least one special character</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
