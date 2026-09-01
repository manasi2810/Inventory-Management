<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'sku' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,sku',
            ],

            'price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'uom' => [
                'required',
                'string',
                'max:50',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'pack_size' => [
                'nullable',
                'string',
                'max:50',
            ],

            'moq' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }
}