<?php

namespace App\Http\Requests;

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
            'customer_code' => ['nullable', 'string', 'max:50', 'unique:customers,customer_code'],
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'npwp' => ['nullable', 'string', 'max:35'],
            'virtual_account' => ['nullable', 'string', 'max:30'],
            'address' => ['required', 'string'],
            'billing_address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
