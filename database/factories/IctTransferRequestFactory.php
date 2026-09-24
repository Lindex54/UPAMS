<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Campus;
use App\Models\IctTransferRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IctTransferRequest>
 */
class IctTransferRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('TRF-######'),
            'asset_id' => Asset::factory(),
            'from_campus_id' => Campus::factory(),
            'to_campus_id' => Campus::factory(),
            'status' => 'Requested',
            'reason' => fake()->sentence(),
            'requested_at' => now(),
        ];
    }
}
