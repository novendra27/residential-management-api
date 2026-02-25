<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\FeeType;
use App\Models\House;
use App\Models\ResidentHouseHistory;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $satpam     = FeeType::where('fee_name', 'Satpam')->first();
        $kebersihan = FeeType::where('fee_name', 'Kebersihan')->first();

        // Buat tagihan untuk setiap rumah yang is_occupied = true
        // Periode: Oktober 2025, November 2025, Desember 2025, Januari 2026, Februari 2026
        $periods = [
            ['2025-10-01', '2025-10-31'],
            ['2025-11-01', '2025-11-30'],
            ['2025-12-01', '2025-12-31'],
            ['2026-01-01', '2026-01-31'],
            ['2026-02-01', '2026-02-28'],
        ];

        $occupiedHouses = House::where('is_occupied', true)->get();

        foreach ($occupiedHouses as $house) {
            $activeHistory = ResidentHouseHistory::where('house_id', $house->id)
                ->where('is_active', true)
                ->first();

            $residentId = $activeHistory?->resident_id;

            foreach ($periods as [$start, $end]) {
                foreach ([$satpam, $kebersihan] as $feeType) {
                    $existing = Bill::where('house_id', $house->id)
                        ->where('fee_type_id', $feeType->id)
                        ->where('period_start', $start)
                        ->first();

                    if (!$existing) {
                        Bill::create([
                            'house_id'     => $house->id,
                            'resident_id'  => $residentId,
                            'fee_type_id'  => $feeType->id,
                            'period_start' => $start,
                            'period_end'   => $end,
                            'total_amount' => $feeType->default_amount,
                            'is_paid'      => false,
                        ]);
                    }
                }
            }
        }

        // Contoh tagihan tahunan (1 tahun sekaligus) untuk beberapa rumah
        $annualHouses = ['A1', 'B1', 'C1'];
        foreach ($annualHouses as $num) {
            $house = House::where('house_number', $num)->first();
            if (!$house) continue;

            $activeHistory = ResidentHouseHistory::where('house_id', $house->id)
                ->where('is_active', true)->first();
            $residentId = $activeHistory?->resident_id;

            foreach ([$satpam, $kebersihan] as $feeType) {
                $existing = Bill::where('house_id', $house->id)
                    ->where('fee_type_id', $feeType->id)
                    ->where('period_start', '2025-01-01')
                    ->first();

                if (!$existing) {
                    Bill::create([
                        'house_id'     => $house->id,
                        'resident_id'  => $residentId,
                        'fee_type_id'  => $feeType->id,
                        'period_start' => '2025-01-01',
                        'period_end'   => '2025-09-30',
                        'total_amount' => $feeType->default_amount * 9,
                        'is_paid'      => true,
                    ]);
                }
            }
        }
    }
}
