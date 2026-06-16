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
                    <x-input
                        label="Name *"
                        name="name"
                    /> 
                </div>
                <div class="col-md-6">
                     <x-input
                        label="Company Name"
                        name="company_name"
                    />
                </div> 
               <div class="col-md-6 mt-2">
                    <x-input
                        label="Mobile"
                        name="mobile"
                        type="tel"
                    />
                </div>
                <div class="col-md-6 mt-2">
                    <x-input
                        label="Alternate Mobile"
                        name="alternate_mobile"
                    />
                </div>
                <div class="col-md-6 mt-2">
                       <x-input
                        label="Email"
                        name="email"
                        type="email"
                    />
                </div> 
                <div class="col-md-6 mt-2">
                     <x-textarea
                    label="Billing Address"
                    name="billing_address"
                />
                </div> 
                <div class="col-md-6 mt-2">
                        <x-textarea
                    label="Shipping Address"
                    name="shipping_address"
                />
                </div> 
                <div class="col-md-6 mt-2">
                    <x-input
                        label="City"
                        name="city"
                        />
                </div> 
                <div class="col-md-4 mt-2">
                    <x-input
                    label="State"
                    name="state"
                />
                </div> 
                <div class="col-md-4 mt-2">
                   <x-input
                    label="Pincode"
                    name="pincode"
                />
                </div> 
                <div class="col-md-4 mt-2">
                <x-input
                    label="Country"
                    name="country"
                    value="India"
                />
            </div>
                <div class="col-md-6 mt-2">
                     <x-input
                    label="GST Number"
                    name="gst_number"
                />  
                </div> 
                <div class="col-md-6 mt-2">
                     <x-input
                    label="PAN Number"
                    name="pan_number"
                />   
                </div> 
                <div class="col-md-6 mt-2">
                    <x-input
                        label="Credit Limit"
                        name="credit_limit"
                        type="number"
                        step="0.01"
                        value="0"
                    />
                </div>

                <div class="col-md-6 mt-2">
                    <x-input
                        label="Opening Balance"
                        name="opening_balance"
                        type="number"
                        step="0.01"
                        value="0"
                    />
                </div>
                <div class="col-md-6 mt-2">
                      <x-select
                        label="Customer Type"
                        name="customer_type"
                        :options="[
                            'business' => 'Business',
                            'individual' => 'Individual'
                        ]"
                    />
                </div> 
                <div class="col-md-6 mt-2">
                       <x-select
                        label="Status"
                        name="status"
                        :options="[
                            '1' => 'Active',
                            '0' => 'Inactive'
                        ]"
                    />
                </div> 
                <div class="col-md-12 mt-2">
                       <x-textarea
                        label="Notes"
                        name="notes"
                        />
                    </div> 
                </div> 
                <br> 
               <div class="mt-3"> 
                <a href="{{ route('Customer') }}"
                class="btn btn-secondary">
                    Cancel
                </a> 
                <x-button
                    type="submit"
                    color="primary"
                    icon="fas fa-save">
                    Save Customer
                </x-button> 
            </div>
        </form> 
    </div> 
</div>

@stop