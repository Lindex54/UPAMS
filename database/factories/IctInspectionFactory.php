<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\ComputerLab;
use App\Models\IctInspection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IctInspection>
 */
class IctInspectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'computer_lab_id' => ComputerLab::factory(),
            'condition' => 'Good',
            'operational_status' => 'Operational',
            'findings' => fake()->sentence(),
            'inspected_at' => now(),
            'next_due_at' => today()->addMonths(3),
        ];
    }
}
