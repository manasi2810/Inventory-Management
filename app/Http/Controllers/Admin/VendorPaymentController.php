<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorLedger;
use Illuminate\Support\Facades\DB;


class VendorPaymentController extends Controller
{
    /**
     * Show Vendor Payment Page
     */
    public function index($id)
    {
        $vendor = Vendor::findOrFail($id);

        $payments = VendorLedger::where('vendor_id', $id)
            ->where('entry_type', 'DEBIT')
            ->latest()
            ->get();

        // ✅ SINGLE SOURCE OF TRUTH (NO MANUAL SUM)
        $outstanding = $vendor->getOutstandingAmount();

        return view('admin.vendor.payments', compact(
            'vendor',
            'payments',
            'outstanding'
        ));
    }

    /**
     * Save Vendor Payment
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {

            $vendor = Vendor::findOrFail($id);
            $amount = (float) $request->amount;

            // ✅ CURRENT OUTSTANDING (MODEL BASED)
            $currentOutstanding = $vendor->getOutstandingAmount();

            // ❌ BLOCK OVER PAYMENT
            if ($amount > $currentOutstanding) {
                throw new \Exception(
                    'Payment exceeds outstanding. Current outstanding is ₹' .
                    number_format($currentOutstanding, 2)
                );
            }

            // ✅ ERP SEQUENTIAL BALANCE (LAST LEDGER BASED)
            $lastBalance = VendorLedger::where('vendor_id', $vendor->id)
                ->latest('id')
                ->value('balance_after') ?? 0;

            $newBalance = $lastBalance - $amount;

            // ✅ INSERT LEDGER ENTRY (DEBIT = PAYMENT)
            VendorLedger::create([
                'vendor_id'      => $vendor->id,
                'entry_type'     => 'DEBIT',
                'amount'         => $amount,
                'reference_type' => 'PAYMENT',
                'reference_id'   => null,
                'balance_after'  => $newBalance,
                'note'           => 'Vendor payment recorded',
                'created_by'     => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Payment recorded successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function statement($id)
        {
            $vendor = Vendor::findOrFail($id);

            $ledgers = VendorLedger::where('vendor_id', $id)
                ->orderBy('id', 'asc')
                ->get();

            $runningBalance = $vendor->opening_balance;

            foreach ($ledgers as $ledger) {

                if ($ledger->entry_type == 'CREDIT') {
                    $runningBalance += $ledger->amount;
                } else {
                    $runningBalance -= $ledger->amount;
                }

                $ledger->running_balance = $runningBalance;
            }

            return view('admin.vendor.statement', compact('vendor', 'ledgers'));
        }
        public function agingReport()
{
    $vendors = Vendor::all();

    $report = [];

    foreach ($vendors as $vendor) {

        $ledgers = VendorLedger::where('vendor_id', $vendor->id)
            ->orderBy('id', 'asc')
            ->get();

        $balance = $vendor->opening_balance;

        $buckets = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+'  => 0,
        ];

        foreach ($ledgers as $ledger) {

            if ($ledger->entry_type == 'CREDIT') {

                $balance += $ledger->amount;

                // Age calculation
                $days = now()->diffInDays($ledger->created_at);

                if ($days <= 30) {
                    $buckets['0-30'] += $ledger->amount;
                } elseif ($days <= 60) {
                    $buckets['31-60'] += $ledger->amount;
                } elseif ($days <= 90) {
                    $buckets['61-90'] += $ledger->amount;
                } else {
                    $buckets['90+'] += $ledger->amount;
                }
            }

            if ($ledger->entry_type == 'DEBIT') {
                $balance -= $ledger->amount;
            }
        }

        $report[] = [
            'vendor' => $vendor,
            'buckets' => $buckets,
            'outstanding' => $balance,
        ];
    }

    return view('admin.vendor.aging_report', compact('report'));
}
}