<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'    => ['sometimes', 'string', 'max:255'],
            'ktp_photo'    => ['sometimes', 'image', 'mimes:jpeg,png', 'max:2048'],
            'is_contract'  => ['sometimes', 'boolean'],
            'phone_number' => ['sometimes', 'string', 'max:20'],
            'is_married'   => ['sometimes', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
