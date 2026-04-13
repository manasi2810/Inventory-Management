@extends('admin.layout.table', ['activePage' => 'Product', 'titlePage' => __('Products')])

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Products</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header position-relative">
                    <h3 class="card-title">Product Details</h3>
                    <a href="{{ route('Product.create') }}" class="btn btn-primary position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%);">
                        Add Product
                    </a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Opening Stock</th>
                                <th>Status</th>
                                <th>Feature</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $key => $product)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->opening_stock }}</td>
                                <td>{{ ucfirst($product->status) }}</td>
                                <td>{{ $product->feature_product ? 'Yes' : 'No' }}</td>
                                <td>
                                    <a href="{{ route('Product.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('Product.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </section>
</div>
@endsection