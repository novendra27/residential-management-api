<?php

namespace App\Services;

use App\Models\Expense;

class ExpenseService
{
    // ─── Format ────────────────────────────────────────────────────────────────

    private function formatExpense(Expense $expense): array
    {
        return [
            'id'           => $expense->id,
            'expense_name' => $expense->expense_name,
            'expense_date' => $expense->expense_date?->format('Y-m-d'),
            'amount'       => (int) $expense->amount,
            'description'  => $expense->description,
            'is_monthly'   => $expense->is_monthly,
            'created_at'   => $expense->created_at?->toIso8601String(),
        ];
    }

    // ─── List ──────────────────────────────────────────────────────────────────

    public function getAll(array $filters): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Expense::orderBy('expense_date', 'desc');

        if (!empty($filters['month']) && !empty($filters['year'])) {
            $query->whereMonth('expense_date', $filters['month'])
                  ->whereYear('expense_date', $filters['year']);
        } elseif (!empty($filters['year'])) {
            $query->whereYear('expense_date', $filters['year']);
        }

        if (isset($filters['is_monthly']) && $filters['is_monthly'] !== '') {
            $query->where('is_monthly', filter_var($filters['is_monthly'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate(15);
    }

    // ─── Show ──────────────────────────────────────────────────────────────────

    public function getById(string $id): ?Expense
    {
        return Expense::find($id);
    }

    // ─── Create ────────────────────────────────────────────────────────────────

    public function create(array $data): array
    {
        $expense = Expense::create([
            'expense_name' => $data['expense_name'],
            'expense_date' => $data['expense_date'],
            'amount'       => $data['amount'],
            'description'  => $data['description'] ?? null,
            'is_monthly'   => $data['is_monthly'],
            'created_at'   => now(),
        ]);

        return [
            'success' => true,
            'data'    => $this->formatExpense($expense->fresh()),
        ];
    }

    // ─── Update ────────────────────────────────────────────────────────────────

    public function update(Expense $expense, array $data): array
    {
        $fields = [];
        if (array_key_exists('expense_name', $data)) $fields['expense_name'] = $data['expense_name'];
        if (array_key_exists('expense_date', $data)) $fields['expense_date'] = $data['expense_date'];
        if (array_key_exists('amount', $data))       $fields['amount']       = $data['amount'];
        if (array_key_exists('description', $data))  $fields['description']  = $data['description'];
        if (array_key_exists('is_monthly', $data))   $fields['is_monthly']   = $data['is_monthly'];

        $expense->update($fields);

        return [
            'success' => true,
            'data'    => $this->formatExpense($expense->fresh()),
        ];
    }

    // ─── Delete ────────────────────────────────────────────────────────────────

    public function delete(Expense $expense): void
    {
        $expense->delete();
    }

    public function formatExpensePublic(Expense $expense): array
    {
        return $this->formatExpense($expense);
    }
}
