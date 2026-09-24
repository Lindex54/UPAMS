<?php

namespace Database\Factories;

use App\Models\Agreement;
use App\Models\Campus;
use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agreement>
 */
class AgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('AGR-######'),
            'title' => fake()->sentence(4),
            'campus_id' => Campus::factory(),
            'org_unit_id' => OrgUnit::factory(),
            'status' => 'Active',
            'starts_at' => today()->subYear(),
            'expires_at' => today()->addYear(),
        ];
    }
}
