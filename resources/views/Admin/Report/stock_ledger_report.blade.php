@extends('adminlte::page')

@section('title', 'Stock Ledger Report')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>📦 Stock Ledger Report</h1>
    </div>

    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Stock Ledger</li>
        </ol>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- FILTER --}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter Ledger</h3>
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
                            🔍 Filter
                        </button>

                        <a href="{{ route('reports.ledger') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- EXPORT --}}
    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('reports.ledger.export', request()->all()) }}"
           class="btn btn-success">
            📥 Export Excel
        </a>
    </div>

    {{-- SUMMARY --}}
    <div class="row mb-3">

        <div class="col-md-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary->total_in ?? 0 }}</h3>
                    <p>Total Stock In</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-down"></i></div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $summary->total_out ?? 0 }}</h3>
                    <p>Total Stock Out</p>
                </div>
                <div class="icon"><i class="fas fa-arrow-up"></i></div>
            </div>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Stock Ledger Entries</h3>
        </div>

        <div class="card-body">

            <table id="ledgerTable" class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Movement</th>
                        <th>Qty</th>
                        <th>Narration</th>
                        <th>Reference ID</th>
                        <th>Balance After</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($ledger as $item)

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                        </td>

                        <td>
                            <strong>{{ $item->product->name ?? '-' }}</strong>
                            <br>
                            <small class="text-muted">{{ $item->product->sku ?? '-' }}</small>
                        </td>

                        <td>
                            @php
                                $type = strtolower($item->movement_type ?? '');
                                $badge = $type == 'in' ? 'success' : ($type == 'out' ? 'danger' : 'secondary');
                            @endphp

                            <span class="badge badge-{{ $badge }}">
                                {{ strtoupper($item->movement_type ?? '-') }}
                            </span>
                        </td>

                        <td>{{ $item->qty ?? 0 }}</td>

                        {{-- 🔥 IMPORTANT FIX: NARRATION --}}
                        <td>
                            @php
                                $ref = $item->reference_type ?? '';
                                $id = $item->reference_id ?? '-';

                                if($ref == 'stock_ins' || $ref == 'po') {
                                    $text = "Purchase Entry";
                                }
                                elseif($ref == 'dc_return' || $ref == 'dc_return_good') {
                                    $text = "DC Return Received";
                                }
                                elseif($ref == 'sale' || $ref == 'order') {
                                    $text = "Sale Order";
                                }
                                else {
                                    $text = ucfirst($ref);
                                }
                            @endphp

                            {{ $text }} #{{ $id }}
                        </td>

                        <td>{{ $item->reference_id ?? '-' }}</td>

                        <td>
                            <strong>{{ $item->balance_after ?? 0 }}</strong>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center">No data found</td>
                    </tr>
                @endforelse

                </tbody>

            </table>

        </div>
    </div>

</div>

@stop

@push('js')
<script>
$(function () {

    $('#ledgerTable').DataTable({
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