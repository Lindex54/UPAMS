<?php

namespace Database\Factories;

use App\Models\UtilityBilling;
use App\Models\UtilityMeter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UtilityBilling>
 */
class UtilityBillingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('UTL-########'),
            'utility_meter_id' => UtilityMeter::factory(),
            'utility_type_id' => fn (array $attributes) => UtilityMeter::query()->findOrFail($attributes['utility_meter_id'])->utility_type_id,
            'campus_id' => fn (array $attributes) => UtilityMeter::query()->findOrFail($attributes['utility_meter_id'])->campus_id,
            'billing_period' => today()->startOfMonth(),
            'previous_reading' => 100,
            'current_reading' => 150,
            'consumption' => 50,
            'rate' => 100,
            'charge' => 5000,
            'payer_name' => 'Busitema University',
            'payment_status' => 'Unpaid',
            'is_abnormal' => false,
        ];
    }
}
