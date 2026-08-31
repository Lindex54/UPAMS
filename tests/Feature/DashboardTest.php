<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function test_dashboard_design_preview_displays_the_complete_sidebar_without_authentication(): void
    {
        $response = $this->get(route('dashboard'));

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
        $response = $this->get(route('estates.dashboard'));

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
        $response = $this->get(route('management.dashboard'));

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
        $response = $this->get(route('campus.dashboard'));

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
        $response = $this->get(route('finance.dashboard'));

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
}
