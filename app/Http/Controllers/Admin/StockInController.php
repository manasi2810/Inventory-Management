<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\StockOut; 

class StockInController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:stock.view')->only(['index']);
        $this->middleware('permission:stock.out')->only(['stockout']);
    }

    public function index()
    {
        $stockIns = StockIn::with('product')
            ->orderBy('id', 'desc')
            ->get();

        return view('Admin.Stock.stock-in', compact('stockIns'));
    }

    public function stockout()
    {
        $stockOuts = StockOut::with('product')
            ->orderBy('id', 'desc')
            ->get();

        return view('Admin.Stock.stock-out', compact('stockOuts'));
    }
}