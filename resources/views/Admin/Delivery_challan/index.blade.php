@extends('adminlte::page')

@section('title', 'Delivery Challan')

@section('content_header')
    <h1>Delivery Challan</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">

            {{-- HEADER --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">

                    <h3 class="card-title mb-0">Delivery Challan List</h3>

                    <div>
                        <button class="btn btn-dark btn-sm" id="printSelected">
                            Print Selected
                        </button>

                        <a href="{{ route('Delivery_challan.create') }}" 
                           class="btn btn-primary btn-sm">
                            + Create Challan
                        </a>
                    </div>

                </div>
            </div>

            <div class="card-body">

                {{-- SUCCESS MESSAGE --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- TABLE --}}
                <table class="table table-bordered table-striped" id="challanTable">

                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>#</th>
                            <th>Challan No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Qty</th>
                            <th>Total Amount</th>
                            <th>Stock Status</th>
                            <th width="300">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($challans as $challan)
                        <tr>

                            {{-- CHECKBOX --}}
                            <td>
                                <input type="checkbox" class="dc-check" value="{{ $challan->id }}">
                            </td>

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <b>{{ $challan->challan_no ?? '-' }}</b>
                            </td>

                            <td>
                                {{ $challan->customer->name ?? '-' }}
                            </td>

                            <td>
                                {{ $challan->challan_date }}
                            </td>

                            {{-- TOTAL QTY --}}
                            <td>
                                {{ $challan->items->sum('qty') }}
                            </td>

                            {{-- TOTAL AMOUNT --}}
                            <td>
                                ₹ {{ number_format($challan->total_amount, 2) }}
                            </td>

                            {{-- STOCK STATUS (OPTIONAL LOGIC) --}}
                            <td>
                                @if($challan->status == 'delivered')
                                    <span class="badge badge-success">Delivered</span>

                                @elseif($challan->status == 'partial')
                                    <span class="badge badge-info">Partial</span>

                                @elseif($challan->status == 'cancelled')
                                    <span class="badge badge-danger">Cancelled</span>

                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td>

                                {{-- VIEW --}}
                                <a href="{{ route('Delivery_challan.show', $challan->id) }}" 
                                   class="btn btn-sm btn-info">
                                    View
                                </a>

                                {{-- PRINT --}}
                                <a href="{{ route('Delivery_challan.print', $challan->id) }}" 
                                   target="_blank"
                                   class="btn btn-sm btn-secondary">
                                    Print
                                </a>

                                {{-- EDIT --}}
                                <a href="{{ route('Delivery_challan.edit', $challan->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    Edit
                                </a>

                                {{-- DELETE --}}
                                <form action="{{ route('Delivery_challan.destroy', $challan->id) }}" 
                                      method="POST" 
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure to delete this challan?')">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>
                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>

@stop


@push('js')
<script>
$(document).ready(function () {

    // DataTable
    $('#challanTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: ["copy", "csv", "excel", "pdf", "print"]
    });

    // Select All Checkbox
    $('#selectAll').on('click', function () {
        $('.dc-check').prop('checked', this.checked);
    });

    // Multiple Print
    $('#printSelected').on('click', function () {

    let ids = [];

    $('.dc-check:checked').each(function () {
        ids.push($(this).val());
    });

    if (ids.length === 0) {
        alert('Please select at least one Delivery Challan');
        return;
    }

    let url = "{{ route('Delivery_challan.bulkPrint') }}?ids=" + ids.join(',');

    window.open(url, '_blank');
});

});
</script>
@endpush