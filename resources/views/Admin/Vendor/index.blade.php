@extends('admin.layout.table', ['activePage' => 'category', 'titlePage' => __('Category')])

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
        <div class="container-fluid">

            <div class="card">

                <div class="card-header position-relative">
                    <h3 class="card-title">Vendor Details</h3>

                    <a href="{{ route('Vendor.create') }}" 
                       class="btn btn-primary position-absolute" 
                       style="right: 15px; top: 50%; transform: translateY(-50%);">
                        Add Vendor
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
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                        </thead> 
                       <tbody>
    @forelse($vendors as $vendor)
        <tr>
            <td>{{ $vendor->name }}</td>
            <td>{{ $vendor->phone }}</td>
            <td>{{ $vendor->email }}</td>
            <td>
                <span class="badge {{ $vendor->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($vendor->status ?? 'active') }}
                </span>
            </td>
            <td>
               <a href="{{ route('Vendor.edit', $vendor) }}" class="btn btn-sm btn-info">Edit</a>

                <form action="{{ route('Vendor.destroy', $vendor->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center text-danger">
                No Vendors Found
            </td>
        </tr>
    @endforelse
</tbody>
                    </table> 
                </div> 
            </div> 
        </div>
    </section>

</div>
@endsection