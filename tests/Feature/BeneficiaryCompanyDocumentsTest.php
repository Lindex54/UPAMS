<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use App\Models\Campus;
use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BeneficiaryCompanyDocumentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    public function test_create_form_shows_optional_company_fields_and_document_slots(): void
    {
        $response = $this->actingAs(User::factory()->create())->get(route('operations.beneficiaries.create'))->assertOk();

        foreach (['Company Name', 'Registration Number', 'TIN Number', 'Tax Returns', 'Name of the Directors', 'Name of the Owners', 'Local Council Government Permit', 'Certificate of Incorporation', 'Form 18', 'Form 20', 'Form 1', 'Annual Returns', 'Other documents'] as $label) {
            $response->assertSee($label);
        }

        foreach (['company_name', 'registration_number', 'tin', 'tax_returns', 'director_names', 'owner_names', 'local_council_permit'] as $field) {
            $response->assertDontSee('name="'.$field.'" required', false);
        }
    }

    public function test_beneficiary_can_be_saved_without_any_company_details_or_documents(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('operations.beneficiaries.store'), $this->validPayload())
            ->assertSessionHasNoErrors();

        $beneficiary = Beneficiary::query()->firstOrFail();
        $this->assertNull($beneficiary->company_name);
        $this->assertSame(0, $beneficiary->documents()->count());
    }

    public function test_company_details_and_documents_are_stored_privately(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('operations.beneficiaries.store'), $this->validPayload([
            'company_name' => 'Tororo Agro Supplies Ltd',
            'registration_number' => '80020001234567',
            'tin' => '1000123456',
            'tax_returns' => 'Filed for FY 2025/26',
            'director_names' => "Jane Akello\nPeter Okello",
            'owner_names' => 'Jane Akello',
            'local_council_permit' => 'LC-TOR-2026-118',
            'documents' => [
                'certificate_of_incorporation' => UploadedFile::fake()->create('incorporation.pdf', 200, 'application/pdf'),
                'form_18' => UploadedFile::fake()->create('form-18.pdf', 120, 'application/pdf'),
            ],
            'other_documents' => [
                ['name' => 'Trading Licence', 'file' => UploadedFile::fake()->create('licence.pdf', 80, 'application/pdf')],
            ],
        ]))->assertSessionHasNoErrors();

        $beneficiary = Beneficiary::query()->firstOrFail();
        $this->assertSame('Tororo Agro Supplies Ltd', $beneficiary->company_name);
        $this->assertSame("Jane Akello\nPeter Okello", $beneficiary->director_names);
        $this->assertEqualsCanonicalizing(['certificate_of_incorporation', 'form_18', 'other'], $beneficiary->documents()->pluck('document_type')->all());

        $licence = $beneficiary->documents()->where('document_type', 'other')->firstOrFail();
        $this->assertSame('Trading Licence', $licence->title);
        $this->assertSame('licence.pdf', $licence->original_name);
        $this->assertSame($user->id, $licence->uploaded_by);
        Storage::disk('local')->assertExists($licence->file_path);
    }

    public function test_other_documents_must_be_named(): void
    {
        $this->actingAs(User::factory()->create())->post(route('operations.beneficiaries.store'), $this->validPayload([
            'other_documents' => [
                ['name' => '', 'file' => UploadedFile::fake()->create('unnamed.pdf', 50, 'application/pdf')],
            ],
        ]))->assertSessionHasErrors('other_documents.0.name');

        $this->assertDatabaseCount('beneficiaries', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_document_files_must_be_documents_or_images(): void
    {
        $this->actingAs(User::factory()->create())->post(route('operations.beneficiaries.store'), $this->validPayload([
            'documents' => ['form_20' => UploadedFile::fake()->create('script.exe', 10, 'application/octet-stream')],
        ]))->assertSessionHasErrors('documents.form_20');
    }

    public function test_reuploading_a_standard_document_replaces_the_previous_file(): void
    {
        $beneficiary = Beneficiary::factory()->create();
        Storage::disk('local')->put('beneficiaries/documents/old-form-1.pdf', 'old');
        $previous = BeneficiaryDocument::factory()->create(['beneficiary_id' => $beneficiary->id, 'document_type' => 'form_1', 'title' => 'Form 1', 'file_path' => 'beneficiaries/documents/old-form-1.pdf']);

        $this->actingAs(User::factory()->create())->put(route('operations.beneficiaries.update', $beneficiary), $this->updatePayload($beneficiary, [
            'documents' => ['form_1' => UploadedFile::fake()->create('new-form-1.pdf', 90, 'application/pdf')],
        ]))->assertSessionHasNoErrors();

        $this->assertModelMissing($previous);
        Storage::disk('local')->assertMissing('beneficiaries/documents/old-form-1.pdf');
        $this->assertSame('new-form-1.pdf', $beneficiary->documents()->where('document_type', 'form_1')->sole()->original_name);
    }

    public function test_stored_documents_can_be_removed_and_only_from_their_own_beneficiary(): void
    {
        $beneficiary = Beneficiary::factory()->create();
        $other = BeneficiaryDocument::factory()->other('Lease Agreement')->create(['beneficiary_id' => $beneficiary->id]);
        Storage::disk('local')->put($other->file_path, 'lease');
        $foreignDocument = BeneficiaryDocument::factory()->create();

        $this->actingAs(User::factory()->create())->put(route('operations.beneficiaries.update', $beneficiary), $this->updatePayload($beneficiary, [
            'remove_documents' => [$other->id, $foreignDocument->id],
        ]))->assertSessionHasNoErrors();

        $this->assertModelMissing($other);
        $this->assertModelExists($foreignDocument);
        Storage::disk('local')->assertMissing($other->file_path);
    }

    public function test_edit_page_lists_documents_and_they_download_with_their_original_name(): void
    {
        $user = User::factory()->create();
        $beneficiary = Beneficiary::factory()->create();
        $document = BeneficiaryDocument::factory()->other('Fire Safety Certificate')->create(['beneficiary_id' => $beneficiary->id, 'original_name' => 'fire-safety.pdf']);
        Storage::disk('local')->put($document->file_path, 'certificate');

        $this->actingAs($user)->get(route('operations.beneficiaries.edit', $beneficiary->reference ?? $beneficiary->id))
            ->assertOk()
            ->assertSee('Fire Safety Certificate')
            ->assertSee(route('operations.beneficiaries.documents.show', [$beneficiary, $document]));

        $this->actingAs($user)->get(route('operations.beneficiaries.documents.show', [$beneficiary, $document]))
            ->assertOk()
            ->assertDownload('fire-safety.pdf');
    }

    public function test_document_cannot_be_downloaded_through_another_beneficiary(): void
    {
        $document = BeneficiaryDocument::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('operations.beneficiaries.documents.show', [Beneficiary::factory()->create(), $document]))
            ->assertNotFound();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        $district = District::factory()->create();
        $county = County::factory()->create(['district_id' => $district->id]);
        $subCounty = SubCounty::factory()->create(['county_id' => $county->id]);
        $parish = Parish::factory()->create(['sub_county_id' => $subCounty->id]);
        $village = Village::factory()->create(['parish_id' => $parish->id]);

        return $overrides + [
            'full_name_organization' => 'Company Documents Tenant',
            'category' => 'Commercial Tenant',
            'telephone' => '+256 700 000 010',
            'national_id_given_names' => 'Jane',
            'national_id_surname' => 'Akello',
            'district_id' => $district->id,
            'county_id' => $county->id,
            'sub_county_id' => $subCounty->id,
            'parish_id' => $parish->id,
            'village_id' => $village->id,
            'campus_id' => Campus::factory()->create()->id,
            'record_status' => 'Active',
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function updatePayload(Beneficiary $beneficiary, array $overrides = []): array
    {
        return $overrides + [
            'full_name_organization' => $beneficiary->full_name_organization,
            'category' => $beneficiary->category,
            'telephone' => $beneficiary->telephone,
            'national_id_given_names' => $beneficiary->national_id_given_names,
            'national_id_surname' => $beneficiary->national_id_surname,
            'district_id' => $beneficiary->district_id,
            'county_id' => $beneficiary->county_id,
            'sub_county_id' => $beneficiary->sub_county_id,
            'parish_id' => $beneficiary->parish_id,
            'village_id' => $beneficiary->village_id,
            'campus_id' => $beneficiary->campus_id,
            'record_status' => $beneficiary->record_status,
        ];
    }
}
