<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DeliveryChallanController;
use App\Http\Controllers\Admin\DcReturnController;

Route::prefix('delivery')->middleware(['auth'])->group(function () {

   Route::resource('/Delivery_challan', DeliveryChallanController::class)->names([
    'index' => 'Delivery_challan',
    'create' => 'Delivery_challan.create',
    'store' => 'Delivery_challan.store',
    'edit' => 'Delivery_challan.edit',
    'update' => 'Delivery_challan.update',
    'show' => 'Delivery_challan.show',
    'destroy' => 'Delivery_challan.destroy',
]);

Route::get('/delivery-challan/print/{id}', [DeliveryChallanController::class, 'print'])
    ->name('Delivery_challan.print');

Route::get('/Delivery_challan/bulk-print', [DeliveryChallanController::class, 'bulkPrint'])
    ->name('Delivery_challan.bulkPrint');

Route::post('/delivery-challan/{id}/dispatch', [DeliveryChallanController::class, 'dispatch'])
    ->name('Delivery_challan.dispatch');

Route::post('/delivery-challan/{id}/approve', [DeliveryChallanController::class, 'approve'])
    ->name('delivery_challan.approve');

Route::get('/delivery-challan/trashed', [DeliveryChallanController::class, 'trashed'])
    ->name('Delivery_challan.trashed');

Route::post('/delivery-challan/{id}/restore', [DeliveryChallanController::class, 'restore'])
    ->name('Delivery_challan.restore');

Route::delete('/delivery-challan/{id}/force-delete', [DeliveryChallanController::class, 'forceDelete'])
    ->name('Delivery_challan.forceDelete');

});

Route::get('/dc/{id}/return', [DcReturnController::class, 'create'])
    ->name('dc_return.create');

Route::post('/dc-return/store', [DcReturnController::class, 'store'])
    ->name('dc_return.store');