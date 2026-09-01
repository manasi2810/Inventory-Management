<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => [
                'required',
                'exists:vendors,id',
            ],

            'invoice_no' => [
                'required',
                'string',
                'max:100',
            ],

            'purchase_date' => [
                'required',
                'date',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'items.*.qty' => [
                'required',
                'numeric',
                'min:1',
            ],

            'items.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }
}