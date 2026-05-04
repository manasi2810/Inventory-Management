@extends('adminlte::page')

@section('title', 'Customer Report')

@section('content_header')
<h1>Customer Report</h1>
@stop

@section('content')

<div class="container-fluid">

    {{-- FILTER --}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter</h3>
        </div>

        <form method="GET">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-6">
                        <input type="text" name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search Customer Name / Mobile">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <button class="btn btn-primary mr-2">Filter</button>
                        <a href="{{ route('reports.customer') }}" class="btn btn-secondary">Reset</a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- SUMMARY --}}
    <div class="row mb-3">

        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $summary->total_customers ?? 0 }}</h3>
                    <p>Total Customers</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary->total_orders ?? 0 }}</h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($summary->total_amount ?? 0, 2) }}</h3>
                    <p>Total Amount</p>
                </div>
            </div>
        </div>

    </div>

    {{-- EXPORT --}}
    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('reports.customer.export', request()->all()) }}"
           class="btn btn-success">
            Export Excel
        </a>
    </div>

    {{-- TABLE --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Customer List</h3>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped" id="customerTable">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Total Orders</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($customers as $c)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->mobile }}</td>
                        <td>{{ $c->total_orders }}</td>
                        <td>{{ number_format($c->total_amount, 2) }}</td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="text-center">No data found</td>
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
    $('#customerTable').DataTable({
        responsive: true,
        autoWidth: false
    });
});
</script>
@endpush