<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'house_id'     => ['required', 'uuid', 'exists:houses,id'],
            'fee_type_id'  => ['required', 'uuid', 'exists:fee_types,id'],
            'period_start' => ['required', 'date_format:Y-m-d'],
            'period_end'   => ['required', 'date_format:Y-m-d', 'after_or_equal:period_start'],
        ];
    }

    public function messages(): array
    {
        return [
            'house_id.required'     => 'House ID wajib diisi.',
            'house_id.exists'       => 'Rumah tidak ditemukan.',
            'fee_type_id.required'  => 'Fee type ID wajib diisi.',
            'fee_type_id.exists'    => 'Jenis iuran tidak ditemukan.',
            'period_start.required' => 'Tanggal mulai periode wajib diisi.',
            'period_start.date_format' => 'Format tanggal mulai harus YYYY-MM-DD.',
            'period_end.required'   => 'Tanggal akhir periode wajib diisi.',
            'period_end.date_format' => 'Format tanggal akhir harus YYYY-MM-DD.',
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
