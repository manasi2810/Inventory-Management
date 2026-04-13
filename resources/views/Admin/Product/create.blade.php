@extends('admin.layout.app', ['activePage' => 'Product', 'titlePage' => __('Product')])

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
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
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <form id="productForm" action="{{ route('Product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
    <div style="color:red; background:#ffe6e6; padding:10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            <div class="row">
                <!-- General Details -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title">General Details</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Product Name*</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="category">Category*</label>
                                <select name="category_id" id="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sku">SKU</label>
                                <input type="text" class="form-control" id="sku" name="sku">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" rows="3" id="description" name="description"></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="opening_stock">Opening Stock</label>
                                    <input type="number" class="form-control" id="opening_stock" name="opening_stock">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="pack_size">Pack Size</label>
                                    <input type="text" class="form-control" id="pack_size" name="pack_size">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="moq">MOQ</label>
                                    <input type="number" class="form-control" id="moq" name="moq">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="uom">UOM*</label>
                                    <input type="text" class="form-control" id="uom" name="uom" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" class="form-control" id="price" name="price">
                            </div>
                            <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="feature_product">Feature Product</label>
                                <select class="form-control" id="feature_product" name="feature_product">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">Status*</label>
                                <select class="form-control" id="status" name="status" required>
                                   <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="col-md-6">
                    <div class="card card-secondary">
                        <div class="card-header"><h3 class="card-title">Product Images</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Main Image</label>
                                <input type="file" name="main_image" class="form-control">
                            </div> 
                            <div class="form-group">
                                <label>Gallery Images</label>
                                <input type="file" name="gallery_images[]" multiple class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- SEO Details -->
                    <div class="card card-info">
                        <div class="card-header"><h3 class="card-title">SEO Details</h3></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="page_title">Page Title</label>
                                <input type="text" class="form-control" id="page_title" name="page_title">
                            </div>
                            <div class="form-group">
                                <label for="alt_text">Alt Text</label>
                                <input type="text" class="form-control" id="alt_text" name="alt_text">
                            </div>
                            <div class="form-group">
                                <label for="meta_keywords">Meta Keywords</label>
                                <input type="text" class="form-control" id="meta_keywords" name="meta_keywords">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Submit Buttons -->
                {{-- <div class="row"> --}}
                    <div class="col-md-12 text-right">
                    <button type="submit" id="saveBtn" class="btn btn-success">
                        Save Product
                    </button>
                        <a href="{{ route('Product') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                {{-- </div> --}}
            </form>
        </section>
    </div>
    @endsection
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