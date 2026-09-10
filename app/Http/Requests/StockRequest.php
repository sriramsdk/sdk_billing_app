<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $stockId = $this->route('stock')?->id;

        return [
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                Rule::unique('stocks', 'product_id')->ignore($stockId),
            ],
            'quantity' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'reserved_quantity' => ['required', 'integer', 'min:0', 'max:4294967295', 'lte:quantity'],
        ];
    }
}