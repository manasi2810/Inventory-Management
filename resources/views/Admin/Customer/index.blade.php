@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <h1>Customers</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            {{-- HEADER --}}
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">

                    <h3 class="card-title mb-0">Customer Details</h3>

                    <a href="{{ route('Customer.create') }}" class="btn btn-primary">
                        + Add Customer
                    </a>

                </div>
            </div>

            <div class="card-body">

                {{-- SUCCESS MESSAGE --}}
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="table table-bordered table-striped" id="customerTable">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Mobile</th>
                            <th>City</th>
                            <th>GST</th>
                            <th>Status</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $customer->name }}</td>

                            <td>{{ $customer->company_name ?? '-' }}</td>

                            <td>{{ $customer->mobile ?? '-' }}</td>

                            <td>{{ $customer->city ?? '-' }}</td>

                            <td>{{ $customer->gst_number ?? '-' }}</td>

                            <td>
                                @if($customer->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>

                            <td>

                                <a href="{{ route('Customer.edit', $customer->id) }}" 
                                   class="btn btn-sm btn-info">
                                    Edit
                                </a>

                                <form action="{{ route('Customer.destroy', $customer->id) }}" 
                                      method="POST" 
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" 
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this customer?')">
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

    $('#customerTable').DataTable({
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