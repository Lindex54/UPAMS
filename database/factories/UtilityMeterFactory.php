<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\UtilityMeter;
use App\Models\UtilityType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UtilityMeter>
 */
class UtilityMeterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('MTR-########'),
            'utility_type_id' => UtilityType::factory(),
            'campus_id' => Campus::factory(),
            'meter_number' => fake()->unique()->bothify('UM-########'),
            'unit' => 'unit',
            'rate' => 100,
            'abnormal_threshold' => 500,
            'status' => 'Active',
        ];
    }
}
