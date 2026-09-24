<?php

namespace Database\Factories;

use App\Models\Campus;
use App\Models\Document;
use App\Models\OrgUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('DOC-######'),
            'title' => fake()->sentence(4),
            'document_type' => 'Licence / Certificate',
            'campus_id' => Campus::factory(),
            'org_unit_id' => OrgUnit::factory(),
            'status' => 'Current',
            'issued_at' => today()->subYear(),
            'expires_at' => today()->addYear(),
        ];
    }
}
