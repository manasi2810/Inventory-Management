@extends('adminlte::page')

@section('title', 'Vendor Payments')

@section('content')

<div class="card">
    <div class="card-header">
        <h3>{{ $vendor->name }} - Payments</h3>
        <h5>Outstanding: ₹ {{ $outstanding }}</h5>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- PAYMENT FORM --}}
        <form method="POST" action="{{ route('vendor.payments.store', $vendor->id) }}">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <input type="number" name="amount" class="form-control" placeholder="Enter amount">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary">Pay</button>
                </div>
            </div>
        </form>

        <hr>

        {{-- PAYMENT HISTORY --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Amount</th>
                    <th>Note</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @foreach($payments as $key => $payment)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>₹ {{ $payment->amount }}</td>
                        <td>{{ $payment->note }}</td>
                        <td>{{ $payment->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection