@extends('adminlte::page')

@section('title', 'Invoice Details')

@section('content_header')
<h1>Invoice Details</h1>
@endsection

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h4>
            Invoice : {{ $invoice->invoice_no }}
        </h4>

        <div>

           

            @if($invoice->invoice_pdf)

            <a href="{{ asset('storage/'.$invoice->invoice_pdf) }}"
               target="_blank"
               class="btn btn-success">

                <i class="fa fa-download"></i>

                Download PDF

            </a>

            @endif

        </div>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <h5>Customer</h5>

                <table class="table table-bordered">

                    <tr>
                        <th>Name</th>
                        <td>{{ $invoice->customer->name }}</td>
                    </tr>

                    <tr>
                        <th>Invoice No</th>
                        <td>{{ $invoice->invoice_no }}</td>
                    </tr>

                    <tr>
                        <th>Invoice Date</th>
                        <td>{{ $invoice->invoice_date }}</td>
                    </tr>

                    <tr>
                        <th>Dispatch</th>
                        <td>{{ $invoice->dispatch->dispatch_no }}</td>
                    </tr>

                    <tr>
                        <th>Delivery Challan</th>
                        <td>{{ $invoice->deliveryChallan->challan_no }}</td>
                    </tr>

                </table>

            </div>

        </div>

        <hr>

        <table class="table table-bordered">

            <thead class="bg-dark text-white">

            <tr>

                <th>#</th>

                <th>Product</th>

                <th>Qty</th>

                <th>Rate</th>

                <th>GST %</th>

                <th>GST Amount</th>

                <th>Total</th>

            </tr>

            </thead>

            <tbody>

            @foreach($invoice->items as $key=>$item)

                <tr>

                    <td>{{ $key+1 }}</td>

                    <td>{{ $item->product->name }}</td>

                    <td>{{ $item->quantity }}</td>

                    <td>{{ number_format($item->rate,2) }}</td>

                    <td>{{ $item->gst_percent }}</td>

                    <td>{{ number_format($item->gst_amount,2) }}</td>

                    <td>{{ number_format($item->amount,2) }}</td>

                </tr>

            @endforeach

            </tbody>

        </table>

        <div class="row">

            <div class="col-md-6"></div>

            <div class="col-md-6">

                <table class="table table-bordered">

                    <tr>

                        <th>Sub Total</th>

                        <td>
                            ₹ {{ number_format($invoice->sub_total,2) }}
                        </td>

                    </tr>

                    <tr>

                        <th>GST</th>

                        <td>
                            ₹ {{ number_format($invoice->gst_amount,2) }}
                        </td>

                    </tr>

                    <tr>

                        <th>Transport</th>

                        <td>
                            ₹ {{ number_format($invoice->transport_charge,2) }}
                        </td>

                    </tr>

                    <tr>

                        <th>Discount</th>

                        <td>
                            ₹ {{ number_format($invoice->discount,2) }}
                        </td>

                    </tr>

                    <tr class="bg-light">

                        <th>Grand Total</th>

                        <td>

                            <strong>

                                ₹ {{ number_format($invoice->total_amount,2) }}

                            </strong>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection