<?php

namespace App\Services;

use App\Models\CustomerLedger;
use Illuminate\Support\Facades\DB;

class CustomerLedgerService
{
    public function addEntry($customerId, $type, $amount, $refType = null, $refId = null)
    {
        return DB::transaction(function () use (
            $customerId,
            $type,
            $amount,
            $refType,
            $refId
        ) {

            // Lock last ledger entry for safe balance calculation
            $lastBalance = CustomerLedger::where('customer_id', $customerId)
                ->lockForUpdate()
                ->latest()
                ->value('balance_after') ?? 0;

            // ERP balance logic
            if ($type === 'CREDIT') {

                // Sales / Invoice → increases receivable
                $newBalance = $lastBalance + $amount;

            } elseif ($type === 'DEBIT') {

                // Payment → reduces receivable
                $newBalance = $lastBalance - $amount;

            } else {
                throw new \Exception("Invalid ledger type: {$type}");
            }

            return CustomerLedger::create([
                'customer_id'    => $customerId,
                'entry_type'     => $type,
                'amount'         => $amount,
                'reference_type'  => $refType,
                'reference_id'   => $refId,
                'balance_after'  => $newBalance,
                'created_by'     => auth()->id(),
            ]);
        });
    }
}