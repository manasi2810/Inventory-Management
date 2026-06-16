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
            ->orderBy('id', 'asc')
            ->get();

        // ERP: current balance from ledger
        $currentBalance = $ledgers->last()->balance_after 
            ?? $customer->opening_balance;

        return view('admin.customer.ledger', compact(
            'customer',
            'ledgers',
            'currentBalance'
        ));
    }
}