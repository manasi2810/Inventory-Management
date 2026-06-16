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
                     <x-input
                        label="Name *"
                        name="name"
                        :value="$customer->name"
                    />
                </div> 
                <div class="col-md-6">
                     <x-input
                        label="Company Name"
                        name="company_name"
                        :value="$customer->company_name"
                    />
                </div> 
                <div class="col-md-6 mt-2">
                    <x-input
                        label="Customer Code"
                        name="customer_code"
                        :value="$customer->customer_code"
                        readonly
                    />
                </div>
                <div class="col-md-6 mt-2">
                     <x-input
                        label="Mobile"
                        name="mobile"
                        :value="$customer->mobile"
                    />
                </div> 
                <div class="col-md-6 mt-2">
                    <x-input
                        label="Alternate Mobile"
                        name="alternate_mobile"
                        :value="$customer->alternate_mobile"
                    />
                </div> 
                <div class="col-md-6 mt-2">
                    <x-input
                        label="Email"
                        name="email"
                        type="email"
                        :value="$customer->email"
                    />  
                </div> 
                <div class="col-md-6 mt-2">
                    <x-textarea
                        label="Billing Address"
                        name="billing_address"
                        :value="$customer->billing_address"
                    />
                </div> 
                <div class="col-md-6 mt-2">
                    <x-textarea
                        label="Shipping Address"
                        name="shipping_address"
                        :value="$customer->shipping_address"
                    />
                </div>
                <div class="col-md-4 mt-2">
                    <x-input
                        label="City"
                        name="city"
                        :value="$customer->city"
                    />
                </div> 
                <div class="col-md-4 mt-2">
                    <x-input
                        label="State"
                        name="state"
                        :value="$customer->state"
                    /> 
                </div>
                 <div class="col-md-4 mt-2">
                    <x-input
                        label="Pincode"
                        name="pincode"
                        :value="$customer->pincode"
                    /> 
                </div>  
                <div class="col-md-4 mt-2">
                    <x-input
                        label="Country"
                        name="country"
                        :value="$customer->country"
                    /> 
                </div>   
                <div class="col-md-6 mt-2">
                    <x-input
                        label="GST Number"
                        name="gst_number"
                        :value="$customer->gst_number"
                    /> 
                </div> 
                <div class="col-md-6 mt-2">
                    <x-input
                        label="PAN Number"
                        name="pan_number"
                        :value="$customer->pan_number"
                    /> 
                </div>  
                <div class="col-md-6 mt-2">
                    <x-input
                        label="Credit Limit"
                        name="credit_limit"
                        type="number"
                        step="0.01"
                        :value="$customer->credit_limit"
                    />
                </div> 
                <div class="col-md-6 mt-2">
                    <x-input
                        label="Opening Balance"
                        name="opening_balance"
                        type="number"
                        step="0.01"
                        :value="$customer->opening_balance"
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
                    :selected="$customer->customer_type"
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
                            :selected="$customer->status"
                        />
                    </div> 
                    <div class="col-md-12 mt-2">
                        <x-textarea
                            label="Notes"
                            name="notes"
                            :value="$customer->notes"
                        />
                    </div>
                </div> 
                <br> 
                    <a href="{{ route('Customer') }}"
                        class="btn btn-secondary">
                        Cancel
                    </a> 
                <x-button
                type="submit"
                color="primary"
                icon="fas fa-save">
                Update Customer
            </x-button>
        </form> 
    </div> 
</div> 
@stop