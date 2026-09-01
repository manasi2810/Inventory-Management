@extends('adminlte::page')

@section('title', 'Products')

@section('content_header')

<div class="row mb-2">

    <div class="col-sm-6">
        <h1>Products</h1>
    </div>

    <div class="col-sm-6">

        <ol class="breadcrumb float-sm-right">

            <li class="breadcrumb-item">
                <a href="#">Home</a>
            </li>

            <li class="breadcrumb-item active">
                Products
            </li>

        </ol>

    </div>

</div>

@stop


@section('content')

<div class="card">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <h3 class="card-title mb-0">
                Product Details
            </h3>

            @can('product.create')

            <a href="{{ route('Product.create') }}"
               class="btn btn-primary btn-sm">

                + Add Product

            </a>

            @endcan

        </div>

    </div>


    <div class="card-body">

        <table class="table table-bordered table-striped"
               id="example1">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Name</th>

                    <th>Category</th>

                    <th>SKU</th>

                    <th>UOM</th>

                    <th>Cost Price</th>

                    <th>Selling Price</th>

                    <th>Status</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

                @forelse($products as $product)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>


                    <td>
                        {{ $product->name }}
                    </td>


                    <td>
                        {{ $product->category->name ?? '-' }}
                    </td>


                    <td>
                        {{ $product->sku ?? '-' }}
                    </td>


                    <td>
                        {{ $product->uom ?? '-' }}
                    </td>


                    <td>
                        ₹ {{ number_format($product->cost_price ?? 0, 2) }}
                    </td>


                    <td>
                        ₹ {{ number_format($product->price ?? 0, 2) }}
                    </td>


                    <td>

                        <span class="badge
                            {{ $product->status == 'active'
                                ? 'badge-success'
                                : 'badge-danger' }}">

                            {{ ucfirst($product->status) }}

                        </span>

                    </td>


                  <td>

    @can('product.edit')

        <a href="{{ route('Product.edit', $product->id) }}"
           class="btn btn-sm btn-warning">
            Edit
        </a>

    @endcan


    @if($product->status === 'active')

        @can('product.delete')

            <form action="{{ route('Product.destroy', $product->id) }}"
                  method="POST"
                  style="display:inline-block;">

                @csrf
                @method('DELETE')

                <button type="submit"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Disable this product?')">

                    Delete

                </button>

            </form>

        @endcan


    @else

        {{-- Restore only for Admin and Manager --}}

        @hasanyrole('Admin|Manager')

            <form action="{{ route('Product.restore', $product->id) }}"
                  method="POST"
                  style="display:inline-block;">

                @csrf
                @method('PATCH')

                <button type="submit"
                        class="btn btn-sm btn-success"
                        onclick="return confirm('Restore this product?')">

                    Restore

                </button>

            </form>

        @endhasanyrole

    @endif

</td>

                </tr>

                @empty

                <tr>

                    <td colspan="9"
                        class="text-center text-muted">

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
            'copy',
            'csv',
            'excel',
            'pdf',
            'print'
        ]

    });

});

</script>

@endpush