@extends('admin.layout.app', ['activePage' => 'vendor', 'titlePage' => __('Vendor')])

@section('content')
<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <h1>Vendor</h1>
        </div>
    </section>

    <!-- Main -->
    <section class="content">

        <form action="{{ route('Vendor.store') }}" method="POST">
            @csrf

            <div class="card card-primary">
                <div class="card-body">

                    <!-- Vendor Name -->
                    <div class="form-group">
                        <label>Vendor Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>

                    <!-- Company -->
                    <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="form-control">
                    </div>

                    <!-- GST -->
                    <div class="form-group">
                        <label>GST Number</label>
                        <input type="text" name="gst_number" class="form-control">
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <button type="submit" id="saveBtn" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Vendor
                        </button>

                    <a href="{{ route('Vendor.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </div>
            </div>

        </form>

    </section>
</div>
@endsection
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("form");
    const btn = document.getElementById("saveBtn");

    form.addEventListener("submit", function () {

        btn.disabled = true;
        btn.innerHTML = "Saving...";

    });

});
</script>