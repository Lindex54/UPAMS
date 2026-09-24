<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use App\Models\AssetType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetType>
 */
class AssetTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_category_id' => AssetCategory::factory(),
            'name' => fake()->unique()->words(2, true),
        ];
    }
}
