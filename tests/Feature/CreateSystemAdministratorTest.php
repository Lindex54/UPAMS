<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateSystemAdministratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_securely_creates_the_initial_system_administrator(): void
    {
        $this->artisan('upams:create-system-administrator')
            ->expectsQuestion('Full name', 'Initial Administrator')
            ->expectsQuestion('Email address', ' ADMIN@BUSITEMA.AC.UG ')
            ->expectsQuestion('Initial password (minimum 8 characters with uppercase, lowercase, numbers, and symbols)', 'Valid26!')
            ->expectsQuestion('Confirm initial password', 'Valid26!')
            ->expectsConfirmation('Create the initial System Administrator account for admin@busitema.ac.ug?', 'yes')
            ->expectsOutput('The initial UPAMS System Administrator account was created successfully.')
            ->assertSuccessful();

        $user = User::query()->sole();

        $this->assertSame('Initial Administrator', $user->name);
        $this->assertSame('admin@busitema.ac.ug', $user->email);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('Valid26!', $user->password));
        $this->assertNotSame('Valid26!', $user->password);
    }

    public function test_command_refuses_to_create_another_initial_administrator(): void
    {
        $existingUser = User::factory()->create();

        $this->artisan('upams:create-system-administrator')
            ->expectsOutput('An UPAMS user account already exists. The initial administrator was not created.')
            ->assertFailed();

        $this->assertDatabaseCount('users', 1);
        $this->assertModelExists($existingUser);
    }

    public function test_command_does_not_create_an_account_with_an_invalid_password(): void
    {
        $this->artisan('upams:create-system-administrator')
            ->expectsQuestion('Full name', 'Initial Administrator')
            ->expectsQuestion('Email address', 'admin@busitema.ac.ug')
            ->expectsQuestion('Initial password (minimum 8 characters with uppercase, lowercase, numbers, and symbols)', 'short')
            ->expectsQuestion('Confirm initial password', 'short')
            ->expectsOutput('The password field must be at least 8 characters.')
            ->assertFailed();

        $this->assertDatabaseEmpty('users');
    }
}
