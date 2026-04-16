@extends('adminlte::page')

@section('title', 'Purchase Details')

@section('content_header')
    <h1>Purchase Details</h1>
@stop

@section('content')

@php
    $totalOrdered = $purchase->items->sum('qty');

    $totalReceived = \App\Models\PurchaseReceiveItem::whereHas('receive', function ($q) use ($purchase) {
        $q->where('purchase_id', $purchase->id);
    })->sum('received_qty');

    $remaining = $totalOrdered - $totalReceived;
@endphp

<div class="row">
    <div class="col-12">

        {{-- PURCHASE INFO --}}
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                Purchase Information
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-3">
                        <strong>PO No:</strong><br>
                        {{ $purchase->invoice_no }}
                    </div>

                    <div class="col-md-3">
                        <strong>Vendor:</strong><br>
                        {{ $purchase->vendor->name ?? '-' }}
                    </div>

                    <div class="col-md-3">
                        <strong>Date:</strong><br>
                        {{ $purchase->purchase_date }}
                    </div>

                    <div class="col-md-3">
                        <strong>Status:</strong><br>

                        @if($purchase->status == 'received')
                            <span class="badge badge-success">Completed</span>
                        @elseif($purchase->status == 'partial')
                            <span class="badge badge-warning">Partial</span>
                        @else
                            <span class="badge badge-danger">Pending</span>
                        @endif

                    </div>

                </div>
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="card mb-3">
            <div class="card-body">

                <div class="row text-center">

                    <div class="col-md-4">
                        <h5>Total Ordered</h5>
                        <h3>{{ $totalOrdered }}</h3>
                    </div>

                    <div class="col-md-4">
                        <h5>Total Received</h5>
                        <h3 class="text-success">{{ $totalReceived }}</h3>
                    </div>

                    <div class="col-md-4">
                        <h5>Remaining</h5>
                        <h3 class="text-danger">{{ $remaining }}</h3>
                    </div>

                </div>

            </div>
        </div>

        {{-- ITEMS TABLE --}}
        <div class="card">
            <div class="card-header bg-success text-white">
                Product Details
            </div>

            <div class="card-body">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Ordered Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($purchase->items as $item)

                        @php
                            $receivedQty = \App\Models\PurchaseReceiveItem::whereHas('receive', function ($q) use ($purchase) {
                                $q->where('purchase_id', $purchase->id);
                            })
                            ->where('product_id', $item->product_id)
                            ->sum('received_qty');

                            $remainingQty = $item->qty - $receivedQty;
                        @endphp

                        <tr>
                            <td>{{ $item->product->name }}</td>

                            <td>
                                {{ $item->qty }}
                                <br>
                                <small class="text-success">
                                    Received: {{ $receivedQty }}
                                </small>
                                <br>
                                <small class="text-danger">
                                    Remaining: {{ $remainingQty }}
                                </small>
                            </td>

                            <td>₹ {{ number_format($item->price, 2) }}</td>

                            <td>₹ {{ number_format($item->qty * $item->price, 2) }}</td>
                        </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>

    </div>
</div>

@stop