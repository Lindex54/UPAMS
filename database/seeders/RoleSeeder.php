<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'System Administrator',
            'University Property / Estates Manager',
            'University Management',
            'Campus Property Officer',
            'Finance Officer',
        ] as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }
    }
}
