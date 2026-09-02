<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('INV-########'),
            'beneficiary_id' => Beneficiary::factory(),
            'campus_id' => Campus::factory(),
            'issue_date' => today(),
            'due_date' => today()->addDays(30),
            'description' => fake()->sentence(),
            'subtotal' => 100000,
            'tax_amount' => 0,
            'total_amount' => 100000,
            'status' => 'Issued',
        ];
    }
}
