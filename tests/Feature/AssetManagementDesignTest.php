<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AssetManagementDesignTest extends TestCase
{
    public function test_guest_is_redirected_from_asset_management_pages(): void
    {
        $response = $this->get(route('asset-management.assets.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_asset_form_marks_identifiers_assignment_and_cost_as_optional_and_accepts_an_image(): void
    {
        $user = User::factory()->make(['is_active' => true]);

        $response = $this->actingAs($user)->get(route('asset-management.assets.create'));

        $response
            ->assertSeeInOrder([
                'Asset Tag / Code',
                '(optional)',
                'Serial Number',
                '(optional)',
                'Acquisition Cost',
                '(optional)',
                'Assigned Unit',
                '(optional)',
                'Custodian',
                '(optional)',
                'Asset Image',
            ])
            ->assertSee('name="asset_image"', escape: false)
            ->assertSee('accept="image/jpeg,image/png,image/webp"', escape: false);
    }

    public function test_land_form_only_marks_core_fields_as_required_and_keeps_assignment_markers_inline(): void
    {
        $user = User::factory()->make(['is_active' => true]);

        $response = $this->actingAs($user)->get(route('asset-management.land.create'));

        $response
            ->assertSeeInOrder([
                'Plot / Block Number',
                '(optional)',
                'Current Use',
                '(optional)',
                'Survey Reference',
                '(optional)',
                'Title / Instrument Number',
                '(optional)',
                'Managing Unit',
                '(optional)',
            ])
            ->assertSee('class="inline-flex items-center gap-1">Campus', escape: false)
            ->assertSee('class="inline-flex items-center gap-1">Record Status', escape: false)
            ->assertSee('Organizational Unit <span class="font-normal text-body-text">(optional)</span>', escape: false);
    }

    /**
     * @param  array<string, string>  $parameters
     * @param  array<int, string>  $expectedContent
     */
    #[DataProvider('assetManagementPages')]
    public function test_authenticated_user_can_view_each_asset_management_design_page(
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
     * @return array<string, array{string, array<string, string>, string, array<int, string>}>
     */
    public static function assetManagementPages(): array
    {
        $modules = [
            'assets' => 'Asset Registry',
            'land' => 'Land Management',
            'buildings' => 'Buildings & Spaces',
            'laboratories' => 'Laboratories & Equipment',
            'vehicles' => 'Vehicles',
            'commercial-property' => 'Commercial Property',
            'agricultural-property' => 'Agricultural Property',
        ];
        $pages = [
            'index' => [[], ['Search and filters', 'Audit / Provenance', 'Date Added']],
            'create' => [[], ['Record Information', 'No data will be saved', 'Save as draft']],
            'show' => [['record' => 'PREVIEW-001'], ['Added By', 'Last Updated Date', 'Record History']],
            'edit' => [['record' => 'PREVIEW-001'], ['Save changes', 'No data will be saved']],
        ];
        $cases = [];

        foreach ($modules as $module => $moduleTitle) {
            foreach ($pages as $page => [$parameters, $expectedContent]) {
                $cases["{$module} {$page} page"] = [
                    "asset-management.{$module}.{$page}",
                    $parameters,
                    $moduleTitle,
                    $expectedContent,
                ];
            }
        }

        return $cases;
    }
}
