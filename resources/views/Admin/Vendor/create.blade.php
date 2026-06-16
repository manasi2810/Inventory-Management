@extends('adminlte::page')

@section('title', 'Vendor Create')

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
        <h3 class="card-title">Add Vendor</h3>
    </div>

    <form action="{{ route('Vendors.store') }}" method="POST" id="vendorForm">
        @csrf

        <div class="card-body">

            {{-- ================= BASIC INFO ================= --}}
            <div class="row">

                <div class="col-md-6">
                    <x-input label="Vendor Name *" name="name" />
                    <x-input label="Email" name="email" type="email" />
                    <x-input label="Contact" name="contact" />
                    <x-input label="Company Name" name="company_name" />
                </div>

                <div class="col-md-6">
                    <x-input label="GST Number" name="gst_number" />
                    <x-input label="PAN Number" name="pan_number" />
                    <x-input label="City" name="city" />
                    <x-input label="State" name="state" />
                </div>

            </div>

            {{-- ================= ADDRESS ================= --}}
            <div class="form-group mt-3">
                <x-textarea label="Address" name="address" />
            </div>

            <hr>

            {{-- ================= FINANCIAL INFO ================= --}}
            <div class="row">

                <div class="col-md-4">
                    <x-input label="Credit Limit" name="credit_limit" type="number" />
                </div>

                <div class="col-md-4">
                    <x-input label="Opening Balance" name="opening_balance" type="number" />
                </div>

                <div class="col-md-4">
                    <label>Opening Balance Type</label>
                    <select name="opening_balance_type" class="form-control">
                        <option value="CR">Credit (Payable)</option>
                        <option value="DR">Debit (Advance)</option>
                    </select>
                </div>

            </div>

            <div class="row mt-2">

                <div class="col-md-12">
                    <x-input label="Payment Days (Net 30/60)" name="payment_days" type="number" />
                </div>

            </div>

            <hr>

            {{-- ================= BANK DETAILS ================= --}}
            <div class="row">

                <div class="col-md-4">
                    <x-input label="Bank Name" name="bank_name" />
                </div>

                <div class="col-md-4">
                    <x-input label="Account Number" name="bank_account_no" />
                </div>

                <div class="col-md-4">
                    <x-input label="IFSC Code" name="ifsc_code" />
                </div>

            </div>

            <hr>

            {{-- ================= SYSTEM INFO ================= --}}
            <div class="row">

                <div class="col-md-6">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <x-textarea label="Remarks" name="remarks" />
                </div>

            </div>

        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="card-footer text-right">

            <a href="{{ route('Vendors') }}" class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit" class="btn btn-success" id="saveBtn">
                <i class="fas fa-save"></i> Save Vendor
            </button>

        </div>

    </form>

</div>

@stop

@push('js')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("vendorForm");
    const btn = document.getElementById("saveBtn");

    form.addEventListener("submit", function () {
        btn.disabled = true;
        btn.innerHTML = "Saving...";
    });

});
</script>
@endpush