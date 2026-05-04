<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StockInController;

Route::middleware(['auth'])->prefix('inventory')->group(function () {

    Route::resource('/stock-in', StockInController::class)->names([
    'index' => 'stock-in',
]);

});