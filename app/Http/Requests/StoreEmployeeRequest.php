<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'contact_no' => 'nullable|string|max:20',
            'address' => 'nullable|string',

            'role' => 'required|string|exists:roles,name',

            'password' => 'required|string|min:6',

            'department' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',

            'date_of_join' => 'nullable|date',

            'salary' => 'nullable|numeric|min:0',

            'profile_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'resume' => [
                'nullable',
                'mimes:pdf,doc,docx',
                'max:2048',
            ],

            'certificates' => [
                'nullable',
                'array',
            ],

            'certificates.*' => [
                'nullable',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],

            'id_proof' => [
                'nullable',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }
}