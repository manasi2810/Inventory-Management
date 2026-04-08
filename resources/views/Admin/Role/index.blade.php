 @extends('admin.layout.table', ['activePage' => 'dashboard', 'titlePage' => __('Dashboard')])

@section('content')
    <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Pincode </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Role</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
         <div class="card">
               <div class="card-header position-relative">
    <h3 class="card-title">Role Details</h3>
    <a href="{{ route('Role.create') }}" class="btn btn-primary position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%);">
        Add Role
    </a>
</div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th> 
                    <th>Actions</th>
                  </tr>
                  </thead> 
                   @foreach($roles as $key => $role)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $role->name }}</td>  
            <td>
                <a href="{{ route('Role.edit', $role->id) }}" class="btn btn-sm btn-info">Edit</a>
                <a href=" " class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
@endforeach
                   
                  
                  
                  
                </table>
              </div> 
            </div>
            </div>  
        <!-- /.row -->
      </div>
     
 
@endsection