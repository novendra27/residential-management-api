<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Bayar semua tagihan bulan Oktober & November 2025
        $paidPeriods = ['2025-10-01', '2025-11-01'];

        foreach ($paidPeriods as $periodStart) {
            $bills = Bill::where('period_start', $periodStart)
                ->where('is_paid', false)
                ->get();

            foreach ($bills as $bill) {
                $paymentDate = date('Y-m-d', strtotime($periodStart . ' +10 days'));

                Payment::firstOrCreate(
                    ['bill_id' => $bill->id],
                    [
                        'payment_date' => $paymentDate,
                        'amount_paid'  => $bill->total_amount,
                        'notes'        => 'Bayar tunai via RT',
                    ]
                );

                $bill->update(['is_paid' => true]);
            }
        }

        // Bayar sebagian tagihan Desember 2025 (hanya blok A & B)
        $partialBills = Bill::where('period_start', '2025-12-01')
            ->where('is_paid', false)
            ->whereHas('house', fn ($q) => $q->whereIn('house_number', ['A1','A2','A3','A4','A5','B1','B2','B3','B4']))
            ->get();

        foreach ($partialBills as $bill) {
            Payment::firstOrCreate(
                ['bill_id' => $bill->id],
                [
                    'payment_date' => '2025-12-15',
                    'amount_paid'  => $bill->total_amount,
                    'notes'        => 'Bayar tunai',
                ]
            );
            $bill->update(['is_paid' => true]);
        }

        // Tagihan tahunan yang sudah is_paid=true, buatkan record payment-nya
        $annualPaidBills = Bill::where('period_start', '2025-01-01')
            ->where('is_paid', true)
            ->whereDoesntHave('payments')
            ->get();

        foreach ($annualPaidBills as $bill) {
            Payment::create([
                'bill_id'      => $bill->id,
                'payment_date' => '2025-01-10',
                'amount_paid'  => $bill->total_amount,
                'notes'        => 'Bayar tahunan sekaligus',
            ]);
        }
    }
}
