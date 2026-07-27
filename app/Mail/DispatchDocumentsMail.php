<?php

namespace App\Mail;

use App\Models\Dispatch;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DispatchDocumentsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Dispatch $dispatch,
        public Invoice $invoice,
        public string $packingSlipPath,
        public string $invoicePath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice & Packing Slip - ' . $this->invoice->invoice_no,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.dispatch-documents',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->packingSlipPath)
                ->as('Packing Slip.pdf')
                ->withMime('application/pdf'),

            Attachment::fromPath($this->invoicePath)
                ->as('Invoice.pdf')
                ->withMime('application/pdf'),
        ];
    }
}