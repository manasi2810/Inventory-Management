<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PurchaseController;

Route::resource('/Purchase', PurchaseController::class)->names([
    'index' => 'Purchase',
    'create' => 'Purchase.create',
    'store' => 'Purchase.store',
    'edit' => 'Purchase.edit',
    'update' => 'Purchase.update',
    'destroy' => 'Purchase.destroy',
]);

Route::get('/Purchase/{id}/receive', [PurchaseController::class, 'receive'])
    ->name('Purchase.receive');

Route::post('/Purchase/{id}/receive', [PurchaseController::class, 'storeReceive'])
    ->name('Purchase.receive.store');

Route::get('/Purchase/{id}/print', [PurchaseController::class, 'print'])
    ->name('Purchase.print');

Route::get('/purchase/multi-print', [PurchaseController::class, 'multiPrint'])
    ->name('Purchase.multiPrint');

Route::post('/purchase/{id}/short-close', [PurchaseController::class, 'shortClose'])
    ->name('purchase.shortClose');