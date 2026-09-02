<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'receipt_number' => fake()->unique()->bothify('RCT-########'),
            'invoice_id' => Invoice::factory(),
            'beneficiary_id' => fn (array $attributes) => Invoice::query()->findOrFail($attributes['invoice_id'])->beneficiary_id,
            'campus_id' => fn (array $attributes) => Invoice::query()->findOrFail($attributes['invoice_id'])->campus_id,
            'paid_at' => now(),
            'amount' => 50000,
            'payment_method' => 'Bank Transfer',
            'payment_reference' => fake()->unique()->bothify('TXN-########'),
            'status' => 'Recorded',
        ];
    }
}
