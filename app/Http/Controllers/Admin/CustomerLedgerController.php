<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLedger;

class CustomerLedgerController extends Controller
{
    /**
     * Show Customer Ledger (ERP style)
     */
   public function index($id)
        {
            $customer = Customer::findOrFail($id);
        
            $ledgers = CustomerLedger::where('customer_id', $id)
                        ->latest('id')
                        ->get();
        
            $currentBalance = optional($ledgers->first())->balance_after
                                ?? $customer->opening_balance;
        
            return view(
                'Admin.Customer.ledger',
                compact('customer','ledgers','currentBalance')
            );
        }
}