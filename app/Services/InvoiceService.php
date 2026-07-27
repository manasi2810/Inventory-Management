<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Dispatch;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class InvoiceService
{
    /** Disk (config/filesystems.php) where invoice PDFs are stored. */
    private const PDF_DISK = 'public';

    /** Sub-folder on the disk where invoice PDFs live. */
    private const PDF_FOLDER = 'invoices';

    /** Decimal places for currency rounding. */
    private const MONEY_SCALE = 2;

    /**
     * Create an Invoice for a Dispatch (idempotent — returns existing invoice if present).
     *
     * @throws Throwable
     */
    public function createInvoice(Dispatch $dispatch): Invoice
    {
        return DB::transaction(function () use ($dispatch) {
            // Lock the dispatch row for the duration of the transaction so a
            // second concurrent request blocks here instead of racing past
            // the existence check below.
            $locked = Dispatch::whereKey($dispatch->id)->lockForUpdate()->firstOrFail();

            // Re-check for an existing invoice now that we hold the lock.
            $existing = Invoice::where('dispatch_id', $locked->id)->first();
            if ($existing) {
                return $existing;
            }

            $locked->loadMissing('items');
            if ($locked->items->isEmpty()) {
                throw new RuntimeException("Dispatch #{$locked->id} has no items to invoice.");
            }

            $dispatch = $locked;

            $invoice = Invoice::create([
                'invoice_no'          => $this->generateInvoiceNumber(),
                'dispatch_id'         => $dispatch->id,
                'delivery_challan_id' => $dispatch->delivery_challan_id,
                'customer_id'         => $dispatch->customer_id,
                'invoice_date'        => Carbon::now()->toDateString(),
                'sub_total'           => 0,
                'gst_amount'          => 0,
                'total_amount'        => 0,
            ]);

            [$itemRows, $totals] = $this->buildInvoiceItems($invoice, $dispatch);

            InvoiceItem::insert($itemRows);

            $invoice->update($totals);

            return $invoice;
        });
    }

    /**
     * Generate a unique invoice number, safe under concurrent requests.
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $prefix = "INV{$year}-";

        $lastNumber = Invoice::where('invoice_no', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('invoice_no')
            ->value('invoice_no');

        $nextSequence = $lastNumber
            ? ((int) substr($lastNumber, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Build invoice item rows and running totals from dispatch items.
     *
     * @return array{0: array<int, array<string, mixed>>, 1: array<string, float>}
     */
    private function buildInvoiceItems(Invoice $invoice, Dispatch $dispatch): array
    {
        $subTotal = 0.0;
        $gstTotal = 0.0;
        $grandTotal = 0.0;
        $rows = [];
        $now = Carbon::now();

        foreach ($dispatch->items as $item) {
            $taxable  = round($item->quantity * $item->rate, self::MONEY_SCALE);
            $gstAmount = round(($taxable * $item->gst_percent) / 100, self::MONEY_SCALE);
            $total    = round($taxable + $gstAmount, self::MONEY_SCALE);

            $rows[] = [
                'invoice_id'       => $invoice->id,
                'dispatch_item_id' => $item->id,
                'product_id'       => $item->product_id,
                'quantity'         => $item->quantity,
                'rate'             => $item->rate,
                'gst_percent'      => $item->gst_percent,
                'gst_amount'       => $gstAmount,
                'amount'           => $total,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];

            $subTotal   += $taxable;
            $gstTotal   += $gstAmount;
            $grandTotal += $total;
        }

        return [
            $rows,
            [
                'sub_total'    => round($subTotal, self::MONEY_SCALE),
                'gst_amount'   => round($gstTotal, self::MONEY_SCALE),
                'total_amount' => round($grandTotal, self::MONEY_SCALE),
            ],
        ];
    }

    /**
     * Generate the Invoice PDF, store it on the configured disk, and
     * persist its relative path on the invoice. Returns the storage path.
     *
     * @throws Throwable
     */
    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['items.product', 'customer', 'dispatch']);

        $pdfOutput = Pdf::loadView('Admin.Invoice.pdf', compact('invoice'))->output();

        $fileName = "invoice-{$invoice->invoice_no}.pdf";
        $relativePath = self::PDF_FOLDER . '/' . $fileName;

        $stored = Storage::disk(self::PDF_DISK)->put($relativePath, $pdfOutput);

        if (! $stored) {
            throw new RuntimeException("Failed to write invoice PDF for {$invoice->invoice_no}.");
        }

        $invoice->update(['invoice_pdf' => $relativePath]);

        return Storage::disk(self::PDF_DISK)->path($relativePath);
    }
}