<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\CustomerController;

Route::middleware(['auth'])->group(function () {

    Route::resource('/Employee', EmployeeController::class)->names([
    'index' => 'Employee',
    'create' => 'Employee.create',
    'store' => 'Employee.store',
    'edit' => 'Employee.edit',
    'update' => 'Employee.update',
    'destroy' => 'Employee.destroy',
]);

Route::resource('/Role', RoleController::class)->names([
    'index' => 'Role',
    'create' => 'Role.create',
    'store' => 'Role.store',
    'edit' => 'Role.edit',
    'update' => 'Role.update',
    'destroy' => 'Role.destroy',
]);

Route::resource('Category', CategoryController::class)->names([
    'index' => 'Category',
    'create' => 'Category.create',
    'store' => 'Category.store',
    'edit' => 'Category.edit',
    'update' => 'Category.update',
    'destroy' => 'Category.destroy',
]);

Route::resource('/Product', ProductController::class)->names([
    'index' => 'Product',
    'create' => 'Product.create',
    'store' => 'Product.store',
    'edit' => 'Product.edit',
    'update' => 'Product.update',
    'destroy' => 'Product.destroy',
]);

Route::resource('/Vendors', VendorController::class)->names([
    'index' => 'Vendors',
    'create' => 'Vendors.create',
    'store' => 'Vendors.store',
    'edit' => 'Vendors.edit',
    'update' => 'Vendors.update',
    'destroy' => 'Vendors.destroy',
]);

Route::resource('Customer', CustomerController::class)->names([
    'index' => 'Customer',
    'create' => 'Customer.create',
    'store' => 'Customer.store',
    'edit' => 'Customer.edit',
    'update' => 'Customer.update',
    'show' => 'Customer.show',
    'destroy' => 'Customer.destroy',
]);

});