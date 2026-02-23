<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust authorization logic if needed (e.g., only Admin)
    }

    public function rules(): array
    {
        return [
            // Warehouse details
            'name' => ['required', 'string', 'max:255'],
            'warehouse_code' => ['required', 'string', 'unique:warehouses,warehouse_code', 'max:50'],
            'address' => ['required', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'opening_time' => ['nullable', 'date_format:H:i'],
            'closing_time' => ['nullable', 'date_format:H:i', 'after:opening_time'],
            'weekly_off_day' => ['nullable', 'string', 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday'],
            
            // Manager details
            'manager_name' => ['required', 'string', 'max:255'],
            'manager_email' => ['required', 'email', 'unique:users,email'],
            'manager_phone' => ['required', 'string', 'max:20'],
            'manager_password' => ['required', 'string', 'min:8'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            sendResponse($validator->errors(), 'Validation failed', 422)
        );
    }
}
