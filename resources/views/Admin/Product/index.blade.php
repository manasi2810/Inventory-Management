@extends('adminlte::page')

@section('title', 'Products')

@section('content_header')
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
@stop

@section('content')

<div class="card"> 
   <div class="card-header">
    <div class="d-flex justify-content-between align-items-center"> 
        <h3 class="card-title mb-0">Product Details</h3> 
        <a href="{{ route('Product.create') }}" class="btn btn-primary btn-sm">
            + Add Product
        </a> 
    </div>
</div>
    <div class="card-body"> 
        <table class="table table-bordered table-striped" id="example1"> 
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead> 
            <tbody>  
                @forelse($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td>{{ $product->sku ?? '-' }}</td>
                    <td>{{ $product->price ?? 0 }}</td> 
                    <td>
                        <span class="badge {{ $product->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td> 
                    <td>
                        <a href="{{ route('Product.edit', $product->id) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a> 
                        <form action="{{ route('Product.destroy', $product->id) }}"
                              method="POST"
                              style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this product?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        No Products Found
                    </td>
                </tr>
                @endforelse 
            </tbody> 
        </table> 
    </div> 
</div> 
@stop 
@push('js')
<script>
$(function () {
    $('#example1').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>
@endpush