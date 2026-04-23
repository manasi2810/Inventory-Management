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
 
    <div class="col-md-6">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">General Details</h3>
            </div>

            <div class="card-body">

                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="{{ $product->name }}" required>
                </div>

                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" class="form-control" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control"
                           value="{{ $product->sku }}">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control">{{ $product->description }}</textarea>
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Opening Stock</label>
                            <input type="number" name="opening_stock" class="form-control"
                                   value="{{ $product->opening_stock }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pack Size</label>
                            <input type="text" name="pack_size" class="form-control"
                                   value="{{ $product->pack_size }}">
                        </div>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>MOQ</label>
                            <input type="number" name="moq" class="form-control"
                                   value="{{ $product->moq }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>UOM *</label>
                            <input type="text" name="uom" class="form-control"
                                   value="{{ $product->uom }}" required>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control"
                           value="{{ $product->price }}">
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Feature Product</label>
                            <select name="feature_product" class="form-control">
                                <option value="1" {{ $product->feature_product == 1 ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ $product->feature_product == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status *</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
 
    <div class="col-md-6">
 
        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Product Images</h3>
            </div>

            <div class="card-body">

                <label>Current Main Image</label><br>

                @php
                    $image = $product->images()->where('type','main')->value('image_path');
                @endphp

                @if($image)
                    <img src="{{ asset('storage/' . $image) }}" width="120">
                @else
                    <p class="text-danger">No Main Image Found</p>
                @endif

                <div class="form-group mt-3">
                    <label>Change Main Image</label>
                    <input type="file" name="main_image" class="form-control">
                </div>

                <label>Gallery Images</label><br>

                @php
                    $galleryImages = $product->images()->where('type','gallery')->get();
                @endphp

                @forelse($galleryImages as $img)
                    <img src="{{ asset('storage/' . $img->image_path) }}"
                         width="90"
                         class="mr-2 mb-2">
                @empty
                    <p class="text-danger">No Gallery Images Found</p>
                @endforelse

                <div class="form-group mt-3">
                    <label>Upload Gallery Images</label>
                    <input type="file" name="gallery_images[]" multiple class="form-control">
                </div>

            </div>
        </div>
 
        <div class="card mt-3">

            <div class="card-header">
                <h3 class="card-title">SEO Details</h3>
            </div>

            <div class="card-body">

                <div class="form-group">
                    <label>Page Title</label>
                    <input type="text" name="page_title" class="form-control"
                           value="{{ $product->page_title }}">
                </div>

                <div class="form-group">
                    <label>Alt Text</label>
                    <input type="text" name="alt_text" class="form-control"
                           value="{{ $product->alt_text }}">
                </div>

                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control"
                           value="{{ $product->meta_keywords }}">
                </div>

            </div>
        </div>

    </div>

</div>
 
<div class="row mt-3">
    <div class="col-12 text-right">

        <button type="submit" id="saveBtn" class="btn btn-success">
            Update Product
        </button>

        <a href="{{ route('Product') }}" class="btn btn-secondary">
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