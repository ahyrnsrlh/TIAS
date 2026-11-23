<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer') ? $this->route('customer')->id : $this->id;

        return [
            'customer_code' => ['required', 'string', 'max:50', Rule::unique('customers', 'customer_code')->ignore($customerId)],
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customerId)],
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
