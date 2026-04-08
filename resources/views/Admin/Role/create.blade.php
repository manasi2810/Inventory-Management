 @extends('admin.layout.app', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('content')
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Employee </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Role</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
  <section class="content">
     <form action="{{ route('Role.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        
                    <div class="card-body"> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Role</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>
                         <div class="col-md-9">
                    <h5>Assign Permissions</h5>
                    <div class="row">
                       @foreach($permissions as $permission)
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="form-check-input" id="perm_{{ $permission->id }}">
                            <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
                        
                    </div> 
                    <!-- Footer -->
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Role
                        </button>
                        <a href="#" class="btn btn-secondary">Cancel</a>
                    </div> 
                </form>
</section>
    <!-- /.content -->
</div>
@endsection