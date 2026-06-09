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
 
            <x-input
                label="Category Name"
                name="name"
            />
 
                <x-textarea
            label="Description"
            name="description"
        /> 

            <x-button
            type="submit"
            color="success"
            icon="fas fa-save">

            Save Category

        </x-button>

            <a href="{{ route('Category') }}" class="btn btn-secondary">
                Cancel
            </a> 

        </div>
    </div> 

</form>

@stop