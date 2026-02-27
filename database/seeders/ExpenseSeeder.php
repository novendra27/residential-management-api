<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $expenses = [
            // Pengeluaran rutin bulanan (is_monthly = true)
            ['expense_name' => 'Gaji Satpam',        'expense_date' => '2025-10-05', 'amount' => 1500000, 'description' => 'Gaji satpam bulan Oktober 2025',   'is_monthly' => true],
            ['expense_name' => 'Gaji Satpam',        'expense_date' => '2025-11-05', 'amount' => 1500000, 'description' => 'Gaji satpam bulan November 2025',   'is_monthly' => true],
            ['expense_name' => 'Gaji Satpam',        'expense_date' => '2025-12-05', 'amount' => 1500000, 'description' => 'Gaji satpam bulan Desember 2025',   'is_monthly' => true],
            ['expense_name' => 'Gaji Satpam',        'expense_date' => '2026-01-05', 'amount' => 1500000, 'description' => 'Gaji satpam bulan Januari 2026',    'is_monthly' => true],
            ['expense_name' => 'Gaji Satpam',        'expense_date' => '2026-02-05', 'amount' => 1500000, 'description' => 'Gaji satpam bulan Februari 2026',   'is_monthly' => true],
            ['expense_name' => 'Token Listrik Pos',  'expense_date' => '2025-10-03', 'amount' =>  200000, 'description' => 'Token listrik pos satpam Oktober',  'is_monthly' => true],
            ['expense_name' => 'Token Listrik Pos',  'expense_date' => '2025-11-03', 'amount' =>  200000, 'description' => 'Token listrik pos satpam November', 'is_monthly' => true],
            ['expense_name' => 'Token Listrik Pos',  'expense_date' => '2025-12-03', 'amount' =>  200000, 'description' => 'Token listrik pos satpam Desember', 'is_monthly' => true],
            ['expense_name' => 'Token Listrik Pos',  'expense_date' => '2026-01-03', 'amount' =>  200000, 'description' => 'Token listrik pos satpam Januari',  'is_monthly' => true],
            ['expense_name' => 'Token Listrik Pos',  'expense_date' => '2026-02-03', 'amount' =>  200000, 'description' => 'Token listrik pos satpam Februari', 'is_monthly' => true],
            ['expense_name' => 'Alat Kebersihan',    'expense_date' => '2025-10-07', 'amount' =>  150000, 'description' => 'Sapu, pel, kantong sampah Oktober',  'is_monthly' => true],
            ['expense_name' => 'Alat Kebersihan',    'expense_date' => '2025-11-07', 'amount' =>  150000, 'description' => 'Sapu, pel, kantong sampah November', 'is_monthly' => true],
            ['expense_name' => 'Alat Kebersihan',    'expense_date' => '2025-12-07', 'amount' =>  150000, 'description' => 'Sapu, pel, kantong sampah Desember', 'is_monthly' => true],
            ['expense_name' => 'Alat Kebersihan',    'expense_date' => '2026-01-07', 'amount' =>  150000, 'description' => 'Sapu, pel, kantong sampah Januari',  'is_monthly' => true],
            ['expense_name' => 'Alat Kebersihan',    'expense_date' => '2026-02-07', 'amount' =>  150000, 'description' => 'Sapu, pel, kantong sampah Februari', 'is_monthly' => true],
            // Pengeluaran insidental (is_monthly = false)
            ['expense_name' => 'Perbaikan Jalan Blok A', 'expense_date' => '2025-10-20', 'amount' =>  750000, 'description' => 'Pengecoran jalan depan blok A yang berlubang',    'is_monthly' => false],
            ['expense_name' => 'Pengecatan Gerbang',     'expense_date' => '2025-11-15', 'amount' =>  400000, 'description' => 'Cat ulang gerbang perumahan',                     'is_monthly' => false],
            ['expense_name' => 'Perbaikan Lampu Jalan',  'expense_date' => '2025-12-10', 'amount' =>  300000, 'description' => 'Ganti 3 lampu jalan yang mati di blok C dan D',   'is_monthly' => false],
            ['expense_name' => 'Pembelian Tanaman',      'expense_date' => '2026-01-18', 'amount' =>  250000, 'description' => 'Tanaman hias untuk taman perumahan',              'is_monthly' => false],
            ['expense_name' => 'Perbaikan Saluran Air',  'expense_date' => '2026-02-12', 'amount' =>  600000, 'description' => 'Perbaikan gorong-gorong blok B yang tersumbat',   'is_monthly' => false],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense);
        }
    }
}
