<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FeeTypeSeeder::class,
            UserSeeder::class,
            HouseSeeder::class,
            ResidentSeeder::class,
            ResidentHouseHistorySeeder::class,
            BillSeeder::class,
            PaymentSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
