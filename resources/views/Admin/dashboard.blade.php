@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
 

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3></h3>
                <p>Total Product</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
        </div>
    </div> 
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3></h3>
                <p>Orders</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div> 
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3></h3>
                <p>Stock </p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
        </div>
    </div> 
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3></h3>
                <p>Report</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
        </div>
    </div>
</div> 
@stop