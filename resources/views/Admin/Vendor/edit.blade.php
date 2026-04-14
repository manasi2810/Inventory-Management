@extends('adminlte::page')

@section('title', 'Edit Vendor')

@section('content_header')
<div class="row mb-2">

    <div class="col-sm-6">
        <h1>Edit Vendor</h1>
    </div>

    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">Vendor</li>
        </ol>
    </div>

</div>
@stop

@section('content')

<div class="card">

    {{-- HEADER --}}
    <div class="card-header">
        <h3 class="card-title">Update Vendor</h3>
    </div>

    {{-- FORM --}}
    <form action="{{ route('Vendor.update', $vendor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="row">

                {{-- LEFT SIDE --}}
                <div class="col-md-6">

                    <div class="form-group">
                        <label>Vendor Name *</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $vendor->name) }}"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email', $vendor->email) }}">
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="{{ old('phone', $vendor->phone) }}">
                    </div>

                </div>

                {{-- RIGHT SIDE --}}
                <div class="col-md-6">

                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text"
                               name="company_name"
                               class="form-control"
                               value="{{ old('company_name', $vendor->company_name) }}">
                    </div>

                    <div class="form-group">
                        <label>GST Number</label>
                        <input type="text"
                               name="gst_number"
                               class="form-control"
                               value="{{ old('gst_number', $vendor->gst_number) }}">
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">

                            <option value="active"
                                {{ $vendor->status == 'active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="inactive"
                                {{ $vendor->status == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>
                    </div>

                </div>

            </div>

            {{-- FULL WIDTH ADDRESS --}}
            <div class="form-group mt-2">
                <label>Address</label>
                <textarea name="address" class="form-control">{{ old('address', $vendor->address) }}</textarea>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="card-footer text-right">

            <a href="{{ route('Vendor.index') }}" class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" id="saveBtn" class="btn btn-success">
                <i class="fas fa-save"></i> Update Vendor
            </button>

        </div>

    </form>

</div>

@stop

{{-- SCRIPT --}}
@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const btn = document.getElementById("saveBtn");

    form.addEventListener("submit", function () {
        btn.disabled = true;
        btn.innerHTML = "Updating...";
    });

});
</script>
@endpush