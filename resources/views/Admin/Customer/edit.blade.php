@extends('adminlte::page')

@section('title', 'Edit Customer')

@section('content_header')
    <h1>Edit Customer</h1>
@stop

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('Customer.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
 
                <div class="col-md-6">
                    <label>Name *</label>
                    <input type="text" 
                           name="name" 
                           class="form-control" 
                           value="{{ $customer->name }}" 
                           required>
                </div> 
                <div class="col-md-6">
                    <label>Company Name</label>
                    <input type="text" 
                           name="company_name" 
                           class="form-control"
                           value="{{ $customer->company_name }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Mobile</label>
                    <input type="text" 
                           name="mobile" 
                           class="form-control"
                           value="{{ $customer->mobile }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Alternate Mobile</label>
                    <input type="text" 
                           name="alternate_mobile" 
                           class="form-control"
                           value="{{ $customer->alternate_mobile }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Email</label>
                    <input type="email" 
                           name="email" 
                           class="form-control"
                           value="{{ $customer->email }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Billing Address</label>
                    <textarea name="billing_address" class="form-control">{{ $customer->billing_address }}</textarea>
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Shipping Address</label>
                    <textarea name="shipping_address" class="form-control">{{ $customer->shipping_address }}</textarea>
                </div>  
                <div class="col-md-4 mt-2">
                    <label>City</label>
                    <input type="text" 
                           name="city" 
                           class="form-control"
                           value="{{ $customer->city }}">
                </div> 
                <div class="col-md-4 mt-2">
                    <label>State</label>
                    <input type="text" 
                           name="state" 
                           class="form-control"
                           value="{{ $customer->state }}">
                </div> 
                <div class="col-md-4 mt-2">
                    <label>Pincode</label>
                    <input type="text" 
                           name="pincode" 
                           class="form-control"
                           value="{{ $customer->pincode }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Country</label>
                    <input type="text" 
                           name="country" 
                           class="form-control"
                           value="{{ $customer->country }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>GST Number</label>
                    <input type="text" 
                           name="gst_number" 
                           class="form-control"
                           value="{{ $customer->gst_number }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>PAN Number</label>
                    <input type="text" 
                           name="pan_number" 
                           class="form-control"
                           value="{{ $customer->pan_number }}">
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Customer Type</label>
                    <select name="customer_type" class="form-control">
                        <option value="business" {{ $customer->customer_type == 'business' ? 'selected' : '' }}>
                            Business
                        </option>
                        <option value="individual" {{ $customer->customer_type == 'individual' ? 'selected' : '' }}>
                            Individual
                        </option>
                    </select>
                </div> 
                <div class="col-md-6 mt-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div> 
                <div class="col-md-12 mt-2">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control">{{ $customer->notes }}</textarea>
                </div> 
            </div> 
            <br> 
            <button class="btn btn-primary">
                Update Customer
            </button> 
        </form> 
    </div> 
</div> 
@stop