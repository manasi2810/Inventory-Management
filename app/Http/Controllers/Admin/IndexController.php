<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\DeliveryChallan;
use App\Models\StockIn;

class IndexController extends Controller
{
    public function index()
{
    $totalProducts = Product::count();
    $totalOrders   = DeliveryChallan::count();
    $totalStock    = StockIn::sum('qty');

    $lowStock = Product::withSum('stockIns', 'qty')
        ->get()
        ->filter(fn($p) => ($p->stock_ins_sum_qty ?? 0) < 10)
        ->count();

    return view('Admin.dashboard', compact(
        'totalProducts',
        'totalOrders',
        'totalStock',
        'lowStock'
    ));
}
}