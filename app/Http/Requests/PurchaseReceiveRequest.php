<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReceiveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'items.*.ordered_qty' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.received_qty' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }
}