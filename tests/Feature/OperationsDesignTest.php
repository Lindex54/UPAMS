<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OperationsDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_operations_pages(): void
    {
        $response = $this->get(route('operations.agreements.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * @param  array<string, string>  $parameters
     * @param  array<int, string>  $expectedContent
     */
    #[DataProvider('operationsPages')]
    public function test_super_administrator_can_view_each_operations_design_page(
        string $routeName,
        array $parameters,
        string $moduleTitle,
        array $expectedContent,
    ): void {
        $user = User::factory()->make(['is_active' => true]);

        $response = $this->actingAs($user)->get(route($routeName, $parameters));

        $response
            ->assertSee($moduleTitle)
            ->assertSee($expectedContent);
    }

    /**
     * @param  array<int, string>  $expectedContent
     */
    #[DataProvider('moduleWorkflowContent')]
    public function test_each_operations_detail_page_displays_its_complete_module_workflow(
        string $module,
        array $expectedContent,
    ): void {
        $user = User::factory()->make(['is_active' => true]);

        $response = $this->actingAs($user)->get(route("operations.{$module}.show", ['record' => 'PREVIEW-001']));

        $response->assertSee($expectedContent);
    }

    /**
     * @param  array<int, string>  $expectedContent
     */
    #[DataProvider('agreementLifecyclePages')]
    public function test_agreement_lifecycle_actions_have_dedicated_design_pages(
        string $routeName,
        array $expectedContent,
    ): void {
        $user = User::factory()->make(['is_active' => true]);

        $response = $this->actingAs($user)->get(route($routeName, ['record' => 'AGR-2026-041']));

        $response
            ->assertSee('Main Campus Bookshop Commercial Lease')
            ->assertSee($expectedContent);
    }

    /**
     * @return array<string, array{string, array<string, string>, string, array<int, string>}>
     */
    public static function operationsPages(): array
    {
        $modules = [
            'agreements' => 'Agreements & Allocations',
            'beneficiaries' => 'Tenants / Beneficiaries',
            'inspections' => 'Inspections',
            'maintenance' => 'Maintenance',
            'documents' => 'Documents',
        ];
        $pages = [
            'index' => [[], ['Search and filters', 'All campuses', 'Super Admin Actions', 'Created By', 'Last Updated At']],
            'create' => [[], ['Record information', 'Administration & control', 'Frontend preview', 'Save as draft']],
            'show' => [['record' => 'PREVIEW-001'], ['Created By', 'Created At', 'Last Updated By', 'Last Updated At', 'Activity / Audit History', 'Super Admin controls']],
            'edit' => [['record' => 'PREVIEW-001'], ['Audit provenance', 'Record accountability', 'Save changes', 'No data will be saved']],
        ];
        $cases = [];

        foreach ($modules as $module => $moduleTitle) {
            foreach ($pages as $page => [$parameters, $expectedContent]) {
                if ($module === 'beneficiaries' && $page === 'create') {
                    $expectedContent = ['Record information', 'Administration & control', 'Location validation active', 'Save as draft'];
                }

                $cases["{$module} {$page} page"] = [
                    "operations.{$module}.{$page}",
                    $parameters,
                    $moduleTitle,
                    $expectedContent,
                ];
            }
        }

        return $cases;
    }

    /**
     * @return array<string, array{string, array<int, string>}>
     */
    public static function moduleWorkflowContent(): array
    {
        return [
            'agreement lifecycle' => ['agreements', ['Agreement & allocation', 'Renewal & termination', 'Start renewal', 'Start termination', 'Approve agreement']],
            'beneficiary financial history' => ['beneficiaries', ['Profile & contact', 'Allocation & agreement', 'Financial position', 'Charges, payments, and balances', 'View payments']],
            'inspection evidence and resolution' => ['inspections', ['Inspection result', 'Findings & action', 'Photographs & reports', 'Upload evidence', 'Mark resolved']],
            'maintenance lifecycle' => ['maintenance', ['Problem Reported → Assessment → Approval → Assignment → Repair → Final Inspection → Closed', 'Resources & cost', 'Advance workflow', 'Record cost']],
            'document version control' => ['documents', ['Document metadata', 'Issue & expiry control', 'Version history', 'New version upload', 'Edit metadata', 'Archive document']],
        ];
    }

    /**
     * @return array<string, array{string, array<int, string>}>
     */
    public static function agreementLifecyclePages(): array
    {
        return [
            'approval review' => ['operations.agreements.approval', ['Review and approve agreement', 'Approval decision', 'Confirm approval decision']],
            'renewal preparation' => ['operations.agreements.renewal', ['Prepare agreement renewal', 'Renewal proposal', 'Create renewal draft']],
            'termination initiation' => ['operations.agreements.termination', ['Initiate agreement termination', 'Termination instruction', 'Create termination draft']],
        ];
    }
}
