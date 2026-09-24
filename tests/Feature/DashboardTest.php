<?php

namespace Tests\Feature;

use App\Models\Agreement;
use App\Models\Approval;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetType;
use App\Models\Campus;
use App\Models\Document;
use App\Models\MaintenanceRequest;
use App\Models\OrgUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_user_sees_the_complete_dashboard_sidebar(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('System Administrator navigation')
            ->assertSee('images/busitema-logo.png', escape: false)
            ->assertSee([
                'Dashboard',
                'Asset Registry',
                'Land Management',
                'Buildings & Spaces',
                'Laboratories & Equipment',
                'Vehicles',
                'Commercial Property',
                'Agricultural Property',
                'Agreements & Allocations',
                'Tenants/Beneficiaries',
                'Inspections',
                'Maintenance',
                'Documents',
                'Billing & Invoices',
                'Payments',
                'Arrears',
                'Utilities',
                'Approvals',
                'Notifications',
                'Reports',
                'Audit Trail',
                'Users',
                'Roles & Permissions',
                'Campuses',
                'Organizational Units',
                'Asset Categories',
                'System Settings',
            ])
            ->assertSee('aria-current="page"', escape: false)
            ->assertSee('Collapse sidebar');
    }

    public function test_estates_dashboard_design_preview_displays_property_oversight_content(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get(route('estates.dashboard'));

        $response
            ->assertOk()
            ->assertSee('University Property / Estates Manager navigation')
            ->assertSee([
                'Asset Registry',
                'Land Management',
                'Buildings & Spaces',
                'Laboratories & Equipment',
                'Vehicles',
                'Commercial Property',
                'Agricultural Property',
                'Agreements & Allocations',
                'Tenants/Beneficiaries',
                'Inspections',
                'Maintenance',
                'Documents',
                'Approvals',
                'Notifications',
                'Reports',
                'Audit History',
                'Maps/Location View',
                'Total Assets',
                'Total Campuses',
                'Available Land',
                'Occupied Spaces',
                'Assets Under Maintenance',
                'Active Agreements',
                'Expiring Agreements',
                'Poor/Critical Condition Assets',
                'Assets by Campus',
                'Assets by Category',
                'Land Utilization',
                'Occupancy Overview',
                'Maintenance Status',
                'Agreement Expiries',
                'Recent Activity',
                'Upcoming Expiries',
            ])
            ->assertDontSee([
                'Users',
                'Roles & Permissions',
                'System Settings',
                'System Health',
                'Failed Jobs',
            ]);
    }

    public function test_management_dashboard_design_preview_displays_executive_oversight_content(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get(route('management.dashboard'));

        $response
            ->assertOk()
            ->assertSee('University Management navigation')
            ->assertSee([
                'Asset Overview',
                'Land Overview',
                'Buildings & Spaces',
                'Laboratories',
                'Vehicles',
                'Approvals',
                'Reports',
                'Maps',
                'Revenue & Collections',
                'Arrears Overview',
                'Maintenance Overview',
                'Asset Condition',
                'Agreement Expiries',
                'Total University Assets',
                'Land Utilization',
                'Occupancy Rate',
                'Revenue Collection',
                'Outstanding Arrears',
                'Assets in Poor/Critical Condition',
                'Expiring Agreements',
                'Pending Management Approvals',
                'Assets by Campus',
                'Asset Distribution',
                'Revenue vs Expected',
                'Maintenance Trends',
                'Asset Condition Trends',
                'Recent Approvals',
                'Key Institutional Alerts',
            ])
            ->assertDontSee([
                'Users',
                'Roles & Permissions',
                'System Settings',
                'Failed Jobs',
                'Tenants/Beneficiaries',
                'Documents',
            ]);
    }

    public function test_campus_dashboard_design_preview_displays_assigned_campus_operations(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get(route('campus.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Campus Property Officer navigation')
            ->assertSee([
                'Asset Registry',
                'Land Management',
                'Buildings & Spaces',
                'Commercial Property',
                'Agricultural Property',
                'Agreements & Allocations',
                'Tenants/Beneficiaries',
                'Inspections',
                'Maintenance',
                'Documents',
                'Notifications',
                'Reports',
                'Map/Location View',
                'Total Campus Assets',
                'Available Land',
                'Vacant Spaces',
                'Occupied Spaces',
                'Assets Under Maintenance',
                'Expiring Agreements',
                'Open Maintenance Requests',
                'Poor/Critical Condition Assets',
                'Recent Asset Activity',
                'Maintenance Status',
                'Agreement Expiries',
                'Asset Condition',
                'Occupancy Overview',
                'Upcoming Document Expiries',
                'Register Asset',
                'Add Land Record',
                'Add Building/Space',
                'Create Inspection',
                'Create Maintenance Request',
                'Add Agreement/Allocation',
                'Upload Document',
            ])
            ->assertDontSee([
                'Users',
                'Roles & Permissions',
                'System Settings',
                'Revenue & Collections',
                'Arrears Overview',
                'Approvals',
            ]);
    }

    public function test_finance_dashboard_design_preview_displays_financial_operations(): void
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get(route('finance.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Finance Officer navigation')
            ->assertSee([
                'Billing & Invoices',
                'Payments',
                'Deposits',
                'Arrears',
                'Utilities & Charges',
                'Tenants/Beneficiaries',
                'Agreements',
                'Financial Reports',
                'Notifications',
                'Expected Revenue',
                'Revenue Collected',
                'Outstanding Balance',
                'Overdue Invoices',
                'Total Arrears',
                'Deposits Held',
                'Utility Charges',
                'Recent Payments',
                'Revenue vs Expected',
                'Arrears Aging',
                'Collection Trends',
                'Financial Alerts',
                'Create Invoice',
                'Record Payment',
                'View Arrears',
                'Generate Report',
            ])
            ->assertDontSee([
                'Users',
                'Roles & Permissions',
                'System Settings',
                'Asset Registry',
                'Land Management',
                'Maintenance Overview',
            ]);
    }

    public function test_dashboard_renders_live_metrics_from_stored_records(): void
    {
        $this->travelTo('2026-09-09 10:00:00');
        $campus = Campus::factory()->create(['name' => 'Main Campus']);
        $unit = OrgUnit::factory()->create(['name' => 'Estates Office']);
        $category = AssetCategory::factory()->create(['name' => 'Equipment & Machinery']);
        $type = AssetType::factory()->create(['asset_category_id' => $category->id, 'name' => 'Generators']);
        $user = User::factory()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id]);
        User::factory()->inactive()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id]);

        $asset = Asset::factory()->create([
            'campus_id' => $campus->id,
            'org_unit_id' => $unit->id,
            'asset_category_id' => $category->id,
            'asset_type_id' => $type->id,
            'status' => 'Under Maintenance',
            'condition' => 'Critical',
            'next_service_at' => today()->addDays(5),
            'calibration_due_at' => today()->addDays(10),
        ]);
        Agreement::factory()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id, 'status' => 'Active', 'expires_at' => today()->addDays(20)]);
        Approval::factory()->create(['campus_id' => $campus->id, 'status' => 'Pending']);
        Document::factory()->create(['campus_id' => $campus->id, 'org_unit_id' => $unit->id, 'expires_at' => today()->addDays(40)]);
        MaintenanceRequest::factory()->create(['asset_id' => $asset->id, 'campus_id' => $campus->id, 'org_unit_id' => $unit->id, 'priority' => 'High', 'status' => 'Repair']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertViewHas('metrics', fn (array $metrics): bool => $metrics['totalAssets'] === 1
                && $metrics['totalUsers'] === 2
                && $metrics['underMaintenance'] === 1
                && $metrics['poorCritical'] === 1
                && $metrics['activeAgreements'] === 1
                && $metrics['pendingApprovals'] === 1
                && $metrics['expiringDocuments'] === 1)
            ->assertSee('Live data')
            ->assertSee('Equipment &amp; Machinery', escape: false)
            ->assertSee('User created');
    }

    public function test_dashboard_filters_assets_by_campus_type_status_unit_and_date_range(): void
    {
        $this->travelTo('2026-09-09 10:00:00');
        $mainCampus = Campus::factory()->create(['name' => 'Main Campus']);
        $otherCampus = Campus::factory()->create(['name' => 'Arapai Campus']);
        $unit = OrgUnit::factory()->create(['name' => 'Facilities Office']);
        $category = AssetCategory::factory()->create(['name' => 'Vehicles']);
        $type = AssetType::factory()->create(['asset_category_id' => $category->id, 'name' => 'Motor Vehicles']);
        $user = User::factory()->create();

        Asset::factory()->create(['campus_id' => $mainCampus->id, 'org_unit_id' => $unit->id, 'asset_category_id' => $category->id, 'asset_type_id' => $type->id, 'condition' => 'Critical']);
        Asset::factory()->create(['campus_id' => $otherCampus->id, 'org_unit_id' => $unit->id, 'asset_category_id' => $category->id, 'asset_type_id' => $type->id, 'condition' => 'Critical']);
        Asset::factory()->create(['campus_id' => $mainCampus->id, 'org_unit_id' => $unit->id, 'asset_category_id' => $category->id, 'asset_type_id' => $type->id, 'condition' => 'Good']);
        Asset::factory()->create(['campus_id' => $mainCampus->id, 'org_unit_id' => $unit->id, 'asset_category_id' => $category->id, 'asset_type_id' => $type->id, 'condition' => 'Critical', 'created_at' => today()->subMonths(13)]);

        $response = $this->actingAs($user)->get(route('dashboard', [
            'campus_id' => $mainCampus->id,
            'asset_type_id' => $type->id,
            'status' => 'poor_critical',
            'org_unit_id' => $unit->id,
            'date_range' => 'last_12_months',
        ]));

        $response
            ->assertOk()
            ->assertViewHas('metrics', fn (array $metrics): bool => $metrics['totalAssets'] === 1 && $metrics['poorCritical'] === 1)
            ->assertSee('Main Campus')
            ->assertSee('Motor Vehicles')
            ->assertSee('Poor / Critical');
    }

    public function test_dashboard_rejects_invalid_filter_values(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from(route('dashboard'))->get(route('dashboard', [
            'status' => 'invented-status',
            'date_range' => 'all-time-forever',
        ]));

        $response
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors(['status', 'date_range']);
    }
}
