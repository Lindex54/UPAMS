<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Beneficiary>
 */
class BeneficiaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => fake()->unique()->bothify('BEN-######'),
            'full_name_organization' => fake()->name(),
            'category' => 'Student Beneficiary',
            'telephone' => fake()->phoneNumber(),
            'email' => null,
            'national_id_given_names' => fake()->firstName(),
            'national_id_surname' => fake()->lastName(),
            'district_id' => District::factory(),
            'county_id' => fn (array $attributes) => County::factory()->create([
                'district_id' => $attributes['district_id'],
            ])->id,
            'sub_county_id' => fn (array $attributes) => SubCounty::factory()->create([
                'county_id' => $attributes['county_id'],
            ])->id,
            'parish_id' => fn (array $attributes) => Parish::factory()->create([
                'sub_county_id' => $attributes['sub_county_id'],
            ])->id,
            'village_id' => fn (array $attributes) => Village::factory()->create([
                'parish_id' => $attributes['parish_id'],
            ])->id,
            'campus_id' => Campus::factory(),
            'record_status' => 'Active',
        ];
    }
}
