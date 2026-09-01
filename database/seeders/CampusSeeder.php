<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Main Campus',
            'Nagongera Campus',
            'Namasagali Campus',
            'Arapai Campus',
            'Mbale Campus',
            'Pallisa Campus',
        ] as $campus) {
            Campus::query()->firstOrCreate(['name' => $campus]);
        }
    }
}
