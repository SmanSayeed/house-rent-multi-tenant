@extends('admin.layouts.app')

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person me-2"></i>Edit Profile
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ url('/admin/profile/update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Information</label>
                            <input type="text" class="form-control @error('contact') is-invalid @enderror" id="contact"
                                name="contact" value="{{ old('contact', $user->contact) }}"
                                placeholder="Phone number or other contact info">
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" readonly>
                            <small class="form-text text-muted">Role cannot be changed</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <div>
                                <a href="{{ url('/admin/profile/change-password') }}" class="btn btn-warning me-2">
                                    <i class="bi bi-key me-2"></i>Change Password
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-2"></i>Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
