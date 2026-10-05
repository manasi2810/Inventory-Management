<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PurchaseCreatedNotification extends Notification
{
    use Queueable;

    protected $purchase;

    public function __construct($purchase)
    {
        $this->purchase = $purchase;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'purchase_created',
            'purchase_id' => $this->purchase->id,
            'invoice_no' => $this->purchase->invoice_no,
            'vendor_id' => $this->purchase->vendor_id,
            'message' => 'New Purchase Order ' .
                $this->purchase->invoice_no .
                ' has been created.',
        ];
    }
}