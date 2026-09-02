<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationTemplate>
 */
class NotificationTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'event_type' => 'General',
            'subject' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'channels' => ['in_system'],
            'is_active' => true,
        ];
    }
}
