@extends('adminlte::page')

@section('title', 'Dashboard')

@section('plugins.Chartjs', true)

@section('content_header')

        <div class="d-flex justify-content-between align-items-center">
            <h1>Dashboard</h1>
            <span class="text-muted">
                Welcome, {{ auth()->user()->name }}
            </span>
        </div> 
        @stop 
        @section('content')
        
        
        {{-- ============================= --}}
        {{-- KPI CARDS - ROW 1 --}}
        {{-- ============================= --}} 
        
        <div class="row">
            {{-- PRODUCTS --}}
            @can('product.view')
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="{{ $totalProducts }}"
                    text="Total Products"
                    icon="fas fa-box"
                    theme="info"
                    url="{{ route('Product') }}"
                    url-text="View Products"/>
            </div>
            @endcan
            {{-- CUSTOMERS --}}
            @can('customer.view')
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="{{ $totalCustomers }}"
                    text="Customers"
                    icon="fas fa-users"
                    theme="primary"
                    url="{{ route('Customer') }}"
                    url-text="View Customers"/>
            </div>
            @endcan
            {{-- DELIVERY --}}
            @can('delivery.view')
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="{{ $totalOrders }}"
                    text="Delivery Challans"
                    icon="fas fa-truck"
                    theme="success"
                    url="{{ route('Delivery_challan') }}"
                    url-text="View"/>
            </div>
            @endcan
            {{-- STOCK --}}
            @can('stock.view')
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="{{ $totalStock }}"
                    text="Current Inventory"
                    icon="fas fa-warehouse"
                    theme="warning"
                    url="{{ route('reports.ledger') }}"
                    url-text="View Stock"/>
            </div>
            @endcan
        </div>
        
        
        {{-- ============================= --}}
        {{-- KPI CARDS - ROW 2 --}}
        {{-- ============================= --}}
        
        
        <div class="row">
            {{-- INVOICE --}}
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="{{ $totalInvoices ?? 0 }}"
                    text="Total Invoices"
                    icon="fas fa-file-invoice"
                    theme="success"
                    url="#"
                    url-text="View"/>
            </div>
            {{-- LOW STOCK --}}
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="{{ $lowStock }}"
                    text="Low Stock"
                    icon="fas fa-exclamation-triangle"
                    theme="danger"
                    url="{{ route('reports.stock') }}"
                    url-text="View Report"/>
            </div>
            {{-- PURCHASE --}}
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="₹ {{ number_format($monthlyPurchase ?? 0, 2) }}"
                    text="Purchase This Month"
                    icon="fas fa-shopping-cart"
                    theme="info"
                    url="#"
                    url-text="View"/>
            </div>
            {{-- DISPATCH --}}
            <div class="col-lg-3 col-md-6">
                <x-adminlte-small-box
                    title="{{ $pendingDispatch ?? 0 }}"
                    text="Pending Dispatch"
                    icon="fas fa-clock"
                    theme="warning"
                    url="#"
                    url-text="View"/>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- CHARTS --}}
        {{-- ============================= --}}
        
        <div class="row">
            <div class="col-md-6">
                <x-adminlte-card
                    title="Monthly Purchase vs Dispatch Value"
                    theme="primary"
                    icon="fas fa-chart-bar">
                    <canvas id="purchaseDispatchChart" height="130"></canvas>
                </x-adminlte-card>
            </div>
            <div class="col-md-6">
                <x-adminlte-card
                    title="Monthly Sales Revenue"
                    theme="success"
                    icon="fas fa-chart-line">
                    <canvas id="revenueChart" height="130"></canvas>
                </x-adminlte-card>
            </div>
        </div>
    
    
        {{-- ============================= --}}
        {{-- LOW STOCK PRODUCTS + TOP MOVING PRODUCTS --}}
        {{-- ============================= --}}
        
        
        <div class="row"> 
        <div class="col-md-6">
            <x-adminlte-card
                title="Low Stock Products"
                theme="danger"
                icon="fas fa-box-open"> 
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th width="120">Current Stock</th>
                            <th width="120">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                              <td class="text-center">

                                @if($product->stock_quantity==0)
                                
                                <span class="badge badge-danger">
                                Out of Stock
                                </span>
                                
                                @elseif($product->stock_quantity<=5)
                                
                                <span class="badge badge-warning">
                                {{ $product->stock_quantity }}
                                </span>
                                
                                @else
                                
                                <span class="badge badge-info">
                                {{ $product->stock_quantity }}
                                </span>
                                
                                @endif
                                
                                </td>
                                <td>
                                    @if($product->stock_quantity == 0)
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @elseif($product->stock_quantity <= 5)
                                        <span class="badge badge-warning">Critical</span>
                                    @else
                                        <span class="badge badge-info">Low Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-success">
                                    <i class="fas fa-check-circle"></i>
                                    No Low Stock Products
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table> 
            </x-adminlte-card>
            </div>
           <div class="col-md-6"> 
            <x-adminlte-card
                title="Top Moving Products"
                theme="info"
                icon="fas fa-chart-line"> 
            <table class="table table-hover"> 
            <thead class="thead-light">
                <tr>
                    <th width="45%">
                        Product
                    </th>
                    <th class="text-center">
                        SKU
                    </th>
                    <th class="text-center">
                        Sold Qty
                    </th>
                    <th class="text-center">
                        Current Stock
                    </th>
                </tr>
            </thead> 
            <tbody>
    
            @forelse($topProducts as $product)
    
            <tr>
                <td>
                    <strong>{{ $product->name }}</strong>
                </td>
                <td class="text-center">
                    {{ $product->sku }}
                </td>
                <td class="text-center">
                    <span class="badge badge-success">
                        {{ $product->dispatch_items_sum_quantity ?? 0 }}
                    </span>
                </td>
                <td class="text-center">
                    @if($product->stock_quantity <= 5)
                        <span class="badge badge-danger">
                            {{ $product->stock_quantity }}
                        </span>
                    @elseif($product->stock_quantity <= 20)
                        <span class="badge badge-warning">
                            {{ $product->stock_quantity }}
                        </span>
                    @else
                        <span class="badge badge-primary">
                            {{ $product->stock_quantity }}
                        </span>
                    @endif
                </td>
            </tr>
            @empty 
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No dispatch data available.
                </td>
            </tr> 
        @endforelse 
        </tbody>
    </table>
    </x-adminlte-card>
    </div>
    </div>  
    
    @stop  
    @section('js')
    <script> 
        // ===============================
        // Purchase vs Dispatch
        // ===============================
        new Chart(
            document.getElementById('purchaseDispatchChart'),
            {
                type: 'bar',
                data: {
                    labels: @json($purchaseDispatchChart['labels']),
                    datasets: [
                        {
                            label: 'Purchase Amount',
                            data: @json($purchaseDispatchChart['purchase']),
                            backgroundColor: '#36A2EB'
                        },
                        {
                            label: 'Dispatch Amount',
                            data: @json($purchaseDispatchChart['dispatch']),
                            backgroundColor: '#4BC0C0'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            }
        ); 
        // ===============================
        // Monthly Revenue Trend
        // =============================== 
        new Chart(
            document.getElementById('revenueChart'),
            {
                type: 'line',
                data: {
                    labels: @json($purchaseDispatchChart['labels']),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: @json($purchaseDispatchChart['revenue']),
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40,167,69,0.15)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            }
        );
    
    </script>
    @stop