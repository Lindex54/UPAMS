<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\ComputerLab;
use App\Models\IctEquipmentAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IctEquipmentAssignment>
 */
class IctEquipmentAssignmentFactory extends Factory
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
            'custodian' => fake()->name(),
            'assigned_at' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
