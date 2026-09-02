<?php

namespace Tests\Feature;

use App\Models\Beneficiary;
use App\Models\Campus;
use App\Models\County;
use App\Models\District;
use App\Models\Parish;
use App\Models\SubCounty;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UgandaLocationBeneficiaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_endpoints_return_only_active_children_for_the_selected_parent(): void
    {
        $user = User::factory()->create();
        [$district, $county] = $this->locationHierarchy();
        County::factory()->create(['district_id' => $district->id, 'name' => 'Inactive County', 'is_active' => false]);
        County::factory()->create(['name' => 'Other District County']);

        $response = $this->actingAs($user)->getJson(route('api.locations.counties', $district));

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $county->id)
            ->assertJsonPath('data.0.name', $county->name)
            ->assertJsonMissing(['name' => 'Inactive County'])
            ->assertJsonCount(1, 'data');
    }

    public function test_create_form_displays_cascading_location_controls_and_optional_profile_fields(): void
    {
        $user = User::factory()->create();
        [$district] = $this->locationHierarchy();

        $response = $this->actingAs($user)->get(route('operations.beneficiaries.create'));

        $response
            ->assertOk()
            ->assertSee('Uganda administrative location')
            ->assertSee('District to village')
            ->assertSee('Physical Address / Landmark')
            ->assertSee('Loading counties')
            ->assertSee($district->name)
            ->assertSee('Email')
            ->assertSee("Person's Photo")
            ->assertSee('Default beneficiary avatar')
            ->assertSee('Maximum file size: 2 MB.')
            ->assertSee('(optional)');
    }

    public function test_edit_form_displays_the_stored_photo_instead_of_the_default_avatar(): void
    {
        Storage::fake('local');
        $photoPath = 'beneficiaries/photos/person.png';
        Storage::disk('local')->put($photoPath, 'stored photo');
        $administrator = User::factory()->create();
        $beneficiary = Beneficiary::factory()->create(['photo_path' => $photoPath]);

        $response = $this->actingAs($administrator)->get(route('operations.beneficiaries.edit', $beneficiary->reference));

        $response
            ->assertOk()
            ->assertSee('Stored beneficiary photo')
            ->assertDontSee('Default beneficiary avatar');
    }

    public function test_beneficiary_can_be_stored_without_email_or_a_system_login_account(): void
    {
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create();
        [$district, $county, $subCounty, $parish, $village] = $this->locationHierarchy();
        $userCount = User::count();

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            'full_name_organization' => 'Development Beneficiary',
            'category' => 'Student Beneficiary',
            'telephone' => '+256 700 000 001',
            'email' => '',
            'national_id_given_names' => 'Development',
            'national_id_surname' => 'Beneficiary',
            'district_id' => $district->id,
            'county_id' => $county->id,
            'sub_county_id' => $subCounty->id,
            'parish_id' => $parish->id,
            'village_id' => $village->id,
            'physical_address_landmark' => 'Near the development sample gate',
            'campus_id' => $campus->id,
            'record_status' => 'Active',
        ]);

        $beneficiary = Beneficiary::sole();

        $response->assertRedirect(route('operations.beneficiaries.edit', $beneficiary->reference));
        $this->assertNull($beneficiary->email);
        $this->assertSame($village->id, $beneficiary->village_id);
        $this->assertSame($administrator->id, $beneficiary->created_by);
        $this->assertSame($userCount, User::count());
        $this->assertNull($beneficiary->photo_path);
    }

    public function test_optional_beneficiary_photo_is_stored_on_the_private_disk(): void
    {
        Storage::fake('local');
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create();
        [$district, $county, $subCounty, $parish, $village] = $this->locationHierarchy();

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            ...$this->validBeneficiaryPayload($campus, $district, $county, $subCounty, $parish, $village),
            'photo' => $this->fakePhoto(),
        ]);

        $beneficiary = Beneficiary::sole();

        $response->assertRedirect(route('operations.beneficiaries.edit', $beneficiary->reference));
        $this->assertNotNull($beneficiary->photo_path);
        Storage::disk('local')->assertExists($beneficiary->photo_path);
    }

    public function test_non_image_beneficiary_photo_is_rejected(): void
    {
        Storage::fake('local');
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create();
        [$district, $county, $subCounty, $parish, $village] = $this->locationHierarchy();

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            ...$this->validBeneficiaryPayload($campus, $district, $county, $subCounty, $parish, $village),
            'photo' => UploadedFile::fake()->create('person.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('photo');
        $this->assertDatabaseCount('beneficiaries', 0);
        Storage::disk('local')->assertEmpty();
    }

    public function test_beneficiary_photo_larger_than_two_megabytes_is_rejected(): void
    {
        Storage::fake('local');
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create();
        [$district, $county, $subCounty, $parish, $village] = $this->locationHierarchy();

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            ...$this->validBeneficiaryPayload($campus, $district, $county, $subCounty, $parish, $village),
            'photo' => $this->fakePhoto(paddingBytes: (2 * 1024 * 1024) + 1),
        ]);

        $response->assertSessionHasErrors('photo');
        $this->assertDatabaseCount('beneficiaries', 0);
        Storage::disk('local')->assertEmpty();
    }

    public function test_replacing_a_beneficiary_photo_removes_the_previous_private_file(): void
    {
        Storage::fake('local');
        $existingPhotoPath = 'beneficiaries/photos/existing.png';
        Storage::disk('local')->put($existingPhotoPath, 'existing photo');
        $administrator = User::factory()->create();
        $beneficiary = Beneficiary::factory()->create(['photo_path' => $existingPhotoPath]);

        $response = $this->actingAs($administrator)->put(route('operations.beneficiaries.update', $beneficiary), [
            ...$this->validBeneficiaryPayload(
                $beneficiary->campus,
                $beneficiary->district,
                $beneficiary->county,
                $beneficiary->subCounty,
                $beneficiary->parish,
                $beneficiary->village,
            ),
            'photo' => $this->fakePhoto('replacement.png'),
        ]);

        $beneficiary->refresh();

        $response->assertRedirect(route('operations.beneficiaries.edit', $beneficiary->reference));
        $this->assertNotNull($beneficiary->photo_path);
        $this->assertNotSame($existingPhotoPath, $beneficiary->photo_path);
        Storage::disk('local')->assertExists($beneficiary->photo_path);
        Storage::disk('local')->assertMissing($existingPhotoPath);
    }

    public function test_beneficiary_location_levels_must_belong_to_the_selected_parent(): void
    {
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create();
        [$district, , $subCounty, $parish, $village] = $this->locationHierarchy();
        $countyFromAnotherDistrict = County::factory()->create();

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            'full_name_organization' => 'Invalid Location Beneficiary',
            'category' => 'External Partner',
            'telephone' => '+256 700 000 002',
            'national_id_given_names' => 'Invalid Location',
            'national_id_surname' => 'Beneficiary',
            'district_id' => $district->id,
            'county_id' => $countyFromAnotherDistrict->id,
            'sub_county_id' => $subCounty->id,
            'parish_id' => $parish->id,
            'village_id' => $village->id,
            'campus_id' => $campus->id,
            'record_status' => 'Pending Verification',
        ]);

        $response->assertSessionHasErrors('county_id');
        $this->assertDatabaseCount('beneficiaries', 0);
    }

    public function test_national_identification_details_are_captured_and_the_nin_is_encrypted(): void
    {
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create();
        [$district, $county, $subCounty, $parish, $village] = $this->locationHierarchy();
        $nin = 'CF000000000001';

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            'full_name_organization' => 'Amina Namukasa',
            'category' => 'Staff Tenant',
            'telephone' => '+256 700 000 003',
            'nin' => 'cf00 0000-0000 01',
            'national_id_surname' => 'Namukasa',
            'national_id_given_names' => 'Amina',
            'national_id_sex' => 'Female',
            'nationality' => 'Ugandan',
            'district_id' => $district->id,
            'county_id' => $county->id,
            'sub_county_id' => $subCounty->id,
            'parish_id' => $parish->id,
            'village_id' => $village->id,
            'campus_id' => $campus->id,
            'record_status' => 'Active',
        ]);

        $beneficiary = Beneficiary::sole();
        $rawNin = DB::table('beneficiaries')->where('id', $beneficiary->id)->value('nin');

        $response->assertRedirect(route('operations.beneficiaries.edit', $beneficiary->reference));
        $this->assertSame($nin, $beneficiary->nin);
        $this->assertNotSame($nin, $rawNin);
        $this->assertSame(hash('sha256', $nin), $beneficiary->nin_hash);
        $this->assertSame('Namukasa', $beneficiary->national_id_surname);
        $this->assertSame('Amina', $beneficiary->national_id_given_names);
        $this->assertSame('Female', $beneficiary->national_id_sex);
        $this->assertSame('Ugandan', $beneficiary->nationality);
    }

    public function test_duplicate_nin_is_rejected_after_normalization(): void
    {
        $administrator = User::factory()->create();
        $nin = 'CM000000000001';
        Beneficiary::factory()->create([
            'nin' => $nin,
            'nin_hash' => hash('sha256', $nin),
        ]);
        $campus = Campus::factory()->create();
        [$district, $county, $subCounty, $parish, $village] = $this->locationHierarchy();

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            'full_name_organization' => 'Duplicate Identity Beneficiary',
            'category' => 'Student Beneficiary',
            'telephone' => '+256 700 000 004',
            'nin' => 'cm00-0000-0000-01',
            'national_id_given_names' => 'Duplicate',
            'national_id_surname' => 'Beneficiary',
            'district_id' => $district->id,
            'county_id' => $county->id,
            'sub_county_id' => $subCounty->id,
            'parish_id' => $parish->id,
            'village_id' => $village->id,
            'campus_id' => $campus->id,
            'record_status' => 'Active',
        ]);

        $response->assertSessionHasErrors('nin_hash');
        $this->assertDatabaseCount('beneficiaries', 1);
    }

    public function test_edit_form_masks_the_stored_nin_and_displays_identity_fields(): void
    {
        $administrator = User::factory()->create();
        $nin = 'CF000000009876';
        $beneficiary = Beneficiary::factory()->create([
            'nin' => $nin,
            'nin_hash' => hash('sha256', $nin),
            'national_id_surname' => 'Atim',
        ]);

        $response = $this->actingAs($administrator)->get(route('operations.beneficiaries.edit', $beneficiary->reference));

        $response
            ->assertOk()
            ->assertSee('First Name')
            ->assertSee('Last Name')
            ->assertSee('NIN ending in 9876')
            ->assertSee('Atim')
            ->assertDontSee('Card Number')
            ->assertDontSee('Date of Birth')
            ->assertDontSee('Date of Issue')
            ->assertDontSee('Date of Expiry')
            ->assertDontSee($nin);
    }

    public function test_first_and_last_name_are_required_while_nin_remains_optional(): void
    {
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create();
        [$district, $county, $subCounty, $parish, $village] = $this->locationHierarchy();

        $response = $this->actingAs($administrator)->post(route('operations.beneficiaries.store'), [
            'full_name_organization' => 'Missing Required Names',
            'category' => 'Student Beneficiary',
            'telephone' => '+256 700 000 005',
            'district_id' => $district->id,
            'county_id' => $county->id,
            'sub_county_id' => $subCounty->id,
            'parish_id' => $parish->id,
            'village_id' => $village->id,
            'campus_id' => $campus->id,
            'record_status' => 'Active',
        ]);

        $response->assertSessionHasErrors(['national_id_given_names', 'national_id_surname']);
        $this->assertDatabaseCount('beneficiaries', 0);
    }

    public function test_sync_command_downloads_and_imports_verified_hierarchical_locations(): void
    {
        $developmentDistrict = District::factory()->create(['code' => 'DEV-DISTRICT']);
        $developmentCounty = County::factory()->create(['district_id' => $developmentDistrict->id, 'code' => 'DEV-COUNTY']);
        $developmentSubCounty = SubCounty::factory()->create(['county_id' => $developmentCounty->id, 'code' => 'DEV-SUBCOUNTY']);
        $developmentParish = Parish::factory()->create(['sub_county_id' => $developmentSubCounty->id, 'code' => 'DEV-PARISH']);
        Village::factory()->create(['parish_id' => $developmentParish->id, 'code' => 'DEV-VILLAGE']);
        $dataset = (string) file_get_contents(base_path('tests/Fixtures/uganda-locations-verified.csv'));
        config()->set('uganda_locations.dataset_url', 'https://locations.example.test/uganda.csv');
        config()->set('uganda_locations.sha256', hash('sha256', $dataset));
        Storage::fake('local');
        Http::preventStrayRequests();
        Http::fake([
            'https://locations.example.test/uganda.csv' => Http::response($dataset),
        ]);

        $this->artisan('locations:sync-uganda')->assertSuccessful();

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://locations.example.test/uganda.csv');
        $this->assertDatabaseCount('districts', 2);
        $this->assertDatabaseCount('counties', 3);
        $this->assertDatabaseCount('sub_counties', 3);
        $this->assertDatabaseCount('parishes', 3);
        $this->assertDatabaseCount('villages', 4);
        $this->assertDatabaseHas('villages', ['name' => 'Kasambya I']);
        $this->assertDatabaseMissing('districts', ['code' => 'DEV-DISTRICT']);

        $kampalaDivision = County::query()->where('name', 'Kampala Central Division')->sole();
        $this->assertSame('Kampala', $kampalaDivision->district->name);
    }

    /**
     * @return array{District, County, SubCounty, Parish, Village}
     */
    private function locationHierarchy(): array
    {
        $district = District::factory()->create(['name' => 'Test District']);
        $county = County::factory()->create(['district_id' => $district->id, 'name' => 'Test County']);
        $subCounty = SubCounty::factory()->create(['county_id' => $county->id, 'name' => 'Test Sub-county']);
        $parish = Parish::factory()->create(['sub_county_id' => $subCounty->id, 'name' => 'Test Parish']);
        $village = Village::factory()->create(['parish_id' => $parish->id, 'name' => 'Test Village']);

        return [$district, $county, $subCounty, $parish, $village];
    }

    /**
     * @return array<string, mixed>
     */
    private function validBeneficiaryPayload(
        Campus $campus,
        District $district,
        County $county,
        SubCounty $subCounty,
        Parish $parish,
        Village $village,
    ): array {
        return [
            'full_name_organization' => 'Photo Test Beneficiary',
            'category' => 'Student Beneficiary',
            'telephone' => '+256 700 000 006',
            'national_id_given_names' => 'Photo',
            'national_id_surname' => 'Beneficiary',
            'district_id' => $district->id,
            'county_id' => $county->id,
            'sub_county_id' => $subCounty->id,
            'parish_id' => $parish->id,
            'village_id' => $village->id,
            'campus_id' => $campus->id,
            'record_status' => 'Active',
        ];
    }

    private function fakePhoto(string $name = 'person.png', int $paddingBytes = 0): UploadedFile
    {
        $pngContents = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WlB9pAAAAAASUVORK5CYII=',
            true,
        );

        if ($pngContents === false) {
            throw new \RuntimeException('The embedded test photo could not be decoded.');
        }

        return UploadedFile::fake()->createWithContent($name, $pngContents.str_repeat("\0", $paddingBytes));
    }
}
