@extends('adminlte::page')

@section('title', 'DC Return')

@section('content_header')
    <h1>Delivery Challan Return</h1>
@stop

@section('content')

<div class="card">

    <div class="card-header bg-warning">
        <h4 class="mb-0">
            DC Return - {{ $dc->challan_no }}
        </h4>
    </div>  
    <div class="card-body">  
        <div class="row mb-3">  
            <div class="col-md-4">
                <strong>Customer:</strong> {{ $dc->customer->name ?? '-' }}
            </div>  
            <div class="col-md-4">
                <strong>Date:</strong> {{ $dc->challan_date }}
            </div> 
            <div class="col-md-4">
                <strong>Status:</strong>
                <span class="badge badge-warning">{{ ucfirst($dc->status) }}</span>
            </div> 
        </div>  
        <hr>  
        <form action="{{ route('dc_return.store') }}" method="POST">
            @csrf 
            <input type="hidden" name="delivery_challan_id" value="{{ $dc->id }}">  
            <div class="table-responsive"> 
                <table class="table table-bordered"> 
                    <thead class="thead-dark">
                        <tr>
                            <th>Product</th>
                            <th>Delivered</th>
                            <th>Returned (History)</th>
                            <th>Remaining</th>
                            <th>Return Now</th>
                            <th>Condition</th>
                        </tr>
                    </thead>  
           <tbody>

@foreach($dc->items as $item)

<tr>
 
    <td>
        {{ $item->product->name }}
        <input type="hidden" name="product_id[{{ $item->id }}]" value="{{ $item->product_id }}">
    </td> 
    <td>
        {{ $item->qty }}
    </td> 
    <td>
        @forelse($item->return_breakdown ?? [] as $type => $qty)
            <span class="badge badge-info">
                {{ ucfirst($type) }} ({{ $qty }})
            </span><br>
        @empty
            <span class="text-muted">No return</span>
        @endforelse
    </td> 
    <td>
        <span class="badge badge-warning">
            {{ $item->remaining_qty }}
        </span>
    </td> 
    <td>
        @if($item->remaining_qty > 0)
            <input type="number"
                name="return_qty[{{ $item->id }}]"
                class="form-control return-input"
                min="0"
                max="{{ $item->remaining_qty }}"
                value="0">
        @else
            <span class="text-success">Completed</span>
        @endif
    </td> 
    <td>
        @if($item->remaining_qty > 0)
            <select name="condition[{{ $item->id }}]" class="form-control">
                <option value="good">Good</option>
                <option value="damaged">Damaged</option>
                <option value="scrap">Scrap</option>
            </select>
        @else
            -
        @endif
    </td> 
</tr>

@endforeach

</tbody>
                </table>
            </div>  
 
            @if(!$dc->all_returned)

                <div class="form-group mt-3">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>  
                <button type="submit" class="btn btn-success">
                    Save DC Return
                </button> 

            @else
                <div class="alert alert-success mt-3">
                    All products have already been fully returned.
                </div>
            @endif

            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                Back
            </a>  
        </form>  
    </div>
</div> 

@stop

@push('js')
<script>
$(document).ready(function () {

    $('input[type=number]').on('input', function () {

        let max = parseFloat($(this).attr('max')) || 0;
        let value = parseFloat($(this).val());

        if (value > max) {
            alert('Cannot exceed remaining quantity');
            $(this).val(max);
        } 
        if (value < 0) {
            $(this).val(0);
        }
    });

});
</script>
@endpush