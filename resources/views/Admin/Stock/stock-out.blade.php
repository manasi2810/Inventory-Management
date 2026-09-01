@extends('adminlte::page')

@section('title', 'Stock Out')

@section('content_header')
    <h1>Stock Out History</h1>
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
                         <th>Reference No</th> 
                        <th>Date</th>
                    </tr>
                </thead> 
                <tbody>
                   @foreach($stockOuts as $key => $stock)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $stock->product->name ?? '-' }}</td>
                    <td>{{ $stock->qty }}</td>
                    <td>
                        <span class="badge badge-danger">
                            {{ $stock->type }}
                        </span>
                    </td>
                  <td>
                @if($stock->reference_id)
                    <a href="{{ route('dispatch.show', $stock->reference_id) }}">
                        {{ $stock->reference_id }}
                    </a>
                @else
                    -
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