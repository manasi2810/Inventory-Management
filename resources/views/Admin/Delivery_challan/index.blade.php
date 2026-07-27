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
                            Print Selected (Dispatched Only)
                        </button>

                        <a href="{{ route('Delivery_challan.create') }}"
                           class="btn btn-primary btn-sm">
                            + Create Challan
                        </a>
                    </div>

                </div>
            </div>

            {{-- BODY --}}
            <div class="card-body">

                <table class="table table-bordered table-striped" id="challanTable">

                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>#</th>
                            <th>Challan No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Qty</th>
                            <th>Dispatched</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th width="300">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($challans as $challan)

                        @php
                            $totalOrdered = $challan->items->sum('qty');
                            $totalDispatched = 0;

                            foreach($challan->items as $item){
                                $totalDispatched += $item->dispatched_qty ?? 0;
                            }

                            $pending = $totalOrdered - $totalDispatched;
                        @endphp

                        <tr>

                            {{-- SELECT --}}
                            <td>
                                @if($challan->status == 'dispatched')
                                    <input type="checkbox" class="dc-check" value="{{ $challan->id }}">
                                @endif
                            </td>

                            <td>{{ $loop->iteration }}</td>

                            {{-- CHALLAN NO (ERP ENTRY POINT) --}}
                            <td>
                                <b>{{ $challan->challan_no ?? '-' }}</b>

                                <br>

                                {{-- QUICK ERP HINT --}}
                              
                            </td>

                            <td>{{ $challan->customer->name ?? '-' }}</td>

                            <td>{{ $challan->challan_date }}</td>

                            <td>
                                {{ $totalOrdered }}
                            </td>

                            {{-- DISPATCHED SUMMARY --}}
                            <td>
                                <span class="text-success">
                                    {{ $totalDispatched }}
                                </span>

                               
                            </td>

                            <td>₹ {{ number_format($challan->total_amount, 2) }}</td>

                            {{-- STATUS (ERP LOGIC) --}}
                            <td>
                                @if($totalDispatched == 0)
                                    <span class="badge badge-secondary">Pending</span>

                                @elseif($totalDispatched < $totalOrdered)
                                    <span class="badge badge-info">Partial</span>

                                @else
                                    <span class="badge badge-success">Completed</span>
                                @endif
                            </td>

                            {{-- ACTIONS --}}
                            <td>

                                
                                @can('delivery.view')
                                <a href="{{ route('Delivery_challan.show', $challan->id) }}"
                                   class="btn btn-xs btn-info">
                                    View
                                </a>
                                @endcan

                                {{-- PRINT --}}
                                @if($challan->status == 'dispatched')
                                    @can('delivery.print')
                                    <a href="{{ route('Delivery_challan.print', $challan->id) }}"
                                       target="_blank"
                                       class="btn btn-xs btn-secondary">
                                        Print
                                    </a>
                                    @endcan
                                @endif

                                {{-- APPROVE --}}
                                @if($challan->status == 'draft')
                                    @can('delivery.approve')
                                    <form action="{{ route('delivery_challan.approve', $challan->id) }}"
                                          method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-xs btn-success"
                                                onclick="return confirm('Approve this challan?')">
                                            Approve
                                        </button>
                                    </form>
                                    @endcan
                                @endif

                                {{-- DISPATCH --}}
                                @if($challan->status == 'approved')
                                    <a href="{{ route('Delivery_challan.dispatch_page', $challan->id) }}"
                                       class="btn btn-xs btn-warning">
                                        <i class="fas fa-truck"></i> Dispatch
                                    </a>

                                @elseif($challan->status == 'partially_dispatched')
                                    <a href="{{ route('Delivery_challan.dispatch_page', $challan->id) }}"
                                       class="btn btn-xs btn-warning">
                                        Partial Dispatch
                                    </a>
                                @endif

                                {{-- DELETE --}}
                                @if($challan->status != 'dispatched')
                                    @can('delivery.delete')
                                    <form action="{{ route('Delivery_challan.destroy', $challan->id) }}"
                                          method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-xs btn-danger"
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    </form>
                                    @endcan
                                @endif

                                {{-- RESTORE --}}
                                @if($challan->deleted_at)
                                    @can('delivery.restore')
                                    <form action="{{ route('Delivery_challan.restore', $challan->id) }}"
                                          method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        <button class="btn btn-success btn-xs">
                                            Restore
                                        </button>
                                    </form>
                                    @endcan
                                @endif
                                {{-- DC RETURN --}}
                            @if($challan->status == 'dispatched' || $challan->status == 'partially_dispatched')
                            
                                @can('dc-return.create')
                                    <a href="{{ route('dc_return.create', $challan->id) }}"
                                          class="btn btn-dark btn-xs">
                                        DC Return
                                    </a>
                                @endcan
                        
                            @endif
                            
                            
                         

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

    $('#challanTable').DataTable({
        responsive: true,
        autoWidth: false
    });

    $('#selectAll').on('click', function () {
        $('.dc-check').prop('checked', this.checked);
    });

    $('#printSelected').on('click', function () {

        let ids = [];

        $('.dc-check:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            alert('Please select dispatched challans');
            return;
        }

        let url = "{{ route('Delivery_challan.bulkPrint') }}?ids=" + ids.join(',');
        window.open(url, '_blank');
    });

});
</script>
@endpush