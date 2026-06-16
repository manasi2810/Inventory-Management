@extends('adminlte::page')

@section('title', 'Vendors')

@section('content_header')
    <h1>Vendors</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">

       <div class="card"> 
                <div class="card-header">

                    <div class="d-flex justify-content-between align-items-center">

                        <h3 class="card-title mb-0">Vendor Details</h3>

                        <div class="d-flex gap-2">

                            <a href="{{ route('vendor.aging.report') }}" class="btn btn-dark btn-sm">
                                Aging Report
                            </a>

                            <a href="{{ route('Vendors.create') }}" class="btn btn-primary btn-sm">
                                + Add Vendor
                            </a>

                        </div>

                    </div>

                </div>
            </div>
            <div class="card-body">  
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif 
                <table class="table table-bordered table-striped" id="vendorTable"> 
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Outstanding</th>
                            <th>GST</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead> 
                    <tbody> 
                        @foreach($vendors as $vendor) 
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->email ?? '-' }}</td>
                            <td>{{ $vendor->contact ?? '-' }}</td>
                            <td>{{ $vendor->company_name ?? '-' }}</td>
                            <td>
                                ₹ {{ number_format($vendor->getOutstandingAmount(), 2) }}
                            </td>
                            <td>{{ $vendor->gst_number ?? '-' }}</td> 
                            <td> 
                        @if($vendor->deleted_at)
                                <span class="badge badge-dark">Deleted</span>
                            @elseif($vendor->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                    </td> 
                          <td>

    @can('vendor.edit')
    <a href="{{ route('Vendors.edit', $vendor->id) }}"
       class="btn btn-sm btn-info"   title="Edit Vendor">
        <i class="fas fa-edit"></i>
    </a>

    <a href="{{ route('vendor.ledger', $vendor->id) }}"
       class="btn btn-dark btn-sm" title="Ledger">
        <i class="fas fa-book"></i>
    </a>
    <a href="{{ route('vendor.statement', $vendor->id) }}"
   class="btn btn-dark btn-sm" title="Statement">
    <i class="fas fa-file-alt"></i>
</a>

    <a href="{{ route('vendor.payments', $vendor->id) }}"
       class="btn btn-success btn-sm" title="Payment">
        <i class="fas fa-rupee-sign"></i> 
    </a>
    @if($vendor->deleted_at)

<form action="{{ route('Vendors.restore',$vendor->id) }}"
      method="POST"
      style="display:inline;">
    @csrf

    <button type="submit"
            class="btn btn-warning btn-sm"
            title="Restore">
        <i class="fas fa-undo"></i>
    </button>
</form>

@endif

    @endcan

    @can('vendor.delete')
    <form action="{{ route('Vendors.destroy', $vendor->id) }}"
          method="POST"
          style="display:inline;">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger btn-sm"
                title="Delete"
                onclick="return confirm('Are you sure you want to delete this vendor?')">
            <i class="fas fa-trash"></i> 
        </button>
    </form>
    @endcan

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
    $('#vendorTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: ["copy", "csv", "excel", "pdf", "print"]
    });
});
</script>
@endpush