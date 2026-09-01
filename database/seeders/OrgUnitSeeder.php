<?php

namespace Database\Seeders;

use App\Models\OrgUnit;
use Illuminate\Database\Seeder;

class OrgUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Facilities Office',
            'Transport Office',
            'Commercial Services',
            'Faculty of Agriculture',
            'Estates Office',
            'Finance Office',
        ] as $orgUnit) {
            OrgUnit::query()->firstOrCreate(['name' => $orgUnit]);
        }
    }
}
