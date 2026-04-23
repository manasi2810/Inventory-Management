@extends('adminlte::page')

@section('title', 'Create Purchase')

@section('content_header')
    <h1>Create Purchase</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">New Purchase Entry</h3>
            </div>
            <div class="card-body">
              
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('Purchase.store') }}" method="POST">
                    @csrf

                   
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vendor</label>
                                <select name="vendor_id" class="form-control" required>
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">
                                            {{ $vendor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Invoice No</label>
                            <input type="text" class="form-control" value="{{ $invoice_no }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <hr>
                   
                    <h5>Purchase Items</h5>
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>
                                    <button type="button" class="btn btn-success btn-sm" id="addRow">
                                        + Add
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="items[0][product_id]" class="form-control" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][qty]" class="form-control qty" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][price]" class="form-control price" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control total" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm removeRow">
                                        X
                                    </button>
                                </td>
                            </tr>
                        </tbody> 
                    </table> 
                    <hr> 
                    <button type="submit" class="btn btn-primary">
                        Save Purchase
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@push('js')
<script>

let rowIndex = 1;
 
$('#addRow').click(function () {

    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][product_id]" class="form-control" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </td>

        <td>
            <input type="number" name="items[${rowIndex}][qty]" class="form-control qty" required>
        </td>

        <td>
            <input type="number" name="items[${rowIndex}][price]" class="form-control price" required>
        </td>

        <td>
            <input type="number" class="form-control total" readonly>
        </td>

        <td>
            <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
        </td>

    </tr>`;

    $('#itemsTable tbody').append(row);
    rowIndex++;
});
 
$(document).on('click', '.removeRow', function () {
    $(this).closest('tr').remove();
});

 
$(document).on('input', '.qty, .price', function () {

    let row = $(this).closest('tr');

    let qty = parseFloat(row.find('.qty').val()) || 0;
    let price = parseFloat(row.find('.price').val()) || 0;

    let total = qty * price;

    row.find('.total').val(total.toFixed(2));
});

</script>
@endpush