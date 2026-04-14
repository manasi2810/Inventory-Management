@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">

        <h1 class="m-0">Role</h1>

        <a href="{{ route('Role.create') }}" class="btn btn-primary btn-sm">
            + Create Role
        </a>

    </div>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <table class="table table-bordered table-striped" id="example1">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>

                @foreach($roles as $role)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $role->name }}</td>

                    <td>
                        <a href="{{ route('Role.edit', $role->id) }}" class="btn btn-sm btn-info">
                            Edit
                        </a>

                        <form action="{{ route('Role.destroy', $role->id) }}"
                              method="POST"
                              style="display:inline-block;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger"
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

@stop

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
        buttons: [
            "copy",
            "csv",
            "excel",
            "pdf",
            "print"
        ]
    });

});
</script>
@endpush