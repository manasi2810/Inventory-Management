@extends('adminlte::page')

@section('title', 'Category')

@section('content_header')
    <h1>Category</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12"> 
        <div class="card"> 
            <div class="card-header">
            <div class="d-flex justify-content-between align-items-center"> 
            <h3 class="card-title mb-0">Category Details</h3> 
            <a href="{{ route('Category.create') }}" class="btn btn-primary">
                + Add Category
            </a> 
        </div>
    </div>
            <div class="card-body">  
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif 
                <table class="table table-bordered table-striped" id="categoryTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead> 
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td> 
                            <td>
                                <a href="{{ route('Category.edit', $category->id) }}" 
                                   class="btn btn-sm btn-info">
                                    Edit
                                </a> 
                                <form action="{{ route('Category.destroy', $category->id) }}" 
                                      method="POST" 
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" 
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody> 
                </table> 
            </div> 
        </div> 
    </div>
</div>  
@stop

@push('js')
<script>
$(document).ready(function () {
    $('#categoryTable').DataTable({
        responsive: true,
        autoWidth: false,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        dom: 'Bfrtip',
        buttons: ["copy", "csv", "excel", "pdf", "print"]
    });
});
</script>
@endpush