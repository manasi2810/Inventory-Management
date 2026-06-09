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
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
    <div class="row"> 
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Basic Info</h3>
                </div> 
                <div class="card-body"> 
                    <div class="row"> 
                      <div class="col-md-6">
            <x-input
                label="Name"
                name="name"
                type="text"
            />
                        </div>

                        <div class="col-md-6">
                            <x-input
                                label="Email"
                                name="email"
                                type="email"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-input
                                label="Contact No"
                                name="contact_no"
                                type="text"
                            />
                        </div>

                        <div class="col-md-12">
                            <x-textarea
                                label="Address"
                                name="address"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-input
                                label="Password"
                                name="password"
                                type="password"
                            />
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
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Job & Documents</h3>
                </div> 
                <div class="card-body"> 
                    <div class="row"> 
                        <div class="col-md-6">
                        <x-select
                            label="Department"
                            name="department"
                            :options="[
                                'HR' => 'HR',
                                'Sales' => 'Sales',
                                'Production' => 'Production',
                                'Admin' => 'Admin',
                                'IT' => 'IT'
                            ]"
                        />
                    </div> 
                       <div class="col-md-6">

                            <x-select
                                label="Designation"
                                name="designation"
                                :options="[
                                    'Manager' => 'Manager',
                                    'Team Lead' => 'Team Lead',
                                    'Staff' => 'Staff',
                                    'Intern' => 'Intern',
                                    'Ground Staff' => 'Ground Staff'
                                ]"
                            />

                        </div> 
                        <div class="col-md-6 form-group">
                               <x-input
                                label="Date of Joining"
                                name="date_of_join"
                                type="date"
                            />
                        </div> 
                        <div class="col-md-6 form-group">
                              <x-input
                                label="Salary"
                                name="salary"
                                type="number"
                            />
                        </div> 
                    </div> 
                    <hr>  
                    <h5>Documents</h5> 
                    <div class="row"> 
                        <div class="col-md-6 ">
                            <x-file-input
                                label="Resume"
                                name="resume"
                            />
                        </div> 
                        <div class="col-md-6 form-group">
                           <x-file-input
                            label="Certificates"
                            name="certificates[]"
                            :multiple="true"
                        />
                        </div> 
                        <div class="col-md-6 form-group">
                           <x-file-input
                            label="ID Proof"
                            name="id_proof"
                        />
                        </div> 
                    </div> 
                </div> 
                <div class="card-footer">
                    <button type="submit" class="btn btn-success">
    Save Employee
</button>
                </div> 
            </div>
        </div> 
    </div> 
</form>

@stop