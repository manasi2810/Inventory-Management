<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockIn;

class StockInController extends Controller
{
   
    public function index()
    {
        $stockIns = StockIn::with('product')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.Stock.stock-in', compact('stockIns'));
    }
}