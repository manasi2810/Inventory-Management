@extends('adminlte::page')

@section('title', 'Purchases')

@section('content_header')
    <h1>Purchases</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            {{-- HEADER --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Purchase List</h3>
                    <a href="{{ route('Purchase.create') }}" class="btn btn-primary">
                        + Create Purchase
                    </a>
                </div>
            </div> 
            <div class="card-body"> 
                {{-- Success Message --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <table class="table table-bordered table-striped" id="purchaseTable"> 
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th width="250">Actions</th>
                        </tr>
                    </thead> 
                    <tbody> 
                        @foreach($purchases as $purchase) 
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase->invoice_no ?? '-' }}</td>
                            <td>{{ $purchase->vendor->name ?? '-' }}</td>
                            <td>{{ $purchase->purchase_date }}</td>
                            <td>₹ {{ number_format($purchase->total_amount, 2) }}</td>
                            {{-- STATUS --}}
                            <td>
                                @if($purchase->status == 'received')
                                    <span class="badge badge-success">Received</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td> 
                            {{-- ACTIONS --}}
                            <td> 
                               <a href="{{ route('Purchase.show', $purchase->id) }}"
                                class="btn btn-sm btn-info">
                                    View
                                </a>
                                {{-- RECEIVE BUTTON --}} 
                              @php
                                $totalOrdered = $purchase->items->sum('qty');

                                $totalReceived = \App\Models\PurchaseReceiveItem::whereHas('receive', function ($q) use ($purchase) {
                                    $q->where('purchase_id', $purchase->id);
                                })->sum('received_qty');
                            @endphp

                            @if($totalReceived < $totalOrdered)

                                <a href="{{ route('Purchase.receive', $purchase->id) }}"
                                class="btn btn-sm btn-success">

                                    @if($purchase->status == 'partial')
                                        Receive Remaining
                                    @else
                                        Receive
                                    @endif

                                </a>

                            @else

                                <span class="badge badge-success">Completed</span>

                            @endif
                            </td>
                        </tr> 
                        @endforeach 
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div>
</div> 
@stop


@push('js')
<script>
$(document).ready(function () {
    $('#purchaseTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: ["copy", "csv", "excel", "pdf", "print"]
    });
});
</script>

@endpush