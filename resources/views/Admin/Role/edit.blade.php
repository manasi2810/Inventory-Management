@extends('admin.layout.app', ['activePage' => 'roles', 'titlePage' => __('Edit Role')])

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Role - {{ $role->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Role</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <form action="{{ route('Role.update', $role->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Important for update -->

            <div class="card-body">
                <!-- Role Name -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="col-md-9 mt-3">
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
            </div>

            <!-- Footer -->
            <div class="card-footer text-right mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Role
                </button>
                <a href=" {{route('Role')}}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </section>
</div>
@endsection 