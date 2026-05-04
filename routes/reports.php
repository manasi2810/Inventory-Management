<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;

Route::prefix('reports')->group(function () {

    Route::get('/stock', [ReportController::class, 'stockReport'])
        ->name('reports.stock');

    Route::get('/ledger', [ReportController::class, 'stockLedgerReport'])
        ->name('reports.ledger');

    Route::get('/product', [ReportController::class, 'productReport'])
        ->name('reports.product');

    Route::get('/customer', [ReportController::class, 'customerReport'])
        ->name('reports.customer');

    Route::get('/vendor', [ReportController::class, 'vendorReport'])
        ->name('reports.vendor');

    Route::get('/dc', [ReportController::class, 'dcReport'])
        ->name('reports.dc');

    Route::get('/dcreturn', [ReportController::class, 'dcreturnReport'])
        ->name('reports.dcreturn');
});

Route::get('/dc/export', [ReportController::class, 'exportDcMainReport'])
    ->name('reports.dc.export');

Route::get('/dc-return/export', [ReportController::class, 'exportDcReport'])
    ->name('reports.dcreturn.export');

Route::get('/reports/stock/export', [ReportController::class, 'exportStockReport'])
    ->name('reports.stock.export');

Route::get('/reports/ledger/export', [ReportController::class, 'exportLedgerReport'])
    ->name('reports.ledger.export');

Route::get('/reports/product/export', [ReportController::class, 'exportProductReport'])
    ->name('reports.product.export');

Route::get('/reports/customer/export', [ReportController::class, 'exportCustomerReport'])
    ->name('reports.customer.export');

Route::get('/reports/vendor/export', [ReportController::class, 'exportVendorReport'])
    ->name('reports.vendor.export');