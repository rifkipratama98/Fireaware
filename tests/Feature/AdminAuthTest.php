<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('status');
    }

    public function test_guest_is_redirected_to_login_when_accessing_any_admin_resource(): void
    {
        $this->get(route('admin.guides.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.education.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.announcements.index'))->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_user_cannot_access_admin_area(): void
    {
        // Masyarakat tidak memiliki akun; bahkan bila terdapat akun non-admin, ia wajib ditolak.
        $user = User::factory()->create(['role' => 'publik']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_can_view_login_page(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSee('Masuk sebagai Admin');
        $response->assertSee('Email');
        $response->assertSee('Password');
    }

    public function test_authenticated_admin_is_redirected_away_from_login_page(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email'    => 'admin@fireaware.id',
            'password' => 'super-secret-password',
        ]);

        $response = $this->post(route('admin.login'), [
            'email'    => 'admin@fireaware.id',
            'password' => 'super-secret-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        User::factory()->create([
            'email'    => 'admin@fireaware.id',
            'password' => 'super-secret-password',
        ]);

        $response = $this->post(route('admin.login'), [
            'email'    => 'admin@fireaware.id',
            'password' => 'password-salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_requires_valid_email_and_password(): void
    {
        $this->post(route('admin.login'), [
            'email'    => 'bukan-email',
            'password' => '',
        ])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login'), [
                'email'    => 'admin@fireaware.id',
                'password' => 'salah',
            ]);
        }

        $response = $this->post(route('admin.login'), [
            'email'    => 'admin@fireaware.id',
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_logout_securely(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.logout'))
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_guest_cannot_logout(): void
    {
        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));
    }

    public function test_no_public_registration_route_exists(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register')->assertNotFound();
    }
}
