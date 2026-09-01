<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'company_name'     => 'nullable|string|max:255',

            'mobile'           => 'nullable|string|max:20',
            'alternate_mobile' => 'nullable|string|max:20',

            'email'            => 'nullable|email|max:255',

            'billing_address'  => 'nullable|string',
            'shipping_address' => 'nullable|string',

            'city'             => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'pincode'          => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:100',

            'gst_number'       => 'nullable|string|max:50',
            'pan_number'       => 'nullable|string|max:20',

            'credit_limit'     => 'nullable|numeric|min:0',
            'opening_balance'  => 'nullable|numeric',

            'customer_type'    => 'nullable|string|max:50',
            'status'           => 'nullable|boolean',

            'notes'            => 'nullable|string',
        ];
    }
}