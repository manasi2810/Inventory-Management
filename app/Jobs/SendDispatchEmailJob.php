<?php

namespace App\Jobs;

use App\Mail\DispatchDocumentsMail;
use App\Models\Dispatch;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDispatchEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $dispatchId,
        public int $invoiceId
    ) {
    }

    /**
     * Send dispatch documents email.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        Log::info('SendDispatchEmailJob Started', [
            'dispatch_id' => $this->dispatchId,
            'invoice_id' => $this->invoiceId,
        ]);

        $dispatch = Dispatch::with('customer')->find($this->dispatchId);
        $invoice = Invoice::find($this->invoiceId);

        if (! $dispatch || ! $invoice) {
            Log::error('Dispatch or Invoice not found.', [
                'dispatch_id' => $this->dispatchId,
                'invoice_id' => $this->invoiceId,
            ]);

            return;
        }

        if (blank($dispatch->customer?->email)) {
            Log::warning('Customer email not available.', [
                'dispatch_id' => $dispatch->id,
            ]);

            return;
        }

        $packingSlipPath = storage_path('app/public/' . $dispatch->packing_slip_pdf);

        $invoicePath = storage_path('app/public/' . $invoice->invoice_pdf);
        
        Mail::to($dispatch->customer->email)
            ->send(
                new DispatchDocumentsMail(
                    $dispatch,
                    $invoice,
                    $packingSlipPath,
                    $invoicePath
                )
            );

        Log::info('Dispatch Email Sent Successfully.', [
            'dispatch_no' => $dispatch->dispatch_no,
            'invoice_no' => $invoice->invoice_no,
            'customer_email' => $dispatch->customer->email,
        ]);
    }
}