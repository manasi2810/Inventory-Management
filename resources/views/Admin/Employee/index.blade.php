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
              <li class="breadcrumb-item active">Employee</li>
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
          <h3 class="card-title">Employee Details</h3>
          <a href="{{ route('Employee.create') }}" class="btn btn-primary position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%);">
              Add Employee
          </a>
      </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
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
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $employee->user->name ?? '-' }}</td>
                        <td>{{ $employee->user->email ?? '-' }}</td>
                        <td>{{ $employee->department ?? '-' }}</td>
                        <td>{{ $employee->designation ?? '-' }}</td>
                        <td>{{ $employee->user->role ?? '-' }}</td>
                        <td>{{ $employee->date_of_join ?? '-' }}</td>
                        <td>
                             <!-- Edit Button -->
                          <a href="{{ route('Employee.edit', $employee->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                  
                </table>
              </div> 
            </div>
            </div>  
        <!-- /.row -->
      </div> 
@endsection