<?php

namespace Database\Factories;

use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BeneficiaryDocument>
 */
class BeneficiaryDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'beneficiary_id' => Beneficiary::factory(),
            'document_type' => 'form_18',
            'title' => 'Form 18',
            'file_path' => 'beneficiaries/documents/'.fake()->uuid().'.pdf',
            'original_name' => 'form-18.pdf',
            'mime_type' => 'application/pdf',
            'size' => 120000,
        ];
    }

    /**
     * A user-named supporting document outside the standard list.
     */
    public function other(string $title = 'Trading Licence'): static
    {
        return $this->state(fn (array $attributes): array => [
            'document_type' => BeneficiaryDocument::TYPE_OTHER,
            'title' => $title,
            'original_name' => str($title)->slug().'.pdf',
        ]);
    }
}
