@extends('adminlte::page')

@section('title', 'DC Return Report')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>DC Return Report</h1>
    </div>  
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">DC Return</li>
        </ol>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid"> 
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter Report</h3>
        </div>  
        <form method="GET">
            <div class="card-body">
                <div class="row"> 
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from_date"
                            value="{{ request('from_date') }}"
                            class="form-control">
                    </div> 
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to_date"
                            value="{{ request('to_date') }}"
                            class="form-control">
                    </div> 
                    <div class="col-md-3">
                        <label>Product</label>
                        <select name="product_id" class="form-control">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div> 
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Filter
                        </button> 
                        <a href="{{ route('reports.dcreturn') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div> 
    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('reports.dcreturn.export', request()->all()) }}"
    class="btn btn-success">
        Export Excel
    </a>
    </div> 
    <div class="row mb-3"> 
        <div class="col-md-2">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $returns->total() }}</h3>
                    <p>Total Records</p>
                </div>
            </div>
        </div> 
        <div class="col-md-2">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $summary->total ?? 0 }}</h3>
                    <p>Total Returned Qty</p>
                </div>
            </div>
        </div>  
        <div class="col-md-2">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary->good ?? 0 }}</h3>
                    <p>Good</p>
                </div>
            </div>
        </div> 
        <div class="col-md-2">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $summary->damaged ?? 0 }}</h3>
                    <p>Damaged</p>
                </div>
            </div>
        </div> 
        <div class="col-md-2">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $summary->scrap ?? 0 }}</h3>
                    <p>Scrap</p>
                </div>
            </div>
        </div> 
    </div> 
    <div class="card"> 
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <h3 class="card-title mb-0">Return List</h3>
            </div>
        </div> 
        <div class="card-body"> 
            <table id="dcReturnTable" class="table table-bordered table-striped"> 
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>DC No</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Condition</th>
                    </tr>
                </thead> 
                <tbody> 
                @foreach($returns as $item) 
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->return_date }}</td>
                        <td>{{ $item->challan_no }}</td>
                        <td>{{ $item->customer_name }}</td>
                        <td>{{ $item->product_name }}</td>  
                        <td>{{ $item->return_qty }}</td> 
                        <td>
                            @php
                                $condition = strtolower($item->condition ?? '');
                                $badge = match($condition) {
                                    'good' => 'success',
                                    'damaged' => 'warning',
                                    'scrap' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp

                            <span class="badge badge-{{ $badge }}">
                                {{ ucfirst($condition ?: 'N/A') }}
                            </span>
                        </td> 
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
    $('#dcReturnTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: ["copy", "csv", "excel", "pdf", "print"]
    });
});
</script>
@endpush