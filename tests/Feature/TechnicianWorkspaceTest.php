<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetType;
use App\Models\AuditLog;
use App\Models\Campus;
use App\Models\ComputerLab;
use App\Models\IctEquipmentAssignment;
use App\Models\IctInspection;
use App\Models\IctTransferRequest;
use App\Models\OrgUnit;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class TechnicianWorkspaceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_technician_data_is_scoped_to_assigned_campus_and_unit(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $ownAsset = $this->ictAsset($campus, $unit, $type, ['serial_number' => 'SN-OWN-001']);
        $otherAsset = $this->ictAsset(Campus::factory()->create(), $unit, $type, ['serial_number' => 'SN-OTHER-001']);

        $this->actingAs($technician)->get(route('technician.dashboard'))
            ->assertOk()->assertViewHas('metrics', fn (array $metrics): bool => $metrics['total'] === 1)->assertSee('IT Technician navigation');
        $this->actingAs($technician)->get(route('technician.equipment.index', ['search' => 'SN-OWN']))
            ->assertOk()->assertSee($ownAsset->serial_number)->assertDontSee($otherAsset->serial_number);
        $this->actingAs($technician)->get(route('technician.equipment.show', $otherAsset))->assertNotFound();
    }

    public function test_technician_cannot_access_administration_or_finance(): void
    {
        [$technician] = $this->technicianContext();

        $this->actingAs($technician)->get(route('dashboard'))->assertRedirect(route('technician.dashboard'));
        $this->actingAs($technician)->get(route('users.index'))->assertForbidden();
        $this->actingAs($technician)->get(route('billing.index'))->assertForbidden();
    }

    public function test_system_administrator_has_university_wide_technician_access(): void
    {
        [, $campus, $unit, $type] = $this->technicianContext();
        $administrator = User::factory()->create(['role_id' => Role::factory()->create(['name' => 'System Administrator'])->id]);
        $this->ictAsset($campus, $unit, $type, ['serial_number' => 'SN-CAMPUS-A']);
        $this->ictAsset(Campus::factory()->create(), $unit, $type, ['serial_number' => 'SN-CAMPUS-B']);

        $this->actingAs($administrator)->get(route('technician.equipment.index'))
            ->assertOk()->assertSee('System Administrator navigation')->assertSee('SN-CAMPUS-A')->assertSee('SN-CAMPUS-B');
    }

    public function test_other_roles_cannot_access_the_shared_ict_workspace(): void
    {
        $financeOfficer = User::factory()->create(['role_id' => Role::factory()->create(['name' => 'Finance Officer'])->id]);

        $this->actingAs($financeOfficer)->get(route('technician.dashboard'))->assertForbidden();
    }

    public function test_technician_can_register_equipment_without_overriding_scope(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $response = $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'reference' => 'MANUAL-ID-MUST-BE-IGNORED', 'serial_number' => ' sn-new-001 ', 'name' => 'Lab Desktop', 'make' => 'Dell',
            'asset_type_id' => $type->id, 'campus_id' => Campus::factory()->create()->id, 'org_unit_id' => $unit->id,
            'condition' => 'Good', 'operational_status' => 'Operational',
            'latitude' => '1.2345678', 'longitude' => '33.1234567',
            'specifications' => ['processor' => 'Intel Core i7', 'ram_size' => '16 GB', 'storage_capacity' => '512 GB'],
        ]);

        $asset = Asset::query()->where('serial_number', 'SN-NEW-001')->firstOrFail();
        $response->assertRedirect(route('technician.equipment.show', $asset));
        $this->assertSame($campus->id, $asset->campus_id);
        $this->assertSame($technician->id, $asset->created_by);
        $this->assertTrue($asset->is_ict);
        $this->assertMatchesRegularExpression('/^ICT-\d{4}-[A-Z0-9]{10}$/', $asset->reference);
        $this->assertNotSame('MANUAL-ID-MUST-BE-IGNORED', $asset->reference);
        $this->assertSame('Intel Core i7', $asset->specifications['processor']);
        $this->assertSame('1.2345678', $asset->latitude);
        $this->assertSame('33.1234567', $asset->longitude);
    }

    public function test_equipment_can_be_registered_with_only_name_make_and_serial_number(): void
    {
        [$technician, $campus, $unit] = $this->technicianContext();

        $response = $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'name' => 'USB Conference Camera',
            'make' => 'Logitech',
            'serial_number' => ' camera-1001 ',
        ]);

        $asset = Asset::query()->where('serial_number', 'CAMERA-1001')->firstOrFail();
        $response->assertRedirect(route('technician.equipment.show', $asset));
        $this->assertSame($campus->id, $asset->campus_id);
        $this->assertSame($unit->id, $asset->org_unit_id);
        $this->assertSame('Good', $asset->condition);
        $this->assertSame('Operational', $asset->operational_status);
        $this->assertNull($asset->asset_type_id);
    }

    public function test_equipment_form_uses_generated_asset_ids_and_type_specific_fields(): void
    {
        [$technician] = $this->technicianContext();

        $this->actingAs($technician)->get(route('technician.equipment.create'))
            ->assertOk()
            ->assertSee('Generated automatically after saving')
            ->assertDontSee('name="reference"', false)
            ->assertSee('name="latitude"', false)
            ->assertSee('name="longitude"', false)
            ->assertSee('processor_generation')
            ->assertSee('storage_capacity');
    }

    public function test_equipment_rejects_incomplete_or_out_of_range_gps_coordinates(): void
    {
        [$technician] = $this->technicianContext();

        $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'name' => 'GPS Validation Device',
            'make' => 'Dell',
            'serial_number' => 'GPS-INVALID-100',
            'latitude' => '91.0000000',
        ])->assertSessionHasErrors(['latitude', 'longitude']);

        $this->assertDatabaseMissing('assets', ['serial_number' => 'GPS-INVALID-100']);
    }

    public function test_equipment_rejects_duplicate_serial_numbers_after_normalization(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $this->ictAsset($campus, $unit, $type, ['serial_number' => 'DUPLICATE-100']);

        $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'name' => 'Duplicate Desktop',
            'make' => 'Dell',
            'serial_number' => ' duplicate-100 ',
        ])->assertSessionHasErrors('serial_number');

        $this->assertSame(1, Asset::query()->where('serial_number', 'DUPLICATE-100')->count());
    }

    public function test_equipment_rejects_specifications_not_defined_for_selected_type(): void
    {
        [$technician, , , $type] = $this->technicianContext();

        $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'name' => 'Desktop with invalid specification',
            'make' => 'Dell',
            'serial_number' => 'DESKTOP-INVALID-SPEC',
            'asset_type_id' => $type->id,
            'specifications' => ['processor' => 'Intel Core i5', 'brightness' => '5000 lumens'],
        ])->assertSessionHasErrors('specifications');

        $this->assertDatabaseMissing('assets', ['serial_number' => 'DESKTOP-INVALID-SPEC']);
    }

    public function test_broad_computer_type_requires_a_specific_subtype(): void
    {
        [$technician, , , $desktopType] = $this->technicianContext();
        $computerType = AssetType::factory()->create([
            'asset_category_id' => $desktopType->asset_category_id,
            'name' => 'Computer Equipment',
        ]);

        $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'name' => 'General Computer',
            'make' => 'HP',
            'serial_number' => 'GENERAL-COMPUTER-100',
            'asset_type_id' => $computerType->id,
        ])->assertSessionHasErrors('specifications.computer_subtype');

        $this->assertDatabaseMissing('assets', ['serial_number' => 'GENERAL-COMPUTER-100']);
    }

    public function test_irrelevant_peripheral_specifications_are_not_persisted(): void
    {
        [$technician, , , $desktopType] = $this->technicianContext();
        $peripheralType = AssetType::factory()->create([
            'asset_category_id' => $desktopType->asset_category_id,
            'name' => 'ICT Peripheral',
        ]);

        $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'name' => 'Lab Keyboard',
            'make' => 'Logitech',
            'serial_number' => 'KEYBOARD-100',
            'asset_type_id' => $peripheralType->id,
            'specifications' => [
                'peripheral_subtype' => 'Keyboard',
                'interface_type' => 'USB',
                'storage_capacity' => '2 TB',
            ],
        ])->assertRedirect();

        $asset = Asset::query()->where('serial_number', 'KEYBOARD-100')->firstOrFail();
        $this->assertSame(['peripheral_subtype' => 'Keyboard', 'interface_type' => 'USB'], $asset->specifications);
    }

    public function test_equipment_detail_displays_type_specific_specifications(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $asset = $this->ictAsset($campus, $unit, $type, [
            'specifications' => ['processor' => 'Intel Core i9', 'ram_size' => '32 GB'],
            'latitude' => '1.2345678',
            'longitude' => '33.1234567',
        ]);

        $this->actingAs($technician)->get(route('technician.equipment.show', $asset))
            ->assertOk()
            ->assertSee('Technical Specifications')
            ->assertSee('Processor')
            ->assertSee('Intel Core i9')
            ->assertSee('RAM Size')
            ->assertSee('32 GB')
            ->assertSee('GPS Latitude')
            ->assertSee('1.2345678')
            ->assertSee('GPS Longitude')
            ->assertSee('33.1234567')
            ->assertSee('View full map')
            ->assertSee('Open in Google Maps');
    }

    public function test_equipment_detail_without_gps_coordinates_shows_empty_map_state(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $asset = $this->ictAsset($campus, $unit, $type, ['latitude' => null, 'longitude' => null]);

        $this->actingAs($technician)->get(route('technician.equipment.show', $asset))
            ->assertOk()
            ->assertSee('No GPS location recorded')
            ->assertDontSee('View full map');
    }

    public function test_lab_detail_displays_asset_ids_and_serial_numbers(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $lab = ComputerLab::factory()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id]);
        $asset = $this->ictAsset($campus, $unit, $type, ['computer_lab_id' => $lab->id, 'reference' => 'ICT-LAB-014', 'serial_number' => 'SERIAL-LAB-014']);

        $this->actingAs($technician)->get(route('technician.labs.show', $lab))
            ->assertOk()->assertSee($asset->reference)->assertSee($asset->serial_number);
    }

    public function test_fault_workflow_updates_equipment_status(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $asset = $this->ictAsset($campus, $unit, $type);
        $this->actingAs($technician)->post(route('technician.faults.store'), [
            'asset_id' => $asset->id, 'title' => 'No display output', 'fault_description' => 'Monitor receives no signal.',
            'priority' => 'High', 'status' => 'Repair In Progress', 'assigned_technician_id' => $technician->id,
        ])->assertRedirect();

        $fault = $asset->maintenanceRequests()->firstOrFail();
        $this->assertNotNull($fault->diagnosed_at);
        $this->assertSame('Under Maintenance', $asset->fresh()->operational_status);

        $this->actingAs($technician)->put(route('technician.faults.update', $fault), [
            'asset_id' => $asset->id, 'title' => $fault->title, 'fault_description' => $fault->fault_description,
            'priority' => 'High', 'status' => 'Resolved', 'assigned_technician_id' => $technician->id,
            'repair_notes' => 'Replaced display cable and passed test.',
        ])->assertRedirect(route('technician.faults.show', $fault));

        $this->assertNotNull($fault->fresh()->resolved_at);
        $this->assertSame('Operational', $asset->fresh()->operational_status);
    }

    public function test_administrator_filters_equipment_by_all_provided_register_dimensions(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $administrator = User::factory()->create(['role_id' => Role::factory()->create(['name' => 'System Administrator'])->id]);
        $lab = ComputerLab::factory()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id]);
        $matching = $this->ictAsset($campus, $unit, $type, [
            'reference' => 'ICT-FILTER-001', 'serial_number' => 'FILTER-SERIAL-001', 'computer_lab_id' => $lab->id,
            'condition' => 'Fair', 'operational_status' => 'Under Maintenance', 'created_by' => $technician->id,
            'created_at' => '2026-09-05 10:00:00',
        ]);
        $this->ictAsset(Campus::factory()->create(), $unit, $type, ['reference' => 'ICT-EXCLUDED-001', 'serial_number' => 'EXCLUDED-SERIAL']);

        $this->actingAs($administrator)->get(route('technician.equipment.index', [
            'asset_id' => 'FILTER', 'serial_number' => 'FILTER-SERIAL', 'campus_id' => $campus->id,
            'computer_lab_id' => $lab->id, 'asset_type_id' => $type->id, 'condition' => 'Fair',
            'status' => 'Under Maintenance', 'created_by' => $technician->id, 'date_from' => '2026-09-01', 'date_to' => '2026-09-09',
        ]))->assertOk()->assertSee($matching->reference)->assertDontSee('ICT-EXCLUDED-001');
    }

    public function test_technician_create_and_administrator_update_are_audited_with_provenance(): void
    {
        [$technician, $campus, $unit, $type] = $this->technicianContext();
        $administrator = User::factory()->create(['name' => 'University ICT Admin', 'role_id' => Role::factory()->create(['name' => 'System Administrator'])->id]);

        $this->actingAs($technician)->post(route('technician.equipment.store'), [
            'serial_number' => 'SERIAL-AUDIT-001', 'name' => 'Audited Desktop', 'make' => 'HP',
            'asset_type_id' => $type->id, 'campus_id' => $campus->id, 'org_unit_id' => $unit->id,
            'condition' => 'Good', 'operational_status' => 'Operational',
        ])->assertRedirect();
        $asset = Asset::query()->where('serial_number', 'SERIAL-AUDIT-001')->firstOrFail();
        $originalReference = $asset->reference;

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $technician->id, 'user_name' => $technician->name, 'user_role' => 'IT Technician',
            'action' => 'asset_created', 'auditable_type' => Asset::class, 'auditable_id' => $asset->id,
        ]);

        $this->actingAs($administrator)->put(route('technician.equipment.update', $asset), [
            'reference' => 'ATTEMPTED-REPLACEMENT', 'serial_number' => $asset->serial_number, 'name' => 'Audited Desktop Updated', 'make' => 'HP',
            'asset_type_id' => $type->id, 'campus_id' => $campus->id, 'org_unit_id' => $unit->id,
            'condition' => 'Good', 'operational_status' => 'Operational',
        ])->assertRedirect(route('technician.equipment.show', $asset));

        $this->assertSame($originalReference, $asset->fresh()->reference);

        $asset->update(['reference' => 'DIRECT-REPLACEMENT']);
        $this->assertSame($originalReference, $asset->fresh()->reference);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $administrator->id, 'user_name' => 'University ICT Admin', 'user_role' => 'System Administrator',
            'action' => 'asset_updated', 'auditable_type' => Asset::class, 'auditable_id' => $asset->id,
        ]);
        $this->actingAs($administrator)->get(route('technician.activity.show', ['type' => 'equipment', 'record' => $asset->id]))
            ->assertOk()->assertSee((string) $technician->id)->assertSee($technician->name)->assertSee('IT Technician')
            ->assertSee((string) $administrator->id)->assertSee('University ICT Admin')->assertSee('System Administrator');
        $this->assertSame(2, AuditLog::query()->where('auditable_type', Asset::class)->where('auditable_id', $asset->id)->count());
    }

    public function test_administrator_manages_assignments_inspections_and_transfer_decisions_on_shared_records(): void
    {
        [, $campus, $unit, $type] = $this->technicianContext();
        $administrator = User::factory()->create(['role_id' => Role::factory()->create(['name' => 'System Administrator'])->id]);
        $asset = $this->ictAsset($campus, $unit, $type);
        $lab = ComputerLab::factory()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id]);
        $destination = Campus::factory()->create();

        $this->actingAs($administrator)->post(route('technician.assignments.store'), [
            'asset_id' => $asset->id, 'computer_lab_id' => $lab->id, 'custodian' => 'Faculty ICT Office',
        ])->assertRedirect();
        $this->actingAs($administrator)->post(route('technician.inspections.store'), [
            'asset_id' => $asset->id, 'condition' => 'Fair', 'operational_status' => 'Operational', 'findings' => 'Operational after inspection.',
        ])->assertRedirect();
        $this->actingAs($administrator)->post(route('technician.transfers.store'), [
            'asset_id' => $asset->id, 'to_campus_id' => $destination->id, 'reason' => 'Move to a higher demand computer lab.',
        ])->assertRedirect();

        $assignment = IctEquipmentAssignment::query()->firstOrFail();
        $inspection = IctInspection::query()->firstOrFail();
        $transfer = IctTransferRequest::query()->firstOrFail();
        $this->assertSame($administrator->id, $assignment->created_by);
        $this->assertSame($administrator->id, $inspection->created_by);
        $this->assertSame($administrator->id, $transfer->created_by);

        $this->actingAs($administrator)->patch(route('technician.transfers.update', $transfer), ['status' => 'Approved'])->assertRedirect();

        $this->assertSame('Approved', $transfer->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'ict_transfer_request_updated', 'auditable_id' => $transfer->id, 'user_id' => $administrator->id]);
    }

    /** @return array{User, Campus, OrgUnit, AssetType} */
    private function technicianContext(): array
    {
        $campus = Campus::factory()->create();
        $unit = OrgUnit::factory()->create();
        $technician = User::factory()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id, 'role_id' => Role::factory()->create(['name' => 'IT Technician'])->id]);
        $category = AssetCategory::factory()->create(['name' => 'ICT Equipment']);
        $type = AssetType::factory()->create(['asset_category_id' => $category->id, 'name' => 'Desktop Computer']);

        return [$technician, $campus, $unit, $type];
    }

    /** @param array<string, mixed> $attributes */
    private function ictAsset(Campus $campus, OrgUnit $unit, AssetType $type, array $attributes = []): Asset
    {
        return Asset::factory()->create([...$attributes, 'campus_id' => $campus->id, 'org_unit_id' => $unit->id, 'asset_category_id' => $type->asset_category_id, 'asset_type_id' => $type->id, 'is_ict' => true, 'serial_number' => $attributes['serial_number'] ?? fake()->unique()->bothify('SN-########'), 'operational_status' => $attributes['operational_status'] ?? 'Operational']);
    }
}
