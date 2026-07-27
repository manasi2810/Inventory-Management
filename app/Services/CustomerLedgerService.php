<?php

namespace App\Services;

use App\Models\CustomerLedger;

class CustomerLedgerService
{
    public function addEntry(
        $customerId,
        $referenceType,
        $referenceId,
        $referenceNo,
        $transactionType,
        $debit = 0,
        $credit = 0,
        $remarks = null,
        $subTotal = 0,
        $gstAmount = 0,
        $totalAmount = 0
    ) {

        $debit = (float) ($debit ?? 0);
        $credit = (float) ($credit ?? 0);

        $subTotal = (float) ($subTotal ?? 0);
        $gstAmount = (float) ($gstAmount ?? 0);
        $totalAmount = (float) ($totalAmount ?? 0);

        $lastBalance = CustomerLedger::where('customer_id', $customerId)
            ->lockForUpdate()
            ->latest('id')
            ->value('balance_after') ?? 0;

        $balance = $lastBalance + $debit - $credit;

        return CustomerLedger::create([
            'customer_id'    => $customerId,
            'entry_type'     => $transactionType,

            'debit'          => $debit,
            'credit'         => $credit,

            'sub_total'      => $subTotal,
            'gst_amount'     => $gstAmount,
            'total_amount'   => $totalAmount,

            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'reference_no'   => $referenceNo,

            'balance_after'  => $balance,
            'remarks'        => $remarks,
            'created_by'     => auth()->id(),
        ]);
    }
}