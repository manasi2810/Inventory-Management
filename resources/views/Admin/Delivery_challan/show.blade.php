@extends('adminlte::page')

@section('title', 'Delivery Challan Details')

@section('content_header')
    <h1>Delivery Challan Details</h1>
@endsection

@section('content')

<div class="card shadow">
    <div class="card-body"> 
        
        <div class="row mb-4">  
            <div class="col-md-4">
                <div class="border p-3 rounded bg-light">
                    <h5><strong>Challan No:</strong> {{ $challan->challan_no }}</h5>
                    <p><strong>Date:</strong> {{ $challan->challan_date }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge badge-{{ $challan->status == 'pending' ? 'warning' : 'success' }}">
                            {{ ucfirst($challan->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border p-3 rounded bg-light">
                    <h5><strong>Customer</strong></h5>
                    <p>{{ $challan->customer->name ?? '-' }}</p>
                    <p>{{ $challan->delivery_to }}</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border p-3 rounded bg-light">
                    <h5><strong>Transport Details</strong></h5>
                    <p><strong>Mode:</strong> {{ $challan->transport_mode }}</p>
                    <p><strong>Vehicle:</strong> {{ $challan->vehicle_no }}</p>
                    <p><strong>LR No:</strong> {{ $challan->lr_no ?? '-' }}</p>
                </div>
            </div> 
        </div>

        
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($challan->items as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="text-left">{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>₹ {{ number_format($item->rate, 2) }}</td>
                        <td>₹ {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

         
        <div class="row mt-4">
            <div class="col-md-6"></div>

            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>Total Qty</th>
                        <td>{{ $challan->total_qty }}</td>
                    </tr>
                    <tr>
                        <th>Sub Total</th>
                        <td>₹ {{ number_format($challan->sub_total, 2) }}</td>
                    </tr>
                    <tr>
                        <th>GST</th>
                        <td>₹ {{ number_format($challan->gst_amount, 2) }}</td>
                    </tr>
                    <tr class="bg-light">
                        <th>Grand Total</th>
                        <td><strong>₹ {{ number_format($challan->total_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div> 
        
        <div class="mt-3 d-flex justify-content-between">

            <a href="{{ route('Delivery_challan') }}" class="btn btn-secondary">
                Back
            </a>

            <div>
                <a href="{{ route('Delivery_challan.print', $challan->id) }}" class="btn btn-primary">
                    Print
                </a>

                @if($challan->status == 'pending')
                    <a href="{{ route('Delivery_challan.edit', $challan->id) }}" class="btn btn-warning">
                        Edit
                    </a>
                @endif
            </div>

        </div>

    </div>
</div>

@endsection