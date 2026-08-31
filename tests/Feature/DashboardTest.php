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
}
