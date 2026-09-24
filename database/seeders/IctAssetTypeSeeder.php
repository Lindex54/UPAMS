<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\AssetType;
use Illuminate\Database\Seeder;

class IctAssetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = AssetCategory::query()->firstOrCreate(['name' => 'ICT Equipment']);

        foreach (['Desktop Computer', 'Laptop', 'Server', 'Monitor', 'Printer / Scanner', 'Network Equipment', 'Projector', 'UPS / Power Equipment', 'ICT Peripheral'] as $name) {
            AssetType::query()->firstOrCreate(['asset_category_id' => $category->id, 'name' => $name]);
        }
    }
}
