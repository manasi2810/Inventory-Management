@extends('adminlte::page')

@section('title', 'Edit Vendor')

@section('content_header')
<div class="row mb-2">

    <div class="col-sm-6">
        <h1>Vendor</h1>
    </div> 
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">Vendor</li>
        </ol>
    </div> 
</div>
@stop

@section('content')

<div class="card"> 
    <div class="card-header">
        <h3 class="card-title">Update Vendor</h3>
    </div> 
    <form action="{{ route('Vendors.update', $vendor->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card-body"> 
            {{-- ================= BASIC INFO ================= --}}
            <div class="row">  
                <div class="col-md-6"> 
                    <x-input label="Vendor Name *"
                        name="name"
                        :value="old('name', $vendor->name)" /> 
                    <x-input label="Email"
                        name="email"
                        type="email"
                        :value="old('email', $vendor->email)" /> 
                    <x-input label="Contact"
                        name="contact"
                        :value="old('contact', $vendor->contact)" /> 
                    <x-input label="Company Name"
                        name="company_name"
                        :value="old('company_name', $vendor->company_name)" /> 
                </div>

                <div class="col-md-6"> 
                    <x-input label="GST Number"
                        name="gst_number"
                        :value="old('gst_number', $vendor->gst_number)" />  
                    <x-input label="PAN Number"
                        name="pan_number"
                        :value="old('pan_number', $vendor->pan_number)" /> 
                    <x-input label="City"
                        name="city"
                        :value="old('city', $vendor->city)" /> 
                    <x-input label="State"
                        name="state"
                        :value="old('state', $vendor->state)" /> 
                </div> 
            </div> 
            {{-- ================= ADDRESS ================= --}}
            <div class="form-group mt-3">
                <x-textarea label="Address"
                    name="address"
                    :value="old('address', $vendor->address)" />
            </div> 
            <hr> 
            {{-- ================= FINANCIAL INFO ================= --}}
            <div class="row"> 
                <div class="col-md-4">
                    <x-input label="Credit Limit"
                        name="credit_limit"
                        type="number"
                        :value="old('credit_limit', $vendor->credit_limit)" />
                </div> 
                <div class="col-md-4">
                    <x-input label="Opening Balance"
                        name="opening_balance"
                        type="number"
                        :value="old('opening_balance', $vendor->opening_balance)" />
                </div>  
                <div class="col-md-4">
                    <label>Opening Balance Type</label>
                    <select name="opening_balance_type" class="form-control">
                        <option value="CR" {{ $vendor->opening_balance_type == 'CR' ? 'selected' : '' }}>
                            Credit (Payable)
                        </option>
                        <option value="DR" {{ $vendor->opening_balance_type == 'DR' ? 'selected' : '' }}>
                            Debit (Advance)
                        </option>
                    </select>
                </div> 
            </div> 
            <div class="row mt-2"> 
                <div class="col-md-12">
                    <x-input label="Payment Days (Net 30/60)"
                        name="payment_days"
                        type="number"
                        :value="old('payment_days', $vendor->payment_days)" />
                </div> 
            </div> 
            <hr> 
            {{-- ================= BANK DETAILS ================= --}}
            <div class="row">  
                <div class="col-md-4">
                    <x-input label="Bank Name"
                        name="bank_name"
                        :value="old('bank_name', $vendor->bank_name)" />
                </div> 
                <div class="col-md-4">
                    <x-input label="Account Number"
                        name="bank_account_no"
                        :value="old('bank_account_no', $vendor->bank_account_no)" />
                </div> 
                <div class="col-md-4">
                    <x-input label="IFSC Code"
                        name="ifsc_code"
                        :value="old('ifsc_code', $vendor->ifsc_code)" />
                </div> 
            </div> 
            <hr> 
            {{-- ================= SYSTEM INFO ================= --}}
            <div class="row"> 
                <div class="col-md-6"> 
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ $vendor->status == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ $vendor->status == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                        <option value="blocked" {{ $vendor->status == 'blocked' ? 'selected' : '' }}>
                            Blocked
                        </option>
                    </select> 
                </div> 
                <div class="col-md-6">
                    <x-textarea label="Remarks"
                        name="remarks"
                        :value="old('remarks', $vendor->remarks)" />
                </div> 
            </div> 
        </div> 
        {{-- ================= FOOTER ================= --}}
        <div class="card-footer text-right"> 
            <a href="{{ route('Vendors') }}" class="btn btn-secondary">
                Cancel
            </a> 
            <button type="submit" class="btn btn-success" id="saveBtn">
                <i class="fas fa-save"></i> Update Vendor
            </button> 
        </div> 
    </form> 
</div> 
@stop

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const btn = document.getElementById("saveBtn");

    form.addEventListener("submit", function () {
        btn.disabled = true;
        btn.innerHTML = "Updating...";
    });

});
</script>
@endpush