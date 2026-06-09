@extends('adminlte::page')

@section('title', 'Edit Category')

@section('content_header')
    <h1>Edit Category</h1>
@stop

@section('content')

<div class="row">
    <div class="col-md-12">

        <div class="card card-primary">
            <div class="card-body">

                <form action="{{ route('Category.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
 
                        <x-input
                                label="Category Name"
                                name="name"
                                :value="old('name', $category->name)"
                        />
 
                        <x-textarea
                            label="Description"
                            name="description"
                            :value="old('description', $category->description)"
                        />
 
                        <x-button
                            type="submit"
                            color="success"
                            icon="fas fa-save"> 
                            Update Category 
                        </x-button>

                    <a href="{{ route('Category') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </form>

            </div>
        </div>

    </div>
</div>

@stop