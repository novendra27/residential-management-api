<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;

class PaymentService
{
    // ─── Format ────────────────────────────────────────────────────────────────

    private function formatPayment(Payment $payment): array
    {
        $payment->loadMissing(['bill.house', 'bill.resident', 'bill.feeType']);

        $bill = $payment->bill;

        return [
            'id'           => $payment->id,
            'payment_date' => $payment->payment_date?->format('Y-m-d'),
            'amount_paid'  => (int) $payment->amount_paid,
            'notes'        => $payment->notes,
            'created_at'   => $payment->created_at?->toIso8601String(),
            'bill'         => $bill ? [
                'id'           => $bill->id,
                'period_start' => $bill->period_start?->format('Y-m-d'),
                'period_end'   => $bill->period_end?->format('Y-m-d'),
                'total_amount' => (int) $bill->total_amount,
                'is_paid'      => $bill->is_paid,
                'fee_type'     => $bill->feeType ? [
                    'id'       => $bill->feeType->id,
                    'fee_name' => $bill->feeType->fee_name,
                ] : null,
                'house'    => $bill->house ? [
                    'id'           => $bill->house->id,
                    'house_number' => $bill->house->house_number,
                ] : null,
                'resident' => $bill->resident ? [
                    'id'        => $bill->resident->id,
                    'full_name' => $bill->resident->full_name,
                ] : null,
            ] : null,
        ];
    }

    // ─── Create ────────────────────────────────────────────────────────────────

    public function create(array $data): array
    {
        /** @var Bill $bill */
        $bill = Bill::find($data['bill_id']);

        if ($bill->is_paid) {
            return [
                'success' => false,
                'message' => 'Tagihan ini sudah lunas.',
                'code'    => 422,
            ];
        }

        $payment = Payment::create([
            'bill_id'      => $bill->id,
            'payment_date' => $data['payment_date'],
            'amount_paid'  => $data['amount_paid'],
            'notes'        => $data['notes'] ?? null,
            'created_at'   => now(),
        ]);

        $bill->update(['is_paid' => true]);

        return [
            'success' => true,
            'data'    => $this->formatPayment($payment->fresh(['bill.house', 'bill.resident', 'bill.feeType'])),
        ];
    }

    public function formatPaymentPublic(Payment $payment): array
    {
        return $this->formatPayment($payment);
    }
}
