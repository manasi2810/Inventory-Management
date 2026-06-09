@extends('adminlte::page')

@section('title', 'Create Role')

@section('content_header')
    <div class="row"> 
        <div class="col-sm-6">
            <h1>Create Role</h1>
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

<form action="{{ route('Role.store') }}" method="POST">
    @csrf

    <div class="card"> 

    <div class="card-header">
        <h3 class="card-title">Role Details</h3>
    </div> 

    <div class="card-body"> 

        <div class="row"> 

            <div class="col-md-6">

                <x-input
                    label="Role Name"
                    name="name"
                    placeholder="Enter role name"
                />

            </div> 

        </div> 

        <hr>  

        <h5>Assign Permissions</h5> 

        <div class="row"> 

            @foreach($permissions as $permission)

                <div class="col-md-3">

                    <x-checkbox
                        label="{{ $permission->name }}"
                        name="permissions[]"
                        value="{{ $permission->name }}"
                        id="perm_{{ $permission->id }}"
                    />

                </div>

            @endforeach

        </div> 

    </div> 

    <div class="card-footer text-right"> 

        <a href="{{ route('Role') }}" class="btn btn-secondary">
            Cancel
        </a> 

        <x-button
            type="submit"
            color="success"
            icon="fas fa-save">

            Save Role

        </x-button>

    </div> 

</div>
</form>

@stop