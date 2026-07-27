@extends('adminlte::page')

@section('title', 'Customer Ledger')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between">
        <h3>{{ $customer->name }} - Ledger</h3>

        <h5>
            Current Balance:
            <span class="badge badge-danger">
                {{ $currentBalance }}
            </span>
        </h5>
    </div>

    <div class="card-body">

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Note</th>
                    <th>Balance</th>
                </tr>
            </thead>

           <tbody>
@forelse($ledgers as $ledger)
<tr>
    <td>{{ $ledger->created_at->format('d-m-Y') }}</td>

    <td>
        @switch($ledger->entry_type)
            @case('DISPATCH')
                <span class="badge badge-info">DISPATCH</span>
                @break

            @case('SALE')
                <span class="badge badge-primary">SALE</span>
                @break

            @case('PAYMENT')
                <span class="badge badge-success">PAYMENT</span>
                @break

            @case('DEBIT')
                <span class="badge badge-danger">DEBIT</span>
                @break

            @case('CREDIT')
                <span class="badge badge-success">CREDIT</span>
                @break

            @default
                <span class="badge badge-secondary">{{ $ledger->entry_type }}</span>
        @endswitch
    </td>

    <td>
        @if($ledger->debit > 0)
            <span class="text-danger">
                {{ number_format($ledger->debit,2) }}
            </span>
        @else
            <span class="text-success">
                {{ number_format($ledger->credit,2) }}
            </span>
        @endif
    </td>

    <td>{{ $ledger->reference_no }}</td>

    <td>{{ $ledger->remarks }}</td>

    <td>
        <strong>{{ number_format($ledger->balance_after,2) }}</strong>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center">No Ledger Found</td>
</tr>
@endforelse
</tbody>
        </table>

    </div>
</div>

@stop