@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content_header')
<h1>Edit Product</h1>
@stop

@section('content')

<form id="productForm"
      action="{{ route('Product.update', $product->id) }}"
      method="POST"
      enctype="multipart/form-data">

@csrf
@method('PUT')

<div class="row">

    {{-- LEFT SIDE --}}

    <div class="col-md-6">

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    General Details
                </h3>

            </div>

            <div class="card-body">

                <x-input
                    label="Product Name *"
                    name="name"
                    :value="$product->name"
                    :required="true"
                />

                <x-select
                    label="Category *"
                    name="category_id"
                    :options="$categories->pluck('name', 'id')->toArray()"
                    :selected="$product->category_id"
                />

                <x-input
                    label="SKU"
                    name="sku"
                    :value="$product->sku"
                />

                <x-textarea
                    label="Description"
                    name="description"
                    :value="$product->description"
                />

                <div class="row">

                    {{-- <div class="col-md-6">

                        <x-input
                            label="Opening Stock"
                            name="opening_stock"
                            type="number"
                            :value="$product->opening_stock"
                        />

                    </div> --}}

                    <div class="col-md-6">

                        <x-input
                            label="Pack Size"
                            name="pack_size"
                            :value="$product->pack_size"
                        />

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">

                        <x-input
                            label="MOQ"
                            name="moq"
                            type="number"
                            :value="$product->moq"
                        />

                    </div>

                    <div class="col-md-6">

                        <x-input
                            label="UOM *"
                            name="uom"
                            :value="$product->uom"
                            :required="true"
                        />

                    </div>

                </div>

                <x-input
                    label="Price"
                    name="price"
                    type="number"
                    :value="$product->price"
                />

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

        </div>

    </div>

    {{-- RIGHT SIDE --}}

    <div class="col-md-6">

        {{-- PRODUCT IMAGES --}}

        <div class="card">

            <div class="card-header">

                <h3 class="card-title">
                    Product Images
                </h3>

            </div>

            <div class="card-body">

                <label>
                    Current Main Image
                </label>

                <br>

                @php
                    $image = $product->images()
                                     ->where('type','main')
                                     ->value('image_path');
                @endphp

                @if($image)

                    <img src="{{ asset('storage/' . $image) }}"
                         width="120">

                @else

                    <p class="text-danger">
                        No Main Image Found
                    </p>

                @endif

                <div class="mt-3">

                    <x-file-input
                        label="Change Main Image"
                        name="main_image"
                    />

                </div>

                <label>
                    Gallery Images
                </label>

                <br>

                @php
                    $galleryImages = $product->images()
                                             ->where('type','gallery')
                                             ->get();
                @endphp

                @forelse($galleryImages as $img)

                    <img src="{{ asset('storage/' . $img->image_path) }}"
                         width="90"
                         class="mr-2 mb-2">

                @empty

                    <p class="text-danger">
                        No Gallery Images Found
                    </p>

                @endforelse

                <div class="mt-3">

                    <x-file-input
                        label="Upload Gallery Images"
                        name="gallery_images[]"
                        :multiple="true"
                    />

                </div>

            </div>

        </div>

        {{-- SEO DETAILS --}}

        <div class="card mt-3">

            <div class="card-header">

                <h3 class="card-title">
                    SEO Details
                </h3>

            </div>

            <div class="card-body">

                <x-input
                    label="Page Title"
                    name="page_title"
                    :value="$product->page_title"
                />

                <x-input
                    label="Alt Text"
                    name="alt_text"
                    :value="$product->alt_text"
                />

                <x-input
                    label="Meta Keywords"
                    name="meta_keywords"
                    :value="$product->meta_keywords"
                />

            </div>

        </div>

    </div>

</div>

{{-- BUTTONS --}}

<div class="row mt-3">

    <div class="col-12 text-right">

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
    const btn = document.getElementById("saveBtn");

    form.addEventListener("submit", function () {
        btn.disabled = true;
        btn.innerText = "Updating...";
    });

});
</script>
@endpush