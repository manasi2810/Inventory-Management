<?php

use Illuminate\Support\Facades\Route;
 

Route::get('/', function () {
    return redirect('/login');
});


require __DIR__ . '/auth.php';

 

require __DIR__ . '/master.php';      // Employee, Role, Category, Product, Vendor, Customer
require __DIR__ . '/purchase.php';    // Purchase module
require __DIR__ . '/inventory.php';   // Stock In, Inventory
require __DIR__ . '/delivery.php';    // Delivery Challan + Return
require __DIR__ . '/reports.php';     // All reports
require __DIR__ . '/system.php';     // Activity logs, system settings, ,dashboard