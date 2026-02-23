<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'warehouse_code' => ['sometimes', 'string', 'max:50', 'unique:warehouses,warehouse_code,' . $this->route('id')],
            'address' => ['sometimes', 'string'],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'zip_code' => ['sometimes', 'nullable', 'string', 'max:20'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'timezone' => ['sometimes', 'nullable', 'string', 'max:100'],
            'opening_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'closing_time' => ['sometimes', 'nullable', 'date_format:H:i', 'after:opening_time'],
            'weekly_off_day' => ['sometimes', 'nullable', 'string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            sendResponse($validator->errors(), 'Validation failed', 422)
        );
    }
}
