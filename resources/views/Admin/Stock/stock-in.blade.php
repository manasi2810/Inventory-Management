@extends('adminlte::page')

@section('title', 'Stock In')

@section('content_header')
    <h1>Stock In History</h1>
@stop

@section('content')

<div class="card"> 
    <div class="card-body"> 
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Type</th>
                     <th>PO No</th> 
                    <th>Date</th>
                </tr>
            </thead> 
            <tbody>
                @foreach($stockIns as $key => $stock)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $stock->product->name ?? '-' }}</td>
                        <td>{{ $stock->qty }}</td>
                        <td>
                            <span class="badge badge-success">
                                {{ $stock->type }}
                            </span>
                        </td>
                      <td>
                        @if($stock->reference_id)
                            <a href="{{ route('Purchase.show', ['Purchase' => $stock->reference_id]) }}">
                                {{ $stock->po_no }}
                            </a>
                        @else
                            {{ $stock->po_no }}
                        @endif
                    </td>
                        <td>{{ $stock->created_at }}</td>
                    </tr>
                @endforeach
            </tbody> 
        </table> 
    </div>
</div>

@stop