<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ReportService
{
    // ─── Summary: 12-month aggregation for a given year ───────────────────

    public function summary(int $year): array
    {
        // Aggregate payments per month
        $incomes = Payment::selectRaw('MONTH(payment_date) as month, SUM(amount_paid) as total')
            ->whereYear('payment_date', $year)
            ->groupByRaw('MONTH(payment_date)')
            ->pluck('total', 'month')
            ->toArray();

        // Aggregate expenses per month
        $expenses = Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->groupByRaw('MONTH(expense_date)')
            ->pluck('total', 'month')
            ->toArray();

        $result = [];
        for ($month = 1; $month <= 12; $month++) {
            $totalIncome  = (int) ($incomes[$month]  ?? 0);
            $totalExpense = (int) ($expenses[$month] ?? 0);
            $result[] = [
                'month'           => $month,
                'year'            => $year,
                'total_income'    => $totalIncome,
                'total_expense'   => $totalExpense,
                'ending_balance'  => $totalIncome - $totalExpense,
            ];
        }

        return $result;
    }

    // ─── Balances: detailed breakdown for a specific month/year ──────────────

    public function balances(int $month, int $year): array
    {
        // Payments in this month
        $payments = Payment::with(['bill.house', 'bill.resident', 'bill.feeType'])
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->orderBy('payment_date')
            ->get();

        // Expenses in this month
        $expenses = Expense::whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->orderBy('expense_date')
            ->get();

        $totalIncome  = (int) $payments->sum('amount_paid');
        $totalExpense = (int) $expenses->sum('amount');

        $incomes = $payments->map(function (Payment $p) {
            $bill = $p->bill;
            return [
                'payment_id'   => $p->id,
                'payment_date' => $p->payment_date?->format('Y-m-d'),
                'amount_paid'  => (int) $p->amount_paid,
                'notes'        => $p->notes,
                'bill'         => $bill ? [
                    'period_start' => $bill->period_start?->format('Y-m-d'),
                    'period_end'   => $bill->period_end?->format('Y-m-d'),
                    'fee_type'     => $bill->feeType?->fee_name,
                ] : null,
                'house'    => $bill?->house    ? ['house_number' => $bill->house->house_number]          : null,
                'resident' => $bill?->resident ? ['full_name'    => $bill->resident->full_name]           : null,
            ];
        })->values()->toArray();

        $expenseList = $expenses->map(fn (Expense $e) => [
            'id'           => $e->id,
            'expense_name' => $e->expense_name,
            'expense_date' => $e->expense_date?->format('Y-m-d'),
            'amount'       => (int) $e->amount,
            'description'  => $e->description,
            'is_monthly'   => $e->is_monthly,
        ])->values()->toArray();

        return [
            'month'          => $month,
            'year'           => $year,
            'total_income'   => $totalIncome,
            'total_expense'  => $totalExpense,
            'ending_balance' => $totalIncome - $totalExpense,
            'incomes'        => $incomes,
            'expenses'       => $expenseList,
        ];
    }
}
