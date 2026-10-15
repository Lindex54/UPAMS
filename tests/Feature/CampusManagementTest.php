<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\District;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\CampusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampusManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_campus_list_shows_campus_details(): void
    {
        $district = District::factory()->create(['name' => 'Tororo']);
        Campus::factory()->create(['name' => 'Nagongera Campus', 'code' => 'NAG', 'district_id' => $district->id, 'faculty' => 'Faculty of Science and Education']);

        $this->actingAs($this->userWithRole('Finance Officer'))->get(route('campuses.index'))
            ->assertOk()
            ->assertSee('Nagongera Campus')
            ->assertSee('NAG')
            ->assertSee('Tororo District')
            ->assertSee('Faculty of Science and Education')
            ->assertDontSee('Add campus');
    }

    public function test_system_administrator_can_add_a_campus(): void
    {
        $administrator = $this->userWithRole('System Administrator');
        $district = District::factory()->create(['name' => 'Kaliro']);

        $this->actingAs($administrator)->get(route('campuses.create'))->assertOk()->assertSee('Register a campus');

        $response = $this->actingAs($administrator)->post(route('campuses.store'), [
            'name' => 'Kaliro Campus',
            'code' => ' kal ',
            'district_id' => $district->id,
            'location' => 'Former Kaliro National Teachers College',
            'faculty' => 'Faculty of Science and Education',
            'latitude' => '0.8950000',
            'longitude' => '33.5040000',
            'status' => 'Active',
        ]);

        $campus = Campus::query()->where('name', 'Kaliro Campus')->firstOrFail();
        $response->assertRedirect(route('campuses.show', $campus));
        $this->assertSame('KAL', $campus->code);
        $this->assertSame($district->id, $campus->district_id);

        $this->actingAs($administrator)->get(route('campuses.show', $campus))
            ->assertOk()
            ->assertSee('Kaliro Campus')
            ->assertSee('View full map');
    }

    public function test_campus_requires_name_code_district_and_valid_status(): void
    {
        $this->actingAs($this->userWithRole('System Administrator'))
            ->post(route('campuses.store'), ['status' => 'Closed'])
            ->assertSessionHasErrors(['name', 'code', 'district_id', 'status']);

        $this->assertDatabaseCount('campuses', 0);
    }

    public function test_campus_name_and_code_must_be_unique(): void
    {
        Campus::factory()->create(['name' => 'Arapai Campus', 'code' => 'ARP']);

        $this->actingAs($this->userWithRole('System Administrator'))
            ->post(route('campuses.store'), ['name' => 'Arapai Campus', 'code' => 'arp', 'district_id' => District::factory()->create()->id, 'status' => 'Active'])
            ->assertSessionHasErrors(['name', 'code']);
    }

    public function test_gps_coordinates_must_be_entered_together(): void
    {
        $this->actingAs($this->userWithRole('System Administrator'))
            ->post(route('campuses.store'), ['name' => 'Test Campus', 'code' => 'TST', 'district_id' => District::factory()->create()->id, 'status' => 'Active', 'latitude' => '1.2'])
            ->assertSessionHasErrors(['longitude']);
    }

    public function test_system_administrator_can_update_a_campus_and_keep_its_own_code(): void
    {
        $administrator = $this->userWithRole('System Administrator');
        $campus = Campus::factory()->create(['name' => 'Pallisa Campus', 'code' => 'PAL']);

        $this->actingAs($administrator)->get(route('campuses.edit', $campus))->assertOk();

        $this->actingAs($administrator)->put(route('campuses.update', $campus), [
            'name' => 'Pallisa Campus',
            'code' => 'PAL',
            'district_id' => District::factory()->create(['name' => 'Pallisa'])->id,
            'status' => 'Inactive',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Inactive', $campus->refresh()->status);
    }

    public function test_other_roles_cannot_add_or_edit_campuses(): void
    {
        $user = $this->userWithRole('Finance Officer');
        $campus = Campus::factory()->create();

        $this->actingAs($user)->get(route('campuses.create'))->assertForbidden();
        $this->actingAs($user)->post(route('campuses.store'), ['name' => 'Rogue Campus', 'code' => 'RGE', 'district_id' => District::factory()->create()->id, 'status' => 'Active'])->assertForbidden();
        $this->actingAs($user)->get(route('campuses.edit', $campus))->assertForbidden();
        $this->actingAs($user)->get(route('campuses.show', $campus))->assertOk()->assertDontSee('Edit campus');

        $this->assertDatabaseMissing('campuses', ['name' => 'Rogue Campus']);
    }

    public function test_campus_seeder_links_campuses_to_their_districts_and_can_be_rerun(): void
    {
        foreach (['Busia', 'Tororo', 'Soroti', 'Kamuli', 'Mbale', 'Pallisa'] as $district) {
            District::factory()->create(['name' => $district]);
        }

        $this->seed(CampusSeeder::class);
        $this->seed(CampusSeeder::class);

        $this->assertDatabaseCount('campuses', 6);
        $this->assertSame('Tororo', Campus::query()->where('code', 'NAG')->firstOrFail()->district->name);
    }

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role_id' => Role::query()->firstOrCreate(['name' => $role])->id]);
    }
}
