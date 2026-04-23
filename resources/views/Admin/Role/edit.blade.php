@extends('adminlte::page')

@section('title', 'Edit Role')

@section('content_header')
    <div class="row">

        <div class="col-sm-6">
            <h1>Edit Role - {{ $role->name }}</h1>
        </div>

        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Role</li>
            </ol>
        </div>

    </div>
@stop

@section('content')

<form action="{{ route('Role.update', $role->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
 
        <div class="card-header">
            <h3 class="card-title">Edit Role</h3>
        </div>
 
        <div class="card-body">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role Name</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ $role->name }}"
                               required>
                    </div>
                </div>

            </div>

            <hr>

            <h5>Assign Permissions</h5>

            <div class="row">

                @foreach($permissions as $permission)
                <div class="col-md-3">
                    <div class="form-check">

                        <input type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->name }}"
                               class="form-check-input"
                               id="perm_{{ $permission->id }}"
                               {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>

                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>

                    </div>
                </div>
                @endforeach

            </div>

        </div>
 
        <div class="card-footer text-right">

            <a href="{{ route('Role') }}" class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Update Role
            </button>

        </div>

    </div>

</form>

@stop