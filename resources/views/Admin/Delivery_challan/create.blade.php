@extends('adminlte::page')

@section('title', 'Create Delivery Challan')

@section('content_header')
    <h1>Create Delivery Challan</h1>
@stop

@section('content')

<form action="{{ route('Delivery_challan.store') }}" method="POST" id="challanForm">
@csrf

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Challan Details</h3>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3">
                <label>Challan No</label>
                <input type="text" name="challan_no"
                       class="form-control"
                       value="{{ $challan_no }}"
                       readonly>
            </div>

            <div class="col-md-3">
                <label>Customer</label>
                <select name="customer_id" class="form-control" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">
                            {{ $customer->name }} ({{ $customer->mobile }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Challan Date</label>
                <input type="date"
                       name="challan_date"
                       class="form-control"
                       value="{{ date('Y-m-d') }}">
            </div> 
            <input type="hidden" name="status" value="draft">

        </div>

        <div class="row mt-2"> 
            <div class="col-md-4">
                <label>Transport Mode</label>
                <input type="text" name="transport_mode" class="form-control">
            </div> 
            <div class="col-md-4">
                <label>Vehicle No</label>
                <input type="text" name="vehicle_no" class="form-control">
            </div> 
            <div class="col-md-4">
                <label>LR No</label>
                <input type="text" name="lr_no" class="form-control">
            </div> 
        </div>

        <div class="row mt-2"> 
            <div class="col-md-6">
                <label>Dispatch From</label>
                <input type="text"
                       name="dispatch_from"
                       class="form-control"
                       value="Main Warehouse"
                       readonly>
            </div> 
            <div class="col-md-6">
                <label>Delivery To</label>
                <textarea name="delivery_to" class="form-control"></textarea>
            </div>  
        </div> 
        <div class="row mt-2">
    <div class="col-md-12">
        <label>Notes</label>
        <textarea name="notes" class="form-control" placeholder="Enter remarks..."></textarea>
    </div>
</div>
    </div>
</div>

 
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Products</h3> 
        <div class="card-tools">
            <button type="button" class="btn btn-success btn-sm" id="addRow">
                + Add Row
            </button>
        </div>
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
                <th>Action</th>
            </tr>
            </thead> 
            <tbody>
            <tr> 
                <td>
                    <select name="items[0][product_id]" class="form-control product">
                        <option value="">Select Product</option>
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
                    <input type="number"
                           name="items[0][qty]"
                           class="form-control qty"
                           value="1">
                </td> 
                <td>
                    <input type="number"
                           name="items[0][rate]"
                           class="form-control rate"
                           step="0.01">
                </td> 
                <td>
                    <input type="text" class="form-control total" readonly>
                </td> 
                <td>
                    <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                </td> 
            </tr>
            </tbody> 
        </table> 
    </div>
</div>
 
<div class="card"> 
    <div class="card-body text-right"> 
        <h4>Subtotal: ₹ <span id="subTotal">0.00</span></h4>
        <h4>GST (18%): ₹ <span id="gstAmount">0.00</span></h4> 
        <hr> 
        <h3>Grand Total: ₹ <span id="grandTotal">0.00</span></h3> 
        <input type="hidden" name="sub_total" id="subTotalInput">
        <input type="hidden" name="gst_amount" id="gstInput">
        <input type="hidden" name="grand_total" id="grandTotalInput"> 
    </div>
</div> 

<div class="text-right mb-3">
    <button type="submit" class="btn btn-primary" id="submitBtn">
        Save Draft Challan
    </button>
</div>

</form>

@stop

 
@push('js')
<script>

let rowIndex = 1;
 
$('#addRow').click(function () {

    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][product_id]" class="form-control product">
                <option value="">Select Product</option>
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
            <input type="number" name="items[${rowIndex}][qty]" class="form-control qty" value="1">
        </td>

        <td>
            <input type="number" name="items[${rowIndex}][rate]" class="form-control rate" step="0.01">
        </td>

        <td>
            <input type="text" class="form-control total" readonly>
        </td>

        <td>
            <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
        </td>
    </tr>`;

    $('#productTable tbody').append(row);
    rowIndex++;
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

    let gst = subTotal * 0.18;
    let grandTotal = subTotal + gst;

    $('#subTotal').text(subTotal.toFixed(2));
    $('#gstAmount').text(gst.toFixed(2));
    $('#grandTotal').text(grandTotal.toFixed(2));

    $('#subTotalInput').val(subTotal.toFixed(2));
    $('#gstInput').val(gst.toFixed(2));
    $('#grandTotalInput').val(grandTotal.toFixed(2));
}
 
$('#challanForm').on('submit', function () {
    $('#submitBtn').prop('disabled', true).text('Saving...');
});

</script>
@endpush