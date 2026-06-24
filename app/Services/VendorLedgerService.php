<?php

namespace App\Services;

use App\Models\VendorLedger;
use Illuminate\Support\Facades\DB;

class VendorLedgerService
{
    public function addEntry($vendorId, $type, $amount, $refType = null, $refId = null, $note = null)
    {
        return DB::transaction(function () use (
            $vendorId,
            $type,
            $amount,
            $refType,
            $refId,
            $note
        ) {

            // LOCK LAST ENTRY (IMPORTANT FOR ERP)
            $lastBalance = VendorLedger::where('vendor_id', $vendorId)
                ->lockForUpdate()
                ->latest()
                ->value('balance_after') ?? 0;

            // =========================
            // ERP BALANCE LOGIC
            // =========================
            if ($type === 'DEBIT') {
                $newBalance = $lastBalance + $amount;
            } elseif ($type === 'CREDIT') {
                $newBalance = $lastBalance - $amount;
            } else {
                throw new \Exception("Invalid ledger type: {$type}");
            }

            return VendorLedger::create([
                'vendor_id'       => $vendorId,
                'entry_type'      => $type,
                'amount'          => $amount,
                'reference_type'  => $refType,
                'reference_id'    => $refId,
                'balance_after'   => $newBalance,
                'note'           => $note,
                'created_by'     => auth()->id(),
            ]);
        });
    }
}