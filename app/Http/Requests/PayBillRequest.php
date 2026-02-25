<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PayBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_date' => ['required', 'date_format:Y-m-d'],
            'amount_paid'  => ['required', 'numeric', 'min:1'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_date.required'    => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date_format' => 'Format tanggal pembayaran harus YYYY-MM-DD.',
            'amount_paid.required'     => 'Jumlah pembayaran wajib diisi.',
            'amount_paid.numeric'      => 'Jumlah pembayaran harus berupa angka.',
            'amount_paid.min'          => 'Jumlah pembayaran minimal 1.',
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
