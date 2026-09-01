<?php

namespace Database\Factories;

use App\Models\County;
use App\Models\SubCounty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubCounty>
 */
class SubCountyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'county_id' => County::factory(),
            'name' => fake()->unique()->words(2, true).' Sub-county',
            'code' => fake()->unique()->bothify('SC-####'),
            'is_active' => true,
        ];
    }
}
