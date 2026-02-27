<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'house_id'     => ['sometimes', 'uuid', 'exists:houses,id'],
            'fee_type_id'  => ['sometimes', 'uuid', 'exists:fee_types,id'],
            'period_start' => ['sometimes', 'date_format:Y-m-d'],
            'period_end'   => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:period_start'],
        ];
    }

    public function messages(): array
    {
        return [
            'house_id.exists'           => 'Rumah tidak ditemukan.',
            'fee_type_id.exists'        => 'Jenis iuran tidak ditemukan.',
            'period_start.date_format'  => 'Format tanggal mulai harus YYYY-MM-DD.',
            'period_end.date_format'    => 'Format tanggal akhir harus YYYY-MM-DD.',
            'period_end.after_or_equal' => 'Tanggal akhir harus sama dengan atau setelah tanggal mulai.',
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
