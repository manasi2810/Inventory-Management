@extends('adminlte::page')

@section('title', 'Vendor')

@section('content_header')
<div class="row mb-2">

    <div class="col-sm-6">
        <h1>Vendor</h1>
    </div>

    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">Vendor</li>
        </ol>
    </div>

</div>
@stop

@section('content')

<div class="card">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">

        <h3 class="card-title mb-0">Vendor Details</h3>

        <a href="{{ route('Vendor.create') }}" class="btn btn-primary btn-sm">
            + Add Vendor
        </a>

    </div>

    {{-- BODY --}}
    <div class="card-body">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- TABLE --}}
        <table class="table table-bordered table-striped" id="example1">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($vendors as $vendor)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $vendor->name }}</td>
                    <td>{{ $vendor->phone }}</td>
                    <td>{{ $vendor->email }}</td>

                    <td>
                        <span class="badge {{ $vendor->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($vendor->status ?? 'active') }}
                        </span>
                    </td>

                    <td>
                        <a href="{{ route('Vendor.edit', $vendor->id) }}" class="btn btn-sm btn-info">
                            Edit
                        </a>

                        <form action="{{ route('Vendor.destroy', $vendor->id) }}"
                              method="POST"
                              style="display:inline-block;">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure?')">
                                Delete
                            </button>

                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        No Vendors Found
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@stop

{{-- DATATABLE SCRIPT --}}
@push('js')
<script>
$(function () {
    $('#example1').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
});
</script>
@endpush