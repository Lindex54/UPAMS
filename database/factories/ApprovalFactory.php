<?php

namespace Database\Factories;

use App\Models\Approval;
use App\Models\Campus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Approval>
 */
class ApprovalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('APR-########'),
            'campus_id' => Campus::factory(),
            'approval_type' => 'Allocation',
            'subject_reference' => fake()->bothify('AST-####'),
            'title' => fake()->sentence(4),
            'request_details' => fake()->paragraph(),
            'status' => 'Pending',
        ];
    }
}
