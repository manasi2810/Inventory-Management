@extends('adminlte::page')

@section('title', 'Edit Delivery Challan')

@section('content_header')
    <h1>Edit Delivery Challan</h1>
@stop

@section('content')

<form action="{{ route('Delivery_challan.update', $challan->id) }}" method="POST">
@csrf
@method('PUT')
 
<div class="card">

    <div class="card-header">
        <h3 class="card-title">Challan Details</h3>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3">
                <label>Challan No</label>
                <input type="text" class="form-control"
                       value="{{ $challan->challan_no }}" readonly>
            </div>

            <div class="col-md-3">
                <label>Status</label>

                @if($challan->status == 'dispatched')
                    <input type="text" class="form-control" value="Dispatched" readonly>
                @else
                    <select name="status" class="form-control">

                        <option value="draft" {{ $challan->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        {{-- <option value="approved" {{ $challan->status == 'approved' ? 'selected' : '' }}>Approved</option> --}}
                        <option value="cancelled" {{ $challan->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>

                    </select>
                @endif

            </div>

            <div class="col-md-3">
                <label>Date</label>
                <input type="date" name="challan_date"
                       class="form-control"
                       value="{{ $challan->challan_date }}"
                       {{ $challan->status == 'dispatched' ? 'readonly' : '' }}>
            </div>

        </div>

    </div>
</div>
 
<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h3 class="card-title">Products</h3>

        @if($challan->status != 'dispatched')
        <button type="button" class="btn btn-success btn-sm" id="addRow">
            + Add Product
        </button>
        @endif

    </div>

    <div class="card-body"> 
        <table class="table table-bordered" id="productTable"> 
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Total</th>
                    @if($challan->status != 'dispatched')
                        <th>Action</th>
                    @endif
                </tr>
            </thead>  
            <tbody>   
                @foreach($challan->items as $index => $item) 
                <tr>  
                    <td>
                        <select name="items[{{ $index }}][product_id]"
                                class="form-control product"
                                {{ $challan->status == 'dispatched' ? 'disabled' : '' }}> 
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->stock ?? 0 }}"
                                        {{ $item->product_id == $product->id ? 'selected' : '' }}> 
                                        {{ $product->name }} 
                                    </option>
                                @endforeach 
                        </select>
                    </td> 
                    <td class="stock-info">
                       {{ $item->product->stock_quantity ?? 0 }}
                    </td> 
                    <td>
                        <input type="number"
                               name="items[{{ $index }}][qty]"
                               class="form-control qty"
                               value="{{ $item->qty }}"
                               {{ $challan->status == 'dispatched' ? 'readonly' : '' }}>
                    </td> 
                    <td>
                        <input type="number"
                               name="items[{{ $index }}][rate]"
                               class="form-control rate"
                               value="{{ $item->rate }}"
                               {{ $challan->status == 'dispatched' ? 'readonly' : '' }}>
                    </td> 
                    <td>
                        <input type="text"
                               class="form-control total"
                               value="{{ $item->qty * $item->rate }}"
                               readonly>
                    </td>  
                    @if($challan->status != 'dispatched')
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">
                            X
                        </button>
                    </td>
                    @endif 
                </tr> 
                @endforeach 
            </tbody> 
        </table> 
    </div>
</div> 

<div class="text-right mb-3">  
    @if($challan->status != 'dispatched') 
        <button type="submit" class="btn btn-primary">
            Update Challan
        </button> 
    @else 
        <div class="alert alert-danger">
            🚫 This challan is dispatched and locked for editing
        </div> 
    @endif

</div> 
</form> 
@stop


@push('js')
<script>

let index = {{ count($challan->items) }};
 
$('#addRow').click(function () {

    let row = `
    <tr>

        <td>
        <select name="items[${index}][product_id]" class="form-control product">

            <option value="">-- Select Product --</option>

            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    data-price="{{ $product->price }}"
                    data-stock="{{ $product->stock ?? 0 }}">
                    {{ $product->name }}
                </option>
            @endforeach

        </select>
    </td>

        <td class="stock-info">0</td>

        <td>
            <input type="number" name="items[${index}][qty]" class="form-control qty" value="1">
        </td>

        <td>
            <input type="number" name="items[${index}][rate]" class="form-control rate" value="0">
        </td>

        <td>
            <input type="text" class="form-control total" readonly>
        </td>

        <td>
            <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
        </td>

    </tr>`;

    $('#productTable tbody').append(row);
    index++;
});

 
$(document).on('click', '.removeRow', function () {
    $(this).closest('tr').remove();
    calculateTotal();
});

$(document).on('change', '.product', function () {

    let selected = $(this).find(':selected');

    let price = selected.data('price') || 0;
    let stock = selected.data('stock') || 0;

    let row = $(this).closest('tr');

    row.find('.rate').val(price);
    row.find('.stock-info').text(stock);

    calculateTotal();
});


$(document).on('keyup change', '.qty, .rate', function () {
    calculateTotal();
});
 
function calculateTotal() {

    let subTotal = 0;

    $('#productTable tbody tr').each(function () {

        let qty = parseFloat($(this).find('.qty').val()) || 0;
        let rate = parseFloat($(this).find('.rate').val()) || 0;
        let stock = parseFloat($(this).find('.stock-info').text()) || 0;

        if (qty > stock) {
            alert("Quantity cannot exceed stock!");
            qty = stock;
            $(this).find('.qty').val(stock);
        }

        let total = qty * rate;

        $(this).find('.total').val(total.toFixed(2));

        subTotal += total;
    });

}

</script>
@endpush