@extends('adminlte::page')

@section('title', 'Add Product')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Add Product</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">Product</li>
        </ol>
    </div>
</div>
@stop

@section('content')

<form id="productForm"
      action="{{ route('Product.store') }}"
      method="POST"
      enctype="multipart/form-data">
@csrf

<div class="card">

    <div class="card-header bg-primary">
        <h3 class="card-title">Product Information</h3>
    </div>

    <div class="card-body">
 
        <h5 class="mb-3 text-primary"><b>General Details</b></h5>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

        </div>

        <div class="row"> 
            <div class="col-md-4">
                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" name="sku" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>UOM *</label>
                    <input type="text" name="uom" class="form-control" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>MOQ</label>
                    <input type="number" name="moq" class="form-control">
                </div>
            </div>

        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <hr>

       
        <h5 class="mb-3 text-success"><b>Stock & Pricing</b></h5>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>Opening Stock</label>
                    <input type="number" name="opening_stock" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Pack Size</label>
                    <input type="text" name="pack_size" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" name="price" class="form-control">
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Feature Product</label>
                    <select name="feature_product" class="form-control">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

        </div>

        <hr>

         
        <h5 class="mb-3 text-warning"><b>Product Images</b></h5>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label>Main Image</label>
                    <input type="file" name="main_image" class="form-control">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Gallery Images</label>
                    <input type="file" name="gallery_images[]" multiple class="form-control">
                </div>
            </div>

        </div>

        <hr>

        
        <h5 class="mb-3 text-info"><b>SEO Details</b></h5>

        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>Page Title</label>
                    <input type="text" name="page_title" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Alt Text</label>
                    <input type="text" name="alt_text" class="form-control">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control">
                </div>
            </div>

        </div>

    </div>

     
    <div class="card-footer text-right">

        <button type="submit" id="saveBtn" class="btn btn-success">
            <i class="fas fa-save"></i> Save Product
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
        btn.innerText = "Saving...";
    });

});
</script>
@endpush