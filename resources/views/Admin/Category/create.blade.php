@extends('adminlte::page')

@section('title', 'Category')

@section('content_header')
    <h1>Category</h1>
@stop

@section('content')

<form action="{{ route('Category.store') }}" method="POST">
    @csrf 

    <div class="card card-primary">
        <div class="card-body"> 
 
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" class="form-control"> 
                
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div> 
 
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div> 
 
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Save Category
            </button> 

            <a href="{{ route('Category') }}" class="btn btn-secondary">
                Cancel
            </a> 

        </div>
    </div> 

</form>

@stop