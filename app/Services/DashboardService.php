<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\DeliveryChallan;
use App\Models\Dispatch;
use App\Models\StockIn;
use App\Models\Purchase;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use App\Models\DispatchItem;
use Carbon\Carbon;

class DashboardService
    {
    
            public function getSummary(): array
            {
        
                return [
        
                    // =========================
                    // KPI CARDS
                    // =========================
        
                    'totalProducts' => Product::count(),
        
        
                    'totalCustomers' => Customer::count(),
        
        
                    'totalVendors' => Vendor::count(),
        
        
                    'totalOrders' => DeliveryChallan::count(),
        
        
                    'totalInvoices' => Invoice::count(),
        
        
                    'totalStock' => StockIn::sum('qty'),
        
                    'lowStock' => Product::where('status', 'active')
                    ->where('stock_quantity', '<=', 50)
                    ->count(),
        
         
        
                    'monthlyPurchase' => PurchaseReceiveItem::whereHas(
                        'purchaseReceive',
                        function ($query) {
                            $query->whereMonth(
                                'receive_date',
                                now()->month
                            )
                            ->whereYear(
                                'receive_date',
                                now()->year
                            );
                        }
                    )
                    ->selectRaw(
                        'SUM(received_qty * price) as total'
                    )
                    ->value('total') ?? 0,
            
            
                    'pendingDispatch' => DeliveryChallan::whereIn('status', [
                        'approved',
                        'partially_dispatched'
                    ])
                    ->whereHas('items', function ($query) {
                
                        $query->whereRaw("
                            qty >
                            (
                                SELECT COALESCE(SUM(dispatch_items.quantity),0)
                                FROM dispatch_items
                                WHERE dispatch_items.delivery_challan_item_id 
                                = delivery_challan_items.id
                            )
                        ");
                
                    })
                    ->count(), 
                    // =========================
                    // TABLE DATA
                    // ========================= 
        
                    'lowStockProducts' => $this->lowStockProducts(), 
        
                    'recentDeliveries' => DeliveryChallan::with('customer')
                        ->latest()
                        ->limit(5)
                        ->get(),  
                        'recentInvoices' => Invoice::with('customer')
                        ->latest()
                        ->limit(5)
                        ->get(),  
                    'topProducts' => $this->topProducts(),  
                    // =========================
                    // CHART DATA
                    // ========================= 
                    'purchaseDispatchChart' => $this->purchaseDispatchChart(), 
                    ]; 
                }   
               /**
             * Low Stock Products
             */
            private function lowStockProducts()
                {
                    return Product::select(
                            'id',
                            'name',
                            'stock_quantity'
                        )
                        ->where('status', 'active')
                        ->where('stock_quantity', '<=', 50)
                        ->orderBy('stock_quantity', 'asc')
                        ->limit(5)
                        ->get();
                } 
             
              /**
             * Top Moving Products
             * Based on dispatched quantity
             */
            private function topProducts()
                {
                    return Product::select(
                            'products.id',
                            'products.name',
                            'products.sku',
                            'products.stock_quantity'
                        )
                        ->withSum('dispatchItems', 'quantity')
                        ->orderByDesc('dispatch_items_sum_quantity')
                        ->limit(5)
                        ->get();
                }  
            /**
             * Purchase vs Dispatch Chart 
             */
            private function purchaseDispatchChart()
                    {
                        $labels = [];
                        $purchaseData = [];
                        $dispatchData = [];
                        $revenueData = []; 
                        $year = now()->year; 
                        for ($month = 1; $month <= 12; $month++) { 
                            $labels[] = Carbon::create($year, $month, 1)->format('M');
                    
                            /*
                            |--------------------------------------------------------------------------
                            | Purchase Amount
                            |--------------------------------------------------------------------------
                            */
                    
                            $purchaseAmount = PurchaseReceiveItem::whereHas(
                                'purchaseReceive',
                                function ($q) use ($year, $month) {
                                    $q->whereYear('receive_date', $year)
                                      ->whereMonth('receive_date', $month);
                                }
                            )
                            ->selectRaw('SUM(received_qty * price) as total')
                            ->value('total') ?? 0;
                    
                    
                            /*
                            |--------------------------------------------------------------------------
                            | Dispatch Amount (Sales Value)
                            |--------------------------------------------------------------------------
                            */
                    
                            $dispatchAmount = DispatchItem::whereHas(
                                'dispatch',
                                function ($q) use ($year, $month) {
                                    $q->whereYear('created_at', $year)
                                      ->whereMonth('created_at', $month);
                                }
                            )
                            ->sum('amount');
                    
                    
                            /*
                            |--------------------------------------------------------------------------
                            | Revenue
                            |--------------------------------------------------------------------------
                            | Currently Revenue = Sales Value
                            | Later you can replace this with Invoice::sum('total')
                            |--------------------------------------------------------------------------
                            */
                    
                            $revenue = $dispatchAmount;
                    
                    
                            $purchaseData[] = $purchaseAmount;
                            $dispatchData[] = $dispatchAmount;
                            $revenueData[]  = $revenue;
                        }
                    
                        return [
                    
                            'labels'   => $labels,
                    
                            'purchase' => $purchaseData,
                    
                            'dispatch' => $dispatchData,
                    
                            'revenue'  => $revenueData,
                        ];
                    }
    }