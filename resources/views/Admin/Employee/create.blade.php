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
              <li class="breadcrumb-item active">Employee</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
  <section class="content">
     <form action="{{ route('Employee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
    <div class="row"> 
        <!-- Basic Info Card -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Basic Info</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_no">Contact No</label>
                               <input type="text" class="form-control" id="contact_no" name="contact_no">
                            </div>
                        </div>
                        <div class="col-md-12">
                        <div class="form-group">
                        <label for="inputDescription">Address</label>
                        <textarea class="form-control" rows="2" name="address"></textarea>
                    </div>
                    </div>
                        <!-- Password -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>
                        <!-- Role -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <!-- Profile Photo -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="profile_photo">Profile Photo</label>
                                <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Job & Documents Card -->
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Job & Documents</h3>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">Job Details</h5>
                    <div class="row">
                       <!-- Department -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select class="form-control" id="department" name="department">
                                <option disabled selected>Select Department</option>
                                <option>HR</option>
                                <option>Sales</option>
                                <option>Production</option>
                                <option>Admin</option>
                                <option>IT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Designation -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="designation">Designation</label>
                            <select class="form-control" id="designation" name="designation">
                                <option disabled selected>Select Designation</option>
                                <option>Manager</option>
                                <option>Team Lead</option>
                                <option>Staff</option>
                                <option>Intern</option> 
                                <option>Ground Staff</option>
                            </select>
                        </div>
                    </div>
                        <!-- Date of Joining -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_of_join">Date of Joining</label>
                                <input type="date" class="form-control" id="date_of_join" name="date_of_join">
                            </div>
                        </div>
                        <!-- Salary -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="salary">Salary</label>
                                <input type="number" class="form-control" id="salary" name="salary">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">Documents</h5>
                    <div class="row">
                        <!-- Resume -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="resume">Resume</label>
                                <input type="file" class="form-control" id="resume" name="resume">
                            </div>
                        </div>
                        <!-- Certificates -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="certificates">Certificates</label>
                                <input type="file" class="form-control" id="certificates" name="certificates[]" multiple>
                            </div>
                        </div>
                        <!-- ID Proof -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_proof">ID Proof</label>
                                <input type="file" class="form-control" id="id_proof" name="id_proof">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Buttons -->
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Save Employee</button>
                    <a href="{{ route('Employee') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </div>

    </div>
     </form>
</section>
    <!-- /.content -->
</div>
@endsection