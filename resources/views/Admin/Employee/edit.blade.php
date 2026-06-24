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
                            :value="old('name', $employee->user->name ?? '')"
                        /> 
                        </div>
                       <div class="col-md-6"> 
                        <x-input
                            label="Email"
                            name="email"
                            type="email"
                            :value="old('email', $employee->user->email ?? '')"
                        /> 
                        </div>
                       <div class="col-md-6"> 
                        <x-input
                            label="Contact No"
                            name="contact_no"
                            :value="old('contact_no', $employee->contact_no)"
                        /> 
                        </div>
                       <div class="col-md-12"> 
                        <x-textarea
                            label="Address"
                            name="address"
                            :value="old('address', $employee->address)"
                        /> 
                        </div>
                       <div class="col-md-6 form-group">
                            <label>Role</label> 
                            <select class="form-control" name="role" required> 
                                <option value="">Select Role</option> 
                                @foreach($roles as $role) 
                                    <option value="{{ $role->name }}"
                                        {{ $employee->user->hasRole($role->name) ? 'selected' : '' }}>

                                        {{ $role->name }}

                                    </option>  
                                @endforeach 
                            </select>
                        </div>
                        <div class="col-md-6"> 
                        <x-input
                            label="Password"
                            name="password"
                            type="password"
                        /> 
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
                            :selected="$employee->department"
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
                            :selected="$employee->designation"
                        /> 
                        </div>
                       <div class="col-md-6"> 
                        <x-input
                            label="Date of Joining"
                            name="date_of_join"
                            type="date"
                            :value="old('date_of_join', $employee->date_of_join)"
                        /> 
                    </div>
                       <div class="col-md-6"> 
                        <x-input
                            label="Salary"
                            name="salary"
                            type="number"
                            :value="old('salary', $employee->salary)"
                        /> 
                    </div>
                    <hr> 
                    <h5>Documents</h5>  
                    <div class="row"> 
                       <div class="col-md-6"> 
                        <x-file-input
                            label="Resume"
                            name="resume"
                        /> 
                        @if($employee->resume) 
                            <a href="{{ asset('storage/'.$employee->resume) }}"
                            target="_blank"> 
                            View 
                            </a> 
                        @endif 
                    </div>

                       <div class="col-md-6"> 
                        <x-file-input
                            label="Certificates"
                            name="certificates[]"
                            :multiple="true"
                        /> 
                        @if($employee->certificates) 
                            @foreach(json_decode($employee->certificates) as $cert) 
                                <a href="{{ asset('storage/'.$cert) }}"
                                target="_blank"
                                class="d-block"> 
                                View 
                                </a> 
                            @endforeach

                        @endif 
                    </div>
                       <div class="col-md-6"> 
                        <x-file-input
                            label="ID Proof"
                            name="id_proof"
                        /> 
                        @if($employee->id_proof) 
                            <a href="{{ asset('storage/'.$employee->id_proof) }}"
                            target="_blank"> 
                            View 
                            </a> 
                        @endif

                    </div>
                    </div> 
                </div>  
                <div class="card-footer">
                   <x-button
                    type="submit"
                    color="success"
                    icon="fas fa-save"> 
                    Update Employee 
                </x-button>
                </div> 
            </div> 
        </div> 
    </div> 
</form>  
@stop