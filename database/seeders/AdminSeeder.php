<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Pengguna::updateOrCreate(
            ['email' => 'admin@forestco.test'],
            [
                'nama' => 'Admin ForestCo',
                'no_telepon' => '081234567890',
                'peran' => 'admin',
                'password' => bcrypt('password'),
            ]
        );
    }
}
