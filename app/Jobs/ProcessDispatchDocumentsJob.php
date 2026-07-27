<?php

namespace App\Jobs;

use App\Models\Dispatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoiceService;

class ProcessDispatchDocumentsJob implements ShouldQueue 

 
{
    use Queueable;

    public $dispatchId;

    public function __construct($dispatchId)
    {
        $this->dispatchId = $dispatchId;
    }

  public function handle(InvoiceService $invoiceService): void
{
    $dispatch = Dispatch::with([
    'customer',
    'deliveryChallan',
    'items.product',
    'invoice'
])->find($this->dispatchId);

    if (!$dispatch) {
        Log::error("Dispatch not found: {$this->dispatchId}");
        return;
    }

    Log::info('Packing Slip Job Started', [
        'dispatch_id' => $dispatch->id,
        'dispatch_no' => $dispatch->dispatch_no,
    ]);

    /*
    |--------------------------------------------------------------------------
    | STEP 1 : GENERATE PACKING SLIP PDF
    |--------------------------------------------------------------------------
    */

    $pdf = Pdf::loadView('pdf.packing-slip', [
        'dispatch' => $dispatch
    ]);

    $fileName = 'packing-slip-' . $dispatch->dispatch_no . '.pdf';

    $folder = storage_path('app/public/packing-slips');

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    $fullPath = $folder.'/'.$fileName;

    $pdf->save($fullPath);

    $relativePath = 'packing-slips/'.$fileName;

    $dispatch->update([
        'packing_slip_pdf' => $relativePath
    ]);

    Log::info('Packing Slip Generated Successfully');

    /*
    |--------------------------------------------------------------------------
    | STEP 2 : CREATE INVOICE
    |--------------------------------------------------------------------------
    */

    $invoice = $invoiceService->createInvoice($dispatch);

    Log::info('Invoice Created', [
        'invoice_id' => $invoice->id,
        'invoice_no' => $invoice->invoice_no,
    ]);

    /*
    |--------------------------------------------------------------------------
    | STEP 3 : GENERATE INVOICE PDF
    |--------------------------------------------------------------------------
    */

    $invoicePdf = $invoiceService->generatePdf($invoice);

    Log::info('Invoice PDF Generated', [
        'invoice_pdf' => $invoicePdf
    ]);

  /*
    |--------------------------------------------------------------------------
    | STEP 4 : QUEUE EMAIL
    |--------------------------------------------------------------------------
    */
    
    SendDispatchEmailJob::dispatch(
        $dispatch->id,
        $invoice->id
    );
    
    Log::info('Dispatch Email Job Dispatched', [
        'dispatch_id' => $dispatch->id,
        'invoice_id'  => $invoice->id,
    ]);
    
    Log::info('Dispatch Document Job Completed');
}
}