@extends('adminlte::page')

@section('title', 'Generate Invoice')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h3>Generate Invoice</h3>

        <a href="{{ route('invoice.store', $dispatch->id) }}"
           class="btn btn-success">

            <i class="fa fa-file-invoice"></i>

            Generate Invoice

        </a>

    </div>

    <div class="card-body">

        {{-- ================= HEADER INFO ================= --}}

        <div class="row">

            <div class="col-md-4">

                <h5><strong>Dispatch No:</strong></h5>
                <p>{{ $dispatch->dispatch_no }}</p>

            </div>

            <div class="col-md-4">

                <h5><strong>Customer:</strong></h5>
                <p>{{ $dispatch->customer->name }}</p>

            </div>

            <div class="col-md-4">

                <h5><strong>Date:</strong></h5>
                <p>{{ date('d-m-Y', strtotime($dispatch->dispatch_date)) }}</p>

            </div>

        </div>

        <hr>

        {{-- ================= ITEM TABLE ================= --}}

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead class="bg-dark text-white">

                    <tr>

                        <th>#</th>

                        <th>Product</th>

                        <th>Qty</th>

                        <th>Rate</th>

                        <th>GST %</th>

                        <th>Taxable Amount</th>

                        <th>GST Amount</th>

                        <th>Total</th>

                    </tr>

                </thead>

                <tbody>

                @php
                    $subTotal = 0;
                    $gstTotal = 0;
                    $grandTotal = 0;
                @endphp

                @foreach($dispatch->items as $key => $item)

                    @php
                        $subTotal += ($item->quantity * $item->rate);
                        $gstTotal += $item->gst_amount;
                        $grandTotal += $item->amount;
                    @endphp

                    <tr>

                        <td>{{ $key + 1 }}</td>

                        <td>{{ $item->product->name }}</td>

                        <td>{{ $item->quantity }}</td>

                        <td>₹ {{ number_format($item->rate,2) }}</td>

                        <td>{{ $item->gst_percent }}%</td>

                        <td>₹ {{ number_format($item->quantity * $item->rate,2) }}</td>

                        <td>₹ {{ number_format($item->gst_amount,2) }}</td>

                        <td>₹ {{ number_format($item->amount,2) }}</td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

        {{-- ================= TOTAL SECTION ================= --}}

        <div class="row">

            <div class="col-md-6"></div>

            <div class="col-md-6">

                <table class="table table-bordered">

                    <tr>

                        <th>Sub Total</th>

                        <td>₹ {{ number_format($subTotal,2) }}</td>

                    </tr>

                    <tr>

                        <th>Total GST</th>

                        <td>₹ {{ number_format($gstTotal,2) }}</td>

                    </tr>

                    <tr class="bg-light">

                        <th>Grand Total</th>

                        <td>
                            <strong>₹ {{ number_format($grandTotal,2) }}</strong>
                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection