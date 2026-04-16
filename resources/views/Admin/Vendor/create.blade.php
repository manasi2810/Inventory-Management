@extends('adminlte::page')

@section('title', 'Vendor Create')

@section('content_header')
<div class="row mb-2">

    <div class="col-sm-6">
        <h1>Vendor</h1>
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
        <h3 class="card-title">Add Vendor</h3>
    </div>

    {{-- FORM --}}
    <form action="{{ route('Vendors.store') }}" method="POST">
        @csrf

        <div class="card-body">

            <div class="row">

                {{-- LEFT SIDE --}}
                <div class="col-md-6">

                    <div class="form-group">
                        <label>Vendor Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Contact</label>
                        <input type="text" name="contact" class="form-control">
                    </div>

                </div>

                {{-- RIGHT SIDE --}}
                <div class="col-md-6">

                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>GST Number</label>
                        <input type="text" name="gst_number" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" class="form-control">
                    </div>

                </div>

            </div>

            {{-- FULL WIDTH ADDRESS --}}
            <div class="form-group mt-2">
                <label>Address</label>
                <textarea name="address" class="form-control"></textarea>
            </div>

        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="card-footer text-right">

            <a href="{{ route('Vendors') }}" class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" id="saveBtn" class="btn btn-success">
                <i class="fas fa-save"></i> Save Vendor
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
        btn.innerHTML = "Saving...";
    });

});
</script>
@endpush