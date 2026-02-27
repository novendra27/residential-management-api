<?php

namespace Database\Seeders;

use App\Models\Resident;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ResidentSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan path placeholder untuk ktp_photo (tidak upload file asli)
        $placeholder = 'ktp/placeholder.jpg';

        $residents = [
            // Penghuni tetap aktif
            ['full_name' => 'Budi Santoso',       'phone_number' => '081234567801', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Siti Rahayu',         'phone_number' => '081234567802', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Ahmad Fauzi',         'phone_number' => '081234567803', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Dewi Lestari',        'phone_number' => '081234567804', 'is_contract' => false, 'is_married' => false],
            ['full_name' => 'Hendra Gunawan',      'phone_number' => '081234567805', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Rina Wulandari',      'phone_number' => '081234567806', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Doni Prasetyo',       'phone_number' => '081234567807', 'is_contract' => false, 'is_married' => false],
            ['full_name' => 'Yuli Astuti',         'phone_number' => '081234567808', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Bambang Supriyadi',   'phone_number' => '081234567809', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Fitri Handayani',     'phone_number' => '081234567810', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Rudi Hermawan',       'phone_number' => '081234567811', 'is_contract' => false, 'is_married' => false],
            ['full_name' => 'Nadia Putri',         'phone_number' => '081234567812', 'is_contract' => false, 'is_married' => false],
            ['full_name' => 'Agus Setiawan',       'phone_number' => '081234567813', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Maya Sari',           'phone_number' => '081234567814', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Eko Cahyono',         'phone_number' => '081234567815', 'is_contract' => false, 'is_married' => true],
            // Penghuni kontrak aktif
            ['full_name' => 'Kevin Ardian',        'phone_number' => '081234567816', 'is_contract' => true,  'is_married' => false],
            ['full_name' => 'Lisa Permata',        'phone_number' => '081234567817', 'is_contract' => true,  'is_married' => false],
            // Mantan penghuni (sudah pindah)
            ['full_name' => 'Joko Widodo',         'phone_number' => '081234567818', 'is_contract' => false, 'is_married' => true],
            ['full_name' => 'Sri Mulyani',         'phone_number' => '081234567819', 'is_contract' => true,  'is_married' => false],
        ];

        foreach ($residents as $resident) {
            Resident::firstOrCreate(
                ['phone_number' => $resident['phone_number']],
                array_merge($resident, ['ktp_photo' => $placeholder])
            );
        }
    }
}
