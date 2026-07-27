@extends('adminlte::page')

@section('title','Dispatch Details')

@section('content')

<div class="card">

    <div class="card-header">

        <h3>Dispatch Details</h3>

    </div>

    <div class="card-body">

        <h4>{{ $dispatch->dispatch_no }}</h4>

        <p>Customer :
            {{ $dispatch->customer->name }}
        </p>

        <p>Delivery Challan :
            {{ $dispatch->challan->challan_no }}
        </p>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>Product</th>

                    <th>Qty</th>

                    <th>Rate</th>

                    <th>Amount</th>

                </tr>

            </thead>

            <tbody>

            @foreach($dispatch->items as $item)

                <tr>

                    <td>{{ $item->product->name }}</td>

                    <td>{{ $item->quantity }}</td>

                    <td>{{ $item->rate }}</td>

                    <td>{{ $item->amount }}</td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection