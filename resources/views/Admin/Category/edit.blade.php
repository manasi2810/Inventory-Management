@extends('adminlte::page')

@section('title', 'Edit Category')

@section('content_header')
    <h1>Edit Category</h1>
@stop

@section('content')

<div class="row">
    <div class="col-md-6">

        <div class="card card-primary">
            <div class="card-body">

                <form action="{{ route('Category.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
 
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
 
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control">{{ old('description', $category->description) }}</textarea>

                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
 
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Category
                    </button>

                    <a href="{{ route('Category') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </form>

            </div>
        </div>

    </div>
</div>

@stop