<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController; 
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\PurchaseController;  
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\DeliveryChallanController;
use App\Http\Controllers\Admin\CustomerController;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [IndexController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Employee
 Route::resource('/Employee', EmployeeController::class)->names([
    'index' => 'Employee',
    'create'  => 'Employee.create',
    'store'  => 'Employee.store',
    'edit' => 'Employee.edit',
    'update' => 'Employee.update',
    'destroy' => 'Employee.destroy', 
]);

// Role
Route::resource('/Role', RoleController::class)->names([
    'index' => 'Role',
    'create'  => 'Role.create',
    'store'  => 'Role.store',
    'edit' => 'Role.edit',
    'update' => 'Role.update',
    'destroy' => 'Role.destroy',
]);

// Category
Route::resource('Category', CategoryController::class)->names([
    'index' => 'Category',
    'create' => 'Category.create',
    'store' => 'Category.store',
    'edit' => 'Category.edit',
    'update' => 'Category.update',
    'destroy' => 'Category.destroy',
]);

// Product
Route::resource('/Product', ProductController::class)->names([
    'index' => 'Product',
    'create'  => 'Product.create',
    'store'  => 'Product.store',
    'edit' => 'Product.edit',
    'update' => 'Product.update',
    'destroy' => 'Product.destroy',
]);

// Vendors
Route::resource('/Vendors', VendorController::class)->names([
    'index' => 'Vendors',
    'create'  => 'Vendors.create',
    'store'  => 'Vendors.store',
    'edit' => 'Vendors.edit',
    'update' => 'Vendors.update',
    'destroy' => 'Vendors.destroy',
]);

// Purchase
Route::resource('/Purchase', PurchaseController::class)->names([
    'index' => 'Purchase',
    'create'  => 'Purchase.create',
    'store'  => 'Purchase.store',
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

// StockIn
Route::resource('/stock-in', StockInController::class)->names([
    'index' => 'stock-in', 
]);

// Customer
Route::resource('Customer', CustomerController::class)->names([
    'index' => 'Customer', 
]); 

// DeliveryChallan
Route::get('/delivery-challan/print/{id}', [DeliveryChallanController::class, 'print'])
    ->name('Delivery_challan.print');
Route::get('/Delivery_challan/bulk-print', [DeliveryChallanController::class, 'bulkPrint'])
    ->name('Delivery_challan.bulkPrint');   
Route::resource('/Delivery_challan', DeliveryChallanController::class)->names([
    'index'   => 'Delivery_challan',
    'create'  => 'Delivery_challan.create',
    'store'   => 'Delivery_challan.store',
    'edit'    => 'Delivery_challan.edit',
    'update'  => 'Delivery_challan.update',
    'show'    => 'Delivery_challan.show',
    'destroy' => 'Delivery_challan.destroy',
]);
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


    
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
}); 


require __DIR__.'/auth.php';
