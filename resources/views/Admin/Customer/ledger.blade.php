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
                            @if($ledger->entry_type == 'CREDIT')
                                <span class="badge badge-success">CREDIT (SALE)</span>
                            @else
                                <span class="badge badge-danger">DEBIT (PAYMENT)</span>
                            @endif
                        </td>

                        <td>{{ $ledger->amount }}</td>
                        <td>{{ $ledger->reference_type }}</td>
                        <td>{{ $ledger->note }}</td>
                        <td><b>{{ $ledger->balance_after }}</b></td>
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