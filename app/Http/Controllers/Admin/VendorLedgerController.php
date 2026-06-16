<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorLedger;

class VendorLedgerController extends Controller
{
    // SHOW LEDGER PAGE
  public function index($vendorId)
{
    $vendor = Vendor::findOrFail($vendorId);

    $ledgers = VendorLedger::where('vendor_id', $vendorId)
        ->orderBy('id', 'asc')
        ->get();

    $credit = VendorLedger::where('vendor_id', $vendorId)
        ->where('entry_type', 'CREDIT')
        ->sum('amount');

    $debit = VendorLedger::where('vendor_id', $vendorId)
        ->where('entry_type', 'DEBIT')
        ->sum('amount');

    $currentBalance = $vendor->opening_balance + $credit - $debit;

    $outstanding = $currentBalance;

    return view('admin.vendor.ledger', compact(
        'vendor',
        'ledgers',
        'currentBalance',
        'outstanding'
    ));
}
}