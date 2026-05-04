@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

<div class="row">

    {{-- TOTAL PRODUCTS --}}
    @can('product.view')
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalProducts }}</h3>
                <p>Total Products</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a href="{{ route('Product') }}" class="small-box-footer">
                View More <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endcan

    {{-- TOTAL ORDERS --}}
    @can('delivery.view')
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $totalOrders }}</h3>
                <p>Orders</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('Delivery_challan') }}" class="small-box-footer">
                View More <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endcan

    {{-- STOCK --}}
    @can('stock.view')
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalStock }}</h3>
                <p>Total Stock</p>
            </div>
            <div class="icon">
                <i class="ion ion-cube"></i>
            </div>
             <a href="{{ route('reports.ledger') }}" class="small-box-footer">
                View More <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endcan

    {{-- LOW STOCK --}}
    @can('report.lowstock')
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $lowStock }}</h3>
                <p>Low Stock Alerts</p>
            </div>
            <div class="icon">
                <i class="ion ion-alert"></i>
            </div>
            <a href="{{ route('reports.stock') }}" class="small-box-footer">
                View Report <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endcan

</div>

@stop