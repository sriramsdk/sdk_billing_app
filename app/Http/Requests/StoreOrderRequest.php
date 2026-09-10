<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->isEmployee();
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'nullable',
                'required_if:customer_mode,existing',
                'exists:customers,id',
            ],

            'customer_name' => ['nullable', 'string', 'max:255'],

            'customer_email' => ['nullable', 'email', 'max:255', 'required_if:customer_mode,new'],

            'customer_mode' => ['required', 'in:walk-in,existing,new'],

            'amount_paid' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' =>
                'Please add at least one product.',

            'items.min' =>
                'Please add at least one product.',

            'items.*.product_id.required' =>
                'Please select a product.',

            'items.*.quantity.min' =>
                'Quantity must be at least 1.',

            'customer_email.required_if' =>
                'A customer email is required for a new customer.',
        ];
    }
}