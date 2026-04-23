@extends('adminlte::page')

@section('title', 'Employee')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Employee</h1>
        </div>

        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Employee</li>
            </ol>
        </div>
    </div>
@stop

@section('content')

<div class="row">
    <div class="col-12"> 
        <div class="card"> 
            <div class="card-header">
    <div class="d-flex justify-content-between align-items-center w-100"> 
        <h3 class="card-title mb-0">Employee Details</h3> 
        <div>
            <a href="{{ route('Employee.create') }}" class="btn btn-primary">
                + Add Employee
            </a>
        </div> 
    </div>
</div>
            <div class="card-body"> 
                <table id="employeeTable" class="table table-bordered table-striped"> 
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Role</th>
                            <th>Date of Joining</th>
                            <th>Actions</th>
                        </tr>
                    </thead> 
                    <tbody>
                        @foreach($employees as $key => $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $employee->user->name ?? '-' }}</td>
                            <td>{{ $employee->user->email ?? '-' }}</td>
                            <td>{{ $employee->department ?? '-' }}</td>
                            <td>{{ $employee->designation ?? '-' }}</td>
                            <td>{{ $employee->user->role ?? '-' }}</td>
                            <td>{{ $employee->date_of_join ?? '-' }}</td> 
                            <td>
                                <a href="{{ route('Employee.edit', $employee->id) }}" class="btn btn-sm btn-info">
                                    Edit
                                </a> 
                                <form action="#" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">
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
$(function () {
    $('#employeeTable').DataTable({
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