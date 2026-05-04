@extends('adminlte::page')

@section('title', 'Product Report')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Product Stock Report</h1>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- SUMMARY --}}
    <div class="row mb-3">

        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $summary->total_products ?? 0 }}</h3>
                    <p>Total Products</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary->total_stock_in ?? 0 }}</h3>
                    <p>Total Stock In</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $summary->total_stock_out ?? 0 }}</h3>
                    <p>Total Stock Out</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $summary->total_available ?? 0 }}</h3>
                    <p>Available Stock</p>
                </div>
            </div>
        </div>

    </div>

    {{-- EXPORT BUTTON --}}
    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('reports.product.export', request()->all()) }}"
           class="btn btn-success">
            Export Excel
        </a>
    </div>

    {{-- TABLE --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Product List</h3>
        </div>

        <div class="card-body">

            <table id="productTable" class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Opening</th>
                        <th>Stock In</th>
                        <th>Stock Out</th>
                        <th>Available</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($products as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->sku ?? '-' }}</td>
                        <td>{{ $p->opening_stock }}</td>
                        <td>{{ $p->total_in }}</td>
                        <td>{{ $p->total_out }}</td>
                        <td>{{ $p->available_stock }}</td>
                    </tr>
                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@stop

@push('js')
<script>
$(function () {
    $('#productTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true
    });
});
</script>
@endpush