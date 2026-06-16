@extends('adminlte::page')

@section('title', 'Vendor Aging Report')

@section('content')

<div class="container">

    <div class="card">

        <div class="card-header">
            <h3>Vendor Aging Report</h3>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>0-30 Days</th>
                        <th>31-60 Days</th>
                        <th>61-90 Days</th>
                        <th>90+ Days</th>
                        <th>Total Outstanding</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($report as $row)

                        <tr>
                            <td>{{ $row['vendor']->name }}</td>

                            <td>₹ {{ number_format($row['buckets']['0-30'], 2) }}</td>
                            <td>₹ {{ number_format($row['buckets']['31-60'], 2) }}</td>
                            <td>₹ {{ number_format($row['buckets']['61-90'], 2) }}</td>
                            <td>₹ {{ number_format($row['buckets']['90+'], 2) }}</td>

                            <td>
                                <strong>
                                    ₹ {{ number_format($row['outstanding'], 2) }}
                                </strong>
                            </td>
                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection