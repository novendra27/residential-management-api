<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_name' => ['required', 'string', 'max:255'],
            'expense_date' => ['required', 'date_format:Y-m-d'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'description'  => ['nullable', 'string'],
            'is_monthly'   => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'expense_name.required' => 'Nama pengeluaran wajib diisi.',
            'expense_date.required' => 'Tanggal pengeluaran wajib diisi.',
            'expense_date.date_format' => 'Format tanggal harus YYYY-MM-DD.',
            'amount.required'       => 'Jumlah pengeluaran wajib diisi.',
            'amount.numeric'        => 'Jumlah pengeluaran harus berupa angka.',
            'amount.min'            => 'Jumlah pengeluaran minimal 1.',
            'is_monthly.required'   => 'Status bulanan wajib diisi.',
            'is_monthly.boolean'    => 'Status bulanan harus true atau false.',
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
