@extends('adminlte::page')

@section('title', 'Receive Purchase')

@section('content_header')
    <h1>Receive Purchase Order</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">

        <form action="{{ route('Purchase.receive.store', $purchase->id) }}" method="POST">
            @csrf

            {{-- PURCHASE INFO --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    Purchase Details
                </div>

                <div class="card-body">
                    <div class="row">
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
                </div>
            </div>

            {{-- ITEMS TABLE --}}
            <div class="card">
                <div class="card-header bg-success text-white">
                    Receive Items
                </div>

                <div class="card-body">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Ordered Qty</th>
                                <th>Already Received</th>
                                <th>Receive Now</th>
                                <th>Price</th>
                                <th>Short</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($purchase->items as $index => $item)

                            <tr>

                                {{-- PRODUCT --}}
                                <td>
                                    {{ $item->product->name }}
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                </td>

                                {{-- ORDERED QTY --}}
                                <td>
                                    <input type="number"
                                           class="form-control ordered"
                                           value="{{ $item->qty }}"
                                           readonly>

                                    <input type="hidden"
                                           name="items[{{ $index }}][ordered_qty]"
                                           value="{{ $item->qty }}">
                                </td>

                                {{-- ALREADY RECEIVED --}}
                                <td>
                                    <input type="number"
                                           class="form-control already-received"
                                           value="{{ $item->already_received ?? 0 }}"
                                           readonly>
                                </td>

                                {{-- RECEIVE NOW --}}
                                <td>
                                    <input type="number"
                                           name="items[{{ $index }}][received_qty]"
                                           class="form-control receive-input"
                                           min="0"
                                           max="{{ $item->remaining_qty }}"
                                           value="0"
                                           required>
                                </td>

                                {{-- PRICE --}}
                                <td>
                                    <input type="number"
                                           name="items[{{ $index }}][price]"
                                           class="form-control"
                                           value="{{ $item->price }}"
                                           readonly>
                                </td>

                                {{-- SHORT --}}
                                <td>
                                    <input type="number"
                                           class="form-control short"
                                           value="{{ $item->remaining_qty }}"
                                           readonly>
                                </td>

                            </tr>

                            @endforeach

                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary mt-3">
                        Save Receive
                    </button>

                </div>
            </div>

        </form>

    </div>
</div>

@stop

@push('js')
<script>
$(document).ready(function () {

    $('.receive-input').on('input', function () {

        let row = $(this).closest('tr');

        let max = parseInt($(this).attr('max')) || 0;
        let val = parseInt($(this).val()) || 0;

        // prevent over receive
        if (val > max) {
            alert('Cannot receive more than remaining quantity');
            $(this).val(max);
            val = max;
        }

        // show remaining after this receive
        row.find('.short').val(max - val);

    });

});
</script>
@endpush