<?php

namespace Database\Factories;

use App\Models\Parish;
use App\Models\SubCounty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Parish>
 */
class ParishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sub_county_id' => SubCounty::factory(),
            'name' => fake()->unique()->words(2, true).' Parish',
            'code' => fake()->unique()->bothify('P-####'),
            'is_active' => true,
        ];
    }
}
