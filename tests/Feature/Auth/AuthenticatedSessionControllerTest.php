<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_identifies_property_management_and_displays_the_login_controls(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('University Property and')
            ->assertSee('Asset Management System')
            ->assertSee('images/busitema-logo.png', escape: false)
            ->assertSee('Email address')
            ->assertSee('Forgot password?')
            ->assertSee('Sign in to Property Management');
    }

    public function test_login_requires_an_email_address_and_password(): void
    {
        $response = $this->from(route('login'))->post(route('login.store'));

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_validation_errors_are_displayed_on_the_login_page(): void
    {
        $this->from(route('login'))->post(route('login.store'));

        $response = $this->get(route('login'));

        $response
            ->assertSee('Please check the highlighted details and try again.')
            ->assertSee('The email field is required.')
            ->assertSee('The password field is required.');
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@busitema.ac.ug',
            'password' => 'correct-password',
        ]);

        $response = $this
            ->followingRedirects()
            ->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ]);

        $response
            ->assertOk()
            ->assertSee('Please check the highlighted details and try again.')
            ->assertSee(__('auth.failed'));

        $this->assertGuest();
    }

    public function test_valid_credentials_authenticate_and_redirect_to_the_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@busitema.ac.ug',
            'password' => 'correct-password',
            'remember_token' => null,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'correct-password',
            'remember' => true,
        ]);

        $response
            ->assertRedirect(route('dashboard'))
            ->assertCookie(Auth::guard('web')->getRecallerName());

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_authenticated_users_are_redirected_away_from_the_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('login'));

        $response->assertRedirect(route('dashboard'));
    }

    #[DataProvider('internalRoutes')]
    public function test_unauthenticated_users_are_redirected_to_login(string $routeName): void
    {
        $response = $this->get(route($routeName));

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession(['authentication-marker' => 'present'])
            ->post(route('logout'));

        $response
            ->assertRedirect(route('login'))
            ->assertSessionMissing('authentication-marker');

        $this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function internalRoutes(): array
    {
        return [
            'dashboard' => ['dashboard'],
            'estates dashboard' => ['estates.dashboard'],
            'management dashboard' => ['management.dashboard'],
            'campus dashboard' => ['campus.dashboard'],
            'finance dashboard' => ['finance.dashboard'],
        ];
    }
}
