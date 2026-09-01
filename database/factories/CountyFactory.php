<?php

namespace Database\Factories;

use App\Models\County;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<County>
 */
class CountyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'district_id' => District::factory(),
            'name' => fake()->unique()->words(2, true).' County',
            'code' => fake()->unique()->bothify('C-####'),
            'is_active' => true,
        ];
    }
}
