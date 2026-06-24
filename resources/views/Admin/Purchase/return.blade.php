@extends('adminlte::page')

@section('title', 'Purchase Return')

@section('content_header')
    <h1>Purchase Return</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            Purchase Return Entry
        </h3>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-4">
                <strong>PO No :</strong>
                {{ $purchase->invoice_no }}
            </div>

            <div class="col-md-4">
                <strong>Vendor :</strong>
                {{ $purchase->vendor->name ?? '-' }}
            </div>

            <div class="col-md-4">
                <strong>Date :</strong>
                {{ $purchase->purchase_date }}
            </div>
        </div>

        <form action="{{ route('purchase.return.store',$purchase->id) }}"
              method="POST">

            @csrf

            <table class="table table-bordered table-striped"> 
                @extends('adminlte::page')

@section('title', 'Purchase Return')

@section('content_header')
    <h1>Purchase Return</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Purchase Return Entry</h3>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- HEADER INFO --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>PO No:</strong> {{ $purchase->invoice_no }}
            </div>
            <div class="col-md-4">
                <strong>Vendor:</strong> {{ $purchase->vendor->name ?? '-' }}
            </div>
            <div class="col-md-4">
                <strong>Date:</strong> {{ $purchase->purchase_date }}
            </div>
        </div>

        <form action="{{ route('purchase.return.store', $purchase->id) }}" method="POST">
            @csrf

            <table class="table table-bordered table-striped">

                <thead>
                <tr>
                    <th>Product</th>
                    <th width="120">Received</th>
                    <th width="120">Returned</th>
                    <th width="120">Available</th>
                    <th width="120">Return Qty</th>
                    <th width="120">Price</th>
                    <th width="150">Total</th>
                </tr>
                </thead>

                <tbody>

                @foreach($purchase->items as $index => $item)

                    @php
                        // ✅ REAL ERP VALUES (must come from controller ideally)
                        $receivedQty = (float) ($item->received_qty ?? 0);
                        $returnedQty = (float) ($item->returned_qty ?? 0);
                        $availableQty = (float) ($item->available_return ?? 0);
                    @endphp

                    <tr>

                        {{-- PRODUCT --}}
                        <td>
                            {{ $item->product->name }}

                            <input type="hidden"
                                   name="items[{{ $index }}][product_id]"
                                   value="{{ $item->product_id }}">
                        </td>

                        {{-- RECEIVED --}}
                        <td>
                            {{ $receivedQty }}
                        </td>

                        {{-- RETURNED --}}
                        <td>
                            {{ $returnedQty }}
                        </td>

                        {{-- AVAILABLE --}}
                        <td>
                            <span class="badge badge-info">
                                {{ $availableQty }}
                            </span>
                        </td>

                        {{-- RETURN QTY --}}
                        <td>
                            @if($availableQty > 0)
                                <input type="number"
                                    name="items[{{ $index }}][return_qty]"
                                    class="form-control return_qty"
                                    min="0"
                                    max="{{ $availableQty }}"
                                    value="0">
                            @else
                                <input type="number"
                                    class="form-control"
                                    value="0"
                                    readonly>
                            @endif
                        </td>

                        {{-- PRICE --}}
                        <td>
                            <input type="number"
                                   name="items[{{ $index }}][price]"
                                   class="form-control price"
                                   value="{{ $item->price }}"
                                   readonly>
                        </td>

                        {{-- TOTAL --}}
                        <td>
                            <input type="text"
                                   class="form-control total"
                                   value="0.00"
                                   readonly>
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            <button type="submit" class="btn btn-danger">
                Submit Return
            </button>

        </form>

    </div>

</div>

@stop

@push('js')

<script>
$(document).ready(function () {

    $('.return_qty').on('input', function () {

        let row = $(this).closest('tr');

        let qty = parseFloat($(this).val()) || 0;
        let max = parseFloat($(this).attr('max')) || 0;
        let price = parseFloat(row.find('.price').val()) || 0;

        // ❗ HARD LIMIT CHECK
        if (qty > max) {
            alert('Return Qty cannot exceed Available Qty');
            qty = max;
            $(this).val(max);
        }

        let total = qty * price;

        row.find('.total').val(total.toFixed(2));
    });

});
</script>

@endpush

            </table>

            <button type="submit" class="btn btn-danger">
                Submit Return
            </button>

        </form>

    </div>

</div>

@stop

@push('js')

<script>

$(document).ready(function () {

    $('.return_qty').on('input', function () {

        let row = $(this).closest('tr');

        let qty = parseFloat($(this).val()) || 0;
        let max = parseFloat($(this).attr('max')) || 0;
        let price = parseFloat(row.find('.price').val()) || 0;

        if (qty > max) {

            alert('Return Qty cannot exceed Available Qty');

            qty = max;

            $(this).val(max);
        }

        row.find('.total').val(
            (qty * price).toFixed(2)
        );

    });

});

</script>

@endpush