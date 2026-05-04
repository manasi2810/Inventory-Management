<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Models\Product;
use App\Exports\DcReturnExport;
use App\Exports\StockReportExport;
use App\Exports\StockLedgerExport;
use App\Exports\ProductReportExport;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /* ================= STOCK REPORT ================= */
    public function stockReport()
        {
            $stock = $this->reportService->getStockReport();
            return view('admin.report.stock_report', compact('stock'));
        } 
    public function exportStockReport()
        {
            return (new StockReportExport($this->reportService))
                ->download('stock_report.xlsx');
        }

    /* ================= DC RETURN ================= */
    public function dcreturnReport(Request $request)
    {
        $filters = $request->all();

        $returns = $this->reportService->getDcReturnReport($filters);
        $summary = $this->reportService->getDcReturnSummary($filters);
        $products = Product::orderBy('name')->get();

        return view('admin.report.dc_return_report', compact(
            'returns', 'summary', 'products'
        ));
    }

    public function exportDcReport(Request $request)
    {
        return (new DcReturnExport($request->all()))
            ->download('dc_return_report.xlsx');
    }

    /* ================= DC REPORT ================= */
    public function dcReport(Request $request)
    {
        $filters = $request->all();

        $dcList = $this->reportService->getDcReport($filters);
        $summary = $this->reportService->getDcReportSummary($filters);

        return view('admin.report.dc_report', compact('dcList', 'summary'));
    }
    public function exportDcMainReport(Request $request)
        {
            return (new \App\Exports\DcReportExport($request->all()))
                ->download('dc_report.xlsx');
        }

    /* ================= STOCK LEDGER ================= */
    public function stockLedgerReport(Request $request)
    {
        $filters = $request->all();

        $ledger = $this->reportService->getStockLedgerReport($filters);
        $summary = $this->reportService->getStockLedgerSummary($filters);
        $products = Product::orderBy('name')->get();

        return view('admin.report.stock_ledger_report', compact(
            'ledger', 'summary', 'products'
        ));
    }

    public function exportLedgerReport(Request $request)
    {
        return (new StockLedgerExport($request->all()))
            ->download('stock_ledger_report.xlsx');
    }

    /* ================= PRODUCT REPORT ================= */
    public function productReport(Request $request)
    {
        $filters = $request->all();

        $products = $this->reportService->getProductReport($filters);
        $summary = $this->reportService->getProductSummary($filters);

        return view('admin.report.product_report', compact('products', 'summary'));
    }

    public function exportProductReport(Request $request)
    {
        return (new ProductReportExport($request->all()))
            ->download('product_report.xlsx');
    }

    /* ================= CUSTOMER REPORT ================= */
public function customerReport(Request $request)
{
    $filters = $request->all();

    $customers = $this->reportService->getCustomerReport($filters);
    $summary   = $this->reportService->getCustomerSummary($filters);

    return view('admin.report.customer_report', compact('customers', 'summary'));
}

/* EXPORT CUSTOMER REPORT */
public function exportCustomerReport(Request $request)
{
    return (new \App\Exports\CustomerReportExport($request->all()))
        ->download('customer_report.xlsx');
}

/* ================= VENDOR REPORT ================= */
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

//* ================= VENDOR REPORT ================= */
public function vendorReport(Request $request)
{
    $filters = $request->all();

    $vendors = $this->reportService->getVendorReport($filters);
    $summary = $this->reportService->getVendorSummary($filters);

    return view('admin.report.vendor_report', compact('vendors', 'summary'));
}

/* EXPORT VENDOR REPORT */
public function exportVendorReport(Request $request)
{
    return (new \App\Exports\VendorReportExport($request->all()))
        ->download('vendor_report.xlsx');
}
}