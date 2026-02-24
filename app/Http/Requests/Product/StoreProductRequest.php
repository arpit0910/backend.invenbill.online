<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products')->where(fn ($q) => $q->where('created_by', $userId)),
            ],
            'mrp' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0', 'lte:mrp'],
            'short_description' => ['nullable', 'string'],
            'detailed_description' => ['nullable', 'string'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp'],
        ];
    }
}
