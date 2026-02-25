<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\FeeType;
use App\Models\House;
use App\Models\Payment;
use Carbon\Carbon;

class BillService
{
    // ─── Format ────────────────────────────────────────────────────────────────

    private function formatBill(Bill $bill): array
    {
        $bill->loadMissing(['house', 'resident', 'feeType', 'payments']);

        $latestPayment = $bill->payments->sortByDesc('payment_date')->first();

        return [
            'id'           => $bill->id,
            'house'        => $bill->house ? [
                'id'           => $bill->house->id,
                'house_number' => $bill->house->house_number,
                'address'      => $bill->house->address,
            ] : null,
            'resident'     => $bill->resident ? [
                'id'        => $bill->resident->id,
                'full_name' => $bill->resident->full_name,
            ] : null,
            'fee_type'     => $bill->feeType ? [
                'id'             => $bill->feeType->id,
                'fee_name'       => $bill->feeType->fee_name,
                'default_amount' => (int) $bill->feeType->default_amount,
            ] : null,
            'period_start'  => $bill->period_start?->format('Y-m-d'),
            'period_end'    => $bill->period_end?->format('Y-m-d'),
            'total_amount'  => (int) $bill->total_amount,
            'is_paid'       => $bill->is_paid,
            'payment_date'  => $latestPayment?->payment_date?->format('Y-m-d'),
            'created_at'    => $bill->created_at?->toIso8601String(),
        ];
    }

    // ─── Calculate months between two dates (inclusive, by month boundary) ─────

    private function calculateMonths(string $periodStart, string $periodEnd): int
    {
        $start = Carbon::parse($periodStart)->startOfMonth();
        $end   = Carbon::parse($periodEnd)->startOfMonth();

        return max(1, $start->diffInMonths($end) + 1);
    }

    // ─── List ──────────────────────────────────────────────────────────────────

    public function getAll(array $filters): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Bill::with(['house', 'resident', 'feeType', 'payments'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['house_id'])) {
            $query->where('house_id', $filters['house_id']);
        }

