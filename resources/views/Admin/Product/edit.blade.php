@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content_header')

<div class="row mb-2">

    <div class="col-sm-6">
        <h1>Edit Product</h1>
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

{{-- Validation Errors --}}

@if ($errors->any())

    <div class="alert alert-danger">

        <ul class="mb-0">

            @foreach ($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<form id="productForm" 
      action="{{ route('Product.update', $product->id) }}" 
      method="POST" 
      enctype="multipart/form-data">

    @csrf
    @method('PUT')


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
                        :value="$product->name"
                        :required="true"
                    />

                </div>


                <div class="col-md-6">

                    <x-select
                        label="Category *"
                        name="category_id"
                        :options="$categories->pluck('name', 'id')->toArray()"
                        :selected="$product->category_id"
                    />

                </div>

            </div>


            <div class="row">

                <div class="col-md-4">

                    <x-input
                        label="SKU"
                        name="sku"
                        :value="$product->sku"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="UOM *"
                        name="uom"
                        :value="$product->uom"
                        :required="true"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="MOQ"
                        name="moq"
                        type="number"
                        :value="$product->moq"
                    />

                </div>

            </div>


            <div class="row">

                <div class="col-md-12">

                    <x-textarea
                        label="Description"
                        name="description"
                        :value="$product->description"
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
                        :value="$product->pack_size"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="Cost Price"
                        name="cost_price"
                        type="number"
                        step="0.01"
                        :value="$product->cost_price"
                    />

                </div>


                <div class="col-md-4">

                    <x-input
                        label="Selling Price"
                        name="price"
                        type="number"
                        step="0.01"
                        :value="$product->price"
                    />

                </div>

            </div>


            <div class="row">

                <div class="col-md-6">

                    <x-select
                        label="Feature Product"
                        name="feature_product"
                        :options="[
                            '1' => 'Yes',
                            '0' => 'No'
                        ]"
                        :selected="$product->feature_product"
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
                        :selected="$product->status"
                    />

                </div>

            </div>

        </div>


        {{-- FOOTER --}}

        <div class="card-footer text-right">

            <x-button
                type="submit"
                color="success"
                icon="fas fa-save">

                Update Product

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

    const btn = form.querySelector("button[type='submit']");

    form.addEventListener("submit", function () {

        btn.disabled = true;

        btn.innerText = "Updating...";

    });

});

</script>

@endpush