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
                {{-- ERRORS --}}
                @if ($errors->any()) 
                    <div class="alert alert-danger"> 
                        <ul class="mb-0"> 
                            @foreach ($errors->all() as $error) 
                                <li>{{ $error }}</li> 
                            @endforeach 
                        </ul> 
                    </div>  
                @endif 

                <form action="{{ route('Purchase.store') }}" method="POST"> 
                    @csrf 

                    {{-- HEADER --}}
                    <div class="row">   
                        <div class="col-md-4"> 
                            <x-select
                                label="Vendor"
                                name="vendor_id"
                                :options="$vendors->pluck('name', 'id')->toArray()"
                            /> 
                        </div>  
                        <div class="col-md-4"> 
                            <x-input
                                label="Invoice No"
                                name="invoice_no"
                                :value="$invoice_no"
                                :readonly="true"
                            />
                        </div>  
                        <div class="col-md-4"> 
                            <x-input
                                label="Purchase Date"
                                name="purchase_date"
                                type="date"
                            />  
                        </div>  
                    </div>   
                    <hr>  
                    <h5>Purchase Items</h5>  
                    {{-- TABLE --}}
                    <table class="table table-bordered" id="itemsTable"> 
                        <thead> 
                            <tr> 
                                <th>Product</th> 
                                <th width="120">Qty</th> 
                                <th width="150">Price</th> 
                                <th width="150">Total</th> 
                                <th width="100">
                                    <button type="button" class="btn btn-success btn-sm" id="addRow">
                                        + Add
                                    </button>
                                </th> 
                            </tr> 
                        </thead>  
                        <tbody> 
                            <tr> 
                                <td> 
                                    <select name="items[0][product_id]" class="form-control product-select" required> 
                                        <option value="">Select Product</option> 
                                        @foreach($products as $product) 
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
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
                                    <button type="button" class="btn btn-danger btn-sm removeRow">X</button> 
                                </td> 
                            </tr>  
                        </tbody> 
                    </table>  
                    <hr>  
                    <x-button type="submit" color="primary" icon="fas fa-save">
                        Save Purchase
                    </x-button>  
                </form> 
            </div> 
        </div> 
    </div>  
</div>
@stop

@push('js')
<script>
let rowIndex = 1;

/* ADD ROW */
$('#addRow').click(function () {

    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][product_id]" class="form-control product-select" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                        {{ $product->name }}
                    </option>
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

/* REMOVE ROW */
$(document).on('click', '.removeRow', function () {
    $(this).closest('tr').remove();
}); 
/* AUTO PRICE FILL ON PRODUCT SELECT */
$(document).on('change', '.product-select', function () {

    let row = $(this).closest('tr');
    let price = $(this).find(':selected').data('price') || 0;

    row.find('.price').val(price);

    let qty = parseFloat(row.find('.qty').val()) || 0;
    row.find('.total').val((qty * price).toFixed(2));
}); 
/* CALCULATE TOTAL */
$(document).on('input', '.qty, .price', function () {

    let row = $(this).closest('tr'); 
    let qty = parseFloat(row.find('.qty').val()) || 0;
    let price = parseFloat(row.find('.price').val()) || 0;

    row.find('.total').val((qty * price).toFixed(2));
});
</script>
@endpush