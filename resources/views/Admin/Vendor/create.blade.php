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

    <div class="card-header">
        <h3 class="card-title">Add Vendor</h3>
    </div>

    <form action="{{ route('Vendors.store') }}" method="POST">
        @csrf

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <x-input
                        label="Vendor Name *"
                        name="name"
                    />

                    <x-input
                        label="Email"
                        name="email"
                        type="email"
                    />

                    <x-input
                        label="Contact"
                        name="contact"
                    />

                </div>

                <div class="col-md-6">

                    <x-input
                        label="Company Name"
                        name="company_name"
                    />

                    <x-input
                        label="GST Number"
                        name="gst_number"
                    />

                    <x-input
                        label="City"
                        name="city"
                    />

                    <x-input
                        label="State"
                        name="state"
                    />

                </div>

            </div>

            <div class="form-group mt-2">

                <x-textarea
                    label="Address"
                    name="address"
                />

            </div>

        </div>

        <div class="card-footer text-right">

            <a href="{{ route('Vendors') }}" class="btn btn-secondary">
                Cancel
            </a>

            <x-button
                type="submit"
                color="success"
                icon="fas fa-save">

                Save Vendor

            </x-button>

        </div>

    </form>

</div>

@stop
 
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