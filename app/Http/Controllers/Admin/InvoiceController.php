<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceService;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF; 

class InvoiceController extends Controller
{
    public function create(Dispatch $dispatch)
        {
            $dispatch->load([
                'customer',
                'items.product',
                'deliveryChallan',
            ]); 
            return view('Admin.Invoice.create', compact('dispatch'));
        } 

    public function store(Dispatch $dispatch, InvoiceService $invoiceService)
        {
            $invoice = $invoiceService->createInvoice($dispatch); 
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice generated successfully.');
        }     

    public function show(Invoice $invoice)
        {
            $invoice->load([
                'items.product',
                'customer',
                'dispatch'
            ]); 
            return view('Admin.Invoice.show', compact('invoice'));
        }

    public function pdf(Invoice $invoice, InvoiceService $invoiceService)
        {
            $pdfPath = $invoiceService->generatePdf($invoice); 
            return response()->download($pdfPath);
        }

}



