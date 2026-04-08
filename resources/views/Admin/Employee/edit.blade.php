@extends('admin.layout.app', ['activePage' => 'dashboard', 'titlePage' => __('Edit Employee')])

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit Employee</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
              <li class="breadcrumb-item active">Edit Employee</li>
            </ol>
          </div>
        </div>
      </div>
    </section> 
    <!-- Main content -->
    <section class="content">
        <form action="{{ route('Employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Important for update -->
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
                                        <input type="text" class="form-control" id="name" name="name" 
                                            value="{{ old('name', $employee->user->name ?? '') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                            value="{{ old('email', $employee->user->email ?? '') }}">
                                    </div>
                                </div>
                                <!-- Contact -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_no">Contact No</label>
                                        <input type="text" class="form-control" id="contact_no" name="contact_no" value="{{ old('contact_no', $employee->contact_no) }}">
                                    </div>
                                </div>
                                <!-- Address -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" rows="2" name="address">{{ old('address', $employee->address) }}</textarea>
                                    </div>
                                </div>
                                <!-- Role -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select class="form-control" id="role" name="role">
                                            <option disabled>Select Role</option>
                                            <option value="Admin" {{ $employee->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="HR" {{ $employee->role == 'HR' ? 'selected' : '' }}>HR</option>
                                            <option value="Employee" {{ $employee->role == 'Employee' ? 'selected' : '' }}>Employee</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Password (Optional) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password <small>(Leave blank to keep current)</small></label>
                                        <input type="password" class="form-control" id="password" name="password">
                                    </div>
                                </div>
                                <!-- Profile Photo -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="profile_photo">Profile Photo</label>
                                        <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                                        @if($employee->profile_photo)
                                            <img src="{{ asset('storage/'.$employee->profile_photo) }}" alt="Profile Photo" width="80" class="mt-2">
                                        @endif
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
                                            <option disabled>Select Department</option>
                                            <option value="HR" {{ $employee->department == 'HR' ? 'selected' : '' }}>HR</option>
                                            <option value="Sales" {{ $employee->department == 'Sales' ? 'selected' : '' }}>Sales</option>
                                            <option value="Production" {{ $employee->department == 'Production' ? 'selected' : '' }}>Production</option>
                                            <option value="Admin" {{ $employee->department == 'Admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="IT" {{ $employee->department == 'IT' ? 'selected' : '' }}>IT</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Designation -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="designation">Designation</label>
                                        <select class="form-control" id="designation" name="designation">
                                            <option disabled>Select Designation</option>
                                            <option value="Manager" {{ $employee->designation == 'Manager' ? 'selected' : '' }}>Manager</option>
                                            <option value="Team Lead" {{ $employee->designation == 'Team Lead' ? 'selected' : '' }}>Team Lead</option>
                                            <option value="Staff" {{ $employee->designation == 'Staff' ? 'selected' : '' }}>Staff</option>
                                            <option value="Intern" {{ $employee->designation == 'Intern' ? 'selected' : '' }}>Intern</option>
                                            <option value="Ground Staff" {{ $employee->designation == 'Ground Staff' ? 'selected' : '' }}>Ground Staff</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Date of Joining -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_of_join">Date of Joining</label>
                                        <input type="date" class="form-control" id="date_of_join" name="date_of_join" value="{{ old('date_of_join', $employee->date_of_join) }}">
                                    </div>
                                </div>
                                <!-- Salary -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="salary">Salary</label>
                                        <input type="number" class="form-control" id="salary" name="salary" value="{{ old('salary', $employee->salary) }}">
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
                                        @if($employee->resume)
                                            <a href="{{ asset('storage/'.$employee->resume) }}" target="_blank">View Current</a>
                                        @endif
                                    </div>
                                </div>
                                <!-- Certificates -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="certificates">Certificates</label>
                                        <input type="file" class="form-control" id="certificates" name="certificates[]" multiple>
                                        @if($employee->certificates)
                                            @foreach(json_decode($employee->certificates) as $cert)
                                                <a href="{{ asset('storage/'.$cert) }}" target="_blank" class="d-block">View Certificate</a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <!-- ID Proof -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_proof">ID Proof</label>
                                        <input type="file" class="form-control" id="id_proof" name="id_proof">
                                        @if($employee->id_proof)
                                            <a href="{{ asset('storage/'.$employee->id_proof) }}" target="_blank">View ID Proof</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Update Employee</button>
                            <a href="{{ route('Employee') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </section>
</div>
@endsection