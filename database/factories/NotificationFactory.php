<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('NTF-########'),
            'campus_id' => Campus::factory(),
            'event_type' => 'General',
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'channels' => ['in_system'],
            'channel_status' => ['in_system' => 'Sent'],
            'status' => 'Sent',
            'sent_at' => now(),
        ];
    }
}
