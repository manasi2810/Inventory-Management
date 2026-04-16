@extends('adminlte::page')

@section('title', 'Vendors')

@section('content_header')
    <h1>Vendors</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            {{-- HEADER --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">

                    <h3 class="card-title mb-0">Vendor Details</h3>

                    <a href="{{ route('Vendors.create') }}" class="btn btn-primary">
                        + Add Vendor
                    </a>

                </div>
            </div>

            <div class="card-body">

                {{-- Success Message --}}
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
                            <td>{{ $vendor->gst_number ?? '-' }}</td>

                            <td>
                                @if($vendor->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>

                            <td>

                                <a href="{{ route('Vendors.edit', $vendor->id) }}"
                                   class="btn btn-sm btn-info">
                                    Edit
                                </a>

                                <form action="{{ route('Vendors.destroy', $vendor->id) }}"
                                      method="POST"
                                      style="display:inline;">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this vendor?')">

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