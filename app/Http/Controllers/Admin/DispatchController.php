<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;

class DispatchController extends Controller
{
    public function show($id)
        {
        $dispatch = Dispatch::with([
            'customer',
            'challan',
            'items.product'
        ])->findOrFail($id);

        return view('Admin.Dispatch.show', compact('dispatch'));
    }

    public function print($id)
        {
        $dispatch = Dispatch::with([
            'customer',
            'challan',
            'items.product'
        ])->findOrFail($id);

        return view('Admin.Dispatch.print', compact('dispatch'));
    }
        
    public function create(Dispatch $dispatch)
        {
            dd($dispatch);
        }
}



