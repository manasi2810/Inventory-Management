@extends('adminlte::page')

@section('title', 'Vendor Statement')

@section('content_header')
    <h1>Vendor Statement</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">
                    {{ $vendor->name }} - Ledger Statement
                </h3>
            </div>

            <div class="card-body">

                {{-- Vendor Summary --}}
                <div class="row mb-3">

                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-user"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Vendor</span>
                                <span class="info-box-number">
                                    {{ $vendor->name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-wallet"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Opening Balance</span>
                                <span class="info-box-number">
                                    ₹ {{ number_format($vendor->opening_balance, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger">
                                <i class="fas fa-money-bill"></i>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Current Outstanding</span>
                                <span class="info-box-number">
                                    ₹ {{ number_format($vendor->getOutstandingAmount(), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Ledger Table --}}
                <table class="table table-bordered table-striped">

                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Debit (₹)</th>
                            <th>Credit (₹)</th>
                            <th>Running Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ledgers as $ledger)

                            <tr>
                                <td>
                                    {{ $ledger->created_at->format('d-m-Y') }}
                                </td>

                                <td>
                                    @if($ledger->entry_type == 'CREDIT')
                                        <span class="badge badge-success">CREDIT</span>
                                    @else
                                        <span class="badge badge-danger">DEBIT</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $ledger->reference_type ?? '-' }}
                                </td>

                                <td>
                                    @if($ledger->entry_type == 'DEBIT')
                                        ₹ {{ number_format($ledger->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if($ledger->entry_type == 'CREDIT')
                                        ₹ {{ number_format($ledger->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <strong>
                                        ₹ {{ number_format($ledger->running_balance, 2) }}
                                    </strong>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center">
                                    No transactions found
                                </td>
                            </tr>

                        @endforelse
                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>

@stop