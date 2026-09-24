<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Campus;
use App\Models\MaintenanceRequest;
use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRequest>
 */
class MaintenanceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('MNT-######'),
            'title' => fake()->sentence(4),
            'asset_id' => Asset::factory(),
            'campus_id' => Campus::factory(),
            'org_unit_id' => OrgUnit::factory(),
            'priority' => 'Medium',
            'status' => 'Problem Reported',
            'target_completion_at' => today()->addMonth(),
        ];
    }
}
