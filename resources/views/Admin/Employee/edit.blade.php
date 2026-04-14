@extends('adminlte::page')

@section('title', 'Edit Employee')

@section('content_header')
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
@stop

@section('content')

<form action="{{ route('Employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">

        <!-- Basic Info -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Basic Info</h3>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name', $employee->user->name ?? '') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email"
                                value="{{ old('email', $employee->user->email ?? '') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Contact No</label>
                            <input type="text" class="form-control" name="contact_no"
                                value="{{ old('contact_no', $employee->contact_no) }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Address</label>
                            <textarea class="form-control" name="address">{{ old('address', $employee->address) }}</textarea>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Role</label>
                            <select class="form-control" name="role">
                                <option value="Admin" {{ $employee->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="HR" {{ $employee->role == 'HR' ? 'selected' : '' }}>HR</option>
                                <option value="Employee" {{ $employee->role == 'Employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Password <small>(Leave blank to keep current)</small></label>
                            <input type="password" class="form-control" name="password">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Profile Photo</label>
                            <input type="file" class="form-control" name="profile_photo">

                            @if($employee->profile_photo)
                                <img src="{{ asset('storage/'.$employee->profile_photo) }}" width="80" class="mt-2">
                            @endif
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Job & Documents -->
        <div class="col-md-6">

            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Job & Documents</h3>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6 form-group">
                            <label>Department</label>
                            <select class="form-control" name="department">
                                <option value="HR" {{ $employee->department == 'HR' ? 'selected' : '' }}>HR</option>
                                <option value="Sales" {{ $employee->department == 'Sales' ? 'selected' : '' }}>Sales</option>
                                <option value="Production" {{ $employee->department == 'Production' ? 'selected' : '' }}>Production</option>
                                <option value="Admin" {{ $employee->department == 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="IT" {{ $employee->department == 'IT' ? 'selected' : '' }}>IT</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Designation</label>
                            <select class="form-control" name="designation">
                                <option value="Manager" {{ $employee->designation == 'Manager' ? 'selected' : '' }}>Manager</option>
                                <option value="Team Lead" {{ $employee->designation == 'Team Lead' ? 'selected' : '' }}>Team Lead</option>
                                <option value="Staff" {{ $employee->designation == 'Staff' ? 'selected' : '' }}>Staff</option>
                                <option value="Intern" {{ $employee->designation == 'Intern' ? 'selected' : '' }}>Intern</option>
                                <option value="Ground Staff" {{ $employee->designation == 'Ground Staff' ? 'selected' : '' }}>Ground Staff</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Date of Joining</label>
                            <input type="date" class="form-control" name="date_of_join"
                                value="{{ old('date_of_join', $employee->date_of_join) }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Salary</label>
                            <input type="number" class="form-control" name="salary"
                                value="{{ old('salary', $employee->salary) }}">
                        </div>

                    </div>

                    <hr>

                    <h5>Documents</h5>

                    <div class="row">

                        <div class="col-md-6 form-group">
                            <label>Resume</label>
                            <input type="file" class="form-control" name="resume">

                            @if($employee->resume)
                                <a href="{{ asset('storage/'.$employee->resume) }}" target="_blank">View</a>
                            @endif
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Certificates</label>
                            <input type="file" class="form-control" name="certificates[]" multiple>

                            @if($employee->certificates)
                                @foreach(json_decode($employee->certificates) as $cert)
                                    <a href="{{ asset('storage/'.$cert) }}" target="_blank" class="d-block">View</a>
                                @endforeach
                            @endif
                        </div>

                        <div class="col-md-6 form-group">
                            <label>ID Proof</label>
                            <input type="file" class="form-control" name="id_proof">

                            @if($employee->id_proof)
                                <a href="{{ asset('storage/'.$employee->id_proof) }}" target="_blank">View</a>
                            @endif
                        </div>

                    </div>

                </div>

                <div class="card-footer">
                    <button class="btn btn-success">Update Employee</button>
                    <a href="{{ route('Employee') }}" class="btn btn-secondary">Cancel</a>
                </div>

            </div>

        </div>

    </div>

</form>

@stop