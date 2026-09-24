<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ComputerLab>
 */
class ComputerLabFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->bothify('Computer Lab ##??'),
            'campus_id' => Campus::factory(),
            'org_unit_id' => OrgUnit::factory(),
            'building' => fake()->streetName().' Block',
            'room' => fake()->bothify('Room ###'),
            'capacity' => fake()->numberBetween(20, 80),
        ];
    }
}
