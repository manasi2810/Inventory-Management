<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

            'email' => 'nullable|email|max:255',

            'contact' => 'nullable|string|max:20',

            'gst_number' => 'nullable|string|max:50',

            'pan_number' => 'nullable|string|max:20',

            'company_name' => 'nullable|string|max:255',

            'address' => 'nullable|string',

            'city' => 'nullable|string|max:100',

            'state' => 'nullable|string|max:100',

            'credit_limit' => 'nullable|numeric|min:0',

            'opening_balance' => 'nullable|numeric|min:0',

            'opening_balance_type' => 'required|in:CR,DR',

            'payment_days' => 'nullable|integer|min:0',

            'bank_name' => 'nullable|string|max:255',

            'bank_account_no' => 'nullable|string|max:50',

            'ifsc_code' => 'nullable|string|max:20',

            'status' => 'required|in:active,inactive,blocked',

            'remarks' => 'nullable|string',
        ];
    }
}