        if (isset($filters['is_paid']) && $filters['is_paid'] !== '') {
            $query->where('is_paid', filter_var($filters['is_paid'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['fee_type_id'])) {
            $query->where('fee_type_id', $filters['fee_type_id']);
        }

        if (!empty($filters['month']) && !empty($filters['year'])) {
            $query->whereMonth('period_start', $filters['month'])
                  ->whereYear('period_start', $filters['year']);
        } elseif (!empty($filters['year'])) {
            $query->whereYear('period_start', $filters['year']);
        }

        return $query->paginate(15);
    }

    // ─── Show ──────────────────────────────────────────────────────────────────

    public function getById(string $id): ?Bill
    {
        return Bill::with(['house', 'resident', 'feeType', 'payments'])->find($id);
    }

    // ─── Create ────────────────────────────────────────────────────────────────

    public function create(array $data): array
    {
        /** @var House $house */
        $house = House::find($data['house_id']);

        if (!$house->is_occupied) {
            return [
                'success' => false,
                'message' => 'Rumah sedang tidak dihuni, tagihan tidak dapat dibuat.',
                'code'    => 422,
            ];
        }

        // Get active resident
        $activeHistory = $house->houseHistories()
            ->where('is_active', true)
            ->first();

        $residentId = $activeHistory?->resident_id;

        // Duplicate check
        $duplicate = Bill::where('house_id', $data['house_id'])
            ->where('fee_type_id', $data['fee_type_id'])
            ->where('period_start', $data['period_start'])
            ->exists();

        if ($duplicate) {
            return [
                'success' => false,
                'message' => 'Tagihan dengan kombinasi rumah, jenis iuran, dan periode yang sama sudah ada.',
                'code'    => 422,
            ];
        }

        // Calculate total
        $feeType     = FeeType::find($data['fee_type_id']);
        $months      = $this->calculateMonths($data['period_start'], $data['period_end']);
        $totalAmount = (int) $feeType->default_amount * $months;

        $bill = Bill::create([
            'house_id'     => $data['house_id'],
            'resident_id'  => $residentId,
            'fee_type_id'  => $data['fee_type_id'],
            'period_start' => $data['period_start'],
            'period_end'   => $data['period_end'],
            'total_amount' => $totalAmount,
            'is_paid'      => false,
            'created_at'   => now(),
        ]);

        return [
            'success' => true,
            'data'    => $this->formatBill($bill->fresh(['house', 'resident', 'feeType', 'payments'])),
        ];
    }

    // ─── Update ────────────────────────────────────────────────────────────────

    public function update(Bill $bill, array $data): array
    {
        if ($bill->is_paid) {
            return [
                'success' => false,
                'message' => 'Tagihan yang sudah lunas tidak dapat diubah.',
                'code'    => 422,
            ];
        }

        $houseId     = $data['house_id']    ?? $bill->house_id;
        $feeTypeId   = $data['fee_type_id'] ?? $bill->fee_type_id;
        $periodStart = $data['period_start'] ?? $bill->period_start->format('Y-m-d');
        $periodEnd   = $data['period_end']   ?? $bill->period_end->format('Y-m-d');

        // If house changed, re-check occupied & update resident
        $residentId = $bill->resident_id;
        if (isset($data['house_id']) && $data['house_id'] !== $bill->house_id) {
            $house = House::find($houseId);
            if (!$house->is_occupied) {
                return [
                    'success' => false,
                    'message' => 'Rumah yang dipilih sedang tidak dihuni.',
                    'code'    => 422,
                ];
            }
            $activeHistory = $house->houseHistories()
                ->where('is_active', true)
                ->first();
            $residentId = $activeHistory?->resident_id;
        }

        // Duplicate check (exclude self)
        $duplicate = Bill::where('house_id', $houseId)
            ->where('fee_type_id', $feeTypeId)
            ->where('period_start', $periodStart)
            ->where('id', '!=', $bill->id)
            ->exists();

        if ($duplicate) {
            return [
                'success' => false,
                'message' => 'Tagihan dengan kombinasi rumah, jenis iuran, dan periode yang sama sudah ada.',
                'code'    => 422,
            ];
        }

        $feeType     = FeeType::find($feeTypeId);
        $months      = $this->calculateMonths($periodStart, $periodEnd);
        $totalAmount = (int) $feeType->default_amount * $months;

        $bill->update([
            'house_id'     => $houseId,
            'resident_id'  => $residentId,
            'fee_type_id'  => $feeTypeId,
            'period_start' => $periodStart,
            'period_end'   => $periodEnd,
            'total_amount' => $totalAmount,
        ]);

        return [
            'success' => true,
            'data'    => $this->formatBill($bill->fresh(['house', 'resident', 'feeType', 'payments'])),
        ];
    }

    // ─── Pay ───────────────────────────────────────────────────────────────────

    public function pay(Bill $bill, array $data): array
    {
        if ($bill->is_paid) {
            return [
                'success' => false,
                'message' => 'Tagihan ini sudah lunas.',
                'code'    => 422,
            ];
        }

        Payment::create([
            'bill_id'      => $bill->id,
            'payment_date' => $data['payment_date'],
            'amount_paid'  => $data['amount_paid'],
            'notes'        => $data['notes'] ?? null,
            'created_at'   => now(),
        ]);

        $bill->update(['is_paid' => true]);

        return [
            'success' => true,
            'data'    => $this->formatBill($bill->fresh(['house', 'resident', 'feeType', 'payments'])),
        ];
    }

    // ─── Delete ────────────────────────────────────────────────────────────────

    public function delete(Bill $bill): array
    {
        if ($bill->is_paid) {
            return [
                'success' => false,
                'message' => 'Tagihan yang sudah lunas tidak dapat dihapus.',
                'code'    => 422,
            ];
        }

        $bill->delete();

        return ['success' => true];
    }

    public function formatBillPublic(Bill $bill): array
    {
        return $this->formatBill($bill);
    }
}
