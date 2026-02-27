<?php

namespace Database\Seeders;

use App\Models\FeeType;
use Illuminate\Database\Seeder;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        $feeTypes = [
            [
                'fee_name'       => 'Satpam',
                'default_amount' => 100000,
            ],
            [
                'fee_name'       => 'Kebersihan',
                'default_amount' => 15000,
            ],
        ];

        foreach ($feeTypes as $feeType) {
            FeeType::firstOrCreate(
                ['fee_name' => $feeType['fee_name']],
                ['default_amount' => $feeType['default_amount']]
            );
        }
    }
}
