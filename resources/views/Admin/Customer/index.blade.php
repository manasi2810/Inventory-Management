@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <h1>Customers</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12"> 
        <div class="card">  
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center"> 
                    <h3 class="card-title mb-0">Customer Details</h3> 
                    <a href="{{ route('Customer.create') }}" class="btn btn-primary">
                        + Add Customer
                    </a> 
                </div>
            </div> 
            <div class="card-body"> 
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif 
                <table class="table table-bordered table-striped" id="customerTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer Code</th>
                <th>Name</th>
                <th>Company</th>
                <th>Mobile</th>
                <th>City</th>
                <th>GST</th>
                <th>Status</th>
                <th width="250">Actions</th>
            </tr>
        </thead>

        <tbody>
        @foreach($customers as $customer)
        <tr>

            <td>{{ $loop->iteration }}</td>

            <td>{{ $customer->customer_code ?? '-' }}</td>

            <td>{{ $customer->name }}</td>

            <td>{{ $customer->company_name ?? '-' }}</td>

            <td>{{ $customer->mobile ?? '-' }}</td>

            <td>{{ $customer->city ?? '-' }}</td>

            <td>{{ $customer->gst_number ?? '-' }}</td>

            <td>
            <span class="status-badge badge {{ $customer->status ? 'badge-success' : 'badge-danger' }}"
                id="status-{{ $customer->id }}">
                {{ $customer->status ? 'Active' : 'Inactive' }}
            </span>
        </td>

            <td>

                @can('customer.edit')
                    <a href="{{ route('Customer.edit', $customer->id) }}"
                    class="btn btn-sm btn-info">
                        Edit
                    </a>

                @endcan
                    
                    <a href="{{ route('customer.ledger', $customer->id) }}"
                    class="btn btn-dark btn-sm">
                    Ledger
                    </a>

                @can('customer.delete')

                    <form action="{{ route('Customer.toggleStatus', $customer->id) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf

                        @if($customer->status) 
                            <button type="submit"
                                    class="btn btn-sm btn-warning"
                                    onclick="return confirm('Deactivate this customer?')">
                                <i class="fas fa-ban"></i> Deactivate
                            </button> 
                        @else 
                            <button type="submit"
                                    class="btn btn-sm btn-success"
                                    onclick="return confirm('Activate this customer?')">
                                <i class="fas fa-check"></i> Activate
                            </button> 
                        @endif 
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

    $('.toggle-status-btn').click(function () {

        let button = $(this);
        let customerId = button.data('id');

        $.ajax({
            url: '/customer/' + customerId + '/toggle-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },

            success: function(response) {

                let badge = $('#status-' + customerId);

                if(response.status) {

                    badge
                        .removeClass('badge-danger')
                        .addClass('badge-success')
                        .text('Active');

                    button
                        .removeClass('btn-success')
                        .addClass('btn-warning')
                        .text('Deactivate');

                } else {

                    badge
                        .removeClass('badge-success')
                        .addClass('badge-danger')
                        .text('Inactive');

                    button
                        .removeClass('btn-warning')
                        .addClass('btn-success')
                        .text('Activate');
                }
            }
        });

    });

});

</script>
@endpush