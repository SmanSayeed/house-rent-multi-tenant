@extends('admin.layouts.app')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>System Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">System Name</label>
                                <input type="text" class="form-control" value="Multi-Tenant System" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Version</label>
                                <input type="text" class="form-control" value="1.0.0" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Environment</label>
                                <input type="text" class="form-control" value="{{ app()->environment() }}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Debug Mode</label>
                                <input type="text" class="form-control"
                                    value="{{ config('app.debug') ? 'Enabled' : 'Disabled' }}" readonly>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-database me-2"></i>Database Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Driver</label>
                            <input type="text" class="form-control" value="{{ config('database.default') }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control"
                                value="{{ config('database.connections.mysql.database') }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>System Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Application Status</span>
                        <span class="badge bg-success">Online</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Database Status</span>
                        <span class="badge bg-success">Connected</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Cache Status</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Queue Status</span>
                        <span class="badge bg-success">Running</span>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check me-2"></i>Security
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>CSRF Protection</span>
                        <span class="badge bg-success">Enabled</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Password Hashing</span>
                        <span class="badge bg-success">Bcrypt</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Session Security</span>
                        <span class="badge bg-success">Secure</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
