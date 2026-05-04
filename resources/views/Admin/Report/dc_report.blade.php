@extends('adminlte::page')

@section('title', 'DC Report')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Delivery Challan Report</h1>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- FILTER --}}
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Filter</h3>
        </div>

        <form method="GET">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="from_date"
                               value="{{ request('from_date') }}"
                               class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="to_date"
                               value="{{ request('to_date') }}"
                               class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                            <option value="dispatched" {{ request('status')=='dispatched'?'selected':'' }}>Dispatched</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary mr-2">
                            Filter
                        </button>

                        <a href="{{ route('reports.dc') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- SUMMARY --}}
    <div class="row mb-3">

        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $summary->total_dc ?? 0 }}</h3>
                    <p>Total DC</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($summary->total_amount ?? 0, 2) }}</h3>
                    <p>Total Amount</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $summary->approved_count ?? 0 }}</h3>
                    <p>Approved</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $summary->dispatched_count ?? 0 }}</h3>
                    <p>Dispatched</p>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $summary->draft_count ?? 0 }}</h3>
                    <p>Draft</p>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="card">

        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title">DC List</h3>

            {{-- EXPORT --}}
            <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('reports.dc.export', request()->all()) }}"
        class="btn btn-success">
            Export Excel
        </a>
        </div>  
        </div>

        <div class="card-body">

            <table id="dcTable" class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>DC No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($dcList as $dc)

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $dc->challan_no }}</td>
                        <td>{{ \Carbon\Carbon::parse($dc->challan_date)->format('d-m-Y') }}</td>
                        <td>{{ $dc->customer->name ?? '-' }}</td>
                        <td>{{ $dc->total_qty }}</td>
                        <td>{{ number_format($dc->total_amount, 2) }}</td>
                        <td>
                            @php
                                $status = strtolower($dc->status);
                                $badge = match($status) {
                                    'draft' => 'secondary',
                                    'approved' => 'primary',
                                    'dispatched' => 'success',
                                    default => 'dark'
                                };
                            @endphp

                            <span class="badge badge-{{ $badge }}">
                                {{ ucfirst($dc->status) }}
                            </span>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center">No records found</td>
                    </tr>
                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@stop

@push('js')
<script>
$(function () {
    $('#dcTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true
    });
});
</script>
@endpush