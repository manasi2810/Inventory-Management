@extends('adminlte::page')

@section('title', 'Purchases')

@section('content_header')
    <h1>Purchases</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card"> 
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Purchase List</h3> 
                    <div>
                        <button class="btn btn-dark btn-sm" id="printSelected">
                            Print Selected
                        </button> 
                        <a href="{{ route('Purchase.create') }}" class="btn btn-primary btn-sm">
                            + Create Purchase
                        </a>
                    </div>
                </div>
            </div> 
            <div class="card-body"> 
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif 
                <table class="table table-bordered table-striped" id="purchaseTable"> 
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th width="300">Actions</th>
                        </tr>
                    </thead> 
                    <tbody> 
                        @foreach($purchases as $purchase)
                        <tr> 
                            <td>
                                <input type="checkbox" class="po-check" value="{{ $purchase->id }}">
                            </td> 
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase->invoice_no ?? '-' }}</td>
                            <td>{{ $purchase->vendor->name ?? '-' }}</td>
                            <td>{{ $purchase->purchase_date }}</td>
                            <td>₹ {{ number_format($purchase->total_amount, 2) }}</td> 
                            <td>
                                @if($purchase->status == 'received')
                                    <span class="badge badge-success">Received</span> 
                                @elseif($purchase->status == 'partial')
                                    <span class="badge badge-info">Partial</span> 
                                @elseif($purchase->status == 'short_closed')
                                    <span class="badge badge-danger">Short Closed</span> 
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td> 
                            <td> 
                                <a href="{{ route('Purchase.show', $purchase->id) }}"
                                   class="btn btn-sm btn-info">
                                    View
                                </a> 
                                <a href="{{ route('Purchase.print', $purchase->id) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-secondary">
                                    Print
                                </a> 
                                @php
                                    $totalOrdered = $purchase->items->sum('qty');
                                    $totalReceived = \App\Models\PurchaseReceiveItem::whereHas('receive', function ($q) use ($purchase) {
                                        $q->where('purchase_id', $purchase->id);
                                    })->sum('received_qty');
                                @endphp 
                                @if($totalReceived < $totalOrdered && !in_array($purchase->status, ['received','short_closed']))

                                    <a href="{{ route('Purchase.receive', $purchase->id) }}"
                                       class="btn btn-sm btn-success">

                                        @if($purchase->status == 'partial')
                                            Receive Remaining
                                        @else
                                            Receive
                                        @endif

                                    </a>

                                @else
                                    <span class="btn btn-sm btn-success">Completed</span>
                                @endif 
                                @if(!in_array($purchase->status, ['received', 'short_closed']))

                                    <form action="{{ route('purchase.shortClose', $purchase->id) }}"
                                          method="POST"
                                          style="display:inline-block;">
                                        @csrf 
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure to short close this PO? Remaining qty will be cancelled.')">
                                            Short Close
                                        </button>
                                    </form> 
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

    $('#selectAll').on('click', function () {
        $('.po-check').prop('checked', this.checked);
    });

    // multiple print 
    $('#printSelected').on('click', function () {

        let ids = [];

        $('.po-check:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert('Please select at least one Purchase Order');
            return;
        }

        let url = "{{ route('Purchase.multiPrint') }}?ids=" + ids.join(',');

        window.open(url, '_blank');
    });

});
</script>
@endpush