@extends('adminlte::page')

@section('title', 'Delivery Challan Details')

@section('content_header')
    <h1>Delivery Challan Details</h1>
@endsection

@section('content')

<div class="card shadow">
    <div class="card-body">

        {{-- ================= MASTER INFO ================= --}}
        <div class="row mb-4">

            <div class="col-md-4">
                <div class="border p-3 rounded bg-light">
                    <h5><strong>Challan No:</strong> {{ $challan->challan_no }}</h5>
                    <p><strong>Date:</strong> {{ $challan->challan_date }}</p>
                    <p>
                        <strong>Status:</strong>
                        <span class="badge badge-primary">
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

        {{-- ================= ITEM TRACKING ================= --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">

                <thead class="bg-dark text-white">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Ordered</th>
                        <th>Dispatched</th>
                        <th>Pending</th>
                        <th>Rate</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($challan->items as $key => $item)

                  @php
                    $dispatched = $item->dispatched_qty;
                    $pending = $item->pending_qty;
                @endphp

                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="text-left">{{ $item->product->name ?? '-' }}</td>

                        <td>{{ $item->qty }}</td>

                        <td style="color:green;">
                            {{ $dispatched }}
                        </td>

                        <td style="color:red;">
                            {{ $pending }}
                        </td>

                        <td>₹ {{ number_format($item->rate, 2) }}</td>

                        <td>₹ {{ number_format($item->total, 2) }}</td>
                    </tr>

                @endforeach

                </tbody>

            </table>
        </div>

       {{-- ================= DISPATCH HISTORY ================= --}}
<div class="card mt-4">

    <div class="card-header bg-dark">
        <h3 class="card-title">
            <i class="fas fa-truck"></i> Dispatch History
        </h3>
    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-hover table-bordered">

            <thead class="thead-light">

                <tr>

                    <th width="12%">Dispatch No</th>

                    <th width="12%">Date</th>

                    <th width="8%" class="text-center">Items</th>

                    <th width="20%">Packing Slip</th>

                    <th width="20%">Invoice</th>

                    <th width="10%">Status</th>

                    <!--<th width="18%">Actions</th>-->

                </tr>

            </thead>

            <tbody>

                @forelse($dispatches as $dispatch)

                <tr>

                    <td>
                        <strong>{{ $dispatch->dispatch_no }}</strong>
                    </td>

                    <td>
                        {{ date('d-M-Y', strtotime($dispatch->dispatch_date)) }}
                    </td>

                    <td class="text-center">
                        {{ $dispatch->items->count() }}
                    </td>

                    <td>

                        @if($dispatch->packing_slip_pdf)

                            <a href="{{ asset('storage/'.$dispatch->packing_slip_pdf) }}"
                               target="_blank"
                               class="btn btn-xs btn-primary">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a href="{{ asset('storage/'.$dispatch->packing_slip_pdf) }}"
                               download
                               class="btn btn-xs btn-success">

                                <i class="fa fa-download"></i>

                            </a>

                        @else

                            <span class="badge badge-danger">
                                Not Generated
                            </span>

                        @endif

                    </td>

                    <td>

                        @if($dispatch->invoice)

                            <a href="{{ route('invoices.show',$dispatch->invoice->id) }}"
                               class="btn btn-xs btn-info">

                                <i class="fa fa-eye"></i>

                            </a>

                                <a href="{{ route('invoices.pdf', $dispatch->invoice->id) }}"
                               target="_blank"
                               class="btn btn-xs btn-success">
                        
                                <i class="fa fa-download"></i>
                        
                            </a>


                        @else

                            <a href="{{ route('invoices.create',$dispatch->id) }}"
                               class="btn btn-xs btn-warning">

                                <i class="fa fa-file-invoice"></i>

                                Generate

                            </a>

                        @endif

                    </td>

                    <td>

                        <span class="badge badge-success">
                            {{ ucfirst($dispatch->status) }}
                        </span>

                    </td>

                    <!--<td>-->

                    <!--    <a href="{{ route('dispatch.show',$dispatch->id) }}"-->
                    <!--       class="btn btn-xs btn-info">-->

                    <!--        <i class="fa fa-eye"></i>-->

                    <!--        Details-->

                    <!--    </a>-->

                    <!--</td>-->

                </tr>

                @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No Dispatch Found

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

        {{-- ================= TOTAL SUMMARY ================= --}}
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

        {{-- ================= ACTIONS ================= --}}
        <div class="mt-3 d-flex justify-content-between">

            <a href="{{ route('Delivery_challan') }}" class="btn btn-secondary">
                Back
            </a>

            <div>

                <a href="{{ route('Delivery_challan.print', $challan->id) }}"
                   class="btn btn-primary">
                    Print
                </a>
               
                @if($challan->status == 'pending')
                    <a href="{{ route('Delivery_challan.edit', $challan->id) }}"
                       class="btn btn-warning">
                        Edit
                    </a>
                @endif

            </div>

        </div>

    </div>
</div>

@endsection