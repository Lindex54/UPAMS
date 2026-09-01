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

    public function test_create_form_displays_the_cascading_location_controls_and_optional_email(): void
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
            ->assertSee('(optional)');
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
}
