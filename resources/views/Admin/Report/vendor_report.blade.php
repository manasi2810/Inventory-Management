@extends('adminlte::page')

@section('title', 'Vendor Report')

@section('content')

<div class="container-fluid">

    {{-- FILTER --}}
    <div class="card">
        <div class="card-header">
            <h3>Vendor Report</h3>
        </div>

        <div class="card-body">
            <form method="GET" class="row">

                <div class="col-md-4">
                    <input type="text" name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Search vendor name / mobile / company">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary">Filter</button>
                    <a href="{{ route('reports.vendor') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row mb-3">

        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $summary->total_vendors }}</h3>
                    <p>Total Vendors</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $summary->total_purchases }}</h3>
                    <p>Total Purchases</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($summary->total_amount,2) }}</h3>
                    <p>Total Purchase Amount</p>
                </div>
            </div>
        </div>

    </div>

    {{-- EXPORT --}}
    <div class="text-right mb-2">
        <a href="{{ route('reports.vendor.export', request()->all()) }}"
           class="btn btn-success">
            Export Excel
        </a>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">

            <table class="table table-bordered" id="vendorTable">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Mobile</th>
                        <th>City</th>
                        <th>Purchases</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($vendors as $v)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $v->name }}</td>
                        <td>{{ $v->company_name }}</td>
                        <td>{{ $v->mobile }}</td>
                        <td>{{ $v->city }}</td>
                        <td>{{ $v->total_purchases }}</td>
                        <td>{{ number_format($v->total_amount,2) }}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>

@stop