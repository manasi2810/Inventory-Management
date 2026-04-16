@extends('adminlte::page')

@section('title', 'Stock In')

@section('content_header')
    <h1>Stock In History</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Type</th>
                    <th>Reference ID</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @foreach($stockIns as $key => $stock)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $stock->product->name ?? '-' }}</td>
                        <td>{{ $stock->qty }}</td>
                        <td>
                            <span class="badge badge-success">
                                {{ $stock->type }}
                            </span>
                        </td>
                        <td>{{ $stock->reference_id }}</td>
                        <td>{{ $stock->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>

        </table>

    </div>
</div>

@stop