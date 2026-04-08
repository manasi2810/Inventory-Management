@extends('admin.layout.table', ['activePage' => 'category', 'titlePage' => __('Category')])

@section('content')
<div class="content-wrapper">

    <!-- Header -->
    <section class="content-header">
        <div class="container-fluid">
            <h1>Category</h1>
        </div>
    </section>

    <!-- Main -->
    <section class="content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header position-relative">
                    <h3 class="card-title">Category Details</h3>

                    <a href="{{ route('Category.create') }}" 
                       class="btn btn-primary position-absolute" 
                       style="right: 15px; top: 50%; transform: translateY(-50%);">
                        Add Category
                    </a>
                </div>

                <div class="card-body">

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($categories as $key => $category)
                            <tr>
                                <td>{{ $key + 1 }}</td>
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

                                        <button type="submit" class="btn btn-sm btn-danger">
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
    </section>

</div>
@endsection