<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();
        $productId = $this->route('id') ?? $this->route('product');

        return [
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('products')->where(fn ($q) => $q->where('created_by', $userId))->ignore($productId),
            ],
            'mrp' => ['sometimes', 'numeric', 'min:0'],
            'sale_price' => ['sometimes', 'numeric', 'min:0', 'lte:mrp'],
            'short_description' => ['nullable', 'string'],
            'detailed_description' => ['nullable', 'string'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp'],
        ];
    }
}
