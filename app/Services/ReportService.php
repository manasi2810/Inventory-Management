<?php

namespace App\Services;

use App\Models\DcReturnItem;
use App\Models\Product;
use App\Models\User;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockLedger;
use App\Models\PurchaseReceive;
use App\Models\DeliveryChallan;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /* ================= DC RETURN REPORT ================= */
    public function getDcReturnReport($filters)
        {
            return $this->baseQuery($filters)
                ->with([
                    'dcReturn.deliveryChallan.customer',
                    'product'
                ])
                ->latest()
                ->paginate(20);
        }
        
    public function getDcReturnReportForExport($filters)
        {
            return $this->baseQuery($filters)
                ->with([
                    'dcReturn.deliveryChallan.customer',
                    'product'
                ])
                ->latest()
                ->get();
        }

    /* ================= DC RETURN SUMMARY ================= */
    public function getDcReturnSummary($filters)
        {
            return $this->baseQuery($filters)
                ->selectRaw("
                    SUM(return_qty) as total,
                    SUM(CASE WHEN `condition`='good' THEN return_qty ELSE 0 END) as good,
                    SUM(CASE WHEN `condition`='damaged' THEN return_qty ELSE 0 END) as damaged,
                    SUM(CASE WHEN `condition`='scrap' THEN return_qty ELSE 0 END) as scrap
                ")
                ->first();
        }

    /* ================= BASE QUERY ================= */
    private function baseQuery($filters)
        {
            $query = DcReturnItem::query();

            if (!empty($filters['from_date'])) {
                $query->whereHas('dcReturn', function ($q) use ($filters) {
                    $q->whereDate('return_date', '>=', $filters['from_date']);
                });
            }

            if (!empty($filters['to_date'])) {
                $query->whereHas('dcReturn', function ($q) use ($filters) {
                    $q->whereDate('return_date', '<=', $filters['to_date']);
                });
            }

            if (!empty($filters['product_id'])) {
                $query->where('product_id', $filters['product_id']);
            }

            return $query;
        }

    /* ================= STOCK REPORT ================= */
    public function getStockReport($filters = [])
        {
                return Product::query()
                    ->select(
                        'id',
                        'name',
                        'sku',
                        'category_id',
                        'stock_quantity'
                    )
                    ->with('category')
                    ->get()
                    ->map(function ($product) use ($filters) {
            
                        /*
                        |--------------------------------------------------------------------------
                        | STOCK LEDGER QUERY
                        |--------------------------------------------------------------------------
                        */
            
                        $ledgerQuery = DB::table('stock_ledgers')
                            ->where('product_id', $product->id);
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | DATE FILTER
                        |--------------------------------------------------------------------------
                        */
            
                        if (!empty($filters['from_date'])) {
                            $ledgerQuery->whereDate(
                                'created_at',
                                '>=',
                                $filters['from_date']
                            );
                        }
            
                        if (!empty($filters['to_date'])) {
                            $ledgerQuery->whereDate(
                                'created_at',
                                '<=',
                                $filters['to_date']
                            );
                        }
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | STOCK IN
                        |--------------------------------------------------------------------------
                        */
            
                        $stockIn = (clone $ledgerQuery)
                            ->where('movement_type', 'IN')
                            ->sum('qty');
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | STOCK OUT
                        |--------------------------------------------------------------------------
                        */
            
                        $stockOut = (clone $ledgerQuery)
                            ->where('movement_type', 'OUT')
                            ->sum('qty');
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | RETURNS
                        |--------------------------------------------------------------------------
                        */
            
                        $returns = DB::table('dc_return_items')
                            ->where('product_id', $product->id)
                            ->when(
                                !empty($filters['from_date']),
                                function ($query) use ($filters) {
                                    $query->whereDate(
                                        'created_at',
                                        '>=',
                                        $filters['from_date']
                                    );
                                }
                            )
                            ->when(
                                !empty($filters['to_date']),
                                function ($query) use ($filters) {
                                    $query->whereDate(
                                        'created_at',
                                        '<=',
                                        $filters['to_date']
                                    );
                                }
                            )
                            ->sum('return_qty');
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | TOTAL IN / OUT
                        |--------------------------------------------------------------------------
                        */
            
                        $totalIn = $stockIn + $returns;
            
                        $totalOut = $stockOut;
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | FIRST MOVEMENT
                        |--------------------------------------------------------------------------
                        */
            
                        $firstMovement = (clone $ledgerQuery)
                            ->orderBy('created_at', 'asc')
                            ->first();
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | LAST MOVEMENT
                        |--------------------------------------------------------------------------
                        */
            
                        $lastMovement = (clone $ledgerQuery)
                            ->orderBy('created_at', 'desc')
                            ->first();
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | STATUS
                        |--------------------------------------------------------------------------
                        */
            
                        if ($product->stock_quantity <= 0) {
            
                            $status = 'Out of Stock';
            
                        } elseif ($product->stock_quantity <= 10) {
            
                            $status = 'Low Stock';
            
                        } else {
            
                            $status = 'In Stock';
                        }
            
            
                        /*
                        |--------------------------------------------------------------------------
                        | RETURN DATA
                        |--------------------------------------------------------------------------
                        */
            
                        return (object) [
            
                            'id' => $product->id,
            
                            'name' => $product->name,
            
                            'sku' => $product->sku,
            
                            'category' => $product->category->name ?? '-',
            
                            'purchase_qty' => $stockIn,
            
                            'return_qty' => $returns,
            
                            'total_in' => $totalIn,
            
                            'sale_qty' => $stockOut,
            
                            'total_out' => $totalOut,
            
                            /*
                             * Current actual stock
                             */
                            'closing_stock' => $product->stock_quantity,
            
                            'status' => $status,
            
                            /*
                             * First movement
                             */
                            'first_movement_date' =>
                                $firstMovement?->created_at,
            
                            /*
                             * Last movement
                             */
                            'last_movement_date' =>
                                $lastMovement?->created_at,
            
                            'last_movement_type' =>
                                $lastMovement?->movement_type,
            
                            'last_movement_qty' =>
                                $lastMovement?->qty,
            
                            'last_balance' =>
                                $lastMovement?->balance_after,
            
                            'last_reference_type' =>
                                $lastMovement?->reference_type,
            
                            'last_reference_id' =>
                                $lastMovement?->reference_id,
            
                            'last_created_by' => $lastMovement
                            ? (User::find($lastMovement->created_by)?->name ?? '-')
                            : '-',
                        ];
                    });
            }
            
    /* ================= DC REPORT ================= */
    public function getDcReport($filters)
        {
        $query = DeliveryChallan::with([
            'customer',
            'items.product'
        ]);
    
        if (!empty($filters['from_date'])) {
            $query->whereDate(
                'challan_date',
                '>=',
                $filters['from_date']
            );
        }
    
        if (!empty($filters['to_date'])) {
            $query->whereDate(
                'challan_date',
                '<=',
                $filters['to_date']
            );
        }
    
        if (!empty($filters['status'])) {
            $query->where(
                'status',
                $filters['status']
            );
        }
    
        if (!empty($filters['customer_id'])) {
            $query->where(
                'customer_id',
                $filters['customer_id']
            );
        }
    
        return $query
            ->latest()
            ->paginate(20);
    }
    
    
    public function getDcReportForExport($filters)
        {
            $query = DeliveryChallan::with([
                'customer',
                'items.product'
            ]);
        
            if (!empty($filters['from_date'])) {
                $query->whereDate('challan_date', '>=', $filters['from_date']);
            }
        
            if (!empty($filters['to_date'])) {
                $query->whereDate('challan_date', '<=', $filters['to_date']);
            }
        
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
        
            if (!empty($filters['customer_id'])) {
                $query->where('customer_id', $filters['customer_id']);
            }
        
            return $query->latest()->get();
        }


    public function getDcReportSummary($filters)
        {
            $query = DeliveryChallan::query();

            if (!empty($filters['from_date'])) {
                $query->whereDate('challan_date', '>=', $filters['from_date']);
            }

            if (!empty($filters['to_date'])) {
                $query->whereDate('challan_date', '<=', $filters['to_date']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['customer_id'])) {
                $query->where('customer_id', $filters['customer_id']);
            }

            return $query->selectRaw("
                COUNT(*) as total_dc,
                SUM(total_qty) as total_qty,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = 'dispatched' THEN 1 ELSE 0 END) as dispatched_count
            ")->first();
        }

    /* ================= STOCK LEDGER ================= */
    public function getStockLedgerReport($filters)
        {
            $query = StockLedger::with('product');
        
            if (!empty($filters['from_date'])) {
                $query->whereDate(
                    'created_at',
                    '>=',
                    $filters['from_date']
                );
            }
        
            if (!empty($filters['to_date'])) {
                $query->whereDate(
                    'created_at',
                    '<=',
                    $filters['to_date']
                );
            }
        
            if (!empty($filters['product_id'])) {
                $query->where(
                    'product_id',
                    $filters['product_id']
                );
            }
        
            $ledger = $query
                ->orderBy('id', 'desc')
                ->paginate(20);
        
            $ledger->getCollection()->transform(function ($item) {
        
                /*
                |--------------------------------------------------------------------------
                | Created By
                |--------------------------------------------------------------------------
                */
        
                $item->created_by_name = User::find(
                    $item->created_by
                )?->name ?? '-';
        
        
                /*
                |--------------------------------------------------------------------------
                | Default Reference Details
                |--------------------------------------------------------------------------
                */
        
                $item->reference_no = '-';
                $item->reference_date = null;
        
        
                /*
                |--------------------------------------------------------------------------
                | Purchase Receive
                |--------------------------------------------------------------------------
                */
        
                if ($item->reference_type === 'PURCHASE_RECEIVE') {
        
                    $receive = PurchaseReceive::with('purchase')
                        ->find($item->reference_id);
        
                    if ($receive) {
        
                        $item->reference_date = $receive->receive_date;
        
                        /*
                        | Purchase has invoice/document number
                        | Use the actual column from your Purchase model.
                        */
        
                        $item->reference_no =
                            $receive->purchase->invoice_no
                            ?? $receive->purchase->id
                            ?? '-';
                    }
                }
        
                return $item;
            });
        
            return $ledger;
        }
        

    public function getStockLedgerSummary($filters)
        {
                $query = StockLedger::query();
            
                if (!empty($filters['from_date'])) {
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $filters['from_date']
                    );
                }
            
                if (!empty($filters['to_date'])) {
                    $query->whereDate(
                        'created_at',
                        '<=',
                        $filters['to_date']
                    );
                }
            
                if (!empty($filters['product_id'])) {
                    $query->where(
                        'product_id',
                        $filters['product_id']
                    );
                }
            
                if (!empty($filters['movement_type'])) {
                    $query->where(
                        'movement_type',
                        $filters['movement_type']
                    );
                }
            
                if (!empty($filters['reference_type'])) {
                    $query->where(
                        'reference_type',
                        $filters['reference_type']
                    );
                }
            
                return $query->selectRaw("
                    COUNT(*) as total_movements,
            
                    SUM(
                        CASE
                            WHEN movement_type = 'IN'
                            THEN qty
                            ELSE 0
                        END
                    ) as total_in,
            
                    SUM(
                        CASE
                            WHEN movement_type = 'OUT'
                            THEN qty
                            ELSE 0
                        END
                    ) as total_out
                ")->first();
            }


    public function getProductReport($filters = [])
        {
            return Product::query()
                ->select('id', 'name', 'sku', 'opening_stock', 'stock_quantity')
                ->get()
                ->map(function ($product) {

                    $in = StockIn::where('product_id', $product->id)->sum('qty');
                    $out = StockOut::where('product_id', $product->id)->sum('qty');

                    $opening = $product->opening_stock ?? 0;

                    $product->total_in = $in;
                    $product->total_out = $out;
                    $product->available_stock = $opening + $in - $out;

                    return $product;
                });
        }
        

    public function getProductSummary($filters = [])
        {
            $products = $this->getProductReport($filters);

            return (object)[
                'total_products' => $products->count(),
                'total_stock_in' => $products->sum('total_in'),
                'total_stock_out' => $products->sum('total_out'),
                'total_available' => $products->sum('available_stock'),
            ];
        } 

    /* ================= CUSTOMER REPORT ================= */
    public function getCustomerReport($filters = [])
        {
            $query = \App\Models\Customer::query();

            if (!empty($filters['search'])) {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('mobile', 'like', '%' . $filters['search'] . '%');
            }

            return $query->orderBy('id', 'desc')
                ->get()
                ->map(function ($customer) { 
                    $customer->total_orders = \App\Models\DeliveryChallan::where('customer_id', $customer->id)->count();
                    $customer->total_amount = \App\Models\DeliveryChallan::where('customer_id', $customer->id)->sum('total_amount');

                    return $customer;
                });
        }
        
        
    /* ================= CUSTOMER SUMMARY ================= */
    public function getCustomerSummary($filters = [])
        {
            $customers = $this->getCustomerReport($filters);

            return (object)[
                'total_customers' => $customers->count(),
                'total_orders' => $customers->sum('total_orders'),
                'total_amount' => $customers->sum('total_amount'),
            ];
        }

    //* ================= VENDOR REPORT ================= */
    public function getVendorReport($filters = [])
        {
            $query = \App\Models\Vendor::query();

            if (!empty($filters['search'])) {
                $search = $filters['search'];

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%")
                    ->orWhere('company_name', 'like', "%$search%");
                });
            }

            return $query->orderBy('id', 'desc')
                ->get()
                ->map(function ($vendor) {

                    $purchases = \App\Models\Purchase::where('vendor_id', $vendor->id);

                    $vendor->total_purchases = $purchases->count();
                    $vendor->total_amount = $purchases->sum('total_amount');

                    return $vendor;
                });
        }

    /* SUMMARY */
    public function getVendorSummary($filters = [])
        {
            $vendors = $this->getVendorReport($filters);

            return (object)[
                'total_vendors' => $vendors->count(),
                'total_purchases' => $vendors->sum('total_purchases'),
                'total_amount' => $vendors->sum('total_amount'),
            ];
        }
    }