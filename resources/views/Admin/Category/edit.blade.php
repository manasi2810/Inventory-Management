@extends('admin.layout.app', ['activePage' => 'category', 'titlePage' => __('Edit Category')])

@section('content')
<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Category</h1>
        </div>
    </section>

    <!-- Main -->
    <section class="content">

        <div class="container-fluid">

            <div class="card card-primary">
                <div class="card-body">

                    <form action="{{ route('Category.update', $category->id) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Category Name -->
    <div class="form-group">
        <label>Category Name</label>
        <input type="text" 
               name="name" 
               class="form-control"
               value="{{ old('name', $category->name) }}" 
               required>
        @error('name')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Description -->
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control">{{ old('description', $category->description) }}</textarea>
        @error('description')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!-- Buttons -->
    <button type="submit" class="btn btn-success">
        <i class="fas fa-save"></i> Update Category
    </button>
    <a href="{{ route('Category') }}" class="btn btn-secondary">Cancel</a>
</form>

                </div>
            </div>

        </div>

    </section>

</div>
@endsection