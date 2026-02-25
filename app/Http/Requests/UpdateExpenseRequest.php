<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_name' => ['sometimes', 'string', 'max:255'],
            'expense_date' => ['sometimes', 'date_format:Y-m-d'],
            'amount'       => ['sometimes', 'numeric', 'min:1'],
            'description'  => ['nullable', 'string'],
            'is_monthly'   => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'expense_date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'amount.numeric'           => 'Jumlah pengeluaran harus berupa angka.',
            'amount.min'               => 'Jumlah pengeluaran minimal 1.',
            'is_monthly.boolean'       => 'Status bulanan harus true atau false.',
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
