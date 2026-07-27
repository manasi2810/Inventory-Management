<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\TestQueueJob;
use App\Http\Controllers\Admin\InvoiceController;
use Illuminate\Support\Facades\Mail;
 

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/test-queue', function () {

    TestQueueJob::dispatch();

    return 'Job dispatched successfully';

});

    Route::get('/invoices/create/{dispatch}',
    [InvoiceController::class,'create'])
    ->name('invoices.create');
    Route::get('/invoices/store/{dispatch}', 
    [InvoiceController::class, 'store'])
    ->name('invoice.store');
    Route::get('/invoices/show/{invoice}', [InvoiceController::class, 'show'])
    ->name('invoices.show');
   Route::get('/invoices/pdf/{invoice}', [InvoiceController::class, 'pdf'])
    ->name('invoices.pdf');
    
    
    
    Route::get('/mail-test', function () {

    Mail::raw('Congratulations! SMTP is working successfully.', function ($message) {

        $message->to('manasinikam09@gmail.com')
                ->subject('Laravel SMTP Test');

    }); 
    return 'Mail Sent Successfully';
    });



require __DIR__ . '/auth.php';

 

require __DIR__ . '/master.php';      // Employee, Role, Category, Product, Vendor, Customer
require __DIR__ . '/purchase.php';    // Purchase module
require __DIR__ . '/inventory.php';   // Stock In, Inventory
require __DIR__ . '/delivery.php';    // Delivery Challan + Return
require __DIR__ . '/reports.php';     // All reports
require __DIR__ . '/system.php';     // Activity logs, system settings, ,dashboard