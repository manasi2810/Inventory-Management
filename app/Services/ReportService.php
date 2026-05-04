<?php

namespace App\Services;

use App\Models\DcReturnItem;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockLedger;
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
 public function getStockReport()
{
    return Product::query()
        ->select('id', 'name', 'sku', 'opening_stock')
        ->get()
        ->map(function ($product) {

            // 📥 Purchase
            $purchase = DB::table('stock_ins')
                ->where('product_id', $product->id)
                ->sum('qty');

            // 📤 Sales
            $sales = DB::table('stock_ledgers')
                ->where('product_id', $product->id)
                ->where('movement_type', 'OUT')
                ->sum('qty');

            // 📥 Returns
            $returns = DB::table('dc_return_items')
                ->where('product_id', $product->id)
                ->sum('return_qty');

           
            $opening = $product->opening_stock ?? 0;

            $totalIn = $opening + $purchase + $returns;

            $totalOut = $sales;

            $closing = $totalIn - $totalOut;

            return (object)[
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,

                'opening_stock' => $opening,
                'purchase_qty' => $purchase,
                'sale_qty' => $sales,
                'return_qty' => $returns,

                'closing_stock' => $closing,
            ];
        });
}
    /* ================= DC REPORT ================= */
    public function getDcReport($filters)
    {
        $query = DeliveryChallan::with(['customer', 'items']);

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

        return $query->latest()->paginate(20);
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
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        return $query->orderBy('id', 'desc')->paginate(20);
    }

    public function getStockLedgerSummary($filters)
    {
        $query = StockLedger::query();

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        return $query->selectRaw("
            SUM(CASE WHEN movement_type = 'IN' THEN qty ELSE 0 END) as total_in,
            SUM(CASE WHEN movement_type = 'OUT' THEN qty ELSE 0 END) as total_out
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