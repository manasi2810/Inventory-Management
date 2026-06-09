@extends('adminlte::page')

@section('title', 'Edit Vendor')

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
        <h3 class="card-title">Update Vendor</h3>
    </div>

    <form action="{{ route('Vendors.update', $vendor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <x-input
                        label="Vendor Name *"
                        name="name"
                        :value="old('name', $vendor->name)"
                    />

                    <x-input
                        label="Email"
                        name="email"
                        type="email"
                        :value="old('email', $vendor->email)"
                    />

                    <x-input
                        label="Contact"
                        name="contact"
                        :value="old('contact', $vendor->contact)"
                    />

                </div>

                <div class="col-md-6">

                    <x-input
                        label="Company Name"
                        name="company_name"
                        :value="old('company_name', $vendor->company_name)"
                    />

                    <x-input
                        label="GST Number"
                        name="gst_number"
                        :value="old('gst_number', $vendor->gst_number)"
                    />

                    <x-input
                        label="City"
                        name="city"
                        :value="old('city', $vendor->city)"
                    />

                    <x-input
                        label="State"
                        name="state"
                        :value="old('state', $vendor->state)"
                    />

                </div>

            </div>

            <div class="form-group mt-2">

                <x-textarea
                    label="Address"
                    name="address"
                    :value="old('address', $vendor->address)"
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

                Update Vendor

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
        btn.innerHTML = "Updating...";
    });

});
</script>
@endpush