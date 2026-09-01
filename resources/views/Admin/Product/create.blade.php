@extends('adminlte::page')

@section('title', 'Add Product')

@section('content_header')

<div class="row mb-2">

    <div class="col-sm-6">
        <h1>Add Product</h1>
    </div>

    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">

            <li class="breadcrumb-item">
                <a href="#">Dashboard</a>
            </li>

            <li class="breadcrumb-item active">
                Product
            </li>

        </ol>
    </div>

</div>

@stop


@section('content')

<form id="productForm"
      action="{{ route('Product.store') }}"
      method="POST">

    @csrf

    <div class="card">

        <div class="card-header bg-primary">

            <h3 class="card-title">
                Product Information
            </h3>

        </div>


        <div class="card-body">

            {{-- GENERAL DETAILS --}}

            <h5 class="mb-3 text-primary">
                <b>General Details</b>
            </h5>


            <div class="row">

                <div class="col-md-6">

                    <x-input
                        label="Product Name *"
                        name="name"
                    />

                </div>


                <div class="col-md-6">

                    <x-select
                        label="Category *"
                        name="category_id"
                        :options="$categories->pluck('name', 'id')->toArray()"
                    />

                </div>

            </div>


            <div class="row">

                <div class="col-md-4">

                    <x-input
                        label="SKU"
                        name="sku"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="UOM *"
                        name="uom"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="MOQ"
                        name="moq"
                        type="number"
                    />

                </div>

            </div>


            <div class="row">

                <div class="col-md-12">

                    <x-textarea
                        label="Description"
                        name="description"
                    />

                </div>

            </div>


            <hr>


            {{-- STOCK & PRICING --}}

            <h5 class="mb-3 text-success">
                <b>Stock & Pricing</b>
            </h5>


            <div class="row">

                <div class="col-md-4">

                    <x-input
                        label="Pack Size"
                        name="pack_size"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="Cost Price"
                        name="cost_price"
                        type="number"
                        step="0.01"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="Selling Price"
                        name="price"
                        type="number"
                        step="0.01"
                    />

                </div>

            </div>


            <div class="row">

                <div class="col-md-6">

                    <x-select
                        label="Feature Product"
                        name="feature_product"
                        :options="[
                            '0' => 'No',
                            '1' => 'Yes'
                        ]"
                    />

                </div>


                <div class="col-md-6">

                    <x-select
                        label="Status *"
                        name="status"
                        :options="[
                            'active' => 'Active',
                            'inactive' => 'Inactive'
                        ]"
                    />

                </div>

            </div>

        </div>


        <div class="card-footer text-right">

            <x-button
                type="submit"
                color="success"
                icon="fas fa-save">

                Save Product

            </x-button>


            <a href="{{ route('Product') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </div>

</form>

@stop


@push('js')

<script>

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("productForm");

    const btn = document.querySelector("button[type='submit']");

    form.addEventListener("submit", function () {

        btn.disabled = true;

        btn.innerText = "Saving...";

    });

});

</script>

@endpush