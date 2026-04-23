@extends('adminlte::page')

@section('title', 'Create Customer')

@section('content_header')
    <h1>Create Customer</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('Customer.store') }}" method="POST">
            @csrf

            <div class="row">
 
                <div class="col-md-6">
                    <label>Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
 
                <div class="col-md-6">
                    <label>Company Name</label>
                    <input type="text" name="company_name" class="form-control">
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control">
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>Billing Address</label>
                    <textarea name="billing_address" class="form-control"></textarea>
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>Shipping Address</label>
                    <textarea name="shipping_address" class="form-control"></textarea>
                </div>
 
                <div class="col-md-4 mt-2">
                    <label>City</label>
                    <input type="text" name="city" class="form-control">
                </div>
 
                <div class="col-md-4 mt-2">
                    <label>State</label>
                    <input type="text" name="state" class="form-control">
                </div>
 
                <div class="col-md-4 mt-2">
                    <label>Pincode</label>
                    <input type="text" name="pincode" class="form-control">
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>GST Number</label>
                    <input type="text" name="gst_number" class="form-control">
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>PAN Number</label>
                    <input type="text" name="pan_number" class="form-control">
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>Customer Type</label>
                    <select name="customer_type" class="form-control">
                        <option value="business">Business</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>
 
                <div class="col-md-6 mt-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
 
                <div class="col-md-12 mt-2">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div> 
            </div> 
            <br>

            <button class="btn btn-primary">
                Save Customer
            </button>

        </form>

    </div>

</div>

@stop