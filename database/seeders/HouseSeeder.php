<?php

namespace Database\Seeders;

use App\Models\House;
use Illuminate\Database\Seeder;

class HouseSeeder extends Seeder
{
    public function run(): void
    {
        $houses = [
            // Blok A (5 rumah)
            ['house_number' => 'A1', 'address' => 'Jl. Melati Blok A No. 1',  'is_occupied' => true],
            ['house_number' => 'A2', 'address' => 'Jl. Melati Blok A No. 2',  'is_occupied' => true],
            ['house_number' => 'A3', 'address' => 'Jl. Melati Blok A No. 3',  'is_occupied' => true],
            ['house_number' => 'A4', 'address' => 'Jl. Melati Blok A No. 4',  'is_occupied' => true],
            ['house_number' => 'A5', 'address' => 'Jl. Melati Blok A No. 5',  'is_occupied' => false],
            // Blok B (5 rumah)
            ['house_number' => 'B1', 'address' => 'Jl. Melati Blok B No. 1',  'is_occupied' => true],
            ['house_number' => 'B2', 'address' => 'Jl. Melati Blok B No. 2',  'is_occupied' => true],
            ['house_number' => 'B3', 'address' => 'Jl. Melati Blok B No. 3',  'is_occupied' => true],
            ['house_number' => 'B4', 'address' => 'Jl. Melati Blok B No. 4',  'is_occupied' => false],
            ['house_number' => 'B5', 'address' => 'Jl. Melati Blok B No. 5',  'is_occupied' => false],
            // Blok C (5 rumah)
            ['house_number' => 'C1', 'address' => 'Jl. Melati Blok C No. 1',  'is_occupied' => true],
            ['house_number' => 'C2', 'address' => 'Jl. Melati Blok C No. 2',  'is_occupied' => true],
            ['house_number' => 'C3', 'address' => 'Jl. Melati Blok C No. 3',  'is_occupied' => true],
            ['house_number' => 'C4', 'address' => 'Jl. Melati Blok C No. 4',  'is_occupied' => true],
            ['house_number' => 'C5', 'address' => 'Jl. Melati Blok C No. 5',  'is_occupied' => true],
            // Blok D (5 rumah)
            ['house_number' => 'D1', 'address' => 'Jl. Melati Blok D No. 1',  'is_occupied' => true],
            ['house_number' => 'D2', 'address' => 'Jl. Melati Blok D No. 2',  'is_occupied' => true],
            ['house_number' => 'D3', 'address' => 'Jl. Melati Blok D No. 3',  'is_occupied' => true],
            ['house_number' => 'D4', 'address' => 'Jl. Melati Blok D No. 4',  'is_occupied' => false],
            ['house_number' => 'D5', 'address' => 'Jl. Melati Blok D No. 5',  'is_occupied' => false],
        ];

        foreach ($houses as $house) {
            House::firstOrCreate(['house_number' => $house['house_number']], $house);
        }
    }
}
