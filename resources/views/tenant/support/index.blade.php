@extends('tenant.layouts.app')

@section('title', 'Support')
@section('page-title', 'Support')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-headset me-2"></i>Contact Support
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tenant.support.submit') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> support@example.com</p>
                    <p><strong>Phone:</strong> +880123456789</p>
                    <p><strong>Hours:</strong> 9 AM - 6 PM</p>
                </div>
            </div>
        </div>
    </div>
@endsection
