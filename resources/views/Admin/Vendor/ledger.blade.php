@extends('adminlte::page')

@section('title', 'Vendor Ledger')

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="mb-0">
            {{ $vendor->name }} - Ledger
        </h3>
    </div>

    <div class="card-body">

        {{-- Summary Cards --}}
        <div class="row mb-4">

            <div class="col-md-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Credit Limit</h6>
                        <h4 class="text-primary mb-0">
                            ₹ {{ number_format($vendor->credit_limit, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-warning shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Opening Balance</h6>
                        <h4 class="text-warning mb-0">
                            ₹ {{ number_format($vendor->opening_balance, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-success shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Outstanding</h6>
                        <h4 class="text-success mb-0">
                            ₹ {{ number_format($outstanding, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-dark shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Current Balance</h6>
                        <h4 class="text-dark mb-0">
                            ₹ {{ number_format($currentBalance, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        {{-- Ledger Table --}}
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
                        @if($ledger->entry_type == 'DEBIT')
                            <span class="badge badge-danger">
                                DEBIT
                            </span>
                        @else
                            <span class="badge badge-success">
                                CREDIT
                            </span>
                        @endif
                    </td>

                    <td>
                        ₹ {{ number_format($ledger->amount, 2) }}
                    </td>

                    <td>
                        {{ $ledger->reference_type ?? '-' }}
                    </td>

                    <td>
                        {{ $ledger->note ?? '-' }}
                    </td>

                    <td>
                        <strong>
                            ₹ {{ number_format($ledger->balance_after, 2) }}
                        </strong>
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="text-center">
                        No Ledger Entries Found
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>

    </div>

</div>

@stop