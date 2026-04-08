<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CategoryController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [IndexController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
 Route::resource('/Employee', EmployeeController::class)->names([
    'index' => 'Employee',
      'create'  => 'Employee.create',
      'store'  => 'Employee.store',
]);
Route::resource('/Role', RoleController::class)->names([
    'index' => 'Role',
      'create'  => 'Role.create',
      'store'  => 'Role.store',
]);
Route::resource('Category', CategoryController::class)->names([
    'index' => 'Category',
    'create' => 'Category.create',
    'store' => 'Category.store',
    'edit' => 'Category.edit',
    'update' => 'Category.update',
    'destroy' => 'Category.destroy',
]);
// Route::resource('categories', CategoryController::class);




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
}); 

require __DIR__.'/auth.php';
