<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@rt.com'],
            [
                'user_name' => 'admin',
                'password'  => Hash::make('password123'),
            ]
        );
    }
}
