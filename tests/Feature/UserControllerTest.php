<?php

namespace Tests\Feature;

use App\Models\Campus;
use App\Models\OrgUnit;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_active_authenticated_user_can_view_user_accounts(): void
    {
        $administrator = User::factory()->create([
            'name' => 'System Administrator',
            'email' => 'admin@busitema.ac.ug',
        ]);
        $inactiveUser = User::factory()->inactive()->create([
            'name' => "O'Reilly <script>alert('xss')</script>",
            'email' => 'inactive@busitema.ac.ug',
        ]);

        $response = $this->actingAs($administrator)->get(route('users.index'));

        $response
            ->assertSee('User accounts')
            ->assertSee('admin@busitema.ac.ug')
            ->assertSee('inactive@busitema.ac.ug')
            ->assertSee('Active')
            ->assertSee('Inactive')
            ->assertSee($inactiveUser->name)
            ->assertDontSee($inactiveUser->name, escape: false);
    }

    public function test_active_authenticated_user_can_open_the_create_user_form(): void
    {
        $administrator = User::factory()->create();
        $campus = Campus::factory()->create(['name' => 'Main Campus']);
        $orgUnit = OrgUnit::factory()->create(['name' => 'Finance Office']);
        $role = Role::factory()->create(['name' => 'Finance Officer']);

        $response = $this->actingAs($administrator)->get(route('users.create'));

        $response
            ->assertSee('Create a user account')
            ->assertSee('First Name')
            ->assertSee('Middle Name')
            ->assertSee('Last Name')
            ->assertSee('Telephone Number')
            ->assertSee('Position / Job Title')
            ->assertSee($campus->name)
            ->assertSee($orgUnit->name)
            ->assertSee($role->name)
            ->assertSee('Initial password')
            ->assertSee('Confirm initial password')
            ->assertDontSee('name="is_active"', escape: false);
    }

    public function test_user_can_be_created_with_a_hashed_initial_password(): void
    {
        $administrator = User::factory()->create();

        $attributes = $this->validUserData([
            'first_name' => 'Godwin',
            'middle_name' => 'M',
            'last_name' => 'Akello',
            'email' => ' FINANCE@BUSITEMA.AC.UG ',
        ]);

        $response = $this->actingAs($administrator)->post(route('users.store'), $attributes);

        $response
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('status', 'User account created successfully.');

        $user = User::query()->where('email', 'finance@busitema.ac.ug')->firstOrFail();

        $this->assertSame('Godwin M Akello', $user->name);
        $this->assertSame('Godwin', $user->first_name);
        $this->assertSame('M', $user->middle_name);
        $this->assertSame('Akello', $user->last_name);
        $this->assertSame('+256 700 000000', $user->telephone);
        $this->assertSame('Finance Officer', $user->position);
        $this->assertSame($attributes['campus_id'], $user->campus_id);
        $this->assertSame($attributes['org_unit_id'], $user->org_unit_id);
        $this->assertSame($attributes['role_id'], $user->role_id);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('Valid26!', $user->password));
        $this->assertNotSame('Valid26!', $user->password);
    }

    public function test_new_user_requires_a_unique_email_and_confirmed_initial_password(): void
    {
        $administrator = User::factory()->create([
            'email' => 'admin@busitema.ac.ug',
        ]);

        $response = $this->actingAs($administrator)->post(route('users.store'), $this->validUserData([
            'email' => 'ADMIN@BUSITEMA.AC.UG',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]));

        $response->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_new_user_requires_profile_and_scope_information(): void
    {
        $administrator = User::factory()->create();

        $response = $this->actingAs($administrator)->post(route('users.store'), [
            'email' => 'new.user@busitema.ac.ug',
            'password' => 'Valid26!',
            'password_confirmation' => 'Valid26!',
        ]);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'telephone',
            'campus_id',
            'org_unit_id',
            'role_id',
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_new_user_rejects_unknown_scope_and_role_values(): void
    {
        $administrator = User::factory()->create();

        $response = $this->actingAs($administrator)->post(route('users.store'), $this->validUserData([
            'campus_id' => 999999,
            'org_unit_id' => 999999,
            'role_id' => 999999,
        ]));

        $response->assertSessionHasErrors(['campus_id', 'org_unit_id', 'role_id']);

        $this->assertDatabaseCount('users', 1);
    }

    #[DataProvider('invalidInitialPasswords')]
    public function test_initial_password_requires_mixed_case_letters_numbers_and_symbols(string $password): void
    {
        $administrator = User::factory()->create();

        $response = $this->actingAs($administrator)->post(route('users.store'), $this->validUserData([
            'email' => 'new.user@busitema.ac.ug',
            'password' => $password,
            'password_confirmation' => $password,
        ]));

        $response->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_basic_user_information_can_be_updated_without_changing_security_fields(): void
    {
        $administrator = User::factory()->create();
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@busitema.ac.ug',
            'password' => 'existing-password',
        ]);
        $originalPassword = $user->password;

        $attributes = $this->validUserData([
            'first_name' => 'Updated',
            'middle_name' => null,
            'last_name' => 'Name',
            'email' => ' UPDATED@BUSITEMA.AC.UG ',
            'password' => 'unexpected-password',
            'is_active' => false,
        ]);

        $response = $this->actingAs($administrator)->put(route('users.update', $user), $attributes);

        $response
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('status', 'User account updated successfully.');

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('Updated', $user->first_name);
        $this->assertSame('Name', $user->last_name);
        $this->assertSame($attributes['campus_id'], $user->campus_id);
        $this->assertSame($attributes['org_unit_id'], $user->org_unit_id);
        $this->assertSame($attributes['role_id'], $user->role_id);
        $this->assertSame('updated@busitema.ac.ug', $user->email);
        $this->assertSame($originalPassword, $user->password);
        $this->assertTrue($user->is_active);
    }

    public function test_user_update_rejects_another_accounts_email_address(): void
    {
        $administrator = User::factory()->create();
        $existingUser = User::factory()->create(['email' => 'existing@busitema.ac.ug']);
        $user = User::factory()->create(['email' => 'original@busitema.ac.ug']);

        $response = $this->actingAs($administrator)->put(route('users.update', $user), $this->validUserData([
            'email' => $existingUser->email,
        ]));

        $response->assertSessionHasErrors('email');

        $this->assertSame('original@busitema.ac.ug', $user->fresh()->email);
    }

    public function test_user_account_can_be_deactivated(): void
    {
        $administrator = User::factory()->create();
        $user = User::factory()->create(['remember_token' => 'original-token']);

        $response = $this->actingAs($administrator)->patch(route('users.status.update', $user), [
            'is_active' => false,
        ]);

        $response->assertSessionHas('status', 'User account deactivated.');

        $user->refresh();

        $this->assertFalse($user->is_active);
        $this->assertNotSame('original-token', $user->remember_token);
    }

    public function test_inactive_user_account_can_be_activated(): void
    {
        $administrator = User::factory()->create();
        $user = User::factory()->inactive()->create();

        $response = $this->actingAs($administrator)->patch(route('users.status.update', $user), [
            'is_active' => true,
        ]);

        $response->assertSessionHas('status', 'User account activated.');

        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_user_cannot_deactivate_their_own_account(): void
    {
        $administrator = User::factory()->create();

        $response = $this->actingAs($administrator)->patch(route('users.status.update', $administrator), [
            'is_active' => false,
        ]);

        $response->assertSessionHasErrors('account', 'You cannot deactivate your own account.');

        $this->assertTrue($administrator->fresh()->is_active);
    }

    public function test_account_status_requires_a_boolean_value(): void
    {
        $administrator = User::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($administrator)->patch(route('users.status.update', $user), [
            'is_active' => 'inactive',
        ]);

        $response->assertSessionHasErrors('is_active');

        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create([
            'email' => 'inactive@busitema.ac.ug',
            'password' => 'correct-password',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertSessionHasErrors('email', __('auth.failed'));

        $this->assertGuest();
    }

    public function test_inactive_authenticated_user_is_logged_out_on_internal_request(): void
    {
        $user = User::factory()->inactive()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email', 'Your account has been deactivated. Contact the UPAMS system administrator.');

        $this->assertGuest();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidInitialPasswords(): array
    {
        return [
            'fewer than eight characters' => ['Aa1!aaa'],
            'no uppercase letter' => ['lowercase26!'],
            'no number' => ['ValidPassword!'],
            'no symbol' => ['ValidPassword26'],
        ];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validUserData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Finance',
            'middle_name' => null,
            'last_name' => 'Officer',
            'email' => 'finance.officer@busitema.ac.ug',
            'telephone' => '+256 700 000000',
            'position' => 'Finance Officer',
            'campus_id' => Campus::factory()->create()->id,
            'org_unit_id' => OrgUnit::factory()->create()->id,
            'role_id' => Role::factory()->create()->id,
            'password' => 'Valid26!',
            'password_confirmation' => 'Valid26!',
        ], $overrides);
    }
}
