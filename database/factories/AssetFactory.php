<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetType;
use App\Models\Campus;
use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('AST-######'),
            'name' => fake()->words(3, true),
            'campus_id' => Campus::factory(),
            'org_unit_id' => OrgUnit::factory(),
            'asset_category_id' => AssetCategory::factory(),
            'asset_type_id' => fn (array $attributes) => AssetType::factory()->create([
                'asset_category_id' => $attributes['asset_category_id'],
            ])->id,
            'status' => 'Active',
            'condition' => 'Good',
            'acquired_at' => fake()->dateTimeBetween('-8 years', 'now'),
            'latitude' => fake()->randomFloat(7, -1.5, 4.3),
            'longitude' => fake()->randomFloat(7, 29.5, 35),
        ];
    }
}
