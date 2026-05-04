@extends('adminlte::page')

@section('title', 'Stock Report')

@section('content_header')
<div class="d-flex justify-content-between">
    <h1>📦 Stock Master Report</h1>

    <a href="{{ route('reports.stock.export') }}" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
</div>
@stop

@section('content')

 

{{-- ================= DASHBOARD CARDS ================= --}}
<div class="row mb-3">

    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stock->count() }}</h3>
                <p>Total Products</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stock->sum('purchase_qty') }}</h3>
                <p>Total Stock In</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stock->sum('sale_qty') }}</h3>
                <p>Total Stock Out</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stock->where('closing_stock','<=',10)->count() }}</h3>
                <p>Low Stock</p>
            </div>
        </div>
    </div>

</div>

{{-- ================= DATE FILTER ================= --}}
<form method="GET" class="card mb-3">
    <div class="card-body">
        <div class="row">

            <div class="col-md-5">
                <label>From Date</label>
                <input type="date" name="from_date"
                       class="form-control"
                       value="{{ request('from_date') }}">
            </div>

            <div class="col-md-5">
                <label>To Date</label>
                <input type="date" name="to_date"
                       class="form-control"
                       value="{{ request('to_date') }}">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary btn-block">
                    Filter
                </button>
            </div>

        </div>
    </div>
</form>

{{-- ================= SEARCH + FILTER ================= --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="row">

            <div class="col-md-6">
                <input type="text" id="searchProduct"
                       class="form-control"
                       placeholder="Search Product / SKU">
            </div>

            <div class="col-md-3">
                <select id="stockFilter" class="form-control">
                    <option value="">All Stock</option>
                    <option value="low">Low Stock (<=10)</option>
                    <option value="out">Out of Stock</option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-secondary btn-block" id="resetFilters">
                    Reset
                </button>
            </div>

        </div>
    </div>
</div>

{{-- ================= TABLE ================= --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Stock Details</h3>
    </div>

    <div class="card-body">
        <table id="stockTable" class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Opening</th>
                    <th>Stock In (+)</th>
                    <th>Stock Out (-)</th>
                    <th>Closing Stock</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            @foreach($stock as $item)
                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td><strong>{{ $item->name }}</strong></td>

                    <td>{{ $item->sku ?? '-' }}</td>

                    <td>{{ $item->opening_stock ?? 0 }}</td>

                    <td>
                        <span class="badge badge-success">
                            {{ $item->purchase_qty ?? 0 }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-danger">
                            {{ $item->sale_qty ?? 0 }}
                        </span>
                    </td>

                    <td>
                        <strong class="{{ $item->closing_stock <= 0 ? 'text-danger' : '' }}">
                            {{ $item->closing_stock }}
                        </strong>
                    </td>

                    <td>
                        @if($item->closing_stock <= 0)
                            <span class="badge badge-danger">Out of Stock</span>
                        @elseif($item->closing_stock <= 10)
                            <span class="badge badge-warning">Low Stock</span>
                        @else
                            <span class="badge badge-success">In Stock</span>
                        @endif
                    </td>

                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
</div>

@stop


{{-- ================= JS ================= --}}
@push('js')
<script>
$(function () {

    let table = $('#stockTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: ["copy", "csv", "excel", "pdf", "print"]
    });

    // SEARCH
    $('#searchProduct').on('keyup', function () {
        table.search(this.value).draw();
    });

    // STOCK FILTER
    $('#stockFilter').on('change', function () {
        let val = $(this).val();

        if (val == "low") {
            table.column(6).search('^(?:[1-9]|[1-9][0-9]+)$', true, false).draw();
        } 
        else if (val == "out") {
            table.column(6).search('^0$', true, false).draw();
        } 
        else {
            table.column(6).search('').draw();
        }
    });

    // RESET
    $('#resetFilters').on('click', function () {
        $('#searchProduct').val('');
        $('#stockFilter').val('');
        table.search('').columns().search('').draw();
    });

});
</script>
@endpush