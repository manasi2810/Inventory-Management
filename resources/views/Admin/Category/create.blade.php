@extends('admin.layout.app', ['activePage' => 'category', 'titlePage' => __('Category')])

@section('content')
<div class="content-wrapper"> 
    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Category</h1>
                </div>
            </div>
        </div>
    </section> 
    <!-- Main -->
    <section class="content"> 
        <form action="{{ route('Category.store') }}" method="POST">
            @csrf 
            <div class="card card-primary">
                <div class="card-body"> 
                    <!-- Category Name -->
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" class="form-control"> 
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> 
                    <!-- Description -->
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div> 
                    <!-- Buttons -->
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Category
                    </button> 
                    <a href="{{ route('Category') }}" class="btn btn-secondary">
                        Cancel
                    </a> 
                </div>
            </div> 
        </form> 
    </section> 
</div>
@endsection