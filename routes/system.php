<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\IndexController;

Route::middleware(['auth'])->group(function () {

    /*
    | PROFILE MODULE
    */
    Route::get('/dashboard', [IndexController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    /*
    | ACTIVITY LOGS 
    */

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity.logs');
});