<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Campus;
use App\Models\OrgUnit;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PropertyRecordManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_property_records(): void
    {
        $this->get(route('asset-management.assets.index'))->assertRedirect(route('login'));
    }

    public function test_campus_officer_only_sees_records_from_their_assigned_campus(): void
    {
        [$officer, $assignedCampus] = $this->campusOfficer();
        $otherCampus = Campus::factory()->create(['name' => 'Other Campus']);
        $category = AssetCategory::factory()->create(['name' => 'Land & Other Property']);
        $this->asset($category, $assignedCampus, 'Assigned Campus Parcel', 'LND-ASSIGNED');
        $this->asset($category, $otherCampus, 'Other Campus Parcel', 'LND-OTHER');

        $response = $this->actingAs($officer)->get(route('asset-management.land.index'));

        $response
            ->assertOk()
            ->assertSee('Assigned Campus Parcel')
            ->assertDontSee('Other Campus Parcel');
    }

    public function test_cross_campus_record_returns_not_found_for_campus_officer(): void
    {
        [$officer] = $this->campusOfficer();
        $otherCampus = Campus::factory()->create();
        $category = AssetCategory::factory()->create(['name' => 'Land & Other Property']);
        $this->asset($category, $otherCampus, 'Hidden Parcel', 'LND-HIDDEN');

        $this->actingAs($officer)
            ->get(route('asset-management.land.show', ['record' => 'LND-HIDDEN']))
            ->assertNotFound();
    }

    /** @param array<string, string> $moduleFields */
    #[DataProvider('propertyModules')]
    public function test_campus_officer_can_create_each_property_record_for_only_their_campus(
        string $module,
        string $expectedCategory,
        array $moduleFields,
    ): void {
        [$officer, $assignedCampus, $orgUnit] = $this->campusOfficer();
        $otherCampus = Campus::factory()->create();

        $response = $this->actingAs($officer)->post(route("asset-management.{$module}.store"), [
            ...$moduleFields,
            'campus_id' => $otherCampus->id,
            'org_unit_id' => $orgUnit->id,
            'record_status' => 'Active',
            'intent' => 'save',
        ]);

        $asset = Asset::query()->latest('id')->firstOrFail();
        $response->assertRedirect(route("asset-management.{$module}.show", ['record' => $asset->reference]));
        $this->assertSame($assignedCampus->id, $asset->campus_id);
        $this->assertSame($expectedCategory, $asset->category->name);
    }

    /** @return array<string, array{string, string, array<string, string>}> */
    public static function propertyModules(): array
    {
        return [
            'asset registry' => ['assets', 'Equipment & Machinery', [
                'asset_name' => 'Campus Generator', 'asset_category' => 'Equipment & Machinery',
                'acquisition_date' => '2026-01-15', 'condition' => 'Good', 'physical_location' => 'Utilities Yard',
            ]],
            'land management' => ['land', 'Land & Other Property', [
                'parcel_name' => 'Training Parcel', 'tenure_type' => 'Freehold',
                'area_acres' => '12.5', 'boundary_status' => 'Clearly demarcated',
            ]],
            'buildings and spaces' => ['buildings', 'Buildings & Spaces', [
                'building_name' => 'Science Block', 'building_type' => 'Academic', 'condition' => 'Good',
            ]],
            'commercial property' => ['commercial-property', 'Commercial Property', [
                'property_unit_name' => 'Campus Canteen', 'property_type' => 'Cafeteria', 'occupancy_status' => 'Occupied',
            ]],
            'agricultural property' => ['agricultural-property', 'Agricultural Property', [
                'property_name' => 'Demonstration Farm', 'property_type' => 'Crop Plot',
                'current_use' => 'Demonstration', 'infrastructure_condition' => 'Good',
            ]],
        ];
    }

    /** @return array{User, Campus, OrgUnit} */
    private function campusOfficer(): array
    {
        $campus = Campus::factory()->create(['name' => 'Assigned Campus']);
        $orgUnit = OrgUnit::factory()->create();
        $role = Role::factory()->create(['name' => 'Campus Property Officer']);
        $officer = User::factory()->create([
            'campus_id' => $campus->id,
            'org_unit_id' => $orgUnit->id,
            'role_id' => $role->id,
        ]);

        return [$officer, $campus, $orgUnit];
    }

    private function asset(AssetCategory $category, Campus $campus, string $name, string $reference): Asset
    {
        return Asset::query()->create([
            'reference' => $reference,
            'name' => $name,
            'campus_id' => $campus->id,
            'asset_category_id' => $category->id,
            'status' => 'Active',
            'condition' => 'Good',
            'specifications' => ['parcel_name' => $name, 'tenure_type' => 'Freehold'],
        ]);
    }
}
