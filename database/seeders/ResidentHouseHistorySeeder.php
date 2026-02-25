<?php

namespace Database\Seeders;

use App\Models\House;
use App\Models\Resident;
use App\Models\ResidentHouseHistory;
use Illuminate\Database\Seeder;

class ResidentHouseHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Mapping: house_number => [resident phone, move_in, move_out, is_active]
        $histories = [
            // Penghuni aktif saat ini
            ['A1', '081234567801', '2023-01-01', null,         true],
            ['A2', '081234567802', '2022-06-01', null,         true],
            ['A3', '081234567803', '2021-03-15', null,         true],
            ['A4', '081234567804', '2023-07-01', null,         true],
            ['B1', '081234567805', '2020-01-01', null,         true],
            ['B2', '081234567806', '2022-09-01', null,         true],
            ['B3', '081234567807', '2024-01-01', null,         true],
            ['C1', '081234567808', '2021-05-01', null,         true],
            ['C2', '081234567809', '2020-08-01', null,         true],
            ['C3', '081234567810', '2023-03-01', null,         true],
            ['C4', '081234567811', '2022-11-01', null,         true],
            ['C5', '081234567812', '2024-03-01', null,         true],
            ['D1', '081234567813', '2021-01-01', null,         true],
            ['D2', '081234567814', '2022-04-01', null,         true],
            ['D3', '081234567815', '2023-10-01', null,         true],
            // Penghuni kontrak aktif
            ['A5', '081234567816', '2025-06-01', null,         true],  // kontrak, A5 dihuni kontrak
            ['B4', '081234567817', '2025-08-01', null,         true],  // kontrak, B4 dihuni kontrak
            // Mantan penghuni (sudah pindah)
            ['A1', '081234567818', '2019-01-01', '2022-12-31', false], // A1 pernah dihuni Joko sebelum Budi
            ['C5', '081234567819', '2020-01-01', '2024-02-28', false], // C5 pernah dihuni Sri sebelum Nadia
        ];

        foreach ($histories as [$houseNum, $phone, $moveIn, $moveOut, $isActive]) {
            $house    = House::where('house_number', $houseNum)->first();
            $resident = Resident::where('phone_number', $phone)->first();

            if (!$house || !$resident) continue;

            ResidentHouseHistory::firstOrCreate(
                [
                    'house_id'    => $house->id,
                    'resident_id' => $resident->id,
                    'move_in_date' => $moveIn,
                ],
                [
                    'move_out_date' => $moveOut,
                    'is_active'     => $isActive,
                ]
            );
        }

        // Update is_occupied untuk rumah yang dihuni kontrak (A5, B4)
        House::whereIn('house_number', ['A5', 'B4'])->update(['is_occupied' => true]);
    }
}
