@extends('layouts.app')
@section('title', 'Add Client')
@section('page-title', 'Add New Client')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-handshake"></i> New Client Details</h3>
                <a href="{{ route('clients.index') }}" class="button button-secondary small">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
            <form action="{{ route('clients.store') }}" method="POST" class="form-card">
                @csrf
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Contact Person / Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-input" value="{{ old('name') }}" placeholder="e.g. Rahul Sharma" required />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Company / Business Name</label>
                        <input type="text" name="company" class="form-input" value="{{ old('company') }}" placeholder="e.g. XYZ Solutions Pvt Ltd" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="client@example.com" />
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="+91 98765 43210" />
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-input" rows="3" placeholder="Full billing address...">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">GST Number</label>
                        <input type="text" name="gst_no" class="form-input" value="{{ old('gst_no') }}" placeholder="22AAAAA0000A1Z5" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">PAN Number</label>
                        <input type="text" name="pan_no" class="form-input" value="{{ old('pan_no') }}" placeholder="AAAAA9999A" />
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-input" required>
                            <option value="active" {{ old('status','active')==='active'?'selected':'' }}>Active</option>
                            <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Internal Notes</label>
                        <textarea name="notes" class="form-input" rows="2" placeholder="Any internal notes about this client...">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="button button-primary px-5 py-2">
                            <i class="fas fa-save me-2"></i>Save Client
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
