@extends('adminlte::page')

@section('title', 'Employee')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Employee</h1>
        </div>

        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active">Employee</li>
            </ol>
        </div>
    </div>
@stop

@section('content')

<form action="{{ route('Employee.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
                            <input type="text" class="form-control" name="name">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Contact No</label>
                            <input type="text" class="form-control" name="contact_no">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Address</label>
                            <textarea class="form-control" name="address"></textarea>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Profile Photo</label>
                            <input type="file" class="form-control" name="profile_photo">
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
                            <select name="department" class="form-control">
                                <option disabled selected>Select Department</option>
                                <option>HR</option>
                                <option>Sales</option>
                                <option>Production</option>
                                <option>Admin</option>
                                <option>IT</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Designation</label>
                            <select name="designation" class="form-control">
                                <option disabled selected>Select Designation</option>
                                <option>Manager</option>
                                <option>Team Lead</option>
                                <option>Staff</option>
                                <option>Intern</option>
                                <option>Ground Staff</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Date of Joining</label>
                            <input type="date" class="form-control" name="date_of_join">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Salary</label>
                            <input type="number" class="form-control" name="salary">
                        </div>

                    </div>

                    <hr>

                    <h5>Documents</h5>

                    <div class="row">

                        <div class="col-md-6 form-group">
                            <label>Resume</label>
                            <input type="file" class="form-control" name="resume">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Certificates</label>
                            <input type="file" class="form-control" name="certificates[]" multiple>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>ID Proof</label>
                            <input type="file" class="form-control" name="id_proof">
                        </div>

                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success">Save Employee</button>
                    <a href="{{ route('Employee') }}" class="btn btn-secondary">Cancel</a>
                </div>

            </div>
        </div>

    </div>

</form>

@stop