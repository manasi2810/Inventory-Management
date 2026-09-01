<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $product = $this->route('Product');

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
                Rule::unique('products', 'sku')
                    ->ignore($product->id),
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