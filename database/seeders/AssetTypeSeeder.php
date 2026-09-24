<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\AssetType;
use Illuminate\Database\Seeder;

class AssetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Buildings & Spaces' => ['Academic Building', 'Administrative Building', 'Room / Space'],
            'Equipment & Machinery' => ['General Equipment', 'Plant & Machinery', 'Laboratory Equipment'],
            'Furniture & Fittings' => ['Furniture', 'Fixtures & Fittings'],
            'Vehicles' => ['Motor Vehicle', 'Motorcycle', 'Agricultural Vehicle'],
            'Land & Other Property' => ['Land Parcel', 'Commercial Property', 'Agricultural Property'],
            'ICT Equipment' => ['Computer Equipment', 'Network Equipment', 'Communication Equipment'],
        ];

        foreach ($types as $categoryName => $typeNames) {
            $category = AssetCategory::query()->firstOrCreate(['name' => $categoryName]);
            foreach ($typeNames as $typeName) {
                AssetType::query()->firstOrCreate(['asset_category_id' => $category->id, 'name' => $typeName]);
            }
        }
    }
}
