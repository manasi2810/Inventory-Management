@extends('adminlte::page')

@section('title', 'Dispatch Delivery Challan')

@section('content_header')
    <h1>Dispatch Delivery Challan</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">

        {{-- ALERTS --}}
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('Delivery_challan.dispatch_store', $challan->id) }}">
            @csrf

            {{-- HEADER --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    Dispatch Details
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-4">
                            <strong>Challan No:</strong> {{ $challan->challan_no }}
                        </div>

                        <div class="col-md-4">
                            <strong>Customer:</strong> {{ $challan->customer->name }}
                        </div>

                        <div class="col-md-4">
                            <strong>Date:</strong> {{ $challan->challan_date }}
                        </div>

                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
            <div class="card">
                <div class="card-header bg-success text-white">
                    Dispatch Items
                </div>

                <div class="card-body p-0">

                    <table class="table table-bordered mb-0">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Ordered</th>
                                <th>Dispatched</th>
                                <th>Pending</th>
                                <th>Dispatch Now</th>
                                <th>Remaining</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach($challan->items as $index => $item)

                            @php
                                $pending = $item->qty - $item->dispatched_qty;
                            @endphp

                            <tr>

                                {{-- PRODUCT --}}
                                <td>
                                    {{ $item->product->name }}
                                    <input type="hidden"
                                           name="items[{{ $index }}][item_id]"
                                           value="{{ $item->id }}">
                                </td>

                                {{-- ORDERED --}}
                                <td>
                                    <input type="number"
                                           class="form-control"
                                           value="{{ $item->qty }}"
                                           readonly>
                                </td>

                                {{-- DISPATCHED --}}
                                <td>
                                    <input type="number"
                                           class="form-control"
                                           value="{{ $item->dispatched_qty }}"
                                           readonly>
                                </td>

                                {{-- PENDING --}}
                                <td>
                                    <input type="number"
                                           class="form-control pending"
                                           value="{{ $pending }}"
                                           readonly>
                                </td>

                                {{-- DISPATCH INPUT --}}
                                <td>
                                    <input type="number"
                                           name="items[{{ $index }}][qty]"
                                           class="form-control dispatch-input"
                                           min="0"
                                           max="{{ $pending }}"
                                           value="0">
                                </td>

                                {{-- REMAINING AFTER INPUT --}}
                                <td>
                                    <input type="number"
                                           class="form-control remaining"
                                           value="{{ $pending }}"
                                           readonly>
                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

                <div class="card-footer text-right">
                    <button class="btn btn-primary">
                        Confirm Dispatch
                    </button>
                </div>

            </div>

        </form>

    </div>
</div>

@endsection


@push('js')
<script>
$(document).on('input', '.dispatch-input', function () {

    let max = parseFloat($(this).attr('max')) || 0;
    let val = parseFloat($(this).val()) || 0;

    if (val > max) {
        alert('Cannot dispatch more than pending quantity');
        $(this).val(max);
    }

    if (val < 0) {
        $(this).val(0);
    }

});
</script>
@endpush