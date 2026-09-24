<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Buildings & Spaces', 'Equipment & Machinery', 'Furniture & Fittings', 'Vehicles', 'Land & Other Property', 'ICT Equipment'] as $category) {
            AssetCategory::query()->firstOrCreate(['name' => $category]);
        }
    }
}
