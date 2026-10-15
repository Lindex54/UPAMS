<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Campus;
use App\Models\Role;
use App\Models\User;
use App\Services\GisFeatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GisMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_gis_page_lists_every_geolocated_record(): void
    {
        Campus::factory()->create(['name' => 'Nagongera Campus', 'code' => 'NAG', 'latitude' => 0.77, 'longitude' => 34.0261]);
        Campus::factory()->create(['name' => 'Unmapped Campus', 'latitude' => null, 'longitude' => null]);
        Asset::factory()->create(['name' => 'Standby Generator', 'latitude' => 1.78, 'longitude' => 33.625]);
        Asset::factory()->create(['name' => 'Unmapped Printer', 'latitude' => null, 'longitude' => null]);

        $this->actingAs($this->userWithRole('System Administrator'))->get(route('gis.index'))
            ->assertOk()
            ->assertSee('University properties on the map')
            ->assertSee('gisExplorer', escape: false)
            ->assertSee('Nagongera Campus')
            ->assertSee('Standby Generator')
            ->assertDontSee('Unmapped Campus')
            ->assertDontSee('Unmapped Printer');
    }

    public function test_features_carry_summary_details_and_layer_counts(): void
    {
        $campus = Campus::factory()->create(['name' => 'Arapai Campus', 'code' => 'ARP', 'faculty' => 'Faculty of Agriculture', 'latitude' => 1.78, 'longitude' => 33.625]);
        Asset::factory()->count(2)->create(['campus_id' => $campus->id, 'latitude' => 1.781, 'longitude' => 33.626]);
        $service = app(GisFeatureService::class);

        $features = $service->features($this->userWithRole('System Administrator'));
        $campusFeature = $features->firstWhere('id', "campus-{$campus->id}");

        $this->assertSame('campuses', $campusFeature['layer']);
        $this->assertSame('ARP', $campusFeature['badge']);
        $this->assertSame('Faculty of Agriculture', $campusFeature['subtitle']);
        $this->assertContains(['Assets', '2'], $campusFeature['stats']);
        $this->assertContains(['Campus code', 'ARP'], $campusFeature['details']);
        $this->assertStringStartsWith('https://www.google.com/maps/dir/', $campusFeature['directions_url']);
        $this->assertSame(route('campuses.show', $campus), $campusFeature['url']);
        $this->assertSame(['campuses' => 1, 'assets' => 2], array_map(fn (array $layer): int => $layer['count'], $service->layers($features)));
    }

    public function test_ict_asset_links_only_for_users_who_can_open_the_ict_workspace(): void
    {
        $asset = Asset::factory()->create(['is_ict' => true, 'latitude' => 0.545, 'longitude' => 34.025]);
        $service = app(GisFeatureService::class);

        $adminFeature = $service->features($this->userWithRole('System Administrator'))->firstWhere('id', "asset-{$asset->id}");
        $financeFeature = $service->features($this->userWithRole('Finance Officer'))->firstWhere('id', "asset-{$asset->id}");

        $this->assertSame(route('technician.equipment.show', $asset), $adminFeature['url']);
        $this->assertNull($financeFeature['url']);
    }

    public function test_empty_map_explains_how_items_appear(): void
    {
        $this->actingAs($this->userWithRole('Finance Officer'))->get(route('gis.index'))
            ->assertOk()
            ->assertSee('Nothing has GPS coordinates yet');
    }

    public function test_sidebar_links_to_the_gis_map(): void
    {
        $this->actingAs($this->userWithRole('System Administrator'))->get(route('dashboard'))
            ->assertSee(route('gis.index'));
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('gis.index'))->assertRedirect(route('login'));
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role_id' => Role::query()->firstOrCreate(['name' => $role])->id]);
    }
}
