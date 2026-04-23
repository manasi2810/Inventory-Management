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
                            <th>Total Amount</th>
                            <th>Status</th> 
                            <th width="250">Actions</th>
                        </tr>
                    </thead> 
                    <tbody> 

                    @foreach($challans as $challan)
                        <tr> 
                            <td>
                                @if($challan->status == 'dispatched')
                                    <input type="checkbox" class="dc-check" value="{{ $challan->id }}">
                                @endif
                            </td> 
                            <td>{{ $loop->iteration }}</td> 
                            <td><b>{{ $challan->challan_no ?? '-' }}</b></td>  
                            <td>{{ $challan->customer->name ?? '-' }}</td> 
                            <td>{{ $challan->challan_date }}</td> 
                            <td>{{ $challan->items->sum('qty') }}</td> 
                            <td>₹ {{ number_format($challan->total_amount, 2) }}</td> 
                            <td>
                                @if($challan->status == 'draft')
                                    <span class="badge badge-secondary">Draft</span>
                                @elseif($challan->status == 'approved')
                                    <span class="badge badge-primary">Approved</span>
                                @elseif($challan->status == 'dispatched')
                                    <span class="badge badge-warning">Dispatched</span>
                                @elseif($challan->status == 'delivered')
                                    <span class="badge badge-success">Delivered</span>
                                @elseif($challan->status == 'cancelled')
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                            </td>  
                          <td> 
                        @if($challan->status == 'dispatched')
                            <a href="{{ route('Delivery_challan.show', $challan->id) }}"
                            class="btn btn-sm btn-info">
                                View
                            </a>
                        @endif
  
                        @if($challan->status == 'dispatched')
                            <a href="{{ route('Delivery_challan.print', $challan->id) }}"
                            target="_blank"
                            class="btn btn-sm btn-secondary">
                                Print
                            </a>
                        @endif
 
                        @if($challan->status == 'draft')
                            <form action="{{ route('delivery_challan.approve', $challan->id) }}"
                                method="POST"
                                style="display:inline-block;">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm btn-success"
                                        onclick="return confirm('Approve this challan?')">
                                    Approve
                                </button>
                            </form>
                        @endif
 
                        @if($challan->status != 'dispatched')
                            <a href="{{ route('Delivery_challan.edit', $challan->id) }}"
                            class="btn btn-sm btn-primary">
                                Edit
                            </a>
                        @endif
 
                        @if($challan->status == 'approved')
                            <form action="{{ route('Delivery_challan.dispatch', $challan->id) }}"
                                method="POST"
                                style="display:inline-block;">
                                @csrf
                                <button type="submit"
                                        class="btn btn-sm btn-warning"
                                        onclick="return confirm('Dispatch this challan? Stock will be reduced.')">
                                    Dispatch
                                </button>
                            </form>
                        @endif
 
                        @if(
                        !$challan->deleted_at &&
                        in_array($challan->status, ['draft', 'approved']) &&
                        strtolower(auth()->user()->role) == 'admin'
                    )
                        <form action="{{ route('Delivery_challan.destroy', $challan->id) }}"
                            method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this challan?')">
                                Delete
                            </button>
                        </form>
                    @endif 
                        @if($challan->deleted_at && strtolower(auth()->user()->role) == 'admin')

                        <form action="{{ route('Delivery_challan.restore', $challan->id) }}"
                            method="POST"
                            style="display:inline-block;">
                            @csrf

                            <button class="btn btn-sm btn-success">
                                Restore
                            </button>
                        </form>

                    @endif

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

    $('#challanTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true
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
            alert('Please select ONLY dispatched challans');
            return;
        }  
        let url = "{{ route('Delivery_challan.bulkPrint') }}?ids=" + ids.join(',');
        window.open(url, '_blank');
    }); 
});
</script>
@endpush