<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